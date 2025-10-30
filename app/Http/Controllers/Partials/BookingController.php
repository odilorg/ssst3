<?php

namespace App\Http\Controllers\Partials;

use App\Http\Controllers\Controller;
use App\Models\Tour;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    /**
     * Show booking form partial
     * Returns: Booking form HTML with tour details
     */
    public function form(string $tourSlug)
    {
        $tour = Tour::where('slug', $tourSlug)
            ->where('is_active', true)
            ->with('activeExtras')
            ->firstOrFail();

        return view('partials.bookings.form', compact('tour'));
    }

    /**
     * Store booking
     * Returns: Confirmation HTML or error HTML
     *
     * Will be implemented in Phase 4
     */
    public function store(Request $request)
    {
        // TODO: Implement in Phase 4
        return response()->json([
            'message' => 'Booking controller - store method - Coming in Phase 4'
        ]);
    }
}
