<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

/**
 * SetLocaleFromRoute Middleware
 *
 * Sets the application locale based on the {locale} route parameter.
 * Only active when multilang is enabled.
 *
 * Responsibilities:
 * - Validate locale against supported locales
 * - Set app locale
 * - Set URL defaults for route() helper
 * - Share locale variables with all views
 * - Bind currentLocale to app container for helper functions
 */
class SetLocaleFromRoute
{
    /**
     * Minimum number of tours a locale must have translations for
     * to be considered "globally supported" (shown on non-tour pages).
     */
    private const GLOBAL_LOCALE_THRESHOLD = 3;

    public function handle(Request $request, Closure $next): Response
    {
        // Get locale from route parameter
        $locale = $request->route('locale');

        $defaultLocale = config('multilang.default_locale', 'en');

        // Use default locale if none provided
        $locale = $locale ?: $defaultLocale;

        // Two-tier locale validation:
        // - "global_locales": locales with translations for N+ tours (safe for all pages)
        // - Tour pages can additionally accept any locale that has a translation for that tour
        $globalLocales = self::getGlobalLocales($defaultLocale);

        $isTourRoute = in_array(
            $request->route()?->getName(),
            ['localized.tours.show', 'tours.show']
        );

        if (!$isTourRoute && !in_array($locale, $globalLocales)) {
            // Non-tour page with unsupported locale → 404 fast
            abort(404);
        }

        // Set the application locale
        app()->setLocale($locale);

        // Bind currentLocale to app container for helper functions (locale_url, locale_route)
        app()->instance('currentLocale', $locale);

        // Set URL defaults so route() helper includes locale automatically
        URL::defaults(['locale' => $locale]);

        // Share locale variables with all Blade views
        View::share('currentLocale', $locale);
        View::share('supportedLocales', $globalLocales);
        View::share('defaultLocale', $defaultLocale);
        View::share('localeNames', config('multilang.locale_names', []));

        return $next($request);
    }

    /**
     * Get locales that are supported site-wide (not just on one tour).
     * A locale qualifies if it has translations for at least N tours.
     */
    public static function getGlobalLocales(string $defaultLocale = null): array
    {
        $defaultLocale = $defaultLocale ?: config('multilang.default_locale', 'en');

        $locales = cache()->remember('global_locales', 3600, function () {
            try {
                return \App\Models\TourTranslation::query()
                    ->select('locale')
                    ->whereNotNull('slug')
                    ->where('slug', '!=', '')
                    ->groupBy('locale')
                    ->havingRaw('COUNT(DISTINCT tour_id) >= ?', [self::GLOBAL_LOCALE_THRESHOLD])
                    ->pluck('locale')
                    ->toArray();
            } catch (\Exception $e) {
                return ['en'];
            }
        });

        // Always include default locale
        if (!in_array($defaultLocale, $locales)) {
            $locales[] = $defaultLocale;
        }

        return $locales;
    }
}
