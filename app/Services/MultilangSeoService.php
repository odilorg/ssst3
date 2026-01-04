<?php

namespace App\Services;

use App\Models\BlogPost;
use App\Models\BlogPostTranslation;
use App\Models\City;
use App\Models\CityTranslation;
use App\Models\Tour;
use App\Models\TourTranslation;
use Illuminate\Support\Collection;

/**
 * Multilingual SEO Service
 *
 * Handles canonical URLs, hreflang tags, and sitemap generation
 * for the multilingual system.
 *
 * IMPORTANT: Only generates hreflang links for locales that have translations.
 */
class MultilangSeoService
{
    /**
     * Get canonical URL for the current page.
     *
     * For localized pages, canonical points to the same localized URL.
     * For old (non-localized) pages, canonical remains unchanged.
     */
    public function getCanonicalUrl(?string $locale = null): string
    {
        // If SEO phase is not enabled, return current URL
        if (!config('multilang.phases.seo')) {
            return url()->current();
        }

        return url()->current();
    }

    /**
     * Get hreflang alternate links for a Tour.
     *
     * Only returns links for locales that have translations.
     *
     * @param Tour $tour The tour model
     * @param string|null $currentLocale Current locale (defaults to app locale)
     * @return Collection Collection of ['locale' => 'xx', 'url' => 'https://...']
     */
    public function getTourHreflangLinks(Tour $tour, ?string $currentLocale = null): Collection
    {
        if (!config('multilang.phases.seo') || !config('multilang.phases.tour_translations')) {
            return collect();
        }

        $currentLocale = $currentLocale ?? app()->getLocale();
        $defaultLocale = config('multilang.default_locale', 'en');

        // Get all translations for this tour
        $translations = $tour->translations()->get();

        if ($translations->isEmpty()) {
            return collect();
        }

        $links = collect();

        foreach ($translations as $translation) {
            $links->push([
                'locale' => $translation->locale,
                'url' => url("/{$translation->locale}/tours/{$translation->slug}"),
                'is_current' => $translation->locale === $currentLocale,
            ]);
        }

        // Add x-default pointing to default locale (if translation exists)
        $defaultTranslation = $translations->firstWhere('locale', $defaultLocale);
        if ($defaultTranslation) {
            $links->push([
                'locale' => 'x-default',
                'url' => url("/{$defaultLocale}/tours/{$defaultTranslation->slug}"),
                'is_current' => false,
            ]);
        }

        return $links;
    }

    /**
     * Get hreflang alternate links for a City.
     *
     * Only returns links for locales that have translations.
     *
     * @param City $city The city model
     * @param string|null $currentLocale Current locale (defaults to app locale)
     * @return Collection Collection of ['locale' => 'xx', 'url' => 'https://...']
     */
    public function getCityHreflangLinks(City $city, ?string $currentLocale = null): Collection
    {
        if (!config('multilang.phases.seo') || !config('multilang.phases.city_translations')) {
            return collect();
        }

        $currentLocale = $currentLocale ?? app()->getLocale();
        $defaultLocale = config('multilang.default_locale', 'en');

        // Get all translations for this city
        $translations = $city->translations()->get();

        if ($translations->isEmpty()) {
            return collect();
        }

        $links = collect();

        foreach ($translations as $translation) {
            $links->push([
                'locale' => $translation->locale,
                'url' => url("/{$translation->locale}/destinations/{$translation->slug}"),
                'is_current' => $translation->locale === $currentLocale,
            ]);
        }

        // Add x-default pointing to default locale (if translation exists)
        $defaultTranslation = $translations->firstWhere('locale', $defaultLocale);
        if ($defaultTranslation) {
            $links->push([
                'locale' => 'x-default',
                'url' => url("/{$defaultLocale}/destinations/{$defaultTranslation->slug}"),
                'is_current' => false,
            ]);
        }

        return $links;
    }

