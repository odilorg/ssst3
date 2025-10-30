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
     * Returns: Grid of tour cards
     */
    public function list(Request $request)
    {
        $tours = Cache::remember('tours.list', 3600, function () {
            return Tour::with('city')
                ->where('is_active', true)
                ->orderBy('created_at', 'desc')
                ->get();
        });

        return view('partials.tours.list', compact('tours'));
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
        $tour = $this->getCachedTour($slug);
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

        return view('partials.tours.show.faqs', compact('tour', 'faqs'));
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
}
