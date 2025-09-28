<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CityDistance extends Model
{
    use HasFactory;

    protected $fillable = [
        'city_from_to',
        'distance_km',
    ];

    protected $casts = [
        'distance_km' => 'decimal:2',
    ];
}
