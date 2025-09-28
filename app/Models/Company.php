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
        'address_city',
        'phone',
        'email',
        'inn',
        'account_number',
        'bank_name',
        'bank_mfo',
        'director_name',
        'logo',
        'is_operator',
        'license_number',
    ];

    protected $casts = [
        'is_operator' => 'boolean',
        'inn' => 'integer',
        'account_number' => 'integer',
        'bank_mfo' => 'integer',
    ];

    // Relationships
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
