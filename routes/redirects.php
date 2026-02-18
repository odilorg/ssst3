<?php

/**
 * SEO Redirect Rules: jahongir-travel.uz → staging.jahongir-travel.uz
 *
 * Migration from old static .php site + WordPress to new Laravel site.
 * All redirects are 301 (permanent) to pass ~90% link equity.
 *
 * Generated from Google Search Console data (2026-02-18):
 * - 120 indexed pages mapped
 * - 538 not-found pages caught by wildcards
 *
 * Order matters: specific routes BEFORE wildcards.
 */

use Illuminate\Support\Facades\Route;

// ============================================
// PRIORITY 1: Old Static .php Pages (Highest SEO Value)
// ============================================

// Tour pages (.php) → new tour slugs
Route::get('/tours-from-samarkand/daytrip-shahrisabz.php', fn () => redirect('/tours/shahrisabz-day-tour-guided', 301));
Route::get('/tajikistan-tours/seven-lakes-tajikistan-tour.php', fn () => redirect('/tours/seven-lakes-tajikistan-day-tour', 301));
Route::get('/tours-from-samarkand/nuratau-homestay-2-days.php', fn () => redirect('/tours/nuratau-mountains-homestay-2-days', 301));
Route::get('/tours-from-bukhara/bukhara-nurata-2d-1n.php', fn () => redirect('/tours/bukhara-nuratau-homestay-2-days', 301));
Route::get('/tours-from-samarkand/hiking-amankutan.php', fn () => redirect('/tours/aman-kutan-hiking-day-tour', 301));
Route::get('/tours-from-samarkand/yurt-camp-tour.php', fn () => redirect('/tours/samarkand-2-day-desert-yurt-camp-camel-ride', 301));

// Tour listing pages (.php)
Route::get('/tours-from-bukhara/index.php', fn () => redirect('/craft-journeys', 301));
Route::get('/tours-from-samarkand/index.php', fn () => redirect('/mini-journeys', 301));

// Blog pages (.php) → new blog slugs
Route::get('/blog/silk-road-history.php', fn () => redirect('/blog/how-the-silk-road-shaped-uzbekistans-history-culture', 301));
Route::get('/blog/hiking-nuratau-mountains.php', fn () => redirect('/blog/hiking-in-the-nuratau-mountains-a-complete-trekking-guide', 301));
Route::get('/blog/uzbek-cuisine-guide.php', fn () => redirect('/blog/uzbek-cuisine-guide-15-must-try-traditional-dishes', 301));
Route::get('/blog/yurt-camp-experience.php', fn () => redirect('/blog/desert-yurt-camp-in-uzbekistan-a-nomadic-adventure-guide', 301));
Route::get('/blog/bukhara-photography-spots.php', fn () => redirect('/blog/best-photography-spots-in-bukhara-a-photographers-guide', 301));
Route::get('/blog/samarkand-hidden-gems.php', fn () => redirect('/blog/samarkand-hidden-gems-10-secret-spots-off-the-beaten-path', 301));

// Static pages (.php)
Route::get('/contact.php', fn () => redirect('/contact', 301));
Route::get('/aboutus.php', fn () => redirect('/about', 301));

// ============================================
// PRIORITY 2: WordPress Tour Pages (with locale prefix)
// ============================================

