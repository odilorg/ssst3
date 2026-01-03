<?php

namespace App\Http\Controllers;

use App\Models\Tour;
use App\Models\TourDeparture;
use Illuminate\Http\Request;
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
        ]);

        if ($validator->fails()) {
            return response('<div style="color: red; padding: 10px;">Validation error: ' . $validator->errors()->first() . '</div>', 422);
        }

        $tour = Tour::findOrFail($request->tour_id);
        $type = $request->type;
        $guestsCount = (int) $request->guests_count;
        $groupDepartureId = $request->group_departure_id;

        if ($type === 'private') {
            // PRIVATE TOUR - return private tour form partial
            if (!$tour->supportsPrivate()) {
                return response('<div style="color: red; padding: 10px;">This tour does not support private bookings</div>', 422);
            }

            // Validate guest count within allowed range
            $guestsCount = max($tour->private_min_guests, min($guestsCount, $tour->private_max_guests));

            // Calculate pricing
            $priceData = null;
            if ($tour->private_base_price) {
                $priceData = [
                    'success' => true,
                    'price_per_person' => (float) $tour->private_base_price,
                    'total_price' => (float) $tour->private_base_price * $guestsCount,
                ];
            }

            return view('partials.booking.private-tour-form', [
                'tour' => $tour,
                'guestsCount' => $guestsCount,
                'priceData' => $priceData,
            ]);

        } elseif ($type === 'group') {
            // GROUP TOUR - return group tour form partial
            if (!$tour->supportsGroup()) {
                return response('<div style="color: red; padding: 10px;">This tour does not support group bookings</div>', 422);
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
            ]);
        }

        return response('<div style="color: red; padding: 10px;">Invalid tour type</div>', 422);
    }
}
