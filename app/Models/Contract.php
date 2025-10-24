<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Contract extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_type',
        'supplier_id',
        'contract_number',
        'title',
        'start_date',
        'end_date',
        'terms',
        'pricing_structure',
        'status',
        'signed_by',
        'notes',
        'contract_file',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'pricing_structure' => 'array',
    ];

    // Relationships
    public function supplier()
    {
        return $this->morphTo();
    }

    public function contractServices(): HasMany
    {
        return $this->hasMany(ContractService::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
                    ->where('start_date', '<=', now())
                    ->where('end_date', '>=', now());
    }

    public function scopeExpired($query)
    {
        return $query->where('end_date', '<', now());
    }

    public function scopeForSupplier($query, $supplierType, $supplierId)
    {
        return $query->where('supplier_type', $supplierType)
                    ->where('supplier_id', $supplierId);
    }

    // Accessors & Mutators
    public function getIsActiveAttribute(): bool
    {
        return $this->status === 'active'
            && $this->start_date <= now()
            && $this->end_date >= now();
    }

    public function getDaysRemainingAttribute(): int
    {
        return max(0, $this->end_date->diffInDays(now()));
    }

    // Helper methods to check supplier type
    public function isCompanyContract(): bool
    {
        return $this->supplier_type === Company::class;
    }

    public function isGuideContract(): bool
    {
        return $this->supplier_type === Guide::class;
    }

    public function isDriverContract(): bool
    {
        return $this->supplier_type === Driver::class;
    }

    // Auto-generate contract number
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($contract) {
            if (empty($contract->contract_number)) {
                $contract->contract_number = static::generateContractNumber();
            }

            if (empty($contract->title)) {
                $contract->title = 'Annual Service Agreement';
            }
        });
    }

    public static function generateContractNumber(): string
    {
        $year = date('Y');
        $prefix = "CON-{$year}-";

        // Get the last contract number for this year
        $lastContract = static::where('contract_number', 'like', "{$prefix}%")
            ->orderBy('contract_number', 'desc')
            ->first();

        if ($lastContract) {
            // Extract the number part and increment
            $lastNumber = (int) substr($lastContract->contract_number, -3);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }
}
