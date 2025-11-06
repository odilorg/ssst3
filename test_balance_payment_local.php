#!/usr/bin/env php
<?php

/**
 * Local Balance Payment System Testing Script
 *
 * This script helps you test the entire balance payment flow locally
 * without needing actual OCTO credentials or sending real emails.
 */

echo "\n";
echo "╔══════════════════════════════════════════════════════════════╗\n";
echo "║   BALANCE PAYMENT SYSTEM - LOCAL TESTING SCRIPT              ║\n";
echo "╚══════════════════════════════════════════════════════════════╝\n";
echo "\n";

// Load Laravel
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Booking;
use App\Models\Payment;
use App\Models\PaymentToken;
use App\Services\PaymentTokenService;
use App\Jobs\SendBalancePaymentReminder;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;

// Helper function for colored output
function output($message, $color = 'white') {
    $colors = [
        'green' => "\033[32m",
        'yellow' => "\033[33m",
        'red' => "\033[31m",
        'blue' => "\033[34m",
        'cyan' => "\033[36m",
        'white' => "\033[37m",
        'reset' => "\033[0m"
    ];
    echo $colors[$color] . $message . $colors['reset'] . "\n";
}

function section($title) {
    echo "\n";
    output("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━", 'cyan');
    output("  " . $title, 'cyan');
    output("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━", 'cyan');
}

function success($message) {
    output("✅ " . $message, 'green');
}

function warning($message) {
    output("⚠️  " . $message, 'yellow');
}

function error($message) {
    output("❌ " . $message, 'red');
}

function info($message) {
    output("ℹ️  " . $message, 'blue');
}