// English tours - specific matches
Route::get('/en/tour/shahrisabz-day-tour-without-guide', fn () => redirect('/tours/shahrisabz-day-tour-self-guided', 301));
Route::get('/en/tour/classic-uzbekistan-tour-8-days', fn () => redirect('/tours/classic-uzbekistan-art-and-craft', 301));
Route::get('/en/tour/fergana-valley-tour-uzbekistan-10-days', fn () => redirect('/tours/silk-to-canvas-fergana-karakalpakstan', 301));
Route::get('/en/tour/bukhara-city-tour', fn () => redirect('/tours/bukhara-day-trip-from-samarkand', 301));
Route::get('/en/tour/nature-tour-uzbekistan-7-days', fn () => redirect('/tours/nature-tour-uzbekistan-7-days', 301));
Route::get('/en/tour/samarkand-history-tour', fn () => redirect('/tours/samarkand-city-group-tour', 301));
Route::get('/en/tour/2-day-desert-yurt-experience', fn () => redirect('/tours/samarkand-2-day-desert-yurt-camp-camel-ride', 301));
Route::get('/en/tour/seven-lakes-day-tour', fn () => redirect('/tours/seven-lakes-tajikistan-day-tour', 301));
Route::get('/en/tour/paper-pottery-silk-carpet-tour-samarkand', fn () => redirect('/tours/samarkand-pottery-weekend-craft-taster', 301));
Route::get('/en/tour/2-day-nuratau-mountains-tour-homestay-nature-heritage', fn () => redirect('/tours/nuratau-mountains-homestay-2-days', 301));
Route::get('/en/tour/desert-fortress-tour-from-khiva-toprak-qala-ayaz-qala-more', fn () => redirect('/tours/khiva-day-tour', 301));
Route::get('/en/tour/silk-road-splendors-of-uzbekistan', fn () => redirect('/tours/silk-road-splendors-uzbekistan-8-days', 301));

// French tours - specific matches
Route::get('/fr/tour/fergana-valley-tour-uzbekistan-10-days', fn () => redirect('/fr/tours/de-la-soie-a-la-toile-grand-voyage-de-la-vallee-de-fergana-au-karakalpakstan', 301));
Route::get('/fr/tour/seven-lakes-day-tour', fn () => redirect('/fr/tours/excursion-dune-journee-en-groupe-aux-sept-lacs-du-tadjikistan', 301));
Route::get('/fr/tour/silk-road-splendors-of-uzbekistan', fn () => redirect('/fr/craft-journeys', 301));
Route::get('/fr/tour/nature-tour-uzbekistan-7-days', fn () => redirect('/fr/craft-journeys', 301));

// Italian tours → redirect to English equivalents (no IT sitemap)
Route::get('/it/tour/shahrisabz-day-tour', fn () => redirect('/tours/shahrisabz-day-tour-guided', 301));
Route::get('/it/tour/fergana-valley-tour-uzbekistan-10-days', fn () => redirect('/tours/silk-to-canvas-fergana-karakalpakstan', 301));
Route::get('/it/tour/paper-pottery-silk-carpet-tour-samarkand', fn () => redirect('/tours/samarkand-pottery-weekend-craft-taster', 301));
Route::get('/it/tour/silk-road-splendors-of-uzbekistan', fn () => redirect('/craft-journeys', 301));
Route::get('/it/tour/classic-uzbekistan-tour-8-days', fn () => redirect('/tours/classic-uzbekistan-art-and-craft', 301));
Route::get('/it/tour/2-day-nuratau-mountains-tour-homestay-nature-heritage', fn () => redirect('/tours/nuratau-mountains-homestay-2-days', 301));
Route::get('/it/tour/2-day-desert-yurt-experience', fn () => redirect('/tours/samarkand-2-day-desert-yurt-camp-camel-ride', 301));

// Japanese tours → redirect to English equivalents (no JA sitemap)
Route::get('/ja/tour/nature-tour-uzbekistan-7-days', fn () => redirect('/tours/nature-tour-uzbekistan-7-days', 301));
Route::get('/ja/tour/silk-road-splendors-of-uzbekistan', fn () => redirect('/tours/silk-road-splendors-uzbekistan-8-days', 301));
Route::get('/ja/tour/desert-fortress-tour-from-khiva-toprak-qala-ayaz-qala-more', fn () => redirect('/tours/khiva-day-tour', 301));

// ============================================
// PRIORITY 3: Wildcard Patterns (Catch remaining)
// ============================================

