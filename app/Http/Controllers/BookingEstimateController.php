<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Services\BookingEstimateService;

class BookingEstimateController extends Controller
{
    public function show(Booking $booking, BookingEstimateService $service)
    {
        abort_unless(
            auth()->check() && auth()->user()->can('viewEstimate', $booking),
            403
        );

        $data = $service->buildEstimateData($booking);

        return view('booking-print-estimate', array_merge(
            ['record' => $booking],
            $data
        ));
    }
}
