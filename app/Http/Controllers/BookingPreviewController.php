<?php

namespace App\Http\Controllers;

use App\Models\Tour;
use App\Models\TourDeparture;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class BookingPreviewController extends Controller
{
    /**
     * Calculate booking preview with server-side pricing logic
     * Returns HTML partial for HTMX swap
     *
     * POST /bookings/preview
     *
     * Payload:
     * - tour_id (required)
     * - type (required: 'private' | 'group')
     * - guests_count (required)
     * - group_departure_id (required if type=group)
     */
    public function preview(Request $request)
    {
        // Validate request
        $validator = Validator::make($request->all(), [
            'tour_id' => 'required|exists:tours,id',
            'type' => 'required|in:private,group',
            'guests_count' => 'required|integer|min:1',
            'group_departure_id' => 'nullable|exists:tour_departures,id',
            'extras' => 'nullable|array',
            'extras.*' => 'integer|exists:tour_extras,id',
        ]);

        if ($validator->fails()) {
            // SECURITY: Log detailed errors internally, return generic message to user
            Log::warning('Booking preview validation failed', [
                'errors' => $validator->errors()->toArray(),
                'tour_id' => $request->tour_id,
                'type' => $request->type,
                'ip' => $request->ip(),
            ]);
            return response('<div class="text-red-600 p-3 text-sm">Please check your selection and try again.</div>', 422);
        }

        try {
            $tour = Tour::findOrFail($request->tour_id);
            $tour->load('activeExtras');
            $type = $request->type;
            $guestsCount = (int) $request->guests_count;
            $groupDepartureId = $request->group_departure_id;

            if ($type === 'private') {
                // PRIVATE TOUR - return private tour form partial
                if (!$tour->supportsPrivate()) {
                    // SECURITY: Generic error message
                    Log::info('Private booking not supported', ['tour_id' => $tour->id]);
                    return response('<div class="text-red-600 p-3 text-sm">This tour type is not available. Please select a different option.</div>', 422);
                }

                // Validate guest count within allowed range
                $guestsCount = max($tour->private_min_guests, min($guestsCount, $tour->private_max_guests));

                // Calculate pricing
                $priceData = null;

                // Try to get pricing tier first (supports tiered pricing)
                $pricingTier = $tour->getPricingTierForGuests($guestsCount);

                if ($pricingTier) {
                    // Use tiered pricing
                    $priceData = [
                        'success' => true,
                        'price_per_person' => (float) $pricingTier->price_per_person,
                        'total_price' => (float) $pricingTier->price_total,
                    ];
                } elseif ($tour->private_base_price) {
                    // Fallback to simple private_base_price
                    $priceData = [
                        'success' => true,
                        'price_per_person' => (float) $tour->private_base_price,
                        'total_price' => (float) $tour->private_base_price * $guestsCount,
                    ];
                } elseif ($tour->price_per_person) {
                    // Final fallback to group price if private pricing not configured
                    // SECURITY: Log warning about data inconsistency
                    Log::warning('Tour pricing fallback triggered - private tour using group price', [
                        'tour_id' => $tour->id,
                        'tour_slug' => $tour->slug,
                        'tour_type' => $tour->tour_type,
                        'private_base_price' => $tour->private_base_price,
                        'price_per_person' => $tour->price_per_person,
                        'guests_count' => $guestsCount,
                    ]);

                    $priceData = [
                        'success' => true,
                        'price_per_person' => (float) $tour->price_per_person,
                        'total_price' => (float) $tour->price_per_person * $guestsCount,
                    ];
                }

                return view('partials.booking.private-tour-form', [
                    'tour' => $tour,
                    'guestsCount' => $guestsCount,
                    'priceData' => $priceData,
                    'selectedExtras' => $request->input('extras', []),
                ]);

            } elseif ($type === 'group') {
                // GROUP TOUR - return group tour form partial
                if (!$tour->supportsGroup()) {
                    // SECURITY: Generic error message
                    Log::info('Group booking not supported', ['tour_id' => $tour->id]);
                    return response('<div class="text-red-600 p-3 text-sm">This tour type is not available. Please select a different option.</div>', 422);
                }

                // Get available departures
                $departures = $tour->getAvailableGroupDepartures();

                // Calculate pricing if departure is selected
                $priceData = null;
                if ($groupDepartureId) {
                    $departure = TourDeparture::find($groupDepartureId);
                    if ($departure && $departure->tour_id === $tour->id) {
                        // Validate guest count against available seats
                        $seatsLeft = $departure->spots_remaining;
                        $guestsCount = min($guestsCount, $seatsLeft);

                        $priceData = [
                            'success' => true,
                            'price_per_person' => (float) $departure->price_per_person,
                            'total_price' => (float) $departure->price_per_person * $guestsCount,
                            'seats_left' => $seatsLeft,
                        ];
                    }
                }

                return view('partials.booking.group-tour-form', [
                    'tour' => $tour,
                    'departures' => $departures,
                    'selectedDepartureId' => $groupDepartureId,
                    'guestsCount' => $guestsCount,
                    'priceData' => $priceData,
                    'selectedExtras' => $request->input('extras', []),
                ]);
            }

            // SECURITY: Generic error for invalid type
            Log::warning('Invalid tour type in preview', [
                'type' => $type,
                'tour_id' => $request->tour_id,
                'ip' => $request->ip(),
            ]);
            return response('<div class="text-red-600 p-3 text-sm">Invalid selection. Please try again.</div>', 422);

        } catch (\Exception $e) {
            // SECURITY: Log detailed error internally, return generic message
            Log::error('Booking preview error', [
                'error' => $e->getMessage(),
                'tour_id' => $request->tour_id,
                'ip' => $request->ip(),
            ]);
            return response('<div class="text-red-600 p-3 text-sm">Something went wrong. Please try again.</div>', 500);
        }
    }
}
