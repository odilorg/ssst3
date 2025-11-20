<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTourRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Authorization should be handled by middleware/policies
        // Return true here; actual authorization in controller or policy
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Basic Information
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:tours,slug',
            'short_description' => 'nullable|string|max:500',
            'long_description' => 'required|string',

            // Duration & Pricing
            'duration_days' => 'required|integer|min:1|max:365',
            'duration_text' => 'nullable|string|max:255',
            'price_per_person' => 'required|numeric|min:0|max:999999.99',
            'currency' => 'nullable|string|max:3',

            // Capacity
            'max_guests' => 'nullable|integer|min:1|max:100',
            'min_guests' => 'nullable|integer|min:1|max:100',

            // Media
            'hero_image' => 'nullable|string|max:1000',
            'gallery_images' => 'nullable|array|max:15',
            'gallery_images.*' => 'string|max:1000',

            // JSON Fields - Highlights
            'highlights' => 'nullable|array|max:10',
            'highlights.*' => 'string|max:500',

            // JSON Fields - Included Items
            'included_items' => 'nullable|array|max:20',
            'included_items.*' => 'string|max:500',

            // JSON Fields - Excluded Items
            'excluded_items' => 'nullable|array|max:20',
            'excluded_items.*' => 'string|max:500',

            // JSON Fields - Languages
            'languages' => 'nullable|array|max:10',
            'languages.*' => 'string|max:100',

            // JSON Fields - Requirements
            'requirements' => 'nullable|array|max:10',
            'requirements.*' => 'string|max:500',

            // Relationships
            'city_id' => 'required|exists:cities,id',
            'tour_type' => 'nullable|string|in:private,group,day_trip',

            // Booking Settings
            'min_booking_hours' => 'nullable|integer|min:1|max:720',
            'has_hotel_pickup' => 'nullable|boolean',
            'pickup_radius_km' => 'nullable|numeric|min:0|max:100',

            // Meeting Point
            'meeting_point_address' => 'nullable|string|max:1000',
            'meeting_instructions' => 'nullable|string|max:2000',
            'meeting_lat' => 'nullable|numeric|between:-90,90',
            'meeting_lng' => 'nullable|numeric|between:-180,180',

            // Cancellation
            'cancellation_hours' => 'nullable|integer|min:0|max:168',
            'cancellation_policy' => 'nullable|string|max:2000',

            // Status
            'is_active' => 'nullable|boolean',
        ];
    }

    /**
     * Get custom error messages
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Tour title is required',
            'duration_days.required' => 'Tour duration is required',
            'duration_days.min' => 'Tour duration must be at least 1 day',
            'price_per_person.required' => 'Price per person is required',
            'city_id.required' => 'Please select a city for this tour',
            'city_id.exists' => 'Selected city does not exist',
            'highlights.max' => 'Maximum 10 highlights allowed',
            'included_items.max' => 'Maximum 20 included items allowed',
            'excluded_items.max' => 'Maximum 20 excluded items allowed',
            'gallery_images.max' => 'Maximum 15 gallery images allowed',
        ];
    }

    /**
     * Get custom attribute names for error messages
     *
     * @return array
     */
    public function attributes(): array
    {
        return [
            'duration_days' => 'duration',
            'price_per_person' => 'price',
            'city_id' => 'city',
            'max_guests' => 'maximum guests',
            'min_guests' => 'minimum guests',
        ];
    }
}
