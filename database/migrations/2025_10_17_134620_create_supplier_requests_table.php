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
        Schema::create('supplier_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->onDelete('cascade');
            $table->enum('supplier_type', ['hotel', 'transport', 'guide', 'restaurant']);
            $table->unsignedBigInteger('supplier_id'); // ID of the specific supplier
            $table->enum('status', ['pending', 'confirmed', 'rejected', 'expired'])->default('pending');
            $table->json('request_data'); // Store request details (dates, requirements, etc.)
            $table->timestamp('generated_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->text('notes')->nullable();
            $table->string('pdf_path')->nullable(); // Path to generated PDF file
            $table->timestamps();
            
            // Indexes for better performance
            $table->index(['booking_id', 'supplier_type']);
            $table->index(['status', 'expires_at']);
            $table->index('supplier_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplier_requests');
    }
};
