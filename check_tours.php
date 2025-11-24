<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Tour;

$tours = Tour::where('is_active', true)
    ->orderBy('duration_days', 'desc')
    ->get();

echo "Checking all active tours...\n";
echo "==========================================\n\n";

foreach ($tours as $tour) {
    $itineraryCount = $tour->itineraryItems()->where('type', 'day')->count();
    $status = $itineraryCount === 0 ? '❌ MISSING' : '✅ EXISTS';
    
    $issues = [];
    if ($itineraryCount > 0) {
        $days = $tour->itineraryItems()->where('type', 'day')->orderBy('sort_order')->get();
        
        $hasBadSort = $days->where('sort_order', 0)->count() > 0;
        if ($hasBadSort) $issues[] = 'Bad sort_order';
        
        foreach ($days as $day) {
            if ($day->description && strlen($day->description) > 500) {
                $issues[] = 'Long descriptions';
                break;
            }
        }
    }
    
    $issueStr = !empty($issues) ? ' ⚠️ [' . implode(', ', $issues) . ']' : '';
    
    echo str_pad($tour->duration_days . ' days', 12) . ' | ';
    echo str_pad($status . $issueStr, 45) . ' | ';
    echo $tour->title . "\n";
    echo '   ID: ' . $tour->id . ' | ';
    if ($itineraryCount > 0) {
        echo 'Days: ' . $itineraryCount;
    }
    echo "\n";
    echo '   ' . $tour->slug . "\n\n";
}

echo "==========================================\n";
echo "Total active tours: " . $tours->count() . "\n";
