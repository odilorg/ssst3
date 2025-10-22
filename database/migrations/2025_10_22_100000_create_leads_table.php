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
        Schema::create('leads', function (Blueprint $table) {
            $table->id();

            // Reference Number (auto-generated)
            $table->string('reference')->unique(); // LD-2025-0001

            // Company Information (PRIMARY DATA)
            $table->string('company_name');
            $table->string('website')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->text('description')->nullable();

            // Contact Person (if available during gathering)
            $table->string('contact_name')->nullable();
            $table->string('contact_position')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('contact_phone')->nullable();

            // Location
            $table->string('country')->nullable();
            $table->string('city')->nullable();

            // Lead Source Tracking
            $table->enum('source', [
                'manual',           // Manually entered
                'csv_import',       // Bulk CSV import
                'web_scraper',      // Automated scraper
                'referral',         // Partner referral
                'directory',        // Industry directory
                'other'
            ])->default('manual');
            $table->string('source_url')->nullable(); // URL where found
            $table->text('source_notes')->nullable(); // How/where we found them

            // Lead Status Pipeline
            $table->enum('status', [
                'new',              // Just imported/created
                'researching',      // Gathering more info
                'qualified',        // Vetted, ready to contact
                'contacted',        // Initial email sent
                'responded',        // They replied
                'negotiating',      // In discussion
                'partner',          // Deal signed! (becomes Customer)
                'not_interested',   // Declined
                'invalid',          // Bad data/doesn't exist
                'on_hold'           // Paused for later
            ])->default('new');

            // Tourism-Specific Fields
            $table->json('tour_types')->nullable(); // ["adventure", "cultural", "luxury"]
            $table->json('target_markets')->nullable(); // Countries they serve
            $table->string('business_type')->nullable(); // Tour operator, DMC, Agency
            $table->integer('annual_volume')->nullable(); // Estimated pax per year
            $table->json('certifications')->nullable(); // IATA, ASTA, etc.

            // Assignment & Tracking
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('last_contacted_at')->nullable();
            $table->timestamp('next_followup_at')->nullable();
            $table->date('converted_to_customer_at')->nullable();
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();

            // Lead Quality Score (1-5 stars, calculated or manual)
            $table->tinyInteger('quality_score')->nullable();

            // General Notes
            $table->text('notes')->nullable();

            // Metadata
            $table->timestamps();
            $table->softDeletes(); // Soft delete for data retention

            // Indexes for performance
            $table->index('status');
            $table->index('source');
            $table->index('assigned_to');
            $table->index('next_followup_at');
            $table->index('company_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
