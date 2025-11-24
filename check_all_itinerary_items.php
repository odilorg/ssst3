<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Tour;
use App\Models\ItineraryItem;

$tours = Tour::where('is_active', true)
    ->orderBy('duration_days', 'desc')
    ->get();

echo "==========================================\n";
echo "COMPLETE ITINERARY AUDIT - ALL TOURS\n";
echo "==========================================\n\n";

$totalTours = 0;
$totalDays = 0;
$issuesFound = [];

foreach ($tours as $tour) {
    $totalTours++;

    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "Tour #{$tour->id}: {$tour->title}\n";
    echo "Duration: {$tour->duration_days} days | Slug: {$tour->slug}\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

    $days = ItineraryItem::where('tour_id', $tour->id)
        ->whereNull('parent_id')
        ->where('type', 'day')
        ->orderBy('sort_order')
        ->get();

    if ($days->count() === 0) {
        echo "❌ NO ITINERARY ITEMS\n\n";
        $issuesFound[] = "Tour #{$tour->id}: Missing itinerary";
        continue;
    }

    foreach ($days as $index => $day) {
        $totalDays++;
        $dayIssues = [];

        // Check sort_order
        $expectedSort = $index + 1;
        if ($day->sort_order != $expectedSort) {
            $dayIssues[] = "sort_order={$day->sort_order} (expected {$expectedSort})";
        }

        // Check title
        if (empty($day->title)) {
            $dayIssues[] = "missing title";
        }

        // Check description
        $descLength = strlen($day->description ?? '');
        if ($descLength === 0) {
            $dayIssues[] = "empty description";
        } elseif ($descLength > 500) {
            $dayIssues[] = "description too long ({$descLength} chars)";
        }

        // Check duration
        if (empty($day->duration_minutes)) {
            $dayIssues[] = "no duration";
        }

        $statusIcon = empty($dayIssues) ? '✅' : '⚠️';

        echo "  {$statusIcon} Day {$day->sort_order}: {$day->title}\n";
        echo "     Description: {$descLength} chars";

        if (!empty($day->duration_minutes)) {
            $hours = floor($day->duration_minutes / 60);
            $mins = $day->duration_minutes % 60;
            echo " | Duration: {$hours}h";
            if ($mins > 0) echo " {$mins}m";
        }
        echo "\n";

        if (!empty($dayIssues)) {
            echo "     ⚠️ Issues: " . implode(', ', $dayIssues) . "\n";
            $issuesFound[] = "Tour #{$tour->id} Day {$day->sort_order}: " . implode(', ', $dayIssues);
        }
        echo "\n";
    }

    echo "  Summary: {$days->count()} days\n\n";
}

echo "==========================================\n";
echo "AUDIT SUMMARY\n";
echo "==========================================\n";
echo "Tours checked: {$totalTours}\n";
echo "Total days: {$totalDays}\n";
echo "Issues found: " . count($issuesFound) . "\n\n";

if (count($issuesFound) > 0) {
    echo "⚠️ ISSUES DETAIL:\n";
    foreach ($issuesFound as $issue) {
        echo "  • {$issue}\n";
    }
} else {
    echo "✅ ALL TOURS PERFECT - NO ISSUES!\n";
}

echo "\n==========================================\n";
