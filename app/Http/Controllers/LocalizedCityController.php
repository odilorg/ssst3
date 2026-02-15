<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\CityTranslation;
use App\Models\Tour;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * LocalizedCityController
 *
 * Handles city detail pages using localized slugs from city_translations table.
 * Used when multilang.phases.city_translations is enabled.
 *
 * SSR approach: Tours and related cities are loaded server-side for SEO crawlability.
 */
class LocalizedCityController extends Controller
{
    /**
     * Display city detail page using localized slug.
     *
     * @param string $locale The locale from route parameter (set by middleware)
     * @param string $slug The localized city slug
     * @return View|RedirectResponse
     */
    public function show(string $locale, string $slug): View|RedirectResponse
    {
        // Try 1: Find translation by exact locale and slug
        $translation = CityTranslation::where('locale', $locale)
            ->where('slug', $slug)
            ->with(['city'])
            ->first();

        // Try 2: Slug belongs to another locale — find city, then get translation
        if (!$translation) {
            $anyTranslation = CityTranslation::where('slug', $slug)
                ->with(['city'])
                ->first();

            if ($anyTranslation) {
                $translation = CityTranslation::where('city_id', $anyTranslation->city_id)
                    ->where('locale', $locale)
                    ->first();

                if ($translation) {
                    $translation->load(['city']);
                }
            }
        }

        // Try 3: Slug matches city.slug directly
        if (!$translation) {
            $city = City::where('slug', $slug)
                ->where('is_active', true)
                ->first();

            if ($city) {
                $translation = $city->translations()->where('locale', $locale)->first();
                if ($translation) {
                    $translation->setRelation('city', $city);
                }
            }
        }

        // Fallback: redirect to English version
        if (!$translation || !$translation->city) {
            if ($locale !== 'en') {
                $enTranslation = $this->findEnglishTranslation($slug);
                if ($enTranslation) {
                    return redirect("/en/destinations/{$enTranslation->slug}", 301);
                }
            }
            abort(404);
        }

        $city = $translation->city;

        // Prepare SEO data - prefer translation fields, fallback to city
        $pageTitle = $translation->seo_title ?? $translation->name ?? $city->meta_title ?? $city->name;
        $metaDescription = $translation->seo_description ?? $translation->short_description ?? $city->meta_description;

        // Use hero image from city or fallback
        $ogImage = $city->hero_image_url ?? $city->featured_image_url ?? asset('images/og-default.jpg');

        // Canonical URL points to localized version
        $canonicalUrl = url("/{$locale}/destinations/{$slug}");

        // SSR: Load initial tours for this city (crawlable by search engines)
        $initialTours = Tour::with(['city'])
            ->where('is_active', true)
            ->where('city_id', $city->id)
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        // SSR: Load related cities (excluding current)
        $relatedCities = City::active()
            ->where('id', '!=', $city->id)
            ->orderBy('display_order')
            ->limit(5)
            ->get();

        return view('pages.destination-landing', compact(
            'city',
            'translation',
            'pageTitle',
            'metaDescription',
            'ogImage',
            'canonicalUrl',
            'initialTours',
            'relatedCities'
        ));
    }

    /**
     * Find the English translation for a city identified by slug.
     */
    private function findEnglishTranslation(string $slug): ?CityTranslation
    {
        $enTranslation = CityTranslation::where('locale', 'en')
            ->where('slug', $slug)
            ->first();

        if ($enTranslation) {
            return $enTranslation;
        }

        $anyTranslation = CityTranslation::where('slug', $slug)->first();
        if ($anyTranslation) {
            return CityTranslation::where('city_id', $anyTranslation->city_id)
                ->where('locale', 'en')
                ->first();
        }

        $city = City::where('slug', $slug)->first();
        if ($city) {
            return $city->translations()->where('locale', 'en')->first();
        }

        return null;
    }
}
