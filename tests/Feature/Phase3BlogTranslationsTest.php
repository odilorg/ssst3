<?php

namespace Tests\Feature;

use App\Models\BlogPost;
use App\Models\BlogPostTranslation;
use App\Models\BlogCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Phase 3.2 Blog Translations Tests
 *
 * Tests for localized blog routes using blog_post_translations table.
 * These tests create their own data (do not rely on seeded DB).
 *
 * Usage: php artisan test --filter=Phase3BlogTranslationsTest
 */
class Phase3BlogTranslationsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Enable multilang and blog_translations features for all tests.
     */
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'multilang.enabled' => true,
            'multilang.phases.routes' => true,
            'multilang.phases.blog_translations' => true,
            'multilang.features.locale_routing' => true,
        ]);
    }

    /**
     * Create a blog post with translations for testing.
     */
    private function createBlogPostWithTranslations(): BlogPost
    {
        // Create category first
        $category = BlogCategory::create([
            'name' => 'Test Category',
            'slug' => 'test-category',
        ]);

        // Create blog post
        $post = BlogPost::create([
            'category_id' => $category->id,
            'title' => 'Test Blog Post',
            'slug' => 'test-blog-post',
            'excerpt' => 'Test excerpt',
            'content' => 'Test content',
            'author_name' => 'Test Author',
            'is_published' => true,
            'published_at' => now(),
        ]);

        // Create English translation
        BlogPostTranslation::create([
            'blog_post_id' => $post->id,
            'locale' => 'en',
            'title' => 'Test Blog Post English',
            'slug' => 'test-blog-en',
            'excerpt' => 'English excerpt',
            'content' => 'English content for the blog post.',
        ]);

        // Create Russian translation
        BlogPostTranslation::create([
            'blog_post_id' => $post->id,
            'locale' => 'ru',
            'title' => 'Тестовая Статья',
            'slug' => 'testovaya-statya',
            'excerpt' => 'Русское описание',
            'content' => 'Русское содержание статьи.',
        ]);

        // Create French translation
        BlogPostTranslation::create([
            'blog_post_id' => $post->id,
            'locale' => 'fr',
            'title' => 'Article de Test',
            'slug' => 'article-de-test',
            'excerpt' => 'Extrait français',
            'content' => 'Contenu français de l\'article.',
        ]);

        return $post;
    }

    // ========================================
    // LOCALIZED BLOG ROUTES
    // ========================================

    /**
     * Test English localized blog returns 200 and contains English title.
     */
    public function test_localized_blog_en_returns_200_with_english_title(): void
    {
        $post = $this->createBlogPostWithTranslations();

        $response = $this->get('/en/blog/test-blog-en');

        $response->assertStatus(200);
        $response->assertSee('Test Blog Post English');
    }

    /**
     * Test Russian localized blog returns 200 and contains Russian title.
     */
    public function test_localized_blog_ru_returns_200_with_russian_title(): void
    {
        $post = $this->createBlogPostWithTranslations();

        $response = $this->get('/ru/blog/testovaya-statya');

        $response->assertStatus(200);
        $response->assertSee('Тестовая Статья');
    }

    /**
     * Test French localized blog returns 200 and contains French title.
     */
    public function test_localized_blog_fr_returns_200_with_french_title(): void
    {
        $post = $this->createBlogPostWithTranslations();

        $response = $this->get('/fr/blog/article-de-test');

        $response->assertStatus(200);
        $response->assertSee('Article de Test');
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
        $post = $this->createBlogPostWithTranslations();

        // Try to access English slug with Russian locale
        $response = $this->get('/ru/blog/test-blog-en');

        $response->assertStatus(404);
    }

    /**
     * Test non-existent slug returns 404.
     */
    public function test_nonexistent_slug_returns_404(): void
    {
        $post = $this->createBlogPostWithTranslations();

        $response = $this->get('/en/blog/nonexistent-blog');

        $response->assertStatus(404);
    }

    /**
     * Test French locale with Russian slug returns 404.
     */
    public function test_fr_locale_with_ru_slug_returns_404(): void
    {
        $post = $this->createBlogPostWithTranslations();

        $response = $this->get('/fr/blog/testovaya-statya');

        $response->assertStatus(404);
    }

    // ========================================
    // OLD ROUTES STILL WORK (REGRESSION)
    // ========================================

    /**
     * Test original (non-localized) blog index still works.
     */
    public function test_original_blog_index_still_works(): void
    {
        $response = $this->get('/blog');

        $response->assertStatus(200);
    }

    /**
     * Test original (non-localized) blog show still works.
     */
    public function test_original_blog_show_still_works(): void
    {
        // Create category
        $category = BlogCategory::create([
            'name' => 'Original Category',
            'slug' => 'original-category',
        ]);

        // Create blog post
        $post = BlogPost::create([
            'category_id' => $category->id,
            'title' => 'Original Blog Post',
            'slug' => 'original-blog-post',
            'content' => 'Original content',
            'author_name' => 'Author',
            'is_published' => true,
            'published_at' => now(),
        ]);

        $response = $this->get('/blog/original-blog-post');

        $response->assertStatus(200);
    }

    // ========================================
    // MODEL TRANSLATION METHODS
    // ========================================

    /**
     * Test BlogPost model translation() method returns correct translation.
     */
    public function test_blog_post_translation_method_returns_correct_locale(): void
    {
        $post = $this->createBlogPostWithTranslations();
        $post->load('translations');

        app()->setLocale('ru');
        $translation = $post->translation();

        $this->assertNotNull($translation);
        $this->assertEquals('ru', $translation->locale);
        $this->assertEquals('Тестовая Статья', $translation->title);
    }

    /**
     * Test BlogPost model translationOrDefault() falls back correctly.
     */
    public function test_blog_post_translation_or_default_falls_back(): void
    {
        $post = $this->createBlogPostWithTranslations();
        $post->load('translations');

        // Set to a locale without translation
        app()->setLocale('de');

        // Should fall back to default locale (en)
        $translation = $post->translationOrDefault();

        $this->assertNotNull($translation);
        $this->assertEquals('en', $translation->locale);
    }

    // ========================================
    // DATABASE CONSTRAINTS
    // ========================================

    /**
     * Test unique constraint on (blog_post_id, locale).
     */
    public function test_cannot_create_duplicate_locale_for_same_post(): void
    {
        $post = $this->createBlogPostWithTranslations();

        $this->expectException(\Illuminate\Database\QueryException::class);

        // Try to create another English translation
        BlogPostTranslation::create([
            'blog_post_id' => $post->id,
            'locale' => 'en',
            'title' => 'Duplicate English',
            'slug' => 'duplicate-en',
        ]);
    }

    /**
     * Test unique constraint on (locale, slug).
     */
    public function test_cannot_create_duplicate_slug_for_same_locale(): void
    {
        $post = $this->createBlogPostWithTranslations();

        // Create another category and post
        $category = BlogCategory::create([
            'name' => 'Another Category',
            'slug' => 'another-category',
        ]);

        $post2 = BlogPost::create([
            'category_id' => $category->id,
            'title' => 'Another Post',
            'slug' => 'another-post',
            'content' => 'Content',
            'author_name' => 'Author',
            'is_published' => true,
            'published_at' => now(),
        ]);

        $this->expectException(\Illuminate\Database\QueryException::class);

        // Try to use same slug in same locale
        BlogPostTranslation::create([
            'blog_post_id' => $post2->id,
            'locale' => 'en',
            'title' => 'Different Title',
            'slug' => 'test-blog-en', // Same slug as first post's English translation
        ]);
    }
}
