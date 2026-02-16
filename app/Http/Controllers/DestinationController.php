<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Tour;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DestinationController extends Controller
{
    /**
     * Display destinations index page
     *
     * @return View
     */
    public function index(): View
    {
        // SSR: Load cities for crawler visibility
        $cities = City::where('country', 'Uzbekistan')
            ->active()
            ->orderBy('is_featured', 'desc')
            ->orderBy('display_order')
            ->get();

        return view('pages.destinations', compact('cities'));
    }

    /**
     * Display destination city guide page
     *
     * Informational page about the city with overview, sights,
     * and featured tours (not filtered by city_id).
     *
     * @param string $slug
     * @return View
     */
    public function show(string $slug): View
    {
        $city = City::where('slug', $slug)
            ->where('is_active', true)
            ->with(['monuments', 'translations'])
            ->firstOrFail();

        // SEO
        $pageTitle = $city->meta_title ?? ($city->name . ' Travel Guide | Jahongir Travel');
        $metaDescription = $city->meta_description ?? ($city->short_description ?? 'Explore ' . $city->name . ' with Jahongir Travel');
        $metaDescription = substr($metaDescription, 0, 160);
        $ogImage = $city->hero_image_url ?? $city->featured_image_url ?? asset('images/default-city.jpg');
        $canonicalUrl = url('/destinations/' . $city->slug);

        // Featured tours (show a few active tours, not filtered by city)
        $featuredTours = Tour::where('is_active', true)
            ->with(['city', 'translations'])
            ->orderBy('rating', 'desc')
            ->limit(6)
            ->get();

        // Related cities (excluding current)
        $relatedCities = City::where('country', 'Uzbekistan')
            ->active()
            ->where('id', '!=', $city->id)
            ->where(function ($q) {
                $q->whereNotNull('short_description')
                  ->orWhere('is_featured', true);
            })
            ->orderBy('display_order')
            ->limit(5)
            ->get();

        // Top sights from monuments
        $topSights = $city->monuments()->limit(6)->get();

        return view('pages.destination-landing', compact(
            'city',
            'pageTitle',
            'metaDescription',
            'ogImage',
            'canonicalUrl',
            'featuredTours',
            'relatedCities',
            'topSights'
        ));
    }
}
