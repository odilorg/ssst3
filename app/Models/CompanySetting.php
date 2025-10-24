<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanySetting extends Model
{
    protected $fillable = [
        'company_name',
        'legal_name',
        'tax_id',
        'registration_number',
        'founded_date',
        'email',
        'phone',
        'mobile',
        'fax',
        'website',
        'facebook',
        'instagram',
        'linkedin',
        'twitter',
        'registered_address',
        'office_address',
        'billing_address',
        'city',
        'country',
        'postal_code',
        'bank_accounts',
        'license_number',
        'license_expiry',
        'insurance_details',
        'terms_and_conditions',
        'logo_path',
        'primary_color',
        'secondary_color',
        'email_signature',
        'currency',
        'timezone',
        'date_format',
    ];

    protected $casts = [
        'bank_accounts' => 'array',
        'founded_date' => 'date',
        'license_expiry' => 'date',
    ];

    /**
     * Get the singleton instance (only one record should exist)
     */
    public static function current(): ?self
    {
        return static::first();
    }

    /**
     * Get or create the company settings
     */
    public static function getOrCreate(): self
    {
        return static::firstOrCreate(
            ['id' => 1],
            [
                'company_name' => config('app.name', 'Company Name'),
                'email' => config('mail.from.address'),
                'currency' => 'USD',
                'timezone' => 'Asia/Tashkent',
            ]
        );
    }

    /**
     * Get formatted bank account for display
     */
    public function getPrimaryBankAccount(): ?array
    {
        if (empty($this->bank_accounts)) {
            return null;
        }
        
        return is_array($this->bank_accounts) && count($this->bank_accounts) > 0 
            ? $this->bank_accounts[0] 
            : null;
    }

    /**
     * Get full address formatted
     */
    public function getFullAddress(): string
    {
        $parts = array_filter([
            $this->office_address ?: $this->registered_address,
            $this->city,
            $this->postal_code,
            $this->country,
        ]);

        return implode(', ', $parts);
    }

    /**
     * Get logo URL
     */
    public function getLogoUrl(): ?string
    {
        return $this->logo_path ? asset('storage/' . $this->logo_path) : null;
    }
}
