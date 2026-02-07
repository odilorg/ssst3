# Payment Balance Reminder Automation - Implementation Plan

## 📊 Current Status Analysis

### ✅ What Already Exists

**Database Infrastructure (100% Complete):**
- ✓ `payment_reminders` table - stores reminder history
- ✓ `payment_transactions` table - tracks all payments
- ✓ `payment_schedules` table - flexible payment plans
- ✓ `payment_settings` table - system configuration
- ✓ Bookings table enhanced with:
  - `payment_type` (deposit/full/flexible)
  - `deposit_percentage`, `deposit_amount`, `deposit_paid_at`
  - `balance_amount`, `balance_due_date`, `balance_paid_at`
  - `payment_reminder_sent_at`

**Current Data:**
- 15 bookings with deposit payment type
- 11 bookings awaiting balance payment
- 0 payment reminders sent (NO AUTOMATION EXISTS)
- Balance due: 30 days before tour (from settings)

### ❌ What's Missing (NO AUTOMATION)

- ❌ Laravel Command to send balance reminders
- ❌ Email templates for balance payment reminders
- ❌ Scheduler configuration
- ❌ Booking Model helper methods for balance logic
- ❌ PaymentReminder Model

---

## 🎯 Implementation Plan

### Phase 1: Models & Helper Methods (30 minutes)

#### 1.1 Create PaymentReminder Model
```bash
php artisan make:model PaymentReminder
```

**app/Models/PaymentReminder.php:**
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentReminder extends Model
{
    protected $fillable = [
        'booking_id',
        'reminder_type',
        'scheduled_date',
        'sent_at',
        'email_sent',
        'sms_sent',
        'response',
    ];

    protected $casts = [
        'scheduled_date' => 'date',
        'sent_at' => 'datetime',
        'email_sent' => 'boolean',
        'sms_sent' => 'boolean',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function getReminderTypeLabel(): string
    {
        return match($this->reminder_type) {
            'balance_45_days' => '45 Days Before Tour',
            'balance_35_days' => '35 Days Before Tour',
            'balance_30_days' => '30 Days Before Balance Due',
            'balance_overdue' => 'Overdue Balance',
            default => $this->reminder_type,
        };
    }
}
```

#### 1.2 Enhance Booking Model

**Add to app/Models/Booking.php:**
```php
// Relationships
public function paymentReminders()
{
    return $this->hasMany(PaymentReminder::class);
}

// Helper Methods
public function isDepositPayment(): bool
{
    return $this->payment_type === 'deposit';
}

public function hasBalanceDue(): bool
{
    return $this->isDepositPayment()
        && is_null($this->balance_paid_at)
        && $this->payment_status !== 'failed';
}

public function daysUntilBalanceDue(): int
{
    if (!$this->balance_due_date) {
        return 0;
    }
    return max(0, (int) now()->diffInDays($this->balance_due_date, false));
}

public function isBalanceOverdue(): bool
{
    return $this->hasBalanceDue()
        && $this->balance_due_date
        && $this->balance_due_date->isPast();
}

public function isEligibleForBalanceReminder(string $reminderType): bool
{
    // Only for deposit bookings with balance due
    if (!$this->hasBalanceDue()) {
        return false;
    }

    // Check if reminder already sent
    $hasReminder = $this->paymentReminders()
        ->where('reminder_type', $reminderType)
        ->exists();

    if ($hasReminder) {
        return false;
    }

    // Ensure booking is confirmed and tour is in future
    if ($this->status !== 'confirmed' || !$this->start_date->isFuture()) {
        return false;
    }

    $daysUntilDue = $this->daysUntilBalanceDue();

    return match($reminderType) {
        'balance_45_days' => $daysUntilDue <= 45 && $daysUntilDue >= 38,
        'balance_35_days' => $daysUntilDue <= 37 && $daysUntilDue >= 31,
        'balance_30_days' => $daysUntilDue <= 30 && $daysUntilDue >= 15,
        'balance_overdue' => $this->isBalanceOverdue(),
        default => false,
    };
}

public function getBalancePaymentUrl(): string
{
    // Generate secure token for balance payment
    if (!$this->payment_uuid) {
        $this->payment_uuid = \Illuminate\Support\Str::uuid();
        $this->save();
    }

    // TODO: Replace with actual route once balance payment portal is built
    if (!\Illuminate\Support\Facades\Route::has('balance-payment.show')) {
        return url("/bookings/{$this->reference}/pay-balance");
    }

    return route('balance-payment.show', ['reference' => $this->reference]);
}
```

**Add to Booking fillable array:**
```php
'payment_reminder_sent_at',
'deposit_paid_at',
'balance_paid_at',
'balance_due_date',
'deposit_amount',
'balance_amount',
'payment_type',
'deposit_percentage',
```

**Add to Booking casts:**
```php
'deposit_paid_at' => 'datetime',
'balance_paid_at' => 'datetime',
'balance_due_date' => 'date',
'payment_reminder_sent_at' => 'datetime',
```

---

### Phase 2: Email Templates (45 minutes)

#### 2.1 Create Mail Classes

```bash
php artisan make:mail BalancePaymentReminder45Days --markdown=emails.payments.balance-reminder-45-days
php artisan make:mail BalancePaymentReminder35Days --markdown=emails.payments.balance-reminder-35-days
php artisan make:mail BalancePaymentReminder30Days --markdown=emails.payments.balance-reminder-30-days
php artisan make:mail BalancePaymentOverdue --markdown=emails.payments.balance-overdue
php artisan make:mail BalancePaymentReceived --markdown=emails.payments.balance-received
```

#### 2.2 Email Template Structure

**Example: BalancePaymentReminder45Days.php**
```php
<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BalancePaymentReminder45Days extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $booking;

    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Balance Payment Due Soon - {$this->booking->tour->title}",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.payments.balance-reminder-45-days',
            with: [
                'booking' => $this->booking,
                'customer' => $this->booking->customer,
                'tour' => $this->booking->tour,
                'balanceAmount' => $this->booking->balance_amount,
                'dueDate' => $this->booking->balance_due_date,
                'paymentUrl' => $this->booking->getBalancePaymentUrl(),
            ],
        );
    }
}
```

#### 2.3 Email Template Content

**resources/views/emails/payments/balance-reminder-45-days.blade.php:**
```blade
<x-mail::message>
# Balance Payment Reminder

