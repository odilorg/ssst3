<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference',
        'customer_id',
        'tour_id',
        'departure_id',
        'booking_type',
        'start_date',
        'end_date',
        'pax_total',
        'status',
        'currency',
        'total_price',
        'notes',
        'customer_name',
        'customer_email',
        'customer_phone',
        'customer_country',
        'payment_status',
        'payment_method',
        'payment_uuid',
        'amount_paid',
        'amount_remaining',
        'discount_applied',
        'balance_due_date',
        'special_requests',
        'inquiry_notes',
        'terms_agreed_at',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'balance_due_date' => 'date',
        'terms_agreed_at' => 'datetime',
        'pax_total' => 'integer',
        'total_price' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'amount_remaining' => 'decimal:2',
        'discount_applied' => 'decimal:2',
    ];

    protected static function booted()
    {
        static::saving(function ($booking) {
            if (empty($booking->reference)) {
                $booking->reference = $booking->generateReference();
            }

            if ($booking->tour && $booking->start_date) {
                $booking->refreshDatesFromTrip();
            }

            if (!$booking->balance_due_date && $booking->departure && $booking->tour) {
                $booking->balance_due_date = $booking->tour->calculateBalanceDueDate(
                    $booking->departure->start_date
                );
            }

            if ($booking->isDirty('total_price') && !$booking->amount_remaining) {
                $booking->amount_remaining = $booking->total_price;
            }
        });

        static::updated(function ($booking) {
            if ($booking->isDirty('status') && $booking->departure) {
                $booking->updateDepartureCapacity();
            }
        });

        static::deleted(function ($booking) {
            if ($booking->departure) {
                $booking->departure->decrementBooked($booking->pax_total);
                $booking->departure->updateStatus();
            }
        });
    }

    // Relationships
    public function tour()
    {
        return $this->belongsTo(Tour::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function departure()
    {
        return $this->belongsTo(TourDeparture::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function completedPayments()
    {
        return $this->payments()->where('status', 'completed');
    }
public function paymentTokens()    {        return $this->hasMany(PaymentToken::class);    }    public function hasValidPaymentToken(): bool    {        return $this->paymentTokens()            ->where('expires_at', '>', now())            ->whereNull('used_at')            ->exists();    }

    public function travelers()
    {
        return $this->hasMany(BookingTraveler::class);
    }

    public function itineraryItems()
    {
        return $this->hasMany(BookingItineraryItem::class);
    }

    public function assignments()
    {
        return $this->hasManyThrough(
            BookingItineraryItemAssignment::class,
            BookingItineraryItem::class,
            'booking_id',
            'booking_itinerary_item_id',
            'id',
            'id'
        );
    }

    public function supplierRequests()
    {
        return $this->hasMany(SupplierRequest::class);
    }

    public function extras()
    {
        return $this->belongsToMany(TourExtra::class, 'booking_tour_extra')
                    ->withPivot('price_at_booking', 'quantity')
                    ->withTimestamps();
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    // Scopes
    public function scopePendingPayment($query)
    {
        return $query->whereIn('status', ['draft', 'pending_payment']);
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    public function scopeInquiries($query)
    {
        return $query->where('status', 'inquiry');
    }

    public function scopeBalanceDueSoon($query, $days = 7)
    {
        return $query->where('balance_due_date', '<=', now()->addDays($days))
                     ->where('payment_status', 'deposit_paid');
    }

    // Business Logic Methods
    public function generateReference()
    {
        $year = Carbon::now()->year;
        $prefix = "BK-{$year}-";

        $lastBooking = static::where('reference', 'like', $prefix . '%')
            ->orderBy('reference', 'desc')
            ->first();

        if ($lastBooking) {
            $lastNumber = (int) substr($lastBooking->reference, strlen($prefix));
            $number = $lastNumber + 1;
        } else {
            $number = 1;
        }

        return $prefix . str_pad($number, 3, '0', STR_PAD_LEFT);
    }

    public function refreshDatesFromTrip()
    {
        if ($this->tour && $this->start_date) {
            $duration = max(1, $this->tour->duration_days);
            $this->end_date = $this->start_date->addDays($duration - 1);
        }
    }

    public function isFullyPaid(): bool
    {
        return $this->payment_status === 'fully_paid';
    }

    public function hasDepositPaid(): bool
    {
        return in_array($this->payment_status, ['deposit_paid', 'fully_paid']);
    }

    public function calculateDepositAmount(): float
    {
        return round($this->total_price * 0.30, 2);
    }

    public function calculateFullPaymentAmount(): float
    {
        $discount = round($this->total_price * 0.10, 2);
        return round($this->total_price - $discount, 2);
    }

    public function recalculatePaymentTotals(): void
    {
        $totalPaid = $this->completedPayments()->sum('amount');

        $this->update([
            'amount_paid' => $totalPaid,
            'amount_remaining' => max(0, $this->total_price - $totalPaid),
            'payment_status' => $this->determinePaymentStatus($totalPaid),
        ]);
    }

    protected function determinePaymentStatus(float $totalPaid): string
    {
        if ($totalPaid <= 0) {
            return 'unpaid';
        }

        $depositAmount = $this->calculateDepositAmount();

        if ($totalPaid >= $this->total_price) {
            return 'fully_paid';
        }

        if ($totalPaid >= $depositAmount) {
            return 'deposit_paid';
        }

        return 'payment_pending';
    }

    public function isBalanceOverdue(): bool
    {
        if (!$this->balance_due_date) {
            return false;
        }

        return now()->isAfter($this->balance_due_date)
            && !$this->isFullyPaid();
    }

    public function hasAgreedToTerms(): bool
    {
        return !is_null($this->terms_agreed_at);
    }

    public function agreeToTerms(): void
    {
        $this->update(['terms_agreed_at' => now()]);
    }

    public function isGroupBooking(): bool
    {
        return $this->booking_type === 'group';
    }

    public function isPrivateBooking(): bool
    {
        return $this->booking_type === 'private';
    }

    public function isInquiry(): bool
    {
        return $this->status === 'inquiry';
    }

    protected function updateDepartureCapacity(): void
    {
        if (!$this->departure) return;

        $oldStatus = $this->getOriginal('status');
        $newStatus = $this->status;

        if (!in_array($oldStatus, ['confirmed', 'in_progress', 'completed'])
            && in_array($newStatus, ['confirmed', 'in_progress', 'completed'])) {
            $this->departure->incrementBooked($this->pax_total);
        }

        if (in_array($oldStatus, ['confirmed', 'in_progress', 'completed'])
            && !in_array($newStatus, ['confirmed', 'in_progress', 'completed'])) {
            $this->departure->decrementBooked($this->pax_total);
        }

        $this->departure->updateStatus();
    }
}
