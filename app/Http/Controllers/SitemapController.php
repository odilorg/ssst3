<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\BlogPostTranslation;
use App\Models\City;
use App\Models\CityTranslation;
use App\Models\Tour;
use App\Models\TourCategory;
use App\Models\TourTranslation;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

class SitemapController extends Controller
{
    /**
     * Generate and return XML sitemap.
     *
     * When SEO phase is enabled, returns a sitemap index pointing to
     * locale-specific sitemaps. Otherwise, returns the original sitemap.
     */
    public function index(): Response
    {
        // If multilang SEO is enabled, return sitemap index
        if (config('multilang.enabled') && config('multilang.phases.seo')) {
            return $this->sitemapIndex();
        }

        // Original behavior: single sitemap
        $xml = Cache::remember('sitemap_xml', 86400, function () {
            return $this->generateSitemap();
        });

        return response($xml, 200)->header('Content-Type', 'application/xml');
    }

    /**
     * Sitemap index - lists all locale-specific sitemaps.
     *
     * URL: /sitemap.xml (when SEO phase enabled)
     */
    protected function sitemapIndex(): Response
    {
        $supportedLocales = config('multilang.locales', ['en', 'ru', 'fr']);

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        foreach ($supportedLocales as $locale) {
            $xml .= '  <sitemap>' . "\n";
            $xml .= '    <loc>' . htmlspecialchars(url("/sitemap-{$locale}.xml")) . '</loc>' . "\n";
            $xml .= '    <lastmod>' . now()->toIso8601String() . '</lastmod>' . "\n";
            $xml .= '  </sitemap>' . "\n";
        }

        $xml .= '</sitemapindex>';

        return response($xml, 200)->header('Content-Type', 'application/xml');
    }

    /**
     * Locale-specific sitemap.
     *
     * URL: /sitemap-{locale}.xml
     */
    public function locale(string $locale): Response
    {
        $supportedLocales = config('multilang.locales', ['en', 'ru', 'fr']);

        if (!in_array($locale, $supportedLocales)) {
            abort(404);
        }

        // Cache for 12 hours
        $xml = Cache::remember("sitemap_{$locale}", 43200, function () use ($locale) {
            return $this->generateLocaleSitemap($locale);
        });

        return response($xml, 200)->header('Content-Type', 'application/xml');
    }

