# Phase 2 - Tour & Booking Model Updates Reference

This document contains the exact code changes needed to complete Phase 2.

---

## Tour Model Updates

**File:** `app/Models/Tour.php`

### 1. Add to `$fillable` array (after line 26):

```php
        'group_price_per_person',      // NEW
        'private_price_per_person',    // NEW
        'private_minimum_charge',      // NEW
```

### 2. Add to `$fillable` array (after line 57):

```php
        'booking_window_hours',           // NEW
        'balance_due_days',              // NEW
        'allow_last_minute_full_payment', // NEW
        'requires_traveler_details',      // NEW
```

### 3. Add to `$casts` array (after line 75):

```php
        'allow_last_minute_full_payment' => 'boolean',  // NEW
        'requires_traveler_details' => 'boolean',       // NEW
```

### 4. Add to `$casts` array (after line 84):

```php
        'booking_window_hours' => 'integer',    // NEW
        'balance_due_days' => 'integer',        // NEW
```

### 5. Add to `$casts` array (after line 90):

```php
        'group_price_per_person' => 'decimal:2',    // NEW
        'private_price_per_person' => 'decimal:2',  // NEW
        'private_minimum_charge' => 'decimal:2',    // NEW
```

### 6. Add new relationships (after line 171):

```php
    /**
     * Get all departures for this tour  (NEW)
     */
    public function departures()
    {
        return $this->hasMany(TourDeparture::class);
    }

    /**
     * Get upcoming departures only  (NEW)
     */
    public function upcomingDepartures()
    {
        return $this->departures()
            ->where('start_date', '>=', now())
            ->whereIn('status', ['open', 'guaranteed'])
            ->orderBy('start_date');
    }

    /**
     * Get available departures (using scope)  (NEW)
     */
    public function availableDepartures()
    {
        return $this->departures()
            ->available()
            ->orderBy('start_date');
    }
```

### 7. Add new helper methods (after line 301, before ACCESSORS section):

```php
    /**
     * Check if tour supports group bookings  (NEW)
     */
    public function supportsGroupBookings(): bool
    {
        return in_array($this->tour_type, ['group_only', 'hybrid']);
    }

    /**
     * Check if tour supports private bookings  (NEW)
     */
    public function supportsPrivateBookings(): bool
    {
        return in_array($this->tour_type, ['private_only', 'hybrid']);
    }

    /**
     * Get price for booking type  (NEW)
     */
    public function getPriceForType(string $type): float
    {
        return $type === 'group'
            ? (float) $this->group_price_per_person
            : (float) $this->private_price_per_person;
    }

    /**
     * Calculate total for private booking  (NEW)
     */
    public function calculatePrivateTotal(int $pax): float
    {
        $perPersonTotal = $this->private_price_per_person * $pax;
        return max($perPersonTotal, $this->private_minimum_charge ?? 0);
    }

    /**
     * Check if booking window allows booking for given date  (NEW)
     */
    public function isBookableForDate(Carbon $departureDate): bool
    {
        $hoursDifference = now()->diffInHours($departureDate, false);
        return $hoursDifference >= ($this->booking_window_hours ?? 72);
    }

    /**
     * Calculate balance due date for departure  (NEW)
     */
    public function calculateBalanceDueDate(Carbon $departureDate): Carbon
    {
        return $departureDate->copy()->subDays($this->balance_due_days ?? 3);
    }
```

### 8. Add use Carbon at top of file (after line 7):

```php
use Carbon\Carbon;
```

---

## Booking Model Updates

**File:** `app/Models/Booking.php`

### Complete Updated Booking Model:

