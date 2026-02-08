<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔧 Fixing all tours to ensure consistency...\n";
echo "=============================================\n\n";

$tours = App\Models\Tour::all();
$fixed = 0;

foreach ($tours as $tour) {
    $changed = false;

    if ($tour->tour_type === 'private_only') {
        if ($tour->supports_private !== true || $tour->supports_group !== false) {
            $tour->supports_private = true;
            $tour->supports_group = false;
            $changed = true;
        }
    } elseif ($tour->tour_type === 'group_only') {
        if ($tour->supports_private !== false || $tour->supports_group !== true) {
            $tour->supports_private = false;
            $tour->supports_group = true;
            $changed = true;
        }
    } elseif ($tour->tour_type === 'hybrid') {
        if ($tour->supports_private !== true || $tour->supports_group !== true) {
            $tour->supports_private = true;
            $tour->supports_group = true;
            $changed = true;
        }
    }

    if ($changed) {
        $tour->save();
        $fixed++;
        echo "✅ Fixed: {$tour->slug} ({$tour->tour_type})\n";
    }
}

echo "\n";
echo "=============================================\n";
echo "Total tours: " . $tours->count() . "\n";
echo "Fixed: {$fixed}\n";
echo "Already correct: " . ($tours->count() - $fixed) . "\n";
echo "\n✅ All tours now have consistent flags!\n";
