<footer class="site-footer">
    <div class="container">
    <div class="footer-main footer-main--desktop">
        <div class="footer-brand">
            <a href="{{ url('/') }}" class="footer-brand__link">
                <i class="fas fa-compass footer-brand__logo"></i>
                <span class="footer-brand__text">Jahongir Travel</span>
            </a>
            <p class="footer-brand__tagline">Tailor-made Uzbekistan tours since 2012.</p>

            <div class="footer-brand__contact-section">
                <div class="footer-brand__contact-title">Get in Touch</div>
                <address class="footer-brand__contact">
                    <a href="mailto:info@jahongirtravel.com"><i class="far fa-envelope"></i> info@jahongirtravel.com</a>
                    <a href="tel:+998991234567"><i class="fas fa-phone"></i> +998 99 123 4567</a>
                    <a href="https://wa.me/998915550808" target="_blank" rel="noopener noreferrer"><i class="fab fa-whatsapp"></i> WhatsApp</a>
                </address>
                <p class="footer-brand__location"><i class="fas fa-map-marker-alt"></i> Samarkand, Uzbekistan</p>
            </div>
        </div>

        <nav class="footer-col footer-nav" aria-label="Quick Links">
            <div class="footer-nav__title">Quick Links</div>
            <ul class="footer-nav__list">
                <li><a href="{{ url('/about') }}">About Us</a></li>
                <li><a href="{{ url('/contact') }}">Contact</a></li>
                <li><a href="{{ route('blog.index') }}">Blog</a></li>
                <li><a href="{{ url('/faqs') }}">FAQs</a></li>
            </ul>
        </nav>

        <nav class="footer-col footer-nav" aria-label="Popular Destinations">
            <div class="footer-nav__title">Destinations</div>
            <ul class="footer-nav__list">
                <li><a href="{{ url('/destinations/samarkand') }}">Samarkand</a></li>
                <li><a href="{{ url('/destinations/bukhara') }}">Bukhara</a></li>
                <li><a href="{{ url('/destinations/khiva') }}">Khiva</a></li>
                <li><a href="{{ url('/destinations/tashkent') }}">Tashkent</a></li>
            </ul>
        </nav>

    </div>
    </div>

    <div class="footer-bottom">
        <div class="footer-bottom__wrap">
            <div>© {{ date('Y') }} Jahongir Travel. All rights reserved.</div>
            <div class="footer-bottom__legal">
                <a href="{{ url('/privacy') }}">Privacy</a>
                <span> • </span>
                <a href="{{ url('/terms') }}">Terms</a>
                <span> • </span>
                <a href="{{ url('/cookies') }}">Cookies</a>
            </div>
        </div>
    </div>
</footer>
