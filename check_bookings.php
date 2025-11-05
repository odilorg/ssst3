<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Checking bookings...\n\n";

$bookingCount = \App\Models\Booking::count();
echo "Total Bookings: $bookingCount\n\n";

if ($bookingCount > 0) {
    $booking = \App\Models\Booking::with('tour', 'departure')->latest()->first();

    echo "Latest Booking:\n";
    echo "  ID: {$booking->id}\n";
    echo "  Reference: {$booking->booking_reference}\n";
    echo "  Customer: {$booking->customer_name}\n";
    echo "  Email: {$booking->customer_email}\n";
    echo "  Total Price: $" . number_format($booking->total_price, 2) . "\n";
    echo "  Status: {$booking->status}\n";
    echo "  Payment Status: {$booking->payment_status}\n";
    echo "  Amount Paid: $" . number_format($booking->amount_paid, 2) . "\n";
    echo "  Amount Remaining: $" . number_format($booking->amount_remaining, 2) . "\n";

    if ($booking->tour) {
        echo "  Tour: {$booking->tour->name}\n";
    }

    if ($booking->departure) {
        echo "  Departure: {$booking->departure->start_date->format('Y-m-d')}\n";
    }

    echo "\n--- Test URLs ---\n";
    echo "Payment Review (Deposit): http://127.0.0.1:8000/payment/review?booking_id={$booking->id}&payment_type=deposit\n";
    echo "Payment Review (Full): http://127.0.0.1:8000/payment/review?booking_id={$booking->id}&payment_type=full_payment\n";
} else {
    echo "No bookings found. Please create a booking in Filament admin first.\n";
    echo "URL: http://127.0.0.1:8000/admin/bookings/create\n";
}
