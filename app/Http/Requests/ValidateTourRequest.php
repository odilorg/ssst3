<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ValidateTourRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $fallbackMode = $this->input('fallback_mode', 'strict');
        $isStrictMode = $fallbackMode === 'strict';

        $rules = [
            'schema_version' => 'nullable|string|max:10',
            'fallback_mode' => 'nullable|string|in:strict,allowed',

            'tour.slug' => 'required|string|max:255|regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
            'tour.duration_days' => 'nullable|integer|min:1|max:365',
            'tour.duration_text' => 'nullable|string|max:100',
            'tour.tour_type' => 'nullable|string|in:private_only,group_only,hybrid',
            'tour.is_active' => 'nullable|boolean',
            'tour.city_id' => 'nullable|integer|exists:cities,id',
            'tour.supports_private' => 'nullable|boolean',
            'tour.supports_group' => 'nullable|boolean',
            'tour.private_base_price' => 'nullable|numeric|min:0|max:999999.99',
            'tour.price_per_person' => 'nullable|numeric|min:0|max:999999.99',
            'tour.currency' => 'nullable|string|max:3',
            'tour.show_price' => 'nullable|boolean',
            'tour.hero_image' => 'nullable|string|max:500',
            'tour.minimum_advance_days' => 'nullable|integer|min:1|max:365',
            'tour.min_booking_hours' => 'nullable|integer|min:1|max:720',
            'tour.cancellation_hours' => 'nullable|integer|min:0|max:720',

            'translations' => 'required|array|min:1',
            'translations.*.locale' => 'required|string|in:en,ru,fr,de,es,it,ja,zh',

            'translations.en' => 'required|array',
            'translations.en.title' => 'required|string|max:255',
            'translations.en.locale' => 'required|string|in:en',

            'translations.*.title' => 'nullable|string|max:255',
            'translations.*.slug' => 'nullable|string|max:255|regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
            'translations.*.excerpt' => 'nullable|string|max:1000',
            'translations.*.content' => 'nullable|string|max:50000',
            'translations.*.seo_title' => 'nullable|string|max:70',
            'translations.*.seo_description' => 'nullable|string|max:160',

            // Itinerary is ALWAYS required (hard fail in both modes)
            'translations.*.itinerary_json' => 'required|array|min:1',
            'translations.*.itinerary_json.*.day' => 'required|integer|min:1',
            'translations.*.itinerary_json.*.title' => 'required|string|max:255',
            'translations.*.itinerary_json.*.description' => 'nullable|string|max:5000',
            'translations.*.itinerary_json.*.activities' => 'nullable|array',
            'translations.*.itinerary_json.*.activities.*.time' => 'nullable|string|max:20',
            'translations.*.itinerary_json.*.activities.*.title' => 'required_with:translations.*.itinerary_json.*.activities|string|max:255',
            'translations.*.itinerary_json.*.activities.*.description' => 'nullable|string|max:2000',

            'translations.*.cancellation_policy' => 'nullable|string|max:5000',
            'translations.*.meeting_instructions' => 'nullable|string|max:2000',
        ];

        // In strict mode: all JSON fields are required
        // In allowed mode: only itinerary_json is required, others are optional (will be templated)
        if ($isStrictMode) {
            $rules['translations.*.highlights_json'] = 'required|array|min:1';
            $rules['translations.*.included_json'] = 'required|array|min:1';
            $rules['translations.*.excluded_json'] = 'required|array|min:1';
            $rules['translations.*.faq_json'] = 'required|array|min:1';
            $rules['translations.*.requirements_json'] = 'required|array|min:1';
        } else {
            // allowed mode - these fields are optional
            $rules['translations.*.highlights_json'] = 'nullable|array';
            $rules['translations.*.included_json'] = 'nullable|array';
            $rules['translations.*.excluded_json'] = 'nullable|array';
            $rules['translations.*.faq_json'] = 'nullable|array';
            $rules['translations.*.requirements_json'] = 'nullable|array';
        }

        // Validation for JSON field contents (when present)
        $rules['translations.*.highlights_json.*.text'] = 'required_with:translations.*.highlights_json|string|max:500';
        $rules['translations.*.included_json.*.text'] = 'required_with:translations.*.included_json|string|max:500';
        $rules['translations.*.excluded_json.*.text'] = 'required_with:translations.*.excluded_json|string|max:500';
        $rules['translations.*.faq_json.*.question'] = 'required_with:translations.*.faq_json|string|max:500';
        $rules['translations.*.faq_json.*.answer'] = 'required_with:translations.*.faq_json|string|max:2000';
        $rules['translations.*.requirements_json.*.icon'] = 'nullable|string|max:50';
        $rules['translations.*.requirements_json.*.title'] = 'required_with:translations.*.requirements_json|string|max:255';
        $rules['translations.*.requirements_json.*.text'] = 'required_with:translations.*.requirements_json|string|max:1000';

        return $rules;
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v) {
            $this->validateItineraryDaysSequential($v);
        });
    }

    protected function validateItineraryDaysSequential(Validator $validator): void
    {
        $translations = $this->input('translations', []);

        foreach ($translations as $locale => $translation) {
            if (!isset($translation['itinerary_json']) || !is_array($translation['itinerary_json'])) {
                continue;
            }

            $days = array_column($translation['itinerary_json'], 'day');
            sort($days);

            $expected = range(1, count($days));

            if ($days !== $expected) {
                $validator->errors()->add(
                    "translations.{$locale}.itinerary_json",
                    "Itinerary days must be sequential starting at 1. Got: [" . implode(', ', $days) . "], expected: [" . implode(', ', $expected) . "]"
                );
            }
        }
    }

    public function messages(): array
    {
        return [
            'tour.slug.required' => 'Tour slug is required',
            'tour.slug.regex' => 'Slug must be lowercase with hyphens only',
            'fallback_mode.in' => 'fallback_mode must be either "strict" or "allowed"',
            'translations.required' => 'At least one translation is required',
            'translations.en.required' => 'English translation is required',
            'translations.en.title.required' => 'English title is required',
            'translations.*.itinerary_json.required' => 'Itinerary is required for each translation',
            'translations.*.itinerary_json.*.day.required' => 'Each itinerary item must have a day number',
            'translations.*.itinerary_json.*.title.required' => 'Each itinerary day must have a title',
            'translations.*.highlights_json.required' => 'Highlights are required in strict mode. Use fallback_mode: "allowed" for templating.',
            'translations.*.included_json.required' => 'Included items are required in strict mode. Use fallback_mode: "allowed" for templating.',
            'translations.*.excluded_json.required' => 'Excluded items are required in strict mode. Use fallback_mode: "allowed" for templating.',
            'translations.*.faq_json.required' => 'FAQ is required in strict mode. Use fallback_mode: "allowed" for templating.',
            'translations.*.requirements_json.required' => 'Requirements are required in strict mode. Use fallback_mode: "allowed" for templating.',
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        $errors = collect($validator->errors()->toArray())
            ->map(function ($messages, $field) {
                return [
                    'field' => $field,
                    'message' => $messages[0],
                ];
            })
            ->values()
            ->toArray();

        throw new HttpResponseException(
            response()->json([
                'ok' => false,
                'errors' => $errors,
            ], 422)
        );
    }
}
