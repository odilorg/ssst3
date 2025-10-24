<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lead extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'reference',
        'company_name',
        'website',
        'email',
        'phone',
        'description',
        'contact_name',
        'contact_position',
        'contact_email',
        'contact_phone',
        'country',
        'city',
        'source',
        'source_url',
        'source_notes',
        'status',
        'tour_types',
        'target_markets',
        'business_type',
        'annual_volume',
        'certifications',
        'has_uzbekistan_partner',
        'uzbekistan_partner_name',
        'uzbekistan_partnership_status',
        'uzbekistan_partnership_notes',
        'working_status',
        'assigned_to',
        'last_contacted_at',
        'next_followup_at',
        'converted_to_customer_at',
        'customer_id',
        'quality_score',
        'notes',
    ];

    protected $casts = [
        'tour_types' => 'array',
        'target_markets' => 'array',
        'certifications' => 'array',
        'has_uzbekistan_partner' => 'boolean',
        'last_contacted_at' => 'datetime',
        'next_followup_at' => 'datetime',
        'converted_to_customer_at' => 'date',
        'annual_volume' => 'integer',
        'quality_score' => 'integer',
    ];

    protected static function booted()
    {
        static::creating(function ($lead) {
            // Generate reference if not set
            if (empty($lead->reference)) {
                $lead->reference = $lead->generateReference();
            }
        });
    }

    // Relationships
    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function emailLogs()
    {
        return $this->hasMany(EmailLog::class);
    }

    // Query Scopes
    public function scopeNew($query)
    {
        return $query->where('status', 'new');
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', [
            'new',
            'researching',
            'qualified',
            'contacted',
            'responded',
            'negotiating',
        ]);
    }

    public function scopeOverdueFollowup($query)
    {
        return $query->whereNotNull('next_followup_at')
            ->where('next_followup_at', '<', now());
    }

    public function scopeConverted($query)
    {
        return $query->where('status', 'partner')
            ->whereNotNull('customer_id');
    }

    public function scopeBySource($query, string $source)
    {
        return $query->where('source', $source);
    }

    public function scopeAssignedTo($query, int $userId)
    {
        return $query->where('assigned_to', $userId);
    }

    public function scopeWithUzbekistanPartner($query)
    {
        return $query->where('has_uzbekistan_partner', true);
    }

    public function scopeActivelyWorking($query)
    {
        return $query->where('working_status', 'active');
    }

    // Helper Methods
    public function isNew(): bool
    {
        return $this->status === 'new';
    }

    public function isContacted(): bool
    {
        return in_array($this->status, ['contacted', 'responded', 'negotiating']);
    }

    public function isConverted(): bool
    {
        return $this->status === 'partner' && $this->customer_id;
    }

    public function needsFollowup(): bool
    {
        return $this->next_followup_at && $this->next_followup_at->isPast();
    }

    // Business Logic Methods
    public function generateReference(): string
    {
        $year = Carbon::now()->year;
        $prefix = "LD-{$year}-";

        // Find the last lead with the same year prefix
        $lastLead = static::where('reference', 'like', $prefix . '%')
            ->orderBy('reference', 'desc')
            ->first();

        if ($lastLead) {
            // Extract the number from the reference and increment
            $lastNumber = (int) substr($lastLead->reference, strlen($prefix));
            $number = $lastNumber + 1;
        } else {
            $number = 1;
        }

        return $prefix . str_pad($number, 4, '0', STR_PAD_LEFT);
    }

    public function markAsContacted(): void
    {
        $this->update([
            'status' => 'contacted',
            'last_contacted_at' => now(),
        ]);
    }

    public function markAsResponded(): void
    {
        $this->update([
            'status' => 'responded',
        ]);
    }

    public function convertToCustomer(Customer $customer): void
    {
        $this->update([
            'status' => 'partner',
            'customer_id' => $customer->id,
            'converted_to_customer_at' => now(),
        ]);
    }
}
