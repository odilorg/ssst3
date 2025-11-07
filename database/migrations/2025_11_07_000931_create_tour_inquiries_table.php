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
        Schema::create('tour_inquiries', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique()->comment('Auto-generated: INQ-2025-001');
            $table->foreignId('tour_id')->constrained()->onDelete('cascade');

            // Customer information (stored directly, not linked to customers table)
            $table->string('customer_name');
            $table->string('customer_email');
            $table->string('customer_phone')->nullable();
            $table->string('customer_country')->nullable();

            // Inquiry details
            $table->date('preferred_date')->nullable()->comment('Optional - they might not know yet');
            $table->integer('estimated_guests')->nullable()->comment('Optional - might be flexible');
            $table->text('message')->comment('Their questions/message');

            // Status tracking
            $table->enum('status', ['new', 'replied', 'converted', 'closed'])->default('new');
            $table->timestamp('replied_at')->nullable();
            $table->foreignId('replied_by')->nullable()->constrained('users')->onDelete('set null');

            // Conversion tracking
            $table->foreignId('booking_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamp('converted_at')->nullable();

            $table->timestamps();

            // Indexes for performance
            $table->index('status');
            $table->index('created_at');
            $table->index(['tour_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tour_inquiries');
    }
};
