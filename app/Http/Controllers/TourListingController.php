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
        // Start with base query
        $query = Tour::active()
            ->withFrontendRelations()
            ->select([
                'id', 'slug', 'title', 'short_description', 'long_description',
                'hero_image', 'price_per_person', 'currency', 'duration_days',
                'city_id', 'rating', 'review_count', 'is_active'
            ]);

        // Filter by category if provided
        if ($request->has('category')) {
            $category = TourCategory::where('slug', $request->get('category'))
                ->where('is_active', true)
                ->first();

            if ($category) {
                $query->whereHas('categories', function($q) use ($category) {
                    $q->where('tour_categories.id', $category->id);
                });
            }
        }

        // Get paginated results
        $tours = $query->recent() // Uses scopeRecent() - orderBy('created_at', 'desc')
            ->paginate(9)
            ->appends($request->only('category')); // Preserve category in pagination links

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
