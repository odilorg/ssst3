<div class="language-switcher">
    <div class="relative inline-block text-left">
        <button type="button" 
                class="inline-flex items-center justify-center w-full px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500"
                id="language-menu-button"
                aria-expanded="false"
                aria-haspopup="true"
                onclick="document.getElementById('language-menu').classList.toggle('hidden')">
            @switch(app()->getLocale())
                @case('ru')
                    🇷🇺 Русский
                    @break
                @case('uz')
                    🇺🇿 O'zbek
                    @break
                @default
                    🇬🇧 English
            @endswitch
            <svg class="w-5 h-5 ml-2 -mr-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
            </svg>
        </button>

        <div id="language-menu"
             class="hidden absolute right-0 z-10 mt-2 w-48 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
             role="menu"
             aria-orientation="vertical"
             aria-labelledby="language-menu-button">
            <div class="py-1" role="none">
                <a href="{{ request()->fullUrlWithQuery(['locale' => 'en']) }}"
                   class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ app()->getLocale() == 'en' ? 'bg-gray-50 font-semibold' : '' }}"
                   role="menuitem">
                    🇬🇧 English
                </a>
                <a href="{{ request()->fullUrlWithQuery(['locale' => 'ru']) }}"
                   class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ app()->getLocale() == 'ru' ? 'bg-gray-50 font-semibold' : '' }}"
                   role="menuitem">
                    🇷🇺 Русский
                </a>
                <a href="{{ request()->fullUrlWithQuery(['locale' => 'uz']) }}"
                   class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ app()->getLocale() == 'uz' ? 'bg-gray-50 font-semibold' : '' }}"
                   role="menuitem">
                    🇺🇿 O'zbek
                </a>
            </div>
        </div>
    </div>
</div>

<script>
// Close dropdown when clicking outside
document.addEventListener('click', function(event) {
    const menu = document.getElementById('language-menu');
    const button = document.getElementById('language-menu-button');
    
    if (!button.contains(event.target) && !menu.contains(event.target)) {
        menu.classList.add('hidden');
    }
});
</script>
