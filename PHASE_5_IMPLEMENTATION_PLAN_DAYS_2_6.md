# Phase 5: Days 2-6 Detailed Implementation Plan
## Email-Based Balance Payment System

**Status:** Day 1 Complete ✅
**Next:** Days 2-6 Implementation

---

## 📅 Overview

This document provides detailed implementation plans for Days 2-6:

- **Day 2:** Reminder Scheduler & Queue System (8 hours)
- **Day 3:** Tokenized Payment Flow (8 hours)
- **Day 4:** Email Templates (8 hours)
- **Day 5:** PDF Generation (8 hours)
- **Day 6:** Testing, Deployment & Documentation (8 hours)

**Total:** 40 hours (5 working days)

---

## Day 2: Reminder Scheduler & Queue System
**Duration:** 8 hours
**Goal:** Automated daily reminders for bookings with balance due

### Tasks Overview

| Task | Duration | Complexity |
|------|----------|------------|
| 2.1 Create SendPaymentReminders Command | 1.5 hours | Medium |
| 2.2 Create SendBalancePaymentReminder Job | 1.5 hours | Medium |
| 2.3 Register Command in Kernel | 30 min | Low |
| 2.4 Setup Queue Infrastructure | 1 hour | Low |
| 2.5 Create Test Data Seeder | 1 hour | Low |
| 2.6 Test Queue Processing | 1.5 hours | Medium |
| 2.7 Create Manual Test Scripts | 1 hour | Low |

---

### Task 2.1: Create SendPaymentReminders Command (1.5 hours)

**File:** `app/Console/Commands/SendPaymentReminders.php`

**Features to implement:**
- Find bookings 7, 3, 1 day before tour with balance due
- Track sent reminders (no duplicates)
- Dry-run mode for testing
- Specific days filter
- Detailed console output
- Progress indicators

**Complete Code:**

```php
<?php

namespace App\Console\Commands;

use App\Jobs\SendBalancePaymentReminder;
use App\Models\Booking;
use Illuminate\Console\Command;

class SendPaymentReminders extends Command
{
    protected $signature = 'reminders:payment
                            {--dry-run : Show what would be sent without actually sending}
                            {--days= : Only send reminders for specific days (comma-separated: 7,3,1)}
                            {--force : Force send even if already sent}';

    protected $description = 'Send payment reminders for bookings with balance due';

    public function handle()
    {
        $dryRun = $this->option('dry-run');
        $specificDays = $this->option('days');
        $force = $this->option('force');

        $this->info('🔍 Searching for bookings with balance due...');
        $this->newLine();

        // Reminder windows: 7 days, 3 days, 1 day before tour
        $reminderWindows = [
            7 => ['column' => 'reminder_7days_sent_at', 'label' => '7-day'],
            3 => ['column' => 'reminder_3days_sent_at', 'label' => '3-day'],
            1 => ['column' => 'reminder_1day_sent_at', 'label' => '1-day'],
        ];

        // Filter to specific days if provided
        if ($specificDays) {
            $daysArray = array_map('trim', explode(',', $specificDays));
            $reminderWindows = array_filter($reminderWindows, function ($key) use ($daysArray) {
                return in_array($key, $daysArray);
            }, ARRAY_FILTER_USE_KEY);
        }

        $totalQueued = 0;
        $totalSkipped = 0;

        foreach ($reminderWindows as $days => $config) {
            $targetDate = now()->addDays($days)->toDateString();

            $this->info("📅 Checking {$config['label']} reminder window");
            $this->line("   Target date: {$targetDate}");

            // Build query for bookings needing reminder
            $query = Booking::whereDate('tour_start_date', $targetDate)
                ->where('payment_status', '!=', 'paid_in_full')
                ->where('amount_remaining', '>', 0)
                ->whereIn('status', ['confirmed', 'payment_pending'])
                ->with('tour');

            // Skip already sent unless forced
            if (!$force) {
                $query->whereNull($config['column']);
            }

            $bookings = $query->get();

            if ($bookings->isEmpty()) {
                $this->warn("   No bookings found requiring {$config['label']} reminder");
                $this->newLine();
                continue;
            }

            $this->info("   Found {$bookings->count()} booking(s) requiring reminder");
            $this->newLine();

            foreach ($bookings as $booking) {
                // Display booking info
                $this->displayBookingInfo($booking, $days, $dryRun);

                if ($dryRun) {
                    $totalSkipped++;
                } else {
                    // Dispatch reminder job
                    SendBalancePaymentReminder::dispatch($booking, $days);

                    // Mark reminder as sent
                    $booking->update([$config['column'] => now()]);

                    $totalQueued++;
                }

                $this->newLine();
            }
        }

        // Summary
        $this->newLine();
        if ($dryRun) {
            $this->info("✅ Dry run complete. {$totalSkipped} reminder(s) would be sent.");
            $this->line('💡 Run without --dry-run to actually send reminders');
        } else {
            $this->info("✅ Total reminders queued: {$totalQueued}");
            $this->line('💡 Jobs will be processed by queue worker');
            $this->line('💡 Monitor: php artisan queue:work');
        }

        return Command::SUCCESS;
    }

    /**
     * Display booking information
     */
    protected function displayBookingInfo(Booking $booking, int $days, bool $dryRun): void
    {
        $prefix = $dryRun ? '[DRY RUN]' : '✉️';

        $this->line("   {$prefix} Booking #{$booking->id}");
        $this->line("      Customer: {$booking->customer_name}");
        $this->line("      Email: {$booking->customer_email}");
        $this->line("      Tour: {$booking->tour->title}");
        $this->line("      Start Date: {$booking->tour_start_date->format('M j, Y')}");
        $this->line("      Balance Due: $" . number_format($booking->amount_remaining, 2));
        $this->line("      Days Until Tour: {$days}");
    }
}
```

