# PHASE 2: Laravel Models & Relationships - Detailed Implementation Plan

**Project:** Jahongir Travel Tour Booking System
**Phase:** 2 of 7
**Estimated Time:** 1-2 days
**Prerequisites:** Phase 1 (Database Schema) completed ✅

---

## Table of Contents

1. [Overview](#overview)
2. [Models to Create](#models-to-create)
3. [Models to Update](#models-to-update)
4. [Step-by-Step Implementation](#step-by-step-implementation)
5. [Testing Strategy](#testing-strategy)
6. [Verification Checklist](#verification-checklist)

---

## Overview

### Objectives

Phase 2 builds the Eloquent model layer on top of the Phase 1 database schema. This includes:

1. **Creating new models** for tour departures, payments, and booking travelers
2. **Updating existing models** (Tour, Booking) with new fields and relationships
3. **Defining relationships** between all booking-related models
4. **Adding business logic** through accessors, mutators, scopes, and methods
5. **Implementing model events** for automatic calculations and updates

### Why This Phase Matters

Models are the backbone of Laravel applications. Proper model design ensures:
- Type-safe data access with casting
- Automatic relationship loading
- Business logic centralization
- Easier testing and maintenance

---

## Models to Create

### 1. TourDeparture Model

**Location:** `app/Models/TourDeparture.php`

**Purpose:** Represents fixed departure dates for group tours

**Key Features:**
- Belongs to Tour
- Has many Bookings
- Tracks capacity (max_pax, booked_pax)
- Status management (open/guaranteed/full/completed/cancelled)
- Automatic status updates based on bookings
- Availability checking
- Price override support

**Relationships:**
```php
- belongsTo: Tour
- hasMany: Booking
```

**Scopes:**
```php
- scopeAvailable()      // Only open/guaranteed departures
- scopeUpcoming()       // Future departures
- scopeFull()           // At capacity
- scopeForTour($tourId) // Filter by tour
```

**Methods:**
```php
- isAvailable(): bool           // Can accept bookings?
- spotsRemaining(): int         // max_pax - booked_pax
- hasSpace($paxCount): bool     // Can fit X people?
- incrementBooked($pax): void   // Add booked passengers
- decrementBooked($pax): void   // Remove booked passengers
- updateStatus(): void          // Recalculate status
- getEffectivePrice(): float    // Override or tour price
```

---

### 2. Payment Model

**Location:** `app/Models/Payment.php`

**Purpose:** Audit log for all payment transactions

**Key Features:**
- Belongs to Booking
- Immutable records (append-only)
- JSON gateway response storage
- Support for negative amounts (refunds)
- Status tracking

**Relationships:**
```php
- belongsTo: Booking
```

**Scopes:**
```php
- scopeCompleted()           // Only successful payments
- scopeForBooking($bookingId) // Filter by booking
- scopeDeposits()            // Only deposit payments
- scopeRefunds()             // Only refunds
```

**Methods:**
```php
- isCompleted(): bool
- isFailed(): bool
- isRefund(): bool
- getFormattedAmount(): string
```

**Notes:**
- Never delete payments - use status 'refunded' instead
- Store full gateway response for audit trail
- Negative amounts indicate refunds

---

### 3. BookingTraveler Model

**Location:** `app/Models/BookingTraveler.php`

**Purpose:** Stores individual passenger details for tours requiring traveler info

**Key Features:**
- Belongs to Booking
- Required only when Tour->requires_traveler_details = true
- Passport validation
- PII (Personally Identifiable Information) - handle with care

**Relationships:**
```php
- belongsTo: Booking
```

**Validation:**
```php
- full_name: required, max 255
- passport_number: nullable, alphanumeric
- passport_expiry: nullable, date, after:today
- date_of_birth: nullable, date, before:today
```

**Methods:**
```php
- hasValidPassport(): bool // Check expiry date
- isAdult(): bool          // Age >= 18
- getAge(): int            // Calculate from DOB
```

---

## Models to Update

### 1. Tour Model (Existing)

**File:** `app/Models/Tour.php`

**New Fields to Add to $fillable:**
```php
// Tour type and pricing
'tour_type',                      // NEW: enum
'group_price_per_person',         // NEW: decimal
'private_price_per_person',       // NEW: decimal
'private_minimum_charge',         // NEW: decimal

// Booking configuration
'booking_window_hours',           // NEW: integer (default 72)
'balance_due_days',              // NEW: integer (default 3)
'allow_last_minute_full_payment', // NEW: boolean
'requires_traveler_details',      // NEW: boolean
```

**New $casts to Add:**
```php
'tour_type' => 'string',
'group_price_per_person' => 'decimal:2',
'private_price_per_person' => 'decimal:2',
'private_minimum_charge' => 'decimal:2',
'booking_window_hours' => 'integer',
'balance_due_days' => 'integer',
'allow_last_minute_full_payment' => 'boolean',
'requires_traveler_details' => 'boolean',
```

**New Relationships to Add:**
```php
public function departures()
{
    return $this->hasMany(TourDeparture::class);
}

public function upcomingDepartures()
{
    return $this->departures()
        ->where('start_date', '>=', now())
        ->whereIn('status', ['open', 'guaranteed'])
        ->orderBy('start_date');
}

public function availableDepartures()
{
    return $this->departures()
        ->available() // Uses scope
        ->orderBy('start_date');
}
```

**New Methods to Add:**
```php
/**
 * Check if tour supports group bookings
 */
public function supportsGroupBookings(): bool
{
    return in_array($this->tour_type, ['group_only', 'hybrid']);
}

/**
 * Check if tour supports private bookings
 */
public function supportsPrivateBookings(): bool
{
    return in_array($this->tour_type, ['private_only', 'hybrid']);
}

/**
 * Get price for booking type
 */
public function getPriceForType(string $type): float
{
    return $type === 'group'
        ? $this->group_price_per_person
        : $this->private_price_per_person;
}

/**
 * Calculate total for private booking
 */
public function calculatePrivateTotal(int $pax): float
{
    $perPersonTotal = $this->private_price_per_person * $pax;
    return max($perPersonTotal, $this->private_minimum_charge ?? 0);
}

/**
 * Check if booking window allows booking for given date
 */
public function isBookableForDate(Carbon $departureDate): bool
{
    $hoursDifference = now()->diffInHours($departureDate, false);
    return $hoursDifference >= $this->booking_window_hours;
}

/**
 * Calculate balance due date for departure
 */
public function calculateBalanceDueDate(Carbon $departureDate): Carbon
{
    return $departureDate->copy()->subDays($this->balance_due_days);
}
```

---

### 2. Booking Model (Existing)

**File:** `app/Models/Booking.php`

**New Fields to Add to $fillable:**
```php
// Departure reference
'departure_id',                   // NEW: foreign key

// Booking type
'booking_type',                   // NEW: enum(group, private)

// Customer information
'customer_name',                  // NEW: string
'customer_email',                 // NEW: string
'customer_phone',                 // NEW: string
'customer_country',               // NEW: string

// Payment tracking
'payment_status',                 // NEW: enum
'payment_method',                 // NEW: enum
'payment_uuid',                   // NEW: OCTO payment UUID
'amount_paid',                    // NEW: decimal
'amount_remaining',               // NEW: decimal
'discount_applied',               // NEW: decimal
'balance_due_date',               // NEW: date

// Special requests
'special_requests',               // NEW: text
'inquiry_notes',                  // NEW: text (for request-to-book)

// Terms agreement
'terms_agreed_at',                // NEW: timestamp
```

**Update $casts:**
```php
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
```

**New Relationships to Add:**
```php
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

public function travelers()
{
    return $this->hasMany(BookingTraveler::class);
}
```

**New Scopes to Add:**
```php
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
```

**New Methods to Add:**
```php
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
```

**Update Existing booted() Method:**
```php
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
        if (!$booking->balance_due_date && $booking->departure) {
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
```

---

## Step-by-Step Implementation

### Step 1: Create TourDeparture Model

**Command:**
```bash
php artisan make:model TourDeparture
```

**Code:** See [Full TourDeparture Model Code](#tourdeparture-model-full-code) below

**Verification:**
```bash
php artisan tinker
>>> $departure = new App\Models\TourDeparture();
>>> $departure->getFillable();
>>> $departure->getCasts();
```

---

### Step 2: Create Payment Model

**Command:**
```bash
php artisan make:model Payment
```

**Code:** See [Full Payment Model Code](#payment-model-full-code) below

**Verification:**
```bash
php artisan tinker
>>> $payment = new App\Models\Payment();
>>> $payment->getCasts();
```

---

### Step 3: Create BookingTraveler Model

**Command:**
```bash
php artisan make:model BookingTraveler
```

**Code:** See [Full BookingTraveler Model Code](#bookingtraveler-model-full-code) below

---

### Step 4: Update Tour Model

**File:** `app/Models/Tour.php`

**Changes:**
1. Add new fields to `$fillable` array
2. Add new casts to `$casts` array
3. Add `departures()` relationship
4. Add `upcomingDepartures()` relationship
5. Add `availableDepartures()` relationship
6. Add helper methods (supportsGroupBookings, etc.)

**Testing:**
```bash
php artisan tinker
>>> $tour = App\Models\Tour::first();
>>> $tour->supportsGroupBookings();
>>> $tour->calculatePrivateTotal(4);
```

---

### Step 5: Update Booking Model

**File:** `app/Models/Booking.php`

**Changes:**
1. Add new fields to `$fillable` array
2. Update `$casts` array
3. Add new relationships (departure, payments, travelers)
4. Add scopes (pendingPayment, confirmed, inquiries)
5. Add payment calculation methods
6. Update `booted()` method with capacity tracking

**Testing:**
```bash
php artisan tinker
>>> $booking = App\Models\Booking::first();
>>> $booking->calculateDepositAmount();
>>> $booking->calculateFullPaymentAmount();
>>> $booking->isFullyPaid();
```

---

### Step 6: Test All Relationships

**Create test data in Tinker:**
```php
// 1. Create a tour departure
$tour = Tour::first();
$departure = TourDeparture::create([
    'tour_id' => $tour->id,
    'start_date' => now()->addDays(30),
    'end_date' => now()->addDays(30 + $tour->duration_days - 1),
    'max_pax' => 12,
    'status' => 'open',
    'departure_type' => 'group',
]);

// 2. Test tour -> departures relationship
$tour->departures; // Should include our new departure
$tour->upcomingDepartures; // Should include our new departure

// 3. Create a booking for this departure
$booking = Booking::create([
    'reference' => 'TEST-001',
    'tour_id' => $tour->id,
    'departure_id' => $departure->id,
    'booking_type' => 'group',
    'start_date' => $departure->start_date,
    'end_date' => $departure->end_date,
    'pax_total' => 2,
    'total_price' => 500.00,
    'currency' => 'USD',
    'status' => 'draft',
    'customer_name' => 'Test Customer',
    'customer_email' => 'test@example.com',
]);

// 4. Test booking -> departure relationship
$booking->departure; // Should return our departure

// 5. Test departure -> bookings relationship
$departure->bookings; // Should include our booking

// 6. Create a payment for this booking
$payment = Payment::create([
    'booking_id' => $booking->id,
    'amount' => 150.00,
    'payment_method' => 'octo_uzcard',
    'status' => 'completed',
    'payment_type' => 'deposit',
    'transaction_id' => 'TEST-TXN-001',
    'processed_at' => now(),
]);

// 7. Test booking -> payments relationship
$booking->payments; // Should include our payment
$booking->completedPayments; // Should include our payment

// 8. Test payment calculations
$booking->recalculatePaymentTotals();
$booking->fresh(); // Reload from DB
$booking->amount_paid; // Should be 150.00
$booking->amount_remaining; // Should be 350.00
$booking->payment_status; // Should be 'deposit_paid'

// 9. Create travelers (if tour requires it)
$traveler = BookingTraveler::create([
    'booking_id' => $booking->id,
    'full_name' => 'John Doe',
    'date_of_birth' => '1990-01-01',
    'nationality' => 'USA',
    'passport_number' => 'P12345678',
    'passport_expiry' => now()->addYears(2),
]);

// 10. Test booking -> travelers relationship
$booking->travelers; // Should include our traveler

// 11. Test capacity tracking
$departure->refresh();
$departure->booked_pax; // Should still be 0 (booking is draft)

$booking->update(['status' => 'confirmed']);
$departure->refresh();
$departure->booked_pax; // Should now be 2
$departure->status; // Should still be 'open' (not at capacity)
```

---

## Full Model Code

### TourDeparture Model Full Code

```php
<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TourDeparture extends Model
{
    use HasFactory;

    protected $fillable = [
        'tour_id',
        'start_date',
        'end_date',
        'max_pax',
        'booked_pax',
        'min_pax',
        'price_per_person',
        'status',
        'departure_type',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'max_pax' => 'integer',
        'booked_pax' => 'integer',
        'min_pax' => 'integer',
        'price_per_person' => 'decimal:2',
    ];

    // ==========================================
    // RELATIONSHIPS
    // ==========================================

    /**
     * Get the tour this departure belongs to
     */
    public function tour()
    {
        return $this->belongsTo(Tour::class);
    }

    /**
     * Get all bookings for this departure
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class, 'departure_id');
    }

    /**
     * Get confirmed bookings only
     */
    public function confirmedBookings()
    {
        return $this->bookings()
            ->whereIn('status', ['confirmed', 'in_progress', 'completed']);
    }

    // ==========================================
    // SCOPES
    // ==========================================

    /**
     * Scope to get only available departures (open or guaranteed)
     */
    public function scopeAvailable($query)
    {
        return $query->whereIn('status', ['open', 'guaranteed']);
    }

    /**
     * Scope to get upcoming departures (future dates)
     */
    public function scopeUpcoming($query)
    {
        return $query->where('start_date', '>=', now());
    }

    /**
     * Scope to get full departures
     */
    public function scopeFull($query)
    {
        return $query->where('status', 'full');
    }

    /**
     * Scope to filter by tour
     */
    public function scopeForTour($query, $tourId)
    {
        return $query->where('tour_id', $tourId);
    }

    /**
     * Scope to get group departures
     */
    public function scopeGroup($query)
    {
        return $query->where('departure_type', 'group');
    }

    /**
     * Scope to get private departures
     */
    public function scopePrivate($query)
    {
        return $query->where('departure_type', 'private');
    }

    // ==========================================
    // BUSINESS LOGIC METHODS
    // ==========================================

    /**
     * Check if departure can accept bookings
     */
    public function isAvailable(): bool
    {
        return in_array($this->status, ['open', 'guaranteed'])
            && $this->start_date->isFuture();
    }

    /**
     * Get number of spots remaining
     */
    public function spotsRemaining(): int
    {
        return max(0, $this->max_pax - $this->booked_pax);
    }

    /**
     * Check if departure has space for X people
     */
    public function hasSpace(int $paxCount): bool
    {
        return $this->spotsRemaining() >= $paxCount;
    }

    /**
     * Increment booked passengers
     */
    public function incrementBooked(int $pax): void
    {
        $this->increment('booked_pax', $pax);
        $this->updateStatus();
    }

    /**
     * Decrement booked passengers
     */
    public function decrementBooked(int $pax): void
    {
        $this->decrement('booked_pax', max(0, $pax));
        $this->updateStatus();
    }

    /**
     * Update status based on current capacity and dates
     */
    public function updateStatus(): void
    {
        $this->refresh();

        // If in the past, mark as completed
        if ($this->end_date->isPast()) {
            $this->update(['status' => 'completed']);
            return;
        }

        // If cancelled, don't auto-update
        if ($this->status === 'cancelled') {
            return;
        }

        // Check capacity
        if ($this->booked_pax >= $this->max_pax) {
            $this->update(['status' => 'full']);
        } elseif ($this->min_pax && $this->booked_pax >= $this->min_pax) {
            $this->update(['status' => 'guaranteed']);
        } else {
            $this->update(['status' => 'open']);
        }
    }

    /**
     * Get effective price (override or tour price)
     */
    public function getEffectivePrice(): float
    {
        if ($this->price_per_person) {
            return (float) $this->price_per_person;
        }

        return $this->departure_type === 'group'
            ? (float) $this->tour->group_price_per_person
            : (float) $this->tour->private_price_per_person;
    }

    /**
     * Check if departure is confirmed (has min passengers)
     */
    public function isGuaranteed(): bool
    {
        return $this->status === 'guaranteed' || $this->status === 'full';
    }

    /**
     * Check if departure is full
     */
    public function isFull(): bool
    {
        return $this->status === 'full';
    }

    /**
     * Get occupancy percentage
     */
    public function getOccupancyPercentage(): float
    {
        if ($this->max_pax === 0) {
            return 0;
        }

        return round(($this->booked_pax / $this->max_pax) * 100, 1);
    }
}
```

---

### Payment Model Full Code

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'amount',
        'payment_method',
        'status',
        'payment_type',
        'transaction_id',
        'gateway_response',
        'processed_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'gateway_response' => 'array',
        'processed_at' => 'datetime',
    ];

    // ==========================================
    // RELATIONSHIPS
    // ==========================================

    /**
     * Get the booking this payment belongs to
     */
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    // ==========================================
    // SCOPES
    // ==========================================

    /**
     * Scope to get only completed payments
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope to get only failed payments
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Scope to filter by booking
     */
    public function scopeForBooking($query, $bookingId)
    {
        return $query->where('booking_id', $bookingId);
    }

    /**
     * Scope to get deposits only
     */
    public function scopeDeposits($query)
    {
        return $query->where('payment_type', 'deposit');
    }

    /**
     * Scope to get full payments only
     */
    public function scopeFullPayments($query)
    {
        return $query->where('payment_type', 'full');
    }

    /**
     * Scope to get balance payments only
     */
    public function scopeBalancePayments($query)
    {
        return $query->where('payment_type', 'balance');
    }

    /**
     * Scope to get refunds only
     */
    public function scopeRefunds($query)
    {
        return $query->where('payment_type', 'refund');
    }

    // ==========================================
    // BUSINESS LOGIC METHODS
    // ==========================================

    /**
     * Check if payment is completed
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Check if payment failed
     */
    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    /**
     * Check if payment is pending
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if this is a refund
     */
    public function isRefund(): bool
    {
        return $this->payment_type === 'refund' || $this->amount < 0;
    }

    /**
     * Get formatted amount with currency
     */
    public function getFormattedAmount(): string
    {
        $currency = $this->booking->currency ?? 'USD';
        $amount = abs($this->amount);

        $formatted = number_format($amount, 2) . ' ' . $currency;

        if ($this->isRefund()) {
            return '-' . $formatted;
        }

        return $formatted;
    }

    /**
     * Get payment method display name
     */
    public function getPaymentMethodName(): string
    {
        return match($this->payment_method) {
            'octo_uzcard' => 'UzCard via OCTO',
            'octo_humo' => 'HUMO via OCTO',
            'octo_visa' => 'VISA via OCTO',
            'octo_mastercard' => 'MasterCard via OCTO',
            'bank_transfer' => 'Bank Transfer',
            'cash' => 'Cash',
            default => ucwords(str_replace('_', ' ', $this->payment_method)),
        };
    }

    // ==========================================
    // MODEL EVENTS
    // ==========================================

    protected static function booted()
    {
        // After payment is completed, update booking totals
        static::updated(function ($payment) {
            if ($payment->isDirty('status') && $payment->isCompleted()) {
                $payment->booking->recalculatePaymentTotals();
            }
        });

        // Set processed_at when status changes to completed
        static::updating(function ($payment) {
            if ($payment->isDirty('status') && $payment->status === 'completed') {
                if (!$payment->processed_at) {
                    $payment->processed_at = now();
                }
            }
        });
    }
}
```

---

### BookingTraveler Model Full Code

```php
<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingTraveler extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'full_name',
        'date_of_birth',
        'nationality',
        'passport_number',
        'passport_expiry',
        'dietary_requirements',
        'special_needs',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'passport_expiry' => 'date',
    ];

    // ==========================================
    // RELATIONSHIPS
    // ==========================================

    /**
     * Get the booking this traveler belongs to
     */
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    // ==========================================
    // BUSINESS LOGIC METHODS
    // ==========================================

    /**
     * Check if passport is valid (not expired)
     */
    public function hasValidPassport(): bool
    {
        if (!$this->passport_expiry) {
            return false;
        }

        return $this->passport_expiry->isFuture();
    }

    /**
     * Check if traveler is an adult (>= 18 years old)
     */
    public function isAdult(): bool
    {
        return $this->getAge() >= 18;
    }

    /**
     * Get traveler age
     */
    public function getAge(): ?int
    {
        if (!$this->date_of_birth) {
            return null;
        }

        return $this->date_of_birth->age;
    }

    /**
     * Check if traveler has dietary requirements
     */
    public function hasDietaryRequirements(): bool
    {
        return !empty($this->dietary_requirements);
    }

    /**
     * Check if traveler has special needs
     */
    public function hasSpecialNeeds(): bool
    {
        return !empty($this->special_needs);
    }

    /**
     * Get initials for display
     */
    public function getInitials(): string
    {
        $names = explode(' ', $this->full_name);

        if (count($names) === 1) {
            return strtoupper(substr($names[0], 0, 2));
        }

        return strtoupper(substr($names[0], 0, 1) . substr($names[count($names) - 1], 0, 1));
    }
}
```

---

## Testing Strategy

### Unit Tests

Create `tests/Unit/Models/` directory with the following tests:

**1. TourDepartureTest.php**
```php
test('it calculates spots remaining correctly')
test('it checks availability correctly')
test('it updates status when capacity reached')
test('it increments booked passengers')
test('it decrements booked passengers')
test('it gets effective price with override')
test('it gets effective price from tour')
```

**2. PaymentTest.php**
```php
test('it identifies completed payments')
test('it identifies refunds')
test('it formats amount correctly')
test('it triggers booking recalculation on completion')
```

**3. BookingTravelerTest.php**
```php
test('it validates passport expiry')
test('it calculates age correctly')
test('it identifies adults')
test('it generates initials')
```

**4. BookingTest.php (update existing)**
```php
test('it calculates deposit amount correctly')
test('it calculates full payment with discount')
test('it recalculates payment totals')
test('it determines payment status correctly')
test('it updates departure capacity on confirmation')
test('it checks if balance is overdue')
```

**5. TourTest.php (update existing)**
```php
test('it supports group bookings based on tour type')
test('it supports private bookings based on tour type')
test('it gets price for booking type')
test('it calculates private total with minimum charge')
test('it checks if date is bookable within window')
test('it calculates balance due date')
```

### Feature Tests

**1. DepartureCapacityTest.php**
```php
test('confirming booking increments departure capacity')
test('cancelling booking decrements departure capacity')
test('deleting booking decrements departure capacity')
test('departure status updates automatically')
```

**2. PaymentFlowTest.php**
```php
test('completing payment updates booking totals')
test('multiple payments aggregate correctly')
test('refund decrements total paid')
test('payment status updates based on total paid')
```

---

## Verification Checklist

After implementing Phase 2, verify the following:

### Model Creation
- [ ] TourDeparture model created
- [ ] Payment model created
- [ ] BookingTraveler model created

### Model Updates
- [ ] Tour model updated with new fields and relationships
- [ ] Booking model updated with new fields and relationships

### Relationships Work
- [ ] Tour -> Departures
- [ ] Tour -> Upcoming Departures
- [ ] Departure -> Tour
- [ ] Departure -> Bookings
- [ ] Booking -> Departure
- [ ] Booking -> Payments
- [ ] Booking -> Travelers
- [ ] Payment -> Booking
- [ ] BookingTraveler -> Booking

### Business Logic
- [ ] Departure capacity tracking works
- [ ] Departure status updates automatically
- [ ] Payment totals calculate correctly
- [ ] Deposit amount calculates as 30%
- [ ] Full payment discount calculates as 10%
- [ ] Balance due date calculates correctly
- [ ] Booking window validation works
- [ ] Private tour minimum charge applies

### Tinker Tests Pass
- [ ] Can create departures
- [ ] Can create bookings with departures
- [ ] Can create payments
- [ ] Can create travelers
- [ ] Confirming booking increments departure booked_pax
- [ ] Cancelling booking decrements departure booked_pax
- [ ] Payment completion updates booking totals

### No Errors
- [ ] No PHP syntax errors
- [ ] No relationship errors
- [ ] No casting errors
- [ ] No mass assignment errors

---

## Common Issues & Solutions

### Issue 1: Mass Assignment Exception
**Error:** `Add [field_name] to fillable property to allow mass assignment`

**Solution:** Add the field to the model's `$fillable` array

### Issue 2: Relationship Not Found
**Error:** `Call to undefined relationship [relationship_name]`

**Solution:** Ensure the relationship method exists and is public

### Issue 3: Cast Type Mismatch
**Error:** `Cannot cast value to type [type]`

**Solution:** Check database column type matches cast type

### Issue 4: Foreign Key Constraint
**Error:** `Cannot add or update a child row: a foreign key constraint fails`

**Solution:** Ensure the foreign key ID exists in the parent table

---

## Next Phase Preview

**Phase 3: Filament Admin Resources**
- Create TourDeparture resource
- Update Tour resource with departure management
- Update Booking resource with payment tracking
- Create Payment resource (view-only)
- Create booking workflow actions

**Estimated Time:** 2-3 days

---

**Phase 2 Status:** Ready for Implementation
**Dependencies:** Phase 1 ✅
**Next:** Begin Step 1 - Create TourDeparture Model
