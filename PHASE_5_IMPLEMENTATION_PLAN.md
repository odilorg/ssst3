# Phase 5: Email-Based Balance Payment System
## Detailed Implementation Plan

---

## 📅 6-Day Implementation Schedule

**Total Duration:** 6 working days (1 week)
**Goal:** Automated balance payment reminders with tokenized payment links

---

## Day 1: Payment Token System & Database
**Duration:** 8 hours
**Goal:** Create secure token generation and validation system

### Tasks

#### 1.1 Create Payment Tokens Migration (30 min)
**File:** `database/migrations/2025_01_XX_000001_create_payment_tokens_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->string('token', 64)->unique();
            $table->enum('type', ['balance_payment', 'booking_access'])->default('balance_payment');
            $table->timestamp('expires_at');
            $table->timestamp('used_at')->nullable();
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();

            $table->index(['token', 'expires_at']);
            $table->index('booking_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_tokens');
    }
};
```

**Run:**
```bash
php artisan make:migration create_payment_tokens_table
# Copy code above, then:
php artisan migrate
```

---

#### 1.2 Add Reminder Tracking to Bookings (30 min)
**File:** `database/migrations/2025_01_XX_000002_add_reminder_tracking_to_bookings.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->timestamp('reminder_7days_sent_at')->nullable()->after('amount_remaining');
            $table->timestamp('reminder_3days_sent_at')->nullable()->after('reminder_7days_sent_at');
            $table->timestamp('reminder_1day_sent_at')->nullable()->after('reminder_3days_sent_at');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['reminder_7days_sent_at', 'reminder_3days_sent_at', 'reminder_1day_sent_at']);
        });
    }
};
```

**Run:**
```bash
php artisan make:migration add_reminder_tracking_to_bookings
# Copy code above, then:
php artisan migrate
```

---

#### 1.3 Create PaymentToken Model (30 min)
**File:** `app/Models/PaymentToken.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'token',
        'type',
        'expires_at',
        'used_at',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'used_at' => 'datetime',
    ];

    /**
     * Get the booking that owns the payment token
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Check if token is expired
     */
    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    /**
     * Check if token has been used
     */
    public function isUsed(): bool
    {
        return !is_null($this->used_at);
    }

    /**
     * Check if token is valid (not expired and not used)
     */
    public function isValid(): bool
    {
        return !$this->isExpired() && !$this->isUsed();
    }

    /**
     * Scope to get only valid tokens
     */
    public function scopeValid($query)
    {
        return $query->where('expires_at', '>', now())
            ->whereNull('used_at');
    }
}
```

**Run:**
```bash
php artisan make:model PaymentToken
# Copy code above
```

---

#### 1.4 Create PaymentTokenService (2 hours)
**File:** `app/Services/PaymentTokenService.php`

