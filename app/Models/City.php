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
        'country',
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
     * Get all translations for this city.
     */
    public function translations(): HasMany
    {
        return $this->hasMany(CityTranslation::class);
    }

    /**
     * Get translation for a specific locale.
     *
     * @param string|null $locale Locale code (defaults to current app locale)
     * @return CityTranslation|null
     */
    public function translation(?string $locale = null): ?CityTranslation
    {
        $locale = $locale ?? app()->getLocale();

        return $this->translations->first(fn ($t) => $t->locale === $locale);
    }

    /**
     * Get translation for locale, falling back to default locale.
     *
     * @param string|null $locale Locale code (defaults to current app locale)
     * @return CityTranslation|null
     */
    public function translationOrDefault(?string $locale = null): ?CityTranslation
    {
        $locale = $locale ?? app()->getLocale();
        $defaultLocale = config('multilang.default_locale', 'en');

        // Try requested locale first
        $translation = $this->translation($locale);
        if ($translation) {
            return $translation;
        }

        // Fall back to default locale if different
        if ($locale !== $defaultLocale) {
            return $this->translation($defaultLocale);
        }

        return null;
    }

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

    /**
     * Get the full URL for the WebP hero image
     */
    public function getHeroImageWebpUrlAttribute(): ?string
    {
        if (empty($this->hero_image_webp)) {
            return null;
        }
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
            return self::where('country', 'Uzbekistan')->active()
                ->featured()
                ->orderBy('display_order')
                ->orderBy('name')->take(4)
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
