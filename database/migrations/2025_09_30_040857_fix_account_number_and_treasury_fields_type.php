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
            // Change account_number from bigInteger to string
            $table->string('account_number')->nullable()->change();
            $table->string('treasury_account_number')->nullable()->change();
            $table->string('treasury_stir')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            // Revert back to original types
            $table->bigInteger('account_number')->nullable()->change();
            $table->string('treasury_account_number')->nullable()->change();
            $table->string('treasury_stir')->nullable()->change();
        });
    }
};
