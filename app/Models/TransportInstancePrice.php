<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransportInstancePrice extends Model
{
    use HasFactory;

    protected $fillable = [
        'transport_id',
        'price_type',
        'cost',
        'currency',
    ];

    protected $casts = [
        'cost' => 'decimal:2',
    ];

    // Relationships
    public function transport()
    {
        return $this->belongsTo(Transport::class);
    }

    public function assignments()
    {
        return $this->hasMany(BookingItineraryItemAssignment::class, 'transport_instance_price_id');
    }

    // Helper methods
    public function getFormattedPriceAttribute()
    {
        return number_format($this->cost, 2) . ' ' . $this->currency;
    }

    public function getPriceTypeLabelAttribute()
    {
        return match($this->price_type) {
            'per_day' => 'Per Day',
            'per_pickup_dropoff' => 'Per Pickup Dropoff',
            'po_gorodu' => 'Po Gorodu',
            'vip' => 'VIP',
            'economy' => 'Economy',
            'business' => 'Business',
            'per_seat' => 'Per Seat',
            'per_km' => 'Per KM',
            'per_hour' => 'Per Hour',
            default => $this->price_type,
        };
    }
}
