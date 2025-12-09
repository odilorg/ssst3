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
        // Check if tours table already exists
        if (Schema::hasTable('tours')) {
            // Table exists - rename old columns to new ones
            Schema::table('tours', function (Blueprint $table) {
                // Rename old columns to new ones
                if (Schema::hasColumn('tours', 'name') && !Schema::hasColumn('tours', 'title')) {
                    $table->renameColumn('name', 'title');
                }

                if (Schema::hasColumn('tours', 'description') && !Schema::hasColumn('tours', 'long_description')) {
                    $table->renameColumn('description', 'long_description');
                    $table->longText('long_description')->change();
                }

                if (Schema::hasColumn('tours', 'image')) {
                    // We'll keep 'image' for now and add hero_image later via another migration
                }

                // Add missing columns if they don't exist
                if (!Schema::hasColumn('tours', 'short_description')) {
                    $table->string('short_description')->nullable()->after('title');
                }

                if (!Schema::hasColumn('tours', 'duration_days')) {
                    $table->tinyInteger('duration_days')->default(1)->after('short_description');
                }

                if (!Schema::hasColumn('tours', 'is_active')) {
                    $table->boolean('is_active')->default(true);
                }
            });
        } else {
            // Table doesn't exist - create it fresh
            Schema::create('tours', function (Blueprint $table) {
                $table->id();
                $table->timestamps();
                $table->string('title');
                $table->tinyInteger('duration_days');
                $table->string('short_description')->nullable();
                $table->longText('long_description')->nullable();
                $table->boolean('is_active')->default(true);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tours');
    }
};
