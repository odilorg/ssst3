<?php

namespace App\Console\Commands;

use App\Models\BlogPost;
use App\Models\BlogCategory;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class ImportWordPressInsights extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'insights:import-wordpress {--skip-existing : Skip insights that already exist}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import insights from old WordPress site via REST API';

    private const WP_API_URL = 'https://jahongir-travel.uz/wp-json/wp/v2/insight';
    private const WP_MEDIA_URL = 'https://jahongir-travel.uz/wp-json/wp/v2/media';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔄 Importing insights from WordPress site...');
        $this->newLine();

        // Fetch all insights from WordPress API
        $this->info('Fetching insights from WordPress API...');
        $insights = $this->fetchAllInsights();

        if (empty($insights)) {
            $this->error('No insights found or API is unavailable.');
            return Command::FAILURE;
        }

        $this->info("Found " . count($insights) . " insights to import");
        $this->newLine();

        $progressBar = $this->output->createProgressBar(count($insights));
        $progressBar->start();

        $imported = 0;
        $skipped = 0;
        $errors = 0;

        foreach ($insights as $wpInsight) {
            try {
                $slug = $wpInsight['slug'] ?? null;

                if (!$slug) {
                    $errors++;
                    $progressBar->advance();
                    continue;
                }

                // Check if insight already exists
                if ($this->option('skip-existing') && BlogPost::where('slug', $slug)->exists()) {
                    $skipped++;
                    $progressBar->advance();
                    continue;
                }

                // Import the insight
                $this->importInsight($wpInsight);
                $imported++;

            } catch (\Exception $e) {
                $this->error("\nError importing insight: " . $e->getMessage());
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
                ['Total processed', count($insights)],
            ]
        );

        if ($imported > 0) {
            $this->newLine();
            $this->comment('💡 Next steps:');
            $this->comment('1. Review imported insights in Filament admin');
            $this->comment('2. Add categories, tags, and download featured images');
            $this->comment('3. Verify SEO metadata');
            $this->comment('4. Publish insights when ready');
        }

        return Command::SUCCESS;
    }

    /**
     * Fetch all insights from WordPress API
     */
    private function fetchAllInsights(): array
    {
        $allInsights = [];
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

            $insights = $response->json();
            if (empty($insights)) {
                break;
            }

            $allInsights = array_merge($allInsights, $insights);
            $page++;

        } while (count($insights) === $perPage);

        return $allInsights;
    }

    /**
     * Import a single insight
     */
    private function importInsight(array $wpInsight): void
    {
        // Extract title
        $title = strip_tags($wpInsight['title']['rendered'] ?? '');

        // Extract content and excerpt
        $content = $wpInsight['content']['rendered'] ?? '';
        $excerpt = strip_tags($wpInsight['excerpt']['rendered'] ?? '');

        // Calculate reading time (rough estimate: 200 words per minute)
        $wordCount = str_word_count(strip_tags($content));
        $readingTime = max(1, ceil($wordCount / 200));

        // Get or create Insights category
        $category = BlogCategory::firstOrCreate(
            ['slug' => 'insights'],
            [
                'name' => 'Insights',
                'description' => 'Cultural insights and historical articles about Uzbekistan and Central Asia',
                'is_active' => true,
            ]
        );

        // Get featured media URL if available
        $featuredImage = null;
        if (!empty($wpInsight['featured_media'])) {
            $featuredImage = $this->getFeaturedMediaUrl($wpInsight['featured_media']);
        }

        // Create or update blog post (insights are stored as blog posts)
        BlogPost::updateOrCreate(
            ['slug' => $wpInsight['slug']],
            [
                'title' => $title,
                'excerpt' => Str::limit($excerpt, 255),
                'content' => $this->cleanHtmlContent($content),
                'category_id' => $category->id,

                // Featured image (external URL for now)
                'featured_image' => $featuredImage,

                // Author info
                'author_name' => 'Jahongir Travel',

                // Reading time
                'reading_time' => $readingTime,

                // Set as unpublished until reviewed
                'is_published' => false,
                'is_featured' => false,

                // SEO fields
                'meta_title' => Str::limit($title, 60),
                'meta_description' => Str::limit($excerpt, 160),
            ]
        );
    }

    /**
     * Get featured media URL from WordPress
     */
    private function getFeaturedMediaUrl(int $mediaId): ?string
    {
        try {
            $response = Http::timeout(10)->get(self::WP_MEDIA_URL . '/' . $mediaId);

            if ($response->successful()) {
                $media = $response->json();
                return $media['source_url'] ?? null;
            }
        } catch (\Exception $e) {
            // Silently fail, we can add images manually later
        }

        return null;
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