```php
<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\PaymentToken;
use Illuminate\Support\Str;

class PaymentTokenService
{
    /**
     * Generate a secure payment token for a booking
     *
     * @param Booking $booking
     * @param int $expiryDays Number of days until token expires
     * @return string The unhashed token for URL
     */
    public function generateBalancePaymentToken(Booking $booking, int $expiryDays = 7): string
    {
        // Generate cryptographically secure random token
        $token = Str::random(64);

        // Store hashed version for security (prevents token theft from DB)
        PaymentToken::create([
            'booking_id' => $booking->id,
            'token' => hash('sha256', $token),
            'type' => 'balance_payment',
            'expires_at' => now()->addDays($expiryDays),
        ]);

        \Log::info('Payment token generated', [
            'booking_id' => $booking->id,
            'expires_at' => now()->addDays($expiryDays),
        ]);

        // Return unhashed token for URL
        return $token;
    }

    /**
     * Validate token and return associated booking
     *
     * @param string $token The unhashed token from URL
     * @return Booking|null
     */
    public function validateToken(string $token): ?Booking
    {
        $hashedToken = hash('sha256', $token);

        $paymentToken = PaymentToken::where('token', $hashedToken)
            ->valid()
            ->first();

        if (!$paymentToken) {
            \Log::warning('Invalid payment token attempt', [
                'token_prefix' => substr($token, 0, 10) . '...',
            ]);
            return null;
        }

        return $paymentToken->booking()
            ->with(['tour', 'payments'])
            ->first();
    }

    /**
     * Mark token as used
     *
     * @param string $token
     * @param string|null $ipAddress
     * @param string|null $userAgent
     * @return bool
     */
    public function markTokenAsUsed(string $token, ?string $ipAddress = null, ?string $userAgent = null): bool
    {
        $hashedToken = hash('sha256', $token);

        $updated = PaymentToken::where('token', $hashedToken)
            ->whereNull('used_at')
            ->update([
                'used_at' => now(),
                'ip_address' => $ipAddress,
                'user_agent' => $userAgent,
            ]);

        if ($updated) {
            \Log::info('Payment token marked as used', [
                'ip_address' => $ipAddress,
            ]);
        }

        return $updated > 0;
    }

    /**
     * Clean up expired tokens (run weekly via scheduler)
     *
     * @param int $olderThanDays Delete tokens older than X days
     * @return int Number of tokens deleted
     */
    public function cleanupExpiredTokens(int $olderThanDays = 30): int
    {
        $deleted = PaymentToken::where('expires_at', '<', now()->subDays($olderThanDays))
            ->delete();

        if ($deleted > 0) {
            \Log::info("Cleaned up {$deleted} expired payment tokens");
        }

        return $deleted;
    }

    /**
     * Get all valid tokens for a booking
     *
     * @param Booking $booking
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getValidTokensForBooking(Booking $booking)
    {
        return PaymentToken::where('booking_id', $booking->id)
            ->valid()
            ->get();
    }

    /**
     * Invalidate all tokens for a booking (e.g., after payment completed)
     *
     * @param Booking $booking
     * @return int Number of tokens invalidated
     */
    public function invalidateBookingTokens(Booking $booking): int
    {
        return PaymentToken::where('booking_id', $booking->id)
            ->whereNull('used_at')
            ->update(['used_at' => now()]);
    }
}
```

**Run:**
```bash
# Create the service file manually or:
mkdir -p app/Services
# Copy code above to app/Services/PaymentTokenService.php
```

---

#### 1.5 Unit Tests for Token Service (2 hours)
**File:** `tests/Unit/PaymentTokenServiceTest.php`

