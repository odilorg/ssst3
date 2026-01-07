<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tour_platform_mappings', function (Blueprint $table) {
            $table->id();
            $table->string('platform', 50); // gyg, viator, klook, etc.
            $table->string('external_tour_id')->nullable(); // Platform's tour ID
            $table->string('external_tour_name'); // How tour appears on platform
            $table->foreignId('tour_id')->constrained()->onDelete('cascade'); // Our internal tour
            $table->boolean('auto_confirm')->default(false); // Auto-set booking status to confirmed
            $table->string('default_booking_type')->default('private'); // private or group
            $table->text('notes')->nullable(); // Admin notes
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Unique constraint: one mapping per platform+external_tour
            $table->unique(['platform', 'external_tour_id'], 'unique_platform_tour');
            $table->index(['platform', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tour_platform_mappings');
    }
};
