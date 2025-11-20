<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Add performance indexes for frequently queried columns
     */
    public function up(): void
    {
        // ============================================
        // TOURS TABLE INDEXES
        // ============================================
        Schema::table('tours', function (Blueprint $table) {
            // Composite index for tour listing with sorting
            // Supports: WHERE is_active = 1 ORDER BY rating DESC, review_count DESC
            $table->index(['is_active', 'rating', 'review_count'], 'idx_tours_active_rating');

            // Composite index for city filtering
            // Supports: WHERE city_id = X AND is_active = 1
            $table->index(['city_id', 'is_active'], 'idx_tours_city_active');

            // Index for chronological sorting
            // Supports: ORDER BY created_at DESC
            $table->index('created_at', 'idx_tours_created_at');

            // Index for slug lookups (if not already unique indexed)
            // Check if slug already has unique index before adding
            if (!$this->indexExists('tours', 'slug')) {
                $table->index('slug', 'idx_tours_slug');
            }
        });

        // ============================================
        // BLOG_POSTS TABLE INDEXES
        // ============================================
        Schema::table('blog_posts', function (Blueprint $table) {
            // Composite index for published posts with date sorting
            // Supports: WHERE is_published = 1 AND published_at IS NOT NULL ORDER BY published_at DESC
            $table->index(['is_published', 'published_at'], 'idx_blog_posts_published');

            // Composite index for category filtering
            // Supports: WHERE category_id = X AND is_published = 1
            $table->index(['category_id', 'is_published'], 'idx_blog_posts_category');

            // Composite index for featured posts
            // Supports: WHERE is_featured = 1 AND is_published = 1
            $table->index(['is_featured', 'is_published'], 'idx_blog_posts_featured');

            // Index for popular posts sorting
            // Supports: ORDER BY view_count DESC
            $table->index('view_count', 'idx_blog_posts_view_count');

            // Index for city relationship
            // Supports: WHERE city_id = X
            $table->index('city_id', 'idx_blog_posts_city_id');
        });

        // ============================================
        // REVIEWS TABLE INDEXES
        // ============================================
        Schema::table('reviews', function (Blueprint $table) {
            // Composite index for approved tour reviews
            // Supports: WHERE tour_id = X AND is_approved = 1 ORDER BY created_at DESC
            $table->index(['tour_id', 'is_approved', 'created_at'], 'idx_reviews_tour_approved');

            // Index for rating-based queries
            // Supports: WHERE rating = 5 AND is_approved = 1
            $table->index(['rating', 'is_approved'], 'idx_reviews_rating_approved');
        });

        // ============================================
        // BLOG_COMMENTS TABLE INDEXES
        // ============================================
        Schema::table('blog_comments', function (Blueprint $table) {
            // Composite index for approved comments by post
            // Supports: WHERE blog_post_id = X AND status = 'approved'
            $table->index(['blog_post_id', 'status'], 'idx_blog_comments_post_status');

            // Index for threaded comments (replies)
            // Supports: WHERE parent_id IS NULL / IS NOT NULL
            $table->index('parent_id', 'idx_blog_comments_parent_id');
        });

        // ============================================
        // ITINERARY_ITEMS TABLE INDEXES
        // ============================================
        Schema::table('itinerary_items', function (Blueprint $table) {
            // Composite index for tour itinerary loading
            // Supports: WHERE tour_id = X AND parent_id IS NULL ORDER BY sort_order
            $table->index(['tour_id', 'parent_id', 'sort_order'], 'idx_itinerary_tour_parent_sort');
        });

        // ============================================
        // TOUR_EXTRAS TABLE INDEXES
        // ============================================
        Schema::table('tour_extras', function (Blueprint $table) {
            // Composite index for active extras by tour
            // Supports: WHERE tour_id = X AND is_active = 1 ORDER BY sort_order
            $table->index(['tour_id', 'is_active', 'sort_order'], 'idx_tour_extras_active');
        });

        // ============================================
        // TOUR_FAQS TABLE INDEXES
        // ============================================
        Schema::table('tour_faqs', function (Blueprint $table) {
            // Composite index for FAQs by tour
            // Supports: WHERE tour_id = X ORDER BY sort_order
            $table->index(['tour_id', 'sort_order'], 'idx_tour_faqs_tour_sort');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop tours indexes
        Schema::table('tours', function (Blueprint $table) {
            $table->dropIndex('idx_tours_active_rating');
            $table->dropIndex('idx_tours_city_active');
            $table->dropIndex('idx_tours_created_at');
            if ($this->indexExists('tours', 'idx_tours_slug')) {
                $table->dropIndex('idx_tours_slug');
            }
        });

        // Drop blog_posts indexes
        Schema::table('blog_posts', function (Blueprint $table) {
            $table->dropIndex('idx_blog_posts_published');
            $table->dropIndex('idx_blog_posts_category');
            $table->dropIndex('idx_blog_posts_featured');
            $table->dropIndex('idx_blog_posts_view_count');
            $table->dropIndex('idx_blog_posts_city_id');
        });

        // Drop reviews indexes
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropIndex('idx_reviews_tour_approved');
            $table->dropIndex('idx_reviews_rating_approved');
        });

        // Drop blog_comments indexes
        Schema::table('blog_comments', function (Blueprint $table) {
            $table->dropIndex('idx_blog_comments_post_status');
            $table->dropIndex('idx_blog_comments_parent_id');
        });

        // Drop itinerary_items indexes
        Schema::table('itinerary_items', function (Blueprint $table) {
            $table->dropIndex('idx_itinerary_tour_parent_sort');
        });

        // Drop tour_extras indexes
        Schema::table('tour_extras', function (Blueprint $table) {
            $table->dropIndex('idx_tour_extras_active');
        });

        // Drop tour_faqs indexes
        Schema::table('tour_faqs', function (Blueprint $table) {
            $table->dropIndex('idx_tour_faqs_tour_sort');
        });
    }

    /**
     * Check if an index exists on a table
     */
    private function indexExists(string $table, string $column): bool
    {
        $indexes = Schema::getIndexes($table);
        foreach ($indexes as $index) {
            if (in_array($column, $index['columns'])) {
                return true;
            }
        }
        return false;
    }
};
