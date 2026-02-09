<?php

namespace App\Jobs;

use App\Models\BlogPost;
use App\Models\BlogPostTranslation;
use App\Models\TranslationLog;
use App\Services\OpenAI\TranslationService;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class TranslateBlogPostWithAI implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 600;
    public $tries = 3;
    public $backoff = [30, 120, 300];
    public $uniqueFor = 1200;

    public function __construct(
        public int $blogPostId,
        public int $translationId,
        public int $userId,
    ) {}

    public function uniqueId(): string
    {
        return "translate:blog:{$this->blogPostId}:{$this->translationId}";
    }

    public function handle(TranslationService $translationService): void
    {
        $lock = Cache::lock("translate:blog:{$this->blogPostId}:trans:{$this->translationId}", 1200);
        if (!$lock->get()) {
            Log::info('Blog translation already in progress, skipping duplicate', [
                'blog_post_id' => $this->blogPostId,
                'translation_id' => $this->translationId,
            ]);
            return;
        }

        $startTime = microtime(true);

        try {
            $blogPost = BlogPost::findOrFail($this->blogPostId);
            $translation = BlogPostTranslation::findOrFail($this->translationId);

            Log::info('Starting blog AI translation', [
                'blog_post_id' => $blogPost->id,
                'translation_id' => $translation->id,
                'locale' => $translation->locale,
                'user_id' => $this->userId,
            ]);

            $result = $translationService->translateBlogPost(
                $blogPost,
                $translation->locale,
                $translation->id
            );

            $translation->update($result['translations']);

            $duration = round(microtime(true) - $startTime, 2);

            TranslationLog::logFor($blogPost, [
                'user_id' => $this->userId,
                'source_locale' => 'en',
                'target_locale' => $translation->locale,
                'sections_translated' => array_keys($result['translations']),
                'tokens_used' => $result['tokens_used'],
                'cost_usd' => $translationService->estimateCost($result['tokens_used'], $result['tokens_used']),
                'model' => config('ai-translation.deepseek.model', 'deepseek-chat'),
                'status' => 'completed',
            ]);

            Notification::make()
                ->success()
                ->title('Blog Translation Completed!')
                ->body("Blog post '{$blogPost->title}' translated to {$translation->locale}. Duration: {$duration}s")
                ->sendToDatabase(\App\Models\User::find($this->userId));

            Log::info('Blog AI translation completed', [
                'blog_post_id' => $blogPost->id,
                'locale' => $translation->locale,
                'duration' => $duration,
                'tokens_used' => $result['tokens_used'],
            ]);

        } catch (\Exception $e) {
            $duration = round(microtime(true) - $startTime, 2);

            try {
                $blogPost = BlogPost::find($this->blogPostId);
                $locale = BlogPostTranslation::find($this->translationId)?->locale ?? 'unknown';

                if ($blogPost) {
                    TranslationLog::logFor($blogPost, [
                        'user_id' => $this->userId,
                        'source_locale' => 'en',
                        'target_locale' => $locale,
                        'sections_translated' => [],
                        'tokens_used' => 0,
                        'cost_usd' => 0,
                        'model' => config('ai-translation.deepseek.model', 'deepseek-chat'),
                        'status' => 'failed',
                        'error_message' => $e->getMessage(),
                    ]);
                }
            } catch (\Exception $logException) {
                Log::warning('Failed to log blog translation error', ['error' => $logException->getMessage()]);
            }

            Notification::make()
                ->danger()
                ->title('Blog Translation Failed')
                ->body("Failed to translate blog post. Error: {$e->getMessage()}")
                ->sendToDatabase(\App\Models\User::find($this->userId));

            Log::error('Blog AI translation failed', [
                'blog_post_id' => $this->blogPostId,
                'translation_id' => $this->translationId,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        } finally {
            $lock->release();
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('Blog AI translation job failed permanently', [
            'blog_post_id' => $this->blogPostId,
            'translation_id' => $this->translationId,
            'error' => $exception->getMessage(),
        ]);

        Notification::make()
            ->danger()
            ->title('Blog Translation Job Failed')
            ->body('The blog AI translation job failed permanently. Please try again later.')
            ->sendToDatabase(\App\Models\User::find($this->userId));
    }
}
