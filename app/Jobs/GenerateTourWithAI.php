<?php

namespace App\Jobs;

use App\Models\Tour;
use App\Models\TourAIGeneration;
use App\Services\TourAIService;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

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
                // Create tour with all required fields for wizard compatibility
                $tour = Tour::create([
                    'title' => $tourData['title'],
                    'slug' => Str::slug($tourData['title']) . '-' . time(),
                    'duration_days' => $tourData['duration_days'],
                    'duration_text' => ($tourData['duration_days'] == 1)
                        ? '1 day'
                        : $tourData['duration_days'] . ' days / ' . ($tourData['duration_days'] - 1) . ' nights',
                    'long_description' => $tourData['description'] ?? null,
                    'short_description' => $tourData['short_description'] ?? Str::limit(strip_tags($tourData['description'] ?? ''), 200),

                    // Private/Group Tour Support (NEW - added 2026-02-08)
                    // Let Tour model's saving hook set tour_type based on these flags
                    'supports_private' => true,  // Default to supporting both
                    'supports_group' => true,

                    // Private Tour Pricing (NEW - added 2026-02-08)
                    'private_base_price' => $tourData['estimated_price'] ?? 100,
                    'private_min_guests' => 1,
                    'private_max_guests' => 15,
                    'private_price_per_person' => $tourData['estimated_price'] ?? 100,
                    'private_minimum_charge' => ($tourData['estimated_price'] ?? 100) * 2, // 2 guests minimum charge

                    // Group Tour Pricing (NEW - added 2026-02-08)
                    'group_tour_price_per_person' => $tourData['estimated_price'] ?? 100,
                    'group_price_per_person' => $tourData['estimated_price'] ?? 100,

                    // Legacy pricing (kept for compatibility)
                    'price_per_person' => $tourData['estimated_price'] ?? 100,
                    'show_price' => false, // Hide price until user sets it
                    'currency' => 'USD',
                    'min_guests' => 1,
                    'max_guests' => 15,

                    // Deposit Settings (NEW - added 2026-02-08)
                    'deposit_required' => false,
                    'deposit_percentage' => null,
                    'deposit_min_amount' => null,
                    'balance_due_days' => null,

                    // Arrays from AI
                    'highlights' => $tourData['highlights'] ?? [],
                    'included_items' => $tourData['included'] ?? [],
                    'excluded_items' => $tourData['excluded'] ?? [],
                    'languages' => $tourData['languages'] ?? ['English'],

                    // Booking Window (NEW - added 2026-02-08)
                    'minimum_advance_days' => 3,  // Default 3 days advance booking
                    'min_booking_hours' => 24,
                    'cancellation_hours' => 24,
                    'booking_window_hours' => null,

                    // Meeting Points (NEW - added 2026-02-08)
                    'meeting_point_address' => null,
                    'meeting_lat' => null,
                    'meeting_lng' => null,
                    'has_hotel_pickup' => true,
                    'pickup_radius_km' => 5,

                    // Capacity (NEW - added 2026-02-08)
                    'minimum_participants_to_operate' => null,

                    // Status
                    'is_active' => false, // Draft mode
                    'schema_enabled' => true,
                ]);

                // Create days and stops
                if (isset($tourData['days']) && is_array($tourData['days'])) {
                    foreach ($tourData['days'] as $dayIndex => $dayData) {
                        $day = $tour->itineraryItems()->create([
                            'type' => 'day',
                            'title' => $dayData['title'] ?? 'Day ' . ($dayIndex + 1),
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
                                    'title' => $stopData['title'] ?? 'Stop ' . ($stopIndex + 1),
                                    'description' => $stopData['description'] ?? null,
                                    'default_start_time' => $stopData['default_start_time'] ?? null,
                                    'duration_minutes' => $stopData['duration_minutes'] ?? 60,
                                    'sort_order' => $stopIndex,
                                ]);
                            }
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

            // Send success notification with edit link
            Notification::make()
                ->success()
                ->title('Tour Generated Successfully!')
                ->body("Your tour {$tour->title} is ready. Click to edit and complete the details.")
                ->actions([
                    Action::make('edit')
                        ->button()
                        ->url(route('filament.admin.resources.tours.tours.edit', ['record' => $tour->id]))
                ])
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
