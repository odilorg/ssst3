<?php

namespace App\Http\Controllers;

use App\Models\CityTranslation;
use Illuminate\View\View;

/**
 * LocalizedCityController
 *
 * Handles city detail pages using localized slugs from city_translations table.
 * Used when multilang.phases.city_translations is enabled.
 */
class LocalizedCityController extends Controller
{
    /**
     * Display city detail page using localized slug.
     *
     * @param string $locale The locale from route parameter (set by middleware)
     * @param string $slug The localized city slug
     * @return View
     */
    public function show(string $locale, string $slug): View
    {
        // Find translation by locale and slug, with city relationship
        $translation = CityTranslation::where('locale', $locale)
            ->where('slug', $slug)
            ->with(['city' => function ($query) {
                $query->with([
                    'tours' => function ($q) {
                        $q->where('is_active', true)
                          ->orderBy('rating', 'desc')
                          ->limit(6);
                    },
                ]);
            }])
            ->firstOrFail();

        $city = $translation->city;

        // Prepare SEO data - prefer translation fields, fallback to city
        $pageTitle = $translation->seo_title ?? $translation->name ?? $city->meta_title ?? $city->name;
        $metaDescription = $translation->seo_description ?? $translation->short_description ?? $city->meta_description;

        // Use hero image from city or fallback
        $ogImage = $city->hero_image_url ?? $city->featured_image_url ?? asset('images/og-default.jpg');

        // Canonical URL points to localized version
        $canonicalUrl = url("/{$locale}/destinations/{$slug}");

        return view('pages.city-detail', compact(
            'city',
            'translation',
            'pageTitle',
            'metaDescription',
            'ogImage',
            'canonicalUrl'
        ));
    }
}
