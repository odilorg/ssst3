<?php

namespace Tests\Feature;

use App\Models\Tour;
use App\Models\TourTranslation;
use App\Models\City;
use App\Models\CityTranslation;
use App\Models\BlogPost;
use App\Models\BlogPostTranslation;
use App\Models\BlogCategory;
use App\Models\User;
use App\Services\TranslationCoverageService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Phase 5 Admin QA Tests
 *
 * Tests for admin QA tools: Translation Coverage Report page and service.
 * These tests create their own data (do not rely on seeded DB).
 *
 * Usage: php artisan test --filter=Phase5AdminQaTest
 */
class Phase5AdminQaTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Enable multilang features for all tests.
     */
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'multilang.enabled' => true,
            'multilang.phases.routes' => true,
            'multilang.phases.tour_translations' => true,
            'multilang.phases.city_translations' => true,
            'multilang.phases.blog_translations' => true,
        ]);
    }

    /**
     * Create an admin user for testing.
     */
    private function createAdminUser(): User
    {
        return User::factory()->create([
            'email' => 'admin@test.com',
        ]);
    }

    /**
     * Create test data with partial translations.
     */
    private function createTestData(): array
    {
        // Create a city
        $city = City::create([
            'name' => 'Test City',
            'slug' => 'test-city',
            'is_active' => true,
            'country' => 'Uzbekistan',
        ]);

        // City with EN and RU translations (missing FR)
        CityTranslation::create([
            'city_id' => $city->id,
            'locale' => 'en',
            'name' => 'Test City EN',
            'slug' => 'test-city-en',
        ]);
        CityTranslation::create([
            'city_id' => $city->id,
            'locale' => 'ru',
            'name' => 'Тестовый Город',
            'slug' => 'testovyy-gorod',
        ]);

        // Create a tour with only EN translation
        $tour = Tour::create([
            'title' => 'Test Tour',
            'slug' => 'test-tour',
            'city_id' => $city->id,
            'is_active' => true,
        ]);

        TourTranslation::create([
            'tour_id' => $tour->id,
            'locale' => 'en',
            'title' => 'Test Tour EN',
            'slug' => 'test-tour-en',
        ]);

        // Create a blog post with all translations
        $category = BlogCategory::create([
            'name' => 'Test Category',
            'slug' => 'test-category',
        ]);

        $post = BlogPost::create([
            'category_id' => $category->id,
            'title' => 'Test Post',
            'slug' => 'test-post',
            'content' => 'Content',
            'author_name' => 'Author',
            'is_published' => true,
            'published_at' => now(),
        ]);

        BlogPostTranslation::create([
            'blog_post_id' => $post->id,
            'locale' => 'en',
            'title' => 'Test Post EN',
            'slug' => 'test-post-en',
        ]);
        BlogPostTranslation::create([
            'blog_post_id' => $post->id,
            'locale' => 'ru',
            'title' => 'Тестовый Пост',
            'slug' => 'testovyy-post',
        ]);
        BlogPostTranslation::create([
            'blog_post_id' => $post->id,
            'locale' => 'fr',
            'title' => 'Post de Test',
            'slug' => 'post-de-test',
        ]);

        return [
            'city' => $city,
            'tour' => $tour,
            'post' => $post,
        ];
    }

    // ========================================
    // TRANSLATION COVERAGE SERVICE TESTS
    // ========================================

    /**
     * Test service returns correct supported locales.
     */
    public function test_service_returns_supported_locales(): void
    {
        $service = app(TranslationCoverageService::class);

        $locales = $service->getSupportedLocales();

        $this->assertIsArray($locales);
        $this->assertContains('en', $locales);
        $this->assertContains('ru', $locales);
        $this->assertContains('fr', $locales);
    }

    /**
     * Test service calculates tour coverage correctly.
     */
    public function test_service_calculates_tour_coverage(): void
    {
        $this->createTestData();

        $service = app(TranslationCoverageService::class);
        $coverage = $service->getToursCoverage();

        // Tour has only EN translation
        $this->assertEquals(1, $coverage['en']['total']);
        $this->assertEquals(1, $coverage['en']['translated']);
        $this->assertEquals(0, $coverage['en']['missing']);
        $this->assertEquals(100, $coverage['en']['percentage']);

        // RU and FR are missing
        $this->assertEquals(1, $coverage['ru']['total']);
        $this->assertEquals(0, $coverage['ru']['translated']);
        $this->assertEquals(1, $coverage['ru']['missing']);
        $this->assertEquals(0, $coverage['ru']['percentage']);

        $this->assertEquals(1, $coverage['fr']['missing']);
    }

    /**
     * Test service calculates city coverage correctly.
     */
    public function test_service_calculates_city_coverage(): void
    {
        $this->createTestData();

        $service = app(TranslationCoverageService::class);
        $coverage = $service->getCitiesCoverage();

        // City has EN and RU translations
        $this->assertEquals(1, $coverage['en']['translated']);
        $this->assertEquals(1, $coverage['ru']['translated']);

        // FR is missing
        $this->assertEquals(1, $coverage['fr']['missing']);
        $this->assertEquals(0, $coverage['fr']['percentage']);
    }

    /**
     * Test service calculates blog post coverage correctly.
     */
    public function test_service_calculates_blog_coverage(): void
    {
        $this->createTestData();

        $service = app(TranslationCoverageService::class);
        $coverage = $service->getBlogPostsCoverage();

        // Blog post has all translations
        $this->assertEquals(1, $coverage['en']['translated']);
        $this->assertEquals(1, $coverage['ru']['translated']);
        $this->assertEquals(1, $coverage['fr']['translated']);
        $this->assertEquals(100, $coverage['en']['percentage']);
        $this->assertEquals(100, $coverage['ru']['percentage']);
        $this->assertEquals(100, $coverage['fr']['percentage']);
    }

    /**
     * Test service returns missing items with correct structure.
     */
    public function test_service_returns_missing_items_structure(): void
    {
        $data = $this->createTestData();

        $service = app(TranslationCoverageService::class);
        $coverage = $service->getToursCoverage();

        // Tour is missing RU translation
        $missingRu = $coverage['ru']['missing_items'];
        $this->assertCount(1, $missingRu);
        $this->assertEquals($data['tour']->id, $missingRu[0]['id']);
        $this->assertEquals($data['tour']->title, $missingRu[0]['title']);
        $this->assertArrayHasKey('edit_url', $missingRu[0]);
    }

    /**
     * Test service generates full report.
     */
    public function test_service_generates_full_report(): void
    {
        $this->createTestData();

        $service = app(TranslationCoverageService::class);
        $report = $service->getFullReport();

        $this->assertArrayHasKey('tours', $report);
        $this->assertArrayHasKey('cities', $report);
        $this->assertArrayHasKey('blog_posts', $report);
        $this->assertArrayHasKey('summary', $report);
    }

    /**
     * Test service calculates summary correctly.
     */
    public function test_service_calculates_summary(): void
    {
        $this->createTestData();

        $service = app(TranslationCoverageService::class);
        $summary = $service->getSummary();

        // EN: 1 tour + 1 city + 1 blog = 3 items, all translated
        $this->assertEquals(3, $summary['en']['total']);
        $this->assertEquals(3, $summary['en']['translated']);
        $this->assertEquals(0, $summary['en']['missing']);

        // RU: 3 items, 2 translated (city + blog), 1 missing (tour)
        $this->assertEquals(3, $summary['ru']['total']);
        $this->assertEquals(2, $summary['ru']['translated']);
        $this->assertEquals(1, $summary['ru']['missing']);

        // FR: 3 items, 1 translated (blog), 2 missing (tour + city)
        $this->assertEquals(3, $summary['fr']['total']);
        $this->assertEquals(1, $summary['fr']['translated']);
        $this->assertEquals(2, $summary['fr']['missing']);
    }

    /**
     * Test inactive tours are excluded from coverage.
     */
    public function test_inactive_tours_excluded(): void
    {
        $city = City::create([
            'name' => 'City',
            'slug' => 'city',
            'is_active' => true,
            'country' => 'Uzbekistan',
        ]);

        // Create inactive tour
        $tour = Tour::create([
            'title' => 'Inactive Tour',
            'slug' => 'inactive-tour',
            'city_id' => $city->id,
            'is_active' => false,
        ]);

        $service = app(TranslationCoverageService::class);
        $coverage = $service->getToursCoverage();

        // Inactive tour should not be counted
        $this->assertEquals(0, $coverage['en']['total']);
    }

    /**
     * Test unpublished blog posts are excluded from coverage.
     */
    public function test_unpublished_posts_excluded(): void
    {
        $category = BlogCategory::create([
            'name' => 'Category',
            'slug' => 'category',
        ]);

        // Create unpublished post
        BlogPost::create([
            'category_id' => $category->id,
            'title' => 'Draft Post',
            'slug' => 'draft-post',
            'content' => 'Content',
            'author_name' => 'Author',
            'is_published' => false,
        ]);

        $service = app(TranslationCoverageService::class);
        $coverage = $service->getBlogPostsCoverage();

        // Unpublished post should not be counted
        $this->assertEquals(0, $coverage['en']['total']);
    }

    // ========================================
    // FILAMENT PAGE TESTS
    // ========================================

    /**
     * Test translation coverage page loads for authenticated admin.
     */
    public function test_translation_coverage_page_loads(): void
    {
        $admin = $this->createAdminUser();
        $this->createTestData();

        $response = $this->actingAs($admin)
            ->get('/admin/translation-coverage');

        $response->assertStatus(200);
    }

    /**
     * Test page shows locale summary.
     */
    public function test_page_shows_locale_summary(): void
    {
        $admin = $this->createAdminUser();
        $this->createTestData();

        $response = $this->actingAs($admin)
            ->get('/admin/translation-coverage');

        $response->assertStatus(200);
        $response->assertSee('English');
        $response->assertSee('Russian');
        $response->assertSee('French');
    }

    /**
     * Test page requires authentication.
     */
    public function test_page_requires_authentication(): void
    {
        $response = $this->get('/admin/translation-coverage');

        // Should redirect to login
        $response->assertRedirect();
    }

    /**
     * Test page is hidden when multilang disabled.
     */
    public function test_page_hidden_when_multilang_disabled(): void
    {
        config(['multilang.enabled' => false]);

        $shouldRegister = \App\Filament\Pages\TranslationCoverage::shouldRegisterNavigation();

        $this->assertFalse($shouldRegister);
    }

    /**
     * Test page visible when multilang enabled.
     */
    public function test_page_visible_when_multilang_enabled(): void
    {
        config(['multilang.enabled' => true]);

        $shouldRegister = \App\Filament\Pages\TranslationCoverage::shouldRegisterNavigation();

        $this->assertTrue($shouldRegister);
    }
}
