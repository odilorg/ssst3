<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BookingItineraryItemAssignment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'booking_itinerary_item_id',
        'assignable_type',
        'assignable_id',
        'room_id',
        'meal_type_id',
        'transport_price_type_id',
        'transport_instance_price_id',
        'guide_service_cost',
        'role',
        'quantity',
        'cost',
        'currency',
        'status',
        'start_time',
        'end_time',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'cost' => 'decimal:2',
    ];

    // Relationships
    public function bookingItineraryItem()
    {
        return $this->belongsTo(BookingItineraryItem::class);
    }

    public function assignable()
    {
        return $this->morphTo();
    }

    public function transportPrice()
    {
        return $this->belongsTo(TransportPrice::class, 'transport_price_type_id');
    }

    public function transportInstancePrice()
    {
        return $this->belongsTo(TransportInstancePrice::class, 'transport_instance_price_id');
    }
}
