<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hotel extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'category',
        'type',
        'city_id',
        'description',
        'phone',
        'email',
        'images',
        'company_id',
    ];

    protected $casts = [
        'images' => 'array',
    ];

    // Relationships
    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function rooms()
    {
        return $this->hasMany(Room::class);
    }

    public function hotelRooms()
    {
        return $this->hasMany(Room::class);
    }

    public function contractServices()
    {
        return $this->morphMany(ContractService::class, 'serviceable');
    }
}
