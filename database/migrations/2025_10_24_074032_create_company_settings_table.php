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
        Schema::create('company_settings', function (Blueprint $table) {
            $table->id();

            // Basic Information
            $table->string('company_name');
            $table->string('legal_name')->nullable();
            $table->string('tax_id')->nullable();
            $table->string('registration_number')->nullable();
            $table->date('founded_date')->nullable();

            // Contact Information
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('mobile')->nullable();
            $table->string('fax')->nullable();
            $table->string('website')->nullable();

            // Social Media
            $table->string('facebook')->nullable();
            $table->string('instagram')->nullable();
            $table->string('linkedin')->nullable();
            $table->string('twitter')->nullable();

            // Addresses
            $table->text('registered_address')->nullable();
            $table->text('office_address')->nullable();
            $table->text('billing_address')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->nullable();
            $table->string('postal_code')->nullable();

            // Banking Information (JSON for multiple accounts)
            $table->json('bank_accounts')->nullable();

            // Legal & Compliance
            $table->string('license_number')->nullable();
            $table->date('license_expiry')->nullable();
            $table->text('insurance_details')->nullable();
            $table->text('terms_and_conditions')->nullable();

            // Branding
            $table->string('logo_path')->nullable();
            $table->string('primary_color')->nullable();
            $table->string('secondary_color')->nullable();
            $table->text('email_signature')->nullable();

            // Additional Settings
            $table->string('currency', 3)->default('USD');
            $table->string('timezone')->default('Asia/Tashkent');
            $table->string('date_format')->default('Y-m-d');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_settings');
    }
};
