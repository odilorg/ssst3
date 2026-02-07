<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TourOperatorReminder extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'reminder_type',
        'scheduled_for',
        'sent_at',
        'email_sent',
        'telegram_sent',
        'cancelled_at',
        'notes',
    ];

    protected $casts = [
        'scheduled_for' => 'date',
        'sent_at' => 'datetime',
        'email_sent' => 'boolean',
        'telegram_sent' => 'boolean',
        'cancelled_at' => 'datetime',
    ];

    // Relationships
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->whereNull('sent_at')->whereNull('cancelled_at');
    }

    public function scopeForToday($query)
    {
        return $query->whereDate('scheduled_for', today());
    }

    public function scopeOverdue($query)
    {
        return $query->whereDate('scheduled_for', '<', today())
            ->whereNull('sent_at')
            ->whereNull('cancelled_at');
    }

    // Helper methods
    public function markAsSent(): void
    {
        $this->update([
            'sent_at' => now(),
            'email_sent' => true,
        ]);
    }

    public function markTelegramSent(): void
    {
        $this->update(['telegram_sent' => true]);
    }

    public function cancel(string $reason = null): void
    {
        $this->update([
            'cancelled_at' => now(),
            'notes' => $reason,
        ]);
    }

    public function isSent(): bool
    {
        return $this->sent_at !== null;
    }

    public function isCancelled(): bool
    {
        return $this->cancelled_at !== null;
    }
}
