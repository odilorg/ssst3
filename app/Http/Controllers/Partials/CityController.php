<?php

namespace App\Http\Controllers\Partials;

use App\Http\Controllers\Controller;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CityController extends Controller
{
    /**
     * Related cities partial
     * Returns: Grid of related city cards (excludes current city)
     *
     * Query params:
     * - current: Current city ID to exclude
     * - limit: Number of cities to show (default: 5)
     *
     * Usage:
     * - GET /partials/cities/related?current=1&limit=5
     */
    public function related(Request $request)
    {
        $currentId = $request->get('current');
        $limit = $request->get('limit', 5);

        // Validate limit
        $limit = min(max($limit, 1), 10);

        // Cache key
        $cacheKey = "related_cities.{$currentId}.{$limit}";

        $cities = Cache::remember($cacheKey, now()->addHours(6), function () use ($currentId, $limit) {
            return City::active()
                ->where('id', '!=', $currentId)
                ->orderBy('display_order')
                ->take($limit)
                ->get();
        });

        return view('partials.cities.related-cards', compact('cities'));
    }

    /**
     * Get city data as JSON
     * Returns: City information for JavaScript consumption
     *
     * Usage:
     * - GET /partials/cities/{slug}/data
     */
    public function data(string $slug)
    {
        $cacheKey = "city_data.{$slug}";

        $data = Cache::remember($cacheKey, now()->addHour(), function () use ($slug) {
            $city = City::where('slug', $slug)
                ->where('is_active', true)
                ->firstOrFail();

            return [
                'id' => $city->id,
                'slug' => $city->slug,
                'name' => $city->name,
                'tagline' => $city->tagline,
                'description' => $city->long_description ?? $city->description ?? '',
                'short_description' => $city->short_description ?? '',
                'featured_image' => $city->featured_image_url,
                'hero_image' => $city->hero_image_url,
                'tour_count' => $city->tour_count_cache ?? $city->tours()->where('is_active', true)->count(),
                'display_order' => $city->display_order,
                'latitude' => $city->latitude,
                'longitude' => $city->longitude,
            ];
        });

        return response()->json($data);
    }
}
