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
        'is_active',
        'start_date',
        'end_date',
        'specific_terms',
    ];

    protected $casts = [
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

    public function prices()
    {
        return $this->hasMany(ContractServicePrice::class);
    }

    public function currentPrice()
    {
        return $this->hasOne(ContractServicePrice::class)
            ->current()
            ->latest('effective_from');
    }

    public function priceHistory()
    {
        return $this->hasMany(ContractServicePrice::class)
            ->orderBy('effective_from', 'desc');
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

    // Helper methods for pricing (delegated to current price version)
    public function getPriceForRoom($roomId, $date = null)
    {
        $priceRecord = $this->getPriceVersion($date);
        return $priceRecord?->getPriceForRoom($roomId);
    }

    public function getPriceForMealType($mealTypeId, $date = null)
    {
        $priceRecord = $this->getPriceVersion($date);
        return $priceRecord?->getPriceForMealType($mealTypeId);
    }

    public function getDirectPrice($date = null)
    {
        $priceRecord = $this->getPriceVersion($date);
        return $priceRecord?->getDirectPrice();
    }

    // Get price version active on a specific date
    public function getPriceVersion($date = null)
    {
        $date = $date ?? now();

        return $this->prices()
            ->activeOn($date)
            ->orderBy('effective_from', 'desc')
            ->first();
    }
}
