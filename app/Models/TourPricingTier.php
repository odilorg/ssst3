<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TourPricingTier extends Model
{
    use HasFactory;

    protected $fillable = [
        'tour_id',
        'min_guests',
        'max_guests',
        'price_total',
        'price_per_person',
        'label',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'min_guests' => 'integer',
        'max_guests' => 'integer',
        'price_total' => 'decimal:2',
        'price_per_person' => 'decimal:2',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Boot method - auto-calculate price_per_person on save
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($tier) {
            // Auto-calculate price_per_person based on average guests in tier
            if ($tier->price_total && $tier->min_guests) {
                $avgGuests = ($tier->min_guests + $tier->max_guests) / 2;
                $tier->price_per_person = $tier->price_total / $avgGuests;
            }
        });
    }

    // ==========================================
    // RELATIONSHIPS
    // ==========================================

    /**
     * Get the tour this pricing tier belongs to
     */
    public function tour(): BelongsTo
    {
        return $this->belongsTo(Tour::class);
    }

    // ==========================================
    // SCOPES
    // ==========================================

    /**
     * Scope to filter active tiers only
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to find tier for specific guest count
     */
    public function scopeForGuestCount($query, int $guestCount)
    {
        return $query->where('min_guests', '<=', $guestCount)
                      ->where('max_guests', '>=', $guestCount)
                      ->active();
    }

    /**
     * Scope to order by guest count
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('min_guests');
    }

    // ==========================================
    // ACCESSORS
    // ==========================================

    /**
     * Get formatted price total in USD
     */
    public function getFormattedPriceTotalAttribute(): string
    {
        return '$' . number_format($this->price_total, 0) . ' USD';
    }

    /**
     * Get formatted price per person in USD
     */
    public function getFormattedPricePerPersonAttribute(): string
    {
        return '$' . number_format($this->price_per_person, 0) . ' USD';
    }

    /**
     * Get price total in UZS (converted from USD)
     */
    public function getPriceTotalUzsAttribute(): float
    {
        $exchangeRate = $this->getExchangeRate();
        return round($this->price_total * $exchangeRate);
    }

    /**
     * Get formatted price total in UZS
     */
    public function getFormattedPriceTotalUzsAttribute(): string
    {
        return number_format($this->price_total_uzs, 0, '.', ' ') . ' UZS';
    }

    /**
     * Get price per person in UZS (converted from USD)
     */
    public function getPricePerPersonUzsAttribute(): float
    {
        $exchangeRate = $this->getExchangeRate();
        return round($this->price_per_person * $exchangeRate);
    }

    /**
     * Get formatted price per person in UZS
     */
    public function getFormattedPricePerPersonUzsAttribute(): string
    {
        return number_format($this->price_per_person_uzs, 0, '.', ' ') . ' UZS';
    }

    /**
     * Get current USD to UZS exchange rate from CBU.uz
     */
    protected function getExchangeRate(): float
    {
        try {
            $date = now()->format('Y-m-d');
            $response = \Illuminate\Support\Facades\Http::timeout(5)
                ->get("https://cbu.uz/ru/arkhiv-kursov-valyut/json/USD/{$date}/");

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data[0]['Rate'])) {
                    return (float) $data[0]['Rate'];
                }
            }
        } catch (\Exception $e) {
            \Log::warning('Failed to fetch exchange rate from CBU.uz: ' . $e->getMessage());
        }

        // Fallback rate
        return 12650.0;
    }

    /**
     * Get guest range display (e.g., "1 person", "2-4 people")
     */
    public function getGuestRangeDisplayAttribute(): string
    {
        if ($this->min_guests === $this->max_guests) {
            return $this->min_guests === 1 
                ? '1 person' 
                : $this->min_guests . ' people';
        }
        
        return $this->min_guests . '-' . $this->max_guests . ' people';
    }

    /**
     * Get tier label or auto-generate one
     */
    public function getDisplayLabelAttribute(): string
    {
        if ($this->label) {
            return $this->label;
        }

        // Auto-generate label based on guest count
        if ($this->min_guests === 1 && $this->max_guests === 1) {
            return 'Solo Traveler';
        }
        if ($this->min_guests === 2 && $this->max_guests === 2) {
            return 'Couple';
        }
        if ($this->max_guests <= 4) {
            return 'Small Group';
        }
        if ($this->max_guests <= 10) {
            return 'Medium Group';
        }
        return 'Large Group';
    }
}
