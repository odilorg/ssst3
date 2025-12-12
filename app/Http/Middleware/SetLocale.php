<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get available locales from config
        $availableLocales = array_keys(config('translatable.locales', ['en', 'ru', 'uz']));
        
        // 1. Check URL parameter (?locale=ru)
        if ($request->has('locale')) {
            $locale = $request->get('locale');
            
            if (in_array($locale, $availableLocales)) {
                session(['locale' => $locale]);
                app()->setLocale($locale);
                return $next($request);
            }
        }
        
        // 2. Check session (user's previous choice)
        if (session()->has('locale')) {
            $locale = session('locale');
            
            if (in_array($locale, $availableLocales)) {
                app()->setLocale($locale);
                return $next($request);
            }
        }
        
        // 3. Check browser language
        $browserLocale = $request->getPreferredLanguage($availableLocales);
        if ($browserLocale && in_array($browserLocale, $availableLocales)) {
            app()->setLocale($browserLocale);
            return $next($request);
        }
        
        // 4. Default to English
        app()->setLocale('en');
        
        return $next($request);
    }
}
