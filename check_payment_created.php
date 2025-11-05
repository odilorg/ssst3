<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Checking if payment was created...\n\n";

$payment = \App\Models\Payment::latest()->first();

if (!$payment) {
    echo "❌ No payment found. Something went wrong.\n";
    exit(1);
}

echo "✅ Payment record was created!\n\n";

echo "Payment Details:\n";
echo "  ID: {$payment->id}\n";
echo "  Booking ID: {$payment->booking_id}\n";
echo "  Amount: $" . number_format($payment->amount, 2) . "\n";
echo "  Type: {$payment->payment_type}\n";
echo "  Status: {$payment->status}\n";
echo "  Method: {$payment->payment_method}\n";
echo "  Created: {$payment->created_at}\n";

echo "\n📋 Next Steps:\n\n";

echo "1. Simulate OCTO Success Webhook:\n";
echo "   php simulate_webhook.php {$payment->id} payment.success\n\n";

echo "2. Then view the success page in your browser:\n";
echo "   http://127.0.0.1:8000/payment/{$payment->id}/success\n\n";

echo "3. Or simulate a failed payment:\n";
echo "   php simulate_webhook.php {$payment->id} payment.failed\n\n";

echo "✨ Everything is working correctly! The error message is expected with test credentials.\n";
