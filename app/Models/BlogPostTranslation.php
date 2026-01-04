<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * BlogPostTranslation Model
 *
 * Stores translated content for blog posts in multiple languages.
 *
 * @property int $id
 * @property int $blog_post_id
 * @property string $locale
 * @property string $title
 * @property string $slug
 * @property string|null $excerpt
 * @property string|null $content
 * @property string|null $seo_title
 * @property string|null $seo_description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read BlogPost $blogPost
 */
class BlogPostTranslation extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'blog_post_id',
        'locale',
        'title',
        'slug',
        'excerpt',
        'content',
        'seo_title',
        'seo_description',
    ];

    /**
     * Get the blog post that owns this translation.
     */
    public function blogPost(): BelongsTo
    {
        return $this->belongsTo(BlogPost::class);
    }
}
