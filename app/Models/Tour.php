<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Spatie\Translatable\HasTranslations;

class Tour extends Model
{
    use HasFactory;
    use HasTranslations;

    public array $translatable = [
        'title',
        'short_description',
        'long_description',
        'seo_title',
        'seo_description',
        'seo_keywords',
        'highlights',
        'included_items',
        'excluded_items',
    ];

    protected $fillable = [
        // Basic Info
        'title',
        'slug',
        'short_description',
        'long_description',

        // SEO Fields
        'seo_title',
        'seo_description',
        'seo_keywords',
        'og_image',
        'schema_enabled',
        'schema_override',

        // Duration
        'duration_days',
        'duration_text',

        // Pricing
        'price_per_person',
        'currency',
        'show_price',

        // Capacity
        'max_guests',
        'min_guests',

        // Images
        'hero_image',
        'hero_image_webp',
        'hero_image_sizes',
        'image_processing_status',
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
        'show_price' => 'boolean',
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
     * Boot method - Auto-generate slug and handle cache invalidation
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

        // Clear category caches when tour active status changes
        static::updated(function ($tour) {
            // Use TourCacheService for comprehensive cache clearing
            app(\App\Services\TourCacheService::class)->clearTourCache($tour);

            if ($tour->isDirty('is_active')) {
                $tour->clearCategoryCaches();
            }
        });

        // Clear category caches when tour is deleted
        static::deleting(function ($tour) {
            $tour->clearCategoryCaches();
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
     * Get all inquiries for this tour
     */
    public function inquiries()
    {
        return $this->hasMany(TourInquiry::class);
    }

    /**
     * Get all categories this tour belongs to
     */
    public function categories()
    {
        return $this->belongsToMany(TourCategory::class, 'tour_category_tour')
            ->withTimestamps()
            ->withPivot([]) // Enable pivot events
            ->using(new class extends \Illuminate\Database\Eloquent\Relations\Pivot {
                protected static function boot()
                {
                    parent::boot();

                    // Clear caches when pivot is created (category assigned)
                    static::created(function ($pivot) {
                        \Illuminate\Support\Facades\Cache::forget("category_{$pivot->tour_category_id}_tour_count");
                        \Illuminate\Support\Facades\Cache::forget("related_categories.{$pivot->tour_category_id}");
                    });

                    // Clear caches when pivot is deleted (category removed)
                    static::deleted(function ($pivot) {
                        \Illuminate\Support\Facades\Cache::forget("category_{$pivot->tour_category_id}_tour_count");
                        \Illuminate\Support\Facades\Cache::forget("related_categories.{$pivot->tour_category_id}");
                    });
                }
            });
    }

    // ==========================================
    // QUERY SCOPES
    // ==========================================

    /**
     * Scope to filter only active tours
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to eager load common frontend relationships
     * Prevents N+1 queries when displaying tours
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithFrontendRelations($query)
    {
        return $query->with([
            'city:id,name,slug,hero_image',
            'categories:id,name,slug',
        ]);
    }

    /**
     * Scope to eager load detailed relationships for tour detail page
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithDetailRelations($query)
    {
        return $query->with([
            'city:id,name,slug,description,hero_image',
            'categories:id,name,slug,icon',
            'itineraryItems' => function($q) {
                $q->whereNull('parent_id')
                  ->orderBy('sort_order')
                  ->with('children');
            },
            'faqs' => function($q) {
                $q->orderBy('sort_order');
            },
            'activeExtras' => function($q) {
                $q->orderBy('sort_order');
            },
        ]);
    }

    /**
     * Scope for tours with reviews and ratings
     * Useful for featuring highly-rated tours
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithReviews($query)
    {
        return $query->whereNotNull('rating')
                     ->where('review_count', '>', 0);
    }

    /**
     * Scope to filter tours by city
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int|string $cityId City ID or slug
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByCity($query, $cityId)
    {
        if (is_numeric($cityId)) {
            return $query->where('city_id', $cityId);
        }

        // If slug is provided, join with cities table
        return $query->whereHas('city', function($q) use ($cityId) {
            $q->where('slug', $cityId);
        });
    }

    /**
     * Scope to filter tours by category
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int|string $categoryId Category ID or slug
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByCategory($query, $categoryId)
    {
        return $query->whereHas('categories', function($q) use ($categoryId) {
            if (is_numeric($categoryId)) {
                $q->where('tour_categories.id', $categoryId);
            } else {
                $q->where('tour_categories.slug', $categoryId);
            }
        });
    }

    /**
     * Scope to order tours by popularity (rating + review count)
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $direction 'desc' or 'asc'
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePopular($query, $direction = 'desc')
    {
        return $query->orderBy('rating', $direction)
                     ->orderBy('review_count', $direction);
    }

    /**
     * Scope to order tours by creation date
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $direction 'desc' or 'asc'
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRecent($query, $direction = 'desc')
    {
        return $query->orderBy('created_at', $direction);
    }

    /**
     * Scope to filter tours by tour type
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $type 'private_only', 'group_only', or 'hybrid'
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByType($query, $type)
    {
        return $query->where('tour_type', $type);
    }

    /**
     * Scope to filter tours by duration
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $minDays Minimum number of days
     * @param int|null $maxDays Maximum number of days (optional)
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByDuration($query, $minDays, $maxDays = null)
    {
        $query->where('duration_days', '>=', $minDays);

        if ($maxDays !== null) {
            $query->where('duration_days', '<=', $maxDays);
        }

        return $query;
    }

    /**
     * Scope to filter tours by price range
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param float $minPrice Minimum price
     * @param float|null $maxPrice Maximum price (optional)
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByPriceRange($query, $minPrice, $maxPrice = null)
    {
        $query->where('price_per_person', '>=', $minPrice);

        if ($maxPrice !== null) {
            $query->where('price_per_person', '<=', $maxPrice);
        }

        return $query;
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

    /**
     * Clear all category-related caches for this tour
     */
    public function clearCategoryCaches(): void
    {
        // Clear cache for each category this tour belongs to
        foreach ($this->categories as $category) {
            \Illuminate\Support\Facades\Cache::forget("category_{$category->id}_tour_count");
            \Illuminate\Support\Facades\Cache::forget("related_categories.{$category->slug}");
            \Illuminate\Support\Facades\Cache::forget("category_data.{$category->slug}." . app()->getLocale());
        }
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
        // If path starts with 'images/', it's in public folder (not storage)
        if (str_starts_with($this->hero_image, 'images/')) {
            return asset($this->hero_image);
        }

        return asset('storage/' . $this->hero_image);
    }

    /**
     * Get the full URL for the WebP hero image
     */
    public function getHeroImageWebpUrlAttribute(): ?string
    {
        if (empty($this->hero_image_webp)) {
            return null;
        }

        // WebP images are always in storage
        return asset('storage/' . $this->hero_image_webp);
    }

    /**
     * Get the responsive hero image sizes as an array
     */
    public function getHeroImageSizesArrayAttribute(): array
    {
        if (empty($this->hero_image_sizes)) {
            return [];
        }

        return json_decode($this->hero_image_sizes, true) ?? [];
    }

    /**
     * Get WebP srcset string for responsive hero images
     */
    public function getHeroImageWebpSrcsetAttribute(): ?string
    {
        $sizes = $this->hero_image_sizes_array;

        if (empty($sizes)) {
            return null;
        }

        $srcset = [];
        $widths = [
            'thumb' => 300,
            'medium' => 800,
            'large' => 1920,
            'xlarge' => 2560,
        ];

        foreach ($sizes as $sizeName => $path) {
            if (isset($widths[$sizeName])) {
                $srcset[] = asset('storage/' . $path) . ' ' . $widths[$sizeName] . 'w';
            }
        }

        return !empty($srcset) ? implode(', ', $srcset) : null;
    }

    /**
     * Check if WebP version is available for hero image
     */
    public function getHasWebpAttribute(): bool
    {
        return !empty($this->hero_image_webp) &&
               $this->image_processing_status === 'completed';
    }

    // ==========================================
    // SEO HELPER METHODS
    // ==========================================

    /**
     * Get the SEO title with fallback to regular title
     */
    public function getSeoTitle(): string
    {
        return $this->attributes['seo_title'] ?? ($this->attributes['title'] . ' | Jahongir Travel');
    }

    /**
     * Get the SEO description with fallback
     */
    public function getSeoDescription(): ?string
    {
        if (!empty($this->attributes['seo_description'])) {
            return $this->attributes['seo_description'];
        }

        // Fallback to short_description or stripped long_description
        if (!empty($this->attributes['short_description'])) {
            return \Illuminate\Support\Str::limit(strip_tags($this->attributes['short_description']), 160);
        }

        if (!empty($this->attributes['long_description'])) {
            return \Illuminate\Support\Str::limit(strip_tags($this->attributes['long_description']), 160);
        }

        return null;
    }

    /**
     * Get the Open Graph image with fallback to hero_image
     */
    public function getOgImageUrl(): string
    {
        if (!empty($this->attributes['og_image'])) {
            if (str_starts_with($this->attributes['og_image'], 'http')) {
                return $this->attributes['og_image'];
            }
            return asset('storage/' . $this->attributes['og_image']);
        }

        // Fallback to hero image or default
        return $this->featured_image_url ?? asset('images/default-tour.jpg');
    }

    /**
     * Generate Schema.org JSON-LD structured data for this tour
     */
    public function generateSchemaData(): ?array
    {
        if (!$this->schema_enabled) {
            return null;
        }

        // If custom schema override exists, use it
        if (!empty($this->schema_override)) {
            return is_array($this->schema_override) ? $this->schema_override : json_decode($this->schema_override, true);
        }

        // Auto-generate schema from tour data
        $schema = [
            "@context" => "https://schema.org",
            "@type" => "Tour",
            "name" => $this->title,
            "description" => strip_tags($this->short_description ?? $this->long_description),
            "provider" => [
                "@type" => "Organization",
                "name" => "Jahongir Travel",
                "url" => url('/'),
            ],
        ];

        // Add images if available
        if ($this->hero_image || $this->gallery_images) {
            $images = [];
            if ($this->hero_image) {
                $images[] = $this->featured_image_url;
            }
            if (!empty($this->gallery_images) && is_array($this->gallery_images)) {
                foreach ($this->gallery_images as $img) {
                    $images[] = asset('storage/' . (is_array($img) ? ($img['path'] ?? '') : $img));
                }
            }
            $schema['image'] = $images;
        }

        // Add pricing
        if ($this->price_per_person) {
            $schema['offers'] = [
                "@type" => "Offer",
                "price" => number_format($this->price_per_person, 2, '.', ''),
                "priceCurrency" => $this->currency,
                "availability" => "https://schema.org/InStock",
                "url" => url('/tours/' . $this->slug),
            ];
        }

        // Add duration
        if ($this->duration_days) {
            if ($this->duration_days === 1) {
                $schema['duration'] = "P1D"; // ISO 8601 duration format
            } else {
                $schema['duration'] = "P{$this->duration_days}D";
            }
        }

        // Add ratings if available
        if ($this->rating && $this->review_count > 0) {
            $schema['aggregateRating'] = [
                "@type" => "AggregateRating",
                "ratingValue" => number_format($this->rating, 1),
                "reviewCount" => $this->review_count,
                "bestRating" => "5",
                "worstRating" => "1",
            ];
        }

        // Add city/location if available
        if ($this->city) {
            $schema['touristType'] = $this->tour_type === 'private_only' ? 'Private Tour' : ($this->tour_type === 'group_only' ? 'Group Tour' : 'Private & Group Tour');
            $schema['location'] = [
                "@type" => "Place",
                "name" => $this->city->name,
                "address" => [
                    "@type" => "PostalAddress",
                    "addressLocality" => $this->city->name,
                    "addressCountry" => "UZ",
                ],
            ];
        }

        return $schema;
    }

    /**
     * Generate BreadcrumbList schema for this tour
     */
    public function generateBreadcrumbSchema(): array
    {
        $breadcrumbs = [
            "@context" => "https://schema.org",
            "@type" => "BreadcrumbList",
            "itemListElement" => []
        ];

        // 1. Home
        $breadcrumbs['itemListElement'][] = [
            "@type" => "ListItem",
            "position" => 1,
            "name" => "Home",
            "item" => url('/')
        ];

        // 2. Tours
        $breadcrumbs['itemListElement'][] = [
            "@type" => "ListItem",
            "position" => 2,
            "name" => "Tours",
            "item" => url('/tours')
        ];

        // 3. City (if available)
        if ($this->city) {
            $breadcrumbs['itemListElement'][] = [
                "@type" => "ListItem",
                "position" => 3,
                "name" => $this->city->name . " Tours",
                "item" => url('/tours?city=' . $this->city->slug)
            ];

            // 4. Current Tour
            $breadcrumbs['itemListElement'][] = [
                "@type" => "ListItem",
                "position" => 4,
                "name" => $this->title,
                "item" => url('/tours/' . $this->slug)
            ];
        } else {
            // 3. Current Tour (no city)
            $breadcrumbs['itemListElement'][] = [
                "@type" => "ListItem",
                "position" => 3,
                "name" => $this->title,
                "item" => url('/tours/' . $this->slug)
            ];
        }

        return $breadcrumbs;
    }

    /**
     * Generate FAQPage schema for this tour's FAQs
     */
    public function generateFaqSchema(): ?array
    {
        // Get FAQs for this tour
        $faqs = $this->faqs()->get();

        // If no FAQs, return null
        if ($faqs->isEmpty()) {
            return null;
        }

        $faqSchema = [
            "@context" => "https://schema.org",
            "@type" => "FAQPage",
            "mainEntity" => []
        ];

        foreach ($faqs as $faq) {
            $faqSchema['mainEntity'][] = [
                "@type" => "Question",
                "name" => $faq->question,
                "acceptedAnswer" => [
                    "@type" => "Answer",
                    "text" => strip_tags($faq->answer)
                ]
            ];
        }

        return $faqSchema;
    }

    /**
     * Check if price should be displayed publicly
     */
    public function shouldShowPrice(): bool
    {
        return $this->show_price && !empty($this->price_per_person);
    }

    /**
     * Get unique cities from itinerary in order
     */
    public function getRouteCities()
    {
        $cities = collect();

        foreach ($this->topLevelItems as $item) {
            foreach ($item->cities as $city) {
                if (!$cities->contains('id', $city->id)) {
                    $cities->push($city);
                }
            }
        }

        return $cities;
    }

    /**
     * Get route as string (e.g., "Tashkent → Samarkand → Bukhara")
     */
    public function getRouteString()
    {
        return $this->getRouteCities()
                    ->pluck('name')
                    ->join(' → ');
    }

    /**
     * Accessor for gallery_images - Return array with 'path' and 'alt' keys for Filament Repeater
     */
    public function getGalleryImagesAttribute($value)
    {
        $decoded = json_decode($value, true);

        if (!is_array($decoded)) {
            return [];
        }

        // Return the array as-is (Repeater expects array with 'path' and 'alt' keys)
        return $decoded;
    }

    /**
     * Mutator for gallery_images - Store as JSON array with 'path' and 'alt' keys
     */
    public function setGalleryImagesAttribute($value)
    {
        if (is_null($value)) {
            $this->attributes['gallery_images'] = null;
            return;
        }

        // If it's already an array, store as JSON
        if (is_array($value)) {
            $this->attributes['gallery_images'] = json_encode(array_values($value));
            return;
        }

        $this->attributes['gallery_images'] = $value;
    }
}
