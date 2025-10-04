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
        Schema::table('companies', function (Blueprint $table) {
            $table->string('accountant_name')->nullable()->after('email');
            $table->boolean('has_treasury_account')->default(false)->after('bank_mfo');
            $table->string('treasury_account_number')->nullable()->after('has_treasury_account');
            $table->string('treasury_stir')->nullable()->after('treasury_account_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn(['accountant_name', 'has_treasury_account', 'treasury_account_number', 'treasury_stir']);
        });
    }
};
