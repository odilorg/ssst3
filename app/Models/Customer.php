<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
    ];

    // Relationships - can be extended for future features
    // public function estimates()
    // {
    //     return $this->hasMany(Estimate::class);
    // }

    // public function bookingRequests()
    // {
    //     return $this->hasMany(BookingRequest::class);
    // }
}
