<?php

namespace App\Console\Commands;

use App\Models\Tour;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class AuditTourDuplicates extends Command
{
    protected $signature = 'tours:audit-duplicates {--threshold=70 : Similarity threshold percentage (0-100)}';
    protected $description = 'Audit tours for potential duplicates or very similar content';

    public function handle()
    {
        $this->info('🔍 Auditing tours for duplicates and similarities...');
        $this->newLine();

        $threshold = (int) $this->option('threshold');

        // Fetch all tours with relevant data
        $tours = Tour::with('city')
            ->select('id', 'title', 'slug', 'short_description', 'long_description', 'duration_days', 'city_id', 'is_active', 'created_at')
            ->orderBy('city_id')
            ->orderBy('title')
            ->get();

        $this->info("Found {$tours->count()} tours to analyze");
        $this->newLine();

        $potentialDuplicates = [];
        $compared = [];

        // Compare each tour with every other tour
        foreach ($tours as $tour1) {
            foreach ($tours as $tour2) {
                // Skip self-comparison
                if ($tour1->id === $tour2->id) {
                    continue;
                }

                // Skip if already compared
                $pairKey = $this->getPairKey($tour1->id, $tour2->id);
                if (isset($compared[$pairKey])) {
                    continue;
                }
                $compared[$pairKey] = true;

                // Calculate similarity
                $similarity = $this->calculateSimilarity($tour1, $tour2);

                // If similarity is above threshold, mark as potential duplicate
                if ($similarity['overall'] >= $threshold) {
                    $potentialDuplicates[] = [
                        'tour1' => $tour1,
                        'tour2' => $tour2,
                        'similarity' => $similarity,
                    ];
                }
            }
        }

        // Display results
        if (empty($potentialDuplicates)) {
            $this->info("✅ No potential duplicates found (threshold: {$threshold}%)");
            return Command::SUCCESS;
        }

        $this->warn("⚠️  Found " . count($potentialDuplicates) . " potential duplicate pairs:");
        $this->newLine();

        foreach ($potentialDuplicates as $index => $duplicate) {
            $this->displayDuplicatePair($index + 1, $duplicate);
        }

        return Command::SUCCESS;
    }

    /**
     * Calculate similarity between two tours
     */
    private function calculateSimilarity(Tour $tour1, Tour $tour2): array
    {
        $scores = [];

        // 1. Title similarity (40% weight)
        similar_text(
            strtolower($tour1->title),
            strtolower($tour2->title),
            $titleSimilarity
        );
        $scores['title'] = round($titleSimilarity, 1);

        // 2. Same city? (20% weight)
        $scores['city'] = ($tour1->city_id && $tour1->city_id === $tour2->city_id) ? 100 : 0;

        // 3. Similar duration? (10% weight)
        if ($tour1->duration_days && $tour2->duration_days) {
            $durationDiff = abs($tour1->duration_days - $tour2->duration_days);
            $scores['duration'] = max(0, 100 - ($durationDiff * 20));
        } else {
            $scores['duration'] = 0;
        }

        // 4. Description similarity (30% weight)
        $desc1 = strip_tags($tour1->short_description . ' ' . $tour1->long_description);
        $desc2 = strip_tags($tour2->short_description . ' ' . $tour2->long_description);

        if (!empty($desc1) && !empty($desc2)) {
            similar_text(
                strtolower(substr($desc1, 0, 1000)),
                strtolower(substr($desc2, 0, 1000)),
                $descSimilarity
            );
            $scores['description'] = round($descSimilarity, 1);
        } else {
            $scores['description'] = 0;
        }

        // Calculate weighted overall score
        $scores['overall'] = round(
            ($scores['title'] * 0.4) +
            ($scores['city'] * 0.2) +
            ($scores['duration'] * 0.1) +
            ($scores['description'] * 0.3),
            1
        );

        return $scores;
    }

    /**
     * Display a duplicate pair
     */
    private function displayDuplicatePair(int $number, array $duplicate): void
    {
        $tour1 = $duplicate['tour1'];
        $tour2 = $duplicate['tour2'];
        $similarity = $duplicate['similarity'];

        $this->comment("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
        $this->comment("Pair #{$number} - Overall Similarity: {$similarity['overall']}%");
        $this->comment("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");

        // Tour 1
        $this->line("<fg=cyan>Tour 1 (ID: {$tour1->id}):</>");
        $this->line("  Title: <fg=white>{$tour1->title}</>");
        $this->line("  Slug: <fg=gray>{$tour1->slug}</>");
        $this->line("  City: " . ($tour1->city ? $tour1->city->name : '<fg=gray>None</>'));
        $this->line("  Duration: {$tour1->duration_days} days");
        $this->line("  Created: " . $tour1->created_at->format('Y-m-d H:i'));
        $this->line("  Short Desc: " . Str::limit(strip_tags($tour1->short_description), 80));

        $this->newLine();

        // Tour 2
        $this->line("<fg=cyan>Tour 2 (ID: {$tour2->id}):</>");
        $this->line("  Title: <fg=white>{$tour2->title}</>");
        $this->line("  Slug: <fg=gray>{$tour2->slug}</>");
        $this->line("  City: " . ($tour2->city ? $tour2->city->name : '<fg=gray>None</>'));
        $this->line("  Duration: {$tour2->duration_days} days");
        $this->line("  Created: " . $tour2->created_at->format('Y-m-d H:i'));
        $this->line("  Short Desc: " . Str::limit(strip_tags($tour2->short_description), 80));

        $this->newLine();

        // Similarity breakdown
        $this->line("<fg=yellow>Similarity Scores:</>");
        $this->line("  Title: {$similarity['title']}%");
        $this->line("  City Match: " . ($similarity['city'] === 100 ? '<fg=green>Yes</>' : '<fg=red>No</>'));
        $this->line("  Duration: {$similarity['duration']}%");
        $this->line("  Description: {$similarity['description']}%");

        $this->newLine();
        $this->line("<fg=yellow>Recommendation:</>");

        if ($similarity['overall'] >= 90) {
            $this->line("  <fg=red>🔴 HIGH: Very likely duplicates - consider merging or deleting one</>");
        } elseif ($similarity['overall'] >= 80) {
            $this->line("  <fg=yellow>🟡 MEDIUM: Possibly duplicates - review content carefully</>");
        } else {
            $this->line("  <fg=blue>🔵 LOW: Similar but may serve different purposes</>");
        }

        $this->newLine();
    }

    /**
     * Get a unique pair key for comparison tracking
     */
    private function getPairKey(int $id1, int $id2): string
    {
        $ids = [$id1, $id2];
        sort($ids);
        return implode('-', $ids);
    }
}
