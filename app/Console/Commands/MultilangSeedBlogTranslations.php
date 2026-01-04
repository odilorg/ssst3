<?php

namespace App\Console\Commands;

use App\Models\BlogPost;
use App\Models\BlogPostTranslation;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Seed Blog Post Translations Command
 *
 * Creates default locale translations for all blog posts by copying
 * existing BlogPost fields into the blog_post_translations table.
 *
 * Usage:
 *   php artisan multilang:seed-blog-translations
 *   php artisan multilang:seed-blog-translations --force
 */
class MultilangSeedBlogTranslations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'multilang:seed-blog-translations
                            {--force : Overwrite existing translations for default locale}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed blog_post_translations table with default locale data from existing blog posts';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $defaultLocale = config('multilang.default_locale', 'en');
        $force = $this->option('force');

        $this->info("Seeding blog post translations for default locale: {$defaultLocale}");
        $this->newLine();

        // Get all blog posts
        $posts = BlogPost::all();

        if ($posts->isEmpty()) {
            $this->warn('No blog posts found in database.');
            return Command::SUCCESS;
        }

        $this->info("Found {$posts->count()} blog posts to process.");
        $this->newLine();

        $progressBar = $this->output->createProgressBar($posts->count());
        $progressBar->start();

        $created = 0;
        $updated = 0;
        $skipped = 0;
        $errors = 0;

        DB::beginTransaction();

        try {
            foreach ($posts as $post) {
                $result = $this->processPost($post, $defaultLocale, $force);

                switch ($result) {
                    case 'created':
                        $created++;
                        break;
                    case 'updated':
                        $updated++;
                        break;
                    case 'skipped':
                        $skipped++;
                        break;
                    case 'error':
                        $errors++;
                        break;
                }

                $progressBar->advance();
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->newLine(2);
            $this->error("Transaction failed: {$e->getMessage()}");
            return Command::FAILURE;
        }

        $progressBar->finish();
        $this->newLine(2);

        // Summary table
        $this->info('Seeding complete!');
        $this->table(
            ['Status', 'Count'],
            [
                ['Created', $created],
                ['Updated', $updated],
                ['Skipped', $skipped],
                ['Errors', $errors],
                ['Total', $posts->count()],
            ]
        );

        if ($skipped > 0 && !$force) {
            $this->newLine();
            $this->comment('Use --force to overwrite existing translations.');
        }

        return Command::SUCCESS;
    }

    /**
     * Process a single blog post and create/update its translation.
     */
    private function processPost(BlogPost $post, string $locale, bool $force): string
    {
        try {
            // Check if translation already exists
            $existing = BlogPostTranslation::where('blog_post_id', $post->id)
                ->where('locale', $locale)
                ->first();

            if ($existing && !$force) {
                return 'skipped';
            }

            // Prepare translation data with defensive field mapping
            $data = $this->mapPostToTranslation($post, $locale);

            if ($existing) {
                // Update existing translation
                $existing->update($data);
                return 'updated';
            }

            // Create new translation
            BlogPostTranslation::create($data);
            return 'created';

        } catch (\Exception $e) {
            $this->newLine();
            $this->warn("Error processing blog post {$post->id}: {$e->getMessage()}");
            return 'error';
        }
    }

    /**
     * Map BlogPost fields to BlogPostTranslation fields defensively.
     */
    private function mapPostToTranslation(BlogPost $post, string $locale): array
    {
        // Defensive title mapping
        $title = $this->getFieldValue($post, ['title', 'name'], 'Untitled Post');

        // Defensive slug mapping
        $slug = $this->getFieldValue($post, ['slug'], null);
        if (empty($slug)) {
            $slug = \Illuminate\Support\Str::slug($title);
        }

        // Map excerpt
        $excerpt = $this->getFieldValue($post, ['excerpt', 'short_description'], null);

        // Map content
        $content = $this->getFieldValue($post, ['content', 'body', 'long_description'], null);

        // Map SEO fields defensively
        $seoTitle = $this->getFieldValue($post, ['meta_title', 'seo_title'], null);
        $seoDescription = $this->getFieldValue($post, ['meta_description', 'seo_description'], null);

        return [
            'blog_post_id' => $post->id,
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
     */
    private function getFieldValue(BlogPost $post, array $fieldNames, mixed $default = null): mixed
    {
        foreach ($fieldNames as $fieldName) {
            if (isset($post->{$fieldName}) && !empty($post->{$fieldName})) {
                return $post->{$fieldName};
            }

            if (isset($post->getAttributes()[$fieldName]) && !empty($post->getAttributes()[$fieldName])) {
                return $post->getAttributes()[$fieldName];
            }
        }

        return $default;
    }
}
