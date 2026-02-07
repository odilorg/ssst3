<?php

namespace App\Services;

use App\Models\Tour;
use App\Models\City;
use App\Models\BlogPost;
use App\Models\TourTranslation;
use App\Models\CityTranslation;
use App\Models\BlogPostTranslation;
use Illuminate\Support\Facades\Cache;

/**
 * SEO Service for Multilingual Support
 *
 * Handles canonical URLs, hreflang tags, and sitemap generation
 * for localized content.
 */
class SeoService
{
    /**
     * Get canonical URL for current page.
     *
     * For localized pages, the canonical is the same localized URL.
     * For old (non-localized) pages, canonical is unchanged.
     *
     * @param string|null $overrideUrl Optional override URL
     * @return string
     */
    public function getCanonicalUrl(?string $overrideUrl = null): string
    {
        if ($overrideUrl) {
            return $overrideUrl;
        }

        return url()->current();
    }

    /**
     * Generate hreflang tags for an entity with translations.
     *
     * Only generates links for locales that have translations.
     *
     * @param object $entity The entity (Tour, City, BlogPost)
     * @param string $routeName The localized route name (e.g., 'localized.tours.show')
     * @param string|null $xDefault The x-default URL (usually default locale or non-localized)
     * @return array Array of ['locale' => 'xx', 'url' => 'https://...']
     */
    public function getHreflangTags(object $entity, string $routeName, ?string $xDefault = null): array
    {
        $hreflangs = [];
        $supportedLocales = config('multilang.locales', ['en', 'ru', 'fr']);
        $defaultLocale = config('multilang.default_locale', 'en');

        // Check if entity has translations
        if (!method_exists($entity, 'translations')) {
            return $hreflangs;
        }

        // Load translations if not already loaded
        if (!$entity->relationLoaded('translations')) {
            $entity->load('translations');
        }

        // Get available translations
        $availableLocales = $entity->translations->pluck('locale')->toArray();

        foreach ($supportedLocales as $locale) {
            if (!in_array($locale, $availableLocales)) {
                continue;
            }

            $translation = $entity->translations->firstWhere('locale', $locale);

            if (!$translation || !$translation->slug) {
                continue;
            }

            $url = route($routeName, [
                'locale' => $locale,
                'slug' => $translation->slug,
            ]);

            $hreflangs[] = [
                'locale' => $locale,
                'url' => $url,
            ];
        }

        // Add x-default if specified or use default locale
        if (!empty($hreflangs)) {
            if ($xDefault) {
                $hreflangs[] = [
                    'locale' => 'x-default',
                    'url' => $xDefault,
                ];
            } elseif (in_array($defaultLocale, $availableLocales)) {
                $defaultTranslation = $entity->translations->firstWhere('locale', $defaultLocale);
                if ($defaultTranslation && $defaultTranslation->slug) {
                    $hreflangs[] = [
                        'locale' => 'x-default',
                        'url' => route($routeName, [
                            'locale' => $defaultLocale,
                            'slug' => $defaultTranslation->slug,
                        ]),
                    ];
                }
            }
        }

        return $hreflangs;
    }

    /**
     * Generate hreflang tags for static pages (same URL structure across locales).
     *
     * @param string $routeName The localized route name (e.g., 'localized.home')
     * @param array $params Additional route parameters (excluding locale)
     * @return array
     */
    public function getHreflangTagsForStaticPage(string $routeName, array $params = []): array
    {
        $hreflangs = [];
        $supportedLocales = config('multilang.locales', ['en', 'ru', 'fr']);
        $defaultLocale = config('multilang.default_locale', 'en');

        foreach ($supportedLocales as $locale) {
            $hreflangs[] = [
                'locale' => $locale,
                'url' => route($routeName, array_merge(['locale' => $locale], $params)),
            ];
        }

        // Add x-default pointing to default locale
        $hreflangs[] = [
            'locale' => 'x-default',
            'url' => route($routeName, array_merge(['locale' => $defaultLocale], $params)),
        ];

        return $hreflangs;
    }

