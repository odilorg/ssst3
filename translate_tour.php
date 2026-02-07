#!/usr/bin/env php
<?php

/**
 * Quick script to translate a tour via command line
 *
 * Usage:
 *   php translate_tour.php <tour_id> <target_locale>
 *
 * Example:
 *   php translate_tour.php 49 uz   # Translate Ceramics tour to Uzbek
 *   php translate_tour.php 49 ru   # Translate Ceramics tour to Russian
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Tour;
use App\Models\TourTranslation;
use App\Models\TranslationLog;
use App\Services\OpenAI\TranslationService;

// Get arguments
$tourId = $argv[1] ?? null;
$targetLocale = $argv[2] ?? null;

if (!$tourId || !$targetLocale) {
    echo "Usage: php translate_tour.php <tour_id> <target_locale>\n";
    echo "Example: php translate_tour.php 49 uz\n";
    exit(1);
}

// Load tour
$tour = Tour::find($tourId);
if (!$tour) {
    echo "❌ Tour ID {$tourId} not found\n";
    exit(1);
}

echo "🎯 Translating Tour: {$tour->title}\n";
echo "   Target Locale: " . strtoupper($targetLocale) . "\n";
echo "   Source Locale: EN (English)\n\n";

// Check if translation record exists
$translation = TourTranslation::where('tour_id', $tourId)
    ->where('locale', $targetLocale)
    ->first();

if (!$translation) {
    // Create translation record
    $translation = TourTranslation::create([
        'tour_id' => $tourId,
        'locale' => $targetLocale,
        'title' => '',
    ]);
    echo "✅ Created new translation record (ID: {$translation->id})\n\n";
} else {
    echo "ℹ️  Found existing translation record (ID: {$translation->id})\n\n";
}

try {
    // Initialize service
    $service = new TranslationService();

    // Create log
    $log = TranslationLog::create([
        'tour_id' => $tour->id,
        'user_id' => 1, // CLI user
        'source_locale' => 'en',
        'target_locale' => $targetLocale,
        'sections_translated' => [],
        'tokens_used' => 0,
        'cost_usd' => 0,
        'model' => config('ai-translation.openai.model'),
        'status' => 'processing',
    ]);

    echo "⏳ Translating...\n";
    $startTime = microtime(true);

    // Translate tour
    $result = $service->translateTour($tour, $targetLocale);

    $duration = round(microtime(true) - $startTime, 2);

    // Update translation record
    $translation->update($result['translations']);

    // Update log
    $cost = $service->estimateCost($result['tokens_used'], $result['tokens_used']);
    $log->update([
        'sections_translated' => array_keys($result['translations']),
        'tokens_used' => $result['tokens_used'],
        'cost_usd' => $cost,
        'status' => 'completed',
    ]);

    echo "\n✅ Translation completed in {$duration} seconds!\n\n";

    echo "📊 Translation Statistics:\n";
    echo "=========================\n";
    echo "Sections translated: " . count($result['translations']) . "\n";
    echo "Sections: " . implode(', ', array_keys($result['translations'])) . "\n";
    echo "Tokens used: ~{$result['tokens_used']}\n";
    echo "Estimated cost: $" . number_format($cost, 4) . " USD\n\n";

    echo "📝 Sample Translations:\n";
    echo "======================\n";
    if (isset($result['translations']['title'])) {
        echo "Title: {$result['translations']['title']}\n\n";
    }
    if (isset($result['translations']['excerpt'])) {
        echo "Excerpt: " . substr($result['translations']['excerpt'], 0, 150) . "...\n\n";
    }

    echo "✅ View translation in admin panel:\n";
    echo "   https://staging.jahongir-travel.uz/admin/tours/{$tourId}/edit#translations\n\n";

} catch (\Exception $e) {
    if (isset($log)) {
        $log->update([
            'status' => 'failed',
            'error_message' => $e->getMessage(),
        ]);
    }

    echo "\n❌ Translation failed!\n";
    echo "Error: {$e->getMessage()}\n";
    exit(1);
}
