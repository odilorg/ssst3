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
}
