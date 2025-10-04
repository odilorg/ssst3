<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Restaurant extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'phone',
        'website',
        'email',
        'city_id',
        'menu_images',
        'company_id',
    ];

    protected $casts = [
        'menu_images' => 'array',
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

    public function mealTypes()
    {
        return $this->hasMany(MealType::class);
    }

    public function contractServices()
    {
        return $this->morphMany(ContractService::class, 'serviceable');
    }
}
