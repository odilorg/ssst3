<?php

namespace App\Http\Controllers;

use App\Models\TourCategory;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryLandingController extends Controller
{
    /**
     * Display category landing page
     *
     * @param string $slug
     * @return View
     */
    public function show(string $slug): View
    {
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

        return view('pages.category-landing', compact(
            'category',
            'pageTitle',
            'metaDescription',
            'ogImage',
            'canonicalUrl',
            'locale'
        ));
    }
}
