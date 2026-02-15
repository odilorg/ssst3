<?php

namespace App\Http\Controllers\Api\Internal;

use App\Http\Controllers\Controller;
use App\Models\Tour;
use App\Models\TourAIGeneration;
use App\Models\TourTranslation;
use App\Services\TourAIService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class TourAIGenerateController extends Controller
{
    /**
     * Generate a tour using AI (synchronous).
     *
     * Optionally auto-saves the tour if auto_save=true.
     */
    public function generate(Request $request): JsonResponse
    {
        $request->validate([
            'destinations' => 'required|string|max:255',
            'duration_days' => 'required|integer|min:1|max:30',
            'tour_style' => 'required|in:cultural_heritage,adventure_nature,luxury_experience,budget_friendly,family_friendly,photography',
            'special_interests' => 'nullable|string|max:500',
            'additional_notes' => 'nullable|string|max:1000',
            'auto_save' => 'nullable|boolean',
        ]);

        // Rate limit: 5 per hour
        $key = 'internal-ai-generate:' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            return response()->json([
                'ok' => false,
                'errors' => [['field' => 'rate_limit', 'message' => "Rate limit exceeded. Try again in {$seconds} seconds."]],
            ], 429);
        }
        RateLimiter::hit($key, 3600);

        Log::info('AI tour generation requested', [
            'destinations' => $request->input('destinations'),
            'duration_days' => $request->input('duration_days'),
            'tour_style' => $request->input('tour_style'),
            'ip' => $request->ip(),
        ]);

        try {
            $service = app(TourAIService::class);
            $tourData = $service->generateTour($request->only([
                'destinations', 'duration_days', 'tour_style',
                'special_interests', 'additional_notes',
            ]));

            $result = [
                'ok' => true,
                'tour_data' => $tourData,
            ];

            // Auto-save if requested
            if ($request->boolean('auto_save')) {
                $saved = $this->saveTour($tourData, $request->all());
                $result['saved'] = true;
                $result['tour_id'] = $saved['tour_id'];
                $result['slug'] = $saved['slug'];
                $result['url'] = url('/tours/' . $saved['slug']);
            }

            Log::info('AI tour generation completed', [
                'title' => $tourData['title'] ?? 'unknown',
                'tokens' => $tourData['_meta']['tokens_used'] ?? 0,
                'auto_saved' => $request->boolean('auto_save'),
            ]);

            return response()->json($result);

        } catch (\Exception $e) {
            Log::error('AI tour generation failed', ['error' => $e->getMessage()]);
            return response()->json([
                'ok' => false,
                'errors' => [['field' => 'ai', 'message' => $e->getMessage()]],
            ], 500);
        }
    }

    /**
     * Save AI-generated tour data to database.
     */
    protected function saveTour(array $tourData, array $inputParams): array
    {
        return DB::transaction(function () use ($tourData, $inputParams) {
            $slug = Str::slug($tourData['title'] ?? 'ai-generated-tour');

            // Ensure unique slug
            $baseSlug = $slug;
            $counter = 1;
            while (Tour::where('slug', $slug)->exists()) {
                $slug = $baseSlug . '-' . $counter++;
            }

            $tour = new Tour();
            $tour->fill([
                'slug' => $slug,
                'title' => $tourData['title'] ?? 'AI Generated Tour',
                'duration_days' => $tourData['duration_days'] ?? $inputParams['duration_days'],
                'duration_text' => ($tourData['duration_days'] ?? $inputParams['duration_days']) . ' days',
                'price_per_person' => $tourData['estimated_price'] ?? 0,
                'is_active' => false, // Draft — needs review before publishing
                'tour_type' => 'private_only',
            ]);
            $tour->save();

            // Create English translation with AI content
            $translation = new TourTranslation();
            $translation->tour_id = $tour->id;
            $translation->locale = 'en';
            $translation->title = $tourData['title'] ?? $tour->title;
            $translation->slug = $slug;
            $translation->excerpt = $tourData['short_description'] ?? null;
            $translation->content = $tourData['description'] ?? null;

            // Map AI arrays to translation JSON fields
            if (!empty($tourData['highlights'])) {
                $translation->highlights_json = array_map(fn($h) => ['text' => $h], $tourData['highlights']);
            }
            if (!empty($tourData['included'])) {
                $translation->included_json = array_map(fn($i) => ['text' => $i], $tourData['included']);
            }
            if (!empty($tourData['excluded'])) {
                $translation->excluded_json = array_map(fn($e) => ['text' => $e], $tourData['excluded']);
            }
            if (!empty($tourData['days'])) {
                $translation->itinerary_json = array_map(function ($day, $index) {
                    return [
                        'day' => $index + 1,
                        'title' => $day['title'] ?? "Day " . ($index + 1),
                        'description' => $day['description'] ?? '',
                        'activities' => array_map(fn($s) => [
                            'title' => $s['title'] ?? '',
                            'description' => $s['description'] ?? '',
                            'duration_minutes' => $s['duration_minutes'] ?? null,
                        ], $day['stops'] ?? []),
                    ];
                }, $tourData['days'], array_keys($tourData['days']));
            }

            $translation->save();

            // Track in AI generations table
            if (class_exists(TourAIGeneration::class)) {
                TourAIGeneration::create([
                    'tour_id' => $tour->id,
                    'user_id' => 1, // System user for MCP
                    'status' => 'completed',
                    'input_parameters' => $inputParams,
                    'ai_response' => $tourData,
                    'tokens_used' => $tourData['_meta']['tokens_used'] ?? 0,
                    'cost' => $tourData['_meta']['cost'] ?? 0,
                    'completed_at' => now(),
                ]);
            }

            return [
                'tour_id' => $tour->id,
                'slug' => $tour->slug,
            ];
        });
    }
}
