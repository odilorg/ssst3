<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingTraveler extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'full_name',
        'date_of_birth',
        'nationality',
        'passport_number',
        'passport_expiry',
        'dietary_requirements',
        'special_needs',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'passport_expiry' => 'date',
    ];

    // ==========================================
    // RELATIONSHIPS
    // ==========================================

    /**
     * Get the booking this traveler belongs to
     */
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    // ==========================================
    // BUSINESS LOGIC METHODS
    // ==========================================

    /**
     * Check if passport is valid (not expired)
     */
    public function hasValidPassport(): bool
    {
        if (!$this->passport_expiry) {
            return false;
        }

        return $this->passport_expiry->isFuture();
    }

    /**
     * Check if traveler is an adult (>= 18 years old)
     */
    public function isAdult(): bool
    {
        return $this->getAge() >= 18;
    }

    /**
     * Get traveler age
     */
    public function getAge(): ?int
    {
        if (!$this->date_of_birth) {
            return null;
        }

        return $this->date_of_birth->age;
    }

    /**
     * Check if traveler has dietary requirements
     */
    public function hasDietaryRequirements(): bool
    {
        return !empty($this->dietary_requirements);
    }

    /**
     * Check if traveler has special needs
     */
    public function hasSpecialNeeds(): bool
    {
        return !empty($this->special_needs);
    }

    /**
     * Get initials for display
     */
    public function getInitials(): string
    {
        $names = explode(' ', $this->full_name);

        if (count($names) === 1) {
            return strtoupper(substr($names[0], 0, 2));
        }

        return strtoupper(substr($names[0], 0, 1) . substr($names[count($names) - 1], 0, 1));
    }
}
