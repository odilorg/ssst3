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
        Schema::create('blog_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('blog_post_id')->constrained()->onDelete('cascade');
            $table->foreignId('parent_id')->nullable()->constrained('blog_comments')->onDelete('cascade');

            // Author information (for guest comments)
            $table->string('author_name', 100);
            $table->string('author_email', 150);
            $table->string('author_website', 200)->nullable();
            $table->ipAddress('author_ip');
            $table->string('author_user_agent', 255)->nullable();

            // Comment content
            $table->text('comment');

            // Moderation
            $table->enum('status', ['pending', 'approved', 'spam', 'trash'])->default('pending');
            $table->integer('spam_score')->default(0); // 0-100

            // Metadata
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->integer('flag_count')->default(0);

            $table->timestamps();

            // Indexes for performance
            $table->index('blog_post_id');
            $table->index('parent_id');
            $table->index('status');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blog_comments');
    }
};
