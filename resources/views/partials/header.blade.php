@php $locale = $currentLocale ?? 'en'; @endphp
<header class="site-header" role="banner">
    <!-- Navigation Bar -->
    <nav class="nav" aria-label="Main navigation">
        <div class="container">
            <a href="{{ route('localized.home', ['locale' => $locale]) }}" class="nav__logo">
                <span class="nav__logo-text">Jahongir <strong>Travel</strong></span>
            </a>

            <ul class="nav__menu" id="navMenu">
                <li><a href="{{ route('localized.home', ['locale' => $locale]) }}" class="{{ request()->is('/') || request()->is($locale) ? 'active' : '' }}">{{ __('ui.nav.home') }}</a></li>
                <li><a href="{{ route('localized.mini-journeys.index', ['locale' => $locale]) }}" class="{{ request()->is('*/mini-journeys*') || request()->is('mini-journeys*') ? 'active' : '' }}">{{ __('ui.nav.mini_journeys') }}</a></li>
                <li><a href="{{ route('localized.craft-journeys.index', ['locale' => $locale]) }}" class="{{ request()->is('*/craft-journeys*') || request()->is('craft-journeys*') || request()->is('*/tours*') || request()->is('tours*') ? 'active' : '' }}">{{ __('ui.nav.craft_journeys') }}</a></li>
                <li><a href="{{ route('localized.destinations.index', ['locale' => $locale]) }}" class="{{ request()->is('*/destinations*') || request()->is('destinations*') ? 'active' : '' }}">{{ __('ui.nav.destinations') }}</a></li>
                <li><a href="{{ route('localized.blog.index', ['locale' => $locale]) }}" class="{{ request()->is('*/blog*') || request()->is('blog*') ? 'active' : '' }}">{{ __('ui.nav.blog') }}</a></li>
                <li><a href="{{ route('localized.about', ['locale' => $locale]) }}" class="{{ request()->is('*/about') || request()->is('about') ? 'active' : '' }}">{{ __('ui.nav.about') }}</a></li>
                <li><a href="{{ route('localized.contact', ['locale' => $locale]) }}" class="{{ request()->is('*/contact') || request()->is('contact') ? 'active' : '' }}">{{ __('ui.nav.contact') }}</a></li>
            </ul>

            {{-- Language Switcher --}}
            <x-lang-switcher />

            <button type="button" class="nav__toggle" id="navToggle" aria-label="Toggle navigation menu" aria-expanded="false">
                <i class="fas fa-bars nav__toggle-icon-bars"></i>
                <i class="fas fa-times nav__toggle-icon-close"></i>
            </button>
        </div>
    </nav>
</header>
