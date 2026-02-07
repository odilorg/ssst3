<?php

namespace App\Console\Commands;

use App\Models\Tour;
use App\Models\TourTranslation;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Seed Tour Translations Command
 *
 * Creates default locale translations for all tours by copying
 * existing Tour fields into the tour_translations table.
 * Uses chunking to avoid memory issues with large datasets.
 *
 * Usage:
 *   php artisan multilang:seed-tour-translations
 *   php artisan multilang:seed-tour-translations --force
 */
class MultilangSeedTourTranslations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'multilang:seed-tour-translations
                            {--force : Overwrite existing translations for default locale}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backfill default-locale tour translations from existing tours table.';

    /**
     * Counters for summary.
     */
    private int $created = 0;
    private int $updated = 0;
    private int $skipped = 0;
    private int $errors = 0;
    private int $warnings = 0;

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $defaultLocale = config('multilang.default_locale', 'en');
        $force = $this->option('force');

        $this->info("Backfilling tour translations for default locale: {$defaultLocale}");
        $this->newLine();

        // Get total count for progress bar
        $totalTours = Tour::count();

        if ($totalTours === 0) {
            $this->warn('No tours found in database.');
            return Command::SUCCESS;
        }

        $this->info("Found {$totalTours} tours to process.");
        $this->newLine();

        $progressBar = $this->output->createProgressBar($totalTours);
        $progressBar->start();

        // Process in chunks of 200 to avoid memory issues
        Tour::chunkById(200, function ($tours) use ($defaultLocale, $force, $progressBar) {
            DB::beginTransaction();

            try {
                foreach ($tours as $tour) {
                    $result = $this->processTour($tour, $defaultLocale, $force);

                    switch ($result) {
                        case 'created':
                            $this->created++;
                            break;
                        case 'updated':
                            $this->updated++;
                            break;
                        case 'skipped':
                            $this->skipped++;
                            break;
                        case 'error':
                            $this->errors++;
                            break;
                    }

                    $progressBar->advance();
                }

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                $this->newLine(2);
                $this->error("Chunk transaction failed: {$e->getMessage()}");
                throw $e;
            }
        });

        $progressBar->finish();
        $this->newLine(2);

        // Summary table
        $this->info('Backfill complete!');
        $this->table(
            ['Status', 'Count'],
            [
                ['Created', $this->created],
                ['Updated', $this->updated],
                ['Skipped', $this->skipped],
                ['Warnings', $this->warnings],
                ['Errors', $this->errors],
                ['Total Processed', $this->created + $this->updated + $this->skipped + $this->errors],
            ]
        );

        if ($this->skipped > 0 && !$force) {
            $this->newLine();
            $this->comment('Use --force to overwrite existing translations.');
        }

        if ($this->warnings > 0) {
            $this->newLine();
            $this->comment('Check above for warnings about tours with missing fields.');
        }

        return Command::SUCCESS;
    }

    /**
     * Process a single tour and create/update its translation.
     *
     * @param Tour $tour
     * @param string $locale
     * @param bool $force
     * @return string 'created', 'updated', 'skipped', or 'error'
     */
    private function processTour(Tour $tour, string $locale, bool $force): string
    {
        try {
            // Check if translation already exists
            $existing = TourTranslation::where('tour_id', $tour->id)
                ->where('locale', $locale)
                ->first();

            if ($existing && !$force) {
                return 'skipped';
            }

            // Prepare translation data with defensive field mapping
            $data = $this->mapTourToTranslation($tour, $locale);

            if ($existing) {
                // Update existing translation (only mapped fields)
                $existing->update($data);
                return 'updated';
            }

            // Create new translation
            TourTranslation::create($data);
            return 'created';

        } catch (\Exception $e) {
            $this->newLine();
            $this->warn("Error processing tour #{$tour->id}: {$e->getMessage()}");
            return 'error';
        }
    }

    /**
     * Map Tour fields to TourTranslation fields defensively.
     *
     * Handles cases where Tour may not have certain fields.
     * Uses fallback chain for each field type.
     *
     * @param Tour $tour
     * @param string $locale
     * @return array
     */
    private function mapTourToTranslation(Tour $tour, string $locale): array
    {
        // Defensive title mapping: prefer title, else name, else heading, else fallback
        $title = $this->getFieldValue($tour, ['title', 'name', 'heading']);

        if (empty($title)) {
            $title = "Tour #{$tour->id}";
            $this->logWarning("Tour #{$tour->id}: Missing title/name field, using fallback.");
        }

        // Defensive slug mapping: try slug, or generate from title
        $slug = $this->getFieldValue($tour, ['slug']);

        if (empty($slug)) {
            // Generate slug from title + id to ensure uniqueness
            $baseSlug = Str::slug($title);
            $slug = $this->generateUniqueSlug($baseSlug, $tour->id, $locale);
            $this->logWarning("Tour #{$tour->id}: Missing slug, generated '{$slug}'.");
        } else {
            // Ensure slug is unique for this locale
            $slug = $this->ensureUniqueSlug($slug, $tour->id, $locale);
        }

        // Map excerpt: try short_description, excerpt, or null
        $excerpt = $this->getFieldValue($tour, ['short_description', 'excerpt']);

        // Map content: try long_description, content, description, or null
        $content = $this->getFieldValue($tour, ['long_description', 'content', 'description']);

        // Map SEO fields defensively
        $seoTitle = $this->getFieldValue($tour, ['seo_title', 'meta_title']);
        $seoDescription = $this->getFieldValue($tour, ['seo_description', 'meta_description']);

        return [
            'tour_id' => $tour->id,
            'locale' => $locale,
            'title' => $title,
            'slug' => $slug,
            'excerpt' => $excerpt,
            'content' => $content,
            'seo_title' => $seoTitle,
            'seo_description' => $seoDescription,
        ];
    }

    /**
     * Get field value from model, trying multiple possible field names.
     *
     * @param Tour $tour
     * @param array $fieldNames List of field names to try in order
     * @return mixed|null
     */
    private function getFieldValue(Tour $tour, array $fieldNames): mixed
    {
        foreach ($fieldNames as $fieldName) {
            // Check via attribute accessor first
            $value = $tour->{$fieldName} ?? null;
            if (!empty($value)) {
                return $value;
            }

            // Also check raw attributes in case of accessor issues
            $rawValue = $tour->getAttributes()[$fieldName] ?? null;
            if (!empty($rawValue)) {
                return $rawValue;
            }
        }

        return null;
    }

    /**
     * Generate a unique slug for the given locale.
     *
     * @param string $baseSlug
     * @param int $tourId
     * @param string $locale
     * @return string
     */
    private function generateUniqueSlug(string $baseSlug, int $tourId, string $locale): string
    {
        // If base slug is empty, use tour ID
        if (empty($baseSlug)) {
            $baseSlug = "tour-{$tourId}";
        }

        $slug = $baseSlug;
        $counter = 1;

        // Check uniqueness in tour_translations for this locale
        while (TourTranslation::where('locale', $locale)
            ->where('slug', $slug)
            ->where('tour_id', '!=', $tourId)
            ->exists()) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }

        return $slug;
    }

    /**
     * Ensure slug is unique for the given locale (handles duplicates).
     *
     * @param string $slug
     * @param int $tourId
     * @param string $locale
     * @return string
     */
    private function ensureUniqueSlug(string $slug, int $tourId, string $locale): string
    {
        // Check if another translation (different tour) uses this slug
        $exists = TourTranslation::where('locale', $locale)
            ->where('slug', $slug)
            ->where('tour_id', '!=', $tourId)
            ->exists();

        if (!$exists) {
            return $slug;
        }

        // Generate unique version
        return $this->generateUniqueSlug($slug, $tourId, $locale);
    }

    /**
     * Log a warning and increment warning counter.
     *
     * @param string $message
     */
    private function logWarning(string $message): void
    {
        $this->warnings++;
        // Only output if verbose mode or this is the first few warnings
        if ($this->getOutput()->isVerbose() || $this->warnings <= 5) {
            $this->newLine();
            $this->warn($message);
        }
    }
}
