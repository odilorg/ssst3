{{--
    SEO Hreflang Component

    Generates hreflang alternate links for multilingual pages.
    Only generates links for locales that have translations.

    Usage:

    For entity pages (tours, cities, blog posts):
    <x-seo.hreflang
        :entity="$tour"
        route-name="localized.tours.show"
        :x-default="url('/tours/' . $tour->slug)"
    />

    For static pages:
    <x-seo.hreflang
        static
        route-name="localized.about"
    />
--}}

@props([
    'entity' => null,
    'routeName' => null,
    'xDefault' => null,
    'static' => false,
    'params' => [],
])

@if(config('multilang.enabled') && config('multilang.phases.seo'))
    @php
        $hreflangs = [];

        if ($static) {
            // Static pages have same URL structure for all locales
            $supportedLocales = config('multilang.locales', ['en', 'ru', 'fr']);
            $defaultLocale = config('multilang.default_locale', 'en');

            foreach ($supportedLocales as $locale) {
                try {
                    $hreflangs[] = [
                        'locale' => $locale,
                        'url' => route($routeName, array_merge(['locale' => $locale], $params)),
                    ];
                } catch (\Exception $e) {
                    // Route may not exist
                }
            }

            // x-default points to default locale
            if (!empty($hreflangs)) {
                try {
                    $hreflangs[] = [
                        'locale' => 'x-default',
                        'url' => route($routeName, array_merge(['locale' => $defaultLocale], $params)),
                    ];
                } catch (\Exception $e) {
                    // Route may not exist
                }
            }
        } elseif ($entity && method_exists($entity, 'translations')) {
            // Entity pages - only generate for locales with translations
            $supportedLocales = config('multilang.locales', ['en', 'ru', 'fr']);
            $defaultLocale = config('multilang.default_locale', 'en');

            // Load translations if not already loaded
            if (!$entity->relationLoaded('translations')) {
                $entity->load('translations');
            }

            $availableLocales = $entity->translations->pluck('locale')->toArray();

            foreach ($supportedLocales as $locale) {
                if (!in_array($locale, $availableLocales)) {
                    continue;
                }

                $translation = $entity->translations->firstWhere('locale', $locale);

                if (!$translation || !$translation->slug) {
                    continue;
                }

                try {
                    $hreflangs[] = [
                        'locale' => $locale,
                        'url' => route($routeName, [
                            'locale' => $locale,
                            'slug' => $translation->slug,
                        ]),
                    ];
                } catch (\Exception $e) {
                    // Route may not exist
                }
            }

            // Add x-default
            if (!empty($hreflangs)) {
                if ($xDefault) {
                    $hreflangs[] = [
                        'locale' => 'x-default',
                        'url' => $xDefault,
                    ];
                } elseif (in_array($defaultLocale, $availableLocales)) {
                    $defaultTranslation = $entity->translations->firstWhere('locale', $defaultLocale);
                    if ($defaultTranslation && $defaultTranslation->slug) {
                        try {
                            $hreflangs[] = [
                                'locale' => 'x-default',
                                'url' => route($routeName, [
                                    'locale' => $defaultLocale,
                                    'slug' => $defaultTranslation->slug,
                                ]),
                            ];
                        } catch (\Exception $e) {
                            // Route may not exist
                        }
                    }
                }
            }
        }
    @endphp

    @foreach($hreflangs as $hreflang)
        <link rel="alternate" hreflang="{{ $hreflang['locale'] }}" href="{{ $hreflang['url'] }}">
    @endforeach
@endif
