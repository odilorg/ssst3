<?php

namespace App\Console\Commands;

use App\Models\Tour;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class GenerateTourSeoData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tours:generate-seo {--force : Overwrite existing SEO data} {--include-inactive : Include inactive tours}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate SEO titles and descriptions for all tours';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Generating SEO data for tours...');
        $this->newLine();

        $force = $this->option('force');
        $includeInactive = $this->option('include-inactive');

        // Get tours based on active status
        $query = Tour::with('city');
        if (!$includeInactive) {
            $query->where('is_active', true);
        }
        $tours = $query->get();

        if ($tours->isEmpty()) {
            $this->error($includeInactive ? 'No tours found.' : 'No active tours found.');
            return Command::FAILURE;
        }

        $this->info("Found {$tours->count()} " . ($includeInactive ? '' : 'active ') . "tours");
        $this->newLine();

        $progressBar = $this->output->createProgressBar($tours->count());
        $progressBar->start();

        $updated = 0;
        $skipped = 0;

        foreach ($tours as $tour) {
            $needsUpdate = false;

            // Generate SEO Title
            if ($force || empty($tour->seo_title)) {
                $tour->seo_title = $this->generateSeoTitle($tour);
                $needsUpdate = true;
            }

            // Generate SEO Description
            if ($force || empty($tour->seo_description)) {
                $tour->seo_description = $this->generateSeoDescription($tour);
                $needsUpdate = true;
            }

            // Generate SEO Keywords
            if ($force || empty($tour->seo_keywords)) {
                $tour->seo_keywords = $this->generateSeoKeywords($tour);
                $needsUpdate = true;
            }

            if ($needsUpdate) {
                $tour->save();
                $updated++;
            } else {
                $skipped++;
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);

        // Summary
        $this->info("✅ SEO data generation complete!");
        $this->table(
            ['Status', 'Count'],
            [
                ['Updated', $updated],
                ['Skipped (already had data)', $skipped],
                ['Total', $tours->count()],
            ]
        );

        if ($skipped > 0 && !$force) {
            $this->newLine();
            $this->comment('💡 Tip: Use --force to overwrite existing SEO data');
        }

        return Command::SUCCESS;
    }

    /**
     * Generate optimized SEO title
     */
    private function generateSeoTitle(Tour $tour): string
    {
        // Format: "Tour Name | City | Jahongir Travel"
        // Keep under 60 characters for Google

        $title = $tour->title;

        // If title is already long, just append brand
        if (strlen($title) > 45) {
            return Str::limit($title, 45, '') . ' | Jahongir Travel';
        }

        // Add city if available and title is short enough
        if ($tour->city && strlen($title) < 35) {
            return "{$title} | {$tour->city->name} | Jahongir Travel";
        }

        return "{$title} | Jahongir Travel";
    }

    /**
     * Generate optimized SEO description
     */
    private function generateSeoDescription(Tour $tour): string
    {
        // Use short_description if available, otherwise extract from long_description
        if (!empty($tour->short_description)) {
            $description = strip_tags($tour->short_description);
        } else {
            $description = strip_tags($tour->long_description);
        }

        // Add compelling CTA
        $description = trim($description);

        // If description doesn't mention price/days, add them
        $additions = [];
        if ($tour->duration_days) {
            $additions[] = "{$tour->duration_days}-day tour";
        }
        if ($tour->price_per_person) {
            $additions[] = "from \${$tour->price_per_person}";
        }

        if (!empty($additions)) {
            $suffix = ' ' . implode(' • ', $additions) . '. Book now!';
            $maxLength = 160 - strlen($suffix);
            $description = Str::limit($description, $maxLength, '');
            $description .= $suffix;
        } else {
            $description = Str::limit($description, 157, '...');
        }

        return $description;
    }

    /**
     * Generate SEO keywords
     */
    private function generateSeoKeywords(Tour $tour): string
    {
        $keywords = [];

        // Add city name
        if ($tour->city) {
            $keywords[] = strtolower($tour->city->name);
            $keywords[] = strtolower($tour->city->name) . ' tours';
        }

        // Add tour type
        $keywords[] = strtolower($tour->tour_type) . ' tour';

        // Add generic keywords
        $keywords[] = 'uzbekistan tours';
        $keywords[] = 'central asia travel';

        // Extract key words from title
        $titleWords = explode(' ', strtolower($tour->title));
        $importantWords = array_filter($titleWords, function($word) {
            return strlen($word) > 4 && !in_array($word, ['tours', 'travel', 'adventure']);
        });
        $keywords = array_merge($keywords, array_slice($importantWords, 0, 3));

        return implode(', ', array_unique($keywords));
    }
}
