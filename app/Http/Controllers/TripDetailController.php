<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\TripDetail;
use Illuminate\Http\Request;

class TripDetailController extends Controller
{
    /**
     * Show the trip details form (token-based access, no login required)
     */
    public function show(string $token)
    {
        $booking = Booking::where('passenger_details_url_token', $token)
            ->with(['tour', 'customer'])
            ->firstOrFail();

        // Create trip detail record if it doesn't exist
        $tripDetail = $booking->tripDetail ?? $booking->tripDetail()->create([]);

        $isMini = !$booking->needsFullTripDetails();

        return view('pages.trip-details', compact('booking', 'tripDetail', 'isMini', 'token'));
    }

    /**
     * Store/update trip details
     */
    public function store(string $token, Request $request)
    {
        $booking = Booking::where('passenger_details_url_token', $token)
            ->with(['tour'])
            ->firstOrFail();

        $rules = [
            'hotel_name' => 'nullable|string|max:255',
            'hotel_address' => 'nullable|string|max:500',
            'whatsapp_number' => 'nullable|string|max:50',
            'language_preference' => 'nullable|string|max:50',
            'referral_source' => 'nullable|string|max:100',
            'additional_notes' => 'nullable|string|max:2000',
        ];

        // Add flight fields for long tours
        if ($booking->needsFullTripDetails()) {
            $rules = array_merge($rules, [
                'arrival_date' => 'nullable|date',
                'arrival_flight' => 'nullable|string|max:50',
                'arrival_time' => 'nullable|string|max:20',
                'departure_date' => 'nullable|date',
                'departure_flight' => 'nullable|string|max:50',
                'departure_time' => 'nullable|string|max:20',
            ]);
        }

        $validated = $request->validate($rules);

        $tripDetail = $booking->tripDetail ?? $booking->tripDetail()->create([]);
        $tripDetail->update($validated);
        $tripDetail->update(['completed_at' => now()]);

        return redirect()
            ->route('trip-details.show', ['token' => $token])
            ->with('success', true);
    }
}
