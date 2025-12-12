<?php

namespace App\Helpers;

class LocaleHelper
{
    /**
     * Get available locales
     */
    public static function getAvailableLocales(): array
    {
        return [
            'en' => 'English',
            'ru' => 'Русский',
            'uz' => 'O\'zbek',
        ];
    }

    /**
     * Get current locale
     */
    public static function getCurrentLocale(): string
    {
        return app()->getLocale();
    }

    /**
     * Get alternate URLs for all locales
     */
    public static function getAlternateUrls(): array
    {
        $urls = [];
        $currentUrl = request()->url();
        
        foreach (self::getAvailableLocales() as $locale => $name) {
            $urls[$locale] = $currentUrl . '?locale=' . $locale;
        }
        
        return $urls;
    }

    /**
     * Generate hreflang tags for SEO
     */
    public static function generateHreflangTags(): string
    {
        $tags = '';
        $alternateUrls = self::getAlternateUrls();
        
        foreach ($alternateUrls as $locale => $url) {
            $tags .= '<link rel="alternate" hreflang="' . $locale . '" href="' . $url . '">' . "\n";
        }
        
        // Add x-default for English
        $tags .= '<link rel="alternate" hreflang="x-default" href="' . $alternateUrls['en'] . '">';
        
        return $tags;
    }
}
