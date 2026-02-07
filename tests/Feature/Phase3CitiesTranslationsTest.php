<?php

namespace Tests\Feature;

use App\Models\City;
use App\Models\CityTranslation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Phase 3.1 Cities Translations Tests
 *
 * Tests for localized city routes using city_translations table.
 * These tests create their own data (do not rely on seeded DB).
 *
 * Usage: php artisan test --filter=Phase3CitiesTranslationsTest
 */
class Phase3CitiesTranslationsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Enable multilang and city_translations features for all tests.
     */
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'multilang.enabled' => true,
            'multilang.phases.routes' => true,
            'multilang.phases.city_translations' => true,
            'multilang.features.locale_routing' => true,
        ]);
    }

    /**
     * Create a city with translations for testing.
     */
    private function createCityWithTranslations(): City
    {
        // Create city
        $city = City::create([
            'name' => 'Test City',
            'slug' => 'test-city',
            'description' => 'Test city description',
            'is_active' => true,
            'is_featured' => false,
            'country' => 'Uzbekistan',
        ]);

        // Create English translation
        CityTranslation::create([
            'city_id' => $city->id,
            'locale' => 'en',
            'name' => 'Test City English',
            'slug' => 'test-city-en',
            'tagline' => 'English tagline',
            'description' => 'English description',
        ]);

        // Create Russian translation
        CityTranslation::create([
            'city_id' => $city->id,
            'locale' => 'ru',
            'name' => 'Тестовый Город',
            'slug' => 'testovyj-gorod',
            'tagline' => 'Русский слоган',
            'description' => 'Русское описание',
        ]);

        // Create French translation
        CityTranslation::create([
            'city_id' => $city->id,
            'locale' => 'fr',
            'name' => 'Ville Test',
            'slug' => 'ville-test',
            'tagline' => 'Slogan français',
            'description' => 'Description française',
        ]);

        return $city;
    }

    // ========================================
    // LOCALIZED CITY ROUTES
    // ========================================

    /**
     * Test English localized city returns 200 and contains English name.
     */
    public function test_localized_city_en_returns_200_with_english_name(): void
    {
        $city = $this->createCityWithTranslations();

        $response = $this->get('/en/destinations/test-city-en');

        $response->assertStatus(200);
        $response->assertSee('Test City English');
    }

    /**
     * Test Russian localized city returns 200 and contains Russian name.
     */
    public function test_localized_city_ru_returns_200_with_russian_name(): void
    {
        $city = $this->createCityWithTranslations();

        $response = $this->get('/ru/destinations/testovyj-gorod');

        $response->assertStatus(200);
        $response->assertSee('Тестовый Город');
    }

    /**
     * Test French localized city returns 200 and contains French name.
     */
    public function test_localized_city_fr_returns_200_with_french_name(): void
    {
        $city = $this->createCityWithTranslations();

        $response = $this->get('/fr/destinations/ville-test');

        $response->assertStatus(200);
        $response->assertSee('Ville Test');
    }

    // ========================================
    // STRICT 404 BEHAVIOR
    // ========================================

    /**
     * Test wrong locale returns 404 (strict lookup).
     *
     * Russian route should NOT find English slug.
     */
    public function test_wrong_locale_slug_combination_returns_404(): void
    {
        $city = $this->createCityWithTranslations();

        // Try to access English slug with Russian locale
        $response = $this->get('/ru/destinations/test-city-en');

        $response->assertStatus(404);
    }

    /**
     * Test non-existent slug returns 404.
     */
    public function test_nonexistent_slug_returns_404(): void
    {
        $city = $this->createCityWithTranslations();

        $response = $this->get('/en/destinations/nonexistent-city');

        $response->assertStatus(404);
    }

    /**
     * Test French locale with Russian slug returns 404.
     */
    public function test_fr_locale_with_ru_slug_returns_404(): void
    {
        $city = $this->createCityWithTranslations();

        $response = $this->get('/fr/destinations/testovyj-gorod');

        $response->assertStatus(404);
    }

    // ========================================
    // OLD ROUTES STILL WORK (REGRESSION)
    // ========================================

    /**
     * Test original (non-localized) destinations index still works.
     */
    public function test_original_destinations_index_still_works(): void
    {
        $response = $this->get('/destinations');

        $response->assertStatus(200);
    }

    /**
     * Test original (non-localized) city show still works.
     */
    public function test_original_city_show_still_works(): void
    {
        $city = City::create([
            'name' => 'Original City',
            'slug' => 'original-city',
            'is_active' => true,
            'country' => 'Uzbekistan',
        ]);

        $response = $this->get('/destinations/original-city');

        $response->assertStatus(200);
    }

    // ========================================
    // MODEL TRANSLATION METHODS
    // ========================================

    /**
     * Test City model translation() method returns correct translation.
     */
    public function test_city_translation_method_returns_correct_locale(): void
    {
        $city = $this->createCityWithTranslations();
        $city->load('translations');

        app()->setLocale('ru');
        $translation = $city->translation();

        $this->assertNotNull($translation);
        $this->assertEquals('ru', $translation->locale);
        $this->assertEquals('Тестовый Город', $translation->name);
    }

    /**
     * Test City model translationOrDefault() falls back correctly.
     */
    public function test_city_translation_or_default_falls_back(): void
    {
        $city = $this->createCityWithTranslations();
        $city->load('translations');

        // Set to a locale without translation (city only has en, ru, fr)
        app()->setLocale('de');

        // Should fall back to default locale (en)
        $translation = $city->translationOrDefault();

        $this->assertNotNull($translation);
        $this->assertEquals('en', $translation->locale);
    }

    // ========================================
    // DATABASE CONSTRAINTS
    // ========================================

    /**
     * Test unique constraint on (city_id, locale).
     */
    public function test_cannot_create_duplicate_locale_for_same_city(): void
    {
        $city = $this->createCityWithTranslations();

        $this->expectException(\Illuminate\Database\QueryException::class);

        // Try to create another English translation
        CityTranslation::create([
            'city_id' => $city->id,
            'locale' => 'en',
            'name' => 'Duplicate English',
            'slug' => 'duplicate-en',
        ]);
    }

    /**
     * Test unique constraint on (locale, slug).
     */
    public function test_cannot_create_duplicate_slug_for_same_locale(): void
    {
        $city = $this->createCityWithTranslations();

        // Create another city
        $city2 = City::create([
            'name' => 'Another City',
            'slug' => 'another-city',
            'is_active' => true,
            'country' => 'Uzbekistan',
        ]);

        $this->expectException(\Illuminate\Database\QueryException::class);

        // Try to use same slug in same locale
        CityTranslation::create([
            'city_id' => $city2->id,
            'locale' => 'en',
            'name' => 'Different Name',
            'slug' => 'test-city-en', // Same slug as first city's English translation
        ]);
    }
}
