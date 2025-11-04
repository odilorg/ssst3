<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class City extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'tagline',
        'description',
        'short_description',
        'long_description',
        'images',
        'featured_image',
        'hero_image',
        'latitude',
        'longitude',
        'display_order',
        'is_featured',
        'is_active',
        'meta_title',
        'meta_description',
        'tour_count_cache',
    ];

    protected $casts = [
        'images' => 'array',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'latitude' => 'decimal:6',
        'longitude' => 'decimal:6',
        'display_order' => 'integer',
        'tour_count_cache' => 'integer',
    ];

    /**
     * Boot method - Register model observers
     */
    protected static function booted(): void
    {
        // Auto-generate slug from name if not provided
        static::creating(function (City $city) {
            if (empty($city->slug)) {
                $city->slug = Str::slug($city->name);
            }
        });

        // Clear cache when city is saved or deleted
        static::saved(function () {
            Cache::forget('homepage.cities');
        });

        static::deleted(function () {
            Cache::forget('homepage.cities');
        });
    }

    // ========================================
    // RELATIONSHIPS
    // ========================================

    /**
     * Get all tours for this city
     */
    public function tours(): HasMany
    {
        return $this->hasMany(Tour::class, 'city_id');
    }

    /**
     * Get all companies in this city
     */
    public function companies(): HasMany
    {
        return $this->hasMany(Company::class);
    }

    /**
     * Get all hotels in this city
     */
    public function hotels(): HasMany
    {
        return $this->hasMany(Hotel::class);
    }

    /**
     * Get all restaurants in this city
     */
    public function restaurants(): HasMany
    {
        return $this->hasMany(Restaurant::class);
    }

    /**
     * Get all monuments in this city
     */
    public function monuments(): HasMany
    {
        return $this->hasMany(Monument::class);
    }

    /**
     * Get all transports in this city
     */
    public function transports(): HasMany
    {
        return $this->hasMany(Transport::class);
    }

    // ========================================
    // SCOPES
    // ========================================

    /**
     * Scope to get only active cities
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get only featured cities
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    // ========================================
    // ACCESSORS
    // ========================================

    /**
     * Get the URL for the featured image
     */
    public function getFeaturedImageUrlAttribute(): ?string
    {
        if (empty($this->featured_image)) {
            return null;
        }

        // If it's already a full URL, return as is
        if (str_starts_with($this->featured_image, 'http')) {
            return $this->featured_image;
        }

        // If path starts with 'images/', it's in public folder
        if (str_starts_with($this->featured_image, 'images/')) {
            return asset($this->featured_image);
        }

        // Otherwise, it's in storage (Filament uploads)
        return asset('storage/' . $this->featured_image);
    }

    /**
     * Get the URL for the hero image
     */
    public function getHeroImageUrlAttribute(): ?string
    {
        if (empty($this->hero_image)) {
            return null;
        }

        if (str_starts_with($this->hero_image, 'http')) {
            return $this->hero_image;
        }

        return asset('storage/' . $this->hero_image);
    }

    // ========================================
    // STATIC METHODS
    // ========================================

    /**
     * Get cities to display on homepage
     * Cached for 1 hour
     */
    public static function getHomepageCities()
    {
        return Cache::remember('homepage.cities', 3600, function () {
            return self::active()
                ->featured()
                ->orderBy('display_order')
                ->orderBy('name')
                ->get();
        });
    }

    // ========================================
    // INSTANCE METHODS
    // ========================================

    /**
     * Update the cached tour count for this city
     */
    public function updateTourCount(): void
    {
        $this->tour_count_cache = $this->tours()->where('is_active', true)->count();
        $this->saveQuietly(); // Save without triggering events
    }

    /**
     * Get the tour count (uses cache if available)
     */
    public function getTourCountAttribute(): int
    {
        if ($this->tour_count_cache > 0) {
            return $this->tour_count_cache;
        }

        return $this->tours()->where('is_active', true)->count();
    }
}
