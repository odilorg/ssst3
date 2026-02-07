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
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get locale from route parameter
        $locale = $request->route('locale');

        // Get supported locales from config
        $supportedLocales = config('multilang.locales', ['en']);
        $defaultLocale = config('multilang.default_locale', 'en');

        // Validate locale - abort 404 if invalid
        if ($locale && !in_array($locale, $supportedLocales)) {
            abort(404, "Locale '{$locale}' is not supported.");
        }

        // Use default locale if none provided
        $locale = $locale ?: $defaultLocale;

        // Set the application locale
        app()->setLocale($locale);

        // Bind currentLocale to app container for helper functions (locale_url, locale_route)
        app()->instance('currentLocale', $locale);

        // Set URL defaults so route() helper includes locale automatically
        URL::defaults(['locale' => $locale]);

        // Share locale variables with all Blade views
        View::share('currentLocale', $locale);
        View::share('supportedLocales', $supportedLocales);
        View::share('defaultLocale', $defaultLocale);
        View::share('localeNames', config('multilang.locale_names', []));

        return $next($request);
    }
}
