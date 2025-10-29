<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TourExtra extends Model
{
    protected $fillable = [
        'tour_id',
        'name',
        'description',
        'price',
        'currency',
        'price_unit',
        'icon',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Get the tour this extra belongs to
     */
    public function tour()
    {
        return $this->belongsTo(Tour::class);
    }

    /**
     * Get all bookings that have this extra
     */
    public function bookings()
    {
        return $this->belongsToMany(Booking::class, 'booking_tour_extra')
                    ->withPivot('price_at_booking', 'quantity')
                    ->withTimestamps();
    }

    /**
     * Scope: Only active extras
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
