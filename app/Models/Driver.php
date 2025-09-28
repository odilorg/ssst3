<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'license_number',
        'license_expiry_date',
        'license_image',
        'profile_image',
    ];

    // Relationships
    public function transports()
    {
        return $this->hasMany(Transport::class);
    }
}
