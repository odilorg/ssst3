<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\\Translatable\\HasTranslations;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BlogPost extends Model
{
    protected $fillable = [
        'category_id',
        'city_id',
        'title',
        'slug',
        'excerpt',
        'content',
        'featured_image',
        'author_name',
        'author_image',
        'reading_time',
        'view_count',
        'is_featured',
        'is_published',
        'published_at',
        'meta_title',
        'meta_description',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'is_published' => 'boolean',
        'published_at' => 'datetime',
        'view_count' => 'integer',
        'reading_time' => 'integer',
    ];

    /**
     * Bootstrap the model and set up cache invalidation
     */
    protected static function booted()
    {
        // Clear blog caches when a post is saved or deleted
        static::saved(function () {
            self::clearBlogCaches();
        });

        static::deleted(function () {
            self::clearBlogCaches();
        });
    }

    /**
     * Clear all blog-related caches
     * Targeted approach to avoid clearing unrelated cache
     */
    protected static function clearBlogCaches(): void
    {
        $cache = \Illuminate\Support\Facades\Cache::store();

        // Clear specific blog cache keys
        $specificKeys = [
            'blog.categories.all',
            'blog.tags.all',
            'blog.featured',
        ];

        foreach ($specificKeys as $key) {
            $cache->forget($key);
        }

        // Clear blog listing cache with MD5 hashes
        // The BlogController uses: 'blog.listing.' . md5(json_encode($params))
        // Since we can't know all possible parameter combinations, we clear common ones

        // Clear unpaginated and first 10 pages (most common)
        $commonParams = [
            ['page' => 1, 'sort' => 'latest'],
            ['page' => 2, 'sort' => 'latest'],
            ['page' => 1, 'sort' => 'popular'],
            ['page' => 1, 'sort' => 'oldest'],
        ];

        foreach ($commonParams as $params) {
            $cacheKey = 'blog.listing.' . md5(json_encode($params));
            $cache->forget($cacheKey);
        }

        // Note: MD5-based cache keys for filtered listings will expire naturally
        // after their TTL (10 minutes for listings, 1 hour for individual posts)
        // This is acceptable as cache invalidation is triggered infrequently (on post save/delete)
        // and the short TTL ensures stale data doesn't persist long

        // Future improvement: Consider using cache tags (requires Redis/Memcached)
        // or maintaining a cache key registry for more comprehensive invalidation
    }

    /**
     * Get the category for this post
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(BlogCategory::class, 'category_id');
    }

    /**
     * Get all tags for this post
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(BlogTag::class, 'blog_post_tag');
    }

    /**
     * Get all comments for this post
     */
    public function comments(): HasMany
    {
        return $this->hasMany(BlogComment::class, 'blog_post_id');
    }

    /**
     * Get approved comments for this post
     */
    public function approvedComments(): HasMany
    {
        return $this->hasMany(BlogComment::class, 'blog_post_id')
            ->where('status', 'approved')
            ->oldest();
    }

    /**
     * Get top-level approved comments (not replies)
     */
    public function topLevelComments(): HasMany
    {
        return $this->hasMany(BlogComment::class, 'blog_post_id')
            ->whereNull('parent_id')
            ->where('status', 'approved')
            ->latest();
    }

    /**
     * Increment view count
     */
    public function incrementViews(): void
    {
        $this->increment('view_count');
    }

    /**
     * Get the full URL for the featured image
     * Handles both public folder images (images/) and storage folder images
     */
    public function getFeaturedImageUrlAttribute(): ?string
    {
        if (empty($this->featured_image)) {
            return null;
        }

        // If it's already a full URL, return as-is
        if (str_starts_with($this->featured_image, 'http')) {
            return $this->featured_image;
        }

        // If path starts with 'images/', it's in public folder
        if (str_starts_with($this->featured_image, 'images/')) {
            return asset($this->featured_image);
        }

        // Otherwise it's in storage folder (uploaded via admin)
        return asset('storage/' . $this->featured_image);
    }

    /**
     * Get the full URL for the WebP featured image
     */
    public function getFeaturedImageWebpUrlAttribute(): ?string
    {
        if (empty($this->featured_image_webp)) {
            return null;
        }

        // WebP images are always in storage
        return asset('storage/' . $this->featured_image_webp);
    }

    /**
     * Get the responsive image sizes as an array
     */
    public function getFeaturedImageSizesArrayAttribute(): array
    {
        if (empty($this->featured_image_sizes)) {
            return [];
        }

        return json_decode($this->featured_image_sizes, true) ?? [];
    }

    /**
     * Get WebP srcset string for responsive images
     */
    public function getFeaturedImageWebpSrcsetAttribute(): ?string
    {
        $sizes = $this->featured_image_sizes_array;

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
     * Check if WebP version is available
     */
    public function getHasWebpAttribute(): bool
    {
        return !empty($this->featured_image_webp) &&
               $this->image_processing_status === 'completed';
    }

    /**
     * Scope for published posts
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true)->whereNotNull('published_at');
    }

    /**
     * Scope for featured posts
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope for popular posts (by views)
     */
    public function scopePopular($query, $limit = 5)
    {
        return $query->published()->orderBy('view_count', 'desc')->limit($limit);
    }

    /**
     * Scope for recent posts
     */
    public function scopeRecent($query, $limit = 5)
    {
        return $query->published()->orderBy('published_at', 'desc')->limit($limit);
    }

    /**
     * Get the city/destination for this post
     */
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    /**
     * Get related tours for this blog post
     * Matching logic:
     * 1. If city_id is set, get tours from that city
     * 2. Otherwise, try to extract city name from title/content and match
     * 3. Fallback to most popular tours
     *
     * @param int $limit Number of tours to return (default: 3)
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getRelatedTours(int $limit = 3)
    {
        // Strategy 1: Direct city relationship
        if ($this->city_id) {
            $tours = Tour::where('city_id', $this->city_id)
                ->where('is_active', true)
                ->with(['city:id,name,slug', 'categories:id,name,slug'])
                ->orderBy('rating', 'desc')
                ->orderBy('review_count', 'desc')
                ->limit($limit)
                ->get();

            if ($tours->isNotEmpty()) {
                return $tours;
            }
        }

        // Strategy 2: Extract city names from title/content
        // Only load minimal fields needed for matching
        $cities = City::active()
            ->select('id', 'name', 'slug')
            ->get();
        $foundCity = null;

        foreach ($cities as $city) {
            // Check if city name appears in title or content (case insensitive)
            $searchText = $this->title . ' ' . $this->excerpt . ' ' . strip_tags($this->content);
            if (stripos($searchText, $city->name) !== false || stripos($searchText, $city->slug) !== false) {
                $foundCity = $city;
                break;
            }
        }

        if ($foundCity) {
            $tours = Tour::where('city_id', $foundCity->id)
                ->where('is_active', true)
                ->with(['city:id,name,slug', 'categories:id,name,slug'])
                ->orderBy('rating', 'desc')
                ->orderBy('review_count', 'desc')
                ->limit($limit)
                ->get();

            if ($tours->isNotEmpty()) {
                return $tours;
            }
        }

        // Strategy 3: Fallback to most popular tours globally
        return Tour::where('is_active', true)
            ->with(['city:id,name,slug', 'categories:id,name,slug'])
            ->orderBy('rating', 'desc')
            ->orderBy('review_count', 'desc')
            ->limit($limit)
            ->get();
    }
}
