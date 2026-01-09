<?php

namespace App\Services;

use App\Models\Tour;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class TourCacheService
{
    /**
     * Cache duration in seconds
     */
    private const CACHE_TTL_SHORT = 1800;  // 30 minutes
    private const CACHE_TTL_MEDIUM = 3600; // 1 hour
    private const CACHE_TTL_LONG = 7200;   // 2 hours

    /**
     * Get featured tours for homepage with caching
     *
     * @param int $limit
     * @return Collection
     */
    public function getFeaturedTours(int $limit = 6): Collection
    {
        return Cache::remember(
            "tours.featured.{$limit}",
            self::CACHE_TTL_MEDIUM,
            fn() => Tour::active()
                ->withReviews()
                ->withFrontendRelations()
                ->popular()
                ->take($limit)
                ->get()
        );
    }

    /**
     * Get tours by city with caching
     *
     * @param int|string $cityId
     * @param int $limit
     * @return Collection
     */
    public function getToursByCity($cityId, int $limit = 10): Collection
    {
        $cacheKey = is_numeric($cityId)
            ? "tours.city.{$cityId}.{$limit}"
            : "tours.city.slug.{$cityId}.{$limit}";

        return Cache::remember(
            $cacheKey,
            self::CACHE_TTL_SHORT,
            fn() => Tour::active()
                ->byCity($cityId)
                ->withFrontendRelations()
                ->popular()
                ->take($limit)
                ->get()
        );
    }

    /**
     * Get tours by category with caching
     *
     * @param int|string $categoryId
     * @param int $limit
     * @return Collection
     */
    public function getToursByCategory($categoryId, int $limit = 10): Collection
    {
        $cacheKey = is_numeric($categoryId)
            ? "tours.category.{$categoryId}.{$limit}"
            : "tours.category.slug.{$categoryId}.{$limit}";

        return Cache::remember(
            $cacheKey,
            self::CACHE_TTL_SHORT,
            fn() => Tour::active()
                ->byCategory($categoryId)
                ->withFrontendRelations()
                ->popular()
                ->take($limit)
                ->get()
        );
    }

    /**
     * Get single tour with details by slug (with caching)
     *
     * @param string $slug
     * @return Tour|null
     */
    public function getTourBySlug(string $slug): ?Tour
    {
        return Cache::remember(
            "tour.detail.{$slug}",
            self::CACHE_TTL_MEDIUM,
            fn() => Tour::active()
                ->where('slug', $slug)
                ->withDetailRelations()
                ->first()
        );
    }

    /**
     * Get popular tours globally
     *
     * @param int $limit
     * @return Collection
     */
    public function getPopularTours(int $limit = 10): Collection
    {
        return Cache::remember(
            "tours.popular.{$limit}",
            self::CACHE_TTL_LONG,
            fn() => Tour::active()
                ->withReviews()
                ->withFrontendRelations()
                ->popular()
                ->take($limit)
                ->get()
        );
    }

    /**
     * Get recently added tours
     *
     * @param int $limit
     * @return Collection
     */
    public function getRecentTours(int $limit = 10): Collection
    {
        return Cache::remember(
            "tours.recent.{$limit}",
            self::CACHE_TTL_SHORT,
            fn() => Tour::active()
                ->withFrontendRelations()
                ->recent()
                ->take($limit)
                ->get()
        );
    }

    /**
     * Clear all tour-related caches
     *
     * @return void
     */
    public function clearAllTourCaches(): void
    {
        $patterns = [
            'tours.featured.*',
            'tours.city.*',
            'tours.category.*',
            'tour.detail.*',
            'tours.popular.*',
            'tours.recent.*',
        ];

        // For file/database cache drivers, clear known keys
        foreach ($patterns as $pattern) {
            $this->clearCachePattern($pattern);
        }
    }

    /**
     * Clear cache for a specific tour
     *
     * @param Tour $tour
     * @return void
     */
    public function clearTourCache(Tour $tour): void
    {
        // Clear featured tours
        for ($i = 1; $i <= 20; $i++) {
            Cache::forget("tours.featured.{$i}");
        }

        // Clear tour detail
        Cache::forget("tour.detail.{$tour->slug}");

        // Clear tour partials cache for all locales
        $locales = config("multilang.locales", ["en"]);
        foreach ($locales as $locale) {
            Cache::forget("tour.{$tour->slug}.{$locale}.with_translation");
        }

        // Clear city tours
        if ($tour->city_id) {
            for ($i = 1; $i <= 20; $i++) {
                Cache::forget("tours.city.{$tour->city_id}.{$i}");
            }
        }

        // Clear category tours
        foreach ($tour->categories as $category) {
            for ($i = 1; $i <= 20; $i++) {
                Cache::forget("tours.category.{$category->id}.{$i}");
                Cache::forget("tours.category.slug.{$category->slug}.{$i}");
            }
        }

        // Clear popular and recent
        for ($i = 1; $i <= 20; $i++) {
            Cache::forget("tours.popular.{$i}");
            Cache::forget("tours.recent.{$i}");
        }
    }

    /**
     * Clear cache by pattern (for non-taggable cache drivers)
     *
     * @param string $pattern
     * @return void
     */
    private function clearCachePattern(string $pattern): void
    {
        // Extract base pattern without wildcard
        $basePattern = str_replace('.*', '', $pattern);

        // Clear common variations (1-20 is reasonable for limits)
        for ($i = 1; $i <= 20; $i++) {
            Cache::forget("{$basePattern}.{$i}");
        }
    }

    /**
     * Warm up frequently accessed caches
     * Run this command via scheduler or manually after major updates
     *
     * @return void
     */
    public function warmUpCache(): void
    {
        // Warm up featured tours (common sizes)
        foreach ([3, 6, 9, 12] as $limit) {
            $this->getFeaturedTours($limit);
        }

        // Warm up popular tours
        foreach ([5, 10, 15] as $limit) {
            $this->getPopularTours($limit);
        }

        // Warm up recent tours
        foreach ([5, 10] as $limit) {
            $this->getRecentTours($limit);
        }
    }
}
