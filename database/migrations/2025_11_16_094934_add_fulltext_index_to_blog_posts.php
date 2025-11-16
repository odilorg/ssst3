<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add fulltext index for better search performance
        // This allows using whereFullText() instead of LIKE queries
        Schema::table('blog_posts', function (Blueprint $table) {
            $table->fullText(['title', 'excerpt', 'content'], 'blog_posts_fulltext_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('blog_posts', function (Blueprint $table) {
            $table->dropFullText('blog_posts_fulltext_index');
        });
    }
};
