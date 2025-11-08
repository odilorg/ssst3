<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Contact extends Model
{
    protected $fillable = [
        'reference',
        'name',
        'email',
        'phone',
        'message',
        'status',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'replied_at' => 'datetime',
    ];

    /**
     * Generate unique reference number on creation
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($contact) {
            if (!$contact->reference) {
                $year = date('Y');
                $count = Contact::whereYear('created_at', $year)->count() + 1;
                $contact->reference = 'CON-' . $year . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
            }
        });
    }

    /**
     * User who replied to this contact
     */
    public function repliedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'replied_by');
    }

    /**
     * Mark contact as replied
     */
    public function markAsReplied($user = null)
    {
        $this->update([
            'status' => 'replied',
            'replied_at' => now(),
            'replied_by' => $user ? $user->id : null,
        ]);
    }
}
