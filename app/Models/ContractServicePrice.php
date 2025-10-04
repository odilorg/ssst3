<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class ContractServicePrice extends Model
{
    use HasFactory;

    protected $fillable = [
        'contract_service_id',
        'effective_from',
        'effective_until',
        'price_data',
        'amendment_number',
        'notes',
    ];

    protected $casts = [
        'effective_from' => 'date',
        'effective_until' => 'date',
        'price_data' => 'array',
    ];

    // Relationships
    public function contractService(): BelongsTo
    {
        return $this->belongsTo(ContractService::class);
    }

    // Scopes
    public function scopeActiveOn($query, $date = null)
    {
        $date = $date ?? now();

        return $query->where('effective_from', '<=', $date)
            ->where(function ($q) use ($date) {
                $q->whereNull('effective_until')
                  ->orWhere('effective_until', '>=', $date);
            });
    }

    public function scopeCurrent($query)
    {
        return $query->activeOn(now());
    }

    // Helper methods for different service types
    public function getPriceForRoom($roomId): ?float
    {
        return $this->price_data['rooms'][$roomId] ?? null;
    }

    public function getPriceForMealType($mealTypeId): ?float
    {
        return $this->price_data['meal_types'][$mealTypeId] ?? null;
    }

    public function getDirectPrice(): ?float
    {
        return $this->price_data['direct_price'] ?? null;
    }

    // Check if this price version is currently active
    public function getIsActiveAttribute(): bool
    {
        $now = now();
        return $this->effective_from <= $now
            && ($this->effective_until === null || $this->effective_until >= $now);
    }

    // Check if this is an amendment
    public function getIsAmendmentAttribute(): bool
    {
        return !empty($this->amendment_number);
    }
}