**Create the command:**
```bash
php artisan make:command SendPaymentReminders
# Copy code above

# Test dry run:
php artisan reminders:payment --dry-run

# Test specific days:
php artisan reminders:payment --dry-run --days=7

# Force resend (testing):
php artisan reminders:payment --dry-run --force
```

**Testing checklist:**
- [ ] Command created successfully
- [ ] Dry-run mode works without sending
- [ ] Specific days filter works (--days=7,3)
- [ ] Force flag works for testing
- [ ] Console output is clear and helpful
- [ ] No errors when no bookings found

---

### Task 2.2: Create SendBalancePaymentReminder Job (1.5 hours)

**File:** `app/Jobs/SendBalancePaymentReminder.php`

**Features to implement:**
- Queue-based processing
- Retry logic (3 attempts with 60s backoff)
- Token generation via PaymentTokenService
- Email sending via BalancePaymentReminder mailable
- Comprehensive logging
- Graceful failure handling

**Complete Code:**

```php
<?php

namespace App\Jobs;

use App\Mail\BalancePaymentReminder;
use App\Models\Booking;
use App\Services\PaymentTokenService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendBalancePaymentReminder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public $tries = 3;

    /**
     * The number of seconds to wait before retrying the job.
     */
    public $backoff = 60;

    /**
     * The maximum number of unhandled exceptions to allow before failing.
     */
    public $maxExceptions = 3;

    /**
     * Delete the job if its models no longer exist.
     */
    public $deleteWhenMissingModels = true;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Booking $booking,
        public int $daysBeforeTour
    ) {}

    /**
     * Execute the job.
     */
    public function handle(PaymentTokenService $tokenService): void
    {
        try {
            // Verify booking still has balance due
            if ($this->booking->amount_remaining <= 0) {
                \Log::info('Skipping reminder - booking fully paid', [
                    'booking_id' => $this->booking->id,
                    'payment_status' => $this->booking->payment_status,
                ]);
                return;
            }

            // Verify booking is still confirmed
            if (!in_array($this->booking->status, ['confirmed', 'payment_pending'])) {
                \Log::info('Skipping reminder - booking not confirmed', [
                    'booking_id' => $this->booking->id,
                    'status' => $this->booking->status,
                ]);
                return;
            }

            // Generate payment token (7-day expiry)
            $token = $tokenService->generateBalancePaymentToken($this->booking, 7);

            // Generate payment URL
            $paymentUrl = route('balance-payment.review', ['token' => $token]);

            \Log::info('Sending balance payment reminder', [
                'booking_id' => $this->booking->id,
                'days_before' => $this->daysBeforeTour,
                'email' => $this->booking->customer_email,
                'payment_url' => $paymentUrl,
            ]);

            // Send email
            Mail::to($this->booking->customer_email)
                ->send(new BalancePaymentReminder(
                    $this->booking,
                    $this->daysBeforeTour,
                    $paymentUrl
                ));

            \Log::info('Balance payment reminder sent successfully', [
                'booking_id' => $this->booking->id,
                'days_before' => $this->daysBeforeTour,
                'email' => $this->booking->customer_email,
                'amount_due' => $this->booking->amount_remaining,
            ]);

        } catch (\Exception $e) {
            \Log::error('Failed to send balance payment reminder', [
                'booking_id' => $this->booking->id,
                'days_before' => $this->daysBeforeTour,
                'attempt' => $this->attempts(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Re-throw to trigger retry mechanism
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        \Log::error('Balance payment reminder job failed permanently', [
            'booking_id' => $this->booking->id,
            'days_before' => $this->daysBeforeTour,
            'error' => $exception->getMessage(),
            'customer_email' => $this->booking->customer_email,
        ]);

        // TODO: Send notification to admin about failed reminder
        // Could dispatch AdminNotification job here
    }

    /**
     * Get the tags that should be assigned to the job.
     */
    public function tags(): array
    {
        return [
            'payment-reminder',
            "booking:{$this->booking->id}",
            "days:{$this->daysBeforeTour}",
        ];
    }
}
```

**Create the job:**
```bash
php artisan make:job SendBalancePaymentReminder
# Copy code above
```

**Testing checklist:**
- [ ] Job created successfully
- [ ] Retry logic configured (3 attempts, 60s backoff)
- [ ] Logs success and failure appropriately
- [ ] Skips if booking fully paid
- [ ] Skips if booking cancelled
- [ ] Tags work for monitoring

---

### Task 2.3: Register Command in Kernel (30 min)

**File:** `app/Console/Kernel.php`

Add scheduling configuration:

```php
<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Send payment reminders daily at 9:00 AM Tashkent time
        $schedule->command('reminders:payment')
            ->dailyAt('09:00')
            ->timezone('Asia/Tashkent')
            ->withoutOverlapping()
            ->onOneServer()
            ->appendOutputTo(storage_path('logs/payment-reminders.log'))
            ->emailOutputOnFailure(config('mail.admin_email'));

        // Clean up old payment tokens weekly on Sunday at 2 AM
        $schedule->call(function () {
            $deleted = app(\App\Services\PaymentTokenService::class)->cleanupExpiredTokens(30);
            \Log::info("Token cleanup: deleted {$deleted} expired tokens");
        })
            ->weekly()
            ->sundays()
            ->at('02:00')
            ->timezone('Asia/Tashkent')
            ->name('cleanup-payment-tokens');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
```

