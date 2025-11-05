# PHASE 1: DATABASE ARCHITECTURE - DETAILED IMPLEMENTATION PLAN

**Project:** Jahongir Travel - Tour Booking System
**Phase:** Phase 1 - Database Architecture
**Branch:** `feature/tour-details-booking-form`
**Duration:** 2-3 days
**Dependencies:** None (can start immediately)

---

## Table of Contents

1. [Overview](#overview)
2. [Pre-Implementation Checklist](#pre-implementation-checklist)
3. [Migration 1: tour_departures](#migration-1-tour_departures)
4. [Migration 2: add_booking_fields_to_tours](#migration-2-add_booking_fields_to_tours)
5. [Migration 3: add_payment_fields_to_bookings](#migration-3-add_payment_fields_to_bookings)
6. [Migration 4: payments](#migration-4-payments)
7. [Migration 5: booking_travelers](#migration-5-booking_travelers)
8. [Execution Plan](#execution-plan)
9. [Testing & Verification](#testing--verification)
10. [Rollback Strategy](#rollback-strategy)
11. [Troubleshooting](#troubleshooting)

---

## Overview

**Objective:** Create and execute all database migrations for the booking system

**What We're Building:**
- ✅ Tour departures table (for group tours with fixed dates)
- ✅ Enhanced tours table (tour types, pricing, booking rules)
- ✅ Enhanced bookings table (payment tracking, customer info)
- ✅ Payments table (transaction audit log)
- ✅ Booking travelers table (individual passenger details)

**Success Criteria:**
- All migrations run without errors
- All foreign keys and indexes created correctly
- Rollback works for all migrations
- Database schema matches business requirements
- No breaking changes to existing data

---

## Pre-Implementation Checklist

**Before starting, verify:**

- [ ] Working directory: `D:/xampp82/htdocs/ssst3`
- [ ] Branch: `feature/tour-details-booking-form`
- [ ] Database connection working
- [ ] Laravel migrations table exists
- [ ] Backup of existing database (if production data exists)

**Check database connection:**
```bash
cd D:/xampp82/htdocs/ssst3
php artisan migrate:status
```

**Create backup (if needed):**
```bash
# MySQL dump
mysqldump -u root -p jahongir_travel > backup_before_phase1_$(date +%Y%m%d).sql
```

---

## Migration 1: tour_departures

**Purpose:** Store fixed departure dates for group tours

### Step 1.1: Create Migration File

**Command:**
```bash
php artisan make:migration create_tour_departures_table
```

**Expected Output:**
```
Created Migration: 2025_11_05_XXXXXX_create_tour_departures_table
```

**File Location:** `database/migrations/2025_11_05_XXXXXX_create_tour_departures_table.php`

---

### Step 1.2: Migration Code

**Full Code:**
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tour_departures', function (Blueprint $table) {
            $table->id();

            // Foreign key to tours
            $table->foreignId('tour_id')
                ->constrained('tours')
                ->onDelete('cascade')
                ->comment('Tour this departure belongs to');

            // Departure dates
            $table->date('start_date')
                ->comment('Departure start date');

            $table->date('end_date')
                ->comment('Departure end date');

            // Capacity management
            $table->unsignedInteger('max_pax')
                ->default(12)
                ->comment('Maximum number of participants');

            $table->unsignedInteger('booked_pax')
                ->default(0)
                ->comment('Current number of booked participants');

            $table->unsignedInteger('min_pax')
                ->nullable()
                ->comment('Minimum participants to guarantee departure (null = no minimum)');

            // Pricing override
            $table->decimal('price_per_person', 10, 2)
                ->nullable()
                ->comment('Override tour base price if set');

            // Status
            $table->enum('status', [
                'open',       // Accepting bookings
                'guaranteed', // Minimum reached, departure confirmed
                'full',       // Maximum capacity reached
                'completed',  // Tour has finished
                'cancelled'   // Departure cancelled
            ])->default('open')
                ->comment('Departure status');

            // Departure type
            $table->enum('departure_type', ['group', 'private'])
                ->default('group')
                ->comment('Type of departure');

            // Additional information
            $table->text('notes')
                ->nullable()
                ->comment('Admin notes about this departure');

            $table->timestamps();

            // Indexes for performance
            $table->index(['tour_id', 'start_date'], 'idx_tour_date');
            $table->index('status', 'idx_status');
            $table->index('start_date', 'idx_start_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tour_departures');
    }
};
```

---

### Step 1.3: Field Specifications

| Field | Type | Nullable | Default | Purpose |
|-------|------|----------|---------|---------|
| `id` | bigint unsigned | No | auto | Primary key |
| `tour_id` | bigint unsigned | No | - | FK to tours table |
| `start_date` | date | No | - | When tour departs |
| `end_date` | date | No | - | When tour returns |
| `max_pax` | int unsigned | No | 12 | Maximum capacity |
| `booked_pax` | int unsigned | No | 0 | Current bookings |
| `min_pax` | int unsigned | Yes | null | Min to guarantee (null = always runs) |
| `price_per_person` | decimal(10,2) | Yes | null | Override tour price |
| `status` | enum | No | 'open' | Departure status |
| `departure_type` | enum | No | 'group' | Group or private |
| `notes` | text | Yes | null | Admin notes |
| `created_at` | timestamp | Yes | null | Creation time |
| `updated_at` | timestamp | Yes | null | Last update |

**Constraints:**
- `tour_id` → Foreign key to `tours.id` (cascade delete)
- `booked_pax` ≤ `max_pax` (enforced in application logic)
- `start_date` < `end_date` (enforced in application logic)

**Indexes:**
- Composite index on `(tour_id, start_date)` for efficient departure queries
- Index on `status` for filtering
- Index on `start_date` for date range queries

---

### Step 1.4: Validation Checklist

After creating the migration, verify:

- [ ] File exists in `database/migrations/`
- [ ] Class name matches filename
- [ ] `up()` method creates table
- [ ] `down()` method drops table
- [ ] All field types are correct
- [ ] Foreign keys defined with cascade
- [ ] Indexes created
- [ ] Comments added for clarity

---

## Migration 2: add_booking_fields_to_tours

**Purpose:** Add tour type, pricing, and booking configuration fields to tours table

### Step 2.1: Create Migration File

**Command:**
```bash
php artisan make:migration add_booking_fields_to_tours_table
```

**Expected Output:**
```
Created Migration: 2025_11_05_XXXXXX_add_booking_fields_to_tours_table
```

---

### Step 2.2: Migration Code

**Full Code:**
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tours', function (Blueprint $table) {
            // Tour type configuration
            $table->enum('tour_type', ['group_only', 'private_only', 'hybrid'])
                ->default('group_only')
                ->after('is_active')
                ->comment('Type of tour offering');

            // Pricing for group tours
            $table->decimal('group_price_per_person', 10, 2)
                ->nullable()
                ->after('tour_type')
                ->comment('Price per person for group bookings');

            // Pricing for private tours
            $table->decimal('private_price_per_person', 10, 2)
                ->nullable()
                ->after('group_price_per_person')
                ->comment('Price per person for private bookings');

            // Minimum charge for private tours
            $table->decimal('private_minimum_charge', 10, 2)
                ->nullable()
                ->after('private_price_per_person')
                ->comment('Minimum total charge for private bookings');

            // Booking window (in hours)
            $table->unsignedInteger('booking_window_hours')
                ->default(72)
                ->after('private_minimum_charge')
                ->comment('Hours in advance required to book (e.g., 8 for short tours, 168 for week-long tours)');

            // Payment policy
            $table->unsignedInteger('balance_due_days')
                ->default(3)
                ->after('booking_window_hours')
                ->comment('Days before tour that balance payment is due');

            $table->boolean('allow_last_minute_full_payment')
                ->default(true)
                ->after('balance_due_days')
                ->comment('Allow bookings within window if full payment made');

            // Traveler details requirement
            $table->boolean('requires_traveler_details')
                ->default(false)
                ->after('allow_last_minute_full_payment')
                ->comment('Requires individual traveler info (for tickets, visas, etc.)');

            // Cancellation policy (stored as JSON)
            $table->json('cancellation_policy')
                ->nullable()
                ->after('requires_traveler_details')
                ->comment('JSON: {full_refund_days: 14, partial_refund_days: 7, partial_refund_percentage: 50, no_refund_days: 3}');

            // Index for queries
            $table->index('tour_type', 'idx_tour_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tours', function (Blueprint $table) {
            $table->dropIndex('idx_tour_type');
            $table->dropColumn([
                'tour_type',
                'group_price_per_person',
                'private_price_per_person',
                'private_minimum_charge',
                'booking_window_hours',
                'balance_due_days',
                'allow_last_minute_full_payment',
                'requires_traveler_details',
                'cancellation_policy',
            ]);
        });
    }
};
```

---

### Step 2.3: Field Specifications

| Field | Type | Nullable | Default | Purpose |
|-------|------|----------|---------|---------|
| `tour_type` | enum | No | 'group_only' | Tour offering type |
| `group_price_per_person` | decimal(10,2) | Yes | null | Group tour pricing |
| `private_price_per_person` | decimal(10,2) | Yes | null | Private tour pricing |
| `private_minimum_charge` | decimal(10,2) | Yes | null | Min charge for private |
| `booking_window_hours` | int unsigned | No | 72 | Advance booking required |
| `balance_due_days` | int unsigned | No | 3 | Days before tour balance due |
| `allow_last_minute_full_payment` | boolean | No | true | Allow last-min if paid full |
| `requires_traveler_details` | boolean | No | false | Need passenger details |
| `cancellation_policy` | json | Yes | null | Refund rules |

**Tour Type Values:**
- `group_only`: Only fixed departure dates
- `private_only`: Only custom dates
- `hybrid`: Offer both group and private options

**Cancellation Policy JSON Example:**
```json
{
  "full_refund_days": 14,
  "partial_refund_days": 7,
  "partial_refund_percentage": 50,
  "no_refund_days": 3
}
```

---

### Step 2.4: Data Migration Strategy

**For existing tours:**

The migration uses nullable fields and sensible defaults, so existing tours will:
- Default to `tour_type = 'group_only'`
- Have `booking_window_hours = 72` (3 days)
- Have `balance_due_days = 3`
- Not require traveler details

**After migration, update existing tours manually or via seeder:**
```php
// Example: Update existing tours
DB::table('tours')->update([
    'group_price_per_person' => DB::raw('price'), // Copy from existing price field if you have one
]);
```

---

## Migration 3: add_payment_fields_to_bookings

**Purpose:** Add payment tracking, customer information, and booking workflow fields

### Step 3.1: Create Migration File

**Command:**
```bash
php artisan make:migration add_payment_fields_to_bookings_table
```

**Expected Output:**
```
Created Migration: 2025_11_05_XXXXXX_add_payment_fields_to_bookings_table
```

---

### Step 3.2: Migration Code

**Full Code:**
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Link to departure (nullable for private tours)
            $table->foreignId('departure_id')
                ->nullable()
                ->after('tour_id')
                ->constrained('tour_departures')
                ->onDelete('set null')
                ->comment('Linked departure for group bookings');

            // Booking type
            $table->enum('booking_type', ['group', 'private'])
                ->default('group')
                ->after('departure_id')
                ->comment('Type of booking');

            // Payment integration fields
            $table->string('payment_uuid', 100)
                ->nullable()
                ->after('reference')
                ->unique()
                ->comment('OCTO payment UUID');

            $table->enum('payment_method', ['deposit', 'full', 'request'])
                ->nullable()
                ->after('payment_uuid')
                ->comment('Selected payment method');

            $table->enum('payment_status', [
                'pending',
                'deposit_paid',
                'paid_in_full',
                'failed',
                'refunded',
                'partially_refunded',
                'awaiting_confirmation'
            ])->default('pending')
                ->after('payment_method')
                ->comment('Current payment status');

            // Financial tracking
            $table->decimal('amount_paid', 12, 2)
                ->default(0)
                ->after('total_price')
                ->comment('Total amount paid so far');

            $table->decimal('amount_remaining', 12, 2)
                ->default(0)
                ->after('amount_paid')
                ->comment('Amount still to be paid');

            $table->decimal('discount_applied', 12, 2)
                ->default(0)
                ->after('amount_remaining')
                ->comment('Discount amount (e.g., 10% for full payment)');

            $table->date('balance_due_date')
                ->nullable()
                ->after('discount_applied')
                ->comment('Date when remaining balance is due');

            // Customer information (for guest bookouts or override)
            $table->string('customer_name', 255)
                ->nullable()
                ->after('customer_id')
                ->comment('Customer full name');

            $table->string('customer_email', 255)
                ->nullable()
                ->after('customer_name')
                ->comment('Customer email address');

            $table->string('customer_phone', 50)
                ->nullable()
                ->after('customer_email')
                ->comment('Customer phone number');

            $table->string('customer_country', 100)
                ->nullable()
                ->after('customer_phone')
                ->comment('Customer country');

            // Special requests and notes
            $table->text('special_requests')
                ->nullable()
                ->after('notes')
                ->comment('Customer special requests (dietary, accessibility, etc.)');

            $table->text('inquiry_notes')
                ->nullable()
                ->after('special_requests')
                ->comment('Notes for request-to-book inquiries');

            // Terms agreement timestamp
            $table->timestamp('terms_agreed_at')
                ->nullable()
                ->after('inquiry_notes')
                ->comment('When customer agreed to terms & conditions');

            // Indexes for performance
            $table->index('payment_uuid', 'idx_payment_uuid');
            $table->index('payment_status', 'idx_payment_status');
            $table->index('booking_type', 'idx_booking_type');
            $table->index('balance_due_date', 'idx_balance_due_date');
            $table->index('customer_email', 'idx_customer_email');
        });

        // Update existing status enum to include new values
        DB::statement("ALTER TABLE bookings MODIFY COLUMN status ENUM(
            'draft',
            'inquiry',
            'pending_payment',
            'confirmed',
            'in_progress',
            'completed',
            'cancelled',
            'declined'
        ) DEFAULT 'draft'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Drop indexes
            $table->dropIndex('idx_payment_uuid');
            $table->dropIndex('idx_payment_status');
            $table->dropIndex('idx_booking_type');
            $table->dropIndex('idx_balance_due_date');
            $table->dropIndex('idx_customer_email');

            // Drop foreign key
            $table->dropForeign(['departure_id']);

            // Drop columns
            $table->dropColumn([
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
            ]);
        });

        // Revert status enum to original values
        DB::statement("ALTER TABLE bookings MODIFY COLUMN status ENUM(
            'draft',
            'pending',
            'confirmed',
            'in_progress',
            'completed',
            'cancelled'
        ) DEFAULT 'draft'");
    }
};
```

---

### Step 3.3: Field Specifications

**Relationship Fields:**

| Field | Type | Nullable | Purpose |
|-------|------|----------|---------|
| `departure_id` | bigint unsigned | Yes | FK to tour_departures (null for private) |
| `booking_type` | enum | No | 'group' or 'private' |

**Payment Tracking:**

| Field | Type | Nullable | Default | Purpose |
|-------|------|----------|---------|---------|
| `payment_uuid` | varchar(100) | Yes | null | OCTO payment identifier |
| `payment_method` | enum | Yes | null | deposit/full/request |
| `payment_status` | enum | No | 'pending' | Current payment state |
| `amount_paid` | decimal(12,2) | No | 0 | Total paid |
| `amount_remaining` | decimal(12,2) | No | 0 | Balance due |
| `discount_applied` | decimal(12,2) | No | 0 | Discount given |
| `balance_due_date` | date | Yes | null | When balance is due |

**Customer Information:**

| Field | Type | Nullable | Purpose |
|-------|------|----------|---------|
| `customer_name` | varchar(255) | Yes | Full name |
| `customer_email` | varchar(255) | Yes | Email address |
| `customer_phone` | varchar(50) | Yes | Phone number |
| `customer_country` | varchar(100) | Yes | Country |

**Additional Fields:**

| Field | Type | Nullable | Purpose |
|-------|------|----------|---------|
| `special_requests` | text | Yes | Dietary, accessibility needs |
| `inquiry_notes` | text | Yes | Admin notes for inquiries |
| `terms_agreed_at` | timestamp | Yes | T&C agreement time |

**Updated Status Enum:**
```
'draft'           - Booking started but not submitted
'inquiry'         - Request to book submitted
'pending_payment' - Awaiting payment
'confirmed'       - Payment received, booking confirmed
'in_progress'     - Tour is happening
'completed'       - Tour finished
'cancelled'       - Booking cancelled
'declined'        - Inquiry declined by admin
```

---

### Step 3.4: Important Notes

**Customer ID Nullable:**
- `customer_id` can be null for guest checkouts
- Use `customer_name`, `customer_email`, etc. for guest info
- Registered users: populate both `customer_id` and customer info fields

**Payment UUID Unique:**
- Each booking has unique OCTO payment
- Indexed for fast webhook lookups

**Balance Due Date:**
- Calculated as: `start_date - balance_due_days`
- Only set for deposit payments
- Null for full payment or request-to-book

---

## Migration 4: payments

**Purpose:** Create audit log for all payment transactions

### Step 4.1: Create Migration File

**Command:**
```bash
php artisan make:migration create_payments_table
```

**Expected Output:**
```
Created Migration: 2025_11_05_XXXXXX_create_payments_table
```

---

### Step 4.2: Migration Code

**Full Code:**
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            // Foreign key to booking
            $table->foreignId('booking_id')
                ->constrained('bookings')
                ->onDelete('cascade')
                ->comment('Booking this payment belongs to');

            // Amount (negative for refunds)
            $table->decimal('amount', 12, 2)
                ->comment('Payment amount (negative for refunds)');

            // Payment method
            $table->string('payment_method', 100)
                ->comment('Payment method (e.g., octo_uzcard, octo_visa, bank_transfer)');

            // Status
            $table->enum('status', ['pending', 'completed', 'failed', 'refunded'])
                ->default('pending')
                ->comment('Payment transaction status');

            // Payment type
            $table->enum('payment_type', ['deposit', 'balance', 'full', 'refund'])
                ->nullable()
                ->comment('Type of payment');

            // Gateway transaction details
            $table->string('transaction_id', 255)
                ->nullable()
                ->comment('Payment gateway transaction ID (e.g., OCTO payment UUID)');

            $table->json('gateway_response')
                ->nullable()
                ->comment('Full gateway response (webhook payload)');

            // Processing timestamp
            $table->timestamp('processed_at')
                ->nullable()
                ->comment('When payment was processed');

            $table->timestamps();

            // Indexes for queries
            $table->index(['booking_id', 'status'], 'idx_booking_status');
            $table->index('transaction_id', 'idx_transaction_id');
            $table->index('payment_type', 'idx_payment_type');
            $table->index('processed_at', 'idx_processed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
```

---

### Step 4.3: Field Specifications

| Field | Type | Nullable | Purpose |
|-------|------|----------|---------|
| `id` | bigint unsigned | No | Primary key |
| `booking_id` | bigint unsigned | No | FK to bookings |
| `amount` | decimal(12,2) | No | Amount (negative = refund) |
| `payment_method` | varchar(100) | No | Method used |
| `status` | enum | No | Transaction status |
| `payment_type` | enum | Yes | Type of payment |
| `transaction_id` | varchar(255) | Yes | Gateway transaction ID |
| `gateway_response` | json | Yes | Full webhook payload |
| `processed_at` | timestamp | Yes | Processing time |

**Payment Method Examples:**
- `octo_uzcard` - OCTO via UZCARD
- `octo_humo` - OCTO via HUMO
- `octo_visa` - OCTO via VISA
- `octo_mastercard` - OCTO via MasterCard
- `bank_transfer` - Manual bank transfer
- `cash` - Cash payment

**Payment Type Values:**
- `deposit` - Initial 30% payment
- `balance` - Remaining payment before tour
- `full` - 100% payment at once
- `refund` - Money returned to customer

**Gateway Response Example:**
```json
{
  "octo_payment_UUID": "abc-123",
  "status": "succeeded",
  "transfer_sum": 482500.00,
  "card_type": "UZCARD",
  "maskedPan": "860006******0005",
  "payed_time": "2025-11-05 14:30:00"
}
```

---

### Step 4.4: Usage Examples

**Creating Payment Records:**

```php
// When deposit payment succeeds
Payment::create([
    'booking_id' => $booking->id,
    'amount' => 150000,
    'payment_method' => 'octo_uzcard',
    'status' => 'completed',
    'payment_type' => 'deposit',
    'transaction_id' => $octoPaymentUUID,
    'gateway_response' => $webhookPayload,
    'processed_at' => now(),
]);

// When refund processed
Payment::create([
    'booking_id' => $booking->id,
    'amount' => -100000, // Negative!
    'payment_method' => 'octo_refund',
    'status' => 'completed',
    'payment_type' => 'refund',
    'transaction_id' => $refundId,
    'gateway_response' => $refundResponse,
    'processed_at' => now(),
]);
```

**Querying:**
```php
// Get all payments for a booking
$booking->payments;

// Get completed payments only
$booking->payments()->completed()->get();

// Calculate total paid
$totalPaid = $booking->payments()
    ->completed()
    ->where('amount', '>', 0)
    ->sum('amount');

// Calculate total refunded
$totalRefunded = abs($booking->payments()
    ->completed()
    ->where('amount', '<', 0)
    ->sum('amount'));
```

---

## Migration 5: booking_travelers

**Purpose:** Store individual traveler details when required

### Step 5.1: Create Migration File

**Command:**
```bash
php artisan make:migration create_booking_travelers_table
```

**Expected Output:**
```
Created Migration: 2025_11_05_XXXXXX_create_booking_travelers_table
```

---

### Step 5.2: Migration Code

**Full Code:**
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('booking_travelers', function (Blueprint $table) {
            $table->id();

            // Foreign key to booking
            $table->foreignId('booking_id')
                ->constrained('bookings')
                ->onDelete('cascade')
                ->comment('Booking this traveler belongs to');

            // Personal information
            $table->string('full_name', 255)
                ->comment('Traveler full name (as per passport)');

            $table->date('date_of_birth')
                ->nullable()
                ->comment('Date of birth');

            $table->string('nationality', 100)
                ->nullable()
                ->comment('Nationality');

            // Passport details (for international tours, visas, tickets)
            $table->string('passport_number', 50)
                ->nullable()
                ->comment('Passport number');

            $table->date('passport_expiry')
                ->nullable()
                ->comment('Passport expiration date');

            // Special requirements
            $table->text('dietary_requirements')
                ->nullable()
                ->comment('Food allergies, vegetarian, halal, etc.');

            $table->text('special_needs')
                ->nullable()
                ->comment('Mobility issues, medical conditions, etc.');

            $table->timestamps();

            // Index for queries
            $table->index('booking_id', 'idx_booking_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_travelers');
    }
};
```

---

### Step 5.3: Field Specifications

| Field | Type | Nullable | Purpose |
|-------|------|----------|---------|
| `id` | bigint unsigned | No | Primary key |
| `booking_id` | bigint unsigned | No | FK to bookings |
| `full_name` | varchar(255) | No | Full name (as per ID) |
| `date_of_birth` | date | Yes | DOB |
| `nationality` | varchar(100) | Yes | Nationality |
| `passport_number` | varchar(50) | Yes | Passport number |
| `passport_expiry` | date | Yes | Passport expiry |
| `dietary_requirements` | text | Yes | Food needs |
| `special_needs` | text | Yes | Accessibility/medical |

---

### Step 5.4: When to Use This Table

**Required when:**
- Tour has `requires_traveler_details = true`
- Booking flights/trains (need passport info)
- International tours (visa requirements)
- Tours with meals (dietary requirements)

**Optional when:**
- Simple day tours
- Domestic tours without tickets
- Tours where only lead booker info needed

**Collection Timing:**
- **Option A:** Collect during booking (may reduce conversions)
- **Option B:** Collect after payment (via email follow-up)
- **Recommended:** Option B - collect within 7 days of tour

**Example Usage:**
```php
// Check if traveler details needed
if ($tour->requires_traveler_details) {
    // Create travelers for each pax
    for ($i = 0; $i < $booking->pax_total; $i++) {
        BookingTraveler::create([
            'booking_id' => $booking->id,
            'full_name' => $request->input("travelers.{$i}.name"),
            'passport_number' => $request->input("travelers.{$i}.passport"),
            // ...
        ]);
    }
}
```

---

## Execution Plan

### Step-by-Step Execution

**Execute in this exact order:**

#### Step 1: Create All Migration Files

```bash
cd D:/xampp82/htdocs/ssst3

# Migration 1
php artisan make:migration create_tour_departures_table

# Migration 2
php artisan make:migration add_booking_fields_to_tours_table

# Migration 3
php artisan make:migration add_payment_fields_to_bookings_table

# Migration 4
php artisan make:migration create_payments_table

# Migration 5
php artisan make:migration create_booking_travelers_table
```

**Expected:** 5 new files in `database/migrations/`

---

#### Step 2: Copy Migration Code

For each migration file, copy the code from this plan:

1. Open migration file
2. Replace entire contents with code from plan
3. Save file
4. Verify no syntax errors

---

#### Step 3: Review Migrations

```bash
# Check pending migrations
php artisan migrate:status
```

**Expected Output:**
```
+------+--------------------------------------------------------+-------+
| Ran? | Migration                                              | Batch |
+------+--------------------------------------------------------+-------+
| No   | 2025_11_05_XXXXXX_create_tour_departures_table         |       |
| No   | 2025_11_05_XXXXXX_add_booking_fields_to_tours_table    |       |
| No   | 2025_11_05_XXXXXX_add_payment_fields_to_bookings_table |       |
| No   | 2025_11_05_XXXXXX_create_payments_table                |       |
| No   | 2025_11_05_XXXXXX_create_booking_travelers_table       |       |
+------+--------------------------------------------------------+-------+
```

---

#### Step 4: Run Migrations

```bash
# Run migrations
php artisan migrate
```

**Expected Output:**
```
Migrating: 2025_11_05_XXXXXX_create_tour_departures_table
Migrated:  2025_11_05_XXXXXX_create_tour_departures_table (XX.XXms)

Migrating: 2025_11_05_XXXXXX_add_booking_fields_to_tours_table
Migrated:  2025_11_05_XXXXXX_add_booking_fields_to_tours_table (XX.XXms)

Migrating: 2025_11_05_XXXXXX_add_payment_fields_to_bookings_table
Migrated:  2025_11_05_XXXXXX_add_payment_fields_to_bookings_table (XX.XXms)

Migrating: 2025_11_05_XXXXXX_create_payments_table
Migrated:  2025_11_05_XXXXXX_create_payments_table (XX.XXms)

Migrating: 2025_11_05_XXXXXX_create_booking_travelers_table
Migrated:  2025_11_05_XXXXXX_create_booking_travelers_table (XX.XXms)
```

**If errors occur, see [Troubleshooting](#troubleshooting)**

---

#### Step 5: Verify Database Schema

```bash
# Check migration status
php artisan migrate:status
```

**All migrations should show "Ran"**

**Verify tables exist:**
```bash
# Access database
mysql -u root -p

# Use database
use jahongir_travel;

# Show tables
SHOW TABLES;
```

**Expected tables:**
- `tour_departures` ✓
- `tours` (with new columns) ✓
- `bookings` (with new columns) ✓
- `payments` ✓
- `booking_travelers` ✓

**Check specific table:**
```sql
-- Describe tour_departures
DESCRIBE tour_departures;

-- Describe tours (should have new columns)
DESCRIBE tours;

-- Describe bookings (should have new columns)
DESCRIBE bookings;

-- Check foreign keys
SELECT
    TABLE_NAME,
    COLUMN_NAME,
    CONSTRAINT_NAME,
    REFERENCED_TABLE_NAME,
    REFERENCED_COLUMN_NAME
FROM
    INFORMATION_SCHEMA.KEY_COLUMN_USAGE
WHERE
    TABLE_SCHEMA = 'jahongir_travel'
    AND REFERENCED_TABLE_NAME IS NOT NULL;
```

---

## Testing & Verification

### Test 1: Insert Sample Departure

```php
// Create test departure
php artisan tinker

$tour = Tour::first();

$departure = \App\Models\TourDeparture::create([
    'tour_id' => $tour->id,
    'start_date' => now()->addDays(30),
    'end_date' => now()->addDays(37),
    'max_pax' => 12,
    'booked_pax' => 0,
    'min_pax' => 4,
    'status' => 'open',
    'departure_type' => 'group',
]);

// Verify
$departure->fresh();
```

**Expected:** Departure created successfully, no errors

---

### Test 2: Update Tour with Booking Fields

```php
php artisan tinker

$tour = Tour::first();

$tour->update([
    'tour_type' => 'hybrid',
    'group_price_per_person' => 500000,
    'private_price_per_person' => 800000,
    'private_minimum_charge' => 2000000,
    'booking_window_hours' => 48,
    'balance_due_days' => 3,
    'requires_traveler_details' => false,
]);

// Verify
$tour->fresh();
```

**Expected:** Tour updated successfully

---

### Test 3: Create Test Booking

```php
php artisan tinker

$booking = Booking::create([
    'reference' => 'TEST-BK-001',
    'tour_id' => 1,
    'departure_id' => 1,
    'booking_type' => 'group',
    'customer_name' => 'Test Customer',
    'customer_email' => 'test@example.com',
    'customer_phone' => '+998901234567',
    'start_date' => now()->addDays(30),
    'end_date' => now()->addDays(37),
    'pax_total' => 2,
    'currency' => 'UZS',
    'total_price' => 1000000,
    'payment_method' => 'deposit',
    'payment_status' => 'pending',
    'amount_remaining' => 300000,
    'status' => 'pending_payment',
]);

// Verify
$booking->fresh();
```

**Expected:** Booking created successfully

---

### Test 4: Create Payment Record

```php
php artisan tinker

$payment = \App\Models\Payment::create([
    'booking_id' => 1,
    'amount' => 300000,
    'payment_method' => 'octo_uzcard',
    'status' => 'completed',
    'payment_type' => 'deposit',
    'transaction_id' => 'TEST-OCTO-UUID-123',
    'processed_at' => now(),
]);

// Verify
$payment->fresh();
```

**Expected:** Payment created successfully

---

### Test 5: Create Traveler Record

```php
php artisan tinker

$traveler = \App\Models\BookingTraveler::create([
    'booking_id' => 1,
    'full_name' => 'John Doe',
    'date_of_birth' => '1990-01-15',
    'nationality' => 'US',
    'passport_number' => 'A12345678',
    'passport_expiry' => '2030-12-31',
]);

// Verify
$traveler->fresh();
```

**Expected:** Traveler created successfully

---

## Rollback Strategy

### Test Rollback

**Before deploying to production, test rollback:**

```bash
# Rollback last batch
php artisan migrate:rollback

# Verify tables/columns removed
# Re-run migrations
php artisan migrate
```

---

### Full Rollback

**If something goes wrong:**

```bash
# Rollback specific number of steps
php artisan migrate:rollback --step=5

# Or rollback all Phase 1 migrations
php artisan migrate:rollback --batch=X
```

**Where X = batch number from `migrate:status`**

---

### Emergency Rollback

**If migrations fail halfway:**

```bash
# Reset database (WARNING: Deletes all data!)
php artisan migrate:fresh

# Or manually drop tables
mysql -u root -p
DROP TABLE booking_travelers;
DROP TABLE payments;
ALTER TABLE bookings DROP COLUMN departure_id;
-- etc.
```

---

## Troubleshooting

### Error: Foreign key constraint fails

**Cause:** Referenced table doesn't exist

**Solution:**
```bash
# Check if tours table exists
SHOW TABLES LIKE 'tours';

# If not, run earlier migrations first
php artisan migrate
```

---

### Error: Column already exists

**Cause:** Migration ran twice or column manually added

**Solution:**
```bash
# Check what's in database
DESCRIBE tours;

# If column exists, skip in migration:
if (!Schema::hasColumn('tours', 'tour_type')) {
    $table->enum('tour_type', ...);
}
```

---

### Error: Syntax error near ENUM

**Cause:** Database doesn't support ENUM (unlikely with MySQL)

**Solution:**
```php
// Change ENUM to string with check constraint
$table->string('status')->default('open');

// Or use raw SQL
DB::statement("ALTER TABLE ...");
```

---

### Error: SQLSTATE[42000] - Unknown column

**Cause:** Trying to add column using `after()` on non-existent column

**Solution:**
```php
// Remove after() clause
$table->enum('tour_type', ...); // Without after()

// Or check if column exists first
if (Schema::hasColumn('tours', 'is_active')) {
    $table->enum('tour_type', ...)->after('is_active');
}
```

---

### Migration stuck or timeout

**Cause:** Large table with indexes

**Solution:**
```bash
# Increase timeout
php artisan migrate --timeout=300

# Or run migrations one by one
php artisan migrate --path=database/migrations/2025_11_05_XXXXXX_create_tour_departures_table.php
```

---

## Phase 1 Completion Checklist

**Before moving to Phase 2, verify:**

- [ ] All 5 migration files created
- [ ] All migrations run successfully
- [ ] All tables exist in database
- [ ] All foreign keys created correctly
- [ ] All indexes created
- [ ] Sample data inserted and retrieved successfully
- [ ] Rollback tested and working
- [ ] No errors in Laravel logs
- [ ] Database backed up
- [ ] Changes committed to git

**Git Commands:**
```bash
git status
git add database/migrations/
git commit -m "Phase 1: Database architecture - Add tour departures, enhanced tours/bookings, payments, travelers tables"
git push origin feature/tour-details-booking-form
```

---

## Success Metrics

**Phase 1 is complete when:**

✅ All 5 tables created
✅ Foreign keys working
✅ Can create test records
✅ Rollback works
✅ No migration errors
✅ Code committed to git

**Estimated Time:**
- Creating migrations: 2-3 hours
- Testing: 1-2 hours
- Troubleshooting: 1 hour buffer
- **Total: 4-6 hours**

---

## Next Steps (Phase 2 Preview)

Once Phase 1 complete:

1. Create/update Eloquent models
2. Add relationships
3. Add business logic methods
4. Create model factories (for testing)
5. Write model tests

**Estimated Phase 2 Duration:** 1-2 days

---

**Phase 1 Plan Complete - Ready to Execute!** 🚀
