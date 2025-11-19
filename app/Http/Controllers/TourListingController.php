<?php

namespace App\Http\Controllers;

use App\Models\Tour;
use App\Models\TourCategory;
use App\Services\StructuredDataService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TourListingController extends Controller
{
    public function __construct(
        private StructuredDataService $structuredDataService
    ) {}

    /**
     * Display tour listing page with all tours
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        // Use new query scopes for cleaner, optimized queries
        $tours = Tour::active()
            ->withFrontendRelations()
            ->select([
                'id', 'slug', 'title', 'short_description', 'long_description',
                'hero_image', 'price_per_person', 'currency', 'duration_days',
                'city_id', 'rating', 'review_count', 'is_active'
            ])
            ->recent() // Uses scopeRecent() - orderBy('created_at', 'desc')
            ->paginate(18);

        // Get categories with tour counts
        $categories = TourCategory::where('is_active', true)
            ->withCount(['tours' => function($query) {
                $query->where('is_active', true);
            }])
            ->orderBy('display_order')
            ->get();

        // Generate structured data using service
        $structuredData = $this->structuredDataService->generateTourListingSchema($tours);

        return view('pages.tours-listing', [
            'tours' => $tours,
            'categories' => $categories,
            'structuredData' => $this->structuredDataService->encode($structuredData)
        ]);
    }
}