```php
<?php

namespace Tests\Unit;

use App\Models\Booking;
use App\Models\PaymentToken;
use App\Services\PaymentTokenService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentTokenServiceTest extends TestCase
{
    use RefreshDatabase;

    protected PaymentTokenService $tokenService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tokenService = new PaymentTokenService();
    }

    /** @test */
    public function it_can_generate_a_payment_token()
    {
        $booking = Booking::factory()->create();

        $token = $this->tokenService->generateBalancePaymentToken($booking);

        $this->assertNotNull($token);
        $this->assertEquals(64, strlen($token));
        $this->assertDatabaseHas('payment_tokens', [
            'booking_id' => $booking->id,
            'type' => 'balance_payment',
        ]);
    }

    /** @test */
    public function it_stores_hashed_token_in_database()
    {
        $booking = Booking::factory()->create();

        $token = $this->tokenService->generateBalancePaymentToken($booking);

        $hashedToken = hash('sha256', $token);
        $this->assertDatabaseHas('payment_tokens', [
            'token' => $hashedToken,
        ]);
    }

    /** @test */
    public function it_can_validate_a_token()
    {
        $booking = Booking::factory()->create();
        $token = $this->tokenService->generateBalancePaymentToken($booking);

        $validatedBooking = $this->tokenService->validateToken($token);

        $this->assertNotNull($validatedBooking);
        $this->assertEquals($booking->id, $validatedBooking->id);
    }

    /** @test */
    public function it_returns_null_for_invalid_token()
    {
        $invalidToken = 'invalid-token-string';

        $booking = $this->tokenService->validateToken($invalidToken);

        $this->assertNull($booking);
    }

    /** @test */
    public function it_returns_null_for_expired_token()
    {
        $booking = Booking::factory()->create();
        $token = $this->tokenService->generateBalancePaymentToken($booking);

        // Manually expire the token
        PaymentToken::where('booking_id', $booking->id)
            ->update(['expires_at' => now()->subDay()]);

        $validatedBooking = $this->tokenService->validateToken($token);

        $this->assertNull($validatedBooking);
    }

    /** @test */
    public function it_can_mark_token_as_used()
    {
        $booking = Booking::factory()->create();
        $token = $this->tokenService->generateBalancePaymentToken($booking);

        $result = $this->tokenService->markTokenAsUsed($token, '127.0.0.1', 'Test Agent');

        $this->assertTrue($result);
        $this->assertDatabaseHas('payment_tokens', [
            'booking_id' => $booking->id,
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Test Agent',
        ]);
        $this->assertDatabaseMissing('payment_tokens', [
            'booking_id' => $booking->id,
            'used_at' => null,
        ]);
    }

    /** @test */
    public function it_does_not_validate_used_token()
    {
        $booking = Booking::factory()->create();
        $token = $this->tokenService->generateBalancePaymentToken($booking);

        $this->tokenService->markTokenAsUsed($token);
        $validatedBooking = $this->tokenService->validateToken($token);

        $this->assertNull($validatedBooking);
    }

    /** @test */
    public function it_can_cleanup_expired_tokens()
    {
        $booking = Booking::factory()->create();

        // Create an old expired token
        PaymentToken::create([
            'booking_id' => $booking->id,
            'token' => hash('sha256', 'old-token'),
            'type' => 'balance_payment',
            'expires_at' => now()->subDays(31),
        ]);

        $deleted = $this->tokenService->cleanupExpiredTokens(30);

        $this->assertEquals(1, $deleted);
    }

    /** @test */
    public function it_can_invalidate_all_booking_tokens()
    {
        $booking = Booking::factory()->create();

        // Create multiple tokens
        $this->tokenService->generateBalancePaymentToken($booking);
        $this->tokenService->generateBalancePaymentToken($booking);

        $invalidated = $this->tokenService->invalidateBookingTokens($booking);

        $this->assertEquals(2, $invalidated);
        $this->assertDatabaseMissing('payment_tokens', [
            'booking_id' => $booking->id,
            'used_at' => null,
        ]);
    }
}
```

**Run:**
```bash
php artisan make:test PaymentTokenServiceTest --unit
# Copy code above
php artisan test --filter=PaymentTokenServiceTest
```

---

#### 1.6 Update Booking Model (30 min)
**File:** `app/Models/Booking.php` (add relationship)

```php
// Add to Booking model:

/**
 * Get the payment tokens for the booking
 */
public function paymentTokens()
{
    return $this->hasMany(PaymentToken::class);
}

/**
 * Check if booking has valid payment token
 */
public function hasValidPaymentToken(): bool
{
    return $this->paymentTokens()
        ->where('expires_at', '>', now())
        ->whereNull('used_at')
        ->exists();
}
```

---

### Day 1 Checklist

- [ ] Create `payment_tokens` migration
- [ ] Run migration successfully
- [ ] Create reminder tracking migration
- [ ] Run migration successfully
- [ ] Create `PaymentToken` model with relationships
- [ ] Create `PaymentTokenService` with all methods
- [ ] Write unit tests for token service
- [ ] Run tests - all passing
- [ ] Update `Booking` model with token relationship
- [ ] Manual test: Generate token, validate, mark as used

