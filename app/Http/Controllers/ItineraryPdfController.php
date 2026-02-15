<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Services\ItineraryPdfService;

class ItineraryPdfController extends Controller
{
    public function download(string $token, ItineraryPdfService $pdfService)
    {
        $booking = Booking::where('passenger_details_url_token', $token)
            ->with(['tour', 'customer', 'tripDetail'])
            ->firstOrFail();

        $pdf = $pdfService->generate($booking);
        $filename = $pdfService->filename($booking);

        return $pdf->download($filename);
    }
}
