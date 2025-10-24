<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address_street',
        'city_id',
        'phone',
        'email',
        'accountant_name',
        'inn',
        'account_number',
        'bank_name',
        'bank_mfo',
        'has_treasury_account',
        'treasury_account_number',
        'treasury_stir',
        'director_name',
        'logo',
        'is_operator',
        'license_number',
    ];

    protected $casts = [
        'is_operator' => 'boolean',
        'has_treasury_account' => 'boolean',
        'inn' => 'integer',
        'bank_mfo' => 'integer',
    ];

    // Relationships
    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function contracts()
    {
        return $this->morphMany(Contract::class, 'supplier');
    }

    // public function hotels()
    // {
    //     return $this->hasMany(Hotel::class);
    // }

    // public function restaurants()
    // {
    //     return $this->hasMany(Restaurant::class);
    // }

    // public function transports()
    // {
    //     return $this->hasMany(Transport::class);
    // }

    // public function monuments()
    // {
    //     return $this->hasMany(Monument::class);
    // }
}
