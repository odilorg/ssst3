# BOOKING SYSTEM WITH OCTO PAYMENT - DETAILED IMPLEMENTATION PLAN

**Project:** Jahongir Travel - Tour Booking System
**Branch:** `feature/tour-details-booking-form`
**Payment Gateway:** OCTO (Octobank)
**Estimated Timeline:** 3-4 weeks
**Status:** Planning Phase Complete - Ready for Implementation

---

## Executive Summary

This document provides a step-by-step implementation plan for the complete tour booking system with OCTO payment integration, based on the finalized business requirements:

- ✅ Both group tours (fixed departures) and private tours (open dates)
- ✅ Hybrid tours offering both group and private options
- ✅ Fixed pricing (no dynamic pricing)
- ✅ Tours run regardless of participant count (guaranteed departures)
- ✅ Configurable booking windows per tour (8 hours to 14 days)
- ✅ Three payment options: Deposit (30%), Full Payment (100%), Request to Book
- ✅ OCTO payment gateway integration (hosted page approach)
- ✅ Manual supplier assignment
- ✅ Per-tour cancellation policies

---

## Table of Contents

1. [Phase 1: Database Architecture](#phase-1-database-architecture)
2. [Phase 2: Models & Relationships](#phase-2-models--relationships)
3. [Phase 3: OCTO Payment Service](#phase-3-octo-payment-service)
4. [Phase 4: Booking Controllers](#phase-4-booking-controllers)
5. [Phase 5: Frontend Integration](#phase-5-frontend-integration)
6. [Phase 6: Admin Panel (Filament)](#phase-6-admin-panel-filament)
7. [Phase 7: Testing & Deployment](#phase-7-testing--deployment)

---

## Phase 1: Database Architecture

**Duration:** 2-3 days
**Objective:** Create all necessary database tables and relationships

### 1.1 Tour Departures Table

**File:** `database/migrations/YYYY_MM_DD_create_tour_departures_table.php`

**Purpose:** Store fixed departure dates for group tours

**Fields:**
```php
Schema::create('tour_departures', function (Blueprint $table) {
    $table->id();
    $table->foreignId('tour_id')->constrained()->onDelete('cascade');

    // Departure dates
    $table->date('start_date');
    $table->date('end_date');

    // Capacity management
    $table->integer('max_pax')->default(12); // Maximum participants
    $table->integer('booked_pax')->default(0); // Current bookings
    $table->integer('min_pax')->nullable(); // Minimum to guarantee (nullable = no minimum)

    // Pricing
    $table->decimal('price_per_person', 10, 2)->nullable(); // Override tour base price if needed

    // Status
    $table->enum('status', [
        'open',        // Accepting bookings
        'guaranteed',  // Min participants reached, tour confirmed
        'full',        // Max capacity reached
        'completed',   // Tour finished
        'cancelled'    // Departure cancelled
    ])->default('open');

    // Departure type
    $table->enum('departure_type', ['group', 'private'])->default('group');

    // Admin notes
    $table->text('notes')->nullable();

    $table->timestamps();

    // Indexes
    $table->index(['tour_id', 'start_date']);
    $table->index('status');
});
```

**Business Logic:**
- Group tours: Admin creates specific departure dates
- Private tours: Bookings create departures on-demand
- Hybrid tours: Both scenarios supported

---

### 1.2 Update Tours Table

**File:** `database/migrations/YYYY_MM_DD_add_booking_fields_to_tours_table.php`

**New Fields:**
```php
Schema::table('tours', function (Blueprint $table) {
    // Tour type configuration
    $table->enum('tour_type', ['group_only', 'private_only', 'hybrid'])
        ->default('group_only')
        ->after('is_active');

    // Pricing
    $table->decimal('group_price_per_person', 10, 2)->nullable()->after('tour_type');
    $table->decimal('private_price_per_person', 10, 2)->nullable()->after('group_price_per_person');
    $table->decimal('private_minimum_charge', 10, 2)->nullable()->after('private_price_per_person');

    // Booking window (in hours)
    $table->integer('booking_window_hours')->default(72)->after('private_minimum_charge');

    // Payment policy
    $table->integer('balance_due_days')->default(3)->after('booking_window_hours');
    $table->boolean('allow_last_minute_full_payment')->default(true)->after('balance_due_days');

    // Traveler details requirement
    $table->boolean('requires_traveler_details')->default(false)->after('allow_last_minute_full_payment');

    // Cancellation policy (JSON)
    $table->json('cancellation_policy')->nullable()->after('requires_traveler_details');
    // Example: {"full_refund_days": 14, "partial_refund_days": 7, "partial_refund_percentage": 50, "no_refund_days": 3}
});
```

**Migration Strategy:**
- Use `after()` to maintain logical column ordering
- Set sensible defaults for existing tours
- Nullable for optional fields (private pricing, cancellation policy)

---

### 1.3 Update Bookings Table

**File:** `database/migrations/YYYY_MM_DD_add_payment_fields_to_bookings_table.php`

**New Fields:**
```php
Schema::table('bookings', function (Blueprint $table) {
    // Link to departure (nullable for private tours)
    $table->foreignId('departure_id')->nullable()->after('tour_id')->constrained('tour_departures')->onDelete('set null');

    // Booking type
    $table->enum('booking_type', ['group', 'private'])->default('group')->after('departure_id');

    // Payment integration
    $table->string('payment_uuid')->nullable()->after('reference')->index();
    $table->enum('payment_method', ['deposit', 'full', 'request'])->nullable()->after('payment_uuid');
    $table->enum('payment_status', [
        'pending',
        'deposit_paid',
        'paid_in_full',
        'failed',
        'refunded',
        'partially_refunded',
        'awaiting_confirmation'
    ])->default('pending')->after('payment_method');

    // Financial tracking
    $table->decimal('amount_paid', 12, 2)->default(0)->after('total_price');
    $table->decimal('amount_remaining', 12, 2)->default(0)->after('amount_paid');
    $table->decimal('discount_applied', 12, 2)->default(0)->after('amount_remaining');
    $table->date('balance_due_date')->nullable()->after('discount_applied');

    // Customer information (for guest checkouts)
    $table->string('customer_name')->nullable()->after('customer_id');
    $table->string('customer_email')->nullable()->after('customer_name');
    $table->string('customer_phone')->nullable()->after('customer_email');
    $table->string('customer_country')->nullable()->after('customer_phone');

    // Special requests
    $table->text('special_requests')->nullable()->after('notes');
    $table->text('inquiry_notes')->nullable()->after('special_requests');

    // Terms agreement
    $table->timestamp('terms_agreed_at')->nullable()->after('inquiry_notes');

    // Update existing status enum
    $table->enum('status', [
        'draft',
        'inquiry',
        'pending_payment',
        'confirmed',
        'in_progress',
        'completed',
        'cancelled',
        'declined'
    ])->default('draft')->change();
});
```

**Key Points:**
- `departure_id` is nullable (private tours don't have departures)
- `customer_id` can be nullable for guest bookings
- `payment_uuid` stores OCTO payment UUID for tracking
- Status includes `inquiry` and `declined` for Request to Book workflow

---

### 1.4 Create Payments Table

**File:** `database/migrations/YYYY_MM_DD_create_payments_table.php`

**Purpose:** Audit log for all payment transactions

**Fields:**
```php
Schema::create('payments', function (Blueprint $table) {
    $table->id();
    $table->foreignId('booking_id')->constrained()->onDelete('cascade');

    // Amount (negative for refunds)
    $table->decimal('amount', 12, 2);

    // Payment method
    $table->string('payment_method'); // 'octo_uzcard', 'octo_humo', 'octo_visa', etc.

    // Status
    $table->enum('status', ['pending', 'completed', 'failed', 'refunded'])->default('pending');

    // Payment type
    $table->enum('payment_type', ['deposit', 'balance', 'full', 'refund'])->nullable();

    // Gateway details
    $table->string('transaction_id')->nullable(); // OCTO payment UUID
    $table->json('gateway_response')->nullable(); // Full webhook payload

    // Timestamps
    $table->timestamp('processed_at')->nullable();
    $table->timestamps();

    // Indexes
    $table->index(['booking_id', 'status']);
    $table->index('transaction_id');
});
```

**Usage:**
- Every payment creates a record (deposits, full payments, refunds)
- Negative amounts for refunds
- `gateway_response` stores full webhook for debugging

---

### 1.5 Create Booking Travelers Table (Optional)

**File:** `database/migrations/YYYY_MM_DD_create_booking_travelers_table.php`

**Purpose:** Store individual traveler details (when required by tour)

**Fields:**
```php
Schema::create('booking_travelers', function (Blueprint $table) {
    $table->id();
    $table->foreignId('booking_id')->constrained()->onDelete('cascade');

    // Personal information
    $table->string('full_name');
    $table->date('date_of_birth')->nullable();
    $table->string('nationality')->nullable();

    // Passport details (for international tours)
    $table->string('passport_number')->nullable();
    $table->date('passport_expiry')->nullable();

    // Special requirements
    $table->text('dietary_requirements')->nullable();
    $table->text('special_needs')->nullable();

    $table->timestamps();

    // Index
    $table->index('booking_id');
});
```

**When to Collect:**
- Only if `tour.requires_traveler_details = true`
- Can be collected after payment (in confirmation email)
- Or collected during booking (slows conversion)

---

### 1.6 Migration Execution Order

**Run migrations in this order:**

```bash
# 1. Create tour_departures table
php artisan make:migration create_tour_departures_table

# 2. Update tours table
php artisan make:migration add_booking_fields_to_tours_table

# 3. Update bookings table
php artisan make:migration add_payment_fields_to_bookings_table

# 4. Create payments table
php artisan make:migration create_payments_table

# 5. Create booking_travelers table
php artisan make:migration create_booking_travelers_table

# 6. Run all migrations
php artisan migrate
```

**Rollback Safety:**
```bash
# Test rollback before committing
php artisan migrate:rollback --step=5
php artisan migrate
```

---

## Phase 2: Models & Relationships

**Duration:** 1-2 days
**Objective:** Update Eloquent models with new fields and relationships

### 2.1 Update Tour Model

**File:** `app/Models/Tour.php`

**Add to `$fillable`:**
```php
protected $fillable = [
    // ... existing fields
    'tour_type',
    'group_price_per_person',
    'private_price_per_person',
    'private_minimum_charge',
    'booking_window_hours',
    'balance_due_days',
    'allow_last_minute_full_payment',
    'requires_traveler_details',
    'cancellation_policy',
];
```

**Add casts:**
```php
protected $casts = [
    // ... existing casts
    'is_active' => 'boolean',
    'group_price_per_person' => 'decimal:2',
    'private_price_per_person' => 'decimal:2',
    'private_minimum_charge' => 'decimal:2',
    'allow_last_minute_full_payment' => 'boolean',
    'requires_traveler_details' => 'boolean',
    'cancellation_policy' => 'array',
];
```

**Add relationships:**
```php
/**
 * Tour has many departures
 */
public function departures()
{
    return $this->hasMany(TourDeparture::class);
}

/**
 * Get upcoming departures
 */
public function upcomingDepartures()
{
    return $this->departures()
        ->where('start_date', '>=', now())
        ->where('status', '!=', 'cancelled')
        ->orderBy('start_date');
}

/**
 * Get available departures (not full)
 */
public function availableDepartures()
{
    return $this->upcomingDepartures()
        ->whereColumn('booked_pax', '<', 'max_pax');
}
```

**Add accessor methods:**
```php
/**
 * Check if tour allows group bookings
 */
public function allowsGroupBookings(): bool
{
    return in_array($this->tour_type, ['group_only', 'hybrid']);
}

/**
 * Check if tour allows private bookings
 */
public function allowsPrivateBookings(): bool
{
    return in_array($this->tour_type, ['private_only', 'hybrid']);
}

/**
 * Get price for booking type
 */
public function getPriceForType(string $bookingType): float
{
    return $bookingType === 'group'
        ? $this->group_price_per_person
        : $this->private_price_per_person;
}

/**
 * Calculate minimum booking time
 */
public function getMinimumBookingTime(): \Carbon\Carbon
{
    return now()->addHours($this->booking_window_hours);
}

/**
 * Check if date is within booking window
 */
public function isWithinBookingWindow(\Carbon\Carbon $date): bool
{
    return $date->greaterThan($this->getMinimumBookingTime());
}
```

---

### 2.2 Create TourDeparture Model

**File:** `app/Models/TourDeparture.php`

**Full Model:**
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

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
        'price_per_person' => 'decimal:2',
    ];

    /**
     * Belongs to tour
     */
    public function tour()
    {
        return $this->belongsTo(Tour::class);
    }

    /**
     * Has many bookings
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class, 'departure_id');
    }

    /**
     * Get available spots
     */
    public function getAvailableSpotsAttribute(): int
    {
        return max(0, $this->max_pax - $this->booked_pax);
    }

    /**
     * Check if departure is full
     */
    public function isFull(): bool
    {
        return $this->booked_pax >= $this->max_pax;
    }

    /**
     * Check if minimum participants reached
     */
    public function isGuaranteed(): bool
    {
        if ($this->min_pax === null) {
            return true; // No minimum = always guaranteed
        }

        return $this->booked_pax >= $this->min_pax;
    }

    /**
     * Check if booking is possible for given pax
     */
    public function canAccommodate(int $pax): bool
    {
        return ($this->booked_pax + $pax) <= $this->max_pax;
    }

    /**
     * Get effective price (override or tour base price)
     */
    public function getEffectivePrice(): float
    {
        return $this->price_per_person ?? $this->tour->group_price_per_person;
    }

    /**
     * Reserve spots (increment booked_pax)
     */
    public function reserveSpots(int $pax): void
    {
        $this->increment('booked_pax', $pax);

        // Auto-update status
        if ($this->isFull()) {
            $this->update(['status' => 'full']);
        } elseif ($this->isGuaranteed() && $this->status === 'open') {
            $this->update(['status' => 'guaranteed']);
        }
    }

    /**
     * Release spots (decrement booked_pax)
     */
    public function releaseSpots(int $pax): void
    {
        $this->decrement('booked_pax', $pax);

        // Revert status if needed
        if ($this->status === 'full' && !$this->isFull()) {
            $this->update(['status' => $this->isGuaranteed() ? 'guaranteed' : 'open']);
        }
    }

    /**
     * Scope: Upcoming departures
     */
    public function scopeUpcoming($query)
    {
        return $query->where('start_date', '>=', now());
    }

    /**
     * Scope: Available departures (not full, not cancelled)
     */
    public function scopeAvailable($query)
    {
        return $query->upcoming()
            ->whereColumn('booked_pax', '<', 'max_pax')
            ->where('status', '!=', 'cancelled');
    }
}
```

---

### 2.3 Update Booking Model

**File:** `app/Models/Booking.php`

**Add to `$fillable`:**
```php
protected $fillable = [
    // ... existing fields
    'departure_id',
    'booking_type',
    'payment_uuid',
    'payment_method',
    'payment_status',
    'amount_paid',
    'amount_remaining',
    'discount_applied',
    'balance_due_date',
    'customer_name',
    'customer_email',
    'customer_phone',
    'customer_country',
    'special_requests',
    'inquiry_notes',
    'terms_agreed_at',
];
```

**Add casts:**
```php
protected $casts = [
    // ... existing casts
    'start_date' => 'date',
    'end_date' => 'date',
    'total_price' => 'decimal:2',
    'amount_paid' => 'decimal:2',
    'amount_remaining' => 'decimal:2',
    'discount_applied' => 'decimal:2',
    'balance_due_date' => 'date',
    'terms_agreed_at' => 'datetime',
];
```

**Add relationships:**
```php
/**
 * Belongs to departure (nullable for private bookings)
 */
public function departure()
{
    return $this->belongsTo(TourDeparture::class);
}

/**
 * Has many payments
 */
public function payments()
{
    return $this->hasMany(Payment::class);
}

/**
 * Has many travelers
 */
public function travelers()
{
    return $this->hasMany(BookingTraveler::class);
}
```

**Add business logic methods:**
```php
/**
 * Calculate deposit amount (30%)
 */
public function getDepositAmount(): float
{
    return $this->total_price * 0.30;
}

/**
 * Calculate full payment amount (with 10% discount)
 */
public function getFullPaymentAmount(): float
{
    return $this->total_price * 0.90;
}

/**
 * Calculate discount for full payment
 */
public function getFullPaymentDiscount(): float
{
    return $this->total_price * 0.10;
}

/**
 * Check if booking is within last-minute window
 */
public function isLastMinuteBooking(): bool
{
    $minimumTime = now()->addHours($this->tour->booking_window_hours);
    return $this->start_date->lessThan($minimumTime);
}

/**
 * Get allowed payment methods based on booking timing
 */
public function getAllowedPaymentMethods(): array
{
    if ($this->isLastMinuteBooking() && $this->tour->allow_last_minute_full_payment) {
        // Last minute: only full payment or request
        return ['full', 'request'];
    }

    // Normal: all methods
    return ['deposit', 'full', 'request'];
}

/**
 * Calculate balance due date
 */
public function calculateBalanceDueDate(): Carbon
{
    return $this->start_date->copy()->subDays($this->tour->balance_due_days);
}

/**
 * Process payment method selection
 */
public function processPaymentMethod(string $method): void
{
    $this->payment_method = $method;

    match($method) {
        'deposit' => $this->processDepositPayment(),
        'full' => $this->processFullPayment(),
        'request' => $this->processRequestToBook(),
    };

    $this->save();
}

private function processDepositPayment(): void
{
    $this->amount_remaining = $this->getDepositAmount();
    $this->discount_applied = 0;
    $this->balance_due_date = $this->calculateBalanceDueDate();
    $this->status = 'pending_payment';
}

private function processFullPayment(): void
{
    $this->amount_remaining = $this->getFullPaymentAmount();
    $this->discount_applied = $this->getFullPaymentDiscount();
    $this->balance_due_date = null;
    $this->status = 'pending_payment';
}

private function processRequestToBook(): void
{
    $this->amount_remaining = 0;
    $this->discount_applied = 0;
    $this->balance_due_date = null;
    $this->status = 'inquiry';
}

/**
 * Check if balance is due soon (within 7 days)
 */
public function isBalanceDueSoon(): bool
{
    if (!$this->balance_due_date) {
        return false;
    }

    return $this->balance_due_date->diffInDays(now()) <= 7;
}

/**
 * Check if balance is overdue
 */
public function isBalanceOverdue(): bool
{
    if (!$this->balance_due_date) {
        return false;
    }

    return $this->balance_due_date->isPast() && $this->amount_remaining > 0;
}
```

---

### 2.4 Create Payment Model

**File:** `app/Models/Payment.php`

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

    /**
     * Belongs to booking
     */
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Check if payment is refund
     */
    public function isRefund(): bool
    {
        return $this->amount < 0;
    }

    /**
     * Scope: Only completed payments
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope: Only refunds
     */
    public function scopeRefunds($query)
    {
        return $query->where('amount', '<', 0);
    }
}
```

---

### 2.5 Create BookingTraveler Model

**File:** `app/Models/BookingTraveler.php`

```php
<?php

namespace App\Models;

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

    /**
     * Belongs to booking
     */
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
```

---

## Phase 3: OCTO Payment Service

**Duration:** 2 days
**Objective:** Create payment service and configuration

### 3.1 Environment Configuration

**File:** `.env`

**Add:**
```env
# OCTO Payment Gateway
OCTO_SHOP_ID=your_shop_id_here
OCTO_SECRET=your_secret_key_here
OCTO_UNIQUE_KEY=your_unique_signature_key_here
OCTO_API_URL=https://secure.octo.uz
OCTO_TEST_MODE=true
```

**File:** `config/services.php`

**Add:**
```php
'octo' => [
    'shop_id' => env('OCTO_SHOP_ID'),
    'secret' => env('OCTO_SECRET'),
    'unique_key' => env('OCTO_UNIQUE_KEY'),
    'api_url' => env('OCTO_API_URL', 'https://secure.octo.uz'),
    'test_mode' => env('OCTO_TEST_MODE', false),
],
```

---

### 3.2 Create OctoPaymentService

**Command:**
```bash
php artisan make:class Services/OctoPaymentService
```

**File:** `app/Services/OctoPaymentService.php`

**Full Implementation:**
```php
<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OctoPaymentService
{
    private string $apiUrl;
    private int $shopId;
    private string $secret;
    private string $uniqueKey;
    private bool $testMode;

    public function __construct()
    {
        $this->apiUrl = config('services.octo.api_url');
        $this->shopId = config('services.octo.shop_id');
        $this->secret = config('services.octo.secret');
        $this->uniqueKey = config('services.octo.unique_key');
        $this->testMode = config('services.octo.test_mode');
    }

    /**
     * Prepare payment and get payment URL
     *
     * @param array $params
     * @return array ['octo_payment_UUID', 'status', 'octo_pay_url', 'total_sum']
     * @throws \Exception
     */
    public function preparePayment(array $params): array
    {
        $payload = array_merge([
            'octo_shop_id' => $this->shopId,
            'octo_secret' => $this->secret,
            'auto_capture' => true, // One-stage payment
            'test' => $this->testMode,
            'language' => 'en',
            'ttl' => 60, // 1 hour payment window
        ], $params);

        Log::info('OCTO: Preparing payment', [
            'shop_transaction_id' => $params['shop_transaction_id'] ?? null,
            'amount' => $params['total_sum'] ?? null,
        ]);

        $response = Http::post("{$this->apiUrl}/prepare_payment", $payload);

        $result = $response->json();

        if ($result['error'] !== 0) {
            Log::error('OCTO: prepare_payment failed', [
                'error' => $result['error'],
                'message' => $result['errMessage'] ?? 'Unknown error',
                'payload' => $payload,
            ]);

            throw new \Exception($result['errMessage'] ?? 'Payment preparation failed');
        }

        Log::info('OCTO: Payment prepared successfully', [
            'payment_uuid' => $result['data']['octo_payment_UUID'],
        ]);

        return $result['data'];
    }

    /**
     * Set accept (confirm or cancel two-stage payment)
     * Note: Not used for one-stage, but included for completeness
     *
     * @param string $paymentUuid
     * @param string $acceptStatus 'capture' or 'cancel'
     * @param float|null $finalAmount
     * @return array
     * @throws \Exception
     */
    public function setAccept(string $paymentUuid, string $acceptStatus, ?float $finalAmount = null): array
    {
        $payload = [
            'octo_shop_id' => $this->shopId,
            'octo_secret' => $this->secret,
            'octo_payment_UUID' => $paymentUuid,
            'accept_status' => $acceptStatus,
        ];

        if ($finalAmount !== null) {
            $payload['final_amount'] = $finalAmount;
        }

        Log::info('OCTO: Setting accept', [
            'payment_uuid' => $paymentUuid,
            'status' => $acceptStatus,
        ]);

        $response = Http::post("{$this->apiUrl}/set_accept", $payload);
        $result = $response->json();

        if ($result['error'] !== 0) {
            Log::error('OCTO: set_accept failed', $result);
            throw new \Exception($result['errMessage'] ?? 'Accept/Cancel failed');
        }

        return $result['data'];
    }

    /**
     * Refund payment (full or partial)
     *
     * @param string $paymentUuid
     * @param float $amount
     * @param string $refundId Unique refund ID
     * @return array
     * @throws \Exception
     */
    public function refund(string $paymentUuid, float $amount, string $refundId): array
    {
        $payload = [
            'octo_shop_id' => $this->shopId,
            'octo_secret' => $this->secret,
            'octo_payment_UUID' => $paymentUuid,
            'shop_refund_id' => $refundId,
            'amount' => $amount,
        ];

        Log::info('OCTO: Processing refund', [
            'payment_uuid' => $paymentUuid,
            'amount' => $amount,
            'refund_id' => $refundId,
        ]);

        $response = Http::post("{$this->apiUrl}/refund", $payload);
        $result = $response->json();

        if ($result['error'] !== 0) {
            Log::error('OCTO: refund failed', $result);
            throw new \Exception($result['errMessage'] ?? 'Refund failed');
        }

        Log::info('OCTO: Refund processed successfully', $result['data']);

        return $result['data'];
    }

    /**
     * Check payment status
     *
     * @param string $shopTransactionId
     * @return array
     * @throws \Exception
     */
    public function checkStatus(string $shopTransactionId): array
    {
        $payload = [
            'octo_shop_id' => $this->shopId,
            'octo_secret' => $this->secret,
            'shop_transaction_id' => $shopTransactionId,
        ];

        $response = Http::post("{$this->apiUrl}/prepare_payment", $payload);
        $result = $response->json();

        if ($result['error'] !== 0) {
            Log::error('OCTO: check_status failed', $result);
            throw new \Exception($result['errMessage'] ?? 'Status check failed');
        }

        return $result['data'];
    }

    /**
     * Verify webhook signature
     *
     * @param string $uuid
     * @param string $status
     * @param string $receivedSignature
     * @return bool
     */
    public function verifySignature(string $uuid, string $status, string $receivedSignature): bool
    {
        $calculatedSignature = sha1($this->uniqueKey . $uuid . $status);

        $isValid = hash_equals($calculatedSignature, $receivedSignature);

        if (!$isValid) {
            Log::warning('OCTO: Signature verification failed', [
                'uuid' => $uuid,
                'status' => $status,
                'received' => $receivedSignature,
                'calculated' => $calculatedSignature,
            ]);
        }

        return $isValid;
    }
}
```

**Key Features:**
- Comprehensive logging for debugging
- Exception handling with meaningful messages
- Test mode support
- Signature verification for webhooks

---

## Phase 4: Booking Controllers

**Duration:** 3-4 days
**Objective:** Create controllers for booking flow and webhooks

### 4.1 Update BookingController

**File:** `app/Http/Controllers/Partials/BookingController.php`

**Replace placeholder `store()` method:**

```php
<?php

namespace App\Http\Controllers\Partials;

use App\Http\Controllers\Controller;
use App\Models\Tour;
use App\Models\Booking;
use App\Models\TourDeparture;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    /**
     * Show booking form for tour
     */
    public function form(string $tourSlug)
    {
        $tour = Tour::where('slug', $tourSlug)
            ->where('is_active', true)
            ->with(['activeExtras', 'availableDepartures'])
            ->firstOrFail();

        return view('partials.bookings.form', compact('tour'));
    }

    /**
     * Store new booking
     */
    public function store(Request $request)
    {
        // Validation
        $validated = $request->validate([
            'tour_id' => 'required|exists:tours,id',
            'booking_type' => 'required|in:group,private',
            'departure_id' => 'required_if:booking_type,group|exists:tour_departures,id',
            'start_date' => 'required|date|after:today',
            'end_date' => 'required|date|after:start_date',
            'pax_total' => 'required|integer|min:1|max:50',
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email',
            'customer_phone' => 'required|string|max:20',
            'customer_country' => 'nullable|string|max:100',
            'special_requests' => 'nullable|string|max:1000',
            'payment_method' => 'required|in:deposit,full,request',
            'terms_agreed' => 'required|accepted',
        ]);

        $tour = Tour::findOrFail($validated['tour_id']);

        // Check booking type is allowed for this tour
        if ($validated['booking_type'] === 'group' && !$tour->allowsGroupBookings()) {
            return back()->withErrors(['booking_type' => 'Group bookings not available for this tour']);
        }

        if ($validated['booking_type'] === 'private' && !$tour->allowsPrivateBookings()) {
            return back()->withErrors(['booking_type' => 'Private bookings not available for this tour']);
        }

        // For group bookings, check departure capacity
        if ($validated['booking_type'] === 'group') {
            $departure = TourDeparture::findOrFail($validated['departure_id']);

            if (!$departure->canAccommodate($validated['pax_total'])) {
                return back()->withErrors([
                    'pax_total' => 'Not enough spots available. Only ' . $departure->available_spots . ' spots left.'
                ]);
            }
        }

        // Check if within booking window
        $startDate = \Carbon\Carbon::parse($validated['start_date']);
        if (!$tour->isWithinBookingWindow($startDate)) {
            return back()->withErrors([
                'start_date' => "Must book at least {$tour->booking_window_hours} hours in advance"
            ]);
        }

        // Calculate price
        $pricePerPerson = $validated['booking_type'] === 'group'
            ? ($departure->price_per_person ?? $tour->group_price_per_person)
            : $tour->private_price_per_person;

        $totalPrice = $pricePerPerson * $validated['pax_total'];

        // For private tours, apply minimum charge if needed
        if ($validated['booking_type'] === 'private' && $tour->private_minimum_charge) {
            $totalPrice = max($totalPrice, $tour->private_minimum_charge);
        }

        // Create booking
        $booking = Booking::create([
            'reference' => Booking::generateReference(),
            'tour_id' => $tour->id,
            'departure_id' => $validated['departure_id'] ?? null,
            'booking_type' => $validated['booking_type'],
            'customer_id' => auth()->id(), // Nullable for guest checkouts
            'customer_name' => $validated['customer_name'],
            'customer_email' => $validated['customer_email'],
            'customer_phone' => $validated['customer_phone'],
            'customer_country' => $validated['customer_country'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'pax_total' => $validated['pax_total'],
            'currency' => 'UZS',
            'total_price' => $totalPrice,
            'special_requests' => $validated['special_requests'],
            'terms_agreed_at' => now(),
            'status' => 'draft', // Will be updated by payment method
        ]);

        // Process payment method
        $booking->processPaymentMethod($validated['payment_method']);

        // For group bookings, reserve spots
        if ($validated['booking_type'] === 'group') {
            $departure->reserveSpots($validated['pax_total']);
        }

        // Redirect based on payment method
        if ($validated['payment_method'] === 'request') {
            // Request to book - send to confirmation page
            return redirect()->route('bookings.show', $booking)
                ->with('success', 'Your booking request has been submitted. We\'ll contact you within 24 hours.');
        }

        // Deposit or full payment - redirect to payment
        return redirect()->route('bookings.payment.initiate', $booking);
    }
}
```

---

### 4.2 Create BookingPaymentController

**Command:**
```bash
php artisan make:controller BookingPaymentController
```

**File:** `app/Http/Controllers/BookingPaymentController.php`

```php
<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Services\OctoPaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BookingPaymentController extends Controller
{
    public function __construct(
        private OctoPaymentService $octoService
    ) {}

    /**
     * Initiate payment for booking
     */
    public function initiatePayment(Booking $booking)
    {
        // Check if booking is in correct status
        if ($booking->status !== 'pending_payment') {
            return redirect()
                ->route('bookings.show', $booking)
                ->with('error', 'This booking is not awaiting payment.');
        }

        try {
            // Prepare payment with OCTO
            $paymentData = $this->octoService->preparePayment([
                'shop_transaction_id' => $booking->reference,
                'total_sum' => $booking->amount_remaining,
                'currency' => $booking->currency,
                'description' => $this->getPaymentDescription($booking),
                'language' => 'en',
                'ttl' => 60, // 1 hour
                'return_url' => route('bookings.payment.return', $booking),
                'notify_url' => route('webhooks.octo'),
                'user_data' => [
                    'email' => $booking->customer_email,
                    'phone' => $booking->customer_phone,
                    'user_id' => $booking->customer_id ?? 'guest',
                ],
                'basket' => [
                    [
                        'name' => $booking->tour->name,
                        'price' => $booking->amount_remaining,
                        'count' => 1,
                    ]
                ],
            ]);

            // Store payment UUID in booking
            $booking->update([
                'payment_uuid' => $paymentData['octo_payment_UUID'],
            ]);

            // Redirect to OCTO payment page
            return redirect($paymentData['octo_pay_url']);

        } catch (\Exception $e) {
            \Log::error('Payment initiation failed', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Payment initialization failed. Please try again or contact support.');
        }
    }

    /**
     * Handle return from OCTO payment page
     */
    public function paymentReturn(Booking $booking)
    {
        try {
            // Check payment status with OCTO
            $status = $this->octoService->checkStatus($booking->reference);

            if ($status['status'] === 'succeeded') {
                return redirect()
                    ->route('bookings.show', $booking)
                    ->with('success', 'Payment successful! Your booking is confirmed.');
            }

            if ($status['status'] === 'canceled') {
                return redirect()
                    ->route('bookings.show', $booking)
                    ->with('error', 'Payment was cancelled. Please try again.');
            }

            // Payment still pending
            return redirect()
                ->route('bookings.show', $booking)
                ->with('warning', 'Payment is being processed. We will notify you once confirmed.');

        } catch (\Exception $e) {
            \Log::error('Payment status check failed', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage(),
            ]);

            return redirect()
                ->route('bookings.show', $booking)
                ->with('info', 'We are verifying your payment. You will receive an email confirmation shortly.');
        }
    }

    /**
     * Handle refund request (admin only)
     */
    public function refund(Request $request, Booking $booking)
    {
        // Check authorization (add your admin check here)
        // if (!auth()->user()->isAdmin()) abort(403);

        $request->validate([
            'amount' => [
                'required',
                'numeric',
                'min:1',
                'max:' . $booking->amount_paid,
            ],
            'reason' => 'required|string|max:500',
        ]);

        try {
            $refundData = $this->octoService->refund(
                paymentUuid: $booking->payment_uuid,
                amount: $request->amount,
                refundId: 'REFUND-' . $booking->reference . '-' . Str::random(8)
            );

            // Log refund in payments table
            $booking->payments()->create([
                'amount' => -$request->amount, // Negative for refund
                'payment_method' => 'octo_refund',
                'status' => 'completed',
                'payment_type' => 'refund',
                'transaction_id' => $refundData['refund_id'],
                'gateway_response' => $refundData,
                'processed_at' => now(),
            ]);

            // Update booking amounts
            $booking->decrement('amount_paid', $request->amount);

            // Update payment status
            if ($booking->amount_paid == 0) {
                $booking->update(['payment_status' => 'refunded']);
            } else {
                $booking->update(['payment_status' => 'partially_refunded']);
            }

            // Add note
            $booking->update([
                'notes' => ($booking->notes ?? '') . "\n\nRefund of {$request->amount} {$booking->currency} processed: {$request->reason}",
            ]);

            return back()->with('success', 'Refund of ' . $request->amount . ' ' . $booking->currency . ' processed successfully.');

        } catch (\Exception $e) {
            \Log::error('Refund failed', [
                'booking_id' => $booking->id,
                'amount' => $request->amount,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Refund failed: ' . $e->getMessage());
        }
    }

    /**
     * Generate payment description
     */
    private function getPaymentDescription(Booking $booking): string
    {
        $type = match($booking->payment_method) {
            'deposit' => 'Deposit (30%)',
            'full' => 'Full Payment',
            default => 'Payment',
        };

        return "{$type} for {$booking->tour->name} - {$booking->pax_total} travelers";
    }
}
```

---

### 4.3 Create OctoWebhookController

**Command:**
```bash
php artisan make:controller OctoWebhookController
```

**File:** `app/Http/Controllers/OctoWebhookController.php`

```php
<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Services\OctoPaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class OctoWebhookController extends Controller
{
    public function __construct(
        private OctoPaymentService $octoService
    ) {}

    /**
     * Handle OCTO webhook notification
     */
    public function handle(Request $request)
    {
        // Log all webhooks for debugging
        Log::info('OCTO Webhook Received', $request->all());

        // Verify signature
        $uuid = $request->input('octo_payment_UUID');
        $status = $request->input('status');
        $signature = $request->input('signature');

        if (!$this->octoService->verifySignature($uuid, $status, $signature)) {
            Log::warning('OCTO Webhook: Invalid signature', [
                'uuid' => $uuid,
                'status' => $status,
                'received_signature' => $signature,
            ]);
            return response('Invalid signature', 403);
        }

        // Find booking by payment UUID
        $booking = Booking::where('payment_uuid', $uuid)->first();

        if (!$booking) {
            Log::warning('OCTO Webhook: Booking not found', [
                'uuid' => $uuid,
                'shop_transaction_id' => $request->input('shop_transaction_id'),
            ]);
            return response('Booking not found', 404);
        }

        // Handle different statuses
        try {
            match($status) {
                'succeeded' => $this->handleSuccess($booking, $request),
                'canceled' => $this->handleCancellation($booking, $request),
                'waiting_for_capture' => $this->handleWaitingCapture($booking, $request),
                default => Log::info('OCTO Webhook: Unhandled status', ['status' => $status])
            };
        } catch (\Exception $e) {
            Log::error('OCTO Webhook: Processing failed', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Still return 200 to prevent retries for permanent errors
            return response('Processing failed: ' . $e->getMessage(), 200);
        }

        // MUST return 200 OK for OCTO to stop retrying
        return response('OK', 200);
    }

    /**
     * Handle successful payment
     */
    private function handleSuccess(Booking $booking, Request $request)
    {
        Log::info('OCTO Webhook: Processing successful payment', [
            'booking_id' => $booking->id,
            'reference' => $booking->reference,
        ]);

        // Determine payment status based on payment method
        $paymentStatus = match($booking->payment_method) {
            'deposit' => 'deposit_paid',
            'full' => 'paid_in_full',
            default => 'paid_in_full',
        };

        // Update booking
        $booking->update([
            'status' => 'confirmed',
            'payment_status' => $paymentStatus,
            'amount_paid' => $request->input('transfer_sum'), // Amount after commission
            'amount_remaining' => $paymentStatus === 'deposit_paid'
                ? ($booking->total_price - $booking->getDepositAmount())
                : 0,
        ]);

        // Create payment record
        $booking->payments()->create([
            'amount' => $request->input('transfer_sum'),
            'payment_method' => 'octo_' . strtolower($request->input('card_type', 'unknown')),
            'status' => 'completed',
            'payment_type' => $booking->payment_method,
            'transaction_id' => $request->input('octo_payment_UUID'),
            'gateway_response' => $request->all(),
            'processed_at' => now(),
        ]);

        // Send confirmation email
        // Mail::to($booking->customer_email)->send(new BookingConfirmed($booking));

        // Send admin notification
        // Mail::to(config('mail.admin'))->send(new NewBookingNotification($booking));

        Log::info('OCTO Webhook: Payment processed successfully', [
            'booking_id' => $booking->id,
            'amount' => $request->input('transfer_sum'),
            'payment_status' => $paymentStatus,
        ]);
    }

    /**
     * Handle cancelled payment
     */
    private function handleCancellation(Booking $booking, Request $request)
    {
        Log::info('OCTO Webhook: Processing cancelled payment', [
            'booking_id' => $booking->id,
        ]);

        // Update booking status
        $booking->update([
            'status' => 'cancelled',
            'payment_status' => 'failed',
        ]);

        // Release departure spots if group booking
        if ($booking->departure_id) {
            $booking->departure->releaseSpots($booking->pax_total);
        }

        Log::info('OCTO Webhook: Payment cancellation processed', [
            'booking_id' => $booking->id,
        ]);
    }

    /**
     * Handle waiting for capture (two-stage payment)
     */
    private function handleWaitingCapture(Booking $booking, Request $request)
    {
        Log::info('OCTO Webhook: Payment awaiting confirmation', [
            'booking_id' => $booking->id,
        ]);

        // Update status to awaiting confirmation
        $booking->update([
            'payment_status' => 'awaiting_confirmation',
        ]);

        // Send admin alert to confirm payment
        // Mail::to(config('mail.admin'))->send(new PaymentAwaitingConfirmation($booking));
    }
}
```

---

### 4.4 Add Routes

**File:** `routes/web.php`

**Add payment routes:**

```php
use App\Http\Controllers\BookingPaymentController;
use App\Http\Controllers\OctoWebhookController;

// Booking payment routes
Route::prefix('bookings')->group(function () {
    Route::get('{booking}/payment', [BookingPaymentController::class, 'initiatePayment'])
        ->name('bookings.payment.initiate');

    Route::get('{booking}/payment/return', [BookingPaymentController::class, 'paymentReturn'])
        ->name('bookings.payment.return');

    Route::post('{booking}/refund', [BookingPaymentController::class, 'refund'])
        ->name('bookings.payment.refund')
        ->middleware('auth'); // Add admin middleware in production
});

// OCTO webhook (exclude from CSRF verification)
Route::post('/webhooks/octo', [OctoWebhookController::class, 'handle'])
    ->name('webhooks.octo');
```

---

### 4.5 Exclude Webhook from CSRF

**File:** `app/Http/Middleware/VerifyCsrfToken.php`

**Add to `$except` array:**

```php
protected $except = [
    'webhooks/octo',
];
```

---

## Phase 5: Frontend Integration

**Duration:** 3-4 days
**Objective:** Convert static HTML to Laravel blade and integrate with backend

### 5.1 Create Booking Form Blade View

**File:** `resources/views/partials/bookings/form.blade.php`

**Task:** Convert `public/tour-details.html` booking section to Blade

**Key Changes:**
```blade
{{-- Replace static tour data with dynamic data --}}
<h1>{{ $tour->name }}</h1>
<p class="price">{{ $tour->currency }} {{ number_format($tour->group_price_per_person, 2) }} per person</p>

{{-- Booking form with Laravel form handling --}}
<form method="POST" action="{{ route('bookings.store') }}" id="booking-form">
    @csrf

    <input type="hidden" name="tour_id" value="{{ $tour->id }}">

    {{-- Step 1: Date & Guests --}}
    <div id="step-1-availability">
        {{-- For group tours: show departure calendar --}}
        @if($tour->allowsGroupBookings())
            <label>Select Departure Date</label>
            <select name="departure_id" id="departure-select" required>
                <option value="">Choose a date...</option>
                @foreach($tour->availableDepartures as $departure)
                    <option value="{{ $departure->id }}"
                            data-spots="{{ $departure->available_spots }}"
                            data-price="{{ $departure->getEffectivePrice() }}">
                        {{ $departure->start_date->format('M d, Y') }}
                        - {{ $departure->available_spots }} spots available
                        - {{ $tour->currency }} {{ number_format($departure->getEffectivePrice(), 2) }}/person
                    </option>
                @endforeach
            </select>
        @endif

        {{-- For private tours: open date picker --}}
        @if($tour->allowsPrivateBookings())
            <label>Select Your Dates</label>
            <input type="date" name="start_date"
                   min="{{ now()->addHours($tour->booking_window_hours)->format('Y-m-d') }}"
                   required>
        @endif

        <label>Number of Travelers</label>
        <input type="number" name="pax_total" min="1" max="50" value="2" required>

        <button type="button" id="check-availability-btn">Check Availability</button>
    </div>

    {{-- Step 2: Full Form (hidden initially) --}}
    <div id="step-2-full-form" style="display: none;">

        {{-- Customer Details --}}
        <h3>Your Information</h3>

        <label>Full Name</label>
        <input type="text" name="customer_name" required>

        <label>Email</label>
        <input type="email" name="customer_email" required>

        <label>Phone</label>
        <input type="tel" name="customer_phone" required>

        <label>Country</label>
        <input type="text" name="customer_country">

        <label>Special Requests</label>
        <textarea name="special_requests" rows="3"></textarea>

        {{-- Payment Method Selection --}}
        <h3>Payment Method</h3>

        <div class="payment-cards">
            @if(in_array('deposit', $allowedMethods))
            <div class="payment-card" data-method="deposit">
                <span class="badge badge-orange">30% Deposit</span>
                <h4>Pay Deposit</h4>
                <p class="amount" id="deposit-amount">UZS 0</p>
                <p class="description">Secure your booking now, pay balance 3 days before tour</p>
            </div>
            @endif

            @if(in_array('full', $allowedMethods))
            <div class="payment-card" data-method="full">
                <span class="badge badge-green">Save 10%</span>
                <h4>Pay Full Amount</h4>
                <p class="amount" id="full-amount">UZS 0</p>
                <p class="description">Pay now and save 10% on your tour</p>
            </div>
            @endif

            @if(in_array('request', $allowedMethods))
            <div class="payment-card" data-method="request">
                <span class="badge badge-purple">Free</span>
                <h4>Request to Book</h4>
                <p class="amount">No payment required</p>
                <p class="description">Submit inquiry, we'll confirm availability and send quote</p>
            </div>
            @endif
        </div>

        <input type="hidden" name="payment_method" id="payment-method-input" required>

        {{-- Terms & Conditions --}}
        <label class="checkbox-label">
            <input type="checkbox" name="terms_agreed" value="1" required>
            I agree to the <a href="#">terms and conditions</a>
        </label>

        <button type="submit" class="btn-submit">Complete Booking</button>
    </div>
</form>

@push('scripts')
<script>
// Progressive disclosure logic
document.getElementById('check-availability-btn').addEventListener('click', function() {
    // Validate Step 1
    // Show Step 2
    // Calculate prices based on pax and departure
    document.getElementById('step-2-full-form').style.display = 'block';
    this.style.display = 'none';
});

// Payment card selection
document.querySelectorAll('.payment-card').forEach(card => {
    card.addEventListener('click', function() {
        document.querySelectorAll('.payment-card').forEach(c => c.classList.remove('selected'));
        this.classList.add('selected');
        document.getElementById('payment-method-input').value = this.dataset.method;
    });
});

// Price calculation
function updatePrices() {
    const pax = parseInt(document.querySelector('[name="pax_total"]').value);
    const pricePerPerson = {{ $tour->group_price_per_person }};
    const total = pax * pricePerPerson;

    document.getElementById('deposit-amount').textContent =
        'UZS ' + (total * 0.30).toLocaleString();
    document.getElementById('full-amount').textContent =
        'UZS ' + (total * 0.90).toLocaleString();
}

document.querySelector('[name="pax_total"]').addEventListener('input', updatePrices);
</script>
@endpush
```

**This is a simplified version - we'll refine during implementation**

---

### 5.2 Update tour-details.js

**File:** `public/tour-details.js`

**Modifications needed:**
- Update form submission to use Laravel route
- Add CSRF token to AJAX requests
- Handle validation errors from Laravel
- Update price calculation based on selected departure

---

## Phase 6: Admin Panel (Filament)

**Duration:** 2-3 days
**Objective:** Create admin interfaces for managing departures and bookings

### 6.1 Create TourDeparture Filament Resource

**Command:**
```bash
php artisan make:filament-resource TourDeparture --generate
```

**File:** `app/Filament/Resources/TourDepartureResource.php`

**Key customizations:**
- Table view showing tour, dates, capacity, booked spots
- Filters for status, tour, date range
- Actions for marking as guaranteed/full/cancelled
- Bulk actions for updating status

---

### 6.2 Update Booking Filament Resource

**File:** `app/Filament/Resources/Bookings/BookingResource.php`

**Add fields for:**
- Payment status badge
- Payment method
- Amount paid / remaining
- OCTO payment UUID link
- Refund action

---

## Phase 7: Testing & Deployment

**Duration:** 3-5 days
**Objective:** Comprehensive testing and production deployment

### 7.1 Testing Checklist

**Database Testing:**
- [ ] All migrations run without errors
- [ ] Rollback works correctly
- [ ] Foreign keys and indexes created

**Model Testing:**
- [ ] All relationships work
- [ ] Business logic methods return correct values
- [ ] Scopes filter correctly

**OCTO Integration Testing:**
- [ ] Test mode payment successful
- [ ] Webhook signature verification works
- [ ] Payment success updates booking correctly
- [ ] Payment cancellation handled properly
- [ ] Refunds process successfully

**Booking Flow Testing:**
- [ ] Group booking with departure works
- [ ] Private booking works
- [ ] Request to book workflow works
- [ ] Deposit payment calculates correctly (30%)
- [ ] Full payment calculates correctly (90% with 10% discount)
- [ ] Last-minute bookings restrict payment methods
- [ ] Booking window validation works
- [ ] Departure capacity checks work

**Frontend Testing:**
- [ ] Progressive form disclosure works
- [ ] Payment card selection works
- [ ] Price calculations are accurate
- [ ] Form validation displays errors
- [ ] Mobile responsive
- [ ] Works on Safari, Chrome, Firefox

**Admin Panel Testing:**
- [ ] Can create departures
- [ ] Can view/manage bookings
- [ ] Can process refunds
- [ ] Can approve/decline inquiries

---

### 7.2 Production Deployment Checklist

**Before Deployment:**
- [ ] Set `OCTO_TEST_MODE=false`
- [ ] Add production OCTO credentials
- [ ] Set `APP_ENV=production`
- [ ] Set `APP_DEBUG=false`
- [ ] Configure webhook URL in OCTO merchant panel
- [ ] Set up SSL certificate (HTTPS required)
- [ ] Configure email settings (for notifications)
- [ ] Set up queue worker (for background jobs)
- [ ] Set up logging/monitoring

**Deployment Steps:**
```bash
# 1. Pull latest code
git pull origin feature/tour-details-booking-form

# 2. Install dependencies
composer install --optimize-autoloader --no-dev

# 3. Run migrations
php artisan migrate --force

# 4. Clear caches
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 5. Restart queue workers
php artisan queue:restart
```

**Post-Deployment:**
- [ ] Test live booking flow (small amount)
- [ ] Verify webhook receives notifications
- [ ] Monitor logs for errors
- [ ] Test email notifications
- [ ] Verify admin panel works

---

## Timeline Summary

| Phase | Duration | Tasks |
|-------|----------|-------|
| Phase 1: Database | 2-3 days | Create migrations, run migrations |
| Phase 2: Models | 1-2 days | Update models, add relationships |
| Phase 3: OCTO Service | 2 days | Create service, configuration |
| Phase 4: Controllers | 3-4 days | Booking, Payment, Webhook controllers |
| Phase 5: Frontend | 3-4 days | Blade views, JavaScript integration |
| Phase 6: Admin Panel | 2-3 days | Filament resources |
| Phase 7: Testing & Deploy | 3-5 days | Testing, bug fixes, deployment |
| **TOTAL** | **16-23 days** | **~3-4 weeks** |

---

## Next Steps

1. ✅ Review this plan and confirm approach
2. ✅ Gather OCTO credentials (shop_id, secret, unique_key)
3. ✅ Start Phase 1: Database migrations
4. ✅ Proceed sequentially through phases
5. ✅ Test thoroughly before production

---

## Support & Resources

- **OCTO Integration Guide:** `D:/xampp82/htdocs/ssst3/OCTO_PAYMENT_INTEGRATION_GUIDE.md`
- **Business Decisions:** `D:/xampp82/htdocs/ssst3/remarks/booking-form/BOOKING_SYSTEM_FINAL_DECISIONS.txt`
- **Todo List:** Use `TodoWrite` tool to track progress

---

**Ready to begin implementation!** 🚀
