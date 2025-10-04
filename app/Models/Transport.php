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
}
