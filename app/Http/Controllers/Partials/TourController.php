<?php

namespace App\Http\Controllers\Partials;

use App\Http\Controllers\Controller;
use App\Models\Tour;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class TourController extends Controller
{
    /**
     * Tour list partial
     * Returns: Grid of tour cards with pagination support
     *
     * Query params:
     * - per_page: Number of tours per page (default: 12, min: 6, max: 50)
     * - page: Current page number (default: 1)
     * - city: City ID to filter by (optional)
     * - append: If true, returns only cards without wrapper (for Load More)
     *
     * Usage:
     * - Initial load: GET /partials/tours?per_page=12
     * - With city filter: GET /partials/tours?per_page=12&city=2
     * - Load more: GET /partials/tours?page=2&per_page=12&append=true
     */
    public function list(Request $request)
    {
        // Get pagination parameters
        $perPage = $request->get('per_page', 12);
        $page = $request->get('page', 1);
        $cityId = $request->get('city');
        $isAppend = $request->boolean('append', false);

        // Validate per_page (prevent abuse: min 6, max 50)
        $perPage = min(max($perPage, 6), 50);

        // Cache key includes page, per_page, and city for proper caching
        $cacheKey = "tours.list.page.{$page}.per_page.{$perPage}.city." . ($cityId ?? 'all');

        $tours = Cache::remember($cacheKey, 3600, function () use ($perPage, $cityId) {
            $query = Tour::with(['city'])
                ->where('is_active', true);

            // Apply city filter if provided
            if (!empty($cityId)) {
                $query->where('city_id', $cityId);
            }

            return $query->orderBy('created_at', 'desc') // Newest first
                ->paginate($perPage);
        });

        return view('partials.tours.list', compact('tours', 'isAppend'));
    }

    /**
     * Hero section
     * Returns: Tour hero with title, image, price, CTA
     */
    public function hero(string $slug)
    {
        $tour = $this->getCachedTour($slug);
        return view('partials.tours.show.hero', compact('tour'));
    }

    /**
     * Gallery section
     * Returns: Hero image and gallery thumbnails
     */
    public function gallery(string $slug)
    {
        $tour = $this->getCachedTour($slug);
        return view('partials.tours.show.gallery', compact('tour'));
    }

    /**
     * Overview section
     * Returns: Description, quick info grid
     */
    public function overview(string $slug)
    {
        $tour = $this->getCachedTour($slug);
        return view('partials.tours.show.overview', compact('tour'));
    }

    /**
     * Highlights section
     * Returns: Bulleted list of tour highlights
     */
    public function highlights(string $slug)
    {
        $tour = $this->getCachedTour($slug);
        return view('partials.tours.show.highlights', compact('tour'));
    }

    /**
     * Itinerary section
     * Returns: Day-by-day itinerary (if multi-day tour)
     */
    public function itinerary(string $slug)
    {
        // Get tour with eager-loaded itinerary items (hierarchical structure)
        $tour = Cache::remember("tour.{$slug}.with_itinerary", 3600, function () use ($slug) {
            return Tour::where('slug', $slug)
                ->where('is_active', true)
                ->with([
                    'topLevelItems' => function($query) {
                        $query->orderBy('sort_order');
                    },
                    'topLevelItems.children' => function($query) {
                        $query->orderBy('sort_order');
                    }
                ])
                ->firstOrFail();
        });

        return view('partials.tours.show.itinerary', compact('tour'));
    }

    /**
     * Included/Excluded section
     * Returns: What's included and what's not included
     */
    public function includedExcluded(string $slug)
    {
        $tour = $this->getCachedTour($slug);
        return view('partials.tours.show.included-excluded', compact('tour'));
    }

    /**
     * FAQs section
     * Returns: Accordion of frequently asked questions
     */
    public function faqs(string $slug)
    {
        $tour = Tour::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $faqs = Cache::remember("tour.{$slug}.faqs", 86400, function () use ($tour) {
            return $tour->faqs()->orderBy('sort_order')->get();
        });

        $globalFaqs = \App\Models\Setting::get('global_faqs', []);

        return view('partials.tours.show.faqs', compact('tour', 'faqs', 'globalFaqs'));
    }

    /**
     * Extras section
     * Returns: Grid of optional add-on services
     */
    public function extras(string $slug)
    {
        $tour = Tour::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $extras = Cache::remember("tour.{$slug}.extras", 3600, function () use ($tour) {
            return $tour->activeExtras()->orderBy('sort_order')->get();
        });

        return view('partials.tours.show.extras', compact('tour', 'extras'));
    }

    /**
     * Reviews section
     * Returns: Paginated customer reviews
     */
    public function reviews(string $slug, Request $request)
    {
        $tour = Tour::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $page = $request->get('page', 1);

        $reviews = Cache::remember("tour.{$slug}.reviews.page.{$page}", 300, function () use ($tour) {
            return $tour->approvedReviews()
                ->orderBy('created_at', 'desc')
                ->paginate(10);
        });

        return view('partials.tours.show.reviews', compact('tour', 'reviews'));
    }

    /**
     * Helper: Get cached tour
     * Caches tour for 1 hour to reduce database queries
     */
    protected function getCachedTour(string $slug): Tour
    {
        return Cache::remember("tour.{$slug}", 3600, function () use ($slug) {
            return Tour::where('slug', $slug)
                ->where('is_active', true)
                ->with('city')
                ->firstOrFail();
        });
    }

    /**
     * Requirements section (Know Before You Go)
     * Returns: Tour-specific requirements or global defaults
     */
    /**
     * Requirements section (Know Before You Go)
     * Returns: Tour-specific requirements or global defaults
     */
    public function requirements(string $slug)
    {
        $tour = $this->getCachedTour($slug);
        $globalRequirements = \App\Models\Setting::get('global_requirements', []);

        return view('partials.tours.show.requirements', compact('tour', 'globalRequirements'));
    }

    /**
     * Cancellation Policy section
     * Returns: Tour cancellation policy with hours and custom text
     */
    public function cancellation(string $slug)
    {
        $tour = $this->getCachedTour($slug);
        return view('partials.tours.show.cancellation', compact('tour'));
    }

    /**
     * Meeting Point & Pickup section
     * Returns: Meeting point details, hotel pickup info, and map coordinates
     */
    public function meetingPoint(string $slug)
    {
        $tour = $this->getCachedTour($slug);
        return view('partials.tours.show.meeting-point', compact('tour'));
    }
}
