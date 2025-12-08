<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class SetLocaleMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $this->getLocale($request);
        
        if (!in_array($locale, config('app.available_locales'))) {
            $locale = config('app.fallback_locale');
        }
        
        App::setLocale($locale);
        Session::put('locale', $locale);
        
        return $next($request);
    }
    
    /**
     * Get the locale from request sources
     */
    private function getLocale(Request $request): string
    {
        // 1. Check URL parameter (route parameter)
        if ($request->route('locale')) {
            return $request->route('locale');
        }
        
        // 2. Check session
        if (Session::has('locale')) {
            return Session::get('locale');
        }
        
        // 3. Check Accept-Language header
        $preferredLanguage = $request->getPreferredLanguage(config('app.available_locales'));
        if ($preferredLanguage) {
            return $preferredLanguage;
        }
        
        // 4. Fallback to default
        return config('app.fallback_locale');
    }
}
