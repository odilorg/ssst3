<?php

namespace App\Http\Controllers\Partials;

use App\Http\Controllers\Controller;
use App\Models\TourCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CategoryController extends Controller
{
    /**
     * Homepage categories partial
     * Returns: Grid of category cards for homepage "Trending Activities" section
     *
     * Usage:
     * - GET /partials/categories/homepage
     */
    public function homepage()
    {
        $categories = TourCategory::getHomepageCategories();

        return view('partials.categories.homepage-cards', compact('categories'));
    }

    /**
     * Related categories partial
     * Returns: Grid of all other category cards (excludes current category)
     *
     * Query params:
     * - current: Current category slug to exclude
     *
     * Usage:
     * - GET /partials/categories/related?current=cultural-historical
     */
    public function related(Request $request)
    {
        $currentSlug = $request->get('current');

        // Cache key (removed limit from cache key)
        $cacheKey = "all_categories_except.{$currentSlug}";

        $categories = Cache::remember($cacheKey, now()->addHours(6), function () use ($currentSlug) {
            return TourCategory::active()
                ->where('slug', '!=', $currentSlug)
                ->orderBy('display_order')
                ->get();
        });

        return view('partials.categories.related-cards', compact('categories'));
    }

    /**
     * Get category data as JSON
     * Returns: Category information for JavaScript consumption
     *
     * Usage:
     * - GET /partials/categories/{slug}/data
     */
    public function data(string $slug)
    {
        $cacheKey = "category_data.{$slug}." . app()->getLocale();

        $data = Cache::remember($cacheKey, now()->addHour(), function () use ($slug) {
            $category = TourCategory::where('slug', $slug)
                ->where('is_active', true)
                ->firstOrFail();

            $locale = app()->getLocale();

            return [
                'slug' => $category->slug,
                'name' => $category->name[$locale] ?? $category->name['en'] ?? 'Untitled',
                'description' => $category->description[$locale] ?? $category->description['en'] ?? '',
                'icon' => $category->icon,
                'image_path' => $category->image_path
                    ? asset('storage/' . $category->image_path)
                    : null,
                'hero_image' => $category->hero_image
                    ? asset('storage/' . $category->hero_image)
                    : null,
                'tour_count' => $category->cached_tour_count,
                'display_order' => $category->display_order,
            ];
        });

        return response()->json($data);
    }
}
