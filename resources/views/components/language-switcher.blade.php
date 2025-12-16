<div class="language-switcher" x-data="{ open: false }" @click.away="open = false">
    <button 
        type="button" 
        class="language-switcher__button" 
        @click="open = !open"
        :aria-expanded="open"
        aria-label="Select language"
    >
        <span class="language-switcher__current">
            @php
                $currentLocale = app()->getLocale();
                $locales = config('locales.available');
                $flags = ['ru' => '🇷🇺', 'en' => '🇬🇧', 'uz' => '🇺🇿'];
            @endphp
            <span class="language-switcher__flag">{{ $flags[$currentLocale] ?? '🌐' }}</span>
            <span class="language-switcher__name">{{ $locales[$currentLocale] ?? 'Language' }}</span>
            <svg class="language-switcher__arrow" :class="{ 'rotate': open }" width="12" height="8" viewBox="0 0 12 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M1 1.5L6 6.5L11 1.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </span>
    </button>

    <div 
        class="language-switcher__dropdown" 
        x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-y-1"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-1"
        style="display: none;"
    >
        @foreach($locales as $code => $name)
            @if($code !== $currentLocale)
                @php
                    // Get current URL without locale prefix
                    $segments = request()->segments();
                    $availableLocales = array_keys(config('locales.available'));
                    
                    // Remove locale prefix if present
                    if (count($segments) > 0 && in_array($segments[0], $availableLocales)) {
                        array_shift($segments);
                    }
                    
                    $pathWithoutLocale = implode('/', $segments);
                    
                    // Build new URL with target locale
                    if ($code === config('locales.default', 'ru')) {
                        // Default locale: no prefix
                        $newUrl = url($pathWithoutLocale ?: '/');
                    } else {
                        // Other locales: add prefix
                        $newUrl = url($code . '/' . $pathWithoutLocale);
                    }
                @endphp
                <a 
                    href="{{ $newUrl }}" 
                    class="language-switcher__item"
                    @click="open = false"
                >
                    <span class="language-switcher__flag">{{ $flags[$code] ?? '🌐' }}</span>
                    <span class="language-switcher__name">{{ $name }}</span>
                </a>
            @endif
        @endforeach
    </div>
</div>

<style>
.language-switcher {
    position: relative;
    display: inline-block;
}

.language-switcher__button {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 12px;
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 8px;
    color: white;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
    backdrop-filter: blur(10px);
}

.language-switcher__button:hover {
    background: rgba(255, 255, 255, 0.15);
    border-color: rgba(255, 255, 255, 0.3);
    transform: translateY(-1px);
}

.language-switcher__current {
    display: flex;
    align-items: center;
    gap: 8px;
}

.language-switcher__flag {
    font-size: 18px;
    line-height: 1;
}

.language-switcher__name {
    font-size: 14px;
    white-space: nowrap;
}

.language-switcher__arrow {
    transition: transform 0.2s ease;
}

.language-switcher__arrow.rotate {
    transform: rotate(180deg);
}

.language-switcher__dropdown {
    position: absolute;
    top: calc(100% + 8px);
    right: 0;
    min-width: 160px;
    background: white;
    border-radius: 12px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
    overflow: hidden;
    z-index: 1000;
}

.language-switcher__item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 16px;
    color: #1a1a1a;
    text-decoration: none;
    transition: background 0.15s ease;
    font-size: 14px;
}

.language-switcher__item:hover {
    background: #f5f5f5;
}

.language-switcher__item .language-switcher__flag {
    font-size: 20px;
}

/* Mobile responsive */
@media (max-width: 768px) {
    .language-switcher__button {
        padding: 6px 10px;
    }
    
    .language-switcher__name {
        display: none;
    }
    
    .language-switcher__dropdown {
        right: auto;
        left: 0;
    }
}
</style>

@push('scripts')
<script src="//unpkg.com/alpinejs" defer></script>
@endpush
