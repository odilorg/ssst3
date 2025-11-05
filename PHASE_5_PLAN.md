# Phase 5: Customer Portal & Account Management
## Implementation Plan

---

## 📋 Overview

Phase 5 will provide customers with a self-service portal to manage their bookings, make balance payments, view payment history, and update their profile information. This phase focuses on customer empowerment and reducing administrative overhead.

---

## 🎯 Objectives

1. **Customer Authentication** - Secure login/registration system
2. **Booking Management** - View and manage all bookings in one place
3. **Payment Self-Service** - Complete balance payments without admin assistance
4. **Profile Management** - Update personal information and preferences
5. **Notification System** - Automated email reminders and updates
6. **Document Access** - Download invoices, receipts, and booking confirmations

---

## 🏗️ Architecture Overview

```
┌─────────────────────────────────────────────────────────┐
│                   Customer Portal                        │
├─────────────────────────────────────────────────────────┤
│                                                          │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐ │
│  │ Auth System  │  │  Dashboard   │  │   Profile    │ │
│  │  - Register  │  │  - Bookings  │  │  - Settings  │ │
│  │  - Login     │  │  - Payments  │  │  - Security  │ │
│  │  - 2FA       │  │  - Documents │  │  - Prefs     │ │
│  └──────────────┘  └──────────────┘  └──────────────┘ │
│                                                          │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐ │
│  │   Payments   │  │ Notifications│  │   Documents  │ │
│  │  - Balance   │  │  - Reminders │  │  - Invoices  │ │
│  │  - History   │  │  - Updates   │  │  - Receipts  │ │
│  │  - Receipts  │  │  - Marketing │  │  - Vouchers  │ │
│  └──────────────┘  └──────────────┘  └──────────────┘ │
│                                                          │
└─────────────────────────────────────────────────────────┘
```

---

## 📦 Deliverables

### 1. Authentication System
- [x] Customer registration with email verification
- [x] Login/logout functionality with remember me
- [x] Password reset flow
- [x] Two-factor authentication (optional)
- [x] Session management
- [x] Email verification required before booking access

### 2. Customer Dashboard
- [x] Overview page with statistics
- [x] Upcoming bookings section
- [x] Past bookings archive
- [x] Payment summary cards
- [x] Quick actions (pay balance, view details)
- [x] Recent activity timeline

### 3. Booking Management
- [x] List all bookings (active, completed, cancelled)
- [x] View detailed booking information
- [x] Download booking confirmation PDF
- [x] Cancel booking (within policy timeframe)
- [x] Request modifications
- [x] Add special requests

### 4. Payment Self-Service
- [x] View payment history
- [x] Pay remaining balance (one-click)
- [x] Download payment receipts
- [x] View refund status
- [x] Payment calendar/reminders
- [x] Multiple payment options

### 5. Profile Management
- [x] Edit personal information
- [x] Change password
- [x] Email preferences
- [x] Phone number verification
- [x] Passport/travel document storage
- [x] Emergency contact information

### 6. Notification System
- [x] Email notifications for:
  - Payment reminders (7 days, 3 days, 1 day before tour)
  - Booking confirmations
  - Tour updates/changes
  - Payment confirmations
  - Password reset
  - Account security alerts
- [x] SMS notifications (optional)
- [x] Notification preferences management

### 7. Document Management
- [x] Generate and download PDFs:
  - Booking confirmation
  - Payment receipts
  - Tax invoices
  - Tour vouchers
- [x] Email documents automatically
- [x] Document archive

---

