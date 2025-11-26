<?php

namespace App\Console\Commands;

use App\Models\Tour;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class ImportWordPressTours extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tours:import-wordpress {--skip-existing : Skip tours that already exist}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import tours from old WordPress site via REST API';

    private const WP_API_URL = 'https://jahongir-travel.uz/wp-json/wp/v2/tour';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔄 Importing tours from WordPress site...');
        $this->newLine();

        // Fetch all tours from WordPress API
        $this->info('Fetching tours from WordPress API...');
        $tours = $this->fetchAllTours();

        if (empty($tours)) {
            $this->error('No tours found or API is unavailable.');
            return Command::FAILURE;
        }

        $this->info("Found " . count($tours) . " tours to import");
        $this->newLine();

        $progressBar = $this->output->createProgressBar(count($tours));
        $progressBar->start();

        $imported = 0;
        $skipped = 0;
        $errors = 0;

        foreach ($tours as $wpTour) {
            try {
                $slug = $wpTour['slug'] ?? null;

                if (!$slug) {
                    $errors++;
                    $progressBar->advance();
                    continue;
                }

                // Check if tour already exists
                if ($this->option('skip-existing') && Tour::where('slug', $slug)->exists()) {
                    $skipped++;
                    $progressBar->advance();
                    continue;
                }

                // Import the tour
                $this->importTour($wpTour);
                $imported++;

            } catch (\Exception $e) {
                $this->error("\nError importing tour: " . $e->getMessage());
                $errors++;
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);

        // Summary
        $this->info('✅ Import complete!');
        $this->table(
            ['Status', 'Count'],
            [
                ['Imported', $imported],
                ['Skipped (already exist)', $skipped],
                ['Errors', $errors],
                ['Total processed', count($tours)],
            ]
        );

        if ($imported > 0) {
            $this->newLine();
            $this->comment('💡 Next steps:');
            $this->comment('1. Review imported tours in Filament admin');
            $this->comment('2. Add pricing, images, and other details');
            $this->comment('3. Run: php artisan tours:generate-seo');
        }

        return Command::SUCCESS;
    }

    /**
     * Fetch all tours from WordPress API
     */
    private function fetchAllTours(): array
    {
        $allTours = [];
        $page = 1;
        $perPage = 100;

        do {
            $response = Http::timeout(30)->get(self::WP_API_URL, [
                'per_page' => $perPage,
                'page' => $page,
            ]);

            if (!$response->successful()) {
                break;
            }

            $tours = $response->json();
            if (empty($tours)) {
                break;
            }

            $allTours = array_merge($allTours, $tours);
            $page++;

        } while (count($tours) === $perPage);

        return $allTours;
    }

    /**
     * Import a single tour
     */
    private function importTour(array $wpTour): void
    {
        // Extract title
        $title = strip_tags($wpTour['title']['rendered'] ?? '');

        // Extract description
        $content = $wpTour['content']['rendered'] ?? '';
        $excerpt = strip_tags($wpTour['excerpt']['rendered'] ?? '');

        // Create or update tour
        Tour::updateOrCreate(
            ['slug' => $wpTour['slug']],
            [
                'title' => $title,
                'short_description' => Str::limit($excerpt, 255),
                'long_description' => $this->cleanHtmlContent($content),

                // Default values (user will fill these in admin)
                'duration_days' => 1,
                'duration_text' => 'To be confirmed',
                'price_per_person' => 0,
                'currency' => 'USD',
                'max_guests' => 10,
                'min_guests' => 1,

                // Set as inactive until reviewed
                'is_active' => false,

                // Meta
                'tour_type' => 'private_only',
                'has_hotel_pickup' => true,
                'min_booking_hours' => 24,
                'cancellation_hours' => 24,
            ]
        );
    }

    /**
     * Clean HTML content
     */
    private function cleanHtmlContent(string $html): string
    {
        // Remove excessive whitespace
        $html = preg_replace('/\s+/', ' ', $html);

        // Remove empty paragraphs
        $html = preg_replace('/<p[^>]*>\s*<\/p>/', '', $html);

        return trim($html);
    }
}
