<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class SupplierRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'supplier_type',
        'supplier_id',
        'status',
        'request_data',
        'generated_at',
        'expires_at',
        'confirmed_at',
        'notes',
        'pdf_path',
    ];

    protected $casts = [
        'request_data' => 'array',
        'generated_at' => 'datetime',
        'expires_at' => 'datetime',
        'confirmed_at' => 'datetime',
    ];

    // Relationships
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function supplier()
    {
        return match($this->supplier_type) {
            'hotel' => $this->belongsTo(Hotel::class, 'supplier_id'),
            'transport' => $this->belongsTo(Transport::class, 'supplier_id'),
            'guide' => $this->belongsTo(Guide::class, 'supplier_id'),
            'restaurant' => $this->belongsTo(Restaurant::class, 'supplier_id'),
            default => null,
        };
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<', now())
                    ->where('status', 'pending');
    }

    public function scopeForBooking($query, $bookingId)
    {
        return $query->where('booking_id', $bookingId);
    }

    // Helper methods
    public function isExpired()
    {
        return $this->expires_at && $this->expires_at->isPast() && $this->status === 'pending';
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isConfirmed()
    {
        return $this->status === 'confirmed';
    }

    public function isRejected()
    {
        return $this->status === 'rejected';
    }

    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'pending' => 'warning',
            'confirmed' => 'success',
            'rejected' => 'danger',
            'expired' => 'secondary',
            default => 'secondary'
        };
    }

    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'pending' => 'Ожидает подтверждения',
            'confirmed' => 'Подтверждено',
            'rejected' => 'Отклонено',
            'expired' => 'Истекло',
            default => 'Неизвестно'
        };
    }

    public function getSupplierTypeLabelAttribute()
    {
        return match($this->supplier_type) {
            'hotel' => 'Гостиница',
            'transport' => 'Транспорт',
            'guide' => 'Гид',
            'restaurant' => 'Ресторан',
            default => 'Неизвестно'
        };
    }

    public function getSupplierTypeIconAttribute()
    {
        return match($this->supplier_type) {
            'hotel' => '🏨',
            'transport' => '🚗',
            'guide' => '👨‍🏫',
            'restaurant' => '🍽️',
            default => '📋'
        };
    }

    // Business logic methods
    public function markAsConfirmed($notes = null)
    {
        $this->update([
            'status' => 'confirmed',
            'confirmed_at' => now(),
            'notes' => $notes
        ]);
    }

    public function markAsRejected($notes = null)
    {
        $this->update([
            'status' => 'rejected',
            'notes' => $notes
        ]);
    }

    public function markAsExpired()
    {
        $this->update([
            'status' => 'expired'
        ]);
    }

    public function setExpiration($hours = 48)
    {
        $this->update([
            'expires_at' => now()->addHours($hours)
        ]);
    }

    // Static methods
    public static function createForSupplier($booking, $supplierType, $supplierId, $requestData, $expirationHours = 48)
    {
        return self::create([
            'booking_id' => $booking->id,
            'supplier_type' => $supplierType,
            'supplier_id' => $supplierId,
            'request_data' => $requestData,
            'generated_at' => now(),
            'expires_at' => now()->addHours($expirationHours),
            'status' => 'pending'
        ]);
    }

    public static function getExpiredRequests()
    {
        return self::expired()->get();
    }

    public static function markExpiredRequests()
    {
        return self::expired()->update(['status' => 'expired']);
    }
}