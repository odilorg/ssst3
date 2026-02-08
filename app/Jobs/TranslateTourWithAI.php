<?php

namespace App\Jobs;

use App\Models\Tour;
use App\Models\TourTranslation;
use App\Models\TranslationLog;
use App\Services\OpenAI\TranslationService;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class TranslateTourWithAI implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 600; // 10 minutes timeout for translation
    public $tries = 1; // Only try once

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Tour $tour,
        public TourTranslation $translation,
        public int $userId,
        public array $sectionsToTranslate = []
    ) {}

    /**
     * Execute the job.
     */
    public function handle(TranslationService $translationService): void
    {
        $startTime = microtime(true);

        try {
            Log::info('Starting AI translation', [
                'tour_id' => $this->tour->id,
                'translation_id' => $this->translation->id,
                'locale' => $this->translation->locale,
                'user_id' => $this->userId,
            ]);

            // Perform translation
            $result = $translationService->translateTour(
                $this->tour,
                $this->translation->locale,
                $this->sectionsToTranslate
            );

            // Update translation with AI-generated content
            $this->translation->update($result['translations']);

            $duration = round(microtime(true) - $startTime, 2);

            // Log translation
            TranslationLog::create([
                'tour_id' => $this->tour->id,
                'translation_id' => $this->translation->id,
                'locale' => $this->translation->locale,
                'sections_translated' => array_keys($result['translations']),
                'tokens_used' => $result['tokens_used'],
                'cost' => $translationService->estimateCost($result['tokens_used'], $result['tokens_used']),
                'duration_seconds' => $duration,
                'status' => 'completed',
            ]);

            // Send success notification
            Notification::make()
                ->success()
                ->title('Translation Completed!')
                ->body("Tour '{$this->tour->title}' has been translated to {$this->translation->locale}. Duration: {$duration}s")
                ->actions([
                    Action::make('view')
                        ->button()
                        ->url(route('filament.admin.resources.tours.edit', ['record' => $this->tour->id]))
                ])
                ->sendToDatabase(\App\Models\User::find($this->userId));

            Log::info('AI translation completed successfully', [
                'tour_id' => $this->tour->id,
                'locale' => $this->translation->locale,
                'duration' => $duration,
                'tokens_used' => $result['tokens_used'],
            ]);

        } catch (\Exception $e) {
            $duration = round(microtime(true) - $startTime, 2);

            // Log failed translation
            TranslationLog::create([
                'tour_id' => $this->tour->id,
                'translation_id' => $this->translation->id,
                'locale' => $this->translation->locale,
                'sections_translated' => [],
                'tokens_used' => 0,
                'cost' => 0,
                'duration_seconds' => $duration,
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            // Send failure notification
            Notification::make()
                ->danger()
                ->title('Translation Failed')
                ->body("Failed to translate tour '{$this->tour->title}' to {$this->translation->locale}. Error: {$e->getMessage()}")
                ->sendToDatabase(\App\Models\User::find($this->userId));

            Log::error('AI translation failed', [
                'tour_id' => $this->tour->id,
                'locale' => $this->translation->locale,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('AI translation job failed permanently', [
            'tour_id' => $this->tour->id,
            'locale' => $this->translation->locale,
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
