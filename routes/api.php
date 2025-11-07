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
