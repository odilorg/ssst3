<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Testing Filament Resources...\n\n";

$resources = [
    'TourDepartureResource' => \App\Filament\Resources\TourDepartures\TourDepartureResource::class,
    'TourResource' => \App\Filament\Resources\Tours\TourResource::class,
    'BookingResource' => \App\Filament\Resources\Bookings\BookingResource::class,
    'PaymentResource' => \App\Filament\Resources\Payments\PaymentResource::class,
];

$success = true;

foreach ($resources as $name => $class) {
    echo "$name: ";
    try {
        if (!class_exists($class)) {
            echo "❌ Class not found\n";
            $success = false;
            continue;
        }

        // Check if model exists
        $model = $class::getModel();
        if (!class_exists($model)) {
            echo "❌ Model not found: $model\n";
            $success = false;
            continue;
        }

        echo "✅ OK (Model: " . class_basename($model) . ")\n";
    } catch (\Exception $e) {
        echo "❌ " . $e->getMessage() . "\n";
        $success = false;
    }
}

echo "\n";

if ($success) {
    echo "✅ All resources loaded successfully!\n";
} else {
    echo "❌ Some resources have errors.\n";
    exit(1);
}

echo "\nTesting Relations...\n";

try {
    $tourResource = \App\Filament\Resources\Tours\TourResource::class;
    $relations = $tourResource::getRelations();
    echo "TourResource relations: " . count($relations) . " relation managers\n";

    $bookingResource = \App\Filament\Resources\Bookings\BookingResource::class;
    $relations = $bookingResource::getRelations();
    echo "BookingResource relations: " . count($relations) . " relation managers\n";

    echo "✅ Relations configured correctly!\n";
} catch (\Exception $e) {
    echo "❌ Relations error: " . $e->getMessage() . "\n";
}

echo "\n✨ Phase 3 testing complete!\n";
