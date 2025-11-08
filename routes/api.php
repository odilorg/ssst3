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
