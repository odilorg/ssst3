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
            'group_departure_id' => 'required_if:type,group|nullable|exists:tour_departures,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $tour = Tour::findOrFail($request->tour_id);
        $type = $request->type;
        $guestsCount = (int) $request->guests_count;
        $groupDepartureId = $request->group_departure_id;

        // Initialize response data
        $data = [
            'success' => true,
            'tour_id' => $tour->id,
            'tour_title' => $tour->title,
            'type' => $type,
            'guests_count' => $guestsCount,
            'price_per_person' => null,
            'total_price' => null,
            'seats_left' => null,
            'currency' => $tour->currency ?? 'USD',
            'errors' => [],
        ];

        if ($type === 'private') {
            // PRIVATE TOUR PRICING LOGIC
            if (!$tour->supportsPrivate()) {
                return response()->json([
                    'success' => false,
                    'errors' => ['type' => 'This tour does not support private bookings'],
                ], 422);
            }

            // Validate guest count within allowed range
            if ($guestsCount < $tour->private_min_guests) {
                $data['errors']['guests_count'] = "Minimum {$tour->private_min_guests} guests required for private tours";
            }

            if ($guestsCount > $tour->private_max_guests) {
                $data['errors']['guests_count'] = "Maximum {$tour->private_max_guests} guests allowed for private tours";
            }

            // Check if private base price is set
            if (!$tour->private_base_price) {
                return response()->json([
                    'success' => false,
                    'errors' => ['tour' => 'Private tour pricing not configured'],
                ], 422);
            }

            // Calculate pricing
            if (empty($data['errors'])) {
                $data['price_per_person'] = (float) $tour->private_base_price;
                $data['total_price'] = $data['price_per_person'] * $guestsCount;
            } else {
                $data['success'] = false;
            }

        } elseif ($type === 'group') {
            // GROUP TOUR PRICING LOGIC
            if (!$tour->supportsGroup()) {
                return response()->json([
                    'success' => false,
                    'errors' => ['type' => 'This tour does not support group bookings'],
                ], 422);
            }

            if (!$groupDepartureId) {
                return response()->json([
                    'success' => false,
                    'errors' => ['group_departure_id' => 'Group departure must be selected'],
                ], 422);
            }

            // Get the group departure
            $departure = TourDeparture::findOrFail($groupDepartureId);

            // Verify departure belongs to this tour
            if ($departure->tour_id !== $tour->id) {
                return response()->json([
                    'success' => false,
                    'errors' => ['group_departure_id' => 'Invalid departure for this tour'],
                ], 422);
            }

            // Verify departure is available
            if (!$departure->is_booking_open) {
                $data['errors']['group_departure_id'] = 'This departure is no longer available for booking';
            }

            // Check if enough seats available
            $seatsLeft = $departure->spots_remaining;
            if ($guestsCount > $seatsLeft) {
                $data['errors']['guests_count'] = "Only {$seatsLeft} seats remaining";
            }

            // Calculate pricing
            if (empty($data['errors'])) {
                $data['price_per_person'] = (float) $departure->price_per_person;
                $data['total_price'] = $data['price_per_person'] * $guestsCount;
                $data['seats_left'] = $seatsLeft;
                $data['departure'] = [
                    'id' => $departure->id,
                    'start_date' => $departure->start_date->format('M d, Y'),
                    'date_range' => $departure->date_range,
                    'max_pax' => $departure->max_pax,
                    'booked_pax' => $departure->booked_pax,
                    'spots_remaining' => $seatsLeft,
                ];
            } else {
                $data['success'] = false;
            }
        }

        return response()->json($data);
    }
}
