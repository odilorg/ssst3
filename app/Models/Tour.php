<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Tour extends Model
{
    use HasFactory;

    protected $fillable = [
        // Basic Info
        'title',
        'slug',
        'short_description',
        'long_description',

        // Duration
        'duration_days',
        'duration_text',

        // Pricing
        'price_per_person',
        'currency',

        // Capacity
        'max_guests',
        'min_guests',

        // Images
        'hero_image',
        'gallery_images',

        // Content (JSON)
        'highlights',
        'included_items',
        'excluded_items',
        'languages',
        'requirements',
        'include_global_requirements',
        'include_global_faqs',

        // Tour Meta
        'tour_type',
        'city_id',
        'is_active',

        // Ratings (cached)
        'rating',
        'review_count',

        // Booking Settings
        'min_booking_hours',
        'has_hotel_pickup',
        'pickup_radius_km',

        // Meeting Point
        'meeting_point_address',
        'meeting_instructions',
        'meeting_lat',
        'meeting_lng',

        // Cancellation
        'cancellation_hours',
        'cancellation_policy',
    ];

    protected $casts = [
        // Booleans
        'is_active' => 'boolean',
        'include_global_requirements' => 'boolean',
        'include_global_faqs' => 'boolean',
        'has_hotel_pickup' => 'boolean',

        // Integers
        'duration_days' => 'integer',
        'max_guests' => 'integer',
        'min_guests' => 'integer',
        'review_count' => 'integer',
        'min_booking_hours' => 'integer',
        'pickup_radius_km' => 'integer',
        'cancellation_hours' => 'integer',

        // Decimals
        'price_per_person' => 'decimal:2',
        'rating' => 'decimal:2',
        'meeting_lat' => 'decimal:8',
        'meeting_lng' => 'decimal:8',

        // JSON Arrays
        'gallery_images' => 'array',
        'highlights' => 'array',
        'included_items' => 'array',
        'excluded_items' => 'array',
        'languages' => 'array',
        'requirements' => 'array',
    ];

    /**
     * Boot method - Auto-generate slug
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($tour) {
            if (empty($tour->slug)) {
                $slug = Str::slug($tour->title);
                $count = 1;
                $originalSlug = $slug;

                // Handle duplicate slugs
                while (static::where('slug', $slug)->exists()) {
                    $slug = $originalSlug . '-' . $count;
                    $count++;
                }

                $tour->slug = $slug;
            }
        });
    }

    // ==========================================
    // RELATIONSHIPS
    // ==========================================

    /**
     * Get the city this tour belongs to
     */
    public function city()
    {
        return $this->belongsTo(City::class);
    }

    /**
     * Get all itinerary items for this tour
     */
    public function itineraryItems()
    {
        return $this->hasMany(ItineraryItem::class);
    }

    /**
     * Get top-level itinerary items (days or main stops)
     */
    public function topLevelItems()
    {
        return $this->itineraryItems()->whereNull('parent_id')->orderBy('sort_order');
    }

    /**
     * Get all bookings for this tour
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Get all FAQs for this tour
     */
    public function faqs()
    {
        return $this->hasMany(TourFaq::class)->orderBy('sort_order');
    }

    /**
     * Get all extras/add-ons for this tour
     */
    public function extras()
    {
        return $this->hasMany(TourExtra::class)->orderBy('sort_order');
    }

    /**
     * Get active extras only
     */
    public function activeExtras()
    {
        return $this->extras()->where('is_active', true);
    }

    /**
     * Get all reviews for this tour
     */
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Get approved reviews only (for public display)
     */
    public function approvedReviews()
    {
        return $this->reviews()->where('is_approved', true)->latest();
    }

    /**
     * Get all categories this tour belongs to
     */
    public function categories()
    {
        return $this->belongsToMany(TourCategory::class, 'tour_category_tour')
            ->withTimestamps();
    }

    // ==========================================
    // HELPER METHODS
    // ==========================================

    /**
     * Check if tour is single-day (uses hours)
     */
    public function isSingleDay(): bool
    {
        return $this->duration_days === 1;
    }

    /**
     * Check if tour is multi-day
     */
    public function isMultiDay(): bool
    {
        return $this->duration_days > 1;
    }

    /**
     * Get formatted duration for display
     */
    public function getFormattedDuration(): string
    {
        if ($this->duration_text) {
            return $this->duration_text;
        }

        if ($this->isSingleDay()) {
            return '1 day';
        }

        return "{$this->duration_days} days";
    }

    /**
     * Update cached rating and review count
     */
    public function updateRatingCache(): void
    {
        $approved = $this->reviews()->where('is_approved', true);

        $this->update([
            'rating' => round($approved->avg('rating'), 2) ?? 0,
            'review_count' => $approved->count(),
        ]);
    }

    // ==========================================
    // ACCESSORS
    // ==========================================

    /**
     * Get the featured image URL
     * Accessor for blade templates (uses hero_image field)
     */
    public function getFeaturedImageUrlAttribute(): ?string
    {
        if (empty($this->hero_image)) {
            return null; // Blade will use default image
        }

        // If hero_image is already a full URL
        if (str_starts_with($this->hero_image, 'http')) {
            return $this->hero_image;
        }

        // If hero_image is a storage path
        return asset('storage/' . $this->hero_image);
    }
}