## 🗂️ File Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Customer/
│   │   │   ├── DashboardController.php          # Customer dashboard
│   │   │   ├── BookingController.php            # View bookings
│   │   │   ├── PaymentController.php            # Payment management
│   │   │   ├── ProfileController.php            # Profile settings
│   │   │   └── DocumentController.php           # PDF downloads
│   │   └── Auth/
│   │       └── RegisteredCustomerController.php # Custom registration
│   └── Middleware/
│       ├── EnsureEmailIsVerified.php            # Email verification
│       └── CustomerAccess.php                    # Customer-only routes
│
├── Models/
│   └── Customer.php                              # Extends User model
│
├── Mail/
│   ├── PaymentReminder.php                       # Balance reminder
│   ├── BookingConfirmation.php                   # Booking confirmed
│   ├── TourUpdateNotification.php                # Tour changes
│   └── WelcomeCustomer.php                       # Welcome email
│
├── Jobs/
│   ├── SendPaymentReminder.php                   # Scheduled reminder
│   └── GenerateBookingDocument.php               # PDF generation
│
├── Services/
│   ├── DocumentGenerationService.php             # PDF creation
│   └── NotificationService.php                   # Notification logic
│
└── Console/
    └── Commands/
        └── SendPaymentReminders.php              # Daily reminder job

resources/
├── views/
│   ├── customer/
│   │   ├── auth/
│   │   │   ├── register.blade.php                # Registration form
│   │   │   ├── login.blade.php                   # Login form
│   │   │   ├── verify-email.blade.php            # Email verification
│   │   │   ├── forgot-password.blade.php         # Password reset
│   │   │   └── reset-password.blade.php          # New password form
│   │   ├── dashboard/
│   │   │   ├── index.blade.php                   # Main dashboard
│   │   │   ├── bookings.blade.php                # All bookings
│   │   │   ├── payments.blade.php                # Payment history
│   │   │   └── documents.blade.php               # Document archive
│   │   ├── bookings/
│   │   │   ├── show.blade.php                    # Booking details
│   │   │   ├── cancel.blade.php                  # Cancel form
│   │   │   └── modify.blade.php                  # Modification request
│   │   ├── profile/
│   │   │   ├── edit.blade.php                    # Edit profile
│   │   │   ├── security.blade.php                # Security settings
│   │   │   └── preferences.blade.php             # Notification prefs
│   │   └── layouts/
│   │       ├── app.blade.php                     # Customer layout
│   │       └── navigation.blade.php              # Customer nav
│   ├── emails/
│   │   ├── payment-reminder.blade.php            # Reminder email
│   │   ├── booking-confirmation-full.blade.php   # Full booking email
│   │   ├── tour-update.blade.php                 # Tour update email
│   │   └── welcome-customer.blade.php            # Welcome email
│   └── pdfs/
│       ├── booking-confirmation.blade.php        # Booking PDF
│       ├── payment-receipt.blade.php             # Receipt PDF
│       ├── tax-invoice.blade.php                 # Invoice PDF
│       └── tour-voucher.blade.php                # Voucher PDF

database/
└── migrations/
    ├── 2025_01_XX_000000_add_customer_fields_to_users_table.php
    ├── 2025_01_XX_000001_create_customer_preferences_table.php
    ├── 2025_01_XX_000002_create_customer_documents_table.php
    └── 2025_01_XX_000003_create_notification_logs_table.php

routes/
└── customer.php                                  # Customer-only routes

config/
└── customer.php                                  # Customer portal config

public/
└── css/
    └── customer-portal.css                       # Customer portal styles
