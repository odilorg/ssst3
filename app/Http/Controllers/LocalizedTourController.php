<?php

namespace App\Http\Controllers;

use App\Models\Tour;
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
        // Eager load relations for tour
        $tourRelations = [
            'pricingTiers' => function ($q) {
                $q->active()->ordered();
            },
            'upcomingDepartures' => function ($q) {
                $q->limit(6);
            },
        ];

        // Try 1: Find translation by exact locale and slug
        $translation = TourTranslation::where('locale', $locale)
            ->where('slug', $slug)
            ->with(['tour' => fn($q) => $q->with($tourRelations)])
            ->first();

        // Try 2: Slug belongs to another locale — find the tour, then get translation for requested locale
        if (!$translation) {
            $anyTranslation = TourTranslation::where('slug', $slug)
                ->with(['tour' => fn($q) => $q->with($tourRelations)])
                ->first();

            if ($anyTranslation) {
                $translation = TourTranslation::where('tour_id', $anyTranslation->tour_id)
                    ->where('locale', $locale)
                    ->first();

                if ($translation) {
                    $translation->load(['tour' => fn($q) => $q->with($tourRelations)]);
                }
                // No fallback to another locale — if no translation exists for this locale, 404
            }
        }

        // Try 3: Slug matches tour.slug directly — get translation for requested locale only
        if (!$translation) {
            $tour = Tour::where('slug', $slug)->with($tourRelations)->first();
            if ($tour) {
                $translation = $tour->translations()->where('locale', $locale)->first();

                if ($translation) {
                    $translation->setRelation('tour', $tour);
                }
            }
        }

        // If still nothing found, abort with 404
        if (!$translation || !$translation->tour) {
            abort(404);
        }

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
