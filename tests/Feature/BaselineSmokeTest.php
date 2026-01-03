<?php

namespace Tests\Feature;

use App\Models\BlogPost;
use App\Models\City;
use App\Models\Tour;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Baseline Smoke Tests
 *
 * These tests verify that all critical public pages return HTTP 200.
 * Run these before and after any multilingual implementation phase
 * to ensure existing functionality is not broken.
 *
 * Usage: php artisan test --filter=BaselineSmokeTest
 */
class BaselineSmokeTest extends TestCase
{
    // Note: We're NOT using RefreshDatabase to test against real seeded data
    // This ensures we test the actual production-like state

    /**
     * Test homepage loads successfully.
     */
    public function test_homepage_returns_200(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Jahongir Travel'); // Adjust to your actual site name
    }

    /**
     * Test mini-journeys listing page loads.
     */
    public function test_mini_journeys_returns_200(): void
    {
        $response = $this->get('/mini-journeys');

        $response->assertStatus(200);
    }

    /**
     * Test craft-journeys listing page loads.
     */
    public function test_craft_journeys_returns_200(): void
    {
        $response = $this->get('/craft-journeys');

        $response->assertStatus(200);
    }

    /**
     * Test tour detail page loads for an existing tour.
     */
    public function test_tour_detail_returns_200(): void
    {
        // Get a real active tour from the database
        $tour = Tour::where('is_active', true)->first();

        if (!$tour) {
            $this->markTestSkipped('No active tours in database');
        }

        $response = $this->get('/tours/' . $tour->slug);

        $response->assertStatus(200);
        $response->assertSee($tour->title);
    }

    /**
     * Test about page loads.
     */
    public function test_about_page_returns_200(): void
    {
        $response = $this->get('/about');

        $response->assertStatus(200);
    }

    /**
     * Test contact page loads.
     */
    public function test_contact_page_returns_200(): void
    {
        $response = $this->get('/contact');

        $response->assertStatus(200);
    }

    /**
     * Test privacy page loads.
     */
    public function test_privacy_page_returns_200(): void
    {
        $response = $this->get('/privacy');

        $response->assertStatus(200);
    }

    /**
     * Test terms page loads.
     */
    public function test_terms_page_returns_200(): void
    {
        $response = $this->get('/terms');

        $response->assertStatus(200);
    }

    /**
     * Test blog listing page loads.
     */
    public function test_blog_index_returns_200(): void
    {
        $response = $this->get('/blog');

        $response->assertStatus(200);
    }

    /**
     * Test blog detail page loads for an existing post.
     */
    public function test_blog_detail_returns_200(): void
    {
        // Get a real published blog post
        $post = BlogPost::where('status', 'published')->first();

        if (!$post) {
            $this->markTestSkipped('No published blog posts in database');
        }

        $response = $this->get('/blog/' . $post->slug);

        $response->assertStatus(200);
    }

    /**
     * Test destinations index page loads.
     */
    public function test_destinations_index_returns_200(): void
    {
        $response = $this->get('/destinations');

        $response->assertStatus(200);
    }

    /**
     * Test destination detail page loads for an existing city.
     */
    public function test_destination_detail_returns_200(): void
    {
        // Get a real city
        $city = City::first();

        if (!$city) {
            $this->markTestSkipped('No cities in database');
        }

        $response = $this->get('/destinations/' . $city->slug);

        $response->assertStatus(200);
    }

    /**
     * Test sitemap returns valid XML.
     */
    public function test_sitemap_returns_xml(): void
    {
        $response = $this->get('/sitemap.xml');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/xml; charset=UTF-8');
    }

    /**
     * Test CSRF token endpoint returns JSON.
     */
    public function test_csrf_token_returns_json(): void
    {
        $response = $this->get('/csrf-token');

        $response->assertStatus(200);
        $response->assertJsonStructure(['token']);
    }

    /**
     * Test Filament admin redirects to login (authentication required).
     */
    public function test_admin_requires_authentication(): void
    {
        $response = $this->get('/admin');

        // Should redirect to login
        $response->assertRedirect();
    }

    /**
     * Test tour partials endpoint returns HTML.
     */
    public function test_tour_search_partial_returns_html(): void
    {
        $response = $this->get('/partials/tours/search?q=test');

        $response->assertStatus(200);
    }

    /**
     * Test HTMX tour itinerary partial loads.
     */
    public function test_tour_itinerary_partial_returns_html(): void
    {
        $tour = Tour::where('is_active', true)->first();

        if (!$tour) {
            $this->markTestSkipped('No active tours in database');
        }

        $response = $this->get('/partials/tours/' . $tour->slug . '/itinerary');

        $response->assertStatus(200);
    }

    /**
     * Test HTMX tour FAQs partial loads without JSON syntax errors.
     */
    public function test_tour_faqs_partial_returns_clean_html(): void
    {
        $tour = Tour::where('is_active', true)->first();

        if (!$tour) {
            $this->markTestSkipped('No active tours in database');
        }

        $response = $this->get('/partials/tours/' . $tour->slug . '/faqs');

        $response->assertStatus(200);

        // Ensure no raw JSON syntax in FAQs (regression test for translatable fix)
        $content = $response->getContent();
        $this->assertStringNotContainsString('{"en":', $content, 'FAQs should not contain raw JSON syntax');
    }

    /**
     * Test booking form partial loads for a tour.
     */
    public function test_booking_form_partial_returns_html(): void
    {
        $tour = Tour::where('is_active', true)->first();

        if (!$tour) {
            $this->markTestSkipped('No active tours in database');
        }

        $response = $this->get('/partials/bookings/form/' . $tour->slug);

        $response->assertStatus(200);
    }

    /**
     * Verify multilang config is loaded correctly (Phase 0 check).
     */
    public function test_multilang_config_exists_and_disabled(): void
    {
        // Config should exist
        $this->assertNotNull(config('multilang'));

        // Master switch should be OFF by default (Phase 0)
        $this->assertFalse(config('multilang.enabled'), 'Multilang should be disabled by default in Phase 0');

        // Locales should be defined
        $this->assertEquals(['en', 'ru', 'fr'], config('multilang.locales'));

        // Default locale should be English
        $this->assertEquals('en', config('multilang.default_locale'));
    }
}
