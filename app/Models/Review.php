<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = [
        'tour_id',
        'booking_id',
        'reviewer_name',
        'reviewer_email',
        'reviewer_location',
        'rating',
        'title',
        'content',
        'avatar_url',
        'source',
        'is_verified',
        'is_approved',
    ];

    protected $casts = [
        'rating' => 'integer',
        'is_verified' => 'boolean',
        'is_approved' => 'boolean',
    ];

    /**
     * Get the tour this review belongs to
     */
    public function tour()
    {
        return $this->belongsTo(Tour::class);
    }

    /**
     * Get the booking this review is linked to (if verified)
     */
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Scope: Only approved reviews (for public display)
     */
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    /**
     * Scope: Only verified reviews (linked to booking)
     */
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    /**
     * Scope: By rating
     */
    public function scopeByRating($query, $rating)
    {
        return $query->where('rating', $rating);
    }

    /**
     * Boot method - Update tour rating cache when review is saved
     */
    protected static function boot()
    {
        parent::boot();

        static::saved(function ($review) {
            if ($review->tour) {
                $review->tour->updateRatingCache();
            }
        });

        static::deleted(function ($review) {
            if ($review->tour) {
                $review->tour->updateRatingCache();
            }
        });
    }
}
