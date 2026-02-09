<?php

namespace App\Models;

use App\Events\PaymentSucceeded;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OctobankPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'octo_payment_uuid',
        'octo_shop_transaction_id',
        'amount',
        'currency',
        'description',
        'status',
        'octo_payment_url',
        'payment_method',
        'masked_pan',
        'card_holder',
        'card_token',
        'card_recurrent_token',
        'card_token_expires_at',
        'refunded_amount',
        'refund_reason',
        'webhook_received_at',
        'webhook_payload',
        'error_code',
        'error_message',
        'request_payload',
        'response_payload',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'refunded_amount' => 'decimal:2',
        'webhook_payload' => 'array',
        'request_payload' => 'array',
        'response_payload' => 'array',
        'webhook_received_at' => 'datetime',
        'card_token_expires_at' => 'datetime',
    ];

    // Status constants
    public const STATUS_CREATED = 'created';
    public const STATUS_WAITING = 'waiting';
    public const STATUS_SUCCEEDED = 'succeeded';
    public const STATUS_FAILED = 'failed';
    public const STATUS_REFUNDED = 'refunded';
    public const STATUS_PARTIAL_REFUND = 'partial_refund';
    public const STATUS_CANCELLED = 'cancelled';

    // Relationships
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    // Scopes
    public function scopeSuccessful($query)
    {
        return $query->where('status', self::STATUS_SUCCEEDED);
    }

    public function scopePending($query)
    {
        return $query->whereIn('status', [self::STATUS_CREATED, self::STATUS_WAITING]);
    }

    public function scopeFailed($query)
    {
        return $query->whereIn('status', [self::STATUS_FAILED, self::STATUS_CANCELLED]);
    }

    // Accessors
    public function getIsSuccessfulAttribute(): bool
    {
        return $this->status === self::STATUS_SUCCEEDED;
    }

    public function getIsPendingAttribute(): bool
    {
        return in_array($this->status, [self::STATUS_CREATED, self::STATUS_WAITING]);
    }

    public function getIsRefundableAttribute(): bool
    {
        return $this->status === self::STATUS_SUCCEEDED 
            && $this->refunded_amount < $this->amount;
    }

    public function getRemainingRefundableAmountAttribute(): float
    {
        return (float) $this->amount - (float) $this->refunded_amount;
    }

    public function getFormattedAmountAttribute(): string
    {
        return number_format($this->amount, 0, '.', ' ') . ' ' . $this->currency;
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            self::STATUS_CREATED => 'Создан',
            self::STATUS_WAITING => 'Ожидание оплаты',
            self::STATUS_SUCCEEDED => 'Оплачен',
            self::STATUS_FAILED => 'Ошибка',
            self::STATUS_REFUNDED => 'Возврат',
            self::STATUS_PARTIAL_REFUND => 'Частичный возврат',
            self::STATUS_CANCELLED => 'Отменён',
            default => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            self::STATUS_SUCCEEDED => 'success',
            self::STATUS_WAITING, self::STATUS_CREATED => 'warning',
            self::STATUS_FAILED, self::STATUS_CANCELLED => 'danger',
            self::STATUS_REFUNDED, self::STATUS_PARTIAL_REFUND => 'info',
            default => 'gray',
        };
    }

    // Methods
    public function markAsSucceeded(array $responseData = []): void
    {
        $this->update([
            'status' => self::STATUS_SUCCEEDED,
            'response_payload' => array_merge($this->response_payload ?? [], $responseData),
            'payment_method' => $responseData['payment_method'] ?? $this->payment_method,
            'masked_pan' => $responseData['masked_pan'] ?? $this->masked_pan,
            'card_holder' => $responseData['card_holder'] ?? $this->card_holder,
        ]);

        // Update booking payment status
        $this->booking->update([
            'payment_status' => 'paid',
            'amount_paid' => $this->amount,
            'paid_at' => now(),
        ]);

        // Dispatch event to send payment confirmation email
        PaymentSucceeded::dispatch($this);
    }

    public function markAsFailed(string $errorCode = null, string $errorMessage = null): void
    {
        $this->update([
            'status' => self::STATUS_FAILED,
            'error_code' => $errorCode,
            'error_message' => $errorMessage,
        ]);

        $this->booking->update([
            'payment_status' => 'failed',
        ]);
    }

    public function processRefund(float $amount, string $reason = null): void
    {
        $newRefundedAmount = $this->refunded_amount + $amount;
        $isFullRefund = $newRefundedAmount >= $this->amount;

        $this->update([
            'status' => $isFullRefund ? self::STATUS_REFUNDED : self::STATUS_PARTIAL_REFUND,
            'refunded_amount' => $newRefundedAmount,
            'refund_reason' => $reason ?? $this->refund_reason,
        ]);

        $this->booking->update([
            'payment_status' => $isFullRefund ? 'refunded' : 'partial_refund',
        ]);
    }

    public function storeCardToken(string $token, string $recurrentToken = null, $expiresAt = null): void
    {
        $this->update([
            'card_token' => $token,
            'card_recurrent_token' => $recurrentToken,
            'card_token_expires_at' => $expiresAt,
        ]);
    }

    // Generate unique shop transaction ID
    public static function generateShopTransactionId(): string
    {
        return 'JHT-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -8));
    }
}
