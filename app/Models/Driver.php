<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'first_name',
        'last_name',
        'patronymic',
        'email',
        'phone',
        'address',
        'city_id',
        'license_number',
        'license_categories',
        'license_expiry_date',
        'license_image',
        'profile_image',
    ];

    protected $casts = [
        'license_categories' => 'array',
        'license_expiry_date' => 'date',
    ];

    // Accessors
    public function getNameAttribute()
    {
        if ($this->first_name || $this->last_name) {
            $nameParts = array_filter([
                $this->first_name,
                $this->patronymic,
                $this->last_name,
            ]);
            return implode(' ', $nameParts);
        }
        return $this->attributes['name'] ?? '';
    }

    // Relationships
    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function transports()
    {
        return $this->hasMany(Transport::class);
    }

    public function contracts()
    {
        return $this->morphMany(Contract::class, 'supplier');
    }
}
