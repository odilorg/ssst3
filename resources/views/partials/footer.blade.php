@php $locale = $currentLocale ?? 'en'; @endphp
<footer class="site-footer">
    <div class="container">
    <div class="footer-main footer-main--desktop">
        <div class="footer-brand">
            <a href="{{ route('localized.home', ['locale' => $locale]) }}" class="footer-brand__link">
                <i class="fas fa-compass footer-brand__logo"></i>
                <span class="footer-brand__text">Jahongir Travel</span>
            </a>
            <p class="footer-brand__tagline">{{ __('ui.footer.tagline') }}</p>

            <div class="footer-brand__contact-section">
                <div class="footer-brand__contact-title">{{ __('ui.footer.get_in_touch') }}</div>
                <address class="footer-brand__contact">
                    <a href="mailto:info@jahongir-travel.uz"><i class="far fa-envelope"></i> info@jahongir-travel.uz</a>
                    <a href="tel:+998915550808"><i class="fas fa-phone"></i> +998 91 555 08 08</a>
                    <a href="https://wa.me/998915550808" target="_blank" rel="noopener noreferrer"><i class="fab fa-whatsapp"></i> WhatsApp</a>
                </address>
                <p class="footer-brand__location"><i class="fas fa-map-marker-alt"></i> Samarkand, Chirokchi 4</p>
            </div>
        </div>

        <nav class="footer-col footer-nav" aria-label="Quick Links">
            <div class="footer-nav__title">{{ __('ui.footer.quick_links') }}</div>
            <ul class="footer-nav__list">
                <li><a href="{{ route('localized.about', ['locale' => $locale]) }}">{{ __('ui.footer.about_us') }}</a></li>
                <li><a href="{{ route('localized.contact', ['locale' => $locale]) }}">{{ __('ui.footer.contact') }}</a></li>
                <li><a href="{{ route('localized.blog.index', ['locale' => $locale]) }}">{{ __('ui.footer.blog') }}</a></li>
            </ul>
        </nav>

        <nav class="footer-col footer-nav" aria-label="Popular Destinations">
            <div class="footer-nav__title">{{ __('ui.footer.destinations') }}</div>
            <ul class="footer-nav__list">
                <li><a href="{{ route('localized.city.show', ['locale' => $locale, 'slug' => 'samarkand']) }}">Samarkand</a></li>
                <li><a href="{{ route('localized.city.show', ['locale' => $locale, 'slug' => 'bukhara']) }}">Bukhara</a></li>
                <li><a href="{{ route('localized.city.show', ['locale' => $locale, 'slug' => 'khiva']) }}">Khiva</a></li>
                <li><a href="{{ route('localized.city.show', ['locale' => $locale, 'slug' => 'tashkent']) }}">Tashkent</a></li>
            </ul>
        </nav>

    </div>
    </div>

    <div class="footer-bottom">
        <div class="footer-bottom__wrap">
            <div>© {{ date('Y') }} Jahongir Travel. {{ __('ui.footer.copyright') }}</div>
            <div class="footer-bottom__legal">
                <a href="{{ route('localized.privacy', ['locale' => $locale]) }}">{{ __('ui.footer.privacy_policy') }}</a>
                <span> • </span>
                <a href="{{ route('localized.terms', ['locale' => $locale]) }}">{{ __('ui.footer.terms_of_service') }}</a>
                <span> • </span>
                <a href="{{ route('localized.cookies', ['locale' => $locale]) }}">{{ __('ui.footer.cookie_policy') }}</a>
            </div>
        </div>
    </div>
</footer>
