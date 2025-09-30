<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Contract extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_company_id',
        'contract_number',
        'title',
        'start_date',
        'end_date',
        'terms',
        'pricing_structure',
        'status',
        'signed_by',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'pricing_structure' => 'array',
    ];

    // Relationships
    public function supplierCompany(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'supplier_company_id');
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

    public function scopeForCompany($query, $companyId)
    {
        return $query->where('supplier_company_id', $companyId);
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
}
