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
        Schema::table('transports', function (Blueprint $table) {
            // Add make field to store vehicle manufacturer (Chevrolet, Toyota, etc.)
            // This separates make from model (e.g., Chevrolet Cobalt → make=Chevrolet, model=Cobalt)
            $table->string('make')->nullable()->after('plate_number');

            // Remove category field - it's redundant since we can get it from transport_type.category
            // This eliminates confusion and ensures single source of truth
            $table->dropColumn('category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transports', function (Blueprint $table) {
            // Restore category field
            $table->enum('category', ['bus', 'car', 'mikro_bus', 'mini_van', 'air', 'rail'])
                ->nullable()
                ->after('number_of_seat');

            // Remove make field
            $table->dropColumn('make');
        });
    }
};