```php
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
        'departure_id',                 // NEW
        'booking_type',                 // NEW
        'start_date',
        'end_date',
        'pax_total',
        'status',
        'currency',
        'total_price',
        'notes',

        // NEW: Customer information
        'customer_name',
        'customer_email',
        'customer_phone',
        'customer_country',

        // NEW: Payment tracking
        'payment_status',
        'payment_method',
        'payment_uuid',
        'amount_paid',
        'amount_remaining',
        'discount_applied',
        'balance_due_date',

        // NEW: Special requests
        'special_requests',
        'inquiry_notes',

        // NEW: Terms agreement
        'terms_agreed_at',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'balance_due_date' => 'date',           // NEW
        'terms_agreed_at' => 'datetime',        // NEW
        'pax_total' => 'integer',
        'total_price' => 'decimal:2',
        'amount_paid' => 'decimal:2',           // NEW
        'amount_remaining' => 'decimal:2',      // NEW
        'discount_applied' => 'decimal:2',      // NEW
    ];

    protected static function booted()
    {
        static::saving(function ($booking) {
            // Generate reference if not set
            if (empty($booking->reference)) {
                $booking->reference = $booking->generateReference();
            }

            // Calculate end_date based on tour duration
            if ($booking->tour && $booking->start_date) {
                $booking->refreshDatesFromTrip();
            }

            // NEW: Set balance due date if not set
            if (!$booking->balance_due_date && $booking->departure && $booking->tour) {
                $booking->balance_due_date = $booking->tour->calculateBalanceDueDate(
                    $booking->departure->start_date
                );
            }

            // NEW: Initialize amount_remaining
            if ($booking->isDirty('total_price') && !$booking->amount_remaining) {
                $booking->amount_remaining = $booking->total_price;
            }
        });

        // NEW: Update departure capacity when booking confirmed/cancelled
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

    /**
     * NEW: Get the departure this booking is for
     */
    public function departure()
    {
        return $this->belongsTo(TourDeparture::class);
    }

    /**
     * NEW: Get all payments for this booking
     */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * NEW: Get only completed payments
     */
    public function completedPayments()
    {
        return $this->payments()->where('status', 'completed');
    }

    /**
     * NEW: Get travelers for this booking
     */
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

    // NEW: Scopes
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

    // NEW: Payment Methods

    /**
     * Check if booking is fully paid
     */
    public function isFullyPaid(): bool
    {
        return $this->payment_status === 'fully_paid';
    }

    /**
     * Check if booking has deposit paid
     */
    public function hasDepositPaid(): bool
    {
        return in_array($this->payment_status, ['deposit_paid', 'fully_paid']);
    }

    /**
     * Calculate deposit amount (30%)
     */
    public function calculateDepositAmount(): float
    {
        return round($this->total_price * 0.30, 2);
    }

    /**
     * Calculate full payment amount with discount (10% off)
     */
    public function calculateFullPaymentAmount(): float
    {
        $discount = round($this->total_price * 0.10, 2);
        return round($this->total_price - $discount, 2);
    }

    /**
     * Update payment totals based on completed payments
     */
    public function recalculatePaymentTotals(): void
    {
        $totalPaid = $this->completedPayments()->sum('amount');

        $this->update([
            'amount_paid' => $totalPaid,
            'amount_remaining' => max(0, $this->total_price - $totalPaid),
            'payment_status' => $this->determinePaymentStatus($totalPaid),
        ]);
    }

    /**
     * Determine payment status based on amount paid
     */
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

    /**
     * Check if balance payment is overdue
     */
    public function isBalanceOverdue(): bool
    {
        if (!$this->balance_due_date) {
            return false;
        }

        return now()->isAfter($this->balance_due_date)
            && !$this->isFullyPaid();
    }

    /**
     * Check if customer agreed to terms
     */
    public function hasAgreedToTerms(): bool
    {
        return !is_null($this->terms_agreed_at);
    }

    /**
     * Record terms agreement
     */
    public function agreeToTerms(): void
    {
        $this->update(['terms_agreed_at' => now()]);
    }

    /**
     * Check if this is a group booking
     */
    public function isGroupBooking(): bool
    {
        return $this->booking_type === 'group';
    }

    /**
     * Check if this is a private booking
     */
    public function isPrivateBooking(): bool
    {
        return $this->booking_type === 'private';
    }

    /**
     * Check if this is an inquiry (request-to-book)
     */
    public function isInquiry(): bool
    {
        return $this->status === 'inquiry';
    }

    /**
     * Update departure booked_pax when booking status changes
     */
    protected function updateDepartureCapacity(): void
    {
        if (!$this->departure) return;

        $oldStatus = $this->getOriginal('status');
        $newStatus = $this->status;

        // Moving to confirmed = increment
        if (!in_array($oldStatus, ['confirmed', 'in_progress', 'completed'])
            && in_array($newStatus, ['confirmed', 'in_progress', 'completed'])) {
            $this->departure->incrementBooked($this->pax_total);
        }

        // Moving from confirmed = decrement
        if (in_array($oldStatus, ['confirmed', 'in_progress', 'completed'])
            && !in_array($newStatus, ['confirmed', 'in_progress', 'completed'])) {
            $this->departure->decrementBooked($this->pax_total);
        }

        $this->departure->updateStatus();
    }
}
```

---

## Quick Application Instructions:

1. Open `app/Models/Tour.php` and apply sections 1-8 above
2. Replace `app/Models/Booking.php` completely with the code above

---

## Verification Commands:

```bash
# Check for syntax errors
php artisan tinker --execute="new App\Models\Tour(); new App\Models\Booking();"

# Test Tour relationships
php artisan tinker --execute="\$t = App\Models\Tour::first(); \$t->departures;"

# Test new Tour methods
php artisan tinker --execute="\$t = App\Models\Tour::first(); \$t->supportsGroupBookings();"
```
