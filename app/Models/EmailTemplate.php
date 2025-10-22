<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class EmailTemplate extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'subject',
        'body',
        'type',
        'is_active',
        'times_used',
        'last_used_at',
        'description',
        'available_variables',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_used_at' => 'datetime',
        'available_variables' => 'array',
        'times_used' => 'integer',
    ];

    protected static function booted()
    {
        static::creating(function ($template) {
            // Auto-generate slug if not provided
            if (empty($template->slug)) {
                $template->slug = Str::slug($template->name);
            }
        });

        static::updating(function ($template) {
            // Update slug if name changed
            if ($template->isDirty('name') && empty($template->slug)) {
                $template->slug = Str::slug($template->name);
            }
        });
    }

    // Query Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    // Helper Methods
    public function render(array $variables): array
    {
        $subject = $this->subject;
        $body = $this->body;

        // Replace all variables in subject and body
        foreach ($variables as $key => $value) {
            $placeholder = "{{" . $key . "}}";
            $subject = str_replace($placeholder, $value, $subject);
            $body = str_replace($placeholder, $value, $body);
        }

        return [
            'subject' => $subject,
            'body' => $body,
        ];
    }

    public function incrementUsage(): void
    {
        $this->increment('times_used');
        $this->update(['last_used_at' => now()]);
    }

    // Get all available template variables
    public static function getAvailableVariables(): array
    {
        return [
            'company_name' => 'Company name',
            'contact_name' => 'Contact person name',
            'country' => 'Company country',
            'website' => 'Company website',
            'sender_name' => 'Your name',
            'sender_email' => 'Your email',
            'sender_company' => 'Your company name',
        ];
    }
}
