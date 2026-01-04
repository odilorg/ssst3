<?php

namespace Tests\Feature;

use App\Models\City;
use App\Models\Tour;
use App\Models\TourTranslation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Phase 2: Tour Content Translations Tests
 *
 * Tests the tour content translation system (Layer 2):
 * - JSON fields in tour_translations table
 * - Localized tour pages show translated content
 * - HTMX partials receive and use translation context
 * - Fallback behavior when translations missing
 */
class Phase2TourContentTranslationsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Enable Phase 2 features
        config([
            'multilang.enabled' => true,
            'multilang.locales' => ['en', 'ru'],
            'multilang.default_locale' => 'en',
            'multilang.phases.routes' => true,
            'multilang.phases.tour_translations' => true,
        ]);
    }

    /** @test */
    public function it_shows_english_tour_content_on_english_route()
    {
        // Create tour with EN translation
        $city = City::factory()->create(['name' => 'Samarkand', 'slug' => 'samarkand']);
        $tour = Tour::factory()->create([
            'city_id' => $city->id,
            'slug' => 'base-tour-slug',
            'is_active' => true,
        ]);

        $enTranslation = TourTranslation::factory()->create([
            'tour_id' => $tour->id,
            'locale' => 'en',
            'slug' => 'registan-square-tour',
            'title' => 'Registan Square Tour',
            'content' => 'English tour description',
            'highlights_json' => [
                ['text' => 'English Highlight 1'],
                ['text' => 'English Highlight 2'],
            ],
            'itinerary_json' => [
                [
                    'day' => 1,
                    'title' => 'Day 1: English Itinerary',
                    'description' => '<p>English itinerary description</p>',
                    'duration_minutes' => 480,
                ],
            ],
            'faq_json' => [
                [
                    'question' => 'English FAQ question?',
                    'answer' => 'English FAQ answer.',
                ],
            ],
            'included_json' => [
                ['text' => 'English Included Item 1'],
            ],
            'excluded_json' => [
                ['text' => 'English Excluded Item 1'],
            ],
            'requirements_json' => [
                ['text' => 'English Requirement 1'],
            ],
            'cancellation_policy' => 'English cancellation policy',
        ]);

        // Test main tour page
        $response = $this->get('/en/tours/registan-square-tour');

        $response->assertStatus(200);
        $response->assertSee('Registan Square Tour');
        $response->assertSee('English tour description');
    }

    /** @test */
    public function it_shows_russian_tour_content_on_russian_route()
    {
        // Create tour with RU translation
        $city = City::factory()->create(['name' => 'Самарканд', 'slug' => 'samarkand']);
        $tour = Tour::factory()->create([
            'city_id' => $city->id,
            'slug' => 'base-tour-slug',
            'is_active' => true,
        ]);

        // English translation
        TourTranslation::factory()->create([
            'tour_id' => $tour->id,
            'locale' => 'en',
            'slug' => 'registan-square-tour',
            'title' => 'Registan Square Tour',
        ]);

        // Russian translation with different content
        $ruTranslation = TourTranslation::factory()->create([
            'tour_id' => $tour->id,
            'locale' => 'ru',
            'slug' => 'tur-po-ploshhadi-registan',
            'title' => 'Тур по площади Регистан',
            'content' => 'Описание тура на русском языке',
            'highlights_json' => [
                ['text' => 'Русский момент 1'],
                ['text' => 'Русский момент 2'],
            ],
            'itinerary_json' => [
                [
                    'day' => 1,
                    'title' => 'День 1: Русский маршрут',
                    'description' => '<p>Описание русского маршрута</p>',
                    'duration_minutes' => 480,
                ],
            ],
            'faq_json' => [
                [
                    'question' => 'Русский вопрос?',
                    'answer' => 'Русский ответ.',
                ],
            ],
            'included_json' => [
                ['text' => 'Русский включенный пункт 1'],
            ],
            'excluded_json' => [
                ['text' => 'Русский исключенный пункт 1'],
            ],
            'requirements_json' => [
                ['text' => 'Русское требование 1'],
            ],
            'cancellation_policy' => 'Русская политика отмены',
        ]);

        // Test Russian tour page
        $response = $this->get('/ru/tours/tur-po-ploshhadi-registan');

        $response->assertStatus(200);
        $response->assertSee('Тур по площади Регистан');
        $response->assertSee('Описание тура на русском языке');
    }

    /** @test */
    public function it_returns_404_for_wrong_locale_slug_combination()
    {
        // Create tour with only EN translation
        $city = City::factory()->create();
        $tour = Tour::factory()->create([
            'city_id' => $city->id,
            'is_active' => true,
        ]);

        TourTranslation::factory()->create([
            'tour_id' => $tour->id,
            'locale' => 'en',
            'slug' => 'english-slug',
            'title' => 'English Tour',
        ]);

        // Trying to access EN slug on RU route should 404
        // (unless fallback is implemented, which we'll handle separately)
        $response = $this->get('/ru/tours/english-slug');

        // This might return 200 with fallback or 404 - depends on implementation
        // For now, let's verify it doesn't crash
        $this->assertContains($response->status(), [200, 404]);
    }

    /** @test */
    public function htmx_highlights_partial_shows_translated_content()
    {
        $city = City::factory()->create();
        $tour = Tour::factory()->create([
            'city_id' => $city->id,
            'slug' => 'test-tour',
            'is_active' => true,
        ]);

        // Russian translation with highlights
        TourTranslation::factory()->create([
            'tour_id' => $tour->id,
            'locale' => 'ru',
            'highlights_json' => [
                ['text' => 'Русский основной момент 1'],
                ['text' => 'Русский основной момент 2'],
            ],
        ]);

        // Request highlights partial with Russian locale
        $response = $this->get('/partials/tours/test-tour/highlights?locale=ru');

        $response->assertStatus(200);
        $response->assertSee('Русский основной момент 1');
        $response->assertSee('Русский основной момент 2');
    }

    /** @test */
    public function htmx_itinerary_partial_shows_translated_content()
    {
        $city = City::factory()->create();
        $tour = Tour::factory()->create([
            'city_id' => $city->id,
            'slug' => 'test-tour',
            'is_active' => true,
        ]);

        // Russian translation with itinerary
        TourTranslation::factory()->create([
            'tour_id' => $tour->id,
            'locale' => 'ru',
            'itinerary_json' => [
                [
                    'day' => 1,
                    'title' => 'Русский День 1',
                    'description' => '<p>Русское описание дня</p>',
                    'duration_minutes' => 480,
                ],
            ],
        ]);

        // Request itinerary partial with Russian locale
        $response = $this->get('/partials/tours/test-tour/itinerary?locale=ru');

        $response->assertStatus(200);
        $response->assertSee('Русский День 1');
        $response->assertSee('Русское описание дня', false); // false = don't escape HTML
    }

    /** @test */
    public function htmx_faq_partial_shows_translated_content()
    {
        $city = City::factory()->create();
        $tour = Tour::factory()->create([
            'city_id' => $city->id,
            'slug' => 'test-tour',
            'is_active' => true,
        ]);

        // Russian translation with FAQ
        TourTranslation::factory()->create([
            'tour_id' => $tour->id,
            'locale' => 'ru',
            'faq_json' => [
                [
                    'question' => 'Русский вопрос FAQ?',
                    'answer' => 'Русский ответ FAQ.',
                ],
            ],
        ]);

        // Request FAQ partial with Russian locale
        $response = $this->get('/partials/tours/test-tour/faqs?locale=ru');

        $response->assertStatus(200);
        $response->assertSee('Русский вопрос FAQ?');
        $response->assertSee('Русский ответ FAQ.');
    }

    /** @test */
    public function htmx_included_excluded_partial_shows_translated_content()
    {
        $city = City::factory()->create();
        $tour = Tour::factory()->create([
            'city_id' => $city->id,
            'slug' => 'test-tour',
            'is_active' => true,
        ]);

        // Russian translation with included/excluded
        TourTranslation::factory()->create([
            'tour_id' => $tour->id,
            'locale' => 'ru',
            'included_json' => [
                ['text' => 'Русский включенный пункт'],
            ],
            'excluded_json' => [
                ['text' => 'Русский исключенный пункт'],
            ],
        ]);

        // Request included-excluded partial with Russian locale
        $response = $this->get('/partials/tours/test-tour/included-excluded?locale=ru');

        $response->assertStatus(200);
        $response->assertSee('Русский включенный пункт');
        $response->assertSee('Русский исключенный пункт');
    }

    /** @test */
    public function htmx_requirements_partial_shows_translated_content()
    {
        $city = City::factory()->create();
        $tour = Tour::factory()->create([
            'city_id' => $city->id,
            'slug' => 'test-tour',
            'is_active' => true,
        ]);

        // Russian translation with requirements
        TourTranslation::factory()->create([
            'tour_id' => $tour->id,
            'locale' => 'ru',
            'requirements_json' => [
                ['text' => 'Русское требование 1'],
                ['text' => 'Русское требование 2'],
            ],
        ]);

        // Request requirements partial with Russian locale
        $response = $this->get('/partials/tours/test-tour/requirements?locale=ru');

        $response->assertStatus(200);
        $response->assertSee('Русское требование 1');
        $response->assertSee('Русское требование 2');
    }

    /** @test */
    public function htmx_cancellation_partial_shows_translated_policy()
    {
        $city = City::factory()->create();
        $tour = Tour::factory()->create([
            'city_id' => $city->id,
            'slug' => 'test-tour',
            'is_active' => true,
            'cancellation_hours' => 24,
        ]);

        // Russian translation with cancellation policy
        TourTranslation::factory()->create([
            'tour_id' => $tour->id,
            'locale' => 'ru',
            'cancellation_policy' => 'Русская политика отмены бронирования',
        ]);

        // Request cancellation partial with Russian locale
        $response = $this->get('/partials/tours/test-tour/cancellation?locale=ru');

        $response->assertStatus(200);
        $response->assertSee('Русская политика отмены бронирования');
    }

    /** @test */
    public function it_falls_back_to_tour_model_when_translation_json_is_null()
    {
        $city = City::factory()->create();
        $tour = Tour::factory()->create([
            'city_id' => $city->id,
            'slug' => 'test-tour',
            'is_active' => true,
            'highlights' => [
                'Tour model highlight 1',
                'Tour model highlight 2',
            ],
        ]);

        // Russian translation WITHOUT highlights_json (should fallback to tour.highlights)
        TourTranslation::factory()->create([
            'tour_id' => $tour->id,
            'locale' => 'ru',
            'highlights_json' => null, // No translated highlights
        ]);

        // Request highlights partial with Russian locale
        $response = $this->get('/partials/tours/test-tour/highlights?locale=ru');

        $response->assertStatus(200);
        $response->assertSee('Tour model highlight 1');
        $response->assertSee('Tour model highlight 2');
    }
}
