<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Workshop extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        // Basic Info
        'title',
        'slug',
        'subtitle',
        'short_description',
        'long_description',
        
        // Master/Artisan Info
        'master_name',
        'master_title',
        'master_bio',
        'master_image',
        
        // Craft Info
        'craft_type',
        'craft_tradition',
        'craft_highlights',
        
        // Location
        'city_id',
        'address',
        'location_description',
        'latitude',
        'longitude',
        
        // Duration & Schedule
        'duration_text',
        'duration_minutes',
        'operating_hours',
        'advance_booking_days',
        
        // Capacity
        'min_guests',
        'max_guests',
        'group_size_text',
        
        // Pricing
        'price_per_person',
        'private_session_price',
        'currency',
        'pricing_notes',
        
        // Content (JSON)
        'what_you_will_do',
        'included_items',
        'excluded_items',
        'who_is_it_for',
        'practical_info',
        'faqs',
        'languages',
        
        // Related
        'related_tour_ids',
        
        // Images
        'hero_image',
        'gallery_images',
        
        // SEO
        'seo_title',
        'seo_description',
        'og_image',
        
        // Status
        'is_active',
        'is_featured',
        'sort_order',
        
        // Ratings
        'rating',
        'review_count',
        
        // Accommodation
        'has_guesthouse',
        'guesthouse_description',
    ];

    protected $casts = [
        'craft_highlights' => 'array',
        'what_you_will_do' => 'array',
        'included_items' => 'array',
        'excluded_items' => 'array',
        'who_is_it_for' => 'array',
        'practical_info' => 'array',
        'faqs' => 'array',
        'languages' => 'array',
        'related_tour_ids' => 'array',
        'gallery_images' => 'array',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'has_guesthouse' => 'boolean',
        'price_per_person' => 'decimal:2',
        'private_session_price' => 'decimal:2',
        'rating' => 'decimal:1',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    /**
     * Boot method for model events.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($workshop) {
            if (empty($workshop->slug)) {
                $workshop->slug = Str::slug($workshop->title);
            }
        });
    }

    // ========================
    // RELATIONSHIPS
    // ========================

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function reviews()
    {
        return $this->morphMany(Review::class, 'reviewable');
    }

    public function relatedTours()
    {
        if (empty($this->related_tour_ids)) {
            return collect();
        }
        $ids = is_array($this->related_tour_ids) ? $this->related_tour_ids : json_decode($this->related_tour_ids, true);
        return Tour::whereIn("id", $ids ?: [])->get();
    }

    // ========================
    // ACCESSORS
    // ========================

    public function getFormattedPriceAttribute(): string
    {
        if (!$this->price_per_person) {
            return 'Contact for pricing';
        }
        return '$' . number_format($this->price_per_person, 0) . '/person';
    }

    public function getLocationFullAttribute(): string
    {
        $parts = array_filter([
            $this->address,
            $this->city?->name,
        ]);
        return implode(', ', $parts);
    }

    public function getHeroImageUrlAttribute(): ?string
    {
        if (!$this->hero_image) {
            return null;
        }
        if (Str::startsWith($this->hero_image, ['http://', 'https://'])) {
            return $this->hero_image;
        }
        return asset('storage/' . $this->hero_image);
    }

    public function getMasterImageUrlAttribute(): ?string
    {
        if (!$this->master_image) {
            return null;
        }
        if (Str::startsWith($this->master_image, ['http://', 'https://'])) {
            return $this->master_image;
        }
        return asset('storage/' . $this->master_image);
    }

    // ========================
    // SCOPES
    // ========================

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeByCraftType($query, string $type)
    {
        return $query->where('craft_type', $type);
    }

    public function scopeByCity($query, int $cityId)
    {
        return $query->where('city_id', $cityId);
    }

    // ========================
    // HELPERS
    // ========================

    public function getGalleryUrls(): array
    {
        if (empty($this->gallery_images)) {
            return [];
        }

        return array_map(function ($image) {
            if (Str::startsWith($image, ['http://', 'https://'])) {
                return $image;
            }
            return asset('storage/' . $image);
        }, $this->gallery_images);
    }

    public function updateRating(): void
    {
        $reviews = $this->reviews();
        $this->rating = $reviews->avg('rating');
        $this->review_count = $reviews->count();
        $this->saveQuietly();
    }

    // ========================
    // JSON FIELD ACCESSORS
    // (ensures arrays are always returned)
    // ========================

    protected function ensureArray($value): array
    {
        if (is_array($value)) {
            return $value;
        }
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            return is_array($decoded) ? $decoded : [];
        }
        return [];
    }

    public function getLanguagesAttribute($value): array
    {
        return $this->ensureArray($value);
    }

    public function getCraftHighlightsAttribute($value): array
    {
        return $this->ensureArray($value);
    }

    public function getWhatYouWillDoAttribute($value): array
    {
        return $this->ensureArray($value);
    }

    public function getIncludedItemsAttribute($value): array
    {
        return $this->ensureArray($value);
    }

    public function getExcludedItemsAttribute($value): array
    {
        return $this->ensureArray($value);
    }

    public function getWhoIsItForAttribute($value): array
    {
        return $this->ensureArray($value);
    }

    public function getPracticalInfoAttribute($value): array
    {
        return $this->ensureArray($value);
    }

    public function getFaqsAttribute($value): array
    {
        return $this->ensureArray($value);
    }

    public function getRelatedTourIdsAttribute($value): array
    {
        return $this->ensureArray($value);
    }

    public function getGalleryImagesAttribute($value): array
    {
        return $this->ensureArray($value);
    }
}
