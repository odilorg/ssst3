<footer class="site-footer">
    <div class="container footer-main footer-main--desktop">
        <div class="footer-brand">
            <a href="{{ url('/') }}" class="footer-brand__link">
                <i class="fas fa-compass footer-brand__logo"></i>
                <span class="footer-brand__text">Jahongir Travel</span>
            </a>
            <p class="footer-brand__tagline">Tailor-made Uzbekistan tours since 2012.</p>
            <address class="footer-brand__contact">
                <a href="mailto:info@jahongirtravel.com">info@jahongirtravel.com</a><br>
                <a href="tel:+998991234567">+998 99 123 4567</a>
            </address>
            <p class="footer-brand__location">Samarkand, Uzbekistan</p>
        </div>

        <nav class="footer-col footer-nav" aria-label="Company">
            <div class="footer-nav__title">Company</div>
            <ul class="footer-nav__list">
                <li><a href="{{ url('/about') }}">About us</a></li>
                <li><a href="{{ url('/careers') }}">Careers</a></li>
                <li><a href="{{ route('blog.index') }}">Blog</a></li>
                <li><a href="{{ url('/partners') }}">Partner</a></li>
                <li><a href="{{ url('/contact') }}">Contact</a></li>
            </ul>
        </nav>

        <nav class="footer-col footer-nav" aria-label="Services">
            <div class="footer-nav__title">Services</div>
            <ul class="footer-nav__list">
                <li><a href="{{ url('/tours') }}">Tour booking</a></li>
                <li><a href="{{ url('/visa') }}">Visa online</a></li>
                <li><a href="{{ url('/guides') }}">Travel guide</a></li>
                <li><a href="{{ url('/car-service') }}">Car service</a></li>
                <li><a href="{{ url('/sim') }}">SIM & eSIM</a></li>
            </ul>
        </nav>

        <nav class="footer-col footer-nav" aria-label="Help">
            <div class="footer-nav__title">Need help?</div>
            <ul class="footer-nav__list">
                <li><a href="{{ url('/faqs') }}">FAQs</a></li>
                <li><a href="{{ url('/support') }}">Customer care</a></li>
                <li><a href="{{ url('/safety') }}">Safety tips</a></li>
                <li><a href="{{ url('/privacy') }}">Privacy policy</a></li>
                <li><a href="{{ url('/terms') }}">Terms of use</a></li>
            </ul>
        </nav>

        <div class="footer-col footer-social">
            <div class="footer-social__title">Connect</div>
            <ul class="footer-social__list">
                <li><a href="https://facebook.com/jahongirtravel" target="_blank" rel="noopener noreferrer" aria-label="Facebook"><i class="fab fa-facebook"></i></a></li>
                <li><a href="https://instagram.com/jahongirtravel" target="_blank" rel="noopener noreferrer" aria-label="Instagram"><i class="fab fa-instagram"></i></a></li>
                <li><a href="https://twitter.com/jahongirtravel" target="_blank" rel="noopener noreferrer" aria-label="Twitter"><i class="fab fa-twitter"></i></a></li>
                <li><a href="https://youtube.com/@jahongirtravel" target="_blank" rel="noopener noreferrer" aria-label="YouTube"><i class="fab fa-youtube"></i></a></li>
            </ul>
        </div>
    </div>

    <div class="container footer-bottom">
        <div class="footer-bottom__wrap">
            <div class="footer-bottom__copyright">© {{ date('Y') }} Jahongir Travel. All rights reserved.</div>
            <div class="footer-bottom__legal">
                <a href="{{ url('/privacy') }}">Privacy</a>
                <span> • </span>
                <a href="{{ url('/terms') }}">Terms</a>
            </div>
        </div>
    </div>
</footer>