Dear {{ $customer->name }},

Thank you for your deposit payment for **{{ $tour->title }}**! Your tour is coming up, and it's time to complete your balance payment.

## Payment Summary

<x-mail::table>
| Detail | Amount |
| :--- | ---: |
| **Total Tour Price** | ${{ number_format($booking->total_price, 2) }} USD |
| **Deposit Paid** | ${{ number_format($booking->deposit_amount, 2) }} USD ✓ |
| **Balance Due** | **${{ number_format($balanceAmount, 2) }} USD** |
| **Due Date** | {{ $dueDate->format('F j, Y') }} |
</x-mail::table>

## Booking Details

<x-mail::table>
| Detail | Information |
| :--- | :--- |
| **Reference** | {{ $booking->reference }} |
| **Tour Start** | {{ $booking->start_date->format('F j, Y') }} |
| **Guests** | {{ $booking->pax_total }} {{ $booking->pax_total === 1 ? 'guest' : 'guests' }} |
| **Days Until Tour** | {{ $booking->daysUntilTour() }} days |
</x-mail::table>

<x-mail::button :url="$paymentUrl">
Pay Balance Now
</x-mail::button>

<x-mail::panel>
**Payment Due:** {{ $dueDate->format('F j, Y') }} ({{ $booking->daysUntilBalanceDue() }} days remaining)

Please complete your balance payment by this date to ensure your tour booking is fully confirmed.
</x-mail::panel>

## Why Pay Now?

- ✓ Secure your spot on the tour
- ✓ Allow us to finalize all travel arrangements
- ✓ Ensure smooth check-in and travel experience
- ✓ Avoid late payment complications

## Payment Methods Accepted

- 💳 Credit/Debit Cards (Visa, Mastercard, Humo, Uzcard)
- 🏦 Bank Transfer
- 💰 Octobank Online Payment

## Need Help?

If you have any questions about your balance payment:

