<div class="language-switcher">
    <div class="dropdown">
        <button class="language-toggle" type="button" id="languageSwitcher" data-dropdown-toggle aria-expanded="false">
            <svg class="language-icon" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M10 18C14.4183 18 18 14.4183 18 10C18 5.58172 14.4183 2 10 2C5.58172 2 2 5.58172 2 10C2 14.4183 5.58172 18 10 18Z" stroke="currentColor" stroke-width="2"/>
                <path d="M2 10H18M10 2C11.5 4.5 12 7 12 10C12 13 11.5 15.5 10 18M10 2C8.5 4.5 8 7 8 10C8 13 8.5 15.5 10 18" stroke="currentColor" stroke-width="2"/>
            </svg>
            <span class="language-code">{{ strtoupper(app()->getLocale()) }}</span>
            <svg class="chevron-icon" width="12" height="12" viewBox="0 0 12 12" fill="none">
                <path d="M3 4.5L6 7.5L9 4.5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
        </button>
        
        <div class="language-dropdown" id="languageDropdown" data-dropdown-menu hidden>
            @foreach(config('app.available_locales') as $locale)
                <a href="{{ route(Route::currentRouteName(), array_merge(Route::current()->parameters(), ['locale' => $locale])) }}" 
                   class="language-option {{ app()->getLocale() === $locale ? 'active' : '' }}">
                    <span class="flag-icon">
                        @switch($locale)
                            @case('en') 🇬🇧 @break
                            @case('ru') 🇷🇺 @break
                            @case('uz') 🇺🇿 @break
                        @endswitch
                    </span>
                    <span class="language-name">
                        @switch($locale)
                            @case('en') English @break
                            @case('ru') Русский @break
                            @case('uz') O'zbek @break
                        @endswitch
                    </span>
                    @if(app()->getLocale() === $locale)
                        <svg class="check-icon" width="16" height="16" viewBox="0 0 16 16" fill="none">
                            <path d="M3 8L6.5 11.5L13 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    @endif
                </a>
            @endforeach
        </div>
    </div>
</div>

<style>
.language-switcher {
    position: relative;
}

.language-toggle {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 8px 12px;
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.2s;
    font-size: 14px;
    font-weight: 500;
}

.language-toggle:hover {
    border-color: #d1d5db;
    background: #f9fafb;
}

.language-icon {
    color: #6b7280;
}

.language-code {
    color: #111827;
    min-width: 24px;
}

.chevron-icon {
    color: #9ca3af;
    transition: transform 0.2s;
}

.language-toggle[aria-expanded="true"] .chevron-icon {
    transform: rotate(180deg);
}

.language-dropdown {
    position: absolute;
    top: calc(100% + 8px);
    right: 0;
    min-width: 180px;
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    z-index: 50;
    overflow: hidden;
}

.language-option {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 16px;
    text-decoration: none;
    color: #374151;
    transition: background 0.15s;
}

.language-option:hover {
    background: #f3f4f6;
}

.language-option.active {
    background: #eff6ff;
    color: #2563eb;
}

.flag-icon {
    font-size: 20px;
    line-height: 1;
}

.language-name {
    flex: 1;
    font-size: 14px;
    font-weight: 500;
}

.check-icon {
    color: #2563eb;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const toggle = document.getElementById('languageSwitcher');
    const dropdown = document.getElementById('languageDropdown');
    
    if (!toggle || !dropdown) return;
    
    toggle.addEventListener('click', function(e) {
        e.stopPropagation();
        const isExpanded = toggle.getAttribute('aria-expanded') === 'true';
        
        if (isExpanded) {
            toggle.setAttribute('aria-expanded', 'false');
            dropdown.setAttribute('hidden', 'true');
        } else {
            toggle.setAttribute('aria-expanded', 'true');
            dropdown.removeAttribute('hidden');
        }
    });
    
    // Close on outside click
    document.addEventListener('click', function(e) {
        if (!toggle.contains(e.target) && !dropdown.contains(e.target)) {
            toggle.setAttribute('aria-expanded', 'false');
            dropdown.setAttribute('hidden', 'true');
        }
    });
    
    // Close on escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            toggle.setAttribute('aria-expanded', 'false');
            dropdown.setAttribute('hidden', 'true');
        }
    });
});
</script>
