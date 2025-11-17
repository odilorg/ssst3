<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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
     * Simple approach for cache drivers that don't support tagging
     */
    protected static function clearBlogCaches(): void
    {
        $cache = \Illuminate\Support\Facades\Cache::store();

        // Clear specific blog cache keys
        $cache->forget('blog.categories.all');
        $cache->forget('blog.tags.all');
        $cache->forget('blog.featured');

        // For blog listing caches, we flush all since we can't use patterns
        // This is acceptable for admin operations which are infrequent
        $cache->flush();
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
                ->orderBy('rating', 'desc')
                ->orderBy('review_count', 'desc')
                ->limit($limit)
                ->get();

            if ($tours->isNotEmpty()) {
                return $tours;
            }
        }

        // Strategy 2: Extract city names from title/content
        $cities = City::active()->get();
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
            ->orderBy('rating', 'desc')
            ->orderBy('review_count', 'desc')
            ->limit($limit)
            ->get();
    }
}