- **Email:** {{ config('mail.from.address') }}
- **Booking Reference:** {{ $booking->reference }}

We're here to help make your Uzbekistan journey unforgettable!

Best regards,<br>
**The Jahongir Travel Team**

---

<x-mail::subcopy>
**Security Note:** This payment link is unique to your booking. If you've already paid or have questions, please contact us.
</x-mail::subcopy>
</x-mail::message>
```

**Similar templates for:**
- `balance-reminder-35-days.blade.php` (more urgent tone)
- `balance-reminder-30-days.blade.php` (final notice before due date)
- `balance-overdue.blade.php` (urgent overdue notice)
- `balance-received.blade.php` (confirmation + thank you)

---

### Phase 3: Laravel Command (45 minutes)

```bash
php artisan make:command SendBalancePaymentReminders
```

**app/Console/Commands/SendBalancePaymentReminders.php:**
```php
<?php

namespace App\Console\Commands;

use App\Mail\BalancePaymentReminder45Days;
use App\Mail\BalancePaymentReminder35Days;
use App\Mail\BalancePaymentReminder30Days;
use App\Mail\BalancePaymentOverdue;
use App\Models\Booking;
use App\Models\PaymentReminder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendBalancePaymentReminders extends Command
{
    protected $signature = 'reminders:balance-payment {--dry-run : Preview reminders without sending}';
    protected $description = 'Send automated balance payment reminder emails to customers with deposits';

    public function handle()
    {
        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->info('🔍 DRY RUN MODE - No emails will be sent');
        }

        $this->info('Starting balance payment reminder checks...');

        $reminderTypes = [
            'balance_45_days' => BalancePaymentReminder45Days::class,
            'balance_35_days' => BalancePaymentReminder35Days::class,
            'balance_30_days' => BalancePaymentReminder30Days::class,
            'balance_overdue' => BalancePaymentOverdue::class,
        ];

        $totalSent = 0;

        foreach ($reminderTypes as $type => $mailableClass) {
            $sent = $this->processReminderType($type, $mailableClass, $dryRun);
            $totalSent += $sent;
        }

        $this->info("✅ Reminder check complete. Total reminders sent: {$totalSent}");

        Log::info('Balance payment reminders completed', [
            'total_sent' => $totalSent,
            'dry_run' => $dryRun,
        ]);

        return Command::SUCCESS;
    }

    private function processReminderType(string $type, string $mailableClass, bool $dryRun): int
    {
        $this->line("\n💰 Checking {$type} reminders...");

        // Get all bookings with balance due
        $bookings = Booking::where('payment_type', 'deposit')
            ->whereNull('balance_paid_at')
            ->where('status', 'confirmed')
            ->whereDate('start_date', '>', now())
            ->with(['customer', 'tour'])
            ->get();

        $sent = 0;

        foreach ($bookings as $booking) {
            if ($booking->isEligibleForBalanceReminder($type)) {
                if ($dryRun) {
                    $this->line("  → Would send {$type} to: {$booking->customer->email} (Booking: {$booking->reference}, Balance: \${$booking->balance_amount})");
                } else {
                    try {
                        // Send the email
                        Mail::to($booking->customer->email)
                            ->send(new $mailableClass($booking));

                        // Log the reminder
                        PaymentReminder::create([
                            'booking_id' => $booking->id,
                            'reminder_type' => $type,
                            'scheduled_date' => now(),
                            'sent_at' => now(),
                            'email_sent' => true,
                        ]);

                        // Update booking
                        $booking->update([
                            'payment_reminder_sent_at' => now(),
                        ]);

                        $this->info("  ✓ Sent {$type} to: {$booking->customer->email} (Balance: \${$booking->balance_amount})");

                        Log::info('Balance payment reminder sent', [
                            'booking_id' => $booking->id,
                            'booking_reference' => $booking->reference,
                            'customer_email' => $booking->customer->email,
                            'reminder_type' => $type,
                            'balance_amount' => $booking->balance_amount,
                            'days_until_due' => $booking->daysUntilBalanceDue(),
                        ]);

                        $sent++;
                    } catch (\Exception $e) {
                        $this->error("  ✗ Failed to send to: {$booking->customer->email}");
                        $this->error("    Error: {$e->getMessage()}");

                        Log::error('Balance payment reminder failed', [
                            'booking_id' => $booking->id,
                            'customer_email' => $booking->customer->email,
                            'reminder_type' => $type,
                            'error' => $e->getMessage(),
                        ]);
                    }
                }
            }
        }

        if ($sent === 0 && !$dryRun) {
            $this->line("  No {$type} reminders needed at this time.");
        }

        return $sent;
    }
}
```

---

### Phase 4: Scheduler Configuration (10 minutes)

**routes/console.php:**
```php
// Add after passenger reminders

