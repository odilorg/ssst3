<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    /**
     * Resolve the supplier model on demand.
     * Not a real Eloquent relation — cannot be eager-loaded.
     * Use supplier_name in lists/tables to avoid N+1.
     */
    public function getSupplierAttribute(): ?Model
    {
        return match($this->supplier_type) {
            'hotel'      => Hotel::find($this->supplier_id),
            'transport'  => Transport::find($this->supplier_id),
            'guide'      => Guide::find($this->supplier_id),
            'restaurant' => Restaurant::find($this->supplier_id),
            default      => null,
        };
    }

    /**
     * Human-readable supplier name for display in tables and notifications.
     * Transport uses transportType.type to match the "vehicle class" display convention.
     */
    public function getSupplierNameAttribute(): string
    {
        return match($this->supplier_type) {
            'hotel'      => Hotel::find($this->supplier_id)?->name
                            ?? 'Неизвестный поставщик',
            'transport'  => Transport::with('transportType')->find($this->supplier_id)?->transportType?->type
                            ?? 'Неизвестный поставщик',
            'guide'      => Guide::find($this->supplier_id)?->name
                            ?? 'Неизвестный поставщик',
            'restaurant' => Restaurant::find($this->supplier_id)?->name
                            ?? 'Неизвестный поставщик',
            default      => 'Неизвестный поставщик',
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