**Deliverable:** Working token generation and validation system with tests

---

## Day 2: Reminder Scheduler & Queue System
**Duration:** 8 hours
**Goal:** Automated daily reminders for bookings with balance due

### Tasks

#### 2.1 Create SendPaymentReminders Command (1.5 hours)
**File:** `app/Console/Commands/SendPaymentReminders.php`

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
                            {--days= : Only send reminders for specific days (comma-separated)}';

    protected $description = 'Send payment reminders for bookings with balance due';

    public function handle()
    {
        $dryRun = $this->option('dry-run');
        $specificDays = $this->option('days');

        $this->info('🔍 Searching for bookings with balance due...');
        $this->newLine();

        // Reminder windows: 7 days, 3 days, 1 day before tour
        $reminderWindows = [
            7 => 'reminder_7days_sent_at',
            3 => 'reminder_3days_sent_at',
            1 => 'reminder_1day_sent_at',
        ];

        // Filter to specific days if provided
        if ($specificDays) {
            $daysArray = explode(',', $specificDays);
            $reminderWindows = array_filter($reminderWindows, function ($key) use ($daysArray) {
                return in_array($key, $daysArray);
            }, ARRAY_FILTER_USE_KEY);
        }

        $totalQueued = 0;

        foreach ($reminderWindows as $days => $column) {
            $targetDate = now()->addDays($days)->toDateString();

            $this->info("📅 Checking {$days}-day reminder window (tour date: {$targetDate})");

            // Find bookings with balance due on target date
            $bookings = Booking::whereDate('tour_start_date', $targetDate)
                ->where('payment_status', '!=', 'paid_in_full')
                ->where('amount_remaining', '>', 0)
                ->whereIn('status', ['confirmed', 'payment_pending'])
                ->whereNull($column) // Haven't sent this reminder yet
                ->with('tour')
                ->get();

            if ($bookings->isEmpty()) {
                $this->warn("  No bookings found requiring {$days}-day reminder");
                continue;
            }

            $this->info("  Found {$bookings->count()} booking(s) requiring reminder");

            foreach ($bookings as $booking) {
                if ($dryRun) {
                    $this->line("  [DRY RUN] Would send {$days}-day reminder for:");
                } else {
                    $this->line("  ✉️  Queueing {$days}-day reminder for:");
                }

                $this->line("      Booking #{$booking->id} - {$booking->customer_name}");
                $this->line("      Tour: {$booking->tour->title}");
                $this->line("      Balance: $" . number_format($booking->amount_remaining, 2));
                $this->line("      Email: {$booking->customer_email}");

                if (!$dryRun) {
                    // Dispatch reminder job
                    SendBalancePaymentReminder::dispatch($booking, $days);

                    // Mark reminder as sent
                    $booking->update([$column => now()]);

                    $totalQueued++;
                }

                $this->newLine();
            }
        }

        if ($dryRun) {
            $this->info('✅ Dry run complete. No reminders were actually sent.');
        } else {
            $this->info("✅ Total reminders queued: {$totalQueued}");
            $this->info('💡 Jobs will be processed by queue worker');
        }

        return Command::SUCCESS;
    }
}
```

**Run:**
```bash
php artisan make:command SendPaymentReminders
# Copy code above

# Test dry run:
php artisan reminders:payment --dry-run

# Test specific days:
php artisan reminders:payment --dry-run --days=7,3
```

---

#### 2.2 Create SendBalancePaymentReminder Job (1.5 hours)
**File:** `app/Jobs/SendBalancePaymentReminder.php`

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
                ]);
                return;
            }

            // Generate payment token (7-day expiry)
            $token = $tokenService->generateBalancePaymentToken($this->booking, 7);

            // Generate payment URL
            $paymentUrl = route('balance-payment.review', ['token' => $token]);

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
        ]);

        // TODO: Send notification to admin about failed reminder
    }
}
```

