<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

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

    // Status constants (matching database ENUM)
    const STATUS_OPEN = 'open';
    const STATUS_GUARANTEED = 'guaranteed';
    const STATUS_FULL = 'full';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    // Departure type constants (matching database ENUM)
    const TYPE_GROUP = 'group';
    const TYPE_PRIVATE = 'private';

    // ==========================================
    // RELATIONSHIPS
    // ==========================================

    /**
     * Get the tour this departure belongs to
     */
    public function tour(): BelongsTo
    {
        return $this->belongsTo(Tour::class);
    }

    /**
     * Get all bookings for this departure
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'departure_id');
    }

    /**
     * Get confirmed bookings only
     */
    public function confirmedBookings(): HasMany
    {
        return $this->bookings()
            ->whereIn('status', ['confirmed', 'completed']);
    }

    // ==========================================
    // SCOPES
    // ==========================================

    /**
     * Scope to get only available departures
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', '!=', self::STATUS_CANCELLED)
                     ->where('start_date', '>', now());
    }

    /**
     * Scope to get upcoming departures
     */
    public function scopeUpcoming($query)
    {
        return $query->where('start_date', '>=', now())
                     ->orderBy('start_date', 'asc');
    }

    /**
     * Scope to get departures by status
     */
    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to get guaranteed departures only
     */
    public function scopeGuaranteed($query)
    {
        return $query->where('departure_type', self::TYPE_GUARANTEED);
    }

    // ==========================================
    // ACCESSORS & MUTATORS
    // ==========================================

    /**
     * Get spots remaining
     */
    public function getSpotsRemainingAttribute(): int
    {
        return max(0, $this->max_pax - $this->booked_pax);
    }

    /**
     * Check if departure is sold out
     */
    public function getIsSoldOutAttribute(): bool
    {
        return $this->booked_pax >= $this->max_pax;
    }

    /**
     * Check if departure is guaranteed
     * With tier pricing, all group departures are guaranteed
     */
    public function getIsGuaranteedAttribute(): bool
    {
        // Group departures with tier pricing are always guaranteed
        return $this->departure_type === self::TYPE_GROUP ||
               $this->booked_pax >= $this->min_pax;
    }

    /**
     * Check if departure is filling fast (80%+ capacity)
     */
    public function getIsFillingFastAttribute(): bool
    {
        if ($this->max_pax == 0) return false;
        $percentage = ($this->booked_pax / $this->max_pax) * 100;
        return $percentage >= 80;
    }

    /**
     * Get departure status badge
     */
    public function getStatusBadgeAttribute(): array
    {
        if ($this->is_sold_out || $this->status === self::STATUS_FULL) {
            return [
                'label' => 'Sold Out',
                'color' => 'red',
                'icon' => '❌'
            ];
        }

        if ($this->is_filling_fast) {
            return [
                'label' => 'Filling Fast',
                'color' => 'orange',
                'icon' => '🔥'
            ];
        }

        if ($this->is_guaranteed || $this->status === self::STATUS_GUARANTEED) {
            return [
                'label' => 'Guaranteed Departure',
                'color' => 'green',
                'icon' => '✅'
            ];
        }

        return [
            'label' => 'Available',
            'color' => 'blue',
            'icon' => '📅'
        ];
    }

    /**
     * Get formatted date range
     */
    public function getDateRangeAttribute(): string
    {
        return $this->start_date->format('M d') . ' - ' . $this->end_date->format('M d, Y');
    }

    /**
     * Get short date range (for compact display)
     */
    public function getShortDateRangeAttribute(): string
    {
        if ($this->start_date->month === $this->end_date->month) {
            return $this->start_date->format('M d') . '-' . $this->end_date->format('d, Y');
        }
        return $this->start_date->format('M d') . ' - ' . $this->end_date->format('M d, Y');
    }

    /**
     * Get days until departure
     */
    public function getDaysUntilDepartureAttribute(): int
    {
        return max(0, now()->diffInDays($this->start_date, false));
    }

    /**
     * Check if booking is still open (closes 14 days before departure)
     */
    public function getIsBookingOpenAttribute(): bool
    {
        return $this->days_until_departure > 14 && !$this->is_sold_out;
    }

    // ==========================================
    // METHODS
    // ==========================================

    /**
     * Update booked_pax count based on confirmed bookings
     */
    public function updateBookedPax(): void
    {
        $this->booked_pax = $this->confirmedBookings()->sum('pax_total');
        $this->updateStatus();
        $this->save();
    }

    /**
     * Update status based on current bookings
     */
    public function updateStatus(): void
    {
        if ($this->booked_pax >= $this->max_pax) {
            $this->status = self::STATUS_FULL;
        } elseif ($this->is_guaranteed || $this->departure_type === self::TYPE_GROUP) {
            // Group departures are guaranteed with tier pricing
            $this->status = self::STATUS_GUARANTEED;
        } else {
            $this->status = self::STATUS_OPEN;
        }
    }

    /**
     * Check if departure can accept more bookings
     */
    public function canAcceptBooking(int $guestCount): bool
    {
        return $this->is_booking_open &&
               ($this->booked_pax + $guestCount) <= $this->max_pax;
    }

    /**
     * Get price for this departure (uses tier pricing if available)
     */
    public function getPriceForGuests(int $guestCount): ?TourPricingTier
    {
        return $this->tour->getPricingTierForGuests($guestCount);
    }
}
