@php
    use App\Models\Language;

    $languages = Language::getActive();
    $currentLocale = app()->getLocale();
@endphp

<div class="language-switcher">
    <button class="lang-toggle" type="button" id="languageDropdown" aria-expanded="false" aria-haspopup="true">
        @php
            $current = $languages->firstWhere('code', $currentLocale);
        @endphp
        @if($current)
            <span class="lang-flag">{{ $current->flag }}</span>
            <span class="lang-name">{{ $current->native_name }}</span>
        @else
            <span class="lang-name">Language</span>
        @endif
        <svg class="lang-arrow" width="10" height="10" viewBox="0 0 10 10" fill="none">
            <path d="M2 3.5L5 6.5L8 3.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </button>
    <ul class="lang-menu" id="languageMenu">
        @foreach($languages as $language)
            <li>
                <a class="lang-item {{ $language->code === $currentLocale ? 'active' : '' }}"
                   href="{{ url()->current() }}?lang={{ $language->code }}">
                    <span class="lang-flag">{{ $language->flag }}</span>
                    <span class="lang-name">{{ $language->native_name }}</span>
                    @if($language->code === $currentLocale)
                        <svg class="lang-check" width="14" height="14" viewBox="0 0 14 14" fill="none">
                            <path d="M11.6667 3.5L5.25 9.91667L2.33333 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    @endif
                </a>
            </li>
        @endforeach
    </ul>
</div>

<style>
.language-switcher {
    position: relative;
    display: inline-block;
}

.lang-toggle {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.3);
    border-radius: 6px;
    cursor: pointer;
    font-size: 13px;
    font-weight: 500;
    color: #fff;
    transition: all 0.3s ease;
    font-family: 'Poppins', sans-serif;
}

.lang-toggle:hover {
    background: rgba(255, 255, 255, 0.15);
    border-color: rgba(255, 255, 255, 0.5);
}

.nav--sticky .lang-toggle {
    background: rgba(0, 0, 0, 0.03);
    border-color: rgba(0, 0, 0, 0.1);
    color: #333;
}

.nav--sticky .lang-toggle:hover {
    background: rgba(0, 0, 0, 0.05);
    border-color: rgba(0, 0, 0, 0.15);
}

.lang-flag {
    font-size: 18px;
    line-height: 1;
}

.lang-name {
    display: none;
}

.lang-arrow {
    transition: transform 0.2s;
    opacity: 0.8;
}

.lang-toggle[aria-expanded="true"] .lang-arrow {
    transform: rotate(180deg);
}

.lang-menu {
    position: absolute;
    top: calc(100% + 8px);
    right: 0;
    min-width: 160px;
    background: #fff;
    border: 1px solid rgba(0, 0, 0, 0.1);
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    padding: 6px;
    margin: 0;
    list-style: none;
    opacity: 0;
    visibility: hidden;
    transform: translateY(-10px);
    transition: all 0.2s ease;
    z-index: 1000;
}

.lang-menu.show {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
}

.lang-menu li {
    margin: 0;
    padding: 0;
}

.lang-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 8px 12px;
    border-radius: 5px;
    cursor: pointer;
    transition: background 0.2s;
    text-decoration: none;
    color: #333;
    font-size: 14px;
}

.lang-item:hover {
    background: #f5f5f5;
}

.lang-item.active {
    background: #e8f4ff;
    color: #0066cc;
    font-weight: 500;
}

.lang-check {
    margin-left: auto;
    color: #0066cc;
}

@media (min-width: 769px) {
    .lang-name {
        display: inline;
    }

    .lang-toggle {
        padding: 6px 14px;
    }
}

@media (max-width: 768px) {
    .lang-menu {
        right: auto;
        left: 50%;
        transform: translateX(-50%) translateY(-10px);
    }

    .lang-menu.show {
        transform: translateX(-50%) translateY(0);
    }
}
</style>

<script>
(function() {
    const toggle = document.getElementById('languageDropdown');
    const menu = document.getElementById('languageMenu');

    if (!toggle || !menu) return;

    toggle.addEventListener('click', function(e) {
        e.stopPropagation();
        const isExpanded = this.getAttribute('aria-expanded') === 'true';

        if (isExpanded) {
            this.setAttribute('aria-expanded', 'false');
            menu.classList.remove('show');
        } else {
            this.setAttribute('aria-expanded', 'true');
            menu.classList.add('show');
        }
    });

    // Close when clicking outside
    document.addEventListener('click', function(e) {
        if (!toggle.contains(e.target) && !menu.contains(e.target)) {
            toggle.setAttribute('aria-expanded', 'false');
            menu.classList.remove('show');
        }
    });

    // Close when pressing Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            toggle.setAttribute('aria-expanded', 'false');
            menu.classList.remove('show');
        }
    });
})();
</script>