    /**
     * Generate locale-specific sitemap XML.
     */
    protected function generateLocaleSitemap(string $locale): string
    {
        $supportedLocales = config('multilang.locales', ['en', 'ru', 'fr']);

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">' . "\n";

        // Static pages
        $staticPages = [
            ['route' => 'localized.home', 'priority' => '1.0', 'changefreq' => 'daily'],
            ['route' => 'localized.about', 'priority' => '0.7', 'changefreq' => 'monthly'],
            ['route' => 'localized.contact', 'priority' => '0.7', 'changefreq' => 'monthly'],
            ['route' => 'localized.mini-journeys.index', 'priority' => '0.9', 'changefreq' => 'weekly'],
            ['route' => 'localized.craft-journeys.index', 'priority' => '0.9', 'changefreq' => 'weekly'],
            ['route' => 'localized.destinations.index', 'priority' => '0.8', 'changefreq' => 'weekly'],
            ['route' => 'localized.blog.index', 'priority' => '0.8', 'changefreq' => 'daily'],
        ];

        foreach ($staticPages as $page) {
            try {
                $url = route($page['route'], ['locale' => $locale]);
                // Build alternates for all locales
                $alternates = [];
                foreach ($supportedLocales as $altLocale) {
                    try {
                        $alternates[$altLocale] = route($page['route'], ['locale' => $altLocale]);
                    } catch (\Exception $e) {
                        // Skip if route doesn't exist for this locale
                    }
                }
                $xml .= $this->addUrl($url, now(), $page['changefreq'], $page['priority'], $alternates);
            } catch (\Exception $e) {
                // Route may not exist, skip
            }
        }

        // Tour translations (if phase enabled)
        if (config('multilang.phases.tour_translations')) {
            TourTranslation::where('locale', $locale)
                ->whereHas('tour', fn ($q) => $q->where('is_active', true))
                ->with(['tour', 'tour.translations:id,tour_id,locale,slug'])
                ->chunk(100, function ($translations) use (&$xml, $locale, $supportedLocales) {
                    foreach ($translations as $translation) {
                        try {
                            $url = route('localized.tours.show', [
                                'locale' => $locale,
                                'slug' => $translation->slug,
                            ]);
                            $lastmod = $translation->tour?->updated_at ?? $translation->updated_at;

                            // Build alternates from sibling translations
                            $alternates = [];
                            if ($translation->tour) {
                                foreach ($translation->tour->translations as $sibling) {
                                    if ($sibling->slug && in_array($sibling->locale, $supportedLocales)) {
                                        try {
                                            $alternates[$sibling->locale] = route('localized.tours.show', [
                                                'locale' => $sibling->locale,
                                                'slug' => $sibling->slug,
                                            ]);
                                        } catch (\Exception $e) {
                                            // Skip
                                        }
                                    }
                                }
                            }

                            // Collect tour images for image sitemap
                            $images = $this->getTourImages($translation->tour, $translation->title ?? $translation->tour?->title);

                            $xml .= $this->addUrl($url, $lastmod, 'weekly', '0.8', $alternates, $images);
                        } catch (\Exception $e) {
                            // Skip invalid routes
                        }
                    }
                });
        }

        // City translations (if phase enabled)
        if (config('multilang.phases.city_translations')) {
            CityTranslation::where('locale', $locale)
                ->whereHas('city', fn ($q) => $q->where('is_active', true))
                ->with(['city:id,updated_at', 'city.translations:id,city_id,locale,slug'])
                ->chunk(100, function ($translations) use (&$xml, $locale, $supportedLocales) {
                    foreach ($translations as $translation) {
                        try {
                            $url = route('localized.city.show', [
                                'locale' => $locale,
                                'slug' => $translation->slug,
                            ]);
                            $lastmod = $translation->city?->updated_at ?? $translation->updated_at;

                            $alternates = [];
                            if ($translation->city) {
                                foreach ($translation->city->translations as $sibling) {
                                    if ($sibling->slug && in_array($sibling->locale, $supportedLocales)) {
                                        try {
                                            $alternates[$sibling->locale] = route('localized.city.show', [
                                                'locale' => $sibling->locale,
                                                'slug' => $sibling->slug,
                                            ]);
                                        } catch (\Exception $e) {
                                            // Skip
                                        }
                                    }
                                }
                            }

                            $xml .= $this->addUrl($url, $lastmod, 'weekly', '0.7', $alternates);
                        } catch (\Exception $e) {
                            // Skip invalid routes
                        }
                    }
                });
        }

        // Blog post translations (if phase enabled)
        if (config('multilang.phases.blog_translations')) {
            BlogPostTranslation::where('locale', $locale)
                ->whereHas('blogPost', fn ($q) => $q->where('is_published', true))
                ->with(['blogPost:id,updated_at', 'blogPost.translations:id,blog_post_id,locale,slug'])
                ->chunk(100, function ($translations) use (&$xml, $locale, $supportedLocales) {
                    foreach ($translations as $translation) {
                        try {
                            $url = route('localized.blog.show', [
                                'locale' => $locale,
                                'slug' => $translation->slug,
                            ]);
                            $lastmod = $translation->blogPost?->updated_at ?? $translation->updated_at;

                            $alternates = [];
                            if ($translation->blogPost) {
                                foreach ($translation->blogPost->translations as $sibling) {
                                    if ($sibling->slug && in_array($sibling->locale, $supportedLocales)) {
                                        try {
                                            $alternates[$sibling->locale] = route('localized.blog.show', [
                                                'locale' => $sibling->locale,
                                                'slug' => $sibling->slug,
                                            ]);
                                        } catch (\Exception $e) {
                                            // Skip
                                        }
                                    }
                                }
                            }

                            $xml .= $this->addUrl($url, $lastmod, 'weekly', '0.6', $alternates);
                        } catch (\Exception $e) {
                            // Skip invalid routes
                        }
                    }
                });
        }

        $xml .= '</urlset>';

        return $xml;
    }

    /**
     * Generate sitemap XML content
     */
    private function generateSitemap(): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        // Homepage
        $xml .= $this->addUrl(url('/'), now(), 'daily', '1.0');

        // Static pages
        $xml .= $this->addUrl(url('/about'), now()->subDays(30), 'monthly', '0.8');
        $xml .= $this->addUrl(url('/contact'), now()->subDays(30), 'monthly', '0.7');

        // Tours listing page
        $xml .= $this->addUrl(url('/tours'), now(), 'daily', '0.9');

