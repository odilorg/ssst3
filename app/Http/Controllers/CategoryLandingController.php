<?php

namespace App\Http\Controllers;

use App\Models\Tour;
use App\Models\TourCategory;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryLandingController extends Controller
{
    /**
     * Display category landing page
     *
     * Loads initial tours server-side for SEO crawlability,
     * while keeping HTMX for filter interactions.
     *
     * @param string $slug
     * @return View
     */
    public function show(string $slug): View
    {
        $slug = request()->route('slug', $slug);

        // Find category or 404
        $category = TourCategory::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        // Prepare SEO-friendly data
        $locale = app()->getLocale();

        $pageTitle = $category->meta_title[$locale] ?? null;
        if (!$pageTitle) {
            $categoryName = $category->name[$locale] ?? $category->name['en'] ?? 'Category';
            $pageTitle = $categoryName . ' Tours in Uzbekistan | Jahongir Travel';
        }

        $metaDescription = $category->meta_description[$locale] ?? $category->description[$locale] ?? '';
        $metaDescription = substr($metaDescription, 0, 160); // Limit to 160 chars

        $ogImage = $category->hero_image
            ? asset('storage/' . $category->hero_image)
            : asset('images/default-category.jpg');

        $canonicalUrl = url('/tours/category/' . $category->slug);

        // SSR: Load initial tours for this category (crawlable by search engines)
        $initialTours = Tour::with(['city'])
            ->where('is_active', true)
            ->whereHas('categories', function ($q) use ($slug) {
                $q->where('slug', $slug)->where('is_active', true);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        // SSR: Load related categories
        $relatedCategories = TourCategory::active()
            ->where('slug', '!=', $category->slug)
            ->orderBy('display_order')
            ->get();

        return view('pages.category-landing', compact(
            'category',
            'pageTitle',
            'metaDescription',
            'ogImage',
            'canonicalUrl',
            'locale',
            'initialTours',
            'relatedCategories'
        ));
    }
}
