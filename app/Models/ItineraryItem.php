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
    ];

    protected $casts = [
        'meta' => 'array',
        'sort_order' => 'integer',
        'duration_minutes' => 'integer',
    ];

    // Relationships
    public function tour()
    {
        return $this->belongsTo(Tour::class);
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

    public function cities()
    {
        return $this->belongsToMany(City::class, 'city_itinerary_item')
                    ->withPivot('order')
                    ->withTimestamps()
                    ->orderBy('city_itinerary_item.order');
    }
}
