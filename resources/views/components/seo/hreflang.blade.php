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
            // Static pages — use DB-driven global locales (threshold-based)
            $globalLocales = \App\Http\Middleware\SetLocaleFromRoute::getGlobalLocales();
            $defaultLocale = config('multilang.default_locale', 'en');

            foreach ($globalLocales as $locale) {
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
            // Entity pages — only emit hreflang for locales with actual translations + valid slugs
            $defaultLocale = config('multilang.default_locale', 'en');

            if (!$entity->relationLoaded('translations')) {
                $entity->load('translations');
            }

            foreach ($entity->translations as $translation) {
                if (!$translation->slug) {
                    continue;
                }

                try {
                    $hreflangs[] = [
                        'locale' => $translation->locale,
                        'url' => route($routeName, [
                            'locale' => $translation->locale,
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
                } else {
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
