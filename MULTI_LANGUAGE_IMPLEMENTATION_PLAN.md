# Multi-Language Implementation Plan
## Jahongir Travel - Complete Step-by-Step Guide

**Date:** December 8, 2025
**Application:** Laravel 12 / Filament v4
**Target Languages:** English (en), Russian (ru), Uzbek (uz)
**Recommended Approach:** Spatie Laravel Translatable with JSON columns

---

## Phase 1: Package Installation & Configuration

### Step 1.1: Install Required Packages

```bash
composer require spatie/laravel-translatable:^6.0
composer require filament/spatie-laravel-translatable-plugin:^4.0
composer require --dev stichoza/google-translate-php:^5.0
```

### Step 1.2: Publish Configuration

```bash
php artisan vendor:publish --provider="Spatie\Translatable\TranslatableServiceProvider"
```

### Step 1.3: Update config/translatable.php

```php
return [
    'locales' => ['en', 'ru', 'uz'],
    'fallback_locale' => 'en',
];
```

### Step 1.4: Update config/app.php

```php
'locale' => 'en',
'fallback_locale' => 'en',
'available_locales' => ['en', 'ru', 'uz'],
```

---

## Phase 2: Database Migrations

### Step 2.1: Create Migration Files

Create 15 migration files for translatable fields:

```bash
php artisan make:migration add_translations_to_tours_table
php artisan make:migration add_translations_to_blog_posts_table
php artisan make:migration add_translations_to_cities_table
php artisan make:migration add_translations_to_hotels_table
php artisan make:migration add_translations_to_monuments_table
php artisan make:migration add_translations_to_restaurants_table
php artisan make:migration add_translations_to_transports_table
php artisan make:migration add_translations_to_guides_table
php artisan make:migration add_translations_to_itinerary_items_table
php artisan make:migration add_translations_to_tour_faqs_table
php artisan make:migration add_translations_to_tour_extras_table
php artisan make:migration add_translations_to_blog_categories_table
php artisan make:migration add_translations_to_meal_types_table
php artisan make:migration add_translations_to_room_types_table
php artisan make:migration add_translations_to_transport_types_table
```

### Step 2.2: Tours Table Migration (CRITICAL)

**File:** `database/migrations/YYYY_MM_DD_HHMMSS_add_translations_to_tours_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Add new JSON columns
        Schema::table('tours', function (Blueprint $table) {
            $table->json('title_translations')->nullable()->after('title');
            $table->json('short_description_translations')->nullable()->after('short_description');
            $table->json('long_description_translations')->nullable()->after('long_description');
            $table->json('seo_title_translations')->nullable()->after('seo_title');
            $table->json('seo_description_translations')->nullable()->after('seo_description');
            $table->json('seo_keywords_translations')->nullable()->after('seo_keywords');
        });

        // Migrate existing data to English (en)
        DB::table('tours')->get()->each(function ($tour) {
            DB::table('tours')
                ->where('id', $tour->id)
                ->update([
                    'title_translations' => json_encode(['en' => $tour->title]),
                    'short_description_translations' => json_encode(['en' => $tour->short_description]),
                    'long_description_translations' => json_encode(['en' => $tour->long_description]),
                    'seo_title_translations' => json_encode(['en' => $tour->seo_title]),
                    'seo_description_translations' => json_encode(['en' => $tour->seo_description]),
                    'seo_keywords_translations' => json_encode(['en' => $tour->seo_keywords]),
                ]);
        });

        // Make translation columns non-nullable
        Schema::table('tours', function (Blueprint $table) {
            $table->json('title_translations')->nullable(false)->change();
            $table->json('short_description_translations')->nullable(false)->change();
            $table->json('long_description_translations')->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('tours', function (Blueprint $table) {
            $table->dropColumn([
                'title_translations',
                'short_description_translations',
                'long_description_translations',
                'seo_title_translations',
                'seo_description_translations',
                'seo_keywords_translations',
            ]);
        });
    }
};
```

### Step 2.3: Similar Migrations for Other Models

Apply the same pattern for:
- **BlogPost:** title, excerpt, content, meta_title, meta_description
- **City:** name, tagline, short_description, long_description, seo_title, seo_description, seo_keywords
- **Hotel:** name, description
- **Monument:** name, description
- **Restaurant:** name, description
- **Transport:** name, description
- **Guide:** name, bio
- **ItineraryItem:** title, description
- **TourFaq:** question, answer
- **TourExtra:** name, description
- **BlogCategory:** name, description
- **MealType:** name
- **RoomType:** name
- **TransportType:** name

