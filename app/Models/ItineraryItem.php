<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItineraryItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'tour_id',
        'parent_id',
        'type',
        'sort_order',
        'title',
        'description',
        'default_start_time',
        'duration_minutes',
        'meta',
        'day_number',
        'city_id',
        'meals',
        'accommodation',
        'transport',
    ];

    protected $casts = [
        'meta' => 'array',
        'sort_order' => 'integer',
        'duration_minutes' => 'integer',
        'day_number' => 'integer',
    ];

    // Relationships
    public function tour()
    {
        return $this->belongsTo(Tour::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('sort_order');
    }

    public function bookingItineraryItems()
    {
        return $this->hasMany(BookingItineraryItem::class, 'tour_itinerary_item_id');
    }
}
