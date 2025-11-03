<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class TourCategory extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'image_path',
        'hero_image',
        'display_order',
        'is_active',
        'show_on_homepage',
        'meta_title',
        'meta_description',
    ];

    protected $casts = [
        'name' => 'array',
        'description' => 'array',
        'meta_title' => 'array',
        'meta_description' => 'array',
        'is_active' => 'boolean',
        'show_on_homepage' => 'boolean',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-generate slug from English name if not provided
        static::creating(function ($category) {
            if (empty($category->slug) && !empty($category->name['en'])) {
                $category->slug = Str::slug($category->name['en']);
            }
        });

        // Clear cache when category is created, updated, or deleted
        static::saved(function () {
            Cache::forget('homepage_categories');
            Cache::forget('active_categories');
        });

        static::deleted(function () {
            Cache::forget('homepage_categories');
            Cache::forget('active_categories');
        });
    }

    /**
     * Relationship: Tours that belong to this category
     */
    public function tours(): BelongsToMany
    {
        return $this->belongsToMany(Tour::class, 'tour_category_tour')
            ->withTimestamps();
    }

    /**
     * Get cached tour count for this category
     */
    public function getCachedTourCountAttribute(): int
    {
        return Cache::remember(
            "category_{$this->id}_tour_count",
            now()->addHours(6),
            fn() => $this->tours()->where('is_active', true)->count()
        );
    }

    /**
     * Get translated name for current locale
     */
    public function getTranslatedNameAttribute(): string
    {
        $locale = app()->getLocale();
        return $this->name[$locale] ?? $this->name['en'] ?? 'Untitled';
    }

    /**
     * Get translated description for current locale
     */
    public function getTranslatedDescriptionAttribute(): ?string
    {
        $locale = app()->getLocale();
        return $this->description[$locale] ?? $this->description['en'] ?? null;
    }

    /**
     * Scope: Only active categories
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Categories to show on homepage
     */
    public function scopeHomepage($query)
    {
        return $query->where('is_active', true)
            ->where('show_on_homepage', true)
            ->orderBy('display_order');
    }

    /**
     * Get all categories for homepage (cached)
     */
    public static function getHomepageCategories()
    {
        return Cache::remember('homepage_categories', now()->addHours(12), function () {
            return static::homepage()->get();
        });
    }
}