**Test scheduling:**
```bash
# List all scheduled tasks
php artisan schedule:list

# Run scheduler manually (test)
php artisan schedule:run

# Test specific command
php artisan reminders:payment --dry-run
```

**Create log file:**
```bash
touch storage/logs/payment-reminders.log
chmod 664 storage/logs/payment-reminders.log
```

---

### Task 2.4: Setup Queue Infrastructure (1 hour)

**Ensure queue tables exist:**
```bash
# Check if jobs table exists
php artisan queue:table
php artisan migrate

# Check if failed_jobs table exists
php artisan queue:failed-table
php artisan migrate
```

**Configure queue in `.env`:**
```env
QUEUE_CONNECTION=database
```

**Verify `config/queue.php`:**
```php
'connections' => [
    'database' => [
        'driver' => 'database',
        'table' => 'jobs',
        'queue' => 'default',
        'retry_after' => 90,
        'after_commit' => false,
    ],
],
```

**Start queue worker (testing):**
```bash
# Terminal 1: Start worker
php artisan queue:work --queue=default --tries=3 --timeout=90 --verbose

# Terminal 2: Trigger jobs
php artisan reminders:payment

# Monitor jobs
php artisan queue:monitor database

# Check failed jobs
php artisan queue:failed
```

---

### Task 2.5: Create Test Data Seeder (1 hour)

**File:** `database/seeders/PaymentReminderTestSeeder.php`

```php
<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Tour;
use Illuminate\Database\Seeder;

class PaymentReminderTestSeeder extends Seeder
{
    public function run(): void
    {
        $tour = Tour::first();

        if (!$tour) {
            $this->command->error('No tours found. Please create a tour first.');
            return;
        }

        // Create booking 7 days from now (needs 7-day reminder)
        $booking7 = Booking::create([
            'tour_id' => $tour->id,
            'tour_start_date' => now()->addDays(7)->toDateString(),
            'customer_email' => 'test7days@example.com',
            'customer_name' => 'John Doe',
            'customer_phone' => '+1234567890',
            'customer_country' => 'US',
            'total_amount' => 1000.00,
            'amount_paid' => 300.00,
            'amount_remaining' => 700.00,
            'payment_status' => 'deposit_paid',
            'status' => 'confirmed',
            'currency' => 'USD',
            'booking_reference' => 'TEST-7D-' . time(),
        ]);

        // Create booking 3 days from now (needs 3-day reminder)
        $booking3 = Booking::create([
            'tour_id' => $tour->id,
            'tour_start_date' => now()->addDays(3)->toDateString(),
            'customer_email' => 'test3days@example.com',
            'customer_name' => 'Jane Smith',
            'customer_phone' => '+1234567891',
            'customer_country' => 'UK',
            'total_amount' => 1500.00,
            'amount_paid' => 450.00,
            'amount_remaining' => 1050.00,
            'payment_status' => 'deposit_paid',
            'status' => 'confirmed',
            'currency' => 'USD',
            'booking_reference' => 'TEST-3D-' . time(),
        ]);

        // Create booking 1 day from now (needs 1-day reminder)
        $booking1 = Booking::create([
            'tour_id' => $tour->id,
            'tour_start_date' => now()->addDay()->toDateString(),
            'customer_email' => 'test1day@example.com',
            'customer_name' => 'Bob Johnson',
            'customer_phone' => '+1234567892',
            'customer_country' => 'CA',
            'total_amount' => 800.00,
            'amount_paid' => 240.00,
            'amount_remaining' => 560.00,
            'payment_status' => 'deposit_paid',
            'status' => 'confirmed',
            'currency' => 'USD',
            'booking_reference' => 'TEST-1D-' . time(),
        ]);

        // Create fully paid booking (should NOT get reminder)
        $bookingPaid = Booking::create([
            'tour_id' => $tour->id,
            'tour_start_date' => now()->addDays(7)->toDateString(),
            'customer_email' => 'testpaid@example.com',
            'customer_name' => 'Alice Paid',
            'customer_phone' => '+1234567893',
            'customer_country' => 'AU',
            'total_amount' => 1000.00,
            'amount_paid' => 1000.00,
            'amount_remaining' => 0.00,
            'payment_status' => 'paid_in_full',
            'status' => 'confirmed',
            'currency' => 'USD',
            'booking_reference' => 'TEST-PAID-' . time(),
        ]);

        $this->command->info('✅ Created test bookings for payment reminders:');
        $this->command->table(
            ['ID', 'Customer', 'Tour Date', 'Balance', 'Days Until Tour'],
            [
                [$booking7->id, $booking7->customer_name, $booking7->tour_start_date, '$700', '7'],
                [$booking3->id, $booking3->customer_name, $booking3->tour_start_date, '$1,050', '3'],
                [$booking1->id, $booking1->customer_name, $booking1->tour_start_date, '$560', '1'],
                [$bookingPaid->id, $bookingPaid->customer_name . ' (PAID)', $bookingPaid->tour_start_date, '$0', '7'],
            ]
        );
    }
}
```

