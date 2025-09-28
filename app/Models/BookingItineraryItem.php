<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingItineraryItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'tour_itinerary_item_id',
        'date',
        'type',
        'sort_order',
        'title',
        'description',
        'planned_start_time',
        'planned_duration_minutes',
        'meta',
        'is_custom',
        'is_locked',
        'status',
    ];

    protected $casts = [
        'date' => 'date',
        'meta' => 'array',
        'sort_order' => 'integer',
        'planned_duration_minutes' => 'integer',
        'is_custom' => 'boolean',
        'is_locked' => 'boolean',
    ];

    // Relationships
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function tourItineraryItem()
    {
        return $this->belongsTo(ItineraryItem::class, 'tour_itinerary_item_id');
    }

    public function assignments()
    {
        return $this->hasMany(BookingItineraryItemAssignment::class);
    }
}
