<?php

namespace Tests\Feature;

use App\Models\Tour;
use App\Models\TourTranslation;
use App\Models\City;
use App\Models\CityTranslation;
use App\Models\BlogPost;
use App\Models\BlogPostTranslation;
use App\Models\BlogCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Phase 4 SEO Tests
 *
 * Tests for SEO features: canonical URLs, hreflang tags, and localized sitemaps.
 * These tests create their own data (do not rely on seeded DB).
 *
 * Usage: php artisan test --filter=Phase4SeoTest
 */
class Phase4SeoTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Enable multilang and SEO features for all tests.
     */
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'multilang.enabled' => true,
            'multilang.phases.routes' => true,
            'multilang.phases.seo' => true,
            'multilang.phases.tour_translations' => true,
            'multilang.phases.city_translations' => true,
            'multilang.phases.blog_translations' => true,
            'multilang.features.locale_routing' => true,
        ]);
    }

    /**
     * Create a tour with translations for testing.
     */
    private function createTourWithTranslations(): Tour
    {
        $city = City::create([
            'name' => 'Samarkand',
            'slug' => 'samarkand',
            'is_active' => true,
            'country' => 'Uzbekistan',
        ]);

        $tour = Tour::create([
            'title' => 'Silk Road Tour',
            'slug' => 'silk-road-tour',
            'short_description' => 'Explore the Silk Road',
            'duration_days' => 5,
            'price_per_person' => 500.00,
            'city_id' => $city->id,
            'is_active' => true,
        ]);

        // Create translations
        TourTranslation::create([
            'tour_id' => $tour->id,
            'locale' => 'en',
            'title' => 'Silk Road Tour English',
            'slug' => 'silk-road-tour-en',
            'short_description' => 'English description',
        ]);

        TourTranslation::create([
            'tour_id' => $tour->id,
            'locale' => 'ru',
            'title' => 'Тур по Шелковому Пути',
            'slug' => 'tur-po-shelkovomu-puti',
            'short_description' => 'Русское описание',
        ]);

        // No French translation (for testing partial translations)

        return $tour;
    }

    /**
     * Create a city with translations for testing.
     */
    private function createCityWithTranslations(): City
    {
        $city = City::create([
            'name' => 'Bukhara',
            'slug' => 'bukhara',
            'is_active' => true,
            'country' => 'Uzbekistan',
        ]);

        CityTranslation::create([
            'city_id' => $city->id,
            'locale' => 'en',
            'name' => 'Bukhara',
            'slug' => 'bukhara-en',
        ]);

        CityTranslation::create([
            'city_id' => $city->id,
            'locale' => 'ru',
            'name' => 'Бухара',
            'slug' => 'buhara',
        ]);

        CityTranslation::create([
            'city_id' => $city->id,
            'locale' => 'fr',
            'name' => 'Boukhara',
            'slug' => 'boukhara',
        ]);

        return $city;
    }

    /**
     * Create a blog post with translations for testing.
     */
    private function createBlogPostWithTranslations(): BlogPost
    {
        $category = BlogCategory::create([
            'name' => 'Travel Tips',
            'slug' => 'travel-tips',
        ]);

        $post = BlogPost::create([
            'category_id' => $category->id,
            'title' => 'Best Time to Visit',
            'slug' => 'best-time-to-visit',
            'content' => 'Content here',
            'author_name' => 'Author',
            'is_published' => true,
            'published_at' => now(),
        ]);

        BlogPostTranslation::create([
            'blog_post_id' => $post->id,
            'locale' => 'en',
            'title' => 'Best Time to Visit',
            'slug' => 'best-time-to-visit-en',
        ]);

        BlogPostTranslation::create([
            'blog_post_id' => $post->id,
            'locale' => 'ru',
            'title' => 'Лучшее время для посещения',
            'slug' => 'luchshee-vremya',
        ]);

        return $post;
    }

    // ========================================
    // SITEMAP INDEX TESTS
    // ========================================

    /**
     * Test sitemap index returns 200 and is valid XML.
     */
    public function test_sitemap_index_returns_200(): void
    {
        $response = $this->get('/sitemap.xml');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/xml');
    }

    /**
     * Test sitemap index contains locale-specific sitemaps.
     */
    public function test_sitemap_index_contains_locale_sitemaps(): void
    {
        $response = $this->get('/sitemap.xml');

        $response->assertStatus(200);
        $response->assertSee('sitemap-en.xml');
        $response->assertSee('sitemap-ru.xml');
        $response->assertSee('sitemap-fr.xml');
    }

    // ========================================
    // LOCALE SITEMAP TESTS
    // ========================================

    /**
     * Test English locale sitemap returns 200.
     */
    public function test_locale_sitemap_en_returns_200(): void
    {
        $response = $this->get('/sitemap-en.xml');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/xml');
    }

    /**
     * Test Russian locale sitemap returns 200.
     */
    public function test_locale_sitemap_ru_returns_200(): void
    {
        $response = $this->get('/sitemap-ru.xml');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/xml');
    }

    /**
     * Test French locale sitemap returns 200.
     */
    public function test_locale_sitemap_fr_returns_200(): void
    {
        $response = $this->get('/sitemap-fr.xml');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/xml');
    }

    /**
     * Test invalid locale sitemap returns 404.
     */
    public function test_invalid_locale_sitemap_returns_404(): void
    {
        $response = $this->get('/sitemap-de.xml');

        $response->assertStatus(404);
    }

    /**
     * Test locale sitemap contains static pages.
     */
    public function test_locale_sitemap_contains_static_pages(): void
    {
        $response = $this->get('/sitemap-en.xml');

        $response->assertStatus(200);
        // Static pages should be present
        $response->assertSee('/en');  // Home
    }

    /**
     * Test locale sitemap contains tour translations.
     */
    public function test_locale_sitemap_contains_tour_translations(): void
    {
        $tour = $this->createTourWithTranslations();

        $response = $this->get('/sitemap-en.xml');

        $response->assertStatus(200);
        $response->assertSee('silk-road-tour-en');

        // Russian sitemap should have Russian slug
        $response = $this->get('/sitemap-ru.xml');
        $response->assertStatus(200);
        $response->assertSee('tur-po-shelkovomu-puti');

        // French sitemap should NOT have this tour (no French translation)
        $response = $this->get('/sitemap-fr.xml');
        $response->assertStatus(200);
        $response->assertDontSee('silk-road-tour');
    }

    /**
     * Test locale sitemap contains city translations.
     */
    public function test_locale_sitemap_contains_city_translations(): void
    {
        $city = $this->createCityWithTranslations();

        $response = $this->get('/sitemap-en.xml');
        $response->assertStatus(200);
        $response->assertSee('bukhara-en');

        $response = $this->get('/sitemap-ru.xml');
        $response->assertStatus(200);
        $response->assertSee('buhara');

        $response = $this->get('/sitemap-fr.xml');
        $response->assertStatus(200);
        $response->assertSee('boukhara');
    }

    /**
     * Test locale sitemap contains blog translations.
     */
    public function test_locale_sitemap_contains_blog_translations(): void
    {
        $post = $this->createBlogPostWithTranslations();

        $response = $this->get('/sitemap-en.xml');
        $response->assertStatus(200);
        $response->assertSee('best-time-to-visit-en');

        $response = $this->get('/sitemap-ru.xml');
        $response->assertStatus(200);
        $response->assertSee('luchshee-vremya');

        // French sitemap should NOT have this post (no French translation)
        $response = $this->get('/sitemap-fr.xml');
        $response->assertStatus(200);
        $response->assertDontSee('best-time-to-visit');
    }

    /**
     * Test inactive tours not in sitemap.
     */
    public function test_inactive_tours_not_in_sitemap(): void
    {
        // Create an inactive tour
        $city = City::create([
            'name' => 'Khiva',
            'slug' => 'khiva',
            'is_active' => true,
            'country' => 'Uzbekistan',
        ]);

        $tour = Tour::create([
            'title' => 'Inactive Tour',
            'slug' => 'inactive-tour',
            'city_id' => $city->id,
            'is_active' => false, // Inactive
        ]);

        TourTranslation::create([
            'tour_id' => $tour->id,
            'locale' => 'en',
            'title' => 'Inactive Tour EN',
            'slug' => 'inactive-tour-en',
        ]);

        $response = $this->get('/sitemap-en.xml');
        $response->assertStatus(200);
        $response->assertDontSee('inactive-tour-en');
    }

    /**
     * Test unpublished blog posts not in sitemap.
     */
    public function test_unpublished_posts_not_in_sitemap(): void
    {
        $category = BlogCategory::create([
            'name' => 'News',
            'slug' => 'news',
        ]);

        $post = BlogPost::create([
            'category_id' => $category->id,
            'title' => 'Draft Post',
            'slug' => 'draft-post',
            'content' => 'Draft content',
            'author_name' => 'Author',
            'is_published' => false, // Not published
        ]);

        BlogPostTranslation::create([
            'blog_post_id' => $post->id,
            'locale' => 'en',
            'title' => 'Draft Post EN',
            'slug' => 'draft-post-en',
        ]);

        $response = $this->get('/sitemap-en.xml');
        $response->assertStatus(200);
        $response->assertDontSee('draft-post-en');
    }

    // ========================================
    // SITEMAP CACHING TESTS
    // ========================================

    /**
     * Test sitemap is cached (same response on second request).
     */
    public function test_sitemap_is_cached(): void
    {
        // First request
        $response1 = $this->get('/sitemap-en.xml');
        $response1->assertStatus(200);

        // Second request (should be served from cache)
        $response2 = $this->get('/sitemap-en.xml');
        $response2->assertStatus(200);

        // Both should have same content
        $this->assertEquals($response1->getContent(), $response2->getContent());
    }

    // ========================================
    // HREFLANG COMPONENT TESTS
    // ========================================

    /**
     * Test hreflang component generates correct tags for entity with translations.
     */
    public function test_hreflang_component_generates_tags_for_tour(): void
    {
        $tour = $this->createTourWithTranslations();
        $tour->load('translations');

        // Render the component
        $view = $this->blade(
            '<x-seo.hreflang :entity="$tour" route-name="localized.tours.show" />',
            ['tour' => $tour]
        );

        // Should have hreflang for en and ru (not fr - no translation)
        $view->assertSee('hreflang="en"');
        $view->assertSee('hreflang="ru"');
        $view->assertDontSee('hreflang="fr"');
        $view->assertSee('hreflang="x-default"');
    }

    /**
     * Test hreflang component generates correct tags for static pages.
     */
    public function test_hreflang_component_generates_tags_for_static_page(): void
    {
        $view = $this->blade(
            '<x-seo.hreflang static route-name="localized.home" />'
        );

        // Should have hreflang for all locales
        $view->assertSee('hreflang="en"');
        $view->assertSee('hreflang="ru"');
        $view->assertSee('hreflang="fr"');
        $view->assertSee('hreflang="x-default"');
    }

    // ========================================
    // CANONICAL COMPONENT TESTS
    // ========================================

    /**
     * Test canonical component outputs current URL.
     */
    public function test_canonical_component_outputs_url(): void
    {
        $view = $this->blade('<x-seo.canonical />');

        $view->assertSee('rel="canonical"');
        $view->assertSee('href="');
    }

    /**
     * Test canonical component accepts explicit URL.
     */
    public function test_canonical_component_accepts_explicit_url(): void
    {
        $url = 'https://example.com/test-page';

        $view = $this->blade(
            '<x-seo.canonical :url="$url" />',
            ['url' => $url]
        );

        $view->assertSee('href="https://example.com/test-page"');
    }

    // ========================================
    // SEO FEATURE FLAG TESTS
    // ========================================

    /**
     * Test sitemap returns original format when SEO phase disabled.
     */
    public function test_sitemap_original_when_seo_disabled(): void
    {
        // Disable SEO phase
        config(['multilang.phases.seo' => false]);

        $response = $this->get('/sitemap.xml');

        $response->assertStatus(200);
        // Should NOT be a sitemap index (no sitemapindex tag)
        $response->assertDontSee('<sitemapindex');
        // Should be regular urlset
        $response->assertSee('<urlset');
    }

    /**
     * Test hreflang component outputs nothing when SEO phase disabled.
     */
    public function test_hreflang_empty_when_seo_disabled(): void
    {
        // Disable SEO phase
        config(['multilang.phases.seo' => false]);

        $tour = $this->createTourWithTranslations();
        $tour->load('translations');

        $view = $this->blade(
            '<x-seo.hreflang :entity="$tour" route-name="localized.tours.show" />',
            ['tour' => $tour]
        );

        // Should not contain any hreflang tags
        $view->assertDontSee('hreflang=');
    }
}
