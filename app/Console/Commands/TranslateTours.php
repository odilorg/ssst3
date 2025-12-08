<?php

namespace App\Console\Commands;

use App\Models\Language;
use App\Models\Tour;
use App\Services\TourTranslationService;
use Illuminate\Console\Command;

class TranslateTours extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tours:translate
                            {--tour= : Translate specific tour by ID}
                            {--lang= : Translate to specific language code (es, fr, etc.)}
                            {--force : Re-translate even if translation exists}
                            {--dry-run : Show what would be translated without actually doing it}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Translate tours to different languages using AI';

    protected TourTranslationService $translationService;

    /**
     * Execute the console command.
     */
    public function handle(TourTranslationService $translationService): int
    {
        $this->translationService = $translationService;

        $this->info('╔════════════════════════════════════════════════╗');
        $this->info('║     AI-Powered Tour Translation System         ║');
        $this->info('╚════════════════════════════════════════════════╝');
        $this->newLine();

        // Determine mode
        $isDryRun = $this->option('dry-run');
        $force = $this->option('force');

        if ($isDryRun) {
            $this->warn('🔍 DRY RUN MODE - No changes will be saved');
            $this->newLine();
        } elseif ($force) {
            $this->warn('⚠️  FORCE MODE - Existing translations will be overwritten');
            $this->newLine();
        } else {
            $this->info('✨ STANDARD MODE - Only missing translations will be added');
            $this->newLine();
        }

        // Get tours to translate
        $tours = $this->getToursToTranslate();

        if ($tours->isEmpty()) {
            $this->error('No tours found to translate.');
            return Command::FAILURE;
        }

        // Get target languages
        $targetLanguages = $this->getTargetLanguages();

        if (empty($targetLanguages)) {
            $this->error('No target languages found.');
            return Command::FAILURE;
        }

        // Display summary
        $this->table(
            ['Property', 'Value'],
            [
                ['Tours to translate', $tours->count()],
                ['Target languages', implode(', ', $targetLanguages)],
                ['Force re-translate', $force ? 'Yes' : 'No'],
                ['Dry run', $isDryRun ? 'Yes' : 'No'],
            ]
        );

        $this->newLine();

        // Confirm before proceeding
        if (!$isDryRun && !$this->confirm('Do you want to proceed?', true)) {
            $this->info('Translation cancelled.');
            return Command::SUCCESS;
        }

        // Process translations
        $this->processTranslations($tours, $targetLanguages, $force, $isDryRun);

        return Command::SUCCESS;
    }

    /**
     * Get tours to translate based on options
     */
    protected function getToursToTranslate()
    {
        $tourId = $this->option('tour');

        if ($tourId) {
            $tour = Tour::find($tourId);

            if (!$tour) {
                $this->error("Tour with ID {$tourId} not found.");
                return collect();
            }

            return collect([$tour]);
        }

        // Get all tours
        return Tour::all();
    }

    /**
     * Get target languages based on options
     */
    protected function getTargetLanguages(): array
    {
        $langOption = $this->option('lang');

        if ($langOption) {
            // Validate the language
            $language = Language::where('code', $langOption)->where('is_active', true)->first();

            if (!$language) {
                $this->error("Language '{$langOption}' not found or inactive.");
                return [];
            }

            return [$langOption];
        }

        // Get all active languages except English (assuming English is the source)
        return Language::where('is_active', true)
            ->where('code', '!=', 'en')
            ->pluck('code')
            ->toArray();
    }

    /**
     * Process translations for all tours
     */
    protected function processTranslations($tours, array $targetLanguages, bool $force, bool $isDryRun): void
    {
        $totalTours = $tours->count();
        $successCount = 0;
        $errorCount = 0;
        $skippedCount = 0;

        $this->info("Processing {$totalTours} tour(s)...");
        $this->newLine();

        $progressBar = $this->output->createProgressBar($totalTours * count($targetLanguages));
        $progressBar->setFormat('%current%/%max% [%bar%] %percent:3s%% - %message%');

        foreach ($tours as $index => $tour) {
            $tourNumber = $index + 1;

            $this->newLine(2);
            $this->line("─────────────────────────────────────────────────");
            $this->info("Tour #{$tour->id}: " . ($tour->getTranslation('title', 'en') ?? $tour->getTranslation('title', 'ru') ?? 'Untitled'));
            $this->line("Slug: {$tour->slug}");

            // Show current translation status
            $progress = $this->translationService->getTranslationProgress($tour);

            $statusTable = [];
            foreach ($targetLanguages as $lang) {
                $statusTable[] = [
                    $lang,
                    $progress[$lang]['translated'] . '/' . $progress[$lang]['total'],
                    $progress[$lang]['percentage'] . '%',
                    $progress[$lang]['complete'] ? '✓' : '✗'
                ];
            }

            $this->table(
                ['Language', 'Fields', 'Progress', 'Complete'],
                $statusTable
            );

            if ($isDryRun) {
                $this->info('   [DRY RUN] Would translate to: ' . implode(', ', $targetLanguages));

                foreach ($targetLanguages as $lang) {
                    $progressBar->setMessage("DRY RUN: Tour {$tourNumber}/{$totalTours}");
                    $progressBar->advance();
                }

                continue;
            }

            // Perform actual translation
            foreach ($targetLanguages as $lang) {
                $progressBar->setMessage("Tour {$tourNumber}/{$totalTours} → {$lang}");

                try {
                    $results = $this->translationService->translateTour($tour, $lang, $force);

                    if ($results[$lang]['success']) {
                        if ($results[$lang]['fields_translated'] > 0) {
                            $this->info("   ✅ {$lang}: Translated {$results[$lang]['fields_translated']} fields");
                            $successCount++;
                        } else {
                            $this->comment("   ⏭️  {$lang}: Already translated");
                            $skippedCount++;
                        }
                    } else {
                        $this->error("   ❌ {$lang}: {$results[$lang]['error']}");
                        $errorCount++;
                    }
                } catch (\Exception $e) {
                    $this->error("   ❌ {$lang}: " . $e->getMessage());
                    $errorCount++;
                }

                $progressBar->advance();
            }
        }

        $progressBar->finish();
        $this->newLine(2);

        // Final summary
        $this->info('╔════════════════════════════════════════════════╗');
        $this->info('║              Translation Complete              ║');
        $this->info('╚════════════════════════════════════════════════╝');
        $this->newLine();

        $this->table(
            ['Status', 'Count'],
            [
                ['✅ Successful translations', $successCount],
                ['⏭️  Skipped (already translated)', $skippedCount],
                ['❌ Errors', $errorCount],
                ['📊 Total operations', $successCount + $skippedCount + $errorCount],
            ]
        );
    }
}
