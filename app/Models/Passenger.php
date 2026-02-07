<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Passenger extends Model
{
    protected $fillable = [
        'booking_id',
        'first_name',
        'last_name',
        'date_of_birth',
        'passport_number',
        'passport_expiry_date',
        'passport_nationality',
        'passport_scan_path',
        'emergency_contact_name',
        'emergency_contact_phone',
        'emergency_contact_relationship',
        'dietary_requirements',
        'medical_conditions',
        'special_needs',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'passport_expiry_date' => 'date',
    ];

    /**
     * Get the booking that owns the passenger
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Get the passenger's full name
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * Check if passport is expiring soon (within 6 months)
     */
    public function isPassportExpiringSoon(): bool
    {
        return $this->passport_expiry_date->diffInMonths(now()) < 6;
    }

    /**
     * Check if passport is valid for travel date
     */
    public function isPassportValidForTravel(): bool
    {
        // Passport should be valid for at least 6 months after tour end date
        $minValidDate = $this->booking->end_date?->addMonths(6) ?? $this->booking->start_date->addMonths(6);
        return $this->passport_expiry_date->greaterThan($minValidDate);
    }
}