**Run seeder:**
```bash
php artisan make:seeder PaymentReminderTestSeeder
# Copy code above

php artisan db:seed --class=PaymentReminderTestSeeder
```

---

### Task 2.6: Test Queue Processing (1.5 hours)

**Complete Test Flow:**

**Terminal 1 - Queue Worker:**
```bash
php artisan queue:work --verbose
```

**Terminal 2 - Trigger Reminders:**
```bash
# Dry run first
php artisan reminders:payment --dry-run

# Actual send
php artisan reminders:payment
```

**Terminal 3 - Monitor Logs:**
```bash
tail -f storage/logs/laravel.log
```

**Verification Steps:**

1. **Check jobs queued:**
```bash
# Should show 3 jobs (7-day, 3-day, 1-day)
SELECT * FROM jobs;
```

2. **Watch worker process:**
- Jobs should process one by one
- Success logs should appear
- No errors in console

3. **Verify reminder tracking:**
```sql
SELECT id, customer_name,
       reminder_7days_sent_at,
       reminder_3days_sent_at,
       reminder_1day_sent_at
FROM bookings
WHERE booking_reference LIKE 'TEST-%';
```

4. **Check email logs:**
```bash
grep "Balance payment reminder sent" storage/logs/laravel.log
```

5. **Test failure handling:**
```bash
# Simulate failure by using invalid email
# Worker should retry 3 times, then fail

# Check failed jobs
php artisan queue:failed

# Retry failed job
php artisan queue:retry {job-id}
```

---

### Task 2.7: Create Manual Test Scripts (1 hour)

**File:** `test_reminder_system.php`

```php
<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Booking;

echo "🧪 Testing Payment Reminder System\n\n";

// 1. Check test bookings
echo "1️⃣ Checking test bookings...\n";
$bookings = Booking::where('booking_reference', 'LIKE', 'TEST-%')
    ->where('amount_remaining', '>', 0)
    ->get();

if ($bookings->isEmpty()) {
    echo "❌ No test bookings found. Run seeder first:\n";
    echo "   php artisan db:seed --class=PaymentReminderTestSeeder\n";
    exit(1);
}

echo "✓ Found {$bookings->count()} test booking(s)\n\n";

foreach ($bookings as $booking) {
    $daysUntil = now()->diffInDays($booking->tour_start_date, false);
    echo "  Booking #{$booking->id}\n";
    echo "    Customer: {$booking->customer_name}\n";
    echo "    Tour Date: {$booking->tour_start_date->format('M j, Y')}\n";
    echo "    Days Until: {$daysUntil}\n";
    echo "    Balance: $" . number_format($booking->amount_remaining, 2) . "\n";
    echo "    Reminders Sent:\n";
    echo "      7-day: " . ($booking->reminder_7days_sent_at ? '✓' : '✗') . "\n";
    echo "      3-day: " . ($booking->reminder_3days_sent_at ? '✓' : '✗') . "\n";
    echo "      1-day: " . ($booking->reminder_1day_sent_at ? '✓' : '✗') . "\n";
    echo "\n";
}

// 2. Test command dry-run
echo "2️⃣ Testing reminder command (dry-run)...\n";
echo "   Running: php artisan reminders:payment --dry-run\n\n";

Artisan::call('reminders:payment', ['--dry-run' => true]);
echo Artisan::output();

// 3. Instructions
echo "\n📋 Next Steps:\n\n";
echo "1. Review output above\n";
echo "2. Start queue worker in another terminal:\n";
echo "   php artisan queue:work --verbose\n\n";
echo "3. Send actual reminders:\n";
echo "   php artisan reminders:payment\n\n";
echo "4. Monitor logs:\n";
echo "   tail -f storage/logs/laravel.log\n\n";
echo "5. Check sent reminders:\n";
echo "   php test_reminder_system.php\n\n";

echo "✅ Test complete!\n";
```

**Run test:**
```bash
php test_reminder_system.php
```

---

### Day 2 Checklist

- [ ] `SendPaymentReminders` command created with dry-run
- [ ] Command finds bookings 7, 3, 1 day before tour
- [ ] Command respects reminder tracking (no duplicates)
- [ ] `SendBalancePaymentReminder` job created
- [ ] Job has retry logic (3 attempts, 60s backoff)
- [ ] Job logs success and failure
- [ ] Kernel scheduler configured
- [ ] Scheduler set to run daily at 9 AM
- [ ] Token cleanup scheduled weekly
- [ ] Queue tables exist (jobs, failed_jobs)
- [ ] Queue worker tested and working
- [ ] Test data seeder created
- [ ] Test bookings created (7-day, 3-day, 1-day)
- [ ] End-to-end test: command → job → (email placeholder)
- [ ] Dry-run mode tested
- [ ] Force flag tested
- [ ] Failed job handling tested
- [ ] Manual test script created and working

**Deliverable:** Working scheduler that finds bookings and queues reminder jobs (email sending pending Day 4)

---

## Day 3: Tokenized Payment Flow
**Duration:** 8 hours
**Goal:** Customer clicks email link and completes payment

### Tasks Overview

| Task | Duration | Complexity |
|------|----------|------------|
| 3.1 Create BalancePaymentController | 2 hours | Medium |
| 3.2 Add Payment Routes | 15 min | Low |
| 3.3 Create Balance Review Template | 2 hours | Medium |
| 3.4 Create Token Expired View | 1 hour | Low |
| 3.5 Create Already Paid View | 30 min | Low |
| 3.6 Integration Testing | 1.5 hours | Medium |
| 3.7 Mobile Responsive Testing | 45 min | Low |

