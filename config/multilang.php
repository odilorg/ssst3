<?php

/**
 * Multilingual Configuration
 *
 * Feature flags and settings for the multilingual implementation.
 * This config allows safe, phased rollout of i18n features.
 *
 * PHASE 0: All features disabled by default
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Multilingual Feature Flag
    |--------------------------------------------------------------------------
    |
    | Master switch for multilingual features. When false, the app behaves
    | exactly as before (single language, no locale routing).
    |
    */

    'enabled' => env('MULTILANG_ENABLED', false),

    /*
    |--------------------------------------------------------------------------
    | Supported Locales
    |--------------------------------------------------------------------------
    |
    | List of locale codes the app supports. The first one is the default.
    | Format: ISO 639-1 language codes (e.g., 'en', 'ru', 'fr')
    |
    */

    'locales' => ['en', 'ru', 'fr'],

    /*
    |--------------------------------------------------------------------------
    | Default Locale
    |--------------------------------------------------------------------------
    |
    | The default locale used when no locale is specified in the URL.
    | Must be one of the locales listed above.
    |
    */

    'default_locale' => 'en',

    /*
    |--------------------------------------------------------------------------
    | Locale URL Parameter Name
    |--------------------------------------------------------------------------
    |
    | The route parameter name used for locale in URLs.
    | Example: /{locale}/tours -> /en/tours
    |
    */

    'locale_param_name' => 'locale',

    /*
    |--------------------------------------------------------------------------
    | Redirect Root by Accept-Language Header
    |--------------------------------------------------------------------------
    |
    | When enabled, visiting the root URL (/) will redirect to the appropriate
    | locale based on the browser's Accept-Language header.
    |
    | WARNING: Keep OFF during initial rollout to avoid breaking existing URLs.
    |
    */

    'redirect_root_by_accept_language' => env('MULTILANG_REDIRECT_BY_HEADER', false),

    /*
    |--------------------------------------------------------------------------
    | Locale Display Names
    |--------------------------------------------------------------------------
    |
    | Human-readable names for each locale, used in language switchers.
    | Format: 'locale_code' => ['name' => 'Display Name', 'native' => 'Native Name']
    |
    */

    'locale_names' => [
        'en' => [
            'name' => 'English',
            'native' => 'English',
            'flag' => '🇬🇧',
        ],
        'ru' => [
            'name' => 'Russian',
            'native' => 'Русский',
            'flag' => '🇷🇺',
        ],
        'fr' => [
            'name' => 'French',
            'native' => 'Français',
            'flag' => '🇫🇷',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Feature Flags for Phased Rollout
    |--------------------------------------------------------------------------
    |
    | Granular feature flags for different aspects of multilingual support.
    | These allow enabling features one at a time for safe rollout.
    |
    */

    'features' => [
        // Phase 1: Basic locale routing and UI translations
        'locale_routing' => env('MULTILANG_LOCALE_ROUTING', false),
        'language_switcher' => env('MULTILANG_LANGUAGE_SWITCHER', false),
        'ui_translations' => env('MULTILANG_UI_TRANSLATIONS', false),

        // Phase 2: Tours DB translations
        'tours_translations' => env('MULTILANG_TOURS_TRANSLATIONS', false),
        'localized_tour_slugs' => env('MULTILANG_LOCALIZED_SLUGS', false),

        // Phase 3: Other content translations
        'cities_translations' => env('MULTILANG_CITIES_TRANSLATIONS', false),
        'insights_translations' => env('MULTILANG_INSIGHTS_TRANSLATIONS', false),

        // Phase 4: SEO features
        'hreflang_tags' => env('MULTILANG_HREFLANG', false),
        'localized_sitemap' => env('MULTILANG_LOCALIZED_SITEMAP', false),

        // Phase 5: Admin tools
        'admin_translation_ui' => env('MULTILANG_ADMIN_UI', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Routes to Exclude from Locale Prefix
    |--------------------------------------------------------------------------
    |
    | These route patterns will NOT get the locale prefix.
    | Useful for API routes, webhooks, admin panel, etc.
    |
    */

    'exclude_routes' => [
        'api/*',
        'admin/*',
        'filament/*',
        'livewire/*',
        'webhook/*',
        '_debugbar/*',
        'sanctum/*',
    ],

    /*
    |--------------------------------------------------------------------------
    | Cookie/Session Settings
    |--------------------------------------------------------------------------
    |
    | How to persist the user's locale preference.
    |
    */

    'persistence' => [
        'cookie_name' => 'locale',
        'cookie_lifetime' => 60 * 24 * 365, // 1 year in minutes
    ],

];
