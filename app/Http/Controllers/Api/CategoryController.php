<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TourCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class CategoryController extends Controller
{
    /**
     * Get category data by slug
     * Returns JSON with category name, description, tour count (translated for current locale)
     *
     * GET /api/categories/{slug}
     */
    public function show(string $slug): JsonResponse
    {
        // Cache for 1 hour
        $cacheKey = "api.category.{$slug}." . app()->getLocale();

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
