<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CategoryController;
use App\Models\Tour;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Category API
Route::get('/categories/{slug}', [CategoryController::class, 'show'])
    ->name('api.categories.show');

// Tour API - Get tour ID from slug
Route::get('/tours/{slug}', function ($slug) {
    $tour = Tour::where('slug', $slug)->firstOrFail();
    return response()->json([
        'id' => $tour->id,
        'slug' => $tour->slug,
        'title' => $tour->title
    ]);
})->name('api.tours.show');

// Get CSRF token
Route::get('/csrf-token', function () {
    return response()->json([
        'token' => csrf_token()
    ]);
});

// Cities API - Get all active cities
Route::get('/cities', function () {
    $cities = \App\Models\City::active()
        ->orderBy('display_order')
        ->get()
        ->map(function ($city) {
            return [
                'id' => $city->id,
                'slug' => $city->slug,
                'name' => $city->name,
                'tagline' => $city->tagline,
                'description' => $city->description,
                'short_description' => $city->short_description,
                'featured_image' => $city->featured_image_url,
                'hero_image' => $city->hero_image_url,
                'tour_count' => $city->tour_count_cache ?? $city->tours()->where('is_active', true)->count(),
                'latitude' => $city->latitude,
                'longitude' => $city->longitude,
            ];
        });

    return response()->json($cities);
})->name('api.cities.index');

// Tours API - Get all active tours
Route::get('/tours', function () {
    $tours = \App\Models\Tour::where('is_active', true)
        ->with(['city'])
        ->orderBy('created_at', 'desc')
        ->get()
        ->map(function ($tour) {
            return [
                'id' => $tour->id,
                'slug' => $tour->slug,
                'title' => $tour->title,
                'description' => $tour->long_description,
                'short_description' => $tour->short_description,
                'featured_image' => $tour->hero_image ? asset('storage/' . $tour->hero_image) : null,
                'price_per_person' => $tour->price_per_person,
                'duration' => $tour->duration_days,
                'city_name' => $tour->city ? $tour->city->name : null,
                'city_slug' => $tour->city ? $tour->city->slug : null,
            ];
        });

    return response()->json($tours);
})->name('api.tours.index');

// Categories API - Get all active categories
Route::get('/categories', function () {
    $categories = \App\Models\TourCategory::active()
        ->orderBy('display_order')
        ->get()
        ->map(function ($category) {
            return [
                'id' => $category->id,
                'slug' => $category->slug,
                'name' => $category->translated_name,
                'icon' => $category->icon,
                'tour_count' => $category->cached_tour_count ?? 0,
            ];
        });

    return response()->json($categories);
})->name('api.categories.index');
