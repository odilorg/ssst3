<?php

namespace App\Services;

use App\Models\Booking;
use Barryvdh\DomPDF\Facade\Pdf;

class ItineraryPdfService
{
    public function generate(Booking $booking): \Barryvdh\DomPDF\PDF
    {
        $booking->loadMissing(['tour', 'customer', 'tripDetail']);
        $tour = $booking->tour;

        // Get itinerary from translation first, fallback to tour columns
        $translation = $tour->translations()->where('locale', 'en')->first();

        $itinerary = $this->resolveJsonField($translation?->itinerary_json);
        $highlights = $this->resolveJsonField($translation?->highlights_json) ?: $this->resolveJsonField($tour->highlights);
        $included = $this->resolveJsonField($translation?->included_json) ?: $this->resolveJsonField($tour->included_items);
        $excluded = $this->resolveJsonField($translation?->excluded_json) ?: $this->resolveJsonField($tour->excluded_items);

        return Pdf::loadView('pdf.itinerary', [
            'booking' => $booking,
            'tour' => $tour,
            'tripDetail' => $booking->tripDetail,
            'itinerary' => $itinerary,
            'highlights' => $highlights,
            'included' => $included,
            'excluded' => $excluded,
        ])->setPaper('a4');
    }

    public function generateContent(Booking $booking): string
    {
        return $this->generate($booking)->output();
    }

    public function filename(Booking $booking): string
    {
        $slug = str($booking->tour->title)->slug()->limit(40, '');
        return "itinerary-{$booking->reference}-{$slug}.pdf";
    }

    private function resolveJsonField($value): ?array
    {
        if (is_array($value) && count($value) > 0) {
            return $value;
        }

        if (is_string($value)) {
            $decoded = json_decode($value, true);
            if (is_array($decoded) && count($decoded) > 0) {
                return $decoded;
            }
        }

        return null;
    }
}