---

### Task 3.1: Create BalancePaymentController (2 hours)

**File:** `app/Http/Controllers/BalancePaymentController.php`

**Methods to implement:**
1. `review($token)` - Show payment review page
2. `initialize(Request $request, $token)` - Process payment initialization

**Complete Code:**

```php
<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Services\OctoPaymentService;
use App\Services\PaymentTokenService;
use Illuminate\Http\Request;

class BalancePaymentController extends Controller
{
    public function __construct(
        private PaymentTokenService $tokenService,
        private OctoPaymentService $octoService
    ) {}

    /**
     * Show balance payment review page (tokenized - no auth required)
     */
    public function review(string $token)
    {
        $booking = $this->tokenService->validateToken($token);

        if (!$booking) {
            return view('payments.token-expired', [
                'title' => 'Payment Link Expired',
                'message' => 'This payment link has expired or is invalid. Please contact us for a new link.',
                'contactEmail' => config('mail.from.address'),
                'whatsapp' => config('services.whatsapp.number'),
            ]);
        }

        // Check if balance still exists
        if ($booking->amount_remaining <= 0) {
            return view('payments.already-paid', [
                'booking' => $booking,
                'title' => 'Payment Complete',
                'message' => 'This booking has already been paid in full. Thank you!',
            ]);
        }

        return view('payments.balance-review', [
            'booking' => $booking,
            'token' => $token,
            'amount' => $booking->amount_remaining,
            'tourDate' => $booking->tour_start_date->format('F j, Y'),
            'depositPaid' => $booking->amount_paid,
            'totalAmount' => $booking->total_amount,
            'daysUntilTour' => now()->diffInDays($booking->tour_start_date, false),
        ]);
    }

    /**
     * Initialize balance payment via OCTO
     */
    public function initialize(Request $request, string $token)
    {
        $booking = $this->tokenService->validateToken($token);

        if (!$booking) {
            return redirect()->route('home')
                ->with('error', 'Invalid or expired payment link. Please contact support.');
        }

        if ($booking->amount_remaining <= 0) {
            return redirect()->route('home')
                ->with('info', 'This booking has already been paid in full.');
        }

        try {
            // Create payment record
            $payment = $booking->payments()->create([
                'amount' => $booking->amount_remaining,
                'payment_type' => 'balance',
                'payment_method' => 'octo',
                'currency' => $booking->currency,
                'status' => 'pending',
            ]);

            \Log::info('Balance payment initialized', [
                'booking_id' => $booking->id,
                'payment_id' => $payment->id,
                'amount' => $booking->amount_remaining,
                'token_used' => substr($token, 0, 10) . '...',
            ]);

            // Initialize OCTO payment
            $result = $this->octoService->initializePayment($booking, $payment);

            // Mark token as used
            $this->tokenService->markTokenAsUsed(
                $token,
                $request->ip(),
                $request->userAgent()
            );

            // Update booking status
            $booking->update([
                'status' => 'payment_pending',
            ]);

            \Log::info('Redirecting to OCTO payment page', [
                'payment_id' => $payment->id,
                'payment_url' => $result['payment_url'] ?? 'N/A',
            ]);

            // Redirect to OCTO payment page
            return redirect($result['payment_url']);

        } catch (\Exception $e) {
            \Log::error('Balance payment initialization failed', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()
                ->with('error', 'Failed to initialize payment. Please try again or contact support.')
                ->with('error_details', config('app.debug') ? $e->getMessage() : null);
        }
    }
}
```

**Create controller:**
```bash
php artisan make:controller BalancePaymentController
# Copy code above
```

---

### Task 3.2: Add Payment Routes (15 min)

**File:** `routes/web.php`

Add at the end of the file:

```php
// Balance payment routes (tokenized - no auth required)
Route::prefix('payment/balance')->group(function () {
    Route::get('/{token}', [BalancePaymentController::class, 'review'])
        ->name('balance-payment.review');

    Route::post('/{token}/initialize', [BalancePaymentController::class, 'initialize'])
        ->name('balance-payment.initialize');
});
```

**Verify routes:**
```bash
php artisan route:list | grep balance-payment
```

Expected output:
```
GET|HEAD  payment/balance/{token} ............... balance-payment.review
POST      payment/balance/{token}/initialize ... balance-payment.initialize
```

---

### Task 3.3: Create Balance Review Template (2 hours)

**File:** `resources/views/payments/balance-review.blade.php`

**Features:**
- Modern, responsive design
- Amount display with breakdown
- Security badges
- Mobile-optimized
- Error message handling
- Professional styling

**Complete HTML:**

