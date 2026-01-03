{{--
    Language Switcher Component

    Displays a dropdown/list of available locales for switching languages.
    Only renders when multilang is enabled and language_switcher feature is on.

    Usage:
    <x-lang-switcher />
    <x-lang-switcher :dropdown="true" />

    Props:
    - dropdown: bool (default: true) - Use dropdown style or inline links
    - showFlags: bool (default: true) - Show flag emojis
    - showNative: bool (default: true) - Show native language names
--}}

@props([
    'dropdown' => true,
    'showFlags' => true,
    'showNative' => true,
])

@php
    // Only render if multilang is enabled and language switcher feature is on
    $isEnabled = config('multilang.enabled') && config('multilang.features.language_switcher');

    if (!$isEnabled) {
        return;
    }

    // Get locale info from shared view variables or config
    $currentLocale = $currentLocale ?? app()->getLocale();
    $supportedLocales = $supportedLocales ?? config('multilang.locales', ['en']);
    $localeNames = $localeNames ?? config('multilang.locale_names', []);

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
    @if($dropdown)
        {{-- Dropdown Style --}}
        <div class="lang-switcher lang-switcher--dropdown relative" x-data="{ open: false }">
            <button
                type="button"
                class="lang-switcher__trigger flex items-center gap-2 px-3 py-2 text-sm font-medium text-gray-700 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 rounded-md"
                @click="open = !open"
                @click.away="open = false"
                aria-haspopup="true"
                :aria-expanded="open"
                aria-label="{{ __('ui.language.switch') }}"
            >
                @if($showFlags && !empty($currentLocaleInfo['flag']))
                    <span class="lang-switcher__flag">{{ $currentLocaleInfo['flag'] }}</span>
                @endif
                <span class="lang-switcher__label">
                    {{ $showNative ? $currentLocaleInfo['native'] : $currentLocaleInfo['name'] }}
                </span>
                <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>

            <div
                x-show="open"
                x-transition:enter="transition ease-out duration-100"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-75"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="lang-switcher__menu absolute right-0 mt-2 w-40 bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5 z-50"
                role="menu"
                aria-orientation="vertical"
            >
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
                        class="lang-switcher__item flex items-center gap-2 px-4 py-2 text-sm {{ $isActive ? 'bg-gray-100 text-gray-900 font-medium' : 'text-gray-700 hover:bg-gray-50' }}"
                        role="menuitem"
                        @if($isActive) aria-current="true" @endif
                        hreflang="{{ $locale }}"
                    >
                        @if($showFlags && !empty($localeInfo['flag']))
                            <span class="lang-switcher__flag">{{ $localeInfo['flag'] }}</span>
                        @endif
                        <span>{{ $showNative ? $localeInfo['native'] : $localeInfo['name'] }}</span>
                        @if($isActive)
                            <svg class="w-4 h-4 ml-auto text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                        @endif
                    </a>
                @endforeach
            </div>
        </div>
    @else
        {{-- Inline Links Style --}}
        <nav class="lang-switcher lang-switcher--inline flex items-center gap-2" aria-label="{{ __('ui.language.switch') }}">
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
                    class="lang-switcher__link flex items-center gap-1 px-2 py-1 text-sm rounded {{ $isActive ? 'bg-blue-100 text-blue-800 font-medium' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}"
                    @if($isActive) aria-current="true" @endif
                    hreflang="{{ $locale }}"
                    title="{{ $localeInfo['name'] }}"
                >
                    @if($showFlags && !empty($localeInfo['flag']))
                        <span class="lang-switcher__flag">{{ $localeInfo['flag'] }}</span>
                    @endif
                    <span class="lang-switcher__code uppercase">{{ $locale }}</span>
                </a>
                @if(!$loop->last)
                    <span class="text-gray-300">|</span>
                @endif
            @endforeach
        </nav>
    @endif
@endif

<style>
/* Lang Switcher Base Styles */
.lang-switcher__flag {
    font-size: 1.25em;
    line-height: 1;
}

/* Ensure dropdown appears above other content */
.lang-switcher--dropdown {
    z-index: 40;
}
</style>
