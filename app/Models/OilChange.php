<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OilChange extends Model
{
    use HasFactory;

    protected $fillable = [
        'transport_id',
        'oil_change_date',
        'mileage_at_change',
        'cost',
        'oil_type',
        'service_center',
        'notes',
        'other_services',
        'next_change_date',
        'next_change_mileage',
    ];

    protected $casts = [
        'oil_change_date' => 'date',
        'next_change_date' => 'date',
        'cost' => 'decimal:2',
        'mileage_at_change' => 'integer',
        'next_change_mileage' => 'integer',
        'other_services' => 'array',
    ];

    // Relationships
    public function transport()
    {
        return $this->belongsTo(Transport::class);
    }
}
