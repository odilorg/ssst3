<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Get payment ID from command line
$paymentId = $argv[1] ?? null;

if (!$paymentId) {
    echo "Usage: php simulate_webhook_fixed.php <payment_id> [event]\n";
    echo "Events: payment.success, payment.failed, payment.cancelled\n";
    exit(1);
}

$event = $argv[2] ?? 'payment.success';

$payment = \App\Models\Payment::find($paymentId);

if (!$payment) {
    echo "Payment not found!\n";
    exit(1);
}

// If payment doesn't have transaction_id, set one
if (!$payment->transaction_id) {
    $transactionId = 'TEST-TXN-' . time();
    $payment->update(['transaction_id' => $transactionId]);
    echo "✅ Updated payment with transaction ID: $transactionId\n\n";
} else {
    $transactionId = $payment->transaction_id;
}

// Simulate webhook payload
$payload = [
    'event' => $event,
    'transaction_id' => $transactionId,
    'status' => $event === 'payment.success' ? 'completed' : 'failed',
    'amount' => (int) round($payment->amount * 100 * 12500), // Convert to tiyin
    'currency' => 'UZS',
    'order_id' => $payment->id,
    'timestamp' => now()->toIso8601String(),
];

// Calculate signature
ksort($payload);
$signatureString = '';
foreach ($payload as $key => $value) {
    $signatureString .= $key . '=' . $value . '&';
}
$signatureString = rtrim($signatureString, '&');
$signature = hash_hmac('sha256', $signatureString, config('services.octo.webhook_secret'));

echo "Simulating OCTO Webhook...\n";
echo "Payment ID: {$payment->id}\n";
echo "Event: {$event}\n";
echo "Transaction ID: {$transactionId}\n";
echo "\nPayload:\n";
echo json_encode($payload, JSON_PRETTY_PRINT) . "\n";
echo "\nSignature: $signature\n";

// Send webhook request
$response = \Illuminate\Support\Facades\Http::withHeaders([
    'X-OCTO-Signature' => $signature,
    'Content-Type' => 'application/json',
])->post('http://127.0.0.1:8000/payment/webhook', $payload);

echo "\n--- Response ---\n";
echo "Status: {$response->status()}\n";
echo "Body: {$response->body()}\n";

// Refresh payment
$payment->refresh();
echo "\n--- Updated Payment ---\n";
echo "Status: {$payment->status}\n";
echo "Processed At: " . ($payment->processed_at ? $payment->processed_at->format('Y-m-d H:i:s') : 'Not set') . "\n";

$booking = $payment->booking;
$booking->refresh();
echo "\n--- Updated Booking ---\n";
echo "Payment Status: {$booking->payment_status}\n";
echo "Amount Paid: $" . number_format($booking->amount_paid, 2) . "\n";
echo "Amount Remaining: $" . number_format($booking->amount_remaining, 2) . "\n";

echo "\n✅ Webhook simulation complete!\n";

if ($event === 'payment.success' && $payment->status === 'completed') {
    echo "\n🎉 SUCCESS! Payment marked as completed.\n";
    echo "✉️  Email notification queued (check storage/logs/laravel.log)\n";
    echo "🌐 View success page: http://127.0.0.1:8000/payment/{$payment->id}/success\n";
}
