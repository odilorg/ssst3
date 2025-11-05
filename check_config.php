<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Checking OCTO Configuration...\n\n";

$config = config('services.octo');

if (!$config) {
    echo "❌ OCTO config not found in services.php\n";
    exit(1);
}

echo "OCTO Config:\n";
echo "  Base URL: " . ($config['base_url'] ?? 'NOT SET') . "\n";
echo "  API Key: " . ($config['api_key'] ?? 'NOT SET') . "\n";
echo "  Webhook Secret: " . ($config['webhook_secret'] ?? 'NOT SET') . "\n";
echo "  Merchant ID: " . ($config['merchant_id'] ?? 'NOT SET') . "\n";

echo "\n";

// Check if all values are set
$allSet = true;
foreach (['base_url', 'api_key', 'webhook_secret', 'merchant_id'] as $key) {
    if (empty($config[$key])) {
        echo "❌ Missing: $key\n";
        $allSet = false;
    }
}

if ($allSet) {
    echo "✅ All OCTO config values are set!\n";

    // Test OctoPaymentService instantiation
    try {
        $service = new \App\Services\OctoPaymentService();
        echo "✅ OctoPaymentService can be instantiated!\n";
    } catch (\Exception $e) {
        echo "❌ Error creating OctoPaymentService: " . $e->getMessage() . "\n";
    }
} else {
    echo "\n❌ Some config values are missing. Check your .env file.\n";
}