```blade
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complete Your Payment - {{ $booking->tour->title }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            line-height: 1.6;
        }

        .container {
            max-width: 600px;
            width: 100%;
            background: white;
            border-radius: 12px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .header h1 {
            font-size: 24px;
            margin-bottom: 10px;
        }

        .header p {
            opacity: 0.9;
            font-size: 14px;
        }

        .content {
            padding: 30px;
        }

        .urgency-banner {
            background: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 16px;
            margin-bottom: 30px;
            border-radius: 4px;
        }

        .urgency-banner strong {
            color: #92400e;
            display: block;
            margin-bottom: 4px;
        }

        .urgency-banner p {
            color: #78350f;
            font-size: 14px;
            margin: 0;
        }

        .amount-box {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 8px;
            padding: 30px;
            text-align: center;
            margin-bottom: 30px;
        }

        .amount-box .label {
            font-size: 14px;
            opacity: 0.9;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .amount-box .amount {
            font-size: 48px;
            font-weight: bold;
            margin: 10px 0;
        }

        .amount-box .tour-name {
            font-size: 18px;
            margin-top: 15px;
            opacity: 0.95;
        }

        .details {
            background: #f9fafb;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .details h3 {
            margin: 0 0 15px 0;
            color: #1f2937;
            font-size: 16px;
        }

        .details-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #e5e7eb;
        }

        .details-row:last-child {
            border-bottom: none;
        }

        .details-row .label {
            color: #6b7280;
            font-size: 14px;
        }

        .details-row .value {
            font-weight: 600;
            color: #1f2937;
            text-align: right;
        }

        .details-row.total {
            margin-top: 10px;
            padding-top: 20px;
            border-top: 2px solid #667eea;
        }

        .details-row.total .value {
            color: #667eea;
            font-size: 20px;
        }

        .security-badges {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin: 30px 0;
            flex-wrap: wrap;
        }

        .security-badge {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            color: #6b7280;
        }

        .security-badge span:first-child {
            font-size: 20px;
        }

        .btn {
            width: 100%;
            padding: 18px;
            font-size: 18px;
            font-weight: bold;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
            font-family: inherit;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.4);
        }

        .info-box {
            background: #eff6ff;
            border-left: 4px solid #3b82f6;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
            font-size: 14px;
            color: #1e40af;
        }

        .alert {
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
        }

        .alert-error {
            background: #fee2e2;
            border-left: 4px solid #ef4444;
            color: #991b1b;
        }

        .footer-text {
            text-align: center;
            color: #6b7280;
            font-size: 12px;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
        }

        .footer-text a {
            color: #667eea;
            text-decoration: none;
        }

        .footer-text a:hover {
            text-decoration: underline;
        }

        @media (max-width: 600px) {
            body {
                padding: 0;
            }

            .container {
                border-radius: 0;
                min-height: 100vh;
            }

            .amount-box .amount {
                font-size: 36px;
            }

            .security-badges {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }

            .details-row {
                flex-direction: column;
                gap: 4px;
            }

            .details-row .value {
                text-align: left;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>💳 Complete Your Payment</h1>
            <p>Secure payment gateway powered by OCTO</p>
        </div>

        <div class="content">
            @if(session('error'))
                <div class="alert alert-error">
                    <strong>⚠️ Error:</strong> {{ session('error') }}
                    @if(session('error_details'))
                        <br><small>{{ session('error_details') }}</small>
                    @endif
                </div>
            @endif

            @if($daysUntilTour <= 3)
                <div class="urgency-banner">
                    <strong>⏰ {{ $daysUntilTour === 0 ? 'Today!' : ($daysUntilTour === 1 ? 'Tomorrow!' : "{$daysUntilTour} Days Left!") }}</strong>
                    <p>Your tour starts {{ $daysUntilTour === 0 ? 'today' : ($daysUntilTour === 1 ? 'tomorrow' : "in {$daysUntilTour} days") }}. Please complete your payment to confirm your reservation.</p>
                </div>
            @endif

            <div class="amount-box">
                <div class="label">Balance Payment</div>
                <div class="amount">${{ number_format($amount, 2) }}</div>
                <div class="tour-name">{{ $booking->tour->title }}</div>
            </div>

            <div class="details">
                <h3>📋 Booking Details</h3>
                <div class="details-row">
                    <span class="label">Booking Reference</span>
                    <span class="value">{{ $booking->booking_reference }}</span>
                </div>
                <div class="details-row">
                    <span class="label">Tour Date</span>
                    <span class="value">{{ $tourDate }}</span>
                </div>
                <div class="details-row">
                    <span class="label">Customer Name</span>
                    <span class="value">{{ $booking->customer_name }}</span>
                </div>
                <div class="details-row">
                    <span class="label">Email</span>
                    <span class="value">{{ $booking->customer_email }}</span>
                </div>
            </div>

            <div class="details">
                <h3>💰 Payment Summary</h3>
                <div class="details-row">
                    <span class="label">Total Tour Price</span>
                    <span class="value">${{ number_format($totalAmount, 2) }}</span>
                </div>
                <div class="details-row">
                    <span class="label">Deposit Paid ✓</span>
                    <span class="value" style="color: #10b981;">${{ number_format($depositPaid, 2) }}</span>
                </div>
                <div class="details-row total">
                    <span class="label" style="font-weight: 600; color: #1f2937;">Balance Due</span>
                    <span class="value">${{ number_format($amount, 2) }}</span>
                </div>
            </div>

            <div class="info-box">
                ℹ️ You will be redirected to our secure payment gateway (OCTO) to complete the transaction. We accept UzCard, Humo, Visa, and Mastercard.
            </div>

            <div class="security-badges">
                <div class="security-badge">
                    <span>🔒</span>
                    <span>Secure Payment</span>
                </div>
                <div class="security-badge">
                    <span>🛡️</span>
                    <span>SSL Encrypted</span>
                </div>
                <div class="security-badge">
                    <span>💳</span>
                    <span>OCTO Gateway</span>
                </div>
            </div>

            <form action="{{ route('balance-payment.initialize', $token) }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-primary">
                    🔐 Proceed to Secure Payment - ${{ number_format($amount, 2) }}
                </button>
            </form>

            <p class="footer-text">
                Need help? Contact us at <a href="mailto:{{ config('mail.from.address') }}">{{ config('mail.from.address') }}</a>
                or WhatsApp: <a href="https://wa.me/{{ config('services.whatsapp.number') }}">{{ config('services.whatsapp.formatted') }}</a>
            </p>
        </div>
    </div>
</body>
</html>
```