    /**
     * Get hreflang alternate links for a BlogPost.
     *
     * Only returns links for locales that have translations.
     *
     * @param BlogPost $post The blog post model
     * @param string|null $currentLocale Current locale (defaults to app locale)
     * @return Collection Collection of ['locale' => 'xx', 'url' => 'https://...']
     */
    public function getBlogHreflangLinks(BlogPost $post, ?string $currentLocale = null): Collection
    {
        if (!config('multilang.phases.seo') || !config('multilang.phases.blog_translations')) {
            return collect();
        }

        $currentLocale = $currentLocale ?? app()->getLocale();
        $defaultLocale = config('multilang.default_locale', 'en');

        // Get all translations for this blog post
        $translations = $post->translations()->get();

        if ($translations->isEmpty()) {
            return collect();
        }

        $links = collect();

        foreach ($translations as $translation) {
            $links->push([
                'locale' => $translation->locale,
                'url' => url("/{$translation->locale}/blog/{$translation->slug}"),
                'is_current' => $translation->locale === $currentLocale,
            ]);
        }

        // Add x-default pointing to default locale (if translation exists)
        $defaultTranslation = $translations->firstWhere('locale', $defaultLocale);
        if ($defaultTranslation) {
            $links->push([
                'locale' => 'x-default',
                'url' => url("/{$defaultLocale}/blog/{$defaultTranslation->slug}"),
                'is_current' => false,
            ]);
        }

        return $links;
    }

    /**
     * Get hreflang alternate links for static pages.
     *
     * Static pages are available in all locales (UI strings translated).
     *
     * @param string $routeName The route name (e.g., 'about', 'contact')
     * @param string|null $currentLocale Current locale (defaults to app locale)
     * @return Collection Collection of ['locale' => 'xx', 'url' => 'https://...']
     */
    public function getStaticPageHreflangLinks(string $routeName, ?string $currentLocale = null): Collection
    {
        if (!config('multilang.phases.seo') || !config('multilang.phases.routes')) {
            return collect();
        }

        $currentLocale = $currentLocale ?? app()->getLocale();
        $defaultLocale = config('multilang.default_locale', 'en');
        $locales = config('multilang.locales', ['en', 'ru', 'fr']);

        $links = collect();

        foreach ($locales as $locale) {
            $links->push([
                'locale' => $locale,
                'url' => url("/{$locale}/{$routeName}"),
                'is_current' => $locale === $currentLocale,
            ]);
        }

        // Add x-default pointing to default locale
        $links->push([
            'locale' => 'x-default',
            'url' => url("/{$defaultLocale}/{$routeName}"),
            'is_current' => false,
        ]);

        return $links;
    }

    /**
     * Get all tours with translations for a specific locale.
     *
     * Used for sitemap generation.
     *
     * @param string $locale The locale code
     * @return Collection
     */
    public function getToursForLocale(string $locale): Collection
    {
        return TourTranslation::where('locale', $locale)
            ->with('tour')
            ->get()
            ->filter(fn ($t) => $t->tour && $t->tour->is_active)
            ->map(fn ($t) => [
                'url' => url("/{$locale}/tours/{$t->slug}"),
                'lastmod' => $t->updated_at ?? $t->tour->updated_at,
            ]);
    }

    /**
     * Get all cities with translations for a specific locale.
     *
     * Used for sitemap generation.
     *
     * @param string $locale The locale code
     * @return Collection
     */
    public function getCitiesForLocale(string $locale): Collection
    {
        return CityTranslation::where('locale', $locale)
            ->with('city')
            ->get()
            ->filter(fn ($t) => $t->city && $t->city->is_active)
            ->map(fn ($t) => [
                'url' => url("/{$locale}/destinations/{$t->slug}"),
                'lastmod' => $t->updated_at ?? $t->city->updated_at,
            ]);
    }

    /**
     * Get all blog posts with translations for a specific locale.
     *
     * Used for sitemap generation.
     *
     * @param string $locale The locale code
     * @return Collection
     */
    public function getBlogPostsForLocale(string $locale): Collection
    {
        return BlogPostTranslation::where('locale', $locale)
            ->with('blogPost')
            ->get()
            ->filter(fn ($t) => $t->blogPost && $t->blogPost->is_published)
            ->map(fn ($t) => [
                'url' => url("/{$locale}/blog/{$t->slug}"),
                'lastmod' => $t->updated_at ?? $t->blogPost->updated_at,
            ]);
    }

    /**
     * Get locales that have any translations.
     *
     * Used for sitemap index generation.
     *
     * @return array List of locale codes with content
     */
    public function getLocalesWithContent(): array
    {
        $localesWithContent = [];
        $locales = config('multilang.locales', ['en', 'ru', 'fr']);

        foreach ($locales as $locale) {
            $hasTours = TourTranslation::where('locale', $locale)->exists();
            $hasCities = CityTranslation::where('locale', $locale)->exists();
            $hasBlogPosts = BlogPostTranslation::where('locale', $locale)->exists();

            if ($hasTours || $hasCities || $hasBlogPosts) {
                $localesWithContent[] = $locale;
            }
        }

        return $localesWithContent;
    }
}
