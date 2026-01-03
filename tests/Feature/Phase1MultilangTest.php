<?php

namespace Tests\Feature;

use App\Models\Tour;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Phase 1 Multilingual Tests
 *
 * Tests for locale routing, UI translations, and language switching.
 * These tests use config overrides to enable multilang features.
 *
 * Usage: php artisan test --filter=Phase1MultilangTest
 */
class Phase1MultilangTest extends TestCase
{
    /**
     * Enable multilang features for all tests in this class.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Enable multilang features via config override
        config([
            'multilang.enabled' => true,
            'multilang.phases.routes' => true,
            'multilang.phases.ui_strings' => true,
            'multilang.features.locale_routing' => true,
            'multilang.features.language_switcher' => true,
            'multilang.features.ui_translations' => true,
        ]);
    }

    // ========================================
    // LOCALE ROUTING TESTS
    // ========================================

    /**
     * Test localized homepage returns 200 for supported locales.
     */
    public function test_localized_homepage_returns_200_for_supported_locales(): void
    {
        foreach (['en', 'ru', 'fr'] as $locale) {
            $response = $this->get("/{$locale}/");

            $response->assertStatus(200, "Localized homepage failed for locale: {$locale}");
        }
    }

    /**
     * Test localized mini-journeys page returns 200.
     */
    public function test_localized_mini_journeys_returns_200(): void
    {
        foreach (['en', 'ru', 'fr'] as $locale) {
            $response = $this->get("/{$locale}/mini-journeys");

            $response->assertStatus(200, "Mini-journeys failed for locale: {$locale}");
        }
    }

    /**
     * Test localized craft-journeys page returns 200.
     */
    public function test_localized_craft_journeys_returns_200(): void
    {
        foreach (['en', 'ru', 'fr'] as $locale) {
            $response = $this->get("/{$locale}/craft-journeys");

            $response->assertStatus(200, "Craft-journeys failed for locale: {$locale}");
        }
    }

    /**
     * Test localized tour detail page returns 200.
     */
    public function test_localized_tour_detail_returns_200(): void
    {
        $tour = Tour::where('is_active', true)->first();

        if (!$tour) {
            $this->markTestSkipped('No active tours in database');
        }

        foreach (['en', 'ru', 'fr'] as $locale) {
            $response = $this->get("/{$locale}/tours/{$tour->slug}");

            $response->assertStatus(200, "Tour detail failed for locale: {$locale}");
        }
    }

    /**
     * Test localized blog index returns 200.
     */
    public function test_localized_blog_index_returns_200(): void
    {
        foreach (['en', 'ru', 'fr'] as $locale) {
            $response = $this->get("/{$locale}/blog");

            $response->assertStatus(200, "Blog index failed for locale: {$locale}");
        }
    }

    /**
     * Test localized destinations index returns 200.
     */
    public function test_localized_destinations_index_returns_200(): void
    {
        foreach (['en', 'ru', 'fr'] as $locale) {
            $response = $this->get("/{$locale}/destinations");

            $response->assertStatus(200, "Destinations index failed for locale: {$locale}");
        }
    }

    /**
     * Test localized about page returns 200.
     */
    public function test_localized_about_page_returns_200(): void
    {
        foreach (['en', 'ru', 'fr'] as $locale) {
            $response = $this->get("/{$locale}/about");

            $response->assertStatus(200, "About page failed for locale: {$locale}");
        }
    }

    /**
     * Test localized contact page returns 200.
     */
    public function test_localized_contact_page_returns_200(): void
    {
        foreach (['en', 'ru', 'fr'] as $locale) {
            $response = $this->get("/{$locale}/contact");

            $response->assertStatus(200, "Contact page failed for locale: {$locale}");
        }
    }

    /**
     * Test invalid locale returns 404.
     */
    public function test_invalid_locale_returns_404(): void
    {
        $response = $this->get('/xx/');

        $response->assertStatus(404);
    }

    /**
     * Test unsupported locale returns 404.
     */
    public function test_unsupported_locale_returns_404(): void
    {
        // 'de' is not in our supported locales
        $response = $this->get('/de/');

        $response->assertStatus(404);
    }

    // ========================================
    // ORIGINAL ROUTES STILL WORK
    // ========================================

