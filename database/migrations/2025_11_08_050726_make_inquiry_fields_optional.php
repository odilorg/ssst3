<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Make inquiry fields optional since the simplified inquiry form
     * only collects name, email, and message (no phone, country, dates, guests)
     */
    public function up(): void
    {
        Schema::table('tour_inquiries', function (Blueprint $table) {
            // Make these fields nullable - they're not collected in simplified form
            $table->string('customer_phone', 50)->nullable()->change();
            $table->string('customer_country', 100)->nullable()->change();
            $table->date('preferred_date')->nullable()->change();
            $table->integer('estimated_guests')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tour_inquiries', function (Blueprint $table) {
            // Note: Can't easily revert nullable to NOT NULL if existing records have NULLs
            // Just documenting the change
        });
    }
};