// Old .php tour directories → tour listing pages
Route::get('/tours-from-samarkand/{any?}', fn () => redirect('/mini-journeys', 301))->where('any', '.*');
Route::get('/tours-from-bukhara/{any?}', fn () => redirect('/craft-journeys', 301))->where('any', '.*');
Route::get('/tours-from-khiva/{any?}', fn () => redirect('/craft-journeys', 301))->where('any', '.*');
Route::get('/uzbekistan-tours/{any?}', fn () => redirect('/craft-journeys', 301))->where('any', '.*');
Route::get('/tajikistan-tours/{any?}', fn () => redirect('/craft-journeys', 301))->where('any', '.*');

// WordPress EN patterns
Route::get('/en/tour/{any?}', fn () => redirect('/craft-journeys', 301))->where('any', '.*');
Route::get('/en/insight/{any?}', fn () => redirect('/blog', 301))->where('any', '.*');
Route::get('/en/city/{any?}', fn () => redirect('/destinations', 301))->where('any', '.*');
Route::get('/en/cultural-insight/{any?}', fn () => redirect('/', 301))->where('any', '.*');
Route::get('/en/insight-itinerarie/{any?}', fn () => redirect('/', 301))->where('any', '.*');
Route::get('/en/tour-topic/{any?}', fn () => redirect('/', 301))->where('any', '.*');
Route::get('/en/tour-type/{any?}', fn () => redirect('/craft-journeys', 301))->where('any', '.*');
Route::get('/en/shop/{any?}', fn () => redirect('/', 301))->where('any', '.*');

// WordPress FR patterns → FR localized pages
Route::get('/fr/tour/{any?}', fn () => redirect('/fr/craft-journeys', 301))->where('any', '.*');
Route::get('/fr/insight/{any?}', fn () => redirect('/fr/blog', 301))->where('any', '.*');
Route::get('/fr/city/{any?}', fn () => redirect('/fr/destinations', 301))->where('any', '.*');
Route::get('/fr/cultural-insight/{any?}', fn () => redirect('/fr', 301))->where('any', '.*');
Route::get('/fr/insight-itinerarie/{any?}', fn () => redirect('/fr', 301))->where('any', '.*');
Route::get('/fr/country/{any?}', fn () => redirect('/fr', 301))->where('any', '.*');
Route::get('/fr/shop/{any?}', fn () => redirect('/fr', 301))->where('any', '.*');

// WordPress IT patterns → EN (no Italian locale)
Route::get('/it/tour/{any?}', fn () => redirect('/craft-journeys', 301))->where('any', '.*');
Route::get('/it/insight/{any?}', fn () => redirect('/blog', 301))->where('any', '.*');
Route::get('/it/city/{any?}', fn () => redirect('/destinations', 301))->where('any', '.*');
Route::get('/it/cultural-insight/{any?}', fn () => redirect('/', 301))->where('any', '.*');
Route::get('/it/insight-itinerarie/{any?}', fn () => redirect('/', 301))->where('any', '.*');

// WordPress JA patterns → EN (no Japanese locale)
Route::get('/ja/tour/{any?}', fn () => redirect('/craft-journeys', 301))->where('any', '.*');
Route::get('/ja/insight/{any?}', fn () => redirect('/blog', 301))->where('any', '.*');
Route::get('/ja/city/{any?}', fn () => redirect('/destinations', 301))->where('any', '.*');
Route::get('/ja/cultural-insight/{any?}', fn () => redirect('/', 301))->where('any', '.*');
Route::get('/ja/insight-itinerarie/{any?}', fn () => redirect('/', 301))->where('any', '.*');

// Old blog directory (.php)
Route::get('/blog/{any}.php', fn () => redirect('/blog', 301))->where('any', '.*');

// Shop pages
Route::get('/shop/{any?}', fn () => redirect('/', 301))->where('any', '.*');

// Template pages
Route::get('/template/{any?}', fn () => redirect('/', 301))->where('any', '.*');

// Catch-all for remaining .php files
Route::get('/{any}.php', fn () => redirect('/', 301))->where('any', '.*');
