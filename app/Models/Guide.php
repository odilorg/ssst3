<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guide extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'daily_rate',
        'language',
        'is_marketing',
        'phone',
        'email',
        'address',
        'city',
        'image',
        'price_types',
    ];

    protected $casts = [
        'price_types' => 'array',
        'is_marketing' => 'boolean',
        'daily_rate' => 'decimal:2',
    ];

    // Relationships
    public function spokenLanguages()
    {
        return $this->belongsToMany(SpokenLanguage::class, 'guide_spoken_language');
    }

    public function contractServices()
    {
        return $this->morphMany(ContractService::class, 'serviceable');
    }
}
