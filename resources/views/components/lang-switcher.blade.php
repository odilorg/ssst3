{{--
    Language Switcher Component

    Displays a dropdown/list of available locales for switching languages.
    Only renders when multilang is enabled and language_switcher feature is on.

    Usage:
    <x-lang-switcher />
--}}

@php
    // Only render if multilang is enabled and language switcher feature is on
    $isEnabled = config('multilang.enabled') && config('multilang.features.language_switcher');

    if (!$isEnabled) {
        return;
    }

    // Get locale info from shared view variables or config
    $currentLocale = $currentLocale ?? app()->getLocale();
    $localeNames = $localeNames ?? config('multilang.locale_names', []);

    // Dynamic locale detection: on tour pages, show only languages with translations
    $supportedLocales = $supportedLocales ?? null;
    if (!$supportedLocales) {
        $route = request()->route();
        $routeName = $route?->getName();
        if ($routeName && in_array($routeName, ['tours.show', 'localized.tours.show'])) {
            $tourSlug = $route->parameter('slug');
            if ($tourSlug) {
                $tourId = \App\Models\TourTranslation::where('slug', $tourSlug)->value('tour_id')
                    ?? \App\Models\Tour::where('slug', $tourSlug)->value('id');
                if ($tourId) {
                    $supportedLocales = \App\Models\TourTranslation::where('tour_id', $tourId)
                        ->pluck('locale')
                        ->toArray();
                }
            }
        }
        $supportedLocales = $supportedLocales ?: config('multilang.locales', ['en']);
    }

    // Get current path and build URLs for each locale
    $currentPath = request()->path();
    $currentPathSegments = explode('/', $currentPath);

    // Check if current path starts with a locale
    $pathHasLocale = in_array($currentPathSegments[0] ?? '', $supportedLocales);

    // Build locale URLs
    $localeUrls = [];
    foreach ($supportedLocales as $locale) {
        if ($pathHasLocale) {
            // Replace first segment with target locale
            $segments = $currentPathSegments;
            $segments[0] = $locale;
            $localeUrls[$locale] = '/' . implode('/', $segments);
        } else {
            // Prefix path with target locale
            $localeUrls[$locale] = '/' . $locale . ($currentPath === '/' ? '' : '/' . $currentPath);
        }

        // Preserve query string
        $queryString = request()->getQueryString();
        if ($queryString) {
            $localeUrls[$locale] .= '?' . $queryString;
        }
    }

    // Get current locale info
    $currentLocaleInfo = $localeNames[$currentLocale] ?? [
        'name' => strtoupper($currentLocale),
        'native' => strtoupper($currentLocale),
        'flag' => '',
    ];
@endphp

@if($isEnabled && count($supportedLocales) > 1)
    <div class="lang-switcher" x-data="{ open: false }">
        <button
            type="button"
            class="lang-switcher__trigger"
            @click="open = !open"
            @click.away="open = false"
            aria-haspopup="true"
            :aria-expanded="open"
            aria-label="Select language"
        >
            <span class="lang-switcher__flag">{{ $currentLocaleInfo['flag'] ?? '' }}</span>
            <span class="lang-switcher__label">{{ strtoupper($currentLocale) }}</span>
            <svg class="lang-switcher__arrow" :class="{ 'is-open': open }" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <path d="M19 9l-7 7-7-7" />
            </svg>
        </button>

        <div class="lang-switcher__menu" x-show="open" x-cloak
             x-transition:enter="transition ease-out duration-100"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-75"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95">
            @foreach($supportedLocales as $locale)
                @php
                    $localeInfo = $localeNames[$locale] ?? [
                        'name' => strtoupper($locale),
                        'native' => strtoupper($locale),
                        'flag' => '',
                    ];
                    $isActive = $locale === $currentLocale;
                @endphp
                <a
                    href="{{ $localeUrls[$locale] }}"
                    class="lang-switcher__item {{ $isActive ? 'is-active' : '' }}"
                    hreflang="{{ $locale }}"
                    @if($isActive) aria-current="true" @endif
                >
                    <span class="lang-switcher__flag">{{ $localeInfo['flag'] ?? '' }}</span>
                    <span class="lang-switcher__name">{{ $localeInfo['native'] }}</span>
                    @if($isActive)
                        <svg class="lang-switcher__check" width="16" height="16" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                    @endif
                </a>
            @endforeach
        </div>
    </div>
@endif
