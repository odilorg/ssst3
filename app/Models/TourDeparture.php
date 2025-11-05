<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TourDeparture extends Model
{
    use HasFactory;

    protected $fillable = [
        'tour_id',
        'start_date',
        'end_date',
        'max_pax',
        'booked_pax',
        'min_pax',
        'price_per_person',
        'status',
        'departure_type',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'max_pax' => 'integer',
        'booked_pax' => 'integer',
        'min_pax' => 'integer',
        'price_per_person' => 'decimal:2',
    ];

    // ==========================================
    // RELATIONSHIPS
    // ==========================================

    /**
     * Get the tour this departure belongs to
     */
    public function tour()
    {
        return $this->belongsTo(Tour::class);
    }

    /**
     * Get all bookings for this departure
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class, 'departure_id');
    }

    /**
     * Get confirmed bookings only
     */
    public function confirmedBookings()
    {
        return $this->bookings()
            ->whereIn('status', ['confirmed', 'in_progress', 'completed']);
    }

    // ==========================================
    // SCOPES
    // ==========================================

    /**
     * Scope to get only available departures (open or guaranteed)
     */
    public function scopeAvailable($query)
    {
        return $query->whereIn('status', ['open', 'guaranteed']);
    }

    /**
     * Scope to get upcoming departures (future dates)
     */
    public function scopeUpcoming($query)
    {
        return $query->where('start_date', '>=', now());
    }

    /**
     * Scope to get full departures
     */
    public function scopeFull($query)
    {
        return $query->where('status', 'full');
    }

    /**
     * Scope to filter by tour
     */
    public function scopeForTour($query, $tourId)
    {
        return $query->where('tour_id', $tourId);
    }

    /**
     * Scope to get group departures
     */
    public function scopeGroup($query)
    {
        return $query->where('departure_type', 'group');
    }

    /**
     * Scope to get private departures
     */
    public function scopePrivate($query)
    {
        return $query->where('departure_type', 'private');
    }

    // ==========================================
    // BUSINESS LOGIC METHODS
    // ==========================================

    /**
     * Check if departure can accept bookings
     */
    public function isAvailable(): bool
    {
        return in_array($this->status, ['open', 'guaranteed'])
            && $this->start_date->isFuture();
    }

    /**
     * Get number of spots remaining
     */
    public function spotsRemaining(): int
    {
        return max(0, $this->max_pax - $this->booked_pax);
    }

    /**
     * Check if departure has space for X people
     */
    public function hasSpace(int $paxCount): bool
    {
        return $this->spotsRemaining() >= $paxCount;
    }

    /**
     * Increment booked passengers
     */
    public function incrementBooked(int $pax): void
    {
        $this->increment('booked_pax', $pax);
        $this->updateStatus();
    }

    /**
     * Decrement booked passengers
     */
    public function decrementBooked(int $pax): void
    {
        $this->decrement('booked_pax', max(0, $pax));
        $this->updateStatus();
    }

    /**
     * Update status based on current capacity and dates
     */
    public function updateStatus(): void
    {
        $this->refresh();

        // If in the past, mark as completed
        if ($this->end_date->isPast()) {
            $this->update(['status' => 'completed']);
            return;
        }

        // If cancelled, don't auto-update
        if ($this->status === 'cancelled') {
            return;
        }

        // Check capacity
        if ($this->booked_pax >= $this->max_pax) {
            $this->update(['status' => 'full']);
        } elseif ($this->min_pax && $this->booked_pax >= $this->min_pax) {
            $this->update(['status' => 'guaranteed']);
        } else {
            $this->update(['status' => 'open']);
        }
    }

    /**
     * Get effective price (override or tour price)
     */
    public function getEffectivePrice(): float
    {
        if ($this->price_per_person) {
            return (float) $this->price_per_person;
        }

        return $this->departure_type === 'group'
            ? (float) $this->tour->group_price_per_person
            : (float) $this->tour->private_price_per_person;
    }

    /**
     * Check if departure is confirmed (has min passengers)
     */
    public function isGuaranteed(): bool
    {
        return $this->status === 'guaranteed' || $this->status === 'full';
    }

    /**
     * Check if departure is full
     */
    public function isFull(): bool
    {
        return $this->status === 'full';
    }

    /**
     * Get occupancy percentage
     */
    public function getOccupancyPercentage(): float
    {
        if ($this->max_pax === 0) {
            return 0;
        }

        return round(($this->booked_pax / $this->max_pax) * 100, 1);
    }
}
