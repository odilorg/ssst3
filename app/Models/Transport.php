<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transport extends Model
{
    use HasFactory;

    protected $fillable = [
        'plate_number',
        'vin',
        'model',
        'number_of_seat',
        'category',
        'transport_type_id',
        'departure_time',
        'arrival_time',
        'running_days',
        'driver_id',
        'city_id',
        'images',
        'fuel_type',
        'oil_change_interval_months',
        'oil_change_interval_km',
        'fuel_consumption',
        'fuel_remaining_liter',
        'company_id',
    ];

    protected $casts = [
        'images' => 'array',
        'running_days' => 'array',
        'number_of_seat' => 'integer',
        'oil_change_interval_months' => 'integer',
        'oil_change_interval_km' => 'integer',
        'fuel_consumption' => 'decimal:2',
        'fuel_remaining_liter' => 'decimal:2',
    ];

    // Relationships
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function transportType()
    {
        return $this->belongsTo(TransportType::class);
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function oilChanges()
    {
        return $this->hasMany(OilChange::class);
    }

    public function latestOilChange()
    {
        return $this->hasOne(OilChange::class)->latest();
    }

    public function amenities()
    {
        return $this->belongsToMany(Amenity::class, 'transport_amenity');
    }

    public function contractServices()
    {
        return $this->morphMany(ContractService::class, 'serviceable');
    }

    public function transportInstancePrices()
    {
        return $this->hasMany(TransportInstancePrice::class);
    }

    /**
     * Generate display label for transport in estimates
     * Format: "{TransportType} {PlateNumber} - {PriceType}"
     * Example: "Mercedes Sprinter BUS-001 - per_day"
     * 
     * @throws \Exception if transport or related data is missing
     */
    public static function getEstimateLabel(BookingItineraryItemAssignment $assignment): string
    {
        $transport = $assignment->assignable;
        
        if (!$transport) {
            throw new \Exception("Transport assignment #{$assignment->id} references non-existent transport ID {$assignment->assignable_id}");
        }
        
        // Load transportType if not already loaded
        if (!$transport->relationLoaded('transportType')) {
            $transport->load('transportType');
        }
        
        $transportType = $transport->transportType;
        if (!$transportType) {
            throw new \Exception("Transport #{$transport->id} has invalid transport_type_id {$transport->transport_type_id}");
        }
        
        // Try transport instance price first, then fall back to transport type price
        $transportPrice = $assignment->transportInstancePrice;
        if (!$transportPrice) {
            $transportPrice = $assignment->transportPrice;
        }
        
        if (!$transportPrice) {
            throw new \Exception("Transport assignment #{$assignment->id} has no valid pricing (neither transport_instance_price_id nor transport_price_type_id)");
        }
        
        $typeName = $transportType->type;
        $plate = $transport->plate_number ?? 'UNKNOWN';
        $priceType = $transportPrice->price_type;
        
        return trim("{$typeName} {$plate} - {$priceType}");
    }
}