---

## Phase 3: Model Updates

### Step 3.1: Update Tour Model (CRITICAL)

**File:** `app/Models/Tour.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Tour extends Model
{
    use HasTranslations;

    public array $translatable = [
        'title',
        'short_description',
        'long_description',
        'seo_title',
        'seo_description',
        'seo_keywords',
    ];

    protected $fillable = [
        'title',
        'slug',
        'short_description',
        'long_description',
        'duration_days',
        'max_group_size',
        'base_price',
        'show_price',
        'seo_title',
        'seo_description',
        'seo_keywords',
        // ... other fields
    ];

    protected $casts = [
        'title' => 'array',
        'short_description' => 'array',
        'long_description' => 'array',
        'seo_title' => 'array',
        'seo_description' => 'array',
        'seo_keywords' => 'array',
        'base_price' => 'decimal:2',
        'show_price' => 'boolean',
    ];

    // Existing relationships...
}
```

### Step 3.2: Update Other Models

Apply `HasTranslations` trait to:
- BlogPost, City, Hotel, Monument, Restaurant, Transport
- Guide, ItineraryItem, TourFaq, TourExtra
- BlogCategory, MealType, RoomType, TransportType

---

## Phase 4: Middleware & Routes

### Step 4.1: Create SetLocaleMiddleware

**File:** `app/Http/Middleware/SetLocaleMiddleware.php`

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class SetLocaleMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $locale = $this->getLocale($request);
        
        if (!in_array($locale, config('app.available_locales'))) {
            $locale = config('app.fallback_locale');
        }
        
        App::setLocale($locale);
        Session::put('locale', $locale);
        
        return $next($request);
    }
    
    private function getLocale(Request $request): string
    {
        // 1. Check URL parameter
        if ($request->route('locale')) {
            return $request->route('locale');
        }
        
        // 2. Check session
        if (Session::has('locale')) {
            return Session::get('locale');
        }
        
        // 3. Check Accept-Language header
        return $request->getPreferredLanguage(config('app.available_locales')) 
            ?? config('app.fallback_locale');
    }
}
```

### Step 4.2: Register Middleware

**File:** `app/Http/Kernel.php`

```php
protected $middlewareGroups = [
    'web' => [
        // ... existing middleware
        \App\Http\Middleware\SetLocaleMiddleware::class,
    ],
];
```

### Step 4.3: Update Routes

**File:** `routes/web.php`

```php
Route::group(['prefix' => '{locale?}', 'where' => ['locale' => 'en|ru|uz']], function () {
    
    // Home
    Route::get('/', [HomeController::class, 'index'])->name('home');
    
    // Tours
    Route::prefix('tours')->name('tours.')->group(function () {
        Route::get('/', [TourController::class, 'index'])->name('index');
        Route::get('/{slug}', [TourController::class, 'show'])->name('show');
    });
    
    // Blog
    Route::prefix('blog')->name('blog.')->group(function () {
        Route::get('/', [BlogController::class, 'index'])->name('index');
        Route::get('/{slug}', [BlogController::class, 'show'])->name('show');
    });
    
    // Destinations
    Route::prefix('destinations')->name('destinations.')->group(function () {
        Route::get('/', [DestinationController::class, 'index'])->name('index');
        Route::get('/{slug}', [DestinationController::class, 'show'])->name('show');
    });
    
    // ... other routes
});

// Redirect root to default locale
Route::get('/', function () {
    return redirect(app()->getLocale());
});
```

---

## Phase 5: Filament Integration

### Step 5.1: Install Filament Translation Plugin

Already installed in Phase 1.1

### Step 5.2: Update Filament Panel Provider

**File:** `app/Providers/Filament/AdminPanelProvider.php`

```php
use Filament\SpatieLaravelTranslatablePlugin;

public function panel(Panel $panel): Panel
{
    return $panel
        // ... existing config
        ->plugin(
            SpatieLaravelTranslatablePlugin::make()
                ->defaultLocales(['en', 'ru', 'uz'])
        );
}
```

### Step 5.3: Update Tour Resource

**File:** `app/Filament/Resources/TourResource.php`

```php
<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Concerns\Translatable;
use Filament\Resources\Resource;

