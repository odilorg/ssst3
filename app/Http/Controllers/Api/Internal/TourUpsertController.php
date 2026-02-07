<?php

namespace App\Http\Controllers\Api\Internal;

use App\Http\Controllers\Controller;
use App\Http\Requests\ValidateTourRequest;
use App\Models\Tour;
use App\Models\TourTranslation;
use App\Services\TourTemplates;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TourUpsertController extends Controller
{
    /**
     * Upsert a tour by slug (create or update).
     *
     * This endpoint is idempotent - calling twice with same data
     * will result in the same record being updated, not duplicated.
     *
     * Supports fallback_mode:
     * - "strict" (default): All JSON fields required
     * - "allowed": Missing fields filled from templates with warnings
     */
    public function upsert(ValidateTourRequest $request): JsonResponse
    {
        $warnings = [];
        $tourData = $request->input('tour', []);
        $translationsData = $request->input('translations', []);
        $fallbackMode = $request->input('fallback_mode', 'strict');
        $slug = $tourData['slug'];

        // Log the upsert request
        Log::info('Tour upsert requested', [
            'slug' => $slug,
            'fallback_mode' => $fallbackMode,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        try {
            $result = DB::transaction(function () use ($tourData, $translationsData, $slug, $fallbackMode, &$warnings) {
                // Find existing tour or create new
                $tour = Tour::where('slug', $slug)->first();
                $isNew = !$tour;

                if ($isNew) {
                    $tour = new Tour();
                }

                // Prepare tour fields to update
                $tourFields = $this->prepareTourFields($tourData, $isNew);
                $tour->fill($tourFields);
                $tour->save();

                // Upsert translations with template fallbacks
                foreach ($translationsData as $locale => $translationData) {
                    $this->upsertTranslation($tour, $locale, $translationData, $fallbackMode, $warnings);
                }

                return [
                    'tour' => $tour,
                    'is_new' => $isNew,
                ];
            });

            $tour = $result['tour'];
            $isNew = $result['is_new'];

            Log::info('Tour upsert completed', [
                'slug' => $slug,
                'tour_id' => $tour->id,
                'action' => $isNew ? 'created' : 'updated',
                'fallback_mode' => $fallbackMode,
                'warnings_count' => count($warnings),
            ]);

            return response()->json([
                'ok' => true,
                'tour_id' => $tour->id,
                'slug' => $tour->slug,
                'url' => url('/tours/' . $tour->slug),
                'action' => $isNew ? 'created' : 'updated',
                'fallback_mode' => $fallbackMode,
                'warnings' => $warnings,
            ], $isNew ? 201 : 200);

        } catch (\Exception $e) {
            Log::error('Tour upsert failed', [
                'slug' => $slug,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'ok' => false,
                'errors' => [
                    ['field' => 'server', 'message' => 'Failed to upsert tour: ' . $e->getMessage()]
                ],
            ], 500);
        }
    }

    /**
     * Prepare tour fields from request data.
     *
     * @param array $data Request data
     * @param bool $isNew Whether this is a new tour (apply defaults)
     */
    protected function prepareTourFields(array $data, bool $isNew = false): array
    {
        $fields = [];

        // Map request fields to model fields
        $mapping = [
            'slug' => 'slug',
            'duration_days' => 'duration_days',
            'duration_text' => 'duration_text',
            'tour_type' => 'tour_type',
            'is_active' => 'is_active',
            'city_id' => 'city_id',
            'supports_private' => 'supports_private',
            'supports_group' => 'supports_group',
            'price_per_person' => 'price_per_person',
            'private_base_price' => 'private_base_price',
            'private_min_guests' => 'private_min_guests',
            'private_max_guests' => 'private_max_guests',
            'group_tour_price_per_person' => 'group_tour_price_per_person',
            'group_price_per_person' => 'group_price_per_person',
            'private_price_per_person' => 'private_price_per_person',
            'private_minimum_charge' => 'private_minimum_charge',
            'currency' => 'currency',
            'show_price' => 'show_price',
            'hero_image' => 'hero_image',
            'minimum_advance_days' => 'minimum_advance_days',
            'min_booking_hours' => 'min_booking_hours',
            'cancellation_hours' => 'cancellation_hours',
            'meeting_point_address' => 'meeting_point_address',
            'meeting_lat' => 'meeting_lat',
            'meeting_lng' => 'meeting_lng',
            'has_hotel_pickup' => 'has_hotel_pickup',
            'pickup_radius_km' => 'pickup_radius_km',
            'max_guests' => 'max_guests',
            'min_guests' => 'min_guests',
            'minimum_participants_to_operate' => 'minimum_participants_to_operate',
            'rating' => 'rating',
            'review_count' => 'review_count',
            'booking_window_hours' => 'booking_window_hours',
            'balance_due_days' => 'balance_due_days',
            'deposit_required' => 'deposit_required',
            'deposit_percentage' => 'deposit_percentage',
            'deposit_min_amount' => 'deposit_min_amount',
            'schema_enabled' => 'schema_enabled',
            'listing_category' => 'listing_category',
        ];

        foreach ($mapping as $requestKey => $modelKey) {
            if (array_key_exists($requestKey, $data)) {
                $fields[$modelKey] = $data[$requestKey];
            }
        }

        // Apply defaults for required fields on new tours
        if ($isNew) {
            $defaults = [
                'price_per_person' => 0.00,
                'duration_days' => $data['duration_days'] ?? 1,
                'tour_type' => $data['tour_type'] ?? 'group_only',
                'is_active' => $data['is_active'] ?? true,
            ];

            foreach ($defaults as $key => $defaultValue) {
                if (!isset($fields[$key])) {
                    $fields[$key] = $defaultValue;
                }
            }
        }

        return $fields;
    }

    /**
     * Upsert a translation for a tour with template fallbacks.
     *
     * @param Tour $tour The tour model
     * @param string $locale Locale code (en, ru, etc.)
     * @param array $data Translation data from request
     * @param string $fallbackMode "strict" or "allowed"
     * @param array $warnings Array to append warnings to
     */
    protected function upsertTranslation(Tour $tour, string $locale, array $data, string $fallbackMode, array &$warnings): TourTranslation
    {
        // Find existing translation or create new
        $translation = TourTranslation::where('tour_id', $tour->id)
            ->where('locale', $locale)
            ->first();

        if (!$translation) {
            $translation = new TourTranslation();
            $translation->tour_id = $tour->id;
            $translation->locale = $locale;
        }

        // Map translation fields
        $translationFields = [
            'title' => $data['title'] ?? null,
            'slug' => $data['slug'] ?? null,
            'excerpt' => $data['excerpt'] ?? null,
            'content' => $data['content'] ?? null,
            'seo_title' => $data['seo_title'] ?? null,
            'seo_description' => $data['seo_description'] ?? null,
            'itinerary_json' => $data['itinerary_json'] ?? null,
            'cancellation_policy' => $data['cancellation_policy'] ?? null,
            'meeting_instructions' => $data['meeting_instructions'] ?? null,
        ];

        // Handle JSON fields with template fallbacks in "allowed" mode
        $jsonFieldsWithTemplates = [
            'highlights_json' => 'highlights',
            'included_json' => 'included',
            'excluded_json' => 'excluded',
            'faq_json' => 'faq',
            'requirements_json' => 'requirements',
        ];

        foreach ($jsonFieldsWithTemplates as $fieldName => $templateName) {
            if (isset($data[$fieldName]) && !empty($data[$fieldName])) {
                // Field was provided - use it
                $translationFields[$fieldName] = $data[$fieldName];
            } elseif ($fallbackMode === 'allowed') {
                // Field not provided and we're in allowed mode - use template
                $template = TourTemplates::get($templateName, 'v1');
                if ($template) {
                    $translationFields[$fieldName] = $template;
                    $warnings[] = "Translation '{$locale}': {$fieldName} was filled from StandardTemplate (v1)";
                }
            } else {
                // Strict mode - field should have been required by validation
                // But if we got here somehow, set to null
                $translationFields[$fieldName] = $data[$fieldName] ?? null;
            }
        }

        // Filter out null values for update (don't overwrite with null unless explicitly set)
        $filteredFields = array_filter($translationFields, function ($value) {
            return $value !== null;
        });

        // For new translations, set all fields
        if (!$translation->exists) {
            $translation->fill($translationFields);
        } else {
            // For existing translations, only update non-null fields
            $translation->fill($filteredFields);
        }

        $translation->save();

        // Add warning if translation slug was auto-generated or empty
        if (empty($translation->slug) && !empty($translation->title)) {
            $warnings[] = "Translation for locale '{$locale}' has no slug set";
        }

        return $translation;
    }
}
