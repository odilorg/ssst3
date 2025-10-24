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
        Schema::create('email_templates', function (Blueprint $table) {
            $table->id();

            // Template Info
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('subject');
            $table->text('body');
            $table->enum('type', [
                'initial_contact',
                'follow_up_1',
                'follow_up_2',
                'follow_up_3',
                'proposal',
                'custom'
            ])->default('custom');

            // Status
            $table->boolean('is_active')->default(true);

            // Usage Stats
            $table->integer('times_used')->default(0);
            $table->timestamp('last_used_at')->nullable();

            // Metadata
            $table->text('description')->nullable();
            $table->json('available_variables')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index('is_active');
            $table->index('type');
            $table->index('slug');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_templates');
    }
};
