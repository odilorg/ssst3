<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * TourTranslation Model
 *
 * Stores translated content for tours in multiple languages.
 *
 * @property int $id
 * @property int $tour_id
 * @property string $locale
 * @property string $title
 * @property string $slug
 * @property string|null $excerpt
 * @property string|null $content
 * @property string|null $seo_title
 * @property string|null $seo_description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Tour $tour
 */
class TourTranslation extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'tour_id',
        'locale',
        'title',
        'slug',
        'excerpt',
        'content',
        'seo_title',
        'seo_description',
    ];

    /**
     * Get the tour that owns this translation.
     */
    public function tour(): BelongsTo
    {
        return $this->belongsTo(Tour::class);
    }
}
