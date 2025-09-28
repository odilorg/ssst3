<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'images',
    ];

    protected $casts = [
        'images' => 'array',
    ];

    // Relationships
    public function hotels()
    {
        return $this->hasMany(Hotel::class);
    }

    public function restaurants()
    {
        return $this->hasMany(Restaurant::class);
    }

    public function monuments()
    {
        return $this->hasMany(Monument::class);
    }

    public function transports()
    {
        return $this->hasMany(Transport::class);
    }
}