class TourResource extends Resource
{
    use Translatable;
    
    protected static ?string $model = Tour::class;
    
    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Tabs::make('Translations')
                ->tabs([
                    Forms\Components\Tabs\Tab::make('Content')
                        ->schema([
                            Forms\Components\TextInput::make('title')
                                ->required()
                                ->maxLength(255),
                            Forms\Components\Textarea::make('short_description')
                                ->required()
                                ->rows(3),
                            Forms\Components\RichEditor::make('long_description')
                                ->required()
                                ->columnSpanFull(),
                        ]),
                    Forms\Components\Tabs\Tab::make('SEO')
                        ->schema([
                            Forms\Components\TextInput::make('seo_title')
                                ->maxLength(60),
                            Forms\Components\Textarea::make('seo_description')
                                ->maxLength(160)
                                ->rows(3),
                            Forms\Components\TextInput::make('seo_keywords')
                                ->maxLength(255),
                        ]),
                    // ... other tabs
                ]),
        ]);
    }
    
    public static function getTranslatableLocales(): array
    {
        return ['en', 'ru', 'uz'];
    }
}
```

---

## Phase 6: Frontend Components

### Step 6.1: Create Language Switcher Component

**File:** `resources/views/components/language-switcher.blade.php`

```blade
<div class="language-switcher">
    <div class="dropdown">
        <button class="dropdown-toggle" type="button" id="languageSwitcher" data-bs-toggle="dropdown">
            <span class="flag-icon flag-icon-{{ app()->getLocale() }}"></span>
            {{ strtoupper(app()->getLocale()) }}
        </button>
        <ul class="dropdown-menu" aria-labelledby="languageSwitcher">
            @foreach(config('app.available_locales') as $locale)
                <li>
                    <a class="dropdown-item {{ app()->getLocale() === $locale ? 'active' : '' }}" 
                       href="{{ route(Route::currentRouteName(), array_merge(Route::current()->parameters(), ['locale' => $locale])) }}">
                        <span class="flag-icon flag-icon-{{ $locale }}"></span>
                        @switch($locale)
                            @case('en') English @break
                            @case('ru') Русский @break
                            @case('uz') O'zbek @break
                        @endswitch
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
</div>
```

### Step 6.2: Add to Layout

**File:** `resources/views/layouts/app.blade.php`

```blade
<nav class="navbar">
    <div class="container">
        <!-- ... existing nav items -->
        <x-language-switcher />
    </div>
</nav>
```

### Step 6.3: Update hreflang Tags

**File:** `resources/views/layouts/app.blade.php` (head section)

```blade
<head>
    <!-- ... existing meta tags -->
    
    @foreach(config('app.available_locales') as $locale)
        <link rel="alternate" 
              hreflang="{{ $locale }}" 
              href="{{ route(Route::currentRouteName(), array_merge(Route::current()->parameters(), ['locale' => $locale])) }}" />
    @endforeach
    
    <link rel="alternate" 
          hreflang="x-default" 
          href="{{ route(Route::currentRouteName(), array_merge(Route::current()->parameters(), ['locale' => 'en'])) }}" />
</head>
```

---

## Phase 7: Controller Updates

### Step 7.1: Update TourController

**File:** `app/Http/Controllers/TourController.php`

```php
<?php

namespace App\Http\Controllers;

use App\Models\Tour;
use Illuminate\Http\Request;

class TourController extends Controller
{
    public function index(Request $request)
    {
        $tours = Tour::with(['category', 'images'])
            ->where('is_active', true)
            ->paginate(12);
        
        return view('tours.index', compact('tours'));
    }
    
    public function show(string $slug)
    {
        $tour = Tour::with(['category', 'images', 'itineraries', 'faqs', 'extras'])
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();
        
        // Translations are automatically handled by Spatie package
        
        return view('tours.show', compact('tour'));
    }
}
```

**Note:** No changes needed for basic translation support. The Spatie package automatically handles locale switching.

---

## Phase 8: Helper Functions

### Step 8.1: Create Translation Helpers

**File:** `app/Helpers/TranslationHelper.php`

```php
<?php

namespace App\Helpers;

