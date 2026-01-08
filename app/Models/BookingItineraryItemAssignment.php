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

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function mealType()
    {
        return $this->belongsTo(MealType::class);
    }

    /**
     * Get the effective cost for this assignment.
     * If cost is manually set (override), use it.
     * Otherwise, derive from the service type.
     */
    public function getEffectiveCost(): ?float
    {
        // If user has set a manual override cost, use it
        if ($this->cost !== null && (float) $this->cost > 0) {
            return (float) $this->cost;
        }

        // Otherwise, derive from service type
        return $this->getDerivedCost();
    }

    /**
     * Get the derived cost from the service type (without override).
     */
    public function getDerivedCost(): ?float
    {
        switch ($this->assignable_type) {
            case Guide::class:
                return $this->getGuideCost();

            case Restaurant::class:
                return $this->getRestaurantCost();

            case Hotel::class:
                return $this->getHotelCost();

            case Transport::class:
                return $this->getTransportCost();

            case Monument::class:
                return $this->getMonumentCost();

            default:
                return null;
        }
    }

    /**
     * Get guide cost from selected price type.
     */
    protected function getGuideCost(): ?float
    {
        if ($this->guide_service_cost === null) {
            return null;
        }

        $guide = $this->assignable;
        if (!$guide || !$guide->price_types) {
            return null;
        }

        $priceTypes = is_array($guide->price_types) 
            ? $guide->price_types 
            : json_decode($guide->price_types, true);

        $index = (int) $this->guide_service_cost;
        if (isset($priceTypes[$index]['price'])) {
            return (float) $priceTypes[$index]['price'];
        }

        return null;
    }

    /**
     * Get restaurant cost from meal type.
     */
    protected function getRestaurantCost(): ?float
    {
        if (!$this->meal_type_id) {
            return null;
        }

        $mealType = $this->mealType;
        if (!$mealType) {
            return null;
        }

        $price = (float) $mealType->price;
        $quantity = $this->quantity ?? 1;

        return $price * $quantity;
    }

    /**
     * Get hotel cost from room.
     */
    protected function getHotelCost(): ?float
    {
        if (!$this->room_id) {
            return null;
        }

        $room = $this->room;
        if (!$room) {
            return null;
        }

        $price = (float) $room->cost_per_night;
        $quantity = $this->quantity ?? 1;

        return $price * $quantity;
    }

    /**
     * Get transport cost from instance price.
     */
    protected function getTransportCost(): ?float
    {
        if (!$this->transport_instance_price_id) {
            return null;
        }

        $instancePrice = $this->transportInstancePrice;
        if (!$instancePrice) {
            return null;
        }

        return (float) $instancePrice->cost;
    }

    /**
     * Get monument cost (ticket price).
     */
    protected function getMonumentCost(): ?float
    {
        $monument = $this->assignable;
        if (!$monument) {
            return null;
        }

        $price = (float) ($monument->ticket_price ?? 0);
        $quantity = $this->quantity ?? 1;

        return $price * $quantity;
    }

    /**
     * Check if this assignment has a manual cost override.
     */
    public function hasManualCost(): bool
    {
        return $this->cost !== null && (float) $this->cost > 0;
    }
}
