<?php

namespace App\Http\Middleware;

use App\Models\Language;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get locale from multiple sources (priority order)
        $locale = $this->getLocale($request);

        // Validate locale against active languages
        $activeLocales = Language::getActiveLocales();

        if (!in_array($locale, $activeLocales)) {
            // Fall back to default language if invalid
            $defaultLanguage = Language::getDefault();
            $locale = $defaultLanguage ? $defaultLanguage->code : config('app.locale', 'en');
        }

        // Set application locale
        App::setLocale($locale);

        // Store in session for persistence
        Session::put('locale', $locale);

        return $next($request);
    }

    /**
     * Get locale from request sources
     *
     * @param Request $request
     * @return string
     */
    private function getLocale(Request $request): string
    {
        // 1. Check query parameter (?lang=es)
        if ($request->has('lang')) {
            return $request->query('lang');
        }

        // 2. Check route parameter (if using URL-based locales like /es/tours)
        if ($request->route('locale')) {
            return $request->route('locale');
        }

        // 3. Check session
        if (Session::has('locale')) {
            return Session::get('locale');
        }

        // 4. Check Accept-Language header from browser
        $browserLocale = $request->getPreferredLanguage(Language::getActiveLocales());
        if ($browserLocale) {
            return $browserLocale;
        }

        // 5. Fall back to default language
        $defaultLanguage = Language::getDefault();
        return $defaultLanguage ? $defaultLanguage->code : config('app.locale', 'en');
    }
}
