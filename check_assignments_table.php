<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== CHECKING BOOKING_ITINERARY_ITEM_ASSIGNMENTS TABLE ===\n";
$columns = \Illuminate\Support\Facades\Schema::getColumnListing('booking_itinerary_item_assignments');
foreach($columns as $col) {
    if(strpos($col, 'transport') !== false) {
        echo "Found transport column: {$col}\n";
    }
}

echo "\n=== CHECKING FOR FOREIGN KEYS ===\n";
$result = \Illuminate\Support\Facades\DB::select("SHOW CREATE TABLE booking_itinerary_item_assignments");
echo $result[0]->{'Create Table'};
