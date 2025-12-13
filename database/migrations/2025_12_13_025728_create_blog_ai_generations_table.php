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
        Schema::create('blog_ai_generations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('blog_post_id')->nullable()->constrained('blog_posts')->nullOnDelete();
            $table->enum('status', ['pending', 'processing', 'completed', 'failed'])->default('pending');
            $table->json('input_parameters'); // User's prompt/requirements
            $table->json('ai_response')->nullable(); // Full AI response
            $table->text('error_message')->nullable();
            $table->integer('tokens_used')->nullable();
            $table->decimal('cost', 10, 4)->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'created_at']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blog_ai_generations');
    }
};
