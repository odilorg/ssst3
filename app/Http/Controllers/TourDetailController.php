<?php

namespace App\Http\Controllers;

use App\Models\Tour;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TourDetailController extends Controller
{
    /**
     * Display tour detail page.
     *
     * When multilang routing is enabled, 301 redirects to the localized URL
     * so Google indexes only one canonical URL pattern (/{locale}/tours/{slug}).
     */
    public function show(string $slug): View|RedirectResponse
    {
        if (config('multilang.enabled') && config('multilang.phases.routes')) {
            $defaultLocale = config('multilang.default_locale', 'en');
            return redirect("/{$defaultLocale}/tours/{$slug}", 301);
        }

        // Find tour or 404, load relationships
        $tour = Tour::where('slug', $slug)
            ->with([
                'pricingTiers' => function($query) {
                    $query->active()->ordered();
                },
                'upcomingDepartures' => function($query) {
                    $query->limit(6); // Show next 6 departures
                }
            ])
            ->firstOrFail();

        // Prepare SEO-friendly data using Tour model methods
        $pageTitle = $tour->getSeoTitle();
        $metaDescription = $tour->getSeoDescription();

        // Use OG image from tour or fallback to default
        $ogImage = $tour->getOgImageUrl() ?? asset('images/og-default.jpg');

        $canonicalUrl = url('/tours/' . $tour->slug);

        // Generate structured data using Tour model method
        $structuredData = json_encode(
            $tour->generateSchemaData(),
            JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT
        );

        return view('pages.tour-details', compact(
            'tour',
            'pageTitle',
            'metaDescription',
            'ogImage',
            'canonicalUrl',
            'structuredData'
        ));
    }
}
