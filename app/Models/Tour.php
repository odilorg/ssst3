<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tour extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'duration_days',
        'short_description',
        'long_description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'duration_days' => 'integer',
    ];

    // Relationships
    public function itineraryItems()
    {
        return $this->hasMany(ItineraryItem::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function topLevelItems()
    {
        return $this->itineraryItems()->whereNull('parent_id')->orderBy('sort_order');
    }
}