// ============================================
// BALANCE PAYMENT REMINDER SCHEDULER
// ============================================

Schedule::command('reminders:balance-payment')
    ->dailyAt('11:00')
    ->timezone('Asia/Tashkent')
    ->emailOutputOnFailure(config('mail.from.address'))
    ->appendOutputTo(storage_path('logs/balance-reminders.log'));
```

---

### Phase 5: Testing (30 minutes)

#### 5.1 Test Command
```bash
# Dry run test
php artisan reminders:balance-payment --dry-run

# Actual test (with QUEUE_CONNECTION=sync for immediate results)
QUEUE_CONNECTION=sync php artisan reminders:balance-payment
```

#### 5.2 Verify Results
```bash
php artisan tinker
```

```php
// Check payment reminders logged
PaymentReminder::latest()->get();

// Check bookings updated
Booking::whereNotNull('payment_reminder_sent_at')->get();

// Check queue jobs (if using database queue)
DB::table('jobs')->count();
```

---

## 📋 Implementation Checklist

### Database & Models
- [ ] Create PaymentReminder model
- [ ] Add relationships to Booking model
- [ ] Add helper methods to Booking model
- [ ] Update Booking fillable/casts arrays

### Email System
- [ ] Create 5 Mail classes (45-day, 35-day, 30-day, overdue, received)
- [ ] Create 5 Blade email templates
- [ ] Test email rendering

### Automation
- [ ] Create SendBalancePaymentReminders command
- [ ] Add scheduler entry in routes/console.php
- [ ] Test dry-run mode
- [ ] Test actual sending

### Testing
- [ ] Test with existing deposit bookings
- [ ] Verify emails sent correctly
- [ ] Verify database logging
- [ ] Verify scheduler works
- [ ] Check cron job (already exists from passenger reminders)

---

## ⏱️ Estimated Timeline

| Phase | Duration | Dependencies |
|-------|----------|--------------|
| Phase 1: Models | 30 min | None |
| Phase 2: Email Templates | 45 min | Phase 1 |
| Phase 3: Command | 45 min | Phase 1, 2 |
| Phase 4: Scheduler | 10 min | Phase 3 |
| Phase 5: Testing | 30 min | All phases |
| **TOTAL** | **2.5 hours** | - |

---

## 🔄 Reminder Logic Summary

**Balance payment reminders sent when:**

1. **45-Day Reminder** (38-45 days before balance due)
   - Friendly reminder
   - Shows payment summary
   - Emphasizes early payment benefits

2. **35-Day Reminder** (31-37 days before balance due)
   - More important tone
   - Highlights approaching deadline
   - Includes payment methods

3. **30-Day Reminder** (15-30 days before balance due)
   - Urgent tone
   - Final notice before due date
   - Strong call-to-action

4. **Overdue Reminder** (after balance due date)
   - Critical urgency
   - Booking at risk notice
   - Immediate action required

**Each reminder only sent once per booking** (tracked in payment_reminders table)

---

## 💡 Additional Enhancements (Future)

1. **SMS Reminders** - Integrate with SMS gateway for critical reminders
2. **WhatsApp Notifications** - Send payment links via WhatsApp
3. **Auto-cancellation** - Cancel bookings with severely overdue balance
4. **Payment Portal** - Build frontend for balance payments
5. **Filament Dashboard** - Admin widgets for balance tracking
6. **Multi-currency** - Support balance payments in different currencies

---

**Implementation Date:** 2025-12-24
**Status:** Ready to implement
**Effort:** 2.5 hours
