<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Add deposit/advance payment fields to tours table.
     * This allows operators to require an upfront deposit (e.g., 20%) to reduce no-shows.
     */
    public function up(): void
    {
        Schema::table('tours', function (Blueprint $table) {
            // Whether deposit/advance payment is required for this tour
            $table->boolean('deposit_required')->default(false)->after('show_price');
            
            // Deposit percentage (e.g., 20 for 20%)
            // If null, use company default setting
            $table->decimal('deposit_percentage', 5, 2)->nullable()->after('deposit_required');
            
            // Optional: minimum deposit amount in USD (overrides percentage if higher)
            $table->decimal('deposit_min_amount', 10, 2)->nullable()->after('deposit_percentage');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tours', function (Blueprint $table) {
            $table->dropColumn(['deposit_required', 'deposit_percentage', 'deposit_min_amount']);
        });
    }
};
