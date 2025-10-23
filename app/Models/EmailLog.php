<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmailLog extends Model
{
    protected $fillable = [
        'lead_id',
        'email_template_id',
        'sent_by',
        'recipient_email',
        'recipient_name',
        'subject',
        'body',
        'status',
        'sent_at',
        'error_message',
        'message_id',
        'headers',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'headers' => 'array',
    ];

    /**
     * Get the lead that this email was sent to.
     */
    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    /**
     * Get the template used for this email.
     */
    public function emailTemplate(): BelongsTo
    {
        return $this->belongsTo(EmailTemplate::class);
    }

    /**
     * Get the user who sent this email.
     */
    public function sentBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sent_by');
    }

    /**
     * Scope to get only sent emails.
     */
    public function scopeSent($query)
    {
        return $query->where('status', 'sent');
    }

    /**
     * Scope to get only failed emails.
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Scope to get only pending emails.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope to get only bounced emails.
     */
    public function scopeBounced($query)
    {
        return $query->where('status', 'bounced');
    }

    /**
     * Scope to get only delivered emails.
     */
    public function scopeDelivered($query)
    {
        return $query->where('status', 'delivered');
    }

    /**
     * Mark the email as sent.
     */
    public function markAsSent(?string $messageId = null): void
    {
        $this->update([
            'status' => 'sent',
            'sent_at' => now(),
            'message_id' => $messageId,
        ]);
    }

    /**
     * Mark the email as failed.
     */
    public function markAsFailed(string $errorMessage): void
    {
        $this->update([
            'status' => 'failed',
            'error_message' => $errorMessage,
        ]);
    }

    /**
     * Mark the email as bounced.
     */
    public function markAsBounced(): void
    {
        $this->update([
            'status' => 'bounced',
        ]);
    }

    /**
     * Mark the email as delivered.
     */
    public function markAsDelivered(): void
    {
        $this->update([
            'status' => 'delivered',
        ]);
    }

    /**
     * Check if the email was sent successfully.
     */
    public function isSent(): bool
    {
        return $this->status === 'sent';
    }

    /**
     * Check if the email failed to send.
     */
    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    /**
     * Check if the email is pending.
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }
}
