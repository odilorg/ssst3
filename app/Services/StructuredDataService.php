<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;

class StructuredDataService
{
    /**
     * Generate breadcrumb structured data
     *
     * @param array $items Array of ['name' => 'Title', 'url' => 'https://...']
     * @return array
     */
    public function generateBreadcrumbs(array $items): array
    {
        $elements = [];

        foreach ($items as $index => $item) {
            $elements[] = [
                "@type" => "ListItem",
                "position" => $index + 1,
                "name" => $item['name'],
                "item" => $item['url']
            ];
        }

        return [
            "@type" => "BreadcrumbList",
            "itemListElement" => $elements
        ];
    }

    /**
     * Generate structured data for tour listing page
     *
     * @param \Illuminate\Pagination\LengthAwarePaginator $tours
     * @return array
     */
    public function generateTourListingSchema($tours): array
    {
        $schema = [
            "@context" => "https://schema.org",
            "@graph" => [
                $this->generateBreadcrumbs([
                    ['name' => 'Home', 'url' => url('/')],
                    ['name' => 'Tours', 'url' => url('/tours')]
                ])
            ]
        ];

        if ($tours->count() > 0) {
            $itemList = [
                "@type" => "ItemList",
                "name" => "Uzbekistan Tours",
                "description" => "Browse all available tours in Uzbekistan - cultural heritage, mountain adventures, and Silk Road journeys",
                "numberOfItems" => $tours->total(),
                "itemListElement" => []
            ];

            foreach ($tours->take(20) as $index => $tour) {
                $item = [
                    "@type" => "TouristTrip",
                    "position" => $index + 1,
                    "name" => $tour->title,
                    "description" => strip_tags($tour->short_description ?? \Illuminate\Support\Str::limit($tour->long_description, 200)),
                    "url" => url('/tours/' . $tour->slug)
                ];

                if ($tour->price_per_person) {
                    $item["offers"] = [
                        "@type" => "Offer",
                        "url" => url('/tours/' . $tour->slug),
                        "price" => (string)$tour->price_per_person,
                        "priceCurrency" => $tour->currency ?? "USD"
                    ];
                }

                $itemList["itemListElement"][] = $item;
            }

            $schema["@graph"][] = $itemList;
        }

        return $schema;
    }

    /**
     * Generate structured data for a single tour
     *
     * @param Model $tour
     * @return array
     */
    public function generateTourSchema(Model $tour): array
    {
        $schema = [
            "@context" => "https://schema.org",
            "@type" => "TouristTrip",
            "name" => $tour->title,
            "description" => strip_tags($tour->short_description ?? $tour->long_description ?? ''),
        ];

        if ($tour->hero_image) {
            $schema["image"] = $tour->featured_image_url ?? asset('storage/' . $tour->hero_image);
        }

        if ($tour->price_per_person) {
            $schema["offers"] = [
                "@type" => "Offer",
                "price" => (string)$tour->price_per_person,
                "priceCurrency" => $tour->currency ?? "USD",
                "availability" => "https://schema.org/InStock"
            ];
        }

        $schema["provider"] = [
            "@type" => "TravelAgency",
            "name" => "Jahongir Travel",
            "url" => url('/')
        ];

        if ($tour->rating && $tour->review_count > 0) {
            $schema["aggregateRating"] = [
                "@type" => "AggregateRating",
                "ratingValue" => (string)$tour->rating,
                "reviewCount" => (string)$tour->review_count,
                "bestRating" => "5",
                "worstRating" => "1"
            ];
        }

        return $schema;
    }

    /**
     * Generate structured data for blog post
     *
     * @param Model $post
     * @return array
     */
    public function generateBlogPostSchema(Model $post): array
    {
        return [
            "@context" => "https://schema.org",
            "@type" => "BlogPosting",
            "headline" => $post->title,
            "image" => $post->featured_image_url ?? asset('images/og-default.jpg'),
            "author" => [
                "@type" => "Person",
                "name" => $post->author_name ?? "Jahongir Travel Team"
            ],
            "publisher" => [
                "@type" => "Organization",
                "name" => "Jahongir Travel",
                "logo" => [
                    "@type" => "ImageObject",
                    "url" => asset('images/logo.png')
                ]
            ],
            "datePublished" => $post->published_at ? $post->published_at->toIso8601String() : '',
            "dateModified" => $post->updated_at ? $post->updated_at->toIso8601String() : '',
            "description" => $post->meta_description ?? $post->excerpt ?? \Illuminate\Support\Str::limit(strip_tags($post->content ?? ''), 160)
        ];
    }

    /**
     * Encode schema as JSON with proper formatting
     *
     * @param array $schema
     * @return string
     */
    public function encode(array $schema): string
    {
        return json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    }
}
