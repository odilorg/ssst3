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

    /**
     * Display Mini Journeys (1-2 day tours including overnight camping)
     *
     * @param Request $request
     * @return View
     */
    public function miniJourneys(Request $request): View
    {
        // Query for tours with duration 1-2 days
        $query = Tour::active()
            ->withFrontendRelations()
            ->where('duration_days', '<=', 2)
            ->select([
                'id', 'slug', 'title', 'short_description', 'long_description',
                'hero_image', 'price_per_person', 'currency', 'duration_days',
                'city_id', 'rating', 'review_count', 'is_active'
            ]);

        // Get paginated results
        $tours = $query->recent()
            ->paginate(9);

        // Generate structured data
        $structuredData = $this->structuredDataService->generateTourListingSchema($tours);

        return view('pages.mini-journeys', [
            'tours' => $tours,
            'pageTitle' => 'Mini Journeys - 1-2 Day Experiences',
            'pageDescription' => 'Quick adventures and overnight camping experiences in Uzbekistan. Perfect for travelers with limited time.',
            'structuredData' => $this->structuredDataService->encode($structuredData)
        ]);
    }

    /**
     * Display Craft Journeys (3+ day multi-day tours with boutique hotels)
     *
     * @param Request $request
     * @return View
     */
    public function craftJourneys(Request $request): View
    {
        // Query for tours with duration 3+ days
        $query = Tour::active()
            ->withFrontendRelations()
            ->where('duration_days', '>=', 3)
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
        $tours = $query->recent()
            ->paginate(9)
            ->appends($request->only('category'));

        // Get categories with tour counts (only for 3+ day tours)
        $categories = TourCategory::where('is_active', true)
            ->withCount(['tours' => function($query) {
                $query->where('is_active', true)
                      ->where('duration_days', '>=', 3);
            }])
            ->orderBy('display_order')
            ->get();

        // Generate structured data
        $structuredData = $this->structuredDataService->generateTourListingSchema($tours);

        return view('pages.craft-journeys', [
            'tours' => $tours,
            'categories' => $categories,
            'pageTitle' => 'Craft Journeys - Multi-Day Boutique Tours',
            'pageDescription' => 'Immersive multi-day adventures with carefully curated boutique hotels across Uzbekistan\'s Silk Road.',
            'structuredData' => $this->structuredDataService->encode($structuredData)
        ]);
    }
}
