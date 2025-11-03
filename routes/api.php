<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CategoryController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Category API
Route::get('/categories/{slug}', [CategoryController::class, 'show'])
    ->name('api.categories.show');
