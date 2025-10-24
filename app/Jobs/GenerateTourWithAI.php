<?php

namespace App\Jobs;

use App\Models\Tour;
use App\Models\TourAIGeneration;
use App\Services\TourAIService;
use Filament\Notifications\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GenerateTourWithAI implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 300; // 5 minutes timeout
    public $tries = 1; // Only try once

    /**
     * Create a new job instance.
     */
    public function __construct(
        public TourAIGeneration $generation
    ) {}

    /**
     * Execute the job.
     */
    public function handle(TourAIService $aiService): void
    {
        try {
            // Update status to processing
            $this->generation->update(['status' => 'processing']);

            // Generate tour data using AI
            $tourData = $aiService->generateTour($this->generation->input_parameters);

            // Create tour and itinerary items in transaction
            $tour = DB::transaction(function () use ($tourData) {
                // Create tour
                $tour = Tour::create([
                    'title' => $tourData['title'],
                    'duration_days' => $tourData['duration_days'],
                    'description' => $tourData['description'] ?? null,
                    'status' => 'draft',
                ]);

                // Create days and stops
                foreach ($tourData['days'] as $dayIndex => $dayData) {
                    $day = $tour->itineraryItems()->create([
                        'type' => 'day',
                        'title' => $dayData['title'],
                        'description' => $dayData['description'] ?? null,
                        'default_start_time' => $dayData['default_start_time'] ?? '09:00',
                        'sort_order' => $dayIndex,
                    ]);

                    // Create stops for this day
                    if (isset($dayData['stops']) && is_array($dayData['stops'])) {
                        foreach ($dayData['stops'] as $stopIndex => $stopData) {
                            $day->children()->create([
                                'tour_id' => $tour->id,
                                'type' => 'stop',
                                'title' => $stopData['title'],
                                'description' => $stopData['description'] ?? null,
                                'default_start_time' => $stopData['default_start_time'] ?? null,
                                'duration_minutes' => $stopData['duration_minutes'] ?? 60,
                                'sort_order' => $stopIndex,
                            ]);
                        }
                    }
                }

                return $tour;
            });

            // Update generation record with success
            $this->generation->update([
                'tour_id' => $tour->id,
                'status' => 'completed',
                'completed_at' => now(),
                'ai_response' => $tourData,
                'tokens_used' => $tourData['_meta']['tokens_used'] ?? null,
                'cost' => $tourData['_meta']['cost'] ?? null,
            ]);

            // Send success notification
            Notification::make()
                ->success()
                ->title('Tour Generated Successfully!')
                ->body("Your tour '{$tour->title}' is ready to edit. Check the Tours list to view it.")
                ->sendToDatabase($this->generation->user);

            Log::info('Tour generated successfully', [
                'generation_id' => $this->generation->id,
                'tour_id' => $tour->id,
                'tokens_used' => $tourData['_meta']['tokens_used'] ?? 0,
                'cost' => $tourData['_meta']['cost'] ?? 0,
            ]);

        } catch (\Exception $e) {
            // Update generation record with failure
            $this->generation->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
                'completed_at' => now(),
            ]);

            // Send failure notification
            Notification::make()
                ->danger()
                ->title('Tour Generation Failed')
                ->body('An error occurred while generating your tour. Please try again.')
                ->sendToDatabase($this->generation->user);

            Log::error('Tour generation failed', [
                'generation_id' => $this->generation->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Re-throw to mark job as failed
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        // Update generation record if not already updated
        if ($this->generation->status !== 'failed') {
            $this->generation->update([
                'status' => 'failed',
                'error_message' => $exception->getMessage(),
                'completed_at' => now(),
            ]);
        }

        // Ensure notification was sent
        Notification::make()
            ->danger()
            ->title('Tour Generation Failed')
            ->body('The AI tour generation job failed. Please contact support if the issue persists.')
            ->sendToDatabase($this->generation->user);
    }
}
