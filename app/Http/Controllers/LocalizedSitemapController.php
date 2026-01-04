<?php

namespace App\Http\Controllers;

use App\Services\MultilangSeoService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

/**
 * Localized Sitemap Controller
 *
 * Generates sitemap index and locale-specific sitemaps for multilingual SEO.
 *
 * Routes:
 *   GET /sitemap.xml          -> Sitemap index (links to locale sitemaps)
 *   GET /sitemap-{locale}.xml -> Locale-specific sitemap
 *
 * Caching: 12 hours per sitemap
 */
class LocalizedSitemapController extends Controller
{
    protected MultilangSeoService $seoService;

    protected const CACHE_TTL = 43200; // 12 hours in seconds

    public function __construct(MultilangSeoService $seoService)
    {
        $this->seoService = $seoService;
    }

    /**
     * Generate sitemap index.
     *
     * When SEO phase is enabled, returns a sitemap index linking to locale-specific sitemaps.
     * Otherwise, falls back to the standard single sitemap.
     */
    public function index(): Response
    {
        // If SEO phase not enabled, use legacy sitemap behavior
        if (!config('multilang.phases.seo')) {
            return app(SitemapController::class)->index();
        }

        $xml = Cache::remember('sitemap_index', self::CACHE_TTL, function () {
            return $this->generateSitemapIndex();
        });

        return response($xml, 200)->header('Content-Type', 'application/xml');
    }

    /**
     * Generate locale-specific sitemap.
     *
     * @param string $locale The locale code (e.g., 'en', 'ru', 'fr')
     */
    public function locale(string $locale): Response
    {
        // Validate locale
        $supportedLocales = config('multilang.locales', ['en', 'ru', 'fr']);
        if (!in_array($locale, $supportedLocales)) {
            abort(404, 'Invalid locale');
        }

        // If SEO phase not enabled, return 404
        if (!config('multilang.phases.seo')) {
            abort(404, 'Localized sitemaps not enabled');
        }

        $cacheKey = "sitemap_{$locale}";
        $xml = Cache::remember($cacheKey, self::CACHE_TTL, function () use ($locale) {
            return $this->generateLocaleSitemap($locale);
        });

        return response($xml, 200)->header('Content-Type', 'application/xml');
    }

    /**
     * Generate sitemap index XML.
     */
    private function generateSitemapIndex(): string
    {
        $localesWithContent = $this->seoService->getLocalesWithContent();

        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        // Add main sitemap (non-localized content)
        $xml .= '<sitemap>';
        $xml .= '<loc>' . url('/sitemap-main.xml') . '</loc>';
        $xml .= '<lastmod>' . now()->toAtomString() . '</lastmod>';
        $xml .= '</sitemap>';

        // Add locale-specific sitemaps
        foreach ($localesWithContent as $locale) {
            $xml .= '<sitemap>';
            $xml .= '<loc>' . url("/sitemap-{$locale}.xml") . '</loc>';
            $xml .= '<lastmod>' . now()->toAtomString() . '</lastmod>';
            $xml .= '</sitemap>';
        }

        $xml .= '</sitemapindex>';

        return $xml;
    }

    /**
     * Generate locale-specific sitemap XML.
     *
     * @param string $locale The locale code
     */
    private function generateLocaleSitemap(string $locale): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"';
        $xml .= ' xmlns:xhtml="http://www.w3.org/1999/xhtml">';

        // Add static pages for this locale
        $xml .= $this->addUrl(url("/{$locale}"), now(), 'daily', '1.0');
        $xml .= $this->addUrl(url("/{$locale}/about"), now()->subDays(30), 'monthly', '0.8');
        $xml .= $this->addUrl(url("/{$locale}/contact"), now()->subDays(30), 'monthly', '0.7');
        $xml .= $this->addUrl(url("/{$locale}/blog"), now(), 'daily', '0.8');
        $xml .= $this->addUrl(url("/{$locale}/destinations"), now(), 'weekly', '0.8');
        $xml .= $this->addUrl(url("/{$locale}/mini-journeys"), now(), 'daily', '0.9');
        $xml .= $this->addUrl(url("/{$locale}/craft-journeys"), now(), 'daily', '0.9');

        // Add localized tours (only if tour_translations phase enabled)
        if (config('multilang.phases.tour_translations')) {
            $tours = $this->seoService->getToursForLocale($locale);
            foreach ($tours as $tour) {
                $xml .= $this->addUrl($tour['url'], $tour['lastmod'], 'weekly', '0.8');
            }
        }

        // Add localized cities (only if city_translations phase enabled)
        if (config('multilang.phases.city_translations')) {
            $cities = $this->seoService->getCitiesForLocale($locale);
            foreach ($cities as $city) {
                $xml .= $this->addUrl($city['url'], $city['lastmod'], 'weekly', '0.7');
            }
        }

        // Add localized blog posts (only if blog_translations phase enabled)
        if (config('multilang.phases.blog_translations')) {
            $posts = $this->seoService->getBlogPostsForLocale($locale);
            foreach ($posts as $post) {
                $xml .= $this->addUrl($post['url'], $post['lastmod'], 'monthly', '0.6');
            }
        }

        $xml .= '</urlset>';

        return $xml;
    }

    /**
     * Generate main sitemap XML (non-localized content).
     *
     * This preserves the original sitemap behavior for old URLs.
     */
    public function main(): Response
    {
        $xml = Cache::remember('sitemap_main', self::CACHE_TTL, function () {
            return app(SitemapController::class)->generateNonLocalizedSitemap();
        });

        return response($xml, 200)->header('Content-Type', 'application/xml');
    }

    /**
     * Add a URL entry to sitemap.
     */
    private function addUrl(
        string $loc,
        $lastmod,
        string $changefreq = 'weekly',
        string $priority = '0.5'
    ): string {
        $xml = '<url>';
        $xml .= '<loc>' . htmlspecialchars($loc) . '</loc>';

        if ($lastmod) {
            $xml .= '<lastmod>' . $lastmod->toAtomString() . '</lastmod>';
        }

        $xml .= '<changefreq>' . $changefreq . '</changefreq>';
        $xml .= '<priority>' . $priority . '</priority>';
        $xml .= '</url>';

        return $xml;
    }

    /**
     * Clear all sitemap caches.
     *
     * Call this after content updates.
     */
    public static function clearCache(): void
    {
        Cache::forget('sitemap_index');
        Cache::forget('sitemap_main');

        $locales = config('multilang.locales', ['en', 'ru', 'fr']);
        foreach ($locales as $locale) {
            Cache::forget("sitemap_{$locale}");
        }

        // Also clear legacy sitemap cache
        Cache::forget('sitemap_xml');
    }
}
