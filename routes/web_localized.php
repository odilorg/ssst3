<?php

/**
 * Localized Web Routes
 *
 * These routes mirror the main web routes but with a locale prefix.
 * They are only loaded when multilang is enabled and phases.routes is true.
 *
 * URL Pattern: /{locale}/...
 * Examples:
 *   /en/          -> localized homepage
 *   /ru/tours     -> Russian tours listing
 *   /fr/blog      -> French blog listing
 *
 * IMPORTANT:
 * - These routes run IN PARALLEL with existing routes
 * - Original routes (without locale prefix) continue to work
 * - Do NOT duplicate controller logic - use same controllers
 */

use App\Http\Middleware\SetLocaleFromRoute;
use Illuminate\Support\Facades\Route;

Route::prefix('{locale}')
    ->whereIn('locale', config('multilang.locales', ['en', 'ru', 'fr']))
    ->middleware(['web', SetLocaleFromRoute::class])
    ->group(function () {

        // ============================================
        // LOCALIZED HOMEPAGE
        // ============================================
        Route::get('/', [\App\Http\Controllers\HomeController::class, 'index'])
            ->name('localized.home');

        // ============================================
        // LOCALIZED TOUR LISTINGS
        // ============================================

        // Mini Journeys - 1-2 day experiences
        Route::get('/mini-journeys', [\App\Http\Controllers\TourListingController::class, 'miniJourneys'])
            ->name('localized.mini-journeys.index');

        // Craft Journeys - 3+ day multi-day tours
        Route::get('/craft-journeys', [\App\Http\Controllers\TourListingController::class, 'craftJourneys'])
            ->name('localized.craft-journeys.index');

        // Tour detail page - uses LocalizedTourController when tour_translations phase enabled
        if (config('multilang.phases.tour_translations')) {
            Route::get('/tours/{slug}', [\App\Http\Controllers\LocalizedTourController::class, 'show'])
                ->name('localized.tours.show');
        } else {
            Route::get('/tours/{slug}', [\App\Http\Controllers\TourDetailController::class, 'show'])
                ->name('localized.tours.show');
        }

        // Category landing page
        Route::get('/tours/category/{slug}', [\App\Http\Controllers\CategoryLandingController::class, 'show'])
            ->name('localized.tours.category');

        // ============================================
        // LOCALIZED BLOG
        // ============================================
        Route::get('/blog', [\App\Http\Controllers\BlogController::class, 'index'])
            ->name('localized.blog.index');

        Route::get('/blog/tag/{slug}', [\App\Http\Controllers\BlogController::class, 'tagPage'])
            ->name('localized.blog.tag')
            ->where('slug', '[a-z0-9-]+');

        // Blog detail page - uses LocalizedBlogController when blog_translations phase enabled
        if (config('multilang.phases.blog_translations')) {
            Route::get('/blog/{slug}', [\App\Http\Controllers\LocalizedBlogController::class, 'show'])
                ->name('localized.blog.show')
                ->where('slug', '[a-z0-9-]+');
        } else {
            Route::get('/blog/{slug}', [\App\Http\Controllers\BlogController::class, 'show'])
                ->name('localized.blog.show')
                ->where('slug', '[a-z0-9-]+');
        }

        // ============================================
        // LOCALIZED DESTINATIONS
        // ============================================
        Route::get('/destinations', [\App\Http\Controllers\DestinationController::class, 'index'])
            ->name('localized.destinations.index');

        // City detail page - uses LocalizedCityController when city_translations phase enabled
        if (config('multilang.phases.city_translations')) {
            Route::get('/destinations/{slug}', [\App\Http\Controllers\LocalizedCityController::class, 'show'])
                ->name('localized.city.show');
        } else {
            Route::get('/destinations/{slug}', [\App\Http\Controllers\DestinationController::class, 'show'])
                ->name('localized.city.show');
        }

        // ============================================
        // LOCALIZED STATIC PAGES
        // ============================================
        Route::get('/about', function () {
            return view('pages.about');
        })->name('localized.about');

        Route::get('/contact', function () {
            return view('pages.contact');
        })->name('localized.contact');

        Route::get('/privacy', function () {
            return view('pages.privacy');
        })->name('localized.privacy');

        Route::get('/terms', function () {
            return view('pages.terms');
        })->name('localized.terms');

        Route::get('/cookies', function () {
            return view('pages.cookies');
        })->name('localized.cookies');
    });
