<header class="site-header" role="banner">
    <!-- Navigation Bar -->
    <nav class="nav" aria-label="Main navigation">
        <div class="container">
            <a href="{{ url('/') }}" class="nav__logo">
                <span class="nav__logo-text">Jahongir <strong>Travel</strong></span>
            </a>

            <ul class="nav__menu" id="navMenu">
                <li><a href="{{ url('/') }}" class="{{ request()->is('/') ? 'active' : '' }}">Home</a></li>
                <li><a href="{{ url('/tours') }}" class="{{ request()->is('tours*') ? 'active' : '' }}">Tours</a></li>
                <li><a href="{{ url('/destinations') }}" class="{{ request()->is('destinations*') ? 'active' : '' }}">Destinations</a></li>
                <li><a href="{{ route('blog.index') }}" class="{{ request()->is('blog*') ? 'active' : '' }}">Blog</a></li>
                <li><a href="{{ url('/about') }}" class="{{ request()->is('about') ? 'active' : '' }}">About Us</a></li>
                <li><a href="{{ url('/contact') }}" class="{{ request()->is('contact') ? 'active' : '' }}">Contact</a></li>
            </ul>

            <div class="nav__actions">
                <x-language-switcher />
                <button type="button" class="nav__toggle" id="navToggle" aria-label="Toggle navigation menu" aria-expanded="false">
                    <i class="fas fa-bars nav__toggle-icon-bars"></i>
                    <i class="fas fa-times nav__toggle-icon-close"></i>
                </button>
            </div>
        </div>
    </nav>
</header>
