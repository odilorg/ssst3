<header class="site-header" role="banner">
    <!-- Navigation Bar -->
    <nav class="nav" aria-label="Main navigation">
        <div class="container">
            <a href="{{ url('/') }}" class="nav__logo">
                <span class="nav__logo-text">Jahongir <strong>Travel</strong></span>
            </a>

            <ul class="nav__menu" id="navMenu">
                <li><a href="{{ url('/') }}" class="{{ request()->is('/') ? 'active' : '' }}">{{ __('ui.nav.home') }}</a></li>
                <li><a href="{{ url('/mini-journeys') }}" class="{{ request()->is('mini-journeys*') ? 'active' : '' }}">{{ __('ui.nav.mini_journeys') }}</a></li>
                <li><a href="{{ url('/craft-journeys') }}" class="{{ request()->is('craft-journeys*') || request()->is('tours*') ? 'active' : '' }}">{{ __('ui.nav.craft_journeys') }}</a></li>
                <li><a href="{{ url('/destinations') }}" class="{{ request()->is('destinations*') ? 'active' : '' }}">{{ __('ui.nav.destinations') }}</a></li>
                <li><a href="{{ route('blog.index') }}" class="{{ request()->is('blog*') ? 'active' : '' }}">{{ __('ui.nav.blog') }}</a></li>
                <li><a href="{{ url('/about') }}" class="{{ request()->is('about') ? 'active' : '' }}">{{ __('ui.nav.about') }}</a></li>
                <li><a href="{{ url('/contact') }}" class="{{ request()->is('contact') ? 'active' : '' }}">{{ __('ui.nav.contact') }}</a></li>
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
