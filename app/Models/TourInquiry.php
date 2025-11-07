<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TourInquiry extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference',
        'tour_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'customer_country',
        'preferred_date',
        'estimated_guests',
        'message',
        'status',
        'replied_at',
        'replied_by',
        'booking_id',
        'converted_at',
    ];

    protected $casts = [
        'preferred_date' => 'date',
        'estimated_guests' => 'integer',
        'replied_at' => 'datetime',
        'converted_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::creating(function ($inquiry) {
            if (empty($inquiry->reference)) {
                $inquiry->reference = $inquiry->generateReference();
            }
        });
    }

    // =========================================================================
    // RELATIONSHIPS
    // =========================================================================

    /**
     * Inquiry belongs to a tour
     */
    public function tour()
    {
        return $this->belongsTo(Tour::class);
    }

    /**
     * Inquiry may be converted to a booking
     */
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * User who replied to this inquiry
     */
    public function repliedBy()
    {
        return $this->belongsTo(User::class, 'replied_by');
    }

    // =========================================================================
    // BUSINESS LOGIC
    // =========================================================================

    /**
     * Generate unique reference number
     * Format: INQ-2025-001, INQ-2025-002, etc.
     */
    public function generateReference()
    {
        $year = Carbon::now()->year;
        $prefix = "INQ-{$year}-";

        // Find the last inquiry with the same year prefix
        $lastInquiry = static::where('reference', 'like', $prefix . '%')
            ->orderBy('reference', 'desc')
            ->first();

        // If no inquiries this year, start at 001
        if (!$lastInquiry) {
            return $prefix . '001';
        }

        // Extract number from last reference and increment
        $lastNumber = (int) substr($lastInquiry->reference, -3);
        $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);

        return $prefix . $newNumber;
    }

    /**
     * Mark inquiry as replied
     */
    public function markAsReplied(User $user)
    {
        $this->update([
            'status' => 'replied',
            'replied_at' => now(),
            'replied_by' => $user->id,
        ]);
    }

    /**
     * Convert inquiry to booking
     */
    public function convertToBooking(Booking $booking)
    {
        $this->update([
            'status' => 'converted',
            'booking_id' => $booking->id,
            'converted_at' => now(),
        ]);
    }

    /**
     * Close inquiry without conversion
     */
    public function close()
    {
        $this->update([
            'status' => 'closed',
        ]);
    }

    // =========================================================================
    // ACCESSORS & SCOPES
    // =========================================================================

    /**
     * Scope: Only new inquiries
     */
    public function scopeNew($query)
    {
        return $query->where('status', 'new');
    }

    /**
     * Scope: Pending reply (new or needs followup)
     */
    public function scopePendingReply($query)
    {
        return $query->whereIn('status', ['new', 'replied'])
                     ->whereNull('converted_at');
    }

    /**
     * Check if inquiry is still open
     */
    public function isOpen()
    {
        return in_array($this->status, ['new', 'replied']);
    }

    /**
     * Check if inquiry has been converted
     */
    public function isConverted()
    {
        return $this->status === 'converted' && $this->booking_id !== null;
    }
}