**Run:**
```bash
php artisan make:job SendBalancePaymentReminder
# Copy code above
```

---

#### 2.3 Register Command in Kernel (30 min)
**File:** `app/Console/Kernel.php`

```php
// In the schedule() method:

protected function schedule(Schedule $schedule): void
{
    // Send payment reminders daily at 9:00 AM Tashkent time
    $schedule->command('reminders:payment')
        ->dailyAt('09:00')
        ->timezone('Asia/Tashkent')
        ->withoutOverlapping()
        ->onOneServer()
        ->appendOutputTo(storage_path('logs/reminders.log'));

    // Clean up old payment tokens weekly on Sunday at 2 AM
    $schedule->call(function () {
        app(\App\Services\PaymentTokenService::class)->cleanupExpiredTokens();
    })
        ->weekly()
        ->sundays()
        ->at('02:00')
        ->timezone('Asia/Tashkent');
}
```

**Test:**
```bash
# List scheduled commands
php artisan schedule:list

# Run scheduler manually
php artisan schedule:run

# Test reminder command
php artisan reminders:payment --dry-run
```

---

#### 2.4 Setup Queue Configuration (1 hour)
**File:** `config/queue.php` (verify configuration)

```php
// Ensure database queue is configured
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

**Create jobs table:**
```bash
php artisan queue:table
php artisan migrate
```

**Create failed_jobs table (if not exists):**
```bash
php artisan queue:failed-table
php artisan migrate
```

---

#### 2.5 Create Test Data & Test Flow (2 hours)
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
        $tour = Tour::first() ?? Tour::factory()->create();

        // Create booking 7 days from now
        Booking::factory()->create([
            'tour_id' => $tour->id,
            'tour_start_date' => now()->addDays(7),
            'customer_email' => 'test7days@example.com',
            'customer_name' => 'John Doe (7 days)',
            'total_amount' => 1000,
            'amount_paid' => 300,
            'amount_remaining' => 700,
            'payment_status' => 'deposit_paid',
            'status' => 'confirmed',
        ]);

        // Create booking 3 days from now
        Booking::factory()->create([
            'tour_id' => $tour->id,
            'tour_start_date' => now()->addDays(3),
            'customer_email' => 'test3days@example.com',
            'customer_name' => 'Jane Smith (3 days)',
            'total_amount' => 1500,
            'amount_paid' => 450,
            'amount_remaining' => 1050,
            'payment_status' => 'deposit_paid',
            'status' => 'confirmed',
        ]);

        // Create booking 1 day from now
        Booking::factory()->create([
            'tour_id' => $tour->id,
            'tour_start_date' => now()->addDay(),
            'customer_email' => 'test1day@example.com',
            'customer_name' => 'Bob Johnson (1 day)',
            'total_amount' => 800,
            'amount_paid' => 240,
            'amount_remaining' => 560,
            'payment_status' => 'deposit_paid',
            'status' => 'confirmed',
        ]);

        $this->command->info('✅ Created 3 test bookings for reminder testing');
    }
}
```

**Run:**
```bash
php artisan make:seeder PaymentReminderTestSeeder
# Copy code above

php artisan db:seed --class=PaymentReminderTestSeeder

# Test the flow:
php artisan reminders:payment --dry-run
```

---

#### 2.6 Test Queue Processing (1 hour)

**Terminal 1 - Start queue worker:**
```bash
php artisan queue:work --queue=default --tries=3 --timeout=90 --verbose
```

**Terminal 2 - Trigger reminders:**
```bash
php artisan reminders:payment
```

**Verify:**
- Jobs appear in queue
- Worker processes jobs
- Logs show success messages
- Check `storage/logs/laravel.log`

---

### Day 2 Checklist

