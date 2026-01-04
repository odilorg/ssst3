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
    <div class="lang-switcher" x-data="{ open: false }">
        <button
            type="button"
            class="lang-switcher__btn"
            @click="open = !open"
            @click.away="open = false"
            aria-haspopup="true"
            :aria-expanded="open"
            aria-label="Select language"
        >
            <span class="lang-switcher__flag">{{ $currentLocaleInfo['flag'] ?? '' }}</span>
            <span class="lang-switcher__code">{{ strtoupper($currentLocale) }}</span>
            <svg class="lang-switcher__arrow" :class="{ 'is-open': open }" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M19 9l-7 7-7-7" />
            </svg>
        </button>

        <div class="lang-switcher__dropdown" x-show="open" x-cloak>
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
                    class="lang-switcher__option {{ $isActive ? 'is-active' : '' }}"
                    hreflang="{{ $locale }}"
                    @if($isActive) aria-current="true" @endif
                >
                    <span class="lang-switcher__flag">{{ $localeInfo['flag'] ?? '' }}</span>
                    <span class="lang-switcher__name">{{ $localeInfo['native'] }}</span>
                    @if($isActive)
                        <svg class="lang-switcher__check" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                    @endif
                </a>
            @endforeach
        </div>
    </div>

    <style>
    /* Language Switcher - Matches site header design */
    .lang-switcher {
        position: relative;
        margin-left: 1rem;
    }

    .lang-switcher__btn {
        display: flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.5rem 0.75rem;
        background: transparent;
        border: 1px solid rgba(255, 255, 255, 0.3);
        border-radius: 6px;
        color: #fff;
        font-size: 0.875rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .lang-switcher__btn:hover {
        background: rgba(255, 255, 255, 0.1);
        border-color: rgba(255, 255, 255, 0.5);
    }

    .lang-switcher__flag {
        font-size: 1.1em;
        line-height: 1;
    }

    .lang-switcher__code {
        font-weight: 600;
        letter-spacing: 0.5px;
    }

    .lang-switcher__arrow {
        width: 14px;
        height: 14px;
        transition: transform 0.2s ease;
    }

    .lang-switcher__arrow.is-open {
        transform: rotate(180deg);
    }

    .lang-switcher__dropdown {
        position: absolute;
        top: calc(100% + 8px);
        right: 0;
        min-width: 160px;
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        overflow: hidden;
        z-index: 100;
    }

    .lang-switcher__option {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem 1rem;
        color: #333;
        font-size: 0.9rem;
        text-decoration: none;
        transition: background 0.15s ease;
    }

    .lang-switcher__option:hover {
        background: #f5f5f5;
    }

    .lang-switcher__option.is-active {
        background: #e8f4fc;
        color: #1a5490;
        font-weight: 500;
    }

    .lang-switcher__name {
        flex: 1;
    }

    .lang-switcher__check {
        width: 16px;
        height: 16px;
        color: #1a5490;
    }

    /* Mobile styles */
    @media (max-width: 768px) {
        .lang-switcher {
            margin-left: 0.5rem;
        }

        .lang-switcher__btn {
            padding: 0.4rem 0.6rem;
        }

        .lang-switcher__name {
            display: none;
        }

        .lang-switcher__dropdown {
            right: -10px;
            min-width: 140px;
        }
    }

    /* Hide when dropdown closed */
    [x-cloak] {
        display: none !important;
    }
    </style>
@endif
