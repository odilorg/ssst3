<?php

namespace App\Jobs;

use App\Models\Tour;
use App\Models\TourTranslation;
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

class TranslateTourWithAI implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 600; // 10 minutes timeout for translation
    public $tries = 3;
    public $backoff = [30, 120, 300]; // 30s, 2min, 5min
    public $uniqueFor = 900; // ShouldBeUnique: 15 min window

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $tourId,
        public int $translationId,
        public int $userId,
        public array $sectionsToTranslate = []
    ) {}

    /**
     * Unique ID prevents duplicate dispatches for the same translation.
     */
    public function uniqueId(): string
    {
        return "translate:{$this->tourId}:{$this->translationId}";
    }

    /**
     * Execute the job.
     */
    public function handle(TranslationService $translationService): void
    {
        // Idempotency: acquire lock to prevent concurrent runs
        $lock = Cache::lock("translate:tour:{$this->tourId}:trans:{$this->translationId}", 900);
        if (!$lock->get()) {
            Log::info('Translation already in progress, skipping duplicate', [
                'tour_id' => $this->tourId,
                'translation_id' => $this->translationId,
            ]);
            return;
        }

        $startTime = microtime(true);

        try {
            // Fetch models fresh from database
            $tour = Tour::findOrFail($this->tourId);
            $translation = TourTranslation::findOrFail($this->translationId);

            Log::info('Starting AI translation', [
                'tour_id' => $tour->id,
                'translation_id' => $translation->id,
                'locale' => $translation->locale,
                'user_id' => $this->userId,
            ]);

            // Perform translation
            $result = $translationService->translateTour(
                $tour,
                $translation->locale,
                $this->sectionsToTranslate
            );

            // Update translation with AI-generated content
            $translation->update($result['translations']);

            $duration = round(microtime(true) - $startTime, 2);

            // Log translation
            TranslationLog::create([
                'tour_id' => $tour->id,
                'user_id' => $this->userId,
                'source_locale' => 'en',
                'target_locale' => $translation->locale,
                'sections_translated' => array_keys($result['translations']),
                'tokens_used' => $result['tokens_used'],
                'cost_usd' => $translationService->estimateCost($result['tokens_used'], $result['tokens_used']),
                'model' => config('ai-translation.deepseek.model', 'deepseek-chat'),
                'status' => 'completed',
            ]);

            // Send success notification
            Notification::make()
                ->success()
                ->title('Translation Completed!')
                ->body("Tour '{$tour->title}' has been translated to {$translation->locale}. Duration: {$duration}s")
                ->actions([
                    Action::make('view')
                        ->button()
                        ->url(route('filament.admin.resources.tours.edit', ['record' => $tour->id]))
                ])
                ->sendToDatabase(\App\Models\User::find($this->userId));

            Log::info('AI translation completed successfully', [
                'tour_id' => $tour->id,
                'locale' => $translation->locale,
                'duration' => $duration,
                'tokens_used' => $result['tokens_used'],
            ]);

        } catch (\Exception $e) {
            $duration = round(microtime(true) - $startTime, 2);

            // Log failed translation
            try {
                TranslationLog::create([
                    'tour_id' => $this->tourId,
                    'user_id' => $this->userId,
                    'source_locale' => 'en',
                    'target_locale' => TourTranslation::find($this->translationId)?->locale ?? 'unknown',
                    'sections_translated' => [],
                    'tokens_used' => 0,
                    'cost_usd' => 0,
                    'model' => config('ai-translation.deepseek.model', 'deepseek-chat'),
                    'status' => 'failed',
                    'error_message' => $e->getMessage(),
                ]);
            } catch (\Exception $logException) {
                Log::warning('Failed to log translation error', ['error' => $logException->getMessage()]);
            }

            // Send failure notification
            Notification::make()
                ->danger()
                ->title('Translation Failed')
                ->body("Failed to translate tour. Error: {$e->getMessage()}")
                ->sendToDatabase(\App\Models\User::find($this->userId));

            Log::error('AI translation failed', [
                'tour_id' => $this->tourId,
                'translation_id' => $this->translationId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        } finally {
            $lock->release();
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('AI translation job failed permanently', [
            'tour_id' => $this->tourId,
            'translation_id' => $this->translationId,
            'error' => $exception->getMessage(),
        ]);

        // Ensure notification was sent
        Notification::make()
            ->danger()
            ->title('Translation Job Failed')
            ->body("The AI translation job failed permanently. Please contact support if the issue persists.")
            ->sendToDatabase(\App\Models\User::find($this->userId));
    }
}