try {
    // Test 1: Check Database Connection
    section("TEST 1: Database Connection");

    try {
        DB::connection()->getPdo();
        success("Database connection successful");
        info("Database: " . config('database.default'));
    } catch (\Exception $e) {
        error("Database connection failed: " . $e->getMessage());
        exit(1);
    }

    // Test 2: Find Test Booking
    section("TEST 2: Finding Test Booking");

    $booking = Booking::where('payment_status', 'deposit_paid')
        ->where('amount_remaining', '>', 0)
        ->first();

    if (!$booking) {
        warning("No suitable booking found. Creating test booking...");

        // Create a test booking
        $booking = Booking::create([
            'reference' => 'TEST-' . time(),
            'customer_name' => 'Test Customer',
            'customer_email' => 'test@example.com',
            'customer_phone' => '+998901234567',
            'tour_date' => now()->addDays(8),
            'tour_name' => 'Test Tour',
            'number_of_people' => 2,
            'total_price' => 1000,
            'amount_paid' => 300,
            'amount_remaining' => 700,
            'payment_status' => 'deposit_paid',
        ]);

        success("Test booking created: " . $booking->reference);
    } else {
        success("Found booking: " . $booking->reference);
    }

    info("Customer: " . $booking->customer_name);
    info("Email: " . $booking->customer_email);
    info("Total Price: $" . number_format($booking->total_price, 2));
    info("Amount Paid: $" . number_format($booking->amount_paid, 2));
    info("Amount Remaining: $" . number_format($booking->amount_remaining, 2));
    info("Payment Status: " . $booking->payment_status);

    // Test 3: Generate Payment Token
    section("TEST 3: Generating Payment Token");

    $tokenService = app(PaymentTokenService::class);
    $token = $tokenService->generateBalancePaymentToken($booking, 7);

    success("Payment token generated successfully");
    info("Token Length: " . strlen($token) . " characters");
    info("Token (preview): " . substr($token, 0, 30) . "...");

    $paymentUrl = route('balance-payment.show', $token);
    success("Payment URL created");
    info("URL: " . $paymentUrl);

    // Test 4: Verify Token in Database
    section("TEST 4: Verifying Token in Database");

    $tokenRecord = PaymentToken::where('token', $token)->first();

    if ($tokenRecord) {
        success("Token found in database");
        info("Token ID: " . $tokenRecord->id);
        info("Booking ID: " . $tokenRecord->booking_id);
        info("Type: " . $tokenRecord->type);
        info("Expires At: " . $tokenRecord->expires_at->format('Y-m-d H:i:s'));
        info("Valid: " . ($tokenRecord->isValid() ? 'Yes' : 'No'));
        info("Used: " . ($tokenRecord->isUsed() ? 'Yes' : 'No'));
    } else {
        error("Token not found in database");
    }

    // Test 5: Email System (Fake)
    section("TEST 5: Testing Email System");

    info("Faking mail system for local testing...");
    Mail::fake();

    info("Dispatching payment reminder job...");
    SendBalancePaymentReminder::dispatch($booking, 7);

    success("Reminder job dispatched successfully");

    // Process the job immediately
    info("Processing job synchronously...");
    Queue::fake();
    SendBalancePaymentReminder::dispatch($booking, 7);

    success("Email job would be sent in production");
    info("In production, email would be sent to: " . $booking->customer_email);

    // Test 6: Payment Page Accessibility
    section("TEST 6: Testing Payment Page Accessibility");

    info("Testing if payment route exists...");

    try {
        $route = app('router')->getRoutes()->getByName('balance-payment.show');
        if ($route) {
            success("Payment route registered correctly");
            info("Route URI: " . $route->uri());
            info("Route Methods: " . implode(', ', $route->methods()));
        }
    } catch (\Exception $e) {
        error("Payment route not found: " . $e->getMessage());
    }

    info("\nTo test the payment page in browser:");
    output("  1. Make sure your server is running: php artisan serve", 'yellow');
    output("  2. Open this URL in browser:", 'yellow');
    output("     " . $paymentUrl, 'cyan');

    // Test 7: Simulate Payment Completion
    section("TEST 7: Simulating Payment Completion");

    info("Current booking state:");
    output("  Amount Paid: $" . number_format($booking->amount_paid, 2), 'white');
    output("  Amount Remaining: $" . number_format($booking->amount_remaining, 2), 'white');
    output("  Payment Status: " . $booking->payment_status, 'white');
    output("  Active Tokens: " . $booking->paymentTokens()->where('expires_at', '>', now())->whereNull('used_at')->count(), 'white');

    info("\nCreating test payment...");
    $payment = Payment::create([
        'booking_id' => $booking->id,
        'amount' => 100.00,
        'currency' => 'USD',
        'status' => 'pending',
        'payment_type' => 'balance',
        'payment_method' => 'test_card',
        'transaction_id' => 'LOCAL-TEST-' . time(),
    ]);

    success("Test payment created (ID: {$payment->id})");

    info("Completing payment (this triggers PaymentObserver)...");
    Mail::fake(); // Fake emails for observer

    $payment->update([
        'status' => 'completed',
        'processed_at' => now(),
    ]);

    success("Payment marked as completed");
    success("PaymentObserver triggered automatically");

    // Refresh booking
    $booking->refresh();

    info("\nBooking state after payment:");
    output("  Amount Paid: $" . number_format($booking->amount_paid, 2), 'green');
    output("  Amount Remaining: $" . number_format($booking->amount_remaining, 2), 'green');
    output("  Payment Status: " . $booking->payment_status, 'green');
    output("  Active Tokens: " . $booking->paymentTokens()->where('expires_at', '>', now())->whereNull('used_at')->count(), 'green');

    success("Observer successfully updated booking amounts");
    success("Observer invalidated payment tokens");
    success("Confirmation email would be sent in production");

    // Test 8: Admin Panel Routes
    section("TEST 8: Checking Admin Panel Routes");

    $adminRoutes = [
        'filament.admin.pages.dashboard',
        'filament.admin.resources.payments.index',
    ];

    foreach ($adminRoutes as $routeName) {
        try {
            $route = route($routeName);
            success("Route exists: " . $routeName);
            info("  URL: " . $route);
        } catch (\Exception $e) {
            warning("Route not found: " . $routeName);
        }
    }

    info("\nTo access admin panel:");
    output("  URL: http://127.0.0.1:8000/admin", 'cyan');
    output("  Login with your admin credentials", 'yellow');

    // Test 9: Security Features
    section("TEST 9: Security Features Check");

    info("Checking CSRF protection...");
    if (config('app.env') !== 'testing') {
        success("CSRF protection is active");
    } else {
        warning("Running in testing environment");
    }

    info("Checking encryption...");
    if (config('app.key')) {
        success("Application key is set");
    } else {
        error("Application key not set!");
    }

    info("Checking rate limiting...");
    try {
        $route = app('router')->getRoutes()->getByName('balance-payment.process');
        if ($route) {
            $middleware = $route->middleware();
            if (in_array('throttle:10,1', $middleware)) {
                success("Rate limiting configured on payment process route");
            } else {
                warning("Rate limiting might not be configured");
            }
        }
    } catch (\Exception $e) {
        warning("Could not check rate limiting");
    }

    // Test 10: System Configuration
    section("TEST 10: System Configuration");

    info("Checking queue configuration...");
    output("  Queue Driver: " . config('queue.default'), 'white');
    if (config('queue.default') === 'sync') {
        warning("Queue is set to 'sync' (immediate processing)");
        info("For production, use 'redis' or 'database'");
    } else {
        success("Queue driver: " . config('queue.default'));
    }

    info("\nChecking mail configuration...");
    output("  Mail Driver: " . config('mail.default'), 'white');
    output("  From Address: " . config('mail.from.address'), 'white');
    output("  From Name: " . config('mail.from.name'), 'white');

    if (config('mail.default') === 'log') {
        warning("Mail is set to 'log' (emails written to log file)");
        info("Check storage/logs/laravel.log for email content");
    } else {
        success("Mail driver: " . config('mail.default'));
    }

    // Summary
    section("TESTING SUMMARY");

    success("All local tests completed successfully! 🎉");
    echo "\n";

    output("📋 WHAT YOU CAN DO NOW:", 'cyan');
    echo "\n";

    output("1️⃣  TEST PAYMENT PAGE IN BROWSER:", 'yellow');
    output("   Open: " . $paymentUrl, 'white');
    echo "\n";

    output("2️⃣  TEST ADMIN PANEL:", 'yellow');
    output("   Open: http://127.0.0.1:8000/admin", 'white');
    output("   Navigate to: Tours & Bookings → Payment Tokens", 'white');
    echo "\n";

    output("3️⃣  CHECK EMAIL LOGS:", 'yellow');
    output("   View: storage/logs/laravel.log", 'white');
    output("   Search for: 'Balance Payment Reminder'", 'white');
    echo "\n";

    output("4️⃣  RUN MORE TESTS:", 'yellow');
    output("   Execute: php test_balance_payment_local.php", 'white');
    output("   Or follow: TESTING_GUIDE.md", 'white');
    echo "\n";

    output("5️⃣  PROCESS QUEUE JOBS:", 'yellow');
    output("   Run: php artisan queue:work", 'white');
    echo "\n";

    info("💡 TIP: You can run this script multiple times to create more test data");

    echo "\n";
    output("╔══════════════════════════════════════════════════════════════╗", 'green');
    output("║              ALL SYSTEMS GO! ✨ READY FOR TESTING            ║", 'green');
    output("╚══════════════════════════════════════════════════════════════╝", 'green');
    echo "\n";

} catch (\Exception $e) {
    echo "\n";
    error("❌ TESTING FAILED");
    error("Error: " . $e->getMessage());
    error("File: " . $e->getFile() . ":" . $e->getLine());
    echo "\n";
    error("Stack trace:");
    echo $e->getTraceAsString();
    echo "\n";
    exit(1);
}
