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
        Schema::create('passengers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->onDelete('cascade');

            // Personal Information
            $table->string('first_name');
            $table->string('last_name');
            $table->date('date_of_birth');

            // Passport Information
            $table->string('passport_number');
            $table->date('passport_expiry_date');
            $table->string('passport_nationality');
            $table->string('passport_scan_path')->nullable();

            // Emergency Contact
            $table->string('emergency_contact_name');
            $table->string('emergency_contact_phone');
            $table->string('emergency_contact_relationship');

            // Special Requirements
            $table->text('dietary_requirements')->nullable();
            $table->text('medical_conditions')->nullable();
            $table->text('special_needs')->nullable();

            $table->timestamps();

            // Indexes
            $table->index('booking_id');
            $table->index('passport_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('passengers');
    }
};
