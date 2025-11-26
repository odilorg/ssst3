<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\City;
use App\Models\Tour;
use App\Models\TourCategory;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

class SitemapController extends Controller
{
    /**
     * Generate and return XML sitemap
     */
    public function index(): Response
    {
        // Cache sitemap for 24 hours
        $xml = Cache::remember('sitemap_xml', 86400, function () {
            return $this->generateSitemap();
        });

        return response($xml, 200)->header('Content-Type', 'application/xml');
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
     * Add a URL entry to sitemap
     */
    private function addUrl(
        string $loc,
        $lastmod,
        string $changefreq = 'weekly',
        string $priority = '0.5'
    ): string {
        $xml = '<url>';
        $xml .= '<loc>' . htmlspecialchars($loc) . '</loc>';
        $xml .= '<lastmod>' . $lastmod->toAtomString() . '</lastmod>';
        $xml .= '<changefreq>' . $changefreq . '</changefreq>';
        $xml .= '<priority>' . $priority . '</priority>';
        $xml .= '</url>';

        return $xml;
    }
}
