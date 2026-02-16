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
        [$tour, $data] = $this->preparePdfData($slug);

        $pdf = Pdf::loadView('pdf.tour-itinerary', $data);
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOption('compress', true);
        $pdf->setOption('dpi', 96);

        return $pdf->download('itinerary-' . $tour->slug . '.pdf');
    }

    public function stream(string $slug)
    {
        [$tour, $data] = $this->preparePdfData($slug);

        $pdf = Pdf::loadView('pdf.tour-itinerary', $data);
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOption('compress', true);
        $pdf->setOption('dpi', 96);

        return $pdf->stream('itinerary-' . $tour->slug . '.pdf');
    }

    private function preparePdfData(string $slug): array
    {
        $tour = Tour::where('slug', $slug)
            ->where('is_active', true)
            ->with(['translations', 'city', 'itineraryItems' => function ($query) {
                $query->where('type', 'day')
                    ->orderBy('sort_order')
                    ->with('children');
            }])
            ->firstOrFail();

        $companySettings = CompanySetting::current();

        // Prefer English translation, fallback to tour's own fields
        $translation = $tour->translationOrDefault('en');

        $itinerary = $this->resolveJson($translation?->itinerary_json);
        $highlights = $this->resolveJson($translation?->highlights_json) ?: $this->resolveJson($tour->highlights);
        $included = $this->resolveJson($translation?->included_json) ?: $this->resolveJson($tour->included_items);
        $excluded = $this->resolveJson($translation?->excluded_json) ?: $this->resolveJson($tour->excluded_items);
        $description = $translation?->excerpt ?: $tour->short_description;

        return [$tour, [
            'tour' => $tour,
            'companySettings' => $companySettings,
            'itinerary' => $itinerary,
            'highlights' => $highlights,
            'included' => $included,
            'excluded' => $excluded,
            'description' => $description,
        ]];
    }

    private function resolveJson($value): ?array
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