    /**
     * Test original (non-localized) homepage still works.
     *
     * This is critical - existing URLs must not break.
     */
    public function test_original_homepage_still_works(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    /**
     * Test original mini-journeys still works.
     */
    public function test_original_mini_journeys_still_works(): void
    {
        $response = $this->get('/mini-journeys');

        $response->assertStatus(200);
    }

    /**
     * Test original craft-journeys still works.
     */
    public function test_original_craft_journeys_still_works(): void
    {
        $response = $this->get('/craft-journeys');

        $response->assertStatus(200);
    }

    /**
     * Test original tour detail still works.
     */
    public function test_original_tour_detail_still_works(): void
    {
        $tour = Tour::where('is_active', true)->first();

        if (!$tour) {
            $this->markTestSkipped('No active tours in database');
        }

        $response = $this->get("/tours/{$tour->slug}");

        $response->assertStatus(200);
    }

    /**
     * Test original blog index still works.
     */
    public function test_original_blog_index_still_works(): void
    {
        $response = $this->get('/blog');

        $response->assertStatus(200);
    }

    // ========================================
    // LOCALE SET CORRECTLY
    // ========================================

    /**
     * Test app locale is set correctly for English routes.
     */
    public function test_app_locale_is_set_to_en(): void
    {
        $this->get('/en/');

        $this->assertEquals('en', app()->getLocale());
    }

    /**
     * Test app locale is set correctly for Russian routes.
     */
    public function test_app_locale_is_set_to_ru(): void
    {
        $this->get('/ru/');

        $this->assertEquals('ru', app()->getLocale());
    }

    /**
     * Test app locale is set correctly for French routes.
     */
    public function test_app_locale_is_set_to_fr(): void
    {
        $this->get('/fr/');

        $this->assertEquals('fr', app()->getLocale());
    }

    // ========================================
    // UI TRANSLATIONS
    // ========================================

    /**
     * Test UI translations exist and return correct values for English.
     */
    public function test_ui_translations_exist_for_en(): void
    {
        app()->setLocale('en');

        $this->assertEquals('Home', __('ui.nav.home'));
        $this->assertEquals('Tours', __('ui.nav.tours'));
        $this->assertEquals('Book Now', __('ui.buttons.book_now'));
        $this->assertEquals('Loading...', __('ui.common.loading'));
    }

    /**
     * Test UI translations exist and return correct values for Russian.
     */
    public function test_ui_translations_exist_for_ru(): void
    {
        app()->setLocale('ru');

        $this->assertEquals('Главная', __('ui.nav.home'));
        $this->assertEquals('Туры', __('ui.nav.tours'));
        $this->assertEquals('Забронировать', __('ui.buttons.book_now'));
        $this->assertEquals('Загрузка...', __('ui.common.loading'));
    }

    /**
     * Test UI translations exist and return correct values for French.
     */
    public function test_ui_translations_exist_for_fr(): void
    {
        app()->setLocale('fr');

        $this->assertEquals('Accueil', __('ui.nav.home'));
        $this->assertEquals('Circuits', __('ui.nav.tours'));
        $this->assertEquals('Réserver', __('ui.buttons.book_now'));
        $this->assertEquals('Chargement...', __('ui.common.loading'));
    }

    /**
     * Test all translation files have matching keys.
     */
    public function test_translation_files_have_matching_keys(): void
    {
        $enTranslations = include base_path('lang/en/ui.php');
        $ruTranslations = include base_path('lang/ru/ui.php');
        $frTranslations = include base_path('lang/fr/ui.php');

        // Get all nested keys
        $getKeys = function (array $array, string $prefix = ''): array {
            $keys = [];
            foreach ($array as $key => $value) {
                $fullKey = $prefix ? "{$prefix}.{$key}" : $key;
                if (is_array($value)) {
                    $keys = array_merge($keys, ($this->getNestedKeys)($value, $fullKey));
                } else {
                    $keys[] = $fullKey;
                }
            }
            return $keys;
        };

        // Use Closure binding for recursion
        $this->getNestedKeys = $getKeys;

        $enKeys = $getKeys($enTranslations);
        $ruKeys = $getKeys($ruTranslations);
        $frKeys = $getKeys($frTranslations);

        // Compare key counts
        $this->assertCount(
            count($enKeys),
            $ruKeys,
            'Russian translations should have same number of keys as English'
        );

        $this->assertCount(
            count($enKeys),
            $frKeys,
            'French translations should have same number of keys as English'
        );

        // Check all English keys exist in other languages
        foreach ($enKeys as $key) {
            $this->assertContains($key, $ruKeys, "Russian translations missing key: {$key}");
            $this->assertContains($key, $frKeys, "French translations missing key: {$key}");
        }
    }

    // ========================================
    // MIDDLEWARE TESTS
    // ========================================

    /**
     * Test middleware sets URL defaults for route helper.
     */
    public function test_middleware_sets_url_defaults(): void
    {
        $this->get('/ru/');

        // URL defaults should include locale
        $this->assertEquals('ru', \Illuminate\Support\Facades\URL::getDefaultParameters()['locale'] ?? null);
    }

    /**
     * Test view has locale variables shared.
     */
    public function test_view_has_locale_variables(): void
    {
        $response = $this->get('/fr/');

        // Check that view has access to locale variables
        $response->assertViewHas('currentLocale', 'fr');
        $response->assertViewHas('supportedLocales');
        $response->assertViewHas('localeNames');
    }

    // ========================================
    // CONFIG TESTS
    // ========================================

    /**
     * Test multilang config has required structure.
     */
    public function test_multilang_config_has_required_structure(): void
    {
        $this->assertNotNull(config('multilang.enabled'));
        $this->assertNotNull(config('multilang.locales'));
        $this->assertNotNull(config('multilang.default_locale'));
        $this->assertNotNull(config('multilang.phases'));
        $this->assertNotNull(config('multilang.features'));
        $this->assertNotNull(config('multilang.locale_names'));
    }

    /**
     * Test supported locales configuration.
     */
    public function test_supported_locales_config(): void
    {
        $locales = config('multilang.locales');

        $this->assertIsArray($locales);
        $this->assertContains('en', $locales);
        $this->assertContains('ru', $locales);
        $this->assertContains('fr', $locales);
    }

    /**
     * Test locale names have required fields.
     */
    public function test_locale_names_have_required_fields(): void
    {
        $localeNames = config('multilang.locale_names');

        foreach (['en', 'ru', 'fr'] as $locale) {
            $this->assertArrayHasKey($locale, $localeNames);
            $this->assertArrayHasKey('name', $localeNames[$locale]);
            $this->assertArrayHasKey('native', $localeNames[$locale]);
            $this->assertArrayHasKey('flag', $localeNames[$locale]);
        }
    }
}
