<?php

namespace App\Http\Controllers;

use App\Models\Tour;
use App\Models\CompanySetting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class TourPdfController extends Controller
{
    public function download(string $slug)
    {
        $tour = Tour::where('slug', $slug)
            ->where('is_active', true)
            ->with(['itineraryItems' => function($query) {
                $query->where('type', 'day')
                    ->orderBy('sort_order')
                    ->with('children');
            }])
            ->firstOrFail();

        $companySettings = CompanySetting::current();

        $pdf = Pdf::loadView('pdf.tour-itinerary', [
            'tour' => $tour,
            'companySettings' => $companySettings,
        ]);

        $pdf->setPaper('A4', 'portrait');

        // Sanitize filename
        $filename = 'itinerary-' . $tour->slug . '.pdf';

        return $pdf->download($filename);
    }

    public function stream(string $slug)
    {
        $tour = Tour::where('slug', $slug)
            ->where('is_active', true)
            ->with(['itineraryItems' => function($query) {
                $query->where('type', 'day')
                    ->orderBy('sort_order')
                    ->with('children');
            }])
            ->firstOrFail();

        $companySettings = CompanySetting::current();

        $pdf = Pdf::loadView('pdf.tour-itinerary', [
            'tour' => $tour,
            'companySettings' => $companySettings,
        ]);

        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream('itinerary-' . $tour->slug . '.pdf');
    }
}
