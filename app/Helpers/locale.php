<?php

if (!function_exists('locale_url')) {
    /**
     * Generate a locale-aware URL.
     * 
     * @param string $path The path without locale prefix (e.g., '/tours', '/about')
     * @param string|null $locale The locale to use (defaults to current locale)
     * @return string The full URL with locale prefix
     */
    function locale_url(string $path, ?string $locale = null): string
    {
        $locale = $locale ?? app('currentLocale') ?? 'en';
        
        // Ensure path starts with /
        $path = '/' . ltrim($path, '/');
        
        return url($locale . $path);
    }
}

if (!function_exists('locale_route')) {
    /**
     * Generate a locale-aware named route URL.
     * 
     * @param string $name The route name (e.g., 'tours.show')
     * @param array $parameters Route parameters
     * @param string|null $locale The locale to use (defaults to current locale)
     * @return string The full URL
     */
    function locale_route(string $name, array $parameters = [], ?string $locale = null): string
    {
        $locale = $locale ?? app('currentLocale') ?? 'en';
        
        // Try localized route first
        $localizedName = 'localized.' . $name;
        
        if (\Illuminate\Support\Facades\Route::has($localizedName)) {
            return route($localizedName, array_merge(['locale' => $locale], $parameters));
        }
        
        // Fall back to non-localized route if it exists
        if (\Illuminate\Support\Facades\Route::has($name)) {
            return route($name, $parameters);
        }
        
        // Last resort: build URL manually
        return locale_url('/' . str_replace('.', '/', $name), $locale);
    }
}