- [ ] Create `SendPaymentReminders` command with dry-run option
- [ ] Create `SendBalancePaymentReminder` job with retry logic
- [ ] Register command in Kernel scheduler
- [ ] Setup queue tables (`jobs`, `failed_jobs`)
- [ ] Create test data seeder
- [ ] Test dry-run mode
- [ ] Test actual queue processing
- [ ] Verify logs for both success and failure cases
- [ ] Test scheduler: `php artisan schedule:run`

**Deliverable:** Working scheduler that finds bookings and queues reminder jobs

---

## Day 3: Tokenized Payment Flow
**Duration:** 8 hours
**Goal:** Customer can click email link and complete payment

### Tasks

#### 3.1 Create BalancePaymentController (2 hours)
**File:** `app/Http/Controllers/BalancePaymentController.php`

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
                'message' => 'This payment link has expired or is invalid. Please contact us for assistance.',
            ]);
        }

        // Check if balance still exists
        if ($booking->amount_remaining <= 0) {
            return view('payments.already-paid', [
                'booking' => $booking,
                'title' => 'Already Paid',
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
                'payment_url' => $result['payment_url'],
            ]);

            // Redirect to OCTO payment page
            return redirect($result['payment_url']);

        } catch (\Exception $e) {
            \Log::error('Balance payment initialization failed', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->with('error', 'Failed to initialize payment. Please try again or contact support.');
        }
    }
}
```

**Run:**
```bash
php artisan make:controller BalancePaymentController
# Copy code above
```

---

#### 3.2 Add Routes (15 min)
**File:** `routes/web.php`

```php
// Add these routes (no auth middleware - tokenized access)
Route::get('/payment/balance/{token}', [BalancePaymentController::class, 'review'])
    ->name('balance-payment.review');

Route::post('/payment/balance/{token}/initialize', [BalancePaymentController::class, 'initialize'])
    ->name('balance-payment.initialize');
```

---

#### 3.3 Create Balance Review Template (2 hours)
**File:** `resources/views/payments/balance-review.blade.php`

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
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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

        .security-badge svg {
            width: 20px;
            height: 20px;
            color: #10b981;
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

        @media (max-width: 600px) {
            .container {
                border-radius: 0;
            }

            .amount-box .amount {
                font-size: 36px;
            }

            .security-badges {
                flex-direction: column;
                align-items: center;
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
                <div style="background: #fee2e2; border-left: 4px solid #ef4444; padding: 15px; margin-bottom: 20px; border-radius: 4px; color: #991b1b;">
                    {{ session('error') }}
                </div>
            @endif

            <div class="amount-box">
                <div class="label">Balance Payment</div>
                <div class="amount">${{ number_format($amount, 2) }}</div>
                <div class="tour-name">{{ $booking->tour->title }}</div>
            </div>

            <div class="details">
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
            </div>

            <div class="details">
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
                ℹ️ You will be redirected to our secure payment gateway (OCTO) to complete the transaction. We support UzCard, Humo, Visa, and Mastercard.
            </div>

            <div class="security-badges">
                <div class="security-badge">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                    <span>Secure Payment</span>
                </div>
                <div class="security-badge">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                    <span>SSL Encrypted</span>
                </div>
                <div class="security-badge">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                    </svg>
                    <span>OCTO Gateway</span>
                </div>
            </div>

            <form action="{{ route('balance-payment.initialize', $token) }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-primary">
                    🔐 Proceed to Secure Payment - ${{ number_format($amount, 2) }}
                </button>
            </form>

            <p style="text-align: center; color: #6b7280; font-size: 12px; margin-top: 20px;">
                Need help? Contact us at <a href="mailto:info@silkroadtravel.com" style="color: #667eea;">info@silkroadtravel.com</a>
                or WhatsApp: <a href="https://wa.me/998901234567" style="color: #667eea;">+998 90 123 4567</a>
            </p>
        </div>
    </div>
</body>
</html>
```

---

#### 3.4 Create Token Expired View (1 hour)
**File:** `resources/views/payments/token-expired.blade.php`

