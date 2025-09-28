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
        'notes',
    ];

    protected $casts = [
        'oil_change_date' => 'date',
    ];

    // Relationships
    public function transport()
    {
        return $this->belongsTo(Transport::class);
    }
}
