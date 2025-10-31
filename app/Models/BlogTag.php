<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class BlogTag extends Model
{
    protected $fillable = [
        'name',
        'slug',
    ];

    /**
     * Get all posts with this tag
     */
    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(BlogPost::class, 'blog_post_tag');
    }

    /**
     * Get published posts with this tag
     */
    public function publishedPosts(): BelongsToMany
    {
        return $this->posts()->where('is_published', true)->whereNotNull('published_at');
    }
}
