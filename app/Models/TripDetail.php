<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TripDetail extends Model
{
    protected $fillable = [
        'booking_id',
        'hotel_name',
        'hotel_address',
        'whatsapp_number',
        'arrival_date',
        'arrival_flight',
        'arrival_time',
        'departure_date',
        'departure_flight',
        'departure_time',
        'language_preference',
        'referral_source',
        'additional_notes',
        'completed_at',
    ];

    protected $casts = [
        'arrival_date' => 'date',
        'departure_date' => 'date',
        'completed_at' => 'datetime',
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Check if trip details form has been completed
     */
    public function isCompleted(): bool
    {
        return !is_null($this->completed_at);
    }

    /**
     * Check if this is for a mini tour (duration <= 2 days)
     */
    public function isMiniTour(): bool
    {
        return $this->booking->tour->duration_days <= 2;
    }

    /**
     * Get required fields based on tour type
     * Mini tours: hotel + whatsapp + referral
     * Long tours: all fields
     */
    public function getRequiredFields(): array
    {
        $base = ['hotel_name', 'whatsapp_number'];

        if (!$this->isMiniTour()) {
            $base = array_merge($base, [
                'arrival_date',
                'arrival_flight',
                'departure_date',
                'departure_flight',
            ]);
        }

        return $base;
    }

    /**
     * Check if all required fields are filled
     */
    public function hasRequiredFields(): bool
    {
        foreach ($this->getRequiredFields() as $field) {
            if (empty($this->$field)) {
                return false;
            }
        }

        return true;
    }
}
