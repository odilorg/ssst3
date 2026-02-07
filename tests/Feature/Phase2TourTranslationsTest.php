<?php

namespace Tests\Feature;

use App\Models\Tour;
use App\Models\TourTranslation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Phase 2 Tour Translations Tests
 *
 * Tests for localized tour slugs, translation fallbacks, and strict locale routing.
 * These tests create their own data and enable multilang features via config.
 *
 * Usage: php artisan test --filter=Phase2TourTranslationsTest
 */
class Phase2TourTranslationsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test tour and translations created for each test.
     */
    protected Tour $tour;
    protected TourTranslation $enTranslation;
    protected TourTranslation $ruTranslation;

    /**
     * Enable multilang features and create test data for all tests.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Enable multilang features via config override
        config([
            'multilang.enabled' => true,
            'multilang.phases.routes' => true,
            'multilang.phases.tour_translations' => true,
            'multilang.features.locale_routing' => true,
            'multilang.features.tours_translations' => true,
            'multilang.features.localized_tour_slugs' => true,
        ]);

        // Create test tour
        $this->tour = Tour::factory()->create([
            'title' => 'Samarkand Day Trip',
            'slug' => 'samarkand-day-trip',
            'short_description' => 'Explore the ancient city of Samarkand',
            'long_description' => 'Full day tour of Samarkand including Registan Square.',
            'is_active' => true,
        ]);

        // Create English translation
        $this->enTranslation = TourTranslation::create([
            'tour_id' => $this->tour->id,
            'locale' => 'en',
            'title' => 'Samarkand Day Trip',
            'slug' => 'samarkand-day-trip-en',
            'excerpt' => 'Explore the ancient city of Samarkand',
            'content' => '<p>Full day tour of Samarkand including Registan Square.</p>',
        ]);

        // Create Russian translation
        $this->ruTranslation = TourTranslation::create([
            'tour_id' => $this->tour->id,
            'locale' => 'ru',
            'title' => 'Однодневная поездка в Самарканд',
            'slug' => 'samarkand-day-trip-ru',
            'excerpt' => 'Исследуйте древний город Самарканд',
            'content' => '<p>Полный день в Самарканде включая площадь Регистан.</p>',
        ]);
    }

    // ========================================
    // LOCALIZED TOUR ROUTES WITH TRANSLATIONS
    // ========================================

    /**
     * Test English localized tour page returns 200 and contains English title.
     */
    public function test_english_localized_tour_returns_200_with_english_title(): void
    {
        $response = $this->get("/en/tours/{$this->enTranslation->slug}");

        $response->assertStatus(200);
        $response->assertSee($this->enTranslation->title);
    }

    /**
     * Test Russian localized tour page returns 200 and contains Russian title.
     */
    public function test_russian_localized_tour_returns_200_with_russian_title(): void
    {
        $response = $this->get("/ru/tours/{$this->ruTranslation->slug}");

        $response->assertStatus(200);
        $response->assertSee($this->ruTranslation->title);
    }

    /**
     * Test strict locale routing: Russian URL with English slug returns 404.
     *
     * This is critical for SEO - each locale should only serve its own slugs.
     */
    public function test_russian_locale_with_english_slug_returns_404(): void
    {
        // Trying to access English slug from Russian route should fail
        $response = $this->get("/ru/tours/{$this->enTranslation->slug}");

        $response->assertStatus(404);
    }

    /**
     * Test strict locale routing: English URL with Russian slug returns 404.
     */
    public function test_english_locale_with_russian_slug_returns_404(): void
    {
        // Trying to access Russian slug from English route should fail
        $response = $this->get("/en/tours/{$this->ruTranslation->slug}");

        $response->assertStatus(404);
    }

    /**
     * Test French locale without translation returns 404.
     *
     * No French translation exists, so any French URL should 404.
     */
    public function test_french_locale_without_translation_returns_404(): void
    {
        $response = $this->get("/fr/tours/{$this->enTranslation->slug}");

        $response->assertStatus(404);
    }

    // ========================================
    // REGRESSION: ORIGINAL ROUTES STILL WORK
    // ========================================

    /**
     * Test original (non-localized) tour URL still returns 200.
     *
     * CRITICAL: Existing URLs must continue to work for SEO and bookmarks.
     */
    public function test_original_tour_route_still_works(): void
    {
        $response = $this->get("/tours/{$this->tour->slug}");

        $response->assertStatus(200);
    }

    /**
     * Test original tour URL shows tour title.
     */
    public function test_original_tour_route_shows_tour_title(): void
    {
        $response = $this->get("/tours/{$this->tour->slug}");

        $response->assertStatus(200);
        $response->assertSee($this->tour->title);
    }

    // ========================================
    // TRANSLATION CONTENT TESTS
    // ========================================

    /**
     * Test English page contains English content.
     */
    public function test_english_page_contains_english_content(): void
    {
        $response = $this->get("/en/tours/{$this->enTranslation->slug}");

        $response->assertStatus(200);
        $response->assertSee('Explore the ancient city of Samarkand');
    }

    /**
     * Test Russian page contains Russian content.
     */
    public function test_russian_page_contains_russian_content(): void
    {
        $response = $this->get("/ru/tours/{$this->ruTranslation->slug}");

        $response->assertStatus(200);
        $response->assertSee('Однодневная поездка в Самарканд');
    }

    // ========================================
    // DATABASE INTEGRITY TESTS
    // ========================================

    /**
     * Test tour has translations relationship.
     */
    public function test_tour_has_translations_relationship(): void
    {
        $tour = Tour::with('translations')->find($this->tour->id);

        $this->assertCount(2, $tour->translations);
    }

    /**
     * Test translation method returns correct locale.
     */
    public function test_translation_method_returns_correct_locale(): void
    {
        $tour = Tour::with('translations')->find($this->tour->id);

        $enTranslation = $tour->translation('en');
        $ruTranslation = $tour->translation('ru');

        $this->assertNotNull($enTranslation);
        $this->assertNotNull($ruTranslation);
        $this->assertEquals('en', $enTranslation->locale);
        $this->assertEquals('ru', $ruTranslation->locale);
    }

    /**
     * Test translationOrDefault returns translation for current locale.
     */
    public function test_translation_or_default_returns_current_locale(): void
    {
        app()->setLocale('ru');

        $tour = Tour::with('translations')->find($this->tour->id);
        $translation = $tour->translationOrDefault();

        $this->assertNotNull($translation);
        $this->assertEquals('ru', $translation->locale);
        $this->assertEquals('Однодневная поездка в Самарканд', $translation->title);
    }

    /**
     * Test translationOrDefault falls back to default locale.
     */
    public function test_translation_or_default_falls_back_to_default(): void
    {
        app()->setLocale('fr'); // No French translation exists

        $tour = Tour::with('translations')->find($this->tour->id);
        $translation = $tour->translationOrDefault();

        // Should fallback to English (default)
        $this->assertNotNull($translation);
        $this->assertEquals('en', $translation->locale);
    }

    /**
     * Test unique constraint on (tour_id, locale) prevents duplicates.
     */
    public function test_unique_constraint_prevents_duplicate_locale(): void
    {
        $this->expectException(\Illuminate\Database\QueryException::class);

        // Try to create another English translation for same tour
        TourTranslation::create([
            'tour_id' => $this->tour->id,
            'locale' => 'en', // Already exists
            'title' => 'Duplicate English',
            'slug' => 'duplicate-english-slug',
        ]);
    }

    /**
     * Test unique constraint on (locale, slug) prevents duplicate slugs.
     */
    public function test_unique_constraint_prevents_duplicate_slug_per_locale(): void
    {
        // Create another tour
        $anotherTour = Tour::factory()->create([
            'title' => 'Another Tour',
            'slug' => 'another-tour',
            'is_active' => true,
        ]);

        $this->expectException(\Illuminate\Database\QueryException::class);

        // Try to create translation with same slug for same locale
        TourTranslation::create([
            'tour_id' => $anotherTour->id,
            'locale' => 'en',
            'title' => 'Another Tour',
            'slug' => $this->enTranslation->slug, // Already exists for 'en'
        ]);
    }

    // ========================================
    // CONFIG FLAG TESTS
    // ========================================

    /**
     * Test tour_translations config flag is enabled.
     */
    public function test_tour_translations_config_flag_is_enabled(): void
    {
        $this->assertTrue(config('multilang.phases.tour_translations'));
    }

    /**
     * Test routes config flag is enabled.
     */
    public function test_routes_config_flag_is_enabled(): void
    {
        $this->assertTrue(config('multilang.phases.routes'));
    }
}