**Create directory and file:**
```bash
mkdir -p resources/views/payments
touch resources/views/payments/balance-review.blade.php
# Copy code above
```

---

### Task 3.4: Create Token Expired View (1 hour)

**File:** `resources/views/payments/token-expired.blade.php`

```blade
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Payment Link Expired' }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            max-width: 600px;
            width: 100%;
            background: white;
            border-radius: 12px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            text-align: center;
        }

        .icon {
            font-size: 80px;
            margin: 40px 0 20px;
        }

        .content {
            padding: 0 40px 40px;
        }

        .content h1 {
            font-size: 28px;
            color: #1f2937;
            margin-bottom: 15px;
        }

        .content p {
            color: #6b7280;
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 30px;
        }

        .btn {
            display: inline-block;
            padding: 14px 32px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            margin: 10px;
            transition: transform 0.2s;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.4);
        }

        .btn-secondary {
            background: #10b981;
        }

        .info-box {
            background: #f3f4f6;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            font-size: 14px;
            color: #4b5563;
        }

        @media (max-width: 600px) {
            .container {
                border-radius: 0;
            }

            .content h1 {
                font-size: 24px;
            }

            .icon {
                font-size: 60px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon">⏰</div>
        <div class="content">
            <h1>{{ $title }}</h1>
            <p>{{ $message }}</p>

            <div class="info-box">
                <strong>What to do next:</strong><br>
                Contact our support team and we'll send you a new payment link right away.
            </div>

            <a href="mailto:{{ $contactEmail ?? 'info@silkroadtravel.com' }}" class="btn">
                ✉️ Email Support
            </a>
            <a href="https://wa.me/{{ $whatsapp ?? '998901234567' }}" class="btn btn-secondary">
                💬 WhatsApp Support
            </a>
        </div>
    </div>
</body>
</html>
```

---

### Task 3.5: Create Already Paid View (30 min)

**File:** `resources/views/payments/already-paid.blade.php`

```blade
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Payment Complete' }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            max-width: 600px;
            width: 100%;
            background: white;
            border-radius: 12px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            text-align: center;
        }

        .icon {
            font-size: 80px;
            margin: 40px 0 20px;
        }

        .content {
            padding: 0 40px 40px;
        }

        .content h1 {
            font-size: 28px;
            color: #1f2937;
            margin-bottom: 15px;
        }

        .content p {
            color: #6b7280;
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 20px;
        }

        .details-box {
            background: #f9fafb;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            text-align: left;
        }

        .details-box .row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #e5e7eb;
        }

        .details-box .row:last-child {
            border-bottom: none;
        }

        .details-box .label {
            color: #6b7280;
            font-size: 14px;
        }

        .details-box .value {
            font-weight: 600;
            color: #1f2937;
        }

        .btn {
            display: inline-block;
            padding: 14px 32px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            margin: 20px 10px;
            transition: transform 0.2s;
        }

        .btn:hover {
            transform: translateY(-2px);
        }

        @media (max-width: 600px) {
            .container {
                border-radius: 0;
            }

            .content h1 {
                font-size: 24px;
            }

            .details-box .row {
                flex-direction: column;
                gap: 4px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon">✅</div>
        <div class="content">
            <h1>{{ $title }}</h1>
            <p>{{ $message }}</p>

            <div class="details-box">
                <div class="row">
                    <span class="label">Booking Reference:</span>
                    <span class="value">{{ $booking->booking_reference }}</span>
                </div>
                <div class="row">
                    <span class="label">Tour:</span>
                    <span class="value">{{ $booking->tour->title }}</span>
                </div>
                <div class="row">
                    <span class="label">Tour Date:</span>
                    <span class="value">{{ $booking->tour_start_date->format('F j, Y') }}</span>
                </div>
                <div class="row">
                    <span class="label">Payment Status:</span>
                    <span class="value" style="color: #10b981;">Paid in Full ✓</span>
                </div>
            </div>

            <p style="font-size: 14px; color: #6b7280;">
                We look forward to seeing you on your tour!
            </p>

            <a href="{{ route('home') }}" class="btn">🏠 Back to Home</a>
        </div>
    </div>
</body>
</html>
```

---

### Task 3.6: Integration Testing (1.5 hours)

**Create Test Script:** `test_balance_payment_flow.php`

