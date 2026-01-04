<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * CityTranslation Model
 *
 * Stores translated content for cities in multiple languages.
 *
 * @property int $id
 * @property int $city_id
 * @property string $locale
 * @property string $name
 * @property string $slug
 * @property string|null $tagline
 * @property string|null $short_description
 * @property string|null $description
 * @property string|null $seo_title
 * @property string|null $seo_description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read City $city
 */
class CityTranslation extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'city_id',
        'locale',
        'name',
        'slug',
        'tagline',
        'short_description',
        'description',
        'seo_title',
        'seo_description',
    ];

    /**
     * Get the city that owns this translation.
     */
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }
}
