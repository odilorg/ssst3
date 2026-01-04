<?php

namespace App\View\Components;

use App\Models\BlogPost;
use App\Models\City;
use App\Models\Tour;
use App\Services\MultilangSeoService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\View\Component;

/**
 * HreflangTags Component
 *
 * Renders <link rel="alternate" hreflang="xx" href="..."> tags for multilingual SEO.
 *
 * Usage:
 *   <x-hreflang-tags :tour="$tour" />
 *   <x-hreflang-tags :city="$city" />
 *   <x-hreflang-tags :blog-post="$blogPost" />
 *   <x-hreflang-tags static-page="about" />
 */
class HreflangTags extends Component
{
    public Collection $links;

    public function __construct(
        ?Tour $tour = null,
        ?City $city = null,
        ?BlogPost $blogPost = null,
        ?string $staticPage = null
    ) {
        $seoService = app(MultilangSeoService::class);

        if ($tour) {
            $this->links = $seoService->getTourHreflangLinks($tour);
        } elseif ($city) {
            $this->links = $seoService->getCityHreflangLinks($city);
        } elseif ($blogPost) {
            $this->links = $seoService->getBlogHreflangLinks($blogPost);
        } elseif ($staticPage) {
            $this->links = $seoService->getStaticPageHreflangLinks($staticPage);
        } else {
            $this->links = collect();
        }
    }

    public function render(): View
    {
        return view('components.hreflang-tags');
    }

    /**
     * Determine if the component should be rendered.
     */
    public function shouldRender(): bool
    {
        return config('multilang.phases.seo') && $this->links->isNotEmpty();
    }
}
