<?php

namespace App\Console\Commands;

use App\Models\Tour;
use App\Services\AIImageMatchingService;
use App\Services\ImageDiscoveryService;
use App\Services\TourImageAssignmentService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class AssignTourImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tours:assign-images
                            {--tour= : Process specific tour by ID}
                            {--dry-run : Preview selections without saving to database}
                            {--force : Overwrite existing images}
                            {--stats : Show image statistics only}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Use AI to automatically assign hero and gallery images to tours';

    private ImageDiscoveryService $imageDiscovery;
    private AIImageMatchingService $aiMatcher;
    private TourImageAssignmentService $assignmentService;

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->imageDiscovery = new ImageDiscoveryService();
        $this->aiMatcher = new AIImageMatchingService();
        $this->assignmentService = new TourImageAssignmentService();

        // Show statistics only
        if ($this->option('stats')) {
            $this->showStatistics();
            return Command::SUCCESS;
        }

        $this->info('╔════════════════════════════════════════════════╗');
        $this->info('║   AI-Powered Tour Image Assignment System     ║');
        $this->info('╚════════════════════════════════════════════════╝');
        $this->newLine();

        $dryRun = $this->option('dry-run');
        $force = $this->option('force');
        $tourId = $this->option('tour');

        if ($dryRun) {
            $this->warn('🔍 DRY RUN MODE - No changes will be saved to database');
        } else {
            $this->info('✨ LIVE MODE - Changes will be saved to database');
        }

        if ($force) {
            $this->warn('⚠️  FORCE MODE - Existing images will be overwritten');
        }

        $this->newLine();

        // Get tours to process
        $toursQuery = Tour::query();

        if ($tourId) {
            $toursQuery->where('id', $tourId);
            $this->info("Processing single tour: ID {$tourId}");
        } else {
            // Only process tours without hero images unless --force is used
            if (!$force) {
                $toursQuery->whereNull('hero_image');
                $this->info('Processing tours without hero images only (use --force to override all)');
            } else {
                $this->info('Processing ALL tours');
            }
        }

        $tours = $toursQuery->get();

        if ($tours->isEmpty()) {
            $this->warn('No tours found to process.');
            return Command::SUCCESS;
        }

        $this->info("Found {$tours->count()} tour(s) to process");
        $this->newLine();

        // Confirm before proceeding
        if (!$dryRun && $tours->count() > 1) {
            if (!$this->confirm('Do you want to proceed?', true)) {
                $this->info('Operation cancelled.');
                return Command::SUCCESS;
            }
            $this->newLine();
        }

        // Process each tour
        $processed = 0;
        $failed = 0;
        $skipped = 0;

        $progressBar = $this->output->createProgressBar($tours->count());
        $progressBar->start();

        foreach ($tours as $tour) {
            $progressBar->advance();
            $this->newLine();
            $this->processTour($tour, $dryRun, $processed, $failed, $skipped);
            $this->newLine();

            // Rate limiting to avoid API throttling
            if ($tours->count() > 1) {
                sleep(2);
            }
        }

        $progressBar->finish();
        $this->newLine(2);

        // Summary
        $this->info('╔════════════════════════════════════════════════╗');
        $this->info('║                   SUMMARY                      ║');
        $this->info('╚════════════════════════════════════════════════╝');
        $this->table(
            ['Status', 'Count'],
            [
                ['✅ Processed', $processed],
                ['❌ Failed', $failed],
                ['⏭️  Skipped', $skipped],
                ['📊 Total', $tours->count()],
            ]
        );

        if ($dryRun) {
            $this->newLine();
            $this->info('💡 This was a dry run. Run without --dry-run to save changes.');
        }

        return Command::SUCCESS;
    }

    /**
     * Process a single tour
     */
    private function processTour(Tour $tour, bool $dryRun, int &$processed, int &$failed, int &$skipped): void
    {
        $this->line('─────────────────────────────────────────────────');
        $this->info("Tour #{$tour->id}: {$tour->title}");
        $this->line("Slug: {$tour->slug}");

        if ($tour->hero_image) {
            $this->line("Current hero: {$tour->hero_image}");
        }

        $galleryCount = is_array($tour->gallery_images) ? count($tour->gallery_images) : 0;
        $this->line("Current gallery: {$galleryCount} images");

        try {
            // Discover images
            $this->line("\n📸 Discovering candidate images...");
            $discoveryResult = $this->imageDiscovery->discoverImagesForTour($tour);

            $this->line("   Found {$discoveryResult['count']} candidate images");

            if ($discoveryResult['count'] < 5) {
                $this->warn("   ⚠️  Not enough images (need at least 5), skipping tour");
                $skipped++;
                return;
            }

            // AI selection
            $this->line("\n🤖 Asking AI to select best images...");

            $selectedImages = $this->aiMatcher->selectImagesForTour($tour, $discoveryResult['images']);

            // Display selections
            $this->line("\n   ✅ AI selections:");
            $this->line("   <fg=cyan>Hero:</>   {$selectedImages['hero']['filename']}</>");
            $this->line("   <fg=gray>Reason: {$selectedImages['hero']['reason']}</>");

            $this->line("\n   <fg=cyan>Gallery:</>");
            foreach ($selectedImages['gallery'] as $i => $img) {
                $this->line("   " . ($i + 1) . ". {$img['filename']}");
                $this->line("      <fg=gray>{$img['reason']}</>");
            }

            // Validate
            $validation = $this->assignmentService->validateSelectedImages($selectedImages);
            if (!$validation['valid']) {
                $this->error("\n   ❌ Validation failed:");
                foreach ($validation['errors'] as $error) {
                    $this->error("      - {$error}");
                }
                $failed++;
                return;
            }

            // Assign to database
            if (!$dryRun) {
                $this->line("\n💾 Updating database...");
                $success = $this->assignmentService->assignImagesToTour($tour, $selectedImages);

                if ($success) {
                    $this->info("   ✅ Tour images updated successfully");
                    $processed++;
                } else {
                    $this->error("   ❌ Failed to update database");
                    $failed++;
                }
            } else {
                $this->line("\n   <fg=yellow>[DRY RUN - No database changes made]</>");
                $processed++;
            }

        } catch (\Exception $e) {
            $this->error("\n❌ Error: {$e->getMessage()}");
            Log::error('Tour image assignment failed', [
                'tour_id' => $tour->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            $failed++;
        }
    }

    /**
     * Show image statistics
     */
    private function showStatistics(): void
    {
        $this->info('╔════════════════════════════════════════════════╗');
        $this->info('║          Tour Image Statistics                 ║');
        $this->info('╚════════════════════════════════════════════════╝');
        $this->newLine();

        $stats = $this->assignmentService->getTourImageStatistics();

        $this->table(
            ['Metric', 'Value'],
            [
                ['Total Tours', $stats['total_tours']],
                ['Tours with Hero Image', $stats['tours_with_hero']],
                ['Tours with Gallery Images', $stats['tours_with_gallery']],
                ['Tours without Images', $stats['tours_without_images']],
                ['Completion %', $stats['completion_percentage'] . '%'],
            ]
        );

        $this->newLine();

        // Show tours without images
        $toursWithoutImages = Tour::whereNull('hero_image')->get();

        if ($toursWithoutImages->isNotEmpty()) {
            $this->warn("Tours without hero images ({$toursWithoutImages->count()}):");
            foreach ($toursWithoutImages as $tour) {
                $this->line("  - #{$tour->id}: {$tour->title}");
            }
        } else {
            $this->info('✅ All tours have hero images!');
        }
    }
}
