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
        Schema::create('booking_itinerary_item_assignments', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('booking_itinerary_item_id')->constrained('booking_itinerary_items', 'id', 'booking_item_assignments_item_id_foreign')->onDelete('cascade');
            $table->string('assignable_type');
            $table->unsignedBigInteger('assignable_id');
            $table->string('role')->nullable();
            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('cost', 10, 2)->nullable();
            $table->string('currency', 3)->default('USD');
            $table->enum('status', ['pending', 'confirmed', 'completed', 'cancelled'])->default('pending');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->text('notes')->nullable();
            $table->softDeletes();
            
            $table->index(['assignable_type', 'assignable_id'], 'assignments_polymorphic_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_itinerary_item_assignments');
    }
};
