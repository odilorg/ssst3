<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'amount',
        'payment_method',
        'status',
        'payment_type',
        'transaction_id',
        'gateway_response',
        'processed_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'gateway_response' => 'array',
        'processed_at' => 'datetime',
    ];

    // ==========================================
    // RELATIONSHIPS
    // ==========================================

    /**
     * Get the booking this payment belongs to
     */
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    // ==========================================
    // SCOPES
    // ==========================================

    /**
     * Scope to get only completed payments
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope to get only failed payments
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Scope to filter by booking
     */
    public function scopeForBooking($query, $bookingId)
    {
        return $query->where('booking_id', $bookingId);
    }

    /**
     * Scope to get deposits only
     */
    public function scopeDeposits($query)
    {
        return $query->where('payment_type', 'deposit');
    }

    /**
     * Scope to get full payments only
     */
    public function scopeFullPayments($query)
    {
        return $query->where('payment_type', 'full');
    }

    /**
     * Scope to get balance payments only
     */
    public function scopeBalancePayments($query)
    {
        return $query->where('payment_type', 'balance');
    }

    /**
     * Scope to get refunds only
     */
    public function scopeRefunds($query)
    {
        return $query->where('payment_type', 'refund');
    }

    // ==========================================
    // BUSINESS LOGIC METHODS
    // ==========================================

    /**
     * Check if payment is completed
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Check if payment failed
     */
    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    /**
     * Check if payment is pending
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if this is a refund
     */
    public function isRefund(): bool
    {
        return $this->payment_type === 'refund' || $this->amount < 0;
    }

    /**
     * Get formatted amount with currency
     */
    public function getFormattedAmount(): string
    {
        $currency = $this->booking->currency ?? 'USD';
        $amount = abs($this->amount);

        $formatted = number_format($amount, 2) . ' ' . $currency;

        if ($this->isRefund()) {
            return '-' . $formatted;
        }

        return $formatted;
    }

    /**
     * Get payment method display name
     */
    public function getPaymentMethodName(): string
    {
        return match($this->payment_method) {
            'octo_uzcard' => 'UzCard via OCTO',
            'octo_humo' => 'HUMO via OCTO',
            'octo_visa' => 'VISA via OCTO',
            'octo_mastercard' => 'MasterCard via OCTO',
            'bank_transfer' => 'Bank Transfer',
            'cash' => 'Cash',
            default => ucwords(str_replace('_', ' ', $this->payment_method)),
        };
    }

    // ==========================================
    // MODEL EVENTS
    // ==========================================

    protected static function booted()
    {
        // After payment is completed, update booking totals
        static::updated(function ($payment) {
            if ($payment->isDirty('status') && $payment->isCompleted()) {
                $payment->booking->recalculatePaymentTotals();
            }
        });

        // Set processed_at when status changes to completed
        static::updating(function ($payment) {
            if ($payment->isDirty('status') && $payment->status === 'completed') {
                if (!$payment->processed_at) {
                    $payment->processed_at = now();
                }
            }
        });
    }
}
