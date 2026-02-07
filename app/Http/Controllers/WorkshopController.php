<?php

namespace App\Http\Controllers;

use App\Models\Workshop;
use Illuminate\View\View;

class WorkshopController extends Controller
{
    /**
     * Display workshop listing page
     */
    public function index(): View
    {
        $workshops = Workshop::active()
            ->with('city')
            ->orderBy('sort_order')
            ->orderBy('title')
            ->get();

        $featuredWorkshops = $workshops->where('is_featured', true);

        return view('pages.workshops', compact(
            'workshops',
            'featuredWorkshops'
        ));
    }

    /**
     * Display workshop detail page
     */
    public function show(string $slug): View
    {
        $workshop = Workshop::where('slug', $slug)
            ->with(['city'])
            ->active()
            ->firstOrFail();

        // Prepare SEO data
        $pageTitle = $workshop->seo_title ?? $workshop->title . ' | Artisan Workshop Experience';
        $metaDescription = $workshop->seo_description ?? $workshop->short_description;
        $ogImage = $workshop->og_image ?? $workshop->hero_image_url ?? asset('images/og-default.jpg');
        $canonicalUrl = url('/workshops/' . $workshop->slug);

        // Get related tours
        $relatedTours = $workshop->relatedTours();

        // Generate structured data for SEO
        $structuredData = json_encode(
            $this->generateSchemaData($workshop),
            JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT
        );

        return view('pages.workshop-details', compact(
            'workshop',
            'pageTitle',
            'metaDescription',
            'ogImage',
            'canonicalUrl',
            'relatedTours',
            'structuredData'
        ));
    }

    /**
     * Generate Schema.org structured data for SEO
     */
    private function generateSchemaData(Workshop $workshop): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'TouristAttraction',
            'name' => $workshop->title,
            'description' => $workshop->short_description,
            'image' => $workshop->hero_image_url,
            'address' => [
                '@type' => 'PostalAddress',
                'addressLocality' => $workshop->city?->name,
                'addressCountry' => 'UZ',
                'streetAddress' => $workshop->address,
            ],
            'geo' => $workshop->latitude ? [
                '@type' => 'GeoCoordinates',
                'latitude' => $workshop->latitude,
                'longitude' => $workshop->longitude,
            ] : null,
            'aggregateRating' => $workshop->rating ? [
                '@type' => 'AggregateRating',
                'ratingValue' => $workshop->rating,
                'reviewCount' => $workshop->review_count,
            ] : null,
            'offers' => [
                '@type' => 'Offer',
                'price' => $workshop->price_per_person,
                'priceCurrency' => $workshop->currency ?? 'USD',
            ],
        ];
    }
}