```

---

## 🔧 Technical Implementation

### 1. Authentication Setup (Laravel Breeze)

**Why Breeze?**
- Lightweight and simple
- Email verification built-in
- Password reset flows included
- Easy to customize
- No heavy dependencies

**Installation:**
```bash
composer require laravel/breeze --dev
php artisan breeze:install blade
php artisan migrate
npm install && npm run build
```

**Customizations Needed:**
- Separate customer and admin authentication
- Add customer-specific fields to users table
- Customize registration flow for booking integration
- Add email verification requirement

### 2. Database Schema

**Users Table Updates:**
```php
Schema::table('users', function (Blueprint $table) {
    $table->string('phone')->nullable();
    $table->string('country')->nullable();
    $table->date('date_of_birth')->nullable();
    $table->string('passport_number')->nullable();
    $table->string('passport_country')->nullable();
    $table->date('passport_expiry')->nullable();
    $table->string('emergency_contact_name')->nullable();
    $table->string('emergency_contact_phone')->nullable();
    $table->string('preferred_language')->default('en');
    $table->boolean('marketing_emails')->default(true);
    $table->boolean('reminder_emails')->default(true);
    $table->timestamp('last_login_at')->nullable();
});
```

**Customer Preferences Table:**
```php
Schema::create('customer_preferences', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->boolean('email_booking_confirmed')->default(true);
    $table->boolean('email_payment_reminder')->default(true);
    $table->boolean('email_tour_updates')->default(true);
    $table->boolean('email_marketing')->default(false);
    $table->boolean('sms_notifications')->default(false);
    $table->string('timezone')->default('Asia/Tashkent');
    $table->timestamps();
});
```

**Customer Documents Table:**
```php
Schema::create('customer_documents', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->foreignId('booking_id')->nullable()->constrained()->cascadeOnDelete();
    $table->string('document_type'); // booking_confirmation, receipt, invoice, voucher
    $table->string('file_path');
    $table->string('file_name');
    $table->integer('file_size');
    $table->timestamp('generated_at');
    $table->timestamps();
});
```

**Notification Logs Table:**
```php
Schema::create('notification_logs', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->foreignId('booking_id')->nullable()->constrained()->cascadeOnDelete();
    $table->string('notification_type'); // email, sms
    $table->string('template'); // payment_reminder, booking_confirmed, etc.
    $table->string('status'); // sent, failed, pending
    $table->text('error_message')->nullable();
    $table->timestamp('sent_at')->nullable();
    $table->timestamps();
});
```

### 3. Customer Dashboard Controller

**Key Methods:**
```php
class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        return view('customer.dashboard.index', [
            'upcomingBookings' => $user->bookings()
                ->where('tour_start_date', '>', now())
                ->where('status', '!=', 'cancelled')
                ->with('tour')
                ->latest()
                ->limit(3)
                ->get(),

            'recentPayments' => $user->payments()
                ->latest()
                ->limit(5)
                ->get(),

            'stats' => [
                'total_bookings' => $user->bookings()->count(),
                'completed_tours' => $user->bookings()
                    ->where('tour_start_date', '<', now())
                    ->where('status', 'confirmed')
                    ->count(),
                'total_spent' => $user->payments()
                    ->where('status', 'completed')
                    ->sum('amount'),
                'balance_due' => $user->bookings()
                    ->sum('amount_remaining'),
            ],
        ]);
    }
}
```

### 4. Balance Payment Flow

**Customer-Initiated Payment:**
```php
class PaymentController extends Controller
{
    public function payBalance(Booking $booking)
    {
        // Verify ownership
        $this->authorize('view', $booking);

        // Check if balance exists
        if ($booking->amount_remaining <= 0) {
            return redirect()->back()
                ->with('error', 'No balance remaining on this booking.');
        }

        // Redirect to payment review
        return redirect()->route('payment.review', [
            'booking_id' => $booking->id,
            'payment_type' => 'balance',
        ]);
    }

    public function paymentHistory()
    {
        $payments = auth()->user()->payments()
            ->with('booking.tour')
            ->latest()
            ->paginate(15);

        return view('customer.dashboard.payments', compact('payments'));
    }
}
```

### 5. Document Generation Service

**PDF Generation with DomPDF:**
```php
class DocumentGenerationService
{
    public function generateBookingConfirmation(Booking $booking): string
    {
        $pdf = PDF::loadView('pdfs.booking-confirmation', [
            'booking' => $booking,
            'tour' => $booking->tour,
            'payments' => $booking->payments,
        ]);

        $fileName = "booking-confirmation-{$booking->booking_reference}.pdf";
        $filePath = "documents/bookings/{$booking->id}/{$fileName}";

        Storage::disk('private')->put($filePath, $pdf->output());

        // Log document generation
        CustomerDocument::create([
            'user_id' => $booking->user_id,
            'booking_id' => $booking->id,
            'document_type' => 'booking_confirmation',
            'file_path' => $filePath,
            'file_name' => $fileName,
            'file_size' => strlen($pdf->output()),
            'generated_at' => now(),
        ]);

        return $filePath;
    }

