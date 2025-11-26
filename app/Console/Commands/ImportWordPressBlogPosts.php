<?php

namespace App\Console\Commands;

use App\Models\BlogPost;
use App\Models\BlogCategory;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class ImportWordPressBlogPosts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'blog:import-wordpress {--skip-existing : Skip posts that already exist}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import blog posts from old WordPress site via REST API';

    private const WP_API_URL = 'https://jahongir-travel.uz/wp-json/wp/v2/posts';
    private const WP_MEDIA_URL = 'https://jahongir-travel.uz/wp-json/wp/v2/media';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔄 Importing blog posts from WordPress site...');
        $this->newLine();

        // Fetch all posts from WordPress API
        $this->info('Fetching posts from WordPress API...');
        $posts = $this->fetchAllPosts();

        if (empty($posts)) {
            $this->error('No posts found or API is unavailable.');
            return Command::FAILURE;
        }

        $this->info("Found " . count($posts) . " posts to import");
        $this->newLine();

        $progressBar = $this->output->createProgressBar(count($posts));
        $progressBar->start();

        $imported = 0;
        $skipped = 0;
        $errors = 0;

        foreach ($posts as $wpPost) {
            try {
                $slug = $wpPost['slug'] ?? null;

                if (!$slug) {
                    $errors++;
                    $progressBar->advance();
                    continue;
                }

                // Check if post already exists
                if ($this->option('skip-existing') && BlogPost::where('slug', $slug)->exists()) {
                    $skipped++;
                    $progressBar->advance();
                    continue;
                }

                // Import the post
                $this->importPost($wpPost);
                $imported++;

            } catch (\Exception $e) {
                $this->error("\nError importing post: " . $e->getMessage());
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
                ['Total processed', count($posts)],
            ]
        );

        if ($imported > 0) {
            $this->newLine();
            $this->comment('💡 Next steps:');
            $this->comment('1. Review imported posts in Filament admin');
            $this->comment('2. Add categories, tags, and featured images');
            $this->comment('3. Verify SEO metadata');
        }

        return Command::SUCCESS;
    }

    /**
     * Fetch all posts from WordPress API
     */
    private function fetchAllPosts(): array
    {
        $allPosts = [];
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

            $posts = $response->json();
            if (empty($posts)) {
                break;
            }

            $allPosts = array_merge($allPosts, $posts);
            $page++;

        } while (count($posts) === $perPage);

        return $allPosts;
    }

    /**
     * Import a single post
     */
    private function importPost(array $wpPost): void
    {
        // Extract title
        $title = strip_tags($wpPost['title']['rendered'] ?? '');

        // Extract content and excerpt
        $content = $wpPost['content']['rendered'] ?? '';
        $excerpt = strip_tags($wpPost['excerpt']['rendered'] ?? '');

        // Calculate reading time (rough estimate: 200 words per minute)
        $wordCount = str_word_count(strip_tags($content));
        $readingTime = max(1, ceil($wordCount / 200));

        // Get or create default category
        $category = BlogCategory::firstOrCreate(
            ['slug' => 'imported'],
            [
                'name' => 'Imported from WordPress',
                'description' => 'Posts imported from the old WordPress site',
                'is_active' => true,
            ]
        );

        // Get featured media URL if available
        $featuredImage = null;
        if (!empty($wpPost['featured_media'])) {
            $featuredImage = $this->getFeaturedMediaUrl($wpPost['featured_media']);
        }

        // Create or update post
        BlogPost::updateOrCreate(
            ['slug' => $wpPost['slug']],
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