    /**
     * Generate sitemap for a specific locale.
     *
     * @param string $locale Locale code
     * @return array Array of URLs with metadata
     */
    public function generateLocaleSitemap(string $locale): array
    {
        $cacheKey = "sitemap_{$locale}";
        $cacheDuration = 60 * 12; // 12 hours

        return Cache::remember($cacheKey, $cacheDuration, function () use ($locale) {
            $urls = [];

            // Static pages
            $staticPages = [
                'localized.home' => ['priority' => '1.0', 'changefreq' => 'daily'],
                'localized.about' => ['priority' => '0.7', 'changefreq' => 'monthly'],
                'localized.contact' => ['priority' => '0.7', 'changefreq' => 'monthly'],
                'localized.mini-journeys.index' => ['priority' => '0.9', 'changefreq' => 'weekly'],
                'localized.craft-journeys.index' => ['priority' => '0.9', 'changefreq' => 'weekly'],
                'localized.destinations.index' => ['priority' => '0.8', 'changefreq' => 'weekly'],
                'localized.blog.index' => ['priority' => '0.8', 'changefreq' => 'daily'],
            ];

            foreach ($staticPages as $routeName => $meta) {
                try {
                    $urls[] = [
                        'loc' => route($routeName, ['locale' => $locale]),
                        'lastmod' => now()->toIso8601String(),
                        'changefreq' => $meta['changefreq'],
                        'priority' => $meta['priority'],
                    ];
                } catch (\Exception $e) {
                    // Route may not exist
                }
            }

            // Tour translations
            if (config('multilang.phases.tour_translations')) {
                $tourTranslations = TourTranslation::where('locale', $locale)
                    ->whereHas('tour', fn ($q) => $q->where('is_active', true))
                    ->with('tour:id,updated_at')
                    ->get();

                foreach ($tourTranslations as $translation) {
                    $urls[] = [
                        'loc' => route('localized.tours.show', [
                            'locale' => $locale,
                            'slug' => $translation->slug,
                        ]),
                        'lastmod' => ($translation->tour?->updated_at ?? $translation->updated_at)->toIso8601String(),
                        'changefreq' => 'weekly',
                        'priority' => '0.8',
                    ];
                }
            }

            // City translations
            if (config('multilang.phases.city_translations')) {
                $cityTranslations = CityTranslation::where('locale', $locale)
                    ->whereHas('city', fn ($q) => $q->where('is_active', true))
                    ->with('city:id,updated_at')
                    ->get();

                foreach ($cityTranslations as $translation) {
                    $urls[] = [
                        'loc' => route('localized.city.show', [
                            'locale' => $locale,
                            'slug' => $translation->slug,
                        ]),
                        'lastmod' => ($translation->city?->updated_at ?? $translation->updated_at)->toIso8601String(),
                        'changefreq' => 'weekly',
                        'priority' => '0.7',
                    ];
                }
            }

            // Blog post translations
            if (config('multilang.phases.blog_translations')) {
                $blogTranslations = BlogPostTranslation::where('locale', $locale)
                    ->whereHas('blogPost', fn ($q) => $q->where('is_published', true))
                    ->with('blogPost:id,updated_at')
                    ->get();

                foreach ($blogTranslations as $translation) {
                    $urls[] = [
                        'loc' => route('localized.blog.show', [
                            'locale' => $locale,
                            'slug' => $translation->slug,
                        ]),
                        'lastmod' => ($translation->blogPost?->updated_at ?? $translation->updated_at)->toIso8601String(),
                        'changefreq' => 'weekly',
                        'priority' => '0.6',
                    ];
                }
            }

            return $urls;
        });
    }

    /**
     * Generate sitemap index (list of locale-specific sitemaps).
     *
     * @return array
     */
    public function generateSitemapIndex(): array
    {
        $sitemaps = [];
        $supportedLocales = config('multilang.locales', ['en', 'ru', 'fr']);

        foreach ($supportedLocales as $locale) {
            $sitemaps[] = [
                'loc' => url("/sitemap-{$locale}.xml"),
                'lastmod' => now()->toIso8601String(),
            ];
        }

        return $sitemaps;
    }

    /**
     * Clear all sitemap caches.
     *
     * @return void
     */
    public function clearSitemapCache(): void
    {
        $supportedLocales = config('multilang.locales', ['en', 'ru', 'fr']);

        foreach ($supportedLocales as $locale) {
            Cache::forget("sitemap_{$locale}");
        }
    }
}
