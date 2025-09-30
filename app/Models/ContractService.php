<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ContractService extends Model
{
    use HasFactory;

    protected $fillable = [
        'contract_id',
        'serviceable_type',
        'serviceable_id',
        'pricing_structure',
        'is_active',
        'start_date',
        'end_date',
        'specific_terms',
    ];

    protected $casts = [
        'pricing_structure' => 'array',
        'is_active' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    // Relationships
    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }

    public function serviceable(): MorphTo
    {
        return $this->morphTo();
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                    ->where(function ($q) {
                        $q->whereNull('start_date')
                          ->orWhere('start_date', '<=', now());
                    })
                    ->where(function ($q) {
                        $q->whereNull('end_date')
                          ->orWhere('end_date', '>=', now());
                    });
    }

    public function scopeForService($query, $serviceableType, $serviceableId)
    {
        return $query->where('serviceable_type', $serviceableType)
                    ->where('serviceable_id', $serviceableId);
    }

    // Helper methods for pricing
    public function getPriceForRoom($roomId)
    {
        return $this->pricing_structure['rooms'][$roomId] ?? null;
    }

    public function getPriceForMealType($mealTypeId)
    {
        return $this->pricing_structure['meal_types'][$mealTypeId] ?? null;
    }

    public function getPriceForTransportType($transportTypeId)
    {
        return $this->pricing_structure['transport_types'][$transportTypeId] ?? null;
    }

    public function getDirectPrice()
    {
        return $this->pricing_structure['direct_price'] ?? null;
    }

    public function getDiscountRate()
    {
        return $this->pricing_structure['discount_rate'] ?? null;
    }
}
