<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Updating test booking...\n\n";

$booking = \App\Models\Booking::find(1);

if (!$booking) {
    echo "Booking not found!\n";
    exit(1);
}

// Update booking with test data
$booking->update([
    'booking_reference' => 'BK-TEST-' . rand(1000, 9999),
    'customer_name' => 'John Test Customer',
    'customer_email' => 'test@example.com',
    'customer_phone' => '+998901234567',
    'customer_country' => 'United States',
    'status' => 'confirmed',
    'payment_status' => 'unpaid',
    'passenger_count' => 2,
    'total_price' => 1000.00,
    'amount_paid' => 0,
    'amount_remaining' => 1000.00,
]);

echo "✅ Booking updated!\n\n";
echo "Booking Details:\n";
echo "  ID: {$booking->id}\n";
echo "  Reference: {$booking->booking_reference}\n";
echo "  Customer: {$booking->customer_name}\n";
echo "  Email: {$booking->customer_email}\n";
echo "  Total Price: $" . number_format($booking->total_price, 2) . "\n";
echo "  Status: {$booking->status}\n";
echo "  Payment Status: {$booking->payment_status}\n";

echo "\n📝 Test the Payment Flow:\n";
echo "1. Payment Review (Deposit 30%): http://127.0.0.1:8000/payment/review?booking_id={$booking->id}&payment_type=deposit\n";
echo "   Amount: $" . number_format($booking->total_price * 0.30, 2) . "\n\n";

echo "2. Payment Review (Full with 10% discount): http://127.0.0.1:8000/payment/review?booking_id={$booking->id}&payment_type=full_payment\n";
echo "   Amount: $" . number_format($booking->total_price * 0.90, 2) . " (save $" . number_format($booking->total_price * 0.10, 2) . ")\n\n";

echo "✨ Open one of the URLs above in your browser to start testing!\n";
