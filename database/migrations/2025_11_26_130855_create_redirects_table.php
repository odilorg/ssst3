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
        Schema::create('redirects', function (Blueprint $table) {
            $table->id();
            $table->string('old_path')->unique()->comment('Source path without domain (e.g., /en/tour/samarkand-day-tour)');
            $table->string('new_path')->comment('Destination path or full URL (e.g., /tours/samarkand-heritage)');
            $table->integer('status_code')->default(301)->comment('HTTP status code: 301 (permanent), 302 (temporary)');
            $table->boolean('is_active')->default(true)->comment('Enable/disable this redirect');
            $table->integer('hits')->default(0)->comment('Number of times this redirect was used');
            $table->text('notes')->nullable()->comment('Admin notes about why this redirect exists');
            $table->timestamps();

            // Indexes for performance
            $table->index('old_path');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('redirects');
    }
};
