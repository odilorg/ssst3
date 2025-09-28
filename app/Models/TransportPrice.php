<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransportPrice extends Model
{
    use HasFactory;

    protected $fillable = [
        'transport_type_id',
        'price_type',
        'cost',
    ];

    protected $casts = [
        'cost' => 'decimal:2',
    ];

    // Relationships
    public function transportType()
    {
        return $this->belongsTo(TransportType::class);
    }
}