    public function generatePaymentReceipt(Payment $payment): string
    {
        $pdf = PDF::loadView('pdfs.payment-receipt', [
            'payment' => $payment,
            'booking' => $payment->booking,
        ]);

        $fileName = "receipt-{$payment->id}.pdf";
        $filePath = "documents/receipts/{$payment->id}/{$fileName}";

        Storage::disk('private')->put($filePath, $pdf->output());

        CustomerDocument::create([
            'user_id' => $payment->booking->user_id,
            'booking_id' => $payment->booking_id,
            'document_type' => 'payment_receipt',
            'file_path' => $filePath,
            'file_name' => $fileName,
            'file_size' => strlen($pdf->output()),
            'generated_at' => now(),
        ]);

        return $filePath;
    }
}
```

### 6. Payment Reminder System

**Command (Schedule Daily):**
```php
class SendPaymentReminders extends Command
{
    protected $signature = 'reminders:payment';

    public function handle()
    {
        // Find bookings with balance due in next 7 days, 3 days, 1 day
        $reminderWindows = [
            7 => 'week_before',
            3 => 'three_days',
            1 => 'day_before',
        ];

        foreach ($reminderWindows as $days => $type) {
            $targetDate = now()->addDays($days)->startOfDay();

            $bookings = Booking::where('payment_status', '!=', 'paid_in_full')
                ->where('amount_remaining', '>', 0)
                ->whereDate('tour_start_date', $targetDate)
                ->whereHas('user', function ($query) {
                    $query->where('reminder_emails', true);
                })
                ->get();

            foreach ($bookings as $booking) {
                // Check if reminder already sent
                if ($this->reminderAlreadySent($booking, $type)) {
                    continue;
                }

                SendPaymentReminder::dispatch($booking, $type);
            }
        }
    }
}
```

**Job:**
```php
class SendPaymentReminder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public Booking $booking,
        public string $reminderType
    ) {}

    public function handle(): void
    {
        try {
            Mail::to($this->booking->user->email)
                ->send(new PaymentReminder($this->booking, $this->reminderType));

            NotificationLog::create([
                'user_id' => $this->booking->user_id,
                'booking_id' => $this->booking->id,
                'notification_type' => 'email',
                'template' => 'payment_reminder_' . $this->reminderType,
                'status' => 'sent',
                'sent_at' => now(),
            ]);
        } catch (\Exception $e) {
            NotificationLog::create([
                'user_id' => $this->booking->user_id,
                'booking_id' => $this->booking->id,
                'notification_type' => 'email',
                'template' => 'payment_reminder_' . $this->reminderType,
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
```

### 7. Booking Linking Strategy

**Two Approaches:**

**Approach A: Link Existing Bookings by Email**
```php
// During registration or first login
$existingBookings = Booking::where('customer_email', $user->email)
    ->whereNull('user_id')
    ->get();

foreach ($existingBookings as $booking) {
    $booking->update(['user_id' => $user->id]);
}
```

**Approach B: Account Creation During Booking**
```php
// In BookingController::store()
if (!auth()->check()) {
    // Create account automatically
    $user = User::create([
        'name' => $request->customer_name,
        'email' => $request->customer_email,
        'password' => Hash::make(Str::random(32)), // Random password
        'email_verified_at' => null, // Require verification
    ]);

    // Send verification + password setup email
    $user->sendEmailVerificationNotification();
}

$booking->user_id = $user->id;
$booking->save();
```

---

## 📊 Feature Priority Matrix

| Feature | Priority | Complexity | Impact | Timeline |
|---------|----------|-----------|--------|----------|
| Authentication System | **HIGH** | Medium | High | 2 days |
| Customer Dashboard | **HIGH** | Medium | High | 3 days |
| Balance Payment | **HIGH** | Low | Critical | 1 day |
| Profile Management | Medium | Low | Medium | 1 day |
| Booking Details View | **HIGH** | Low | High | 1 day |
| Payment History | Medium | Low | Medium | 1 day |
| Email Notifications | **HIGH** | Medium | High | 2 days |
| Payment Reminders | **HIGH** | Medium | Critical | 2 days |
| PDF Generation | Medium | Medium | Medium | 2 days |
| Document Downloads | Medium | Low | Medium | 1 day |
| Booking Cancellation | Low | Medium | Low | 2 days |
| Two-Factor Auth | Low | Medium | Low | 1 day |
| **TOTAL** | - | - | - | **19 days** |

---

## 📝 Implementation Checklist

### Phase 5.1: Authentication & Foundation (3 days)

- [ ] Install Laravel Breeze
- [ ] Customize registration form with additional fields
- [ ] Add email verification requirement
- [ ] Create customer middleware
- [ ] Set up customer routes file
- [ ] Create customer layout and navigation
- [ ] Add customer-specific authentication guards
- [ ] Link existing bookings to user accounts
- [ ] Test registration → verification → login flow

**Files to Create:**
- `app/Http/Middleware/CustomerAccess.php`
- `routes/customer.php`
- `resources/views/customer/layouts/app.blade.php`
- `resources/views/customer/layouts/navigation.blade.php`
- Migration: `add_customer_fields_to_users_table`

### Phase 5.2: Dashboard & Booking Views (4 days)

- [ ] Create DashboardController
- [ ] Build dashboard index view with stats
- [ ] Create BookingController for customer
- [ ] Build bookings list view
- [ ] Build booking detail view
- [ ] Add quick actions (pay balance, download docs)
- [ ] Create booking timeline component
- [ ] Implement booking status badges
- [ ] Add responsive design for mobile

**Files to Create:**
- `app/Http/Controllers/Customer/DashboardController.php`
- `app/Http/Controllers/Customer/BookingController.php`
- `resources/views/customer/dashboard/index.blade.php`
- `resources/views/customer/dashboard/bookings.blade.php`
- `resources/views/customer/bookings/show.blade.php`

### Phase 5.3: Payment Self-Service (3 days)

- [ ] Add balance payment route to PaymentController
- [ ] Create payment history view
- [ ] Add "Pay Balance" buttons throughout portal
- [ ] Update payment review page for balance payments
- [ ] Test balance payment flow
- [ ] Add payment status indicators
- [ ] Create payment receipt download functionality

**Files to Update:**
- `app/Http/Controllers/PaymentController.php` (add customer methods)
- `resources/views/customer/dashboard/payments.blade.php` (NEW)
- `resources/views/payments/review.blade.php` (update for balance)

### Phase 5.4: Notification System (4 days)

- [ ] Create PaymentReminder mailable
- [ ] Create BookingConfirmation mailable (full version)
- [ ] Create TourUpdateNotification mailable
- [ ] Create WelcomeCustomer mailable
- [ ] Build SendPaymentReminder job
- [ ] Create SendPaymentReminders command
- [ ] Schedule command in Kernel
- [ ] Create notification_logs table
- [ ] Add notification tracking
- [ ] Test all email flows
- [ ] Add email preference settings

**Files to Create:**
- `app/Mail/PaymentReminder.php`
- `app/Mail/BookingConfirmation.php`
- `app/Mail/TourUpdateNotification.php`
- `app/Mail/WelcomeCustomer.php`
- `app/Jobs/SendPaymentReminder.php`
- `app/Console/Commands/SendPaymentReminders.php`
- `resources/views/emails/payment-reminder.blade.php`
- `resources/views/emails/booking-confirmation-full.blade.php`
- `resources/views/emails/tour-update.blade.php`
- `resources/views/emails/welcome-customer.blade.php`
- Migration: `create_notification_logs_table`

### Phase 5.5: Profile & Preferences (2 days)

- [ ] Create ProfileController
- [ ] Build profile edit view
- [ ] Add password change functionality
- [ ] Create preferences table
- [ ] Build preferences view
- [ ] Add email notification toggles
- [ ] Add passport information form
- [ ] Add emergency contact form
- [ ] Test profile updates

**Files to Create:**
- `app/Http/Controllers/Customer/ProfileController.php`
- `resources/views/customer/profile/edit.blade.php`
- `resources/views/customer/profile/security.blade.php`
- `resources/views/customer/profile/preferences.blade.php`
- Migration: `create_customer_preferences_table`

### Phase 5.6: Document Management (3 days)

- [ ] Install DomPDF: `composer require barryvdh/laravel-dompdf`
- [ ] Create DocumentGenerationService
- [ ] Build PDF templates:
  - Booking confirmation
  - Payment receipt
  - Tax invoice
  - Tour voucher
- [ ] Create DocumentController
- [ ] Add download routes
- [ ] Create customer_documents table
- [ ] Auto-generate documents on payment
- [ ] Add document archive view
- [ ] Test PDF generation and downloads

**Files to Create:**
- `app/Services/DocumentGenerationService.php`
- `app/Http/Controllers/Customer/DocumentController.php`
- `resources/views/pdfs/booking-confirmation.blade.php`
- `resources/views/pdfs/payment-receipt.blade.php`
- `resources/views/pdfs/tax-invoice.blade.php`
- `resources/views/pdfs/tour-voucher.blade.php`
- `resources/views/customer/dashboard/documents.blade.php`
- Migration: `create_customer_documents_table`

---

## 🧪 Testing Strategy

### Manual Testing Checklist

**Authentication:**
- [ ] Register new account with all fields
- [ ] Verify email verification email sent
- [ ] Click verification link and confirm account activated
- [ ] Login with credentials
- [ ] Test "Remember Me" functionality
- [ ] Request password reset
- [ ] Complete password reset flow
- [ ] Test logout

**Dashboard:**
- [ ] View dashboard with upcoming bookings
- [ ] Check stats accuracy (bookings, payments, balance)
- [ ] Navigate to bookings list
- [ ] View individual booking details
- [ ] Test quick actions (pay balance, download)

**Payments:**
- [ ] Click "Pay Balance" from dashboard
- [ ] Complete payment flow
- [ ] Verify booking updated
- [ ] Check payment history
- [ ] Download payment receipt

**Notifications:**
- [ ] Check welcome email after registration
- [ ] Verify booking confirmation email
- [ ] Test payment reminder (manual trigger)
- [ ] Update notification preferences
- [ ] Verify preferences respected

**Profile:**
- [ ] Update personal information
- [ ] Change password
- [ ] Add passport details
- [ ] Add emergency contact
- [ ] Update email preferences
- [ ] Verify changes saved

**Documents:**
- [ ] Download booking confirmation PDF
- [ ] Download payment receipt PDF
- [ ] View document archive
- [ ] Verify PDF formatting and content

---

## 🔒 Security Considerations

1. **Authorization Policies**
   - Customers can only view their own bookings
   - Customers cannot modify completed bookings
   - Document downloads require ownership verification

2. **Email Verification**
   - Required before accessing bookings
   - Prevents fake account creation

3. **Password Security**
   - Minimum 8 characters
   - Must include letters and numbers
   - Password reset tokens expire after 1 hour

4. **Payment Security**
   - Re-verify payment amounts before processing
   - Prevent duplicate payment submissions
   - Secure document storage (private disk)

5. **Session Management**
   - Auto-logout after 120 minutes of inactivity
   - Invalidate old sessions on password change

---

## 📧 Email Templates Required

1. **WelcomeCustomer** - Sent after registration
2. **EmailVerification** - Laravel built-in, customize template
3. **PasswordReset** - Laravel built-in, customize template
4. **BookingConfirmation** - Full booking details with payment summary
5. **PaymentConfirmation** - Already exists from Phase 4
6. **PaymentReminder** - 7 days, 3 days, 1 day before tour
7. **TourUpdate** - When admin modifies tour details
8. **BalancePaymentDue** - Final reminder 24 hours before tour
9. **BookingCancelled** - If customer cancels
10. **RefundProcessed** - When refund completes

---

## 🚀 Deployment Considerations

### Environment Variables

```env
# Customer Portal Settings
CUSTOMER_PORTAL_ENABLED=true
CUSTOMER_REGISTRATION_ENABLED=true
EMAIL_VERIFICATION_REQUIRED=true

# Payment Reminder Schedule
PAYMENT_REMINDER_ENABLED=true
PAYMENT_REMINDER_DAYS_BEFORE=7,3,1

# Document Generation
PDF_STORAGE_DISK=private
PDF_FONT_FAMILY=DejaVu Sans
PDF_PAGE_SIZE=A4

# Session Configuration
SESSION_LIFETIME=120
SESSION_SECURE_COOKIE=true
```

### Laravel Scheduler

Add to `app/Console/Kernel.php`:
```php
protected function schedule(Schedule $schedule)
{
    // Send payment reminders daily at 9:00 AM
    $schedule->command('reminders:payment')
        ->dailyAt('09:00')
        ->timezone('Asia/Tashkent');

    // Clean up old verification tokens weekly
    $schedule->command('auth:clear-resets')
        ->weekly();
}
```

Run scheduler with cron:
```bash
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

### Queue Worker

```bash
# Run queue worker for email jobs
php artisan queue:work --queue=emails,default --tries=3 --timeout=90
```

### Storage Setup

```bash
# Create private storage disk for documents
php artisan storage:link

# Set proper permissions
chmod -R 755 storage/app/private
```

---

## 📈 Success Metrics

### Phase 5 Complete When:

1. ✅ Customers can register and login
2. ✅ Customers can view all their bookings
3. ✅ Customers can pay remaining balance with one click
4. ✅ Payment reminders sent automatically
5. ✅ Customers can download booking confirmation PDFs
6. ✅ Customers can download payment receipt PDFs
7. ✅ Customers can update their profile
8. ✅ Email notifications working for all events
9. ✅ All existing bookings linked to customer accounts
10. ✅ Customer portal responsive on mobile devices

### Key Performance Indicators:

- **Registration Rate:** % of bookings that create accounts
- **Balance Payment Rate:** % of deposits that complete full payment via portal
- **Reminder Effectiveness:** % of reminders that result in payment
- **Customer Satisfaction:** Support tickets reduced by self-service
- **Document Access:** % of customers who download confirmation PDFs

---

## 🎯 Phase 5 Timeline

**Total Estimated Time:** 19 days (3-4 weeks)

### Week 1 (Days 1-5)
- Authentication setup (Days 1-2)
- Dashboard foundation (Days 3-5)

### Week 2 (Days 6-10)
- Booking views (Days 6-7)
- Payment self-service (Days 8-10)

### Week 3 (Days 11-15)
- Notification system (Days 11-14)
- Profile management (Day 15)

### Week 4 (Days 16-19)
- Document generation (Days 16-18)
- Testing and bug fixes (Day 19)

---

## 🔄 Integration with Existing Code

### Update Booking Model

Add relationship and helper methods:
```php
class Booking extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function canPayBalance(): bool
    {
        return $this->amount_remaining > 0
            && $this->payment_status !== 'paid_in_full'
            && $this->status !== 'cancelled';
    }

    public function canBeCancelled(): bool
    {
        // Allow cancellation up to 30 days before tour
        return $this->tour_start_date->greaterThan(now()->addDays(30))
            && $this->status !== 'cancelled';
    }
}
```

### Update User Model

```php
class User extends Authenticatable
{
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function payments()
    {
        return $this->hasManyThrough(Payment::class, Booking::class);
    }

    public function preferences()
    {
        return $this->hasOne(CustomerPreference::class);
    }

    public function documents()
    {
        return $this->hasMany(CustomerDocument::class);
    }
}
```

### Update Payment Observer

Add document generation after successful payment:
```php
class PaymentObserver
{
    public function updated(Payment $payment)
    {
        if ($payment->isDirty('status') && $payment->status === 'completed') {
            // Existing code...

            // Generate receipt PDF
            $documentService = app(DocumentGenerationService::class);
            $documentService->generatePaymentReceipt($payment);

            // If fully paid, generate booking confirmation
            if ($payment->booking->payment_status === 'paid_in_full') {
                $documentService->generateBookingConfirmation($payment->booking);
            }
        }
    }
}
```

---

## 📚 Dependencies to Install

```bash
# PDF Generation
composer require barryvdh/laravel-dompdf

# Authentication (if not already installed)
composer require laravel/breeze --dev
php artisan breeze:install blade

# QR Code for vouchers (optional)
composer require simplesoftwareio/simple-qrcode
```

---

## 🎨 UI/UX Considerations

### Design Principles
1. **Mobile-First:** Portal must work seamlessly on phones
2. **Clarity:** Clear payment status and remaining balance
3. **Speed:** Quick access to pay balance
4. **Trust:** Security badges and SSL indicators
5. **Accessibility:** WCAG 2.1 AA compliance

### Color Scheme
- **Primary Action:** Blue (#3B82F6) - Pay Balance buttons
- **Success:** Green (#10B981) - Completed payments
- **Warning:** Orange (#F59E0B) - Payment reminders
- **Danger:** Red (#EF4444) - Cancellations
- **Neutral:** Gray (#6B7280) - Informational text

### Key UI Elements
- Progress bars for payment completion
- Timeline view for booking history
- Cards for booking summaries
- Badges for status indicators
- Tooltips for help text

---

## 🐛 Known Challenges & Solutions

### Challenge 1: Linking Guest Bookings
**Problem:** Bookings created before portal existed have no user_id

**Solution:**
- Run migration to link by email on portal launch
- Add "Claim Booking" feature with reference number
- Auto-link on registration if email matches

### Challenge 2: Multiple Bookings Same Email
**Problem:** One email might have multiple bookings

**Solution:**
- All bookings automatically linked to user account
- Dashboard shows all past and future bookings
- Clear separation by status (upcoming, completed, cancelled)

### Challenge 3: Payment Reminder Spam
**Problem:** Customer might book multiple tours

**Solution:**
- Group reminders by day (one email with all due bookings)
- Allow snooze/dismiss for specific reminders
- Respect email preference settings

### Challenge 4: Document Storage Size
**Problem:** PDFs accumulate over time

**Solution:**
- Generate PDFs on-demand initially
- Cache generated PDFs for 30 days
- Implement cleanup job for old documents
- Consider cloud storage for production

---

## 📞 Support Integration

### Help Center Links
- Dashboard: Link to "How to Pay Balance"
- Profile: Link to "Managing Your Account"
- Bookings: Link to "Cancellation Policy"

### Contact Support
- WhatsApp button in navigation
- Email support@ssst3.com
- Phone number in footer

---

## ✅ Phase 5 Completion Criteria

Phase 5 is considered COMPLETE when:

1. ✅ Customer can register, verify email, and login
2. ✅ Customer dashboard displays:
   - Upcoming bookings (next 3)
   - Statistics (bookings, spent, balance)
   - Recent payments
3. ✅ Customer can view all bookings with details
4. ✅ Customer can pay balance with one click
5. ✅ Payment reminders sent automatically (7, 3, 1 day before)
6. ✅ Customer can download booking confirmation PDF
7. ✅ Customer can download payment receipt PDF
8. ✅ Customer can update profile and preferences
9. ✅ All email notifications working
10. ✅ Mobile-responsive design
11. ✅ Authorization policies enforced
12. ✅ All manual tests passing
13. ✅ Documentation updated
14. ✅ Deployed to staging environment

---

## 📖 Documentation to Update

- [ ] README.md - Add customer portal section
- [ ] API documentation - Customer endpoints
- [ ] User guide - Customer portal walkthrough
- [ ] Admin guide - Managing customer accounts
- [ ] Email templates guide - Customization instructions

---

## 🎉 Next Phase Preview

### Phase 6: Advanced Features (Future)
- Multi-language support (Russian, Uzbek)
- SMS notifications (Twilio integration)
- Tour reviews and ratings
- Booking modification self-service
- Group booking management
- Travel document verification
- Trip insurance integration
- Currency selection (UZS, USD, EUR)
- Mobile app (React Native)
- Live chat support

---

**Prepared By:** Claude Code Assistant
**Date:** 2025-01-05
**Project:** SSST3 Tour Booking System
**Phase:** 5 - Customer Portal & Account Management
**Status:** READY FOR IMPLEMENTATION

---

## 🚦 Ready to Start?

Approve this plan to begin Phase 5 implementation with:
1. Authentication setup (Laravel Breeze)
2. Customer dashboard foundation
3. Balance payment integration

**Estimated completion:** 3-4 weeks