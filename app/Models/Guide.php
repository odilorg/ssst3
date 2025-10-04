<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guide extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'first_name',
        'last_name',
        'patronymic',
        'is_marketing',
        'phone',
        'email',
        'address',
        'city_id',
        'image',
        'price_types',
        'certificate_number',
        'certificate_issue_date',
        'certificate_category',
    ];

    protected $casts = [
        'price_types' => 'array',
        'is_marketing' => 'boolean',
        'certificate_issue_date' => 'date',
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

    public function spokenLanguages()
    {
        return $this->belongsToMany(SpokenLanguage::class, 'guide_spoken_language');
    }

    public function contractServices()
    {
        return $this->morphMany(ContractService::class, 'serviceable');
    }

    public function contracts()
    {
        return $this->morphMany(Contract::class, 'supplier');
    }

    public function permittedCities()
    {
        return $this->belongsToMany(City::class, 'guide_city');
    }
}
