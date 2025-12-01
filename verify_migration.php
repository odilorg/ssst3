<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== MIGRATION VERIFICATION ===\n";

// Check transport instance prices
$instancePrices = \Illuminate\Support\Facades\DB::table('transport_instance_prices')->count();
echo "Transport instance prices created: {$instancePrices}\n";

// Check assignments with transport instance prices
$assignmentsWithInstancePrices = \Illuminate\Support\Facades\DB::table('booking_itinerary_item_assignments')
    ->whereNotNull('transport_instance_price_id')
    ->count();
echo "Assignments with transport instance prices: {$assignmentsWithInstancePrices}\n";

// Check assignments still using transport type prices
$assignmentsWithTypePrices = \Illuminate\Support\Facades\DB::table('booking_itinerary_item_assignments')
    ->whereNotNull('transport_price_type_id')
    ->whereNull('transport_instance_price_id')
    ->count();
echo "Assignments still using transport type prices: {$assignmentsWithTypePrices}\n";

// Show sample data
echo "\n=== SAMPLE TRANSPORT INSTANCE PRICES ===\n";
$samples = \Illuminate\Support\Facades\DB::table('transport_instance_prices')
    ->join('transports', 'transport_instance_prices.transport_id', '=', 'transports.id')
    ->join('transport_types', 'transports.transport_type_id', '=', 'transport_types.id')
    ->select('transport_types.type', 'transports.plate_number', 'transport_instance_prices.price_type', 'transport_instance_prices.cost')
    ->limit(5)
    ->get();

foreach ($samples as $sample) {
    echo "{$sample->type} {$sample->plate_number} - {$sample->price_type}: \${$sample->cost}\n";
}
