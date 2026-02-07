<?php

namespace App\Services;

use App\Models\Tour;
use App\Models\City;
use App\Models\BlogPost;
use App\Models\TourTranslation;
use App\Models\CityTranslation;
use App\Models\BlogPostTranslation;
use Illuminate\Support\Collection;

/**
 * Translation Coverage Service
 *
 * Provides statistics and reports on translation coverage
 * for tours, cities, and blog posts.
 */
class TranslationCoverageService
{
    /**
     * Get supported locales.
     */
    public function getSupportedLocales(): array
    {
        return config('multilang.locales', ['en', 'ru', 'fr']);
    }

    /**
     * Get default locale.
     */
    public function getDefaultLocale(): string
    {
        return config('multilang.default_locale', 'en');
    }

    /**
     * Get complete translation coverage report.
     */
    public function getFullReport(): array
    {
        return [
            'tours' => $this->getToursCoverage(),
            'cities' => $this->getCitiesCoverage(),
            'blog_posts' => $this->getBlogPostsCoverage(),
            'summary' => $this->getSummary(),
        ];
    }

    /**
     * Get tours translation coverage.
     */
    public function getToursCoverage(): array
    {
        $locales = $this->getSupportedLocales();
        $activeTours = Tour::where('is_active', true)->with('translations')->get();

        $coverage = [];
        $missingByLocale = [];

        foreach ($locales as $locale) {
            $missingByLocale[$locale] = [];
        }

        foreach ($activeTours as $tour) {
            $translatedLocales = $tour->translations->pluck('locale')->toArray();

            foreach ($locales as $locale) {
                if (!in_array($locale, $translatedLocales)) {
                    $missingByLocale[$locale][] = [
                        'id' => $tour->id,
                        'title' => $tour->title,
                        'slug' => $tour->slug,
                        'edit_url' => route('filament.admin.resources.tours.edit', $tour->id),
                    ];
                }
            }
        }

        $totalTours = $activeTours->count();

        foreach ($locales as $locale) {
            $translated = $totalTours - count($missingByLocale[$locale]);
            $coverage[$locale] = [
                'total' => $totalTours,
                'translated' => $translated,
                'missing' => count($missingByLocale[$locale]),
                'percentage' => $totalTours > 0 ? round(($translated / $totalTours) * 100, 1) : 0,
                'missing_items' => $missingByLocale[$locale],
            ];
        }

        return $coverage;
    }

    /**
     * Get cities translation coverage.
     */
    public function getCitiesCoverage(): array
    {
        $locales = $this->getSupportedLocales();
        $activeCities = City::where('is_active', true)->with('translations')->get();

        $coverage = [];
        $missingByLocale = [];

        foreach ($locales as $locale) {
            $missingByLocale[$locale] = [];
        }

        foreach ($activeCities as $city) {
            $translatedLocales = $city->translations->pluck('locale')->toArray();

            foreach ($locales as $locale) {
                if (!in_array($locale, $translatedLocales)) {
                    $missingByLocale[$locale][] = [
                        'id' => $city->id,
                        'title' => $city->name,
                        'slug' => $city->slug,
                        'edit_url' => route('filament.admin.resources.cities.edit', $city->id),
                    ];
                }
            }
        }

        $totalCities = $activeCities->count();

        foreach ($locales as $locale) {
            $translated = $totalCities - count($missingByLocale[$locale]);
            $coverage[$locale] = [
                'total' => $totalCities,
                'translated' => $translated,
                'missing' => count($missingByLocale[$locale]),
                'percentage' => $totalCities > 0 ? round(($translated / $totalCities) * 100, 1) : 0,
                'missing_items' => $missingByLocale[$locale],
            ];
        }

        return $coverage;
    }

    /**
     * Get blog posts translation coverage.
     */
    public function getBlogPostsCoverage(): array
    {
        $locales = $this->getSupportedLocales();
        $publishedPosts = BlogPost::where('is_published', true)->with('translations')->get();

        $coverage = [];
        $missingByLocale = [];

        foreach ($locales as $locale) {
            $missingByLocale[$locale] = [];
        }

        foreach ($publishedPosts as $post) {
            $translatedLocales = $post->translations->pluck('locale')->toArray();

            foreach ($locales as $locale) {
                if (!in_array($locale, $translatedLocales)) {
                    $missingByLocale[$locale][] = [
                        'id' => $post->id,
                        'title' => $post->title,
                        'slug' => $post->slug,
                        'edit_url' => route('filament.admin.resources.blog-posts.edit', $post->id),
                    ];
                }
            }
        }

        $totalPosts = $publishedPosts->count();

        foreach ($locales as $locale) {
            $translated = $totalPosts - count($missingByLocale[$locale]);
            $coverage[$locale] = [
                'total' => $totalPosts,
                'translated' => $translated,
                'missing' => count($missingByLocale[$locale]),
                'percentage' => $totalPosts > 0 ? round(($translated / $totalPosts) * 100, 1) : 0,
                'missing_items' => $missingByLocale[$locale],
            ];
        }

        return $coverage;
    }

    /**
     * Get overall summary statistics.
     */
    public function getSummary(): array
    {
        $locales = $this->getSupportedLocales();
        $toursCoverage = $this->getToursCoverage();
        $citiesCoverage = $this->getCitiesCoverage();
        $blogPostsCoverage = $this->getBlogPostsCoverage();

        $summary = [];

        foreach ($locales as $locale) {
            $totalItems = ($toursCoverage[$locale]['total'] ?? 0)
                + ($citiesCoverage[$locale]['total'] ?? 0)
                + ($blogPostsCoverage[$locale]['total'] ?? 0);

            $translatedItems = ($toursCoverage[$locale]['translated'] ?? 0)
                + ($citiesCoverage[$locale]['translated'] ?? 0)
                + ($blogPostsCoverage[$locale]['translated'] ?? 0);

            $missingItems = ($toursCoverage[$locale]['missing'] ?? 0)
                + ($citiesCoverage[$locale]['missing'] ?? 0)
                + ($blogPostsCoverage[$locale]['missing'] ?? 0);

            $summary[$locale] = [
                'total' => $totalItems,
                'translated' => $translatedItems,
                'missing' => $missingItems,
                'percentage' => $totalItems > 0 ? round(($translatedItems / $totalItems) * 100, 1) : 0,
            ];
        }

        return $summary;
    }

    /**
     * Get missing translations for a specific locale.
     */
    public function getMissingForLocale(string $locale): array
    {
        $toursCoverage = $this->getToursCoverage();
        $citiesCoverage = $this->getCitiesCoverage();
        $blogPostsCoverage = $this->getBlogPostsCoverage();

        return [
            'tours' => $toursCoverage[$locale]['missing_items'] ?? [],
            'cities' => $citiesCoverage[$locale]['missing_items'] ?? [],
            'blog_posts' => $blogPostsCoverage[$locale]['missing_items'] ?? [],
        ];
    }

    /**
     * Get locale display names.
     */
    public function getLocaleNames(): array
    {
        return config('multilang.locale_names', [
            'en' => ['name' => 'English', 'native' => 'English', 'flag' => '🇬🇧'],
            'ru' => ['name' => 'Russian', 'native' => 'Русский', 'flag' => '🇷🇺'],
            'fr' => ['name' => 'French', 'native' => 'Français', 'flag' => '🇫🇷'],
        ]);
    }
}
