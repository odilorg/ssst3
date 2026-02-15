<?php

namespace App\Http\Controllers;

use App\Models\Tour;
use App\Models\TourTranslation;
use Illuminate\Http\RedirectResponse;
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
    public function show(string $locale, string $slug): View|RedirectResponse
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

        // Fallback: requested locale missing → 302 redirect to English version
        if (!$translation || !$translation->tour) {
            if ($locale !== 'en') {
                $enTranslation = $this->findEnglishTranslation($slug, $tourRelations);
                if ($enTranslation) {
                    return redirect("/en/tours/{$enTranslation->slug}", 301);
                }
            }
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

        // Detect thin/empty translations → noindex to prevent low-quality indexing
        $hasContent = !empty($translation->content) || !empty($translation->excerpt);
        $robotsDirective = $hasContent ? 'index, follow' : 'noindex, follow';

        // Collect alternate locale URLs for og:locale:alternate
        $ogLocaleAlternates = [];
        if (!$tour->relationLoaded('translations')) {
            $tour->load('translations:id,tour_id,locale,slug');
        }
        foreach ($tour->translations as $sibling) {
            if ($sibling->locale !== $locale && $sibling->slug) {
                $ogLocaleAlternates[] = $sibling->locale . '_' . strtoupper($sibling->locale);
            }
        }

        // Generate structured data using Tour model methods (translation-aware)
        $schemas = array_filter([
            $tour->generateSchemaData($translation),
            $tour->generateBreadcrumbSchema($translation),
            $tour->generateFaqSchema($translation),
        ]);

        $structuredData = json_encode(
            count($schemas) === 1 ? $schemas[0] : array_values($schemas),
            JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT
        );

        return view('pages.tour-details', compact(
            'tour',
            'translation',
            'pageTitle',
            'metaDescription',
            'ogImage',
            'canonicalUrl',
            'structuredData',
            'robotsDirective',
            'ogLocaleAlternates'
        ));
    }

    /**
     * Find the English translation for a tour identified by slug.
     */
    private function findEnglishTranslation(string $slug, array $tourRelations): ?TourTranslation
    {
        // Slug might be the EN translation slug itself
        $enTranslation = TourTranslation::where('locale', 'en')
            ->where('slug', $slug)
            ->first();

        if ($enTranslation) {
            return $enTranslation;
        }

        // Slug belongs to another locale — find tour, then get EN translation
        $anyTranslation = TourTranslation::where('slug', $slug)->first();
        if ($anyTranslation) {
            return TourTranslation::where('tour_id', $anyTranslation->tour_id)
                ->where('locale', 'en')
                ->first();
        }

        // Slug matches tour.slug directly
        $tour = Tour::where('slug', $slug)->first();
        if ($tour) {
            return $tour->translations()->where('locale', 'en')->first();
        }

        return null;
    }
}