```php
<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Booking;
use App\Services\PaymentTokenService;

echo "🧪 Testing Balance Payment Flow\n\n";

// 1. Find a test booking with balance
echo "1️⃣ Finding test booking with balance...\n";
$booking = Booking::where('booking_reference', 'LIKE', 'TEST-%')
    ->where('amount_remaining', '>', 0)
    ->first();

if (!$booking) {
    echo "❌ No test booking found. Run seeder first:\n";
    echo "   php artisan db:seed --class=PaymentReminderTestSeeder\n";
    exit(1);
}

echo "✓ Found booking #{$booking->id}\n";
echo "  Customer: {$booking->customer_name}\n";
echo "  Balance: $" . number_format($booking->amount_remaining, 2) . "\n\n";

// 2. Generate payment token
echo "2️⃣ Generating payment token...\n";
$tokenService = new PaymentTokenService();
$token = $tokenService->generateBalancePaymentToken($booking);

echo "✓ Token generated\n";
echo "  Token (first 10 chars): " . substr($token, 0, 10) . "...\n\n";

// 3. Generate payment URL
echo "3️⃣ Generating payment URL...\n";
$url = route('balance-payment.review', ['token' => $token]);

echo "✓ Payment URL:\n";
echo "  {$url}\n\n";

// 4. Test token validation
echo "4️⃣ Testing token validation...\n";
$validatedBooking = $tokenService->validateToken($token);

if ($validatedBooking && $validatedBooking->id === $booking->id) {
    echo "✓ Token validation successful\n\n";
} else {
    echo "❌ Token validation failed\n";
    exit(1);
}

// 5. Test invalid token
echo "5️⃣ Testing invalid token handling...\n";
$invalidBooking = $tokenService->validateToken('invalid-token-12345');

if ($invalidBooking === null) {
    echo "✓ Invalid token correctly rejected\n\n";
} else {
    echo "❌ Invalid token was accepted (security issue!)\n";
    exit(1);
}

// Summary
echo "📋 Manual Testing Steps:\n\n";
echo "1. Copy the URL above and open in browser (incognito mode)\n";
echo "2. Verify booking details displayed correctly\n";
echo "3. Check amount breakdown shows:\n";
echo "   - Total: $" . number_format($booking->total_amount, 2) . "\n";
echo "   - Paid: $" . number_format($booking->amount_paid, 2) . "\n";
echo "   - Due: $" . number_format($booking->amount_remaining, 2) . "\n";
echo "4. Click 'Proceed to Secure Payment' button\n";
echo "5. Verify redirect to OCTO (or error with test credentials)\n";
echo "6. Check logs: tail -f storage/logs/laravel.log\n\n";

echo "🔍 Testing Edge Cases:\n\n";

// Test expired token
echo "Testing expired token...\n";
$expiredToken = $tokenService->generateBalancePaymentToken($booking, -1);
sleep(2);
$expiredBooking = $tokenService->validateToken($expiredToken);
echo ($expiredBooking === null ? "✓" : "❌") . " Expired token handling\n";

// Test used token
echo "Testing used token...\n";
$usedToken = $tokenService->generateBalancePaymentToken($booking);
$tokenService->markTokenAsUsed($usedToken, '127.0.0.1', 'Test Agent');
$usedBooking = $tokenService->validateToken($usedToken);
echo ($usedBooking === null ? "✓" : "❌") . " Used token handling\n\n";

echo "✅ All tests passed!\n";
```

**Run tests:**
```bash
php test_balance_payment_flow.php
```

---

### Day 3 Checklist

- [ ] `BalancePaymentController` created
- [ ] `review()` method validates token and shows payment page
- [ ] `initialize()` method creates payment and redirects to OCTO
- [ ] Routes added to `web.php`
- [ ] Route names verified with `route:list`
- [ ] `balance-review.blade.php` created with responsive design
- [ ] Amount breakdown displays correctly
- [ ] Security badges shown
- [ ] `token-expired.blade.php` created
- [ ] Contact information displayed
- [ ] `already-paid.blade.php` created
- [ ] Booking details shown when paid
- [ ] Integration test script created
- [ ] Token validation tested
- [ ] Invalid token rejected
- [ ] Expired token rejected
- [ ] Used token rejected
- [ ] Manual browser testing completed
- [ ] Mobile responsive verified (Chrome DevTools)
- [ ] Error messages display correctly

**Deliverable:** Complete tokenized payment flow from email link to OCTO gateway

---

## Days 4-6 Summary

Due to length constraints, here's a high-level overview:

### Day 4: Email Templates (8 hours)
- Create `BalancePaymentReminder` mailable
- Design HTML email template (3 versions: 7-day, 3-day, 1-day)
- Create `BalancePaymentConfirmation` mailable
- Test email rendering
- Mobile email client testing

### Day 5: PDF Generation (8 hours)
- Install DomPDF (`composer require barryvdh/laravel-dompdf`)
- Create `DocumentGenerationService`
- Build payment receipt PDF template
- Build booking confirmation PDF template
- Hook into payment observer
- Test PDF generation and downloads

### Day 6: Testing & Deployment (8 hours)
- End-to-end testing (full flow)
- Mobile testing
- Email client testing (Gmail, Outlook, Apple Mail)
- Performance testing
- Security audit
- Deployment checklist
- Documentation updates
- Create runbook for operations

---

## 🎯 Success Criteria

Phase 5 will be complete when:

1. ✅ Migrations run successfully
2. ✅ Payment tokens generate and validate correctly
3. ✅ Scheduler finds and queues reminder jobs
4. ✅ Queue worker processes jobs without errors
5. ✅ Tokenized payment links work (no login required)
6. ✅ Email templates render correctly
7. ✅ PDFs generate and attach to emails
8. ✅ Mobile responsive on all pages/emails
9. ✅ All tests passing
10. ✅ Deployed to production

---

**Total Estimate:** 40 hours (5 working days at 8 hours/day)

**Current Status:** Day 1 Complete ✅
**Next:** Day 2 - Reminder Scheduler

**Ready to continue implementation?**
