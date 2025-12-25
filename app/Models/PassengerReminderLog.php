<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PassengerReminderLog extends Model
{
    protected $fillable = [
        'booking_id',
        'reminder_type',
        'sent_at',
        'opened_at',
        'clicked_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'opened_at' => 'datetime',
        'clicked_at' => 'datetime',
    ];

    /**
     * Get the booking that owns the reminder log
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Get human-readable reminder type
     */
    public function getReminderTypeLabel(): string
    {
        return match($this->reminder_type) {
            '45_days' => '45 Days Before',
            '30_days' => '30 Days Before',
            '14_days' => '14 Days Before',
            '7_days' => '7 Days Before',
            'final' => 'Final Reminder',
            default => $this->reminder_type,
        };
    }
}
