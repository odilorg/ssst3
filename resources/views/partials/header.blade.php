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

            <a href="tel:+998991234567" class="btn btn--accent nav__cta">
                <svg width="18" height="18" viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M6.62 10.79a15.09 15.09 0 006.59 6.59l2.2-2.2a1 1 0 011.11-.24 11.72 11.72 0 003.67.59 1 1 0 011 1v3.54a1 1 0 01-1 1A18.5 18.5 0 013 5a1 1 0 011-1h3.55a1 1 0 011 1 11.72 11.72 0 00.59 3.67 1 1 0 01-.25 1.11z"/>
                </svg>
                +998 99 123 4567
            </a>

            <button class="nav__toggle" id="navToggle" aria-label="Toggle navigation menu" aria-expanded="false">
                <span class="nav__toggle-icon"></span>
            </button>
        </div>
    </nav>
</header>
