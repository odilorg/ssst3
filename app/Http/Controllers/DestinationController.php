<?php

namespace App\Http\Controllers;

use App\Models\City;
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
        return view('pages.destinations');
    }

    /**
     * Display destination landing page for a specific city
     *
     * @param string $slug
     * @return View
     */
    public function show(string $slug): View
    {
        // Find city or 404
        $city = City::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        // Prepare SEO-friendly data
        $pageTitle = $city->meta_title ?? ($city->name . ' Tours & Travel Guide | Jahongir Travel');
        $metaDescription = $city->meta_description ?? ($city->short_description ?? '');
        $metaDescription = substr($metaDescription, 0, 160);

        $ogImage = $city->hero_image_url ?? $city->featured_image_url ?? asset('images/default-city.jpg');
        $canonicalUrl = url('/destinations/' . $city->slug);

        return view('pages.destination-landing', compact(
            'city',
            'pageTitle',
            'metaDescription',
            'ogImage',
            'canonicalUrl'
        ));
    }
}
