<?php

namespace App\Console\Commands;

use App\Jobs\ConvertImageToWebP;
use App\Models\BlogPost;
use App\Models\City;
use App\Models\Tour;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ConvertExistingImagesToWebP extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'images:convert-to-webp 
                            {--model= : Specific model to process (tour, blog, city, or all)}
                            {--limit= : Limit number of images to process (for testing)}
                            {--dry-run : Show what would be processed without actually dispatching jobs}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Batch convert existing images to WebP format';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting batch WebP conversion...');
        $this->newLine();

        $modelFilter = $this->option('model') ?? 'all';
        $limit = $this->option('limit');
        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->warn('DRY RUN MODE - No jobs will be dispatched');
            $this->newLine();
        }

        $totalDispatched = 0;

        // Process Tours
        if ($modelFilter === 'all' || $modelFilter === 'tour') {
            $this->info('Processing Tours...');
            $count = $this->processTours($limit, $dryRun);
            $totalDispatched += $count;
            $this->info("Tours: {$count} jobs " . ($dryRun ? 'would be' : '') . " dispatched");
            $this->newLine();
        }

        // Process Blog Posts
        if ($modelFilter === 'all' || $modelFilter === 'blog') {
            $this->info('Processing Blog Posts...');
            $count = $this->processBlogPosts($limit, $dryRun);
            $totalDispatched += $count;
            $this->info("Blog Posts: {$count} jobs " . ($dryRun ? 'would be' : '') . " dispatched");
            $this->newLine();
        }

        // Process Cities
        if ($modelFilter === 'all' || $modelFilter === 'city') {
            $this->info('Processing Cities...');
            $count = $this->processCities($limit, $dryRun);
            $totalDispatched += $count;
            $this->info("Cities: {$count} jobs " . ($dryRun ? 'would be' : '') . " dispatched");
            $this->newLine();
        }

        $this->info("Total: {$totalDispatched} jobs " . ($dryRun ? 'would be' : '') . " dispatched");
        
        if (!$dryRun && $totalDispatched > 0) {
            $this->newLine();
            $this->comment('Jobs have been dispatched to the queue. Monitor progress with:');
            $this->comment('  php artisan queue:work --queue=image-processing');
        }

        return Command::SUCCESS;
    }

    /**
     * Process Tour images
     */
    protected function processTours(?int $limit, bool $dryRun): int
    {
        $query = Tour::query()
            ->whereNotNull('hero_image')
            ->where(function ($q) {
                $q->whereNull('hero_image_webp')
                  ->orWhere('image_processing_status', '!=', 'completed');
            });

        if ($limit) {
            $query->limit($limit);
        }

        $tours = $query->get();
        $count = 0;

        foreach ($tours as $tour) {
            if (!$dryRun) {
                ConvertImageToWebP::dispatch($tour, 'hero_image');
            }
            $count++;
            
            if ($this->output->isVerbose()) {
                $this->line("  - Tour #{$tour->id}: {$tour->title}");
            }
        }

        return $count;
    }

    /**
     * Process BlogPost images
     */
    protected function processBlogPosts(?int $limit, bool $dryRun): int
    {
        $query = BlogPost::query()
            ->whereNotNull('featured_image')
            ->where(function ($q) {
                $q->whereNull('featured_image_webp')
                  ->orWhere('image_processing_status', '!=', 'completed');
            });

        if ($limit) {
            $query->limit($limit);
        }

        $posts = $query->get();
        $count = 0;

        foreach ($posts as $post) {
            if (!$dryRun) {
                ConvertImageToWebP::dispatch($post, 'featured_image');
            }
            $count++;
            
            if ($this->output->isVerbose()) {
                $this->line("  - Blog Post #{$post->id}: {$post->title}");
            }
        }

        return $count;
    }

    /**
     * Process City images
     */
    protected function processCities(?int $limit, bool $dryRun): int
    {
        $query = City::query()
            ->whereNotNull('hero_image')
            ->where(function ($q) {
                $q->whereNull('hero_image_webp')
                  ->orWhere('image_processing_status', '!=', 'completed');
            });

        if ($limit) {
            $query->limit($limit);
        }

        $cities = $query->get();
        $count = 0;

        foreach ($cities as $city) {
            if (!$dryRun) {
                ConvertImageToWebP::dispatch($city, 'hero_image');
            }
            $count++;
            
            if ($this->output->isVerbose()) {
                $this->line("  - City #{$city->id}: {$city->name}");
            }
        }

        return $count;
    }
}