```blade
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Payment Link Expired' }}</title>
    <style>
        /* Copy same base styles from balance-review.blade.php */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
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
        }

        .btn-secondary {
            background: #6b7280;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon">⏰</div>
        <div class="content">
            <h1>{{ $title }}</h1>
            <p>{{ $message }}</p>
            <a href="mailto:info@silkroadtravel.com" class="btn">✉️ Contact Support</a>
            <a href="https://wa.me/998901234567" class="btn btn-secondary">💬 WhatsApp</a>
        </div>
    </div>
</body>
</html>
```

---

#### 3.5 Create Already Paid View (30 min)
**File:** `resources/views/payments/already-paid.blade.php`

```blade
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Complete</title>
    <style>
        /* Copy same base styles */
        body {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }
        /* ... rest of styles similar to token-expired ... */
    </style>
</head>
<body>
    <div class="container">
        <div class="icon">✅</div>
        <div class="content">
            <h1>{{ $title }}</h1>
            <p>{{ $message }}</p>
            <p><strong>Booking Reference:</strong> {{ $booking->booking_reference }}</p>
            <p><strong>Tour:</strong> {{ $booking->tour->title }}</p>
            <a href="{{ route('home') }}" class="btn">🏠 Back to Home</a>
        </div>
    </div>
</body>
</html>
```

---

#### 3.6 Integration Testing (1.5 hours)

**Test Script:** `test_balance_payment_flow.php`

```php
<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Booking;
use App\Services\PaymentTokenService;

echo "🧪 Testing Balance Payment Flow\n\n";

// 1. Find a booking with balance
$booking = Booking::where('amount_remaining', '>', 0)->first();

if (!$booking) {
    echo "❌ No booking found with balance. Run seeder first.\n";
    exit(1);
}

echo "✓ Found booking #{$booking->id}\n";
echo "  Balance: $" . number_format($booking->amount_remaining, 2) . "\n\n";

// 2. Generate token
$tokenService = new PaymentTokenService();
$token = $tokenService->generateBalancePaymentToken($booking);

echo "✓ Generated payment token\n";
echo "  Token (first 10 chars): " . substr($token, 0, 10) . "...\n\n";

// 3. Generate URL
$url = route('balance-payment.review', ['token' => $token]);

echo "✓ Payment URL generated:\n";
echo "  {$url}\n\n";

echo "📋 Next Steps:\n";
echo "1. Copy the URL above\n";
echo "2. Open in browser (incognito mode)\n";
echo "3. Review booking details\n";
echo "4. Click 'Proceed to Secure Payment'\n";
echo "5. Complete OCTO payment flow\n\n";

echo "✅ Test complete!\n";
```

**Run:**
```bash
php test_balance_payment_flow.php
```

---

### Day 3 Checklist

- [ ] Create `BalancePaymentController` with review and initialize methods
- [ ] Add routes for balance payment
- [ ] Create `balance-review.blade.php` template
- [ ] Create `token-expired.blade.php` template
- [ ] Create `already-paid.blade.php` template
- [ ] Create test script for flow
- [ ] Test token validation
- [ ] Test expired token handling
- [ ] Test already paid scenario
- [ ] Test OCTO payment initialization
- [ ] Mobile responsive check

**Deliverable:** Working tokenized payment flow from email link to OCTO

---

## Day 4-6: Email Templates & PDF Generation

**Due to length constraints, I'll create a separate detailed plan for Days 4-6 covering:**

- Day 4: Email templates (reminder + confirmation)
- Day 5: PDF generation (receipt + booking confirmation)
- Day 6: End-to-end testing, deployment, monitoring

Would you like me to:
1. Continue with the detailed Days 4-6 plan?
2. Start implementing Day 1 tasks now?
3. Review and adjust the plan?

---

**Status:** Day 1-3 detailed plan complete ✅
**Next:** Days 4-6 detailed plan OR start implementation
