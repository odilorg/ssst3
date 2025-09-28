<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MealType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'restaurant_id',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    // Relationships
    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }
}
