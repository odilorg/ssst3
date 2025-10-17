<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TRANSPORTS TABLE STRUCTURE ===\n";
$columns = \Illuminate\Support\Facades\Schema::getColumnListing('transports');
foreach($columns as $col) {
    $result = \Illuminate\Support\Facades\DB::select("SHOW COLUMNS FROM transports WHERE Field = ?", [$col]);
    $type = $result[0]->Type;
    $null = $result[0]->Null;
    $default = $result[0]->Default;
    echo "{$col}: {$type} | Null: {$null} | Default: " . ($default ?? 'NULL') . "\n";
}

echo "\n=== CHECKING FOR RUNNING DAYS FIELDS ===\n";
$runningDaysFields = array_filter($columns, function($col) {
    return strpos(strtolower($col), 'day') !== false || 
           strpos(strtolower($col), 'schedule') !== false ||
           strpos(strtolower($col), 'week') !== false;
});

if (!empty($runningDaysFields)) {
    echo "Found potential running days fields:\n";
    foreach ($runningDaysFields as $field) {
        echo "  - {$field}\n";
    }
} else {
    echo "No running days fields found in transports table.\n";
}