        // Individual tours
        Tour::active()->chunk(100, function ($tours) use (&$xml) {
            foreach ($tours as $tour) {
                $xml .= $this->addUrl(
                    url('/tours/' . $tour->slug),
                    $tour->updated_at,
                    'weekly',
                    '0.8'
                );
            }
        });

        // Tour categories
        TourCategory::active()->chunk(100, function ($categories) use (&$xml) {
            foreach ($categories as $category) {
                $xml .= $this->addUrl(
                    url('/tours/category/' . $category->slug),
                    $category->updated_at ?? now(),
                    'weekly',
                    '0.7'
                );
            }
        });

        // Blog listing page
        $xml .= $this->addUrl(url('/blog'), now(), 'daily', '0.8');

        // Individual blog posts
        BlogPost::published()->chunk(100, function ($posts) use (&$xml) {
            foreach ($posts as $post) {
                $xml .= $this->addUrl(
                    url('/blog/' . $post->slug),
                    $post->updated_at,
                    'monthly',
                    '0.6'
                );
            }
        });

        // Destinations/Cities listing
        $xml .= $this->addUrl(url('/destinations'), now(), 'weekly', '0.8');

        // Individual cities
        City::active()->chunk(100, function ($cities) use (&$xml) {
            foreach ($cities as $city) {
                $xml .= $this->addUrl(
                    url('/destinations/' . $city->slug),
                    $city->updated_at ?? now(),
                    'weekly',
                    '0.7'
                );
            }
        });

        $xml .= '</urlset>';

        return $xml;
    }

    /**
     * Add a URL entry to sitemap with optional hreflang alternates.
     *
     * @param array<string, string> $alternates Locale => URL map for xhtml:link alternates
     */
    /**
     * @param array<string, string> $alternates Locale => URL map for xhtml:link alternates
     * @param array<int, array{loc: string, title?: string}> $images Image entries for image sitemap
     */
    private function addUrl(
        string $loc,
        $lastmod,
        string $changefreq = 'weekly',
        string $priority = '0.5',
        array $alternates = [],
        array $images = []
    ): string {
        $xml = '  <url>' . "\n";
        $xml .= '    <loc>' . htmlspecialchars($loc) . '</loc>' . "\n";
        $xml .= '    <lastmod>' . $lastmod->toAtomString() . '</lastmod>' . "\n";
        $xml .= '    <changefreq>' . $changefreq . '</changefreq>' . "\n";
        $xml .= '    <priority>' . $priority . '</priority>' . "\n";

        foreach ($alternates as $hreflang => $href) {
            $xml .= '    <xhtml:link rel="alternate" hreflang="' . htmlspecialchars($hreflang) . '" href="' . htmlspecialchars($href) . '"/>' . "\n";
        }

        foreach ($images as $image) {
            $xml .= '    <image:image>' . "\n";
            $xml .= '      <image:loc>' . htmlspecialchars($image['loc']) . '</image:loc>' . "\n";
            if (!empty($image['title'])) {
                $xml .= '      <image:title>' . htmlspecialchars($image['title']) . '</image:title>' . "\n";
            }
            $xml .= '    </image:image>' . "\n";
        }

        $xml .= '  </url>' . "\n";

        return $xml;
    }

    /**
     * Extract image URLs and titles from a Tour for image sitemap.
     */
    private function getTourImages(?Tour $tour, ?string $tourTitle): array
    {
        if (!$tour) {
            return [];
        }

        $images = [];
        $title = $tourTitle ?? $tour->title ?? '';

        // Hero/featured image
        $heroUrl = $tour->has_webp
            ? $tour->hero_image_webp_url
            : ($tour->featured_image_url ?? null);

        if ($heroUrl) {
            $images[] = [
                'loc' => str_starts_with($heroUrl, 'http') ? $heroUrl : url($heroUrl),
                'title' => $title,
            ];
        }

        // Gallery images
        $rawGallery = $tour->getRawOriginal('gallery_images');
        $gallery = is_string($rawGallery) ? json_decode($rawGallery, true) : $rawGallery;

        if (is_array($gallery)) {
            foreach ($gallery as $index => $image) {
                $path = is_array($image) ? ($image['path'] ?? null) : $image;
                if (!$path) continue;

                $imageUrl = str_starts_with($path, 'http') ? $path : url('storage/' . $path);
                $alt = is_array($image) && isset($image['alt']) ? $image['alt'] : $title . ' - Photo ' . ($index + 1);

                $images[] = [
                    'loc' => $imageUrl,
                    'title' => $alt,
                ];
            }
        }

        return $images;
    }
}
