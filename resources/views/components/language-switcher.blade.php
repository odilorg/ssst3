@php
    use App\Models\Language;

    $languages = Language::getActive();
    $currentLocale = app()->getLocale();
@endphp

<div class="language-switcher">
    <div class="dropdown">
        <button class="dropdown-toggle" type="button" id="languageDropdown" data-bs-toggle="dropdown" aria-expanded="false">
            @php
                $current = $languages->firstWhere('code', $currentLocale);
            @endphp
            @if($current)
                <span class="flag">{{ $current->flag }}</span>
                <span class="language-name">{{ $current->native_name }}</span>
            @else
                <span>Language</span>
            @endif
            <svg class="dropdown-arrow" width="12" height="12" viewBox="0 0 12 12" fill="none">
                <path d="M2.5 4.5L6 8L9.5 4.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </button>
        <ul class="dropdown-menu" aria-labelledby="languageDropdown">
            @foreach($languages as $language)
                <li>
                    <a class="dropdown-item {{ $language->code === $currentLocale ? 'active' : '' }}"
                       href="{{ url()->current() }}?lang={{ $language->code }}">
                        <span class="flag">{{ $language->flag }}</span>
                        <span class="language-name">{{ $language->native_name }}</span>
                        @if($language->code === $currentLocale)
                            <svg class="check-icon" width="16" height="16" viewBox="0 0 16 16" fill="none">
                                <path d="M13.3333 4L6 11.3333L2.66667 8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        @endif
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
</div>

<style>
.language-switcher {
    position: relative;
    display: inline-block;
}

.language-switcher .dropdown-toggle {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 16px;
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    cursor: pointer;
    font-size: 14px;
    font-weight: 500;
    transition: all 0.2s;
}

.language-switcher .dropdown-toggle:hover {
    border-color: #d1d5db;
    background: #f9fafb;
}

.language-switcher .flag {
    font-size: 20px;
    line-height: 1;
}

.language-switcher .language-name {
    color: #374151;
}

.language-switcher .dropdown-arrow {
    margin-left: 4px;
    transition: transform 0.2s;
}

.language-switcher .dropdown.show .dropdown-arrow {
    transform: rotate(180deg);
}

.language-switcher .dropdown-menu {
    min-width: 200px;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    padding: 4px;
    margin-top: 8px;
}

.language-switcher .dropdown-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px 12px;
    border-radius: 6px;
    cursor: pointer;
    transition: background 0.2s;
    text-decoration: none;
    color: #374151;
}

.language-switcher .dropdown-item:hover {
    background: #f3f4f6;
}

.language-switcher .dropdown-item.active {
    background: #eff6ff;
    color: #2563eb;
    font-weight: 500;
}

.language-switcher .dropdown-item .check-icon {
    margin-left: auto;
    color: #2563eb;
}
</style>
