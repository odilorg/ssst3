<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransportType extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'category',
        'running_days',
    ];

    protected $casts = [
        'running_days' => 'array',
    ];

    // Relationships
    public function transports()
    {
        return $this->hasMany(Transport::class);
    }

    public function transportPrices()
    {
        return $this->hasMany(TransportPrice::class);
    }
}
