# Tour Booking & Inquiry Forms - Implementation Plan

**Feature Branch:** `feature/tour-booking-inquiry`
**Created:** 2025-11-07
**Status:** In Development
**Estimated Time:** 2-3 hours total

---

## 📋 Table of Contents

1. [Overview](#overview)
2. [Design Decision](#design-decision)
3. [Database Schema](#database-schema)
4. [Implementation Phases](#implementation-phases)
5. [File Structure](#file-structure)
6. [Testing Checklist](#testing-checklist)
7. [Deployment Plan](#deployment-plan)

---

## 1. Overview

### Problem
Current booking form has 3 payment options (Deposit, Full Payment, Request to Book) which is overly complex for initial launch and requires payment gateway integration.

### Solution
Simplified two-action system:
- **Book This Tour** - Creates confirmed booking (pending admin confirmation)
- **Ask a Question** - Creates tour inquiry (separate from Lead Management)

### Key Requirements
✅ No payment processing needed
✅ Admin manually confirms bookings
✅ Two separate backend flows (Booking vs Inquiry)
✅ Customer email + Admin notification for both actions
✅ Clean, modern UX with side-by-side buttons

---

## 2. Design Decision

### User Flow

```
┌─────────────────────────────────────────┐
│  STEP 1: Basic Info                     │
│  • Tour Date (required)                 │
│  • Number of Guests (required)          │
│  [Check Availability Button]            │
└──────────────┬──────────────────────────┘
               │ Progressive Disclosure
               ↓
┌─────────────────────────────────────────┐
│  STEP 2: Customer Information           │
│  • Full Name (required)                 │
│  • Email (required)                     │
│  • Phone (required)                     │
│  • Country (optional)                   │
│  • Special Requests (optional)          │
│                                         │
│  Choose Action:                         │
│  ┌────────────────┐  ┌────────────────┐│
│  │ 📅 BOOK NOW    │  │ 💬 ASK QUESTION││
│  │                │  │                ││
│  │ Ready to book  │  │ Need more info ││
│  └────────────────┘  └────────────────┘│
└─────────────────────────────────────────┘
```

### Backend Routing

**POST /bookings**
- `action=book` → Create Booking record
- `action=inquiry` → Create TourInquiry record

---

## 3. Database Schema

### 3.1 Existing Tables (No Changes)

#### `bookings` table
```php
// Already exists with these fields:
- id
- reference (auto-generated: BK-2025-001)
- customer_id (foreign key to customers)
- tour_id (foreign key to tours)
- start_date
- end_date (auto-calculated from tour duration)
- pax_total (number of guests)
- status (enum: pending, confirmed, cancelled)
- currency
- total_price
- notes
- timestamps
```

#### `customers` table
```php
// Already exists:
- id
- name
- email
- phone
- country
- telegram_username
- address
- timestamps
```

### 3.2 New Table: `tour_inquiries`

**Migration:** `database/migrations/YYYY_MM_DD_create_tour_inquiries_table.php`

```php
Schema::create('tour_inquiries', function (Blueprint $table) {
    $table->id();
    $table->string('reference')->unique(); // INQ-2025-001
    $table->foreignId('tour_id')->constrained()->onDelete('cascade');

    // Customer info (stored directly, not linked to customers table)
    $table->string('customer_name');
    $table->string('customer_email');
    $table->string('customer_phone')->nullable();
    $table->string('customer_country')->nullable();

    // Inquiry details
    $table->date('preferred_date')->nullable(); // They might not know yet
    $table->integer('estimated_guests')->nullable(); // Might be flexible
    $table->text('message'); // Their questions

    // Status tracking
    $table->enum('status', ['new', 'replied', 'converted', 'closed'])->default('new');
    $table->timestamp('replied_at')->nullable();
    $table->foreignId('replied_by')->nullable()->constrained('users');

    // Conversion tracking
    $table->foreignId('booking_id')->nullable()->constrained()->onDelete('set null');
    $table->timestamp('converted_at')->nullable();

    $table->timestamps();

    // Indexes
    $table->index('status');
    $table->index('created_at');
    $table->index(['tour_id', 'created_at']);
});
```

**Why separate table instead of Lead model?**
- Tour inquiries are product-specific (not general leads)
- Different fields (preferred_date, tour_id required)
- Different workflow (can convert to booking)
- Easier reporting (tour performance metrics)

---

## 4. Implementation Phases

### Phase 1: Database & Models (30 min)

#### Task 1.1: Create Migration
**File:** `database/migrations/YYYY_MM_DD_create_tour_inquiries_table.php`

```bash
php artisan make:migration create_tour_inquiries_table
```

#### Task 1.2: Create TourInquiry Model
**File:** `app/Models/TourInquiry.php`

```php
<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class TourInquiry extends Model
{
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

    // Relationships
    public function tour()
    {
        return $this->belongsTo(Tour::class);
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function repliedBy()
    {
        return $this->belongsTo(User::class, 'replied_by');
    }

    // Business Logic
    public function generateReference()
    {
        $year = Carbon::now()->year;
        $prefix = "INQ-{$year}-";

        $lastInquiry = static::where('reference', 'like', $prefix . '%')
            ->orderBy('reference', 'desc')
            ->first();

        if (!$lastInquiry) {
            return $prefix . '001';
        }

        $lastNumber = (int) substr($lastInquiry->reference, -3);
        $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);

        return $prefix . $newNumber;
    }

    public function markAsReplied(User $user)
    {
        $this->update([
            'status' => 'replied',
            'replied_at' => now(),
            'replied_by' => $user->id,
        ]);
    }

    public function convertToBooking(Booking $booking)
    {
        $this->update([
            'status' => 'converted',
            'booking_id' => $booking->id,
            'converted_at' => now(),
        ]);
    }
}
```

#### Task 1.3: Update Relationships

**Add to `app/Models/Tour.php`:**
```php
public function inquiries()
{
    return $this->hasMany(TourInquiry::class);
}
```

**Add to `app/Models/Booking.php`:**
```php
public function inquiry()
{
    return $this->hasOne(TourInquiry::class);
}
```

---

### Phase 2: Backend Controller (45 min)

#### Task 2.1: Update BookingController

**File:** `app/Http/Controllers/Partials/BookingController.php`

```php
<?php

namespace App\Http\Controllers\Partials;

use App\Http\Controllers\Controller;
use App\Mail\BookingConfirmation;
use App\Mail\BookingAdminNotification;
use App\Mail\InquiryConfirmation;
use App\Mail\InquiryAdminNotification;
use App\Models\Booking;
use App\Models\Customer;
use App\Models\Tour;
use App\Models\TourInquiry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class BookingController extends Controller
{
    /**
     * Show booking form partial
     */
    public function form(string $tourSlug)
    {
        $tour = Tour::where('slug', $tourSlug)
            ->where('is_active', true)
            ->with('activeExtras')
            ->firstOrFail();

        return view('partials.bookings.form', compact('tour'));
    }

    /**
     * Store booking or inquiry
     */
    public function store(Request $request)
    {
        $action = $request->input('action');

        if ($action === 'book') {
            return $this->handleBooking($request);
        } elseif ($action === 'inquiry') {
            return $this->handleInquiry($request);
        }

        return response()->json([
            'success' => false,
            'message' => 'Invalid action specified'
        ], 400);
    }

    /**
     * Handle booking request
     */
    private function handleBooking(Request $request)
    {
        // Validation
        $validator = Validator::make($request->all(), [
            'tour_id' => 'required|exists:tours,id',
            'tour_date' => 'required|date|after:today',
            'guests' => 'required|integer|min:1|max:50',
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'required|string|max:50',
            'customer_country' => 'nullable|string|max:100',
            'special_requests' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Find or create customer
            $customer = Customer::firstOrCreate(
                ['email' => $request->customer_email],
                [
                    'name' => $request->customer_name,
                    'phone' => $request->customer_phone,
                    'country' => $request->customer_country,
                ]
            );

            // Get tour
            $tour = Tour::findOrFail($request->tour_id);

            // Create booking
            $booking = Booking::create([
                'customer_id' => $customer->id,
                'tour_id' => $tour->id,
                'start_date' => $request->tour_date,
                'pax_total' => $request->guests,
                'status' => 'pending', // Admin needs to confirm
                'currency' => 'USD',
                'total_price' => $tour->price_per_person * $request->guests,
                'notes' => $request->special_requests,
            ]);

            // Send emails
            Mail::to($customer->email)
                ->send(new BookingConfirmation($booking, $customer));

            Mail::to(config('mail.admin_email'))
                ->send(new BookingAdminNotification($booking, $customer));

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Booking request submitted successfully! We\'ll confirm availability within 24 hours.',
                'booking_reference' => $booking->reference,
                'customer_email' => $customer->email,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing your booking. Please try again.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Handle inquiry request
     */
    private function handleInquiry(Request $request)
    {
        // Validation (more flexible than booking)
        $validator = Validator::make($request->all(), [
            'tour_id' => 'required|exists:tours,id',
            'tour_date' => 'nullable|date|after:today', // Optional for inquiry
            'guests' => 'nullable|integer|min:1|max:50', // Optional for inquiry
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'nullable|string|max:50', // Optional for inquiry
            'customer_country' => 'nullable|string|max:100',
            'message' => 'required|string|max:2000', // Required for inquiry
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Get tour
            $tour = Tour::findOrFail($request->tour_id);

            // Create inquiry
            $inquiry = TourInquiry::create([
                'tour_id' => $tour->id,
                'customer_name' => $request->customer_name,
                'customer_email' => $request->customer_email,
                'customer_phone' => $request->customer_phone,
                'customer_country' => $request->customer_country,
                'preferred_date' => $request->tour_date,
                'estimated_guests' => $request->guests,
                'message' => $request->message,
                'status' => 'new',
            ]);

            // Send emails
            Mail::to($inquiry->customer_email)
                ->send(new InquiryConfirmation($inquiry, $tour));

            Mail::to(config('mail.admin_email'))
                ->send(new InquiryAdminNotification($inquiry, $tour));

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Thank you for your inquiry! We\'ll respond within 24 hours.',
                'inquiry_reference' => $inquiry->reference,
                'customer_email' => $inquiry->customer_email,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while sending your inquiry. Please try again.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
}
```

#### Task 2.2: Add Admin Email to Config

**File:** `config/mail.php`

Add this line:
```php
'admin_email' => env('MAIL_ADMIN_ADDRESS', 'admin@jahongir-hotels.uz'),
```

**File:** `.env`

Add:
```
MAIL_ADMIN_ADDRESS=admin@jahongir-hotels.uz
```

---

### Phase 3: Email Templates (30 min)

#### Task 3.1: Booking Confirmation Email (Customer)

**File:** `app/Mail/BookingConfirmation.php`

```php
<?php

namespace App\Mail;

use App\Models\Booking;
use App\Models\Customer;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BookingConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public $booking;
    public $customer;

    public function __construct(Booking $booking, Customer $customer)
    {
        $this->booking = $booking;
        $this->customer = $customer;
    }

    public function build()
    {
        return $this->subject("Booking Request Received - {$this->booking->tour->title}")
                    ->markdown('emails.bookings.confirmation');
    }
}
```

**File:** `resources/views/emails/bookings/confirmation.blade.php`

```blade
@component('mail::message')
# Booking Request Received

Hello {{ $customer->name }},

Thank you for booking **{{ $booking->tour->title }}**!

## Booking Details

**Reference:** {{ $booking->reference }}
**Tour Date:** {{ $booking->start_date->format('F d, Y') }}
**Guests:** {{ $booking->pax_total }} {{ Str::plural('person', $booking->pax_total) }}
**Total Price:** ${{ number_format($booking->total_price, 2) }}

@if($booking->notes)
**Special Requests:**
{{ $booking->notes }}
@endif

## What's Next?

Our team will review your booking and confirm availability within **24 hours**. You'll receive a confirmation email with:
- Final price breakdown
- Payment instructions
- Meeting point details
- Contact information

@component('mail::button', ['url' => route('tours.show', $booking->tour->slug)])
View Tour Details
@endcomponent

If you have any questions, feel free to reply to this email or contact us at {{ config('mail.from.address') }}.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
```

#### Task 3.2: Booking Admin Notification

**File:** `app/Mail/BookingAdminNotification.php`

```php
<?php

namespace App\Mail;

use App\Models\Booking;
use App\Models\Customer;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BookingAdminNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $booking;
    public $customer;

    public function __construct(Booking $booking, Customer $customer)
    {
        $this->booking = $booking;
        $this->customer = $customer;
    }

    public function build()
    {
        return $this->subject("🔔 New Booking: {$this->booking->reference}")
                    ->markdown('emails.bookings.admin-notification');
    }
}
```

**File:** `resources/views/emails/bookings/admin-notification.blade.php`

```blade
@component('mail::message')
# 🔔 New Booking Request

A new booking has been submitted and requires confirmation.

## Booking Details

**Reference:** {{ $booking->reference }}
**Tour:** {{ $booking->tour->title }}
**Date:** {{ $booking->start_date->format('F d, Y') }}
**Guests:** {{ $booking->pax_total }}
**Total Price:** ${{ number_format($booking->total_price, 2) }}

## Customer Information

**Name:** {{ $customer->name }}
**Email:** {{ $customer->email }}
**Phone:** {{ $customer->phone }}
**Country:** {{ $customer->country ?? 'Not specified' }}

@if($booking->notes)
## Special Requests
{{ $booking->notes }}
@endif

@component('mail::button', ['url' => url('/admin/bookings/' . $booking->id)])
Review in Admin Panel
@endcomponent

**Action Required:** Please confirm availability and contact the customer within 24 hours.

{{ config('app.name') }} Admin System
@endcomponent
```

#### Task 3.3: Inquiry Confirmation Email (Customer)

**File:** `app/Mail/InquiryConfirmation.php`

```php
<?php

namespace App\Mail;

use App\Models\Tour;
use App\Models\TourInquiry;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InquiryConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public $inquiry;
    public $tour;

    public function __construct(TourInquiry $inquiry, Tour $tour)
    {
        $this->inquiry = $inquiry;
        $this->tour = $tour;
    }

    public function build()
    {
        return $this->subject("Inquiry Received - {$this->tour->title}")
                    ->markdown('emails.inquiries.confirmation');
    }
}
```

**File:** `resources/views/emails/inquiries/confirmation.blade.php`

```blade
@component('mail::message')
# Thank You for Your Inquiry

Hello {{ $inquiry->customer_name }},

We've received your inquiry about **{{ $tour->title }}** and will get back to you shortly!

## Inquiry Details

**Reference:** {{ $inquiry->reference }}
@if($inquiry->preferred_date)
**Preferred Date:** {{ $inquiry->preferred_date->format('F d, Y') }}
@endif
@if($inquiry->estimated_guests)
**Guests:** {{ $inquiry->estimated_guests }} {{ Str::plural('person', $inquiry->estimated_guests) }}
@endif

**Your Message:**
{{ $inquiry->message }}

## What's Next?

Our team will review your questions and respond within **24 hours** with detailed information about:
- Tour availability
- Pricing and payment options
- Meeting point and logistics
- Any special arrangements you need

@component('mail::button', ['url' => route('tours.show', $tour->slug)])
View Tour Details
@endcomponent

In the meantime, feel free to browse our other tours or contact us at {{ config('mail.from.address') }}.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
```

#### Task 3.4: Inquiry Admin Notification

**File:** `app/Mail/InquiryAdminNotification.php`

```php
<?php

namespace App\Mail;

use App\Models\Tour;
use App\Models\TourInquiry;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InquiryAdminNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $inquiry;
    public $tour;

    public function __construct(TourInquiry $inquiry, Tour $tour)
    {
        $this->inquiry = $inquiry;
        $this->tour = $tour;
    }

    public function build()
    {
        return $this->subject("💬 New Tour Inquiry: {$this->inquiry->reference}")
                    ->markdown('emails.inquiries.admin-notification');
    }
}
```

**File:** `resources/views/emails/inquiries/admin-notification.blade.php`

```blade
@component('mail::message')
# 💬 New Tour Inquiry

A potential customer has submitted an inquiry about **{{ $tour->title }}**.

## Inquiry Details

**Reference:** {{ $inquiry->reference }}
**Tour:** {{ $tour->title }}
@if($inquiry->preferred_date)
**Preferred Date:** {{ $inquiry->preferred_date->format('F d, Y') }}
@endif
@if($inquiry->estimated_guests)
**Guests:** {{ $inquiry->estimated_guests }}
@endif

## Customer Information

**Name:** {{ $inquiry->customer_name }}
**Email:** {{ $inquiry->customer_email }}
**Phone:** {{ $inquiry->customer_phone ?? 'Not provided' }}
**Country:** {{ $inquiry->customer_country ?? 'Not specified' }}

## Customer's Message

{{ $inquiry->message }}

@component('mail::button', ['url' => url('/admin/tour-inquiries/' . $inquiry->id)])
Respond in Admin Panel
@endcomponent

**Action Required:** Please respond to this inquiry within 24 hours.

{{ config('app.name') }} Admin System
@endcomponent
```

---

### Phase 4: Frontend Updates (45 min)

#### Task 4.1: Update HTML Form

**File:** `public/tour-details.html` (lines 788-834)

**REMOVE:** All 3 payment card divs
**REPLACE WITH:**

```html
<!-- Action Buttons Section -->
<div class="form-section">
  <h3 class="form-section__title">How would you like to proceed?</h3>

  <div class="action-buttons">
    <!-- Primary: Book This Tour -->
    <button type="submit"
            name="action"
            value="book"
            class="action-btn action-btn--primary"
            id="action-book">
      <svg class="action-btn__icon" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
        <line x1="16" y1="2" x2="16" y2="6"></line>
        <line x1="8" y1="2" x2="8" y2="6"></line>
        <line x1="3" y1="10" x2="21" y2="10"></line>
      </svg>
      <div class="action-btn__content">
        <span class="action-btn__title">Book This Tour</span>
        <span class="action-btn__subtitle">We'll confirm availability within 24 hours</span>
      </div>
    </button>

    <!-- Secondary: Send Inquiry -->
    <button type="submit"
            name="action"
            value="inquiry"
            class="action-btn action-btn--secondary"
            id="action-inquiry">
      <svg class="action-btn__icon" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
      </svg>
      <div class="action-btn__content">
        <span class="action-btn__title">Ask a Question</span>
        <span class="action-btn__subtitle">Get answers before booking</span>
      </div>
    </button>
  </div>

  <!-- Inquiry Message Field (Hidden by default, shown when inquiry clicked) -->
  <div class="form-group" id="inquiry-message-group" style="display: none;">
    <label for="inquiry-message" class="form-label">
      Your Questions <span class="required">*</span>
    </label>
    <textarea id="inquiry-message"
              name="message"
              class="form-input"
              rows="4"
              placeholder="Please let us know what you'd like to know about this tour..."></textarea>
  </div>
</div>
```

#### Task 4.2: Update CSS Styles

**File:** `public/tour-details.css`

**ADD at end of file:**

```css
/* =================================================================
   ACTION BUTTONS (Book vs Inquiry)
   ================================================================= */

.action-buttons {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1rem;
  margin-bottom: 1.5rem;
}

@media (max-width: 767px) {
  .action-buttons {
    grid-template-columns: 1fr;
  }
}

.action-btn {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 1.25rem 1.5rem;
  border: 2px solid #E0E0E0;
  border-radius: 12px;
  background: #fff;
  cursor: pointer;
  transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
  text-align: left;
  width: 100%;
}

.action-btn:hover {
  border-color: #1C54B2;
  transform: translateY(-2px);
  box-shadow: 0 8px 16px rgba(28, 84, 178, 0.1);
}

.action-btn:active {
  transform: translateY(0);
}

/* Primary Button (Book) */
.action-btn--primary {
  border-color: #1C54B2;
  background: linear-gradient(135deg, #1C54B2 0%, #1648A0 100%);
  color: #fff;
}

.action-btn--primary:hover {
  border-color: #1648A0;
  background: linear-gradient(135deg, #1648A0 0%, #143C8E 100%);
  box-shadow: 0 8px 20px rgba(28, 84, 178, 0.25);
}

.action-btn--primary .action-btn__icon {
  color: #fff;
}

/* Secondary Button (Inquiry) */
.action-btn--secondary {
  border-color: #E0E0E0;
  background: #fff;
  color: #2C2C2C;
}

.action-btn--secondary:hover {
  border-color: #1C54B2;
  background: #F8FAFC;
}

.action-btn--secondary .action-btn__icon {
  color: #1C54B2;
}

/* Icon */
.action-btn__icon {
  flex-shrink: 0;
  width: 24px;
  height: 24px;
}

/* Content */
.action-btn__content {
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
  flex: 1;
}

.action-btn__title {
  font-size: 1rem;
  font-weight: 600;
  line-height: 1.2;
}

.action-btn--primary .action-btn__title {
  color: #fff;
}

.action-btn--secondary .action-btn__title {
  color: #2C2C2C;
}

.action-btn__subtitle {
  font-size: 0.875rem;
  line-height: 1.3;
  opacity: 0.9;
}

.action-btn--primary .action-btn__subtitle {
  color: rgba(255, 255, 255, 0.9);
}

.action-btn--secondary .action-btn__subtitle {
  color: #6B6B6B;
}

/* Inquiry Message Field */
#inquiry-message-group {
  margin-top: 1.5rem;
  animation: fadeInUp 0.3s ease-out;
}

/* Remove old payment card styles (if you want to clean up) */
/* You can keep them for now in case you need them later */
```

#### Task 4.3: Update JavaScript

**File:** `public/tour-details.js`

**ADD before the last closing brace:**

```javascript
// =============================================================================
// ACTION BUTTON INTERACTIONS
// =============================================================================

/**
 * Handle action button clicks (Book vs Inquiry)
 */
function initActionButtons() {
  const bookBtn = document.getElementById('action-book');
  const inquiryBtn = document.getElementById('action-inquiry');
  const inquiryMessageGroup = document.getElementById('inquiry-message-group');
  const inquiryMessageField = document.getElementById('inquiry-message');
  const dateInput = document.getElementById('tour-date');
  const guestsInput = document.getElementById('tour-guests');

  if (!bookBtn || !inquiryBtn) return;

  // Track which action was selected
  let selectedAction = 'book'; // Default

  // Book button click
  bookBtn.addEventListener('click', (e) => {
    e.preventDefault();
    selectedAction = 'book';

    // Hide inquiry message field
    if (inquiryMessageGroup) {
      inquiryMessageGroup.style.display = 'none';
      if (inquiryMessageField) {
        inquiryMessageField.removeAttribute('required');
      }
    }

    // Make date and guests required for booking
    if (dateInput) dateInput.setAttribute('required', 'required');
    if (guestsInput) guestsInput.setAttribute('required', 'required');

    // Visual feedback
    bookBtn.classList.add('action-btn--selected');
    inquiryBtn.classList.remove('action-btn--selected');

    // Track analytics
    gtagEvent('booking_action_selected', {
      action: 'book',
      tour_id: window.TOUR_SLUG || 'unknown'
    });

    // Submit form
    submitBookingForm('book');
  });

  // Inquiry button click
  inquiryBtn.addEventListener('click', (e) => {
    e.preventDefault();
    selectedAction = 'inquiry';

    // Show inquiry message field
    if (inquiryMessageGroup) {
      inquiryMessageGroup.style.display = 'block';
      if (inquiryMessageField) {
        inquiryMessageField.setAttribute('required', 'required');
        // Focus on message field
        setTimeout(() => inquiryMessageField.focus(), 300);
      }
    }

    // Make date and guests optional for inquiry
    if (dateInput) dateInput.removeAttribute('required');
    if (guestsInput) guestsInput.removeAttribute('required');

    // Visual feedback
    inquiryBtn.classList.add('action-btn--selected');
    bookBtn.classList.remove('action-btn--selected');

    // Track analytics
    gtagEvent('inquiry_action_selected', {
      action: 'inquiry',
      tour_id: window.TOUR_SLUG || 'unknown'
    });

    // Submit form
    submitBookingForm('inquiry');
  });
}

/**
 * Submit booking/inquiry form
 */
function submitBookingForm(action) {
  const form = document.getElementById('booking-form');
  if (!form) return;

  // Validate form
  if (!form.checkValidity()) {
    form.reportValidity();
    return;
  }

  // Prepare form data
  const formData = new FormData(form);
  formData.set('action', action);
  formData.set('tour_id', window.TOUR_ID || ''); // Set from page context

  // Show loading state
  const submitBtn = action === 'book'
    ? document.getElementById('action-book')
    : document.getElementById('action-inquiry');

  if (submitBtn) {
    submitBtn.disabled = true;
    submitBtn.style.opacity = '0.6';
    submitBtn.style.cursor = 'not-allowed';
  }

  // Submit via fetch
  fetch('/bookings', {
    method: 'POST',
    headers: {
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
      'Accept': 'application/json',
    },
    body: formData
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      // Show success message
      showSuccessMessage(data.message, data.booking_reference || data.inquiry_reference);

      // Track conversion
      gtagEvent(action === 'book' ? 'booking_submitted' : 'inquiry_submitted', {
        reference: data.booking_reference || data.inquiry_reference,
        tour_id: window.TOUR_SLUG || 'unknown'
      });

      // Reset form
      form.reset();

      // Hide step 2
      const step2 = document.getElementById('step-2-full-form');
      if (step2) step2.style.display = 'none';

    } else {
      // Show error message
      showErrorMessage(data.message || 'An error occurred. Please try again.');
    }
  })
  .catch(error => {
    console.error('Submission error:', error);
    showErrorMessage('Network error. Please check your connection and try again.');
  })
  .finally(() => {
    // Re-enable button
    if (submitBtn) {
      submitBtn.disabled = false;
      submitBtn.style.opacity = '1';
      submitBtn.style.cursor = 'pointer';
    }
  });
}

/**
 * Show success message
 */
function showSuccessMessage(message, reference) {
  // Create success notification
  const notification = document.createElement('div');
  notification.className = 'notification notification--success';
  notification.innerHTML = `
    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
      <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
      <polyline points="22 4 12 14.01 9 11.01"></polyline>
    </svg>
    <div>
      <strong>Success!</strong>
      <p>${message}</p>
      ${reference ? `<p class="reference">Reference: <strong>${reference}</strong></p>` : ''}
    </div>
  `;

  document.body.appendChild(notification);

  // Auto-remove after 8 seconds
  setTimeout(() => {
    notification.classList.add('notification--fadeout');
    setTimeout(() => notification.remove(), 500);
  }, 8000);
}

/**
 * Show error message
 */
function showErrorMessage(message) {
  const notification = document.createElement('div');
  notification.className = 'notification notification--error';
  notification.innerHTML = `
    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
      <circle cx="12" cy="12" r="10"></circle>
      <line x1="15" y1="9" x2="9" y2="15"></line>
      <line x1="9" y1="9" x2="15" y2="15"></line>
    </svg>
    <div>
      <strong>Error</strong>
      <p>${message}</p>
    </div>
  `;

  document.body.appendChild(notification);

  setTimeout(() => {
    notification.classList.add('notification--fadeout');
    setTimeout(() => notification.remove(), 500);
  }, 6000);
}

// Initialize action buttons when DOM is ready
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initActionButtons);
} else {
  initActionButtons();
}
```

**ADD notification CSS:**

```css
/* =================================================================
   NOTIFICATIONS
   ================================================================= */

.notification {
  position: fixed;
  top: 20px;
  right: 20px;
  z-index: 10000;
  display: flex;
  align-items: flex-start;
  gap: 1rem;
  padding: 1rem 1.25rem;
  border-radius: 8px;
  background: #fff;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
  max-width: 400px;
  animation: slideInRight 0.3s ease-out;
}

@keyframes slideInRight {
  from {
    transform: translateX(100%);
    opacity: 0;
  }
  to {
    transform: translateX(0);
    opacity: 1;
  }
}

.notification--fadeout {
  animation: slideOutRight 0.5s ease-out forwards;
}

@keyframes slideOutRight {
  to {
    transform: translateX(120%);
    opacity: 0;
  }
}

.notification--success {
  border-left: 4px solid #10B981;
}

.notification--success svg {
  color: #10B981;
  flex-shrink: 0;
}

.notification--error {
  border-left: 4px solid #EF4444;
}

.notification--error svg {
  color: #EF4444;
  flex-shrink: 0;
}

.notification strong {
  display: block;
  margin-bottom: 0.25rem;
  font-size: 1rem;
  font-weight: 600;
}

.notification p {
  margin: 0;
  font-size: 0.875rem;
  line-height: 1.4;
  color: #4B5563;
}

.notification .reference {
  margin-top: 0.5rem;
  padding-top: 0.5rem;
  border-top: 1px solid #E5E7EB;
  font-size: 0.75rem;
  color: #6B7280;
}

.notification .reference strong {
  display: inline;
  font-family: monospace;
  font-size: 0.75rem;
  color: #1F2937;
}

@media (max-width: 640px) {
  .notification {
    left: 20px;
    right: 20px;
    max-width: none;
  }
}
```

---

### Phase 5: Filament Admin Resources (30 min)

#### Task 5.1: Create TourInquiry Resource

```bash
php artisan make:filament-resource TourInquiry --generate
```

**File:** `app/Filament/Resources/TourInquiryResource.php`

Key features:
- List page with filters (status, date range, tour)
- View/edit inquiry details
- Mark as replied action
- Convert to booking action
- Email reply form

---

## 5. File Structure

```
ssst3/
├── app/
│   ├── Http/Controllers/Partials/
│   │   └── BookingController.php ✅ UPDATED
│   ├── Mail/
│   │   ├── BookingConfirmation.php ✨ NEW
│   │   ├── BookingAdminNotification.php ✨ NEW
│   │   ├── InquiryConfirmation.php ✨ NEW
│   │   └── InquiryAdminNotification.php ✨ NEW
│   ├── Models/
│   │   ├── Booking.php ✅ (existing - add inquiry() relation)
│   │   ├── Tour.php ✅ (existing - add inquiries() relation)
│   │   └── TourInquiry.php ✨ NEW
│   └── Filament/Resources/
│       └── TourInquiryResource.php ✨ NEW
├── database/migrations/
│   └── YYYY_MM_DD_create_tour_inquiries_table.php ✨ NEW
├── resources/views/emails/
│   ├── bookings/
│   │   ├── confirmation.blade.php ✨ NEW
│   │   └── admin-notification.blade.php ✨ NEW
│   └── inquiries/
│       ├── confirmation.blade.php ✨ NEW
│       └── admin-notification.blade.php ✨ NEW
├── public/
│   ├── tour-details.html ✅ UPDATED (lines 788-834)
│   ├── tour-details.css ✅ UPDATED (add ~150 lines)
│   └── tour-details.js ✅ UPDATED (add ~200 lines)
├── config/
│   └── mail.php ✅ UPDATED (add admin_email)
├── .env ✅ UPDATED (add MAIL_ADMIN_ADDRESS)
└── BOOKING_INQUIRY_IMPLEMENTATION_PLAN.md ✨ THIS FILE
```

---

## 6. Testing Checklist

### 6.1 Booking Flow

- [ ] **Step 1:** Select date + guests → Click "Check Availability"
- [ ] **Step 2 reveals:** Customer info form visible
- [ ] **Fill form:** Name, Email, Phone (required fields)
- [ ] **Click "Book This Tour":** Form submits
- [ ] **Success notification:** Shows with booking reference
- [ ] **Customer email:** Receives booking confirmation
- [ ] **Admin email:** Receives admin notification
- [ ] **Database:** Booking record created with status `pending`
- [ ] **Database:** Customer record created/updated
- [ ] **Filament:** Booking visible in admin panel
- [ ] **Form reset:** Form clears after submission

### 6.2 Inquiry Flow

- [ ] **Click "Ask a Question":** Message field appears
- [ ] **Date/Guests optional:** Can submit without these fields
- [ ] **Message required:** Cannot submit without message
- [ ] **Fill form:** Name, Email, Message
- [ ] **Submit:** Form sends successfully
- [ ] **Success notification:** Shows with inquiry reference
- [ ] **Customer email:** Receives inquiry confirmation
- [ ] **Admin email:** Receives inquiry notification
- [ ] **Database:** TourInquiry record created with status `new`
- [ ] **Filament:** Inquiry visible in admin panel

### 6.3 Validation Testing

- [ ] **Empty date (booking):** Shows validation error
- [ ] **Empty guests (booking):** Shows validation error
- [ ] **Empty name:** Shows validation error
- [ ] **Invalid email:** Shows validation error
- [ ] **Empty phone (booking):** Shows validation error
- [ ] **Empty message (inquiry):** Shows validation error
- [ ] **Date in past:** Shows validation error

### 6.4 Email Testing

- [ ] **Booking confirmation:** Correct tour details, dates, price
- [ ] **Booking admin:** Contains customer info, special requests
- [ ] **Inquiry confirmation:** Acknowledges questions
- [ ] **Inquiry admin:** Contains customer message
- [ ] **All emails:** Links work (tour detail, admin panel)
- [ ] **All emails:** From/to addresses correct

### 6.5 Admin Panel Testing

- [ ] **TourInquiries list:** Shows all inquiries
- [ ] **Filter by status:** Works correctly
- [ ] **Filter by tour:** Works correctly
- [ ] **View inquiry:** Shows full details
- [ ] **Mark as replied:** Updates status and timestamp
- [ ] **Convert to booking:** Creates booking, links inquiry
- [ ] **Reply to customer:** Email form works

---

## 7. Deployment Plan

### Pre-Deployment Checklist

- [ ] All tests passing
- [ ] Email templates reviewed
- [ ] Admin panel tested
- [ ] Frontend responsive on mobile/tablet/desktop
- [ ] Analytics tracking verified
- [ ] `.env` configured with admin email
- [ ] Database migration ready

### Deployment Steps

```bash
# 1. Ensure on correct branch
git checkout feature/tour-booking-inquiry

# 2. Run migration
php artisan migrate

# 3. Clear caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# 4. Test on staging/local
# - Submit test booking
# - Submit test inquiry
# - Check emails
# - Check admin panel

# 5. Commit changes
git add .
git commit -m "feat: Implement booking and inquiry forms

- Add TourInquiry model and migration
- Update BookingController with dual flow (booking/inquiry)
- Create 4 email templates (booking/inquiry × customer/admin)
- Update frontend with action buttons (Book vs Ask Question)
- Add Filament admin resource for inquiries
- Add form validation and success/error handling"

# 6. Push to remote
git push origin feature/tour-booking-inquiry

# 7. Create PR (if needed)
# Or merge to master if ready

# 8. Deploy to production
# - Pull changes
# - Run php artisan migrate --force
# - Clear caches
```

### Post-Deployment Verification

- [ ] Submit test booking on production
- [ ] Submit test inquiry on production
- [ ] Verify emails sent correctly
- [ ] Check admin panel access
- [ ] Monitor error logs for 24 hours
- [ ] Track analytics (booking vs inquiry ratio)

---

## 8. Future Enhancements

### Phase 2 (After Launch)

1. **Payment Integration**
   - Add payment gateway (Stripe/PayPal)
   - Enable deposit and full payment options
   - Auto-confirm bookings after payment

2. **Availability Calendar**
   - Real-time availability checking
   - Block unavailable dates
   - Show "Only X spots left" urgency

3. **Automated Workflows**
   - Auto-send reminder if admin hasn't responded in 24h
   - Auto-close old inquiries after 30 days
   - Lead scoring for inquiries (likely to convert)

4. **Customer Portal**
   - Customers can view booking status
   - Download invoice/itinerary
   - Request modifications

5. **Multi-Language**
   - Translate emails (EN/RU/UZ)
   - Detect customer's preferred language

---

## 9. Notes & Decisions

### Why Separate TourInquiry Model?

**Alternative:** Could have used existing Lead model

**Decision:** Separate model because:
- Tour inquiries are product-specific
- Different fields (tour_id required, preferred_date)
- Different workflow (can convert to booking)
- Easier analytics and reporting
- Clear separation of concerns

### Why No Payment Now?

**Decision:** Launch with manual confirmation first to:
- Get operational quickly
- Test customer demand
- Refine pricing based on real bookings
- Build trust before asking for payment
- Avoid payment gateway complexity initially

### Email Strategy

**Decision:** Send 2 emails for both actions:
1. Customer confirmation (peace of mind)
2. Admin notification (action required)

This ensures:
- Customers feel acknowledged
- Admins never miss a request
- Clear paper trail
- Professional image

---

## 10. Success Metrics

### Track After Launch

**Conversion Funnel:**
1. Tour detail page views
2. "Check Availability" clicks
3. Step 2 reveals (form engagement)
4. Booking submissions
5. Inquiry submissions
6. Admin confirmations
7. Inquiry → Booking conversions

**Key Metrics:**
- Booking conversion rate (views → bookings)
- Inquiry conversion rate (views → inquiries)
- Inquiry-to-booking conversion rate
- Average response time (admin)
- Email open rates
- Mobile vs desktop submissions

**Goals (First Month):**
- [ ] 50+ booking submissions
- [ ] 30+ inquiries
- [ ] 80%+ admin response rate within 24h
- [ ] 20%+ inquiry-to-booking conversion

---

**Document Status:** Complete
**Ready to Implement:** Yes
**Estimated Implementation Time:** 2-3 hours
**Developer:** Ready to start on your command! 🚀
