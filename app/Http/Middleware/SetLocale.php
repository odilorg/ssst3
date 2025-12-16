<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
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
        // Get available locales from config
        $availableLocales = array_keys(config('locales.available'));
        
        // Priority: URL segment > Session > Default
        $locale = $request->segment(1);
        
        if (in_array($locale, $availableLocales)) {
            // Valid locale in URL
            app()->setLocale($locale);
            session(['locale' => $locale]);
        } else {
            // No locale in URL, use session or default
            $locale = session('locale', config('locales.default'));
            if (in_array($locale, $availableLocales)) {
                app()->setLocale($locale);
            }
        }
        
        return $next($request);
    }
}
