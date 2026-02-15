<?php

namespace App\Jobs;

use App\Models\City;
use App\Models\CityTranslation;
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

class TranslateCityWithAI implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 600;
    public $tries = 3;
    public $backoff = [30, 120, 300];
    public $uniqueFor = 1200;

    public function __construct(
        public int $cityId,
        public int $translationId,
        public int $userId,
    ) {}

    public function uniqueId(): string
    {
        return "translate:city:{$this->cityId}:{$this->translationId}";
    }

    public function handle(TranslationService $translationService): void
    {
        $lock = Cache::lock("translate:city:{$this->cityId}:trans:{$this->translationId}", 1200);
        if (!$lock->get()) {
            Log::info('City translation already in progress, skipping duplicate', [
                'city_id' => $this->cityId,
                'translation_id' => $this->translationId,
            ]);
            return;
        }

        $startTime = microtime(true);

        try {
            // Enforce rate and cost limits before proceeding
            $translationService->checkRateLimits($this->userId);
            $translationService->checkCostLimits();

            $city = City::findOrFail($this->cityId);
            $translation = CityTranslation::findOrFail($this->translationId);

            Log::info('Starting city AI translation', [
                'city_id' => $city->id,
                'translation_id' => $translation->id,
                'locale' => $translation->locale,
                'user_id' => $this->userId,
            ]);

            $result = $translationService->translateCity($city, $translation->locale);

            $translation->update($result['translations']);

            $duration = round(microtime(true) - $startTime, 2);

            TranslationLog::logFor($city, [
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
                ->title('City Translation Completed!')
                ->body("City '{$city->name}' translated to {$translation->locale}. Duration: {$duration}s")
                ->actions([
                    Action::make('view')
                        ->button()
                        ->url(route('filament.admin.resources.cities.edit', ['record' => $city->id]))
                ])
                ->sendToDatabase(\App\Models\User::find($this->userId));

            Log::info('City AI translation completed', [
                'city_id' => $city->id,
                'locale' => $translation->locale,
                'duration' => $duration,
            ]);

        } catch (\Exception $e) {
            $duration = round(microtime(true) - $startTime, 2);

            try {
                $city = City::find($this->cityId);
                $locale = CityTranslation::find($this->translationId)?->locale ?? 'unknown';

                if ($city) {
                    TranslationLog::logFor($city, [
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
                Log::warning('Failed to log city translation error', ['error' => $logException->getMessage()]);
            }

            Notification::make()
                ->danger()
                ->title('City Translation Failed')
                ->body("Failed to translate city. Error: {$e->getMessage()}")
                ->sendToDatabase(\App\Models\User::find($this->userId));

            Log::error('City AI translation failed', [
                'city_id' => $this->cityId,
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
        Log::error('City AI translation job failed permanently', [
            'city_id' => $this->cityId,
            'translation_id' => $this->translationId,
            'error' => $exception->getMessage(),
        ]);

        Notification::make()
            ->danger()
            ->title('City Translation Job Failed')
            ->body('The city AI translation job failed permanently. Please try again later.')
            ->sendToDatabase(\App\Models\User::find($this->userId));
    }
}
