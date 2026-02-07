<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentReminder extends Model
{
    protected $fillable = [
        'booking_id',
        'reminder_type',
        'scheduled_date',
        'sent_at',
        'email_sent',
        'sms_sent',
        'response',
    ];

    protected $casts = [
        'scheduled_date' => 'date',
        'sent_at' => 'datetime',
        'email_sent' => 'boolean',
        'sms_sent' => 'boolean',
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function getReminderTypeLabel(): string
    {
        return match($this->reminder_type) {
            'balance_45_days' => '45 Days Before Balance Due',
            'balance_35_days' => '35 Days Before Balance Due',
            'balance_30_days' => '30 Days Before Balance Due',
            'balance_overdue' => 'Overdue Balance Payment',
            default => $this->reminder_type,
        };
    }
}
