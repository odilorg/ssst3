<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guide extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'is_marketing',
        'phone',
        'email',
        'address',
        'city_id',
        'image',
        'price_types',
    ];

    protected $casts = [
        'price_types' => 'array',
        'is_marketing' => 'boolean',
    ];

    // Relationships
    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function spokenLanguages()
    {
        return $this->belongsToMany(SpokenLanguage::class, 'guide_spoken_language');
    }

    public function contractServices()
    {
        return $this->morphMany(ContractService::class, 'serviceable');
    }
}