class TranslationHelper
{
    public static function getCurrentLocale(): string
    {
        return app()->getLocale();
    }
    
    public static function getAvailableLocales(): array
    {
        return config('app.available_locales');
    }
    
    public static function isRtl(string $locale = null): bool
    {
        $locale = $locale ?? app()->getLocale();
        return in_array($locale, ['ar', 'he', 'fa']);
    }
    
    public static function getLocalizedUrl(string $routeName, array $parameters = []): string
    {
        return route($routeName, array_merge(['locale' => app()->getLocale()], $parameters));
    }
    
    public static function switchLocale(string $newLocale): void
    {
        if (in_array($newLocale, config('app.available_locales'))) {
            session(['locale' => $newLocale]);
            app()->setLocale($newLocale);
        }
    }
}
```

---

## Phase 9: Testing

### Step 9.1: Create Feature Tests

**File:** `tests/Feature/LocalizationTest.php`

```php
<?php

namespace Tests\Feature;

use App\Models\Tour;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocalizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_tour_displays_in_english(): void
    {
        $tour = Tour::factory()->create([
            'title' => ['en' => 'Silk Road Tour', 'ru' => 'Тур по Шелковому пути'],
        ]);

        $response = $this->get('/en/tours/' . $tour->slug);

        $response->assertStatus(200);
        $response->assertSee('Silk Road Tour');
        $response->assertDontSee('Тур по Шелковому пути');
    }

    public function test_tour_displays_in_russian(): void
    {
        $tour = Tour::factory()->create([
            'title' => ['en' => 'Silk Road Tour', 'ru' => 'Тур по Шелковому пути'],
        ]);

        $response = $this->get('/ru/tours/' . $tour->slug);

        $response->assertStatus(200);
        $response->assertSee('Тур по Шелковому пути');
    }

    public function test_language_switcher_updates_locale(): void
    {
        $this->get('/en/tours');
        $this->assertEquals('en', app()->getLocale());

        $this->get('/ru/tours');
        $this->assertEquals('ru', app()->getLocale());
    }
}
```

### Step 9.2: Run Tests

```bash
php artisan test --filter=LocalizationTest
```

---

## Phase 10: Deployment Checklist

### Step 10.1: Pre-Deployment

- [ ] Full database backup created
- [ ] All migrations tested on staging
- [ ] Translation progress verified (at least English complete)
- [ ] Filament admin translation UI tested
- [ ] Frontend language switcher tested
- [ ] SEO hreflang tags verified
- [ ] Routes tested with all locale prefixes
- [ ] Performance benchmarks completed
- [ ] Test coverage ≥ 80%

### Step 10.2: Deployment Commands

```bash
# Backup database
php artisan backup:run --only-db

# Run migrations
php artisan migrate --force

# Clear caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Regenerate optimizations
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Update sitemap
php artisan sitemap:generate
```

### Step 10.3: Post-Deployment

- [ ] Verify all pages load correctly
- [ ] Test language switcher on live site
- [ ] Submit updated sitemap to Google
- [ ] Monitor error logs for 24 hours
- [ ] Check page load times
- [ ] Verify hreflang tags in production
- [ ] Test Filament admin translations

---

## Implementation Timeline

### Week 1
- **Day 1-2:** Package installation, configuration, database migrations
- **Day 3-4:** Model updates, middleware setup
- **Day 5:** Route configuration, Filament integration

### Week 2
- **Day 1-2:** Frontend components, language switcher
- **Day 3:** Controller updates, helper functions
- **Day 4:** Testing
- **Day 5:** Staging deployment, UAT

### Week 3
- **Day 1:** Production deployment
- **Day 2-5:** Monitoring, bug fixes, translation completion

---

## Troubleshooting

### Issue: Translations not displaying
- Check `app()->getLocale()` returns correct locale
- Verify JSON column contains data: `Tour::first()->title`
- Ensure `HasTranslations` trait is used in model
- Check `$translatable` array includes field

### Issue: Language switcher not working
- Verify middleware is registered
- Check route parameter name matches middleware
- Ensure session is working (check `config/session.php`)

### Issue: Filament shows wrong language
- Clear Filament cache: `php artisan filament:cache-clear`
- Check plugin configuration in panel provider
- Verify `getTranslatableLocales()` method exists

---

**END OF IMPLEMENTATION PLAN**
