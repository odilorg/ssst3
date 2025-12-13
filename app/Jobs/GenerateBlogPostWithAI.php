<?php

namespace App\Jobs;

use App\Models\BlogPost;
use App\Models\BlogAIGeneration;
use App\Models\BlogTag;
use App\Models\BlogCategory;
use App\Services\BlogAIService;
use Filament\Notifications\Notification;
use Filament\Notifications\Actions\Action;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class GenerateBlogPostWithAI implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 300;
    public $tries = 1;

    public function __construct(
        public BlogAIGeneration $generation
    ) {}

    public function handle(BlogAIService $aiService): void
    {
        try {
            $this->generation->update(['status' => 'processing']);

            $blogData = $aiService->generateBlogPost($this->generation->input_parameters);

            $blogPost = DB::transaction(function () use ($blogData) {
                $category = BlogCategory::where('name', 'LIKE', '%' . ($blogData['suggested_category'] ?? '') . '%')->first();

                $blogPost = BlogPost::create([
                    'title' => $blogData['title'],
                    'slug' => Str::slug($blogData['title']) . '-' . time(),
                    'excerpt' => $blogData['excerpt'],
                    'content' => $blogData['content'],
                    'category_id' => $category?->id,
                    'author_name' => 'Jahongir Travel Team',
                    'reading_time' => $blogData['reading_time'] ?? 5,
                    'view_count' => 0,
                    'is_featured' => false,
                    'is_published' => false,
                    'meta_title' => $blogData['meta_title'] ?? null,
                    'meta_description' => $blogData['meta_description'] ?? null,
                ]);

                if (!empty($blogData['suggested_tags'])) {
                    $tagIds = [];
                    foreach ($blogData['suggested_tags'] as $tagName) {
                        $tag = BlogTag::firstOrCreate(
                            ['name' => $tagName],
                            ['slug' => Str::slug($tagName)]
                        );
                        $tagIds[] = $tag->id;
                    }
                    $blogPost->tags()->sync($tagIds);
                }

                return $blogPost;
            });

            $this->generation->update([
                'blog_post_id' => $blogPost->id,
                'status' => 'completed',
                'completed_at' => now(),
                'ai_response' => $blogData,
                'tokens_used' => $blogData['_meta']['tokens_used'] ?? null,
                'cost' => $blogData['_meta']['cost'] ?? null,
            ]);

            Notification::make()
                ->success()
                ->title('Blog Post Generated Successfully!')
                ->body("'{$blogPost->title}' is ready to edit and publish.")
                ->actions([
                    Action::make('edit')
                        ->button()
                        ->url(route('filament.admin.resources.blog-posts.blog-posts.edit', ['record' => $blogPost->id]))
                ])
                ->sendToDatabase($this->generation->user);

            Log::info('Blog post generated successfully', [
                'generation_id' => $this->generation->id,
                'blog_post_id' => $blogPost->id,
                'tokens_used' => $blogData['_meta']['tokens_used'] ?? 0,
                'cost' => $blogData['_meta']['cost'] ?? 0,
            ]);

        } catch (\Exception $e) {
            $this->generation->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
                'completed_at' => now(),
            ]);

            Notification::make()
                ->danger()
                ->title('Blog Generation Failed')
                ->body('An error occurred while generating your blog post. Please try again.')
                ->sendToDatabase($this->generation->user);

            Log::error('Blog generation failed', [
                'generation_id' => $this->generation->id,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        if ($this->generation->status !== 'failed') {
            $this->generation->update([
                'status' => 'failed',
                'error_message' => $exception->getMessage(),
                'completed_at' => now(),
            ]);
        }

        Notification::make()
            ->danger()
            ->title('Blog Generation Failed')
            ->body('The AI blog generation job failed. Please contact support if this persists.')
            ->sendToDatabase($this->generation->user);
    }
}
