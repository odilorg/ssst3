<?php

namespace App\Http\Controllers;

use App\Models\TourTranslation;
use Illuminate\View\View;

/**
 * LocalizedTourController
 *
 * Handles tour detail pages using localized slugs from tour_translations table.
 * Used when multilang.phases.tour_translations is enabled.
 */
class LocalizedTourController extends Controller
{
    /**
     * Display tour detail page using localized slug.
     *
     * @param string $locale The locale from route parameter (set by middleware)
     * @param string $slug The localized tour slug
     * @return View
     */
    public function show(string $locale, string $slug): View
    {
        // Find translation by locale and slug, with tour relationship
        $translation = TourTranslation::where('locale', $locale)
            ->where('slug', $slug)
            ->with(['tour' => function ($query) {
                $query->with([
                    'pricingTiers' => function ($q) {
                        $q->active()->ordered();
                    },
                    'upcomingDepartures' => function ($q) {
                        $q->limit(6);
                    },
                ]);
            }])
            ->firstOrFail();

        $tour = $translation->tour;

        // Prepare SEO data - prefer translation fields, fallback to tour
        $pageTitle = $translation->seo_title ?? $translation->title ?? $tour->getSeoTitle();
        $metaDescription = $translation->seo_description ?? $tour->getSeoDescription();

        // Use OG image from tour or fallback to default
        $ogImage = $tour->getOgImageUrl() ?? asset('images/og-default.jpg');

        // Canonical URL points to localized version
        $canonicalUrl = url("/{$locale}/tours/{$slug}");

        // Generate structured data using Tour model method
        $structuredData = json_encode(
            $tour->generateSchemaData(),
            JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT
        );

        return view('pages.tour-details', compact(
            'tour',
            'translation',
            'pageTitle',
            'metaDescription',
            'ogImage',
            'canonicalUrl',
            'structuredData'
        ));
    }
}
