<div class="lang-switcher">
    <button type="button" 
            class="lang-switcher__button"
            id="language-menu-button"
            aria-expanded="false"
            aria-haspopup="true"
            onclick="document.getElementById('language-menu').classList.toggle('lang-switcher__menu--open')">
        <span class="lang-switcher__current">
            @switch(app()->getLocale())
                @case('ru')
                    🇷🇺 <span class="lang-switcher__text">Русский</span>
                    @break
                @case('uz')
                    🇺🇿 <span class="lang-switcher__text">O'zbek</span>
                    @break
                @default
                    🇬🇧 <span class="lang-switcher__text">English</span>
            @endswitch
        </span>
        <svg class="lang-switcher__arrow" width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M3 4.5L6 7.5L9 4.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </button>

    <div id="language-menu"
         class="lang-switcher__menu"
         role="menu"
         aria-orientation="vertical"
         aria-labelledby="language-menu-button">
        <a href="{{ request()->fullUrlWithQuery(['locale' => 'en']) }}"
           class="lang-switcher__item {{ app()->getLocale() == 'en' ? 'lang-switcher__item--active' : '' }}"
           role="menuitem">
            🇬🇧 English
        </a>
        <a href="{{ request()->fullUrlWithQuery(['locale' => 'ru']) }}"
           class="lang-switcher__item {{ app()->getLocale() == 'ru' ? 'lang-switcher__item--active' : '' }}"
           role="menuitem">
            🇷🇺 Русский
        </a>
        <a href="{{ request()->fullUrlWithQuery(['locale' => 'uz']) }}"
           class="lang-switcher__item {{ app()->getLocale() == 'uz' ? 'lang-switcher__item--active' : '' }}"
           role="menuitem">
            🇺🇿 O'zbek
        </a>
    </div>
</div>

<script>
// Close dropdown when clicking outside
document.addEventListener('click', function(event) {
    const menu = document.getElementById('language-menu');
    const button = document.getElementById('language-menu-button');
    
    if (!button.contains(event.target) && !menu.contains(event.target)) {
        menu.classList.remove('lang-switcher__menu--open');
        button.setAttribute('aria-expanded', 'false');
    }
});

// Toggle aria-expanded
document.getElementById('language-menu-button').addEventListener('click', function() {
    const isOpen = document.getElementById('language-menu').classList.contains('lang-switcher__menu--open');
    this.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
});
</script>
