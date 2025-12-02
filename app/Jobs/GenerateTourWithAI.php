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

    public $timeout = 300;
    public $tries = 1;

    public function __construct(
        public TourAIGeneration $generation
    ) {}

    public function handle(TourAIService $aiService): void
    {
        try {
            $this->generation->update(["status" => "processing"]);
            $tourData = $aiService->generateTour($this->generation->input_parameters);

            $tour = DB::transaction(function () use ($tourData) {
                $tour = Tour::create([
                    "title" => $tourData["title"],
                    "slug" => $tourData["slug"] ?? null,
                    "short_description" => $tourData["short_description"] ?? null,
                    "long_description" => $tourData["description"] ?? null,
                    "duration_days" => $tourData["duration_days"],
                    "duration_text" => ($tourData["duration_days"] ?? 1) . " " . (($tourData["duration_days"] ?? 1) === 1 ? "Day" : "Days"),
                    "tour_type" => $tourData["tour_type"] ?? "hybrid",
                    "city_id" => $tourData["city_id"] ?? null,
                    "is_active" => $tourData["is_active"] ?? false,
                    "price_per_person" => $tourData["group_price"] ?? $tourData["private_price"] ?? null,
                    "currency" => $tourData["currency"] ?? "USD",
                    "min_guests" => $tourData["min_guests"] ?? 1,
                    "max_guests" => $tourData["max_guests"] ?? 15,
                    "highlights" => $tourData["highlights"] ?? [],
                    "included_items" => $tourData["included"] ?? [],
                    "excluded_items" => $tourData["excluded"] ?? [],
                    "requirements" => $tourData["requirements"] ?? [],
                    "languages" => ["en"],
                    "min_booking_hours" => $tourData["min_booking_hours"] ?? 24,
                    "has_hotel_pickup" => $tourData["has_hotel_pickup"] ?? true,
                    "cancellation_hours" => $tourData["cancellation_hours"] ?? 24,
                    "seo_title" => $tourData["seo_title"] ?? null,
                    "seo_description" => $tourData["seo_description"] ?? null,
                    "schema_enabled" => $tourData["schema_enabled"] ?? true,
                ]);

                if (!empty($tourData["category_ids"])) {
                    $tour->categories()->attach($tourData["category_ids"]);
                }

                foreach ($tourData["days"] as $dayIndex => $dayData) {
                    $day = $tour->itineraryItems()->create([
                        "type" => "day",
                        "title" => $dayData["title"],
                        "description" => $dayData["description"] ?? null,
                        "default_start_time" => $dayData["default_start_time"] ?? "09:00",
                        "sort_order" => $dayIndex,
                    ]);

                    if (isset($dayData["stops"]) && is_array($dayData["stops"])) {
                        foreach ($dayData["stops"] as $stopIndex => $stopData) {
                            $day->children()->create([
                                "tour_id" => $tour->id,
                                "type" => "stop",
                                "title" => $stopData["title"],
                                "description" => $stopData["description"] ?? null,
                                "default_start_time" => $stopData["default_start_time"] ?? null,
                                "duration_minutes" => $stopData["duration_minutes"] ?? 60,
                                "sort_order" => $stopIndex,
                            ]);
                        }
                    }
                }

                return $tour;
            });

            $this->generation->update([
                "tour_id" => $tour->id,
                "status" => "completed",
                "completed_at" => now(),
                "ai_response" => $tourData,
                "tokens_used" => $tourData["_meta"]["tokens_used"] ?? null,
                "cost" => $tourData["_meta"]["cost"] ?? null,
            ]);

            Notification::make()
                ->success()
                ->title("Tour Generated Successfully!")
                ->body("Your tour " . $tour->title . " is ready to edit.")
                ->sendToDatabase($this->generation->user);

            Log::info("Tour generated successfully", [
                "generation_id" => $this->generation->id,
                "tour_id" => $tour->id,
                "tokens_used" => $tourData["_meta"]["tokens_used"] ?? 0,
                "cost" => $tourData["_meta"]["cost"] ?? 0,
            ]);

        } catch (\Exception $e) {
            $this->generation->update([
                "status" => "failed",
                "error_message" => $e->getMessage(),
                "completed_at" => now(),
            ]);

            Notification::make()
                ->danger()
                ->title("Tour Generation Failed")
                ->body("An error occurred while generating your tour. Please try again.")
                ->sendToDatabase($this->generation->user);

            Log::error("Tour generation failed", [
                "generation_id" => $this->generation->id,
                "error" => $e->getMessage(),
                "trace" => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        if ($this->generation->status !== "failed") {
            $this->generation->update([
                "status" => "failed",
                "error_message" => $exception->getMessage(),
                "completed_at" => now(),
            ]);
        }

        Notification::make()
            ->danger()
            ->title("Tour Generation Failed")
            ->body("The AI tour generation job failed. Please contact support if the issue persists.")
            ->sendToDatabase($this->generation->user);
    }
}
