# Multilingual Implementation Guide
## Laravel Tour Management System

**Date:** November 7, 2025
**Project:** D:\xampp82\htdocs\ssst3
**Target Languages:** English, Russian, Uzbek (+ optional: German, French, Spanish)

---

## Table of Contents
1. [Current State Analysis](#1-current-state-analysis)
2. [Recommended Architecture](#2-recommended-architecture)
3. [Implementation Plan](#3-implementation-plan)
4. [Code Examples](#4-code-examples)
5. [Database Schema](#5-database-schema)
6. [Frontend Integration](#6-frontend-integration)
7. [SEO Considerations](#7-seo-considerations)
8. [Testing Strategy](#8-testing-strategy)

---

## 1. Current State Analysis

### ✅ What's Already Multilingual

Your database **already supports** multiple languages for content:

```php
// From web.php:280-284
$locale = app()->getLocale();
$categoryName = $category->name[$locale] ?? $category->name['en'] ?? 'Category';
```

**Models with JSON translation columns:**
- `Tour`: `name`, `description`
- `TourCategory`: `name`, `description`, `meta_title`, `meta_description`
- `City`: Likely has translated fields
- Other content models

**Database Schema Example:**
```json
{
  "name": {
    "en": "Samarkand City Tour",
    "ru": "Тур по Самарканду",
    "uz": "Samarqand shahar sayohati"
  }
}
```

### ❌ What's NOT Multilingual Yet

1. **UI Strings**: Buttons, labels, error messages
   - "Book Now", "Contact Us", "Read More", etc.
   - Form validation messages
   - Success/error notifications

2. **Routes**: No URL localization
   - Currently: `/tours/samarkand-tour`
   - Could be: `/en/tours/samarkand-tour`, `/ru/tours/samarqand-tur`

3. **Language Switcher**: No UI component to change language

4. **Email Templates**: Fixed language emails

5. **Admin Panel**: Filament UI in English only

---

## 2. Recommended Architecture

### Option A: Lightweight Approach (RECOMMENDED)

**Use Case:** Small-medium projects, 2-5 languages

**Stack:**
1. Laravel's built-in localization for UI strings
2. Keep existing JSON columns for content
3. Session-based language detection
4. Query parameter for language switching (`?lang=ru`)

**Pros:**
- ✅ No external dependencies
- ✅ Works with existing schema
- ✅ Simple to implement
- ✅ Easy to maintain

**Cons:**
- ❌ No SEO-friendly multilingual URLs (unless using middleware)
- ❌ Manual language file management

**Estimated Effort:** 15-20 hours

---

### Option B: Spatie Package (RECOMMENDED for this project)

**Package:** `spatie/laravel-translatable`

**Use Case:** Projects already using JSON columns (like yours!)

**Installation:**
```bash
composer require spatie/laravel-translatable
```

**Usage:**
```php
use Spatie\Translatable\HasTranslations;

class Tour extends Model
{
    use HasTranslations;

    public $translatable = ['name', 'description'];

    // Get translation
    $tour->getTranslation('name', 'ru');

    // Set translation
    $tour->setTranslation('name', 'uz', 'Samarqand');
}
```

**Pros:**
- ✅ Works seamlessly with your JSON columns
- ✅ Clean API
- ✅ Fallback language support
- ✅ Well-maintained (10k+ stars)

**Cons:**
- ❌ External dependency
- ❌ Still need UI translation separately

**Estimated Effort:** 12-18 hours

---

### Option C: Full SEO Solution (For large projects)

**Package:** `mcamara/laravel-localization`

**Use Case:** Large projects, SEO-critical multilingual URLs

**Installation:**
```bash
composer require mcamara/laravel-localization
```

**Features:**
- URL localization: `/en/tours`, `/ru/туры`
- Automatic locale detection
- hreflang tag generation
- SEO-friendly

**Routes Example:**
```php
Route::group(['prefix' => LaravelLocalization::setLocale()], function() {
    Route::get('/', HomeController::class);
    Route::get('/tours', TourController::class);
});
```

**URLs Generated:**
- `/en/tours`
- `/ru/туры`
- `/uz/sayohatlar`

**Pros:**
- ✅ Best for SEO
- ✅ Professional URL structure
- ✅ Comprehensive solution

**Cons:**
- ❌ More complex setup
- ❌ Requires route rewrites
- ❌ May break existing URLs (needs redirects)

**Estimated Effort:** 20-25 hours

---

## 3. Implementation Plan

### Recommended: Hybrid Approach

**Best of both worlds for your project:**

```
Content (DB) → spatie/laravel-translatable (Already using JSON!)
UI Strings → Laravel's built-in localization
Language Detection → Custom middleware + session
URLs → Optional: Keep current, add ?lang= parameter
```

---

### Phase 1: Setup (2-3 hours)

#### Step 1.1: Install Spatie Package
```bash
composer require spatie/laravel-translatable
```

#### Step 1.2: Update Models
```php
// app/Models/Tour.php
use Spatie\Translatable\HasTranslations;

class Tour extends Model
{
    use HasTranslations;

    public $translatable = [
        'name',
        'description',
        'short_description',
        'seo_title',
        'seo_description'
    ];
}
```

#### Step 1.3: Create Language Files
```bash
php artisan lang:publish
```

Create:
- `resources/lang/en.json`
- `resources/lang/ru.json`
- `resources/lang/uz.json`

---

### Phase 2: UI Translations (4-6 hours)

#### Step 2.1: Extract UI Strings

Create `resources/lang/en.json`:
```json
{
  "Home": "Home",
  "About": "About",
  "Tours": "Tours",
  "Blog": "Blog",
  "Contact": "Contact",
  "Book Now": "Book Now",
  "View Details": "View Details",
  "Read More": "Read More",
  "Search Tours": "Search Tours",
  "Filter by City": "Filter by City",
  "Select Date": "Select Date",
  "Number of Guests": "Number of Guests",
  "Name": "Name",
  "Email": "Email",
  "Phone": "Phone",
  "Submit": "Submit",
  "Send Inquiry": "Send Inquiry",
  "Success!": "Success!",
  "Error": "Error",
  "Loading...": "Loading...",
  "From": "From",
  "per person": "per person",
  "Duration": "Duration",
  "days": "days",
  "Max Group Size": "Max Group Size",
  "people": "people"
}
```

Create `resources/lang/ru.json`:
```json
{
  "Home": "Главная",
  "About": "О нас",
  "Tours": "Туры",
  "Blog": "Блог",
  "Contact": "Контакты",
  "Book Now": "Забронировать",
  "View Details": "Подробнее",
  "Read More": "Читать далее",
  "Search Tours": "Поиск туров",
  "Filter by City": "Фильтр по городу",
  "Select Date": "Выберите дату",
  "Number of Guests": "Количество гостей",
  "Name": "Имя",
  "Email": "Email",
  "Phone": "Телефон",
  "Submit": "Отправить",
  "Send Inquiry": "Отправить запрос",
  "Success!": "Успешно!",
  "Error": "Ошибка",
  "Loading...": "Загрузка...",
  "From": "От",
  "per person": "на человека",
  "Duration": "Длительность",
  "days": "дней",
  "Max Group Size": "Макс. размер группы",
  "people": "человек"
}
```

Create `resources/lang/uz.json`:
```json
{
  "Home": "Bosh sahifa",
  "About": "Biz haqimizda",
  "Tours": "Sayohatlar",
  "Blog": "Blog",
  "Contact": "Aloqa",
  "Book Now": "Buyurtma berish",
  "View Details": "Batafsil",
  "Read More": "Davomini o'qish",
  "Search Tours": "Sayohatlarni qidirish",
  "Filter by City": "Shahar bo'yicha filtr",
  "Select Date": "Sanani tanlang",
  "Number of Guests": "Mehmonlar soni",
  "Name": "Ism",
  "Email": "Email",
  "Phone": "Telefon",
  "Submit": "Yuborish",
  "Send Inquiry": "So'rov yuborish",
  "Success!": "Muvaffaqiyatli!",
  "Error": "Xatolik",
  "Loading...": "Yuklanmoqda...",
  "From": "dan",
  "per person": "kishi uchun",
  "Duration": "Davomiyligi",
  "days": "kunlar",
  "Max Group Size": "Maks. guruh hajmi",
  "people": "kishi"
}
```

#### Step 2.2: Update Blade Templates

**Before:**
```html
<button>Book Now</button>
```

**After:**
```html
<button>{{ __('Book Now') }}</button>
```

---

### Phase 3: Language Detection Middleware (3-4 hours)

#### Step 3.1: Create Middleware

```bash
php artisan make:middleware SetLocale
```

```php
// app/Http/Middleware/SetLocale.php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class SetLocale
{
    /**
     * Supported languages
     */
    protected $supportedLocales = ['en', 'ru', 'uz'];

    /**
     * Default locale
     */
    protected $defaultLocale = 'en';

    public function handle(Request $request, Closure $next)
    {
        // Priority 1: URL parameter ?lang=ru
        if ($request->has('lang')) {
            $locale = $request->get('lang');
            if (in_array($locale, $this->supportedLocales)) {
                Session::put('locale', $locale);
                App::setLocale($locale);
            }
        }

        // Priority 2: Session
        elseif (Session::has('locale')) {
            $locale = Session::get('locale');
            if (in_array($locale, $this->supportedLocales)) {
                App::setLocale($locale);
            }
        }

        // Priority 3: Browser Accept-Language header
        elseif ($request->hasHeader('Accept-Language')) {
            $browserLang = substr($request->header('Accept-Language'), 0, 2);
            if (in_array($browserLang, $this->supportedLocales)) {
                App::setLocale($browserLang);
            }
        }

        // Fallback: Default locale
        else {
            App::setLocale($this->defaultLocale);
        }

        return $next($request);
    }
}
```

#### Step 3.2: Register Middleware

```php
// bootstrap/app.php (Laravel 11)
->withMiddleware(function (Middleware $middleware) {
    $middleware->web(append: [
        \App\Http\Middleware\SetLocale::class,
    ]);
})
```

---

### Phase 4: Language Switcher Component (2-3 hours)

#### Step 4.1: Create Blade Component

```bash
php artisan make:component LanguageSwitcher
```

```php
// app/View/Components/LanguageSwitcher.php
<?php

namespace App\View\Components;

use Illuminate\View\Component;

class LanguageSwitcher extends Component
{
    public $currentLocale;
    public $languages;

    public function __construct()
    {
        $this->currentLocale = app()->getLocale();
        $this->languages = [
            'en' => ['name' => 'English', 'flag' => '🇬🇧'],
            'ru' => ['name' => 'Русский', 'flag' => '🇷🇺'],
            'uz' => ['name' => 'O\'zbekcha', 'flag' => '🇺🇿'],
        ];
    }

    public function render()
    {
        return view('components.language-switcher');
    }
}
```

#### Step 4.2: Create Component View

```blade
<!-- resources/views/components/language-switcher.blade.php -->
<div class="language-switcher">
    <button class="lang-toggle" aria-label="Select language">
        {{ $languages[$currentLocale]['flag'] }}
        {{ $languages[$currentLocale]['name'] }}
        <svg class="chevron" width="12" height="12" viewBox="0 0 12 12">
            <path d="M2 4l4 4 4-4" stroke="currentColor" fill="none"/>
        </svg>
    </button>

    <div class="lang-menu" hidden>
        @foreach ($languages as $code => $lang)
            <a href="{{ request()->fullUrlWithQuery(['lang' => $code]) }}"
               class="lang-option {{ $code === $currentLocale ? 'active' : '' }}">
                {{ $lang['flag'] }} {{ $lang['name'] }}
            </a>
        @endforeach
    </div>
</div>

<style>
.language-switcher {
    position: relative;
    display: inline-block;
}

.lang-toggle {
    padding: 8px 16px;
    border: 1px solid #ddd;
    border-radius: 4px;
    background: white;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 8px;
}

.lang-menu {
    position: absolute;
    top: 100%;
    right: 0;
    margin-top: 4px;
    background: white;
    border: 1px solid #ddd;
    border-radius: 4px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    min-width: 150px;
    z-index: 1000;
}

.lang-option {
    display: block;
    padding: 10px 16px;
    text-decoration: none;
    color: #333;
}

.lang-option:hover {
    background: #f5f5f5;
}

.lang-option.active {
    background: #e3f2fd;
    font-weight: bold;
}
</style>

<script>
document.querySelector('.lang-toggle').addEventListener('click', function() {
    const menu = document.querySelector('.lang-menu');
    menu.hidden = !menu.hidden;
});

// Close menu when clicking outside
document.addEventListener('click', function(event) {
    const switcher = document.querySelector('.language-switcher');
    if (!switcher.contains(event.target)) {
        document.querySelector('.lang-menu').hidden = true;
    }
});
</script>
```

#### Step 4.3: Add to Layout

```blade
<!-- In your header partial or layout -->
<header>
    <nav>
        <!-- Your existing navigation -->
    </nav>

    <!-- Add language switcher -->
    <x-language-switcher />
</header>
```

---

### Phase 5: Update Routes & Controllers (3-4 hours)

#### Step 5.1: Update Homepage Route

```php
// routes/web.php
Route::get('/', function () {
    $locale = app()->getLocale();

    // Get homepage categories with translations
    $categories = \App\Models\TourCategory::getHomepageCategories();

    // Apply translations for current locale
    $categories->each(function ($category) use ($locale) {
        $category->translated_name = $category->getTranslation('name', $locale);
        $category->translated_description = $category->getTranslation('description', $locale);
    });

    // ... rest of your code
});
```

#### Step 5.2: Update Tour Controller

```php
// app/Http/Controllers/Partials/TourController.php
public function hero($slug)
{
    $locale = app()->getLocale();
    $tour = Tour::where('slug', $slug)->firstOrFail();

    // Get translated content
    $tourData = [
        'title' => $tour->getTranslation('name', $locale),
        'description' => $tour->getTranslation('description', $locale),
        // ... other fields
    ];

    return view('partials.tours.show.hero', compact('tourData'));
}
```

---

### Phase 6: Email Translations (2-3 hours)

#### Step 6.1: Create Translatable Email Templates

```blade
<!-- resources/views/emails/bookings/confirmation.blade.php -->
<h1>{{ __('Booking Confirmation') }}</h1>

<p>{{ __('Dear :name,', ['name' => $customer->name]) }}</p>

<p>{{ __('Thank you for booking with us!') }}</p>

<p><strong>{{ __('Booking Reference') }}:</strong> {{ $booking->reference }}</p>
<p><strong>{{ __('Tour') }}:</strong> {{ $tour->getTranslation('name', app()->getLocale()) }}</p>
<!-- ... -->
```

#### Step 6.2: Set Email Locale

```php
// app/Mail/BookingConfirmation.php
public function build()
{
    $locale = $this->customer->preferred_language ?? 'en';

    return $this->locale($locale)
        ->subject(__('Booking Confirmation'))
        ->view('emails.bookings.confirmation');
}
```

---

## 4. Code Examples

### Example 1: Translated Tour Listing

```php
// Controller
public function index()
{
    $locale = app()->getLocale();

    $tours = Tour::where('is_active', true)
        ->get()
        ->map(function ($tour) use ($locale) {
            return [
                'id' => $tour->id,
                'slug' => $tour->slug,
                'name' => $tour->getTranslation('name', $locale),
                'description' => $tour->getTranslation('short_description', $locale),
                'price' => $tour->price_per_person,
                'currency' => 'USD',
            ];
        });

    return view('tours.index', compact('tours'));
}
```

### Example 2: Translated Category Menu

```blade
<!-- Navigation -->
<nav>
    @foreach ($categories as $category)
        <a href="/tours/category/{{ $category->slug }}">
            {{ $category->getTranslation('name', app()->getLocale()) }}
        </a>
    @endforeach
</nav>
```

### Example 3: Form Validation Messages

```php
// In controller or form request
$messages = [
    'name.required' => __('validation.required', ['attribute' => __('Name')]),
    'email.required' => __('validation.required', ['attribute' => __('Email')]),
    'email.email' => __('validation.email', ['attribute' => __('Email')]),
];

$validator = Validator::make($request->all(), $rules, $messages);
```

---

## 5. Database Schema

### Your Current Schema (Already Good!)

```php
// Migration example (already exists)
Schema::create('tours', function (Blueprint $table) {
    $table->id();
    $table->json('name');  // {"en": "...", "ru": "...", "uz": "..."}
    $table->json('description');
    $table->string('slug')->unique();
    // ... other columns
});
```

### No Changes Needed!
Your database is already set up for multilingual content. Just need to add:

```php
// Optional: Add user language preference
Schema::table('customers', function (Blueprint $table) {
    $table->string('preferred_language', 2)->default('en')->after('country');
});
```

---

## 6. Frontend Integration

### Update Static HTML Files

**Before** (`public/index.html`):
```html
<button class="btn-primary">Book Now</button>
<h2>Popular Tours</h2>
```

**After**:
```html
<button class="btn-primary" data-translate="Book Now">Book Now</button>
<h2 data-translate="Popular Tours">Popular Tours</h2>

<script>
// Client-side translation (for static HTML)
const translations = {
    en: { "Book Now": "Book Now", "Popular Tours": "Popular Tours" },
    ru: { "Book Now": "Забронировать", "Popular Tours": "Популярные туры" },
    uz: { "Book Now": "Buyurtma berish", "Popular Tours": "Mashhur sayohatlar" }
};

function translatePage(lang) {
    document.querySelectorAll('[data-translate]').forEach(el => {
        const key = el.getAttribute('data-translate');
        if (translations[lang] && translations[lang][key]) {
            el.textContent = translations[lang][key];
        }
    });
}

// Get language from URL or session
const urlParams = new URLSearchParams(window.location.search);
const lang = urlParams.get('lang') || localStorage.getItem('language') || 'en';
translatePage(lang);
</script>
```

### HTMX Integration

```html
<!-- Language switcher that reloads partials -->
<select id="lang-switcher" hx-get="/partials/tours"
        hx-trigger="change" hx-target="#tours-container">
    <option value="en" selected>English</option>
    <option value="ru">Русский</option>
    <option value="uz">O'zbekcha</option>
</select>

<script>
document.getElementById('lang-switcher').addEventListener('change', function(e) {
    // Update URL with language parameter
    const lang = e.target.value;
    const url = new URL(window.location);
    url.searchParams.set('lang', lang);
    window.history.pushState({}, '', url);

    // Store in localStorage
    localStorage.setItem('language', lang);
});
</script>
```

---

## 7. SEO Considerations

### hreflang Tags

Add to your main layout or HTML head:

```blade
<link rel="alternate" hreflang="en" href="{{ url('en/tours/' . $tour->slug) }}" />
<link rel="alternate" hreflang="ru" href="{{ url('ru/tours/' . $tour->slug) }}" />
<link rel="alternate" hreflang="uz" href="{{ url('uz/tours/' . $tour->slug) }}" />
<link rel="alternate" hreflang="x-default" href="{{ url('tours/' . $tour->slug) }}" />
```

### Sitemap Generation

```php
// routes/web.php
Route::get('/sitemap.xml', function () {
    $tours = Tour::where('is_active', true)->get();
    $locales = ['en', 'ru', 'uz'];

    $xml = '<?xml version="1.0" encoding="UTF-8"?>';
    $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
                    xmlns:xhtml="http://www.w3.org/1999/xhtml">';

    foreach ($tours as $tour) {
        foreach ($locales as $locale) {
            $xml .= '<url>';
            $xml .= '<loc>' . url("/{$locale}/tours/{$tour->slug}") . '</loc>';

            // Add alternate language links
            foreach ($locales as $altLocale) {
                $xml .= '<xhtml:link rel="alternate" hreflang="' . $altLocale . '"
                                href="' . url("/{$altLocale}/tours/{$tour->slug}") . '" />';
            }

            $xml .= '<lastmod>' . $tour->updated_at->toAtomString() . '</lastmod>';
            $xml .= '<priority>0.8</priority>';
            $xml .= '</url>';
        }
    }

    $xml .= '</urlset>';

    return response($xml)->header('Content-Type', 'application/xml');
});
```

---

## 8. Testing Strategy

### Manual Testing Checklist

- [ ] Language switcher changes URL parameter
- [ ] All UI strings translate correctly
- [ ] Tour names show in selected language
- [ ] Fallback to English when translation missing
- [ ] Email sent in user's preferred language
- [ ] Form validation messages in correct language
- [ ] Admin panel still works (remains in English)
- [ ] Browser back/forward buttons work with language
- [ ] Search works in all languages
- [ ] Booking confirmation page shows correct language

### Automated Testing

```php
// tests/Feature/MultilingualTest.php
public function test_language_switcher_changes_locale()
{
    $response = $this->get('/?lang=ru');

    $this->assertEquals('ru', app()->getLocale());
    $response->assertSee('Главная'); // Russian for "Home"
}

public function test_tour_displays_in_russian()
{
    $tour = Tour::factory()->create([
        'name' => [
            'en' => 'Samarkand Tour',
            'ru' => 'Тур по Самарканду',
        ],
    ]);

    $response = $this->get("/tours/{$tour->slug}?lang=ru");

    $response->assertSee('Тур по Самарканду');
    $response->assertDontSee('Samarkand Tour');
}
```

---

## 9. Filament Admin Panel Translation

### Option 1: Keep Admin in English
- Simplest approach
- Most practical for internal team
- No changes needed

### Option 2: Translate Filament UI

```bash
php artisan filament:install-translations ru
```

```php
// config/app.php
'locale' => 'en',
'fallback_locale' => 'en',
'available_locales' => ['en', 'ru', 'uz'],
```

---

## 10. Implementation Checklist

### Week 1: Setup & Infrastructure
- [ ] Install spatie/laravel-translatable
- [ ] Update all models with HasTranslations trait
- [ ] Create SetLocale middleware
- [ ] Register middleware
- [ ] Create language files (en.json, ru.json, uz.json)
- [ ] Extract 50-100 common UI strings

### Week 2: Frontend Integration
- [ ] Create LanguageSwitcher component
- [ ] Add language switcher to header
- [ ] Update blade templates with __() helper
- [ ] Update static HTML files with data-translate
- [ ] Add client-side translation script
- [ ] Test all pages

### Week 3: Content & Email
- [ ] Translate remaining UI strings
- [ ] Verify all tour content has translations in DB
- [ ] Update email templates
- [ ] Add language preference to customers table
- [ ] Test email in all languages
- [ ] Add validation message translations

### Week 4: SEO & Polish
- [ ] Add hreflang tags
- [ ] Generate multilingual sitemap
- [ ] Test with Google Search Console
- [ ] Performance optimization
- [ ] Final QA testing
- [ ] Documentation

---

## 11. Cost Estimate

### Development Time
- **Setup & Infrastructure:** 6-8 hours
- **UI String Translation:** 8-10 hours (including extraction)
- **Frontend Integration:** 10-12 hours
- **Content Verification:** 4-6 hours
- **Email Templates:** 4-6 hours
- **SEO Implementation:** 4-6 hours
- **Testing & QA:** 6-8 hours
- **Documentation:** 2-3 hours

**Total:** 44-59 hours (~1.5-2 weeks full-time)

### Translation Costs
- **Russian Translation:** $0.10-0.15 per word
- **Uzbek Translation:** $0.08-0.12 per word
- Estimated 2,000-3,000 words of UI content
- **Total:** $400-$800 for professional translation

### Optional: Additional Languages
- German, French, Spanish: $500-700 each

---

## 12. Maintenance

### Ongoing Tasks
1. **New Content:** Ensure translations for new tours/blogs
2. **UI Updates:** Translate new UI strings
3. **Quality Check:** Review translations quarterly
4. **SEO Monitoring:** Track multilingual SEO performance

### Tools Recommendation
- **Translation Memory:** Use Crowdin or POEditor
- **Monitoring:** Google Analytics with language segments
- **Testing:** Automated tests for each language

---

## 13. Quick Start Commands

```bash
# 1. Install package
composer require spatie/laravel-translatable

# 2. Publish language files
php artisan lang:publish

# 3. Create middleware
php artisan make:middleware SetLocale

# 4. Create component
php artisan make:component LanguageSwitcher

# 5. Clear caches
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# 6. Test
php artisan serve
# Visit: http://localhost:8000?lang=ru
```

---

## 14. Troubleshooting

### Issue: Translations not showing
**Solution:** Clear cache
```bash
php artisan cache:clear
php artisan config:clear
```

### Issue: JSON column not working
**Solution:** Ensure DB column is JSON type
```php
$table->json('name'); // Not TEXT or VARCHAR
```

### Issue: Fallback not working
**Solution:** Check fallback locale in config
```php
// config/app.php
'fallback_locale' => 'en',
```

---

## 15. Resources

### Documentation
- [Laravel Localization](https://laravel.com/docs/11.x/localization)
- [Spatie Laravel Translatable](https://github.com/spatie/laravel-translatable)
- [Laravel Localization Package](https://github.com/mcamara/laravel-localization)

### Translation Services
- [Crowdin](https://crowdin.com/) - Translation management platform
- [POEditor](https://poeditor.com/) - Localization management
- [DeepL API](https://www.deepl.com/pro-api) - Machine translation (better than Google)

### SEO Resources
- [Google Multilingual SEO Guide](https://developers.google.com/search/docs/advanced/crawling/localized-versions)
- [hreflang Tag Generator](https://www.aleydasolis.com/english/international-seo-tools/hreflang-tags-generator/)

---

**Guide Created:** November 7, 2025
**Last Updated:** November 7, 2025
**Next Review:** After Phase 1 implementation
