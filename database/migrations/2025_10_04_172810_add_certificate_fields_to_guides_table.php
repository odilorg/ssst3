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
        Schema::table('guides', function (Blueprint $table) {
            $table->string('certificate_number')->nullable()->after('email');
            $table->date('certificate_issue_date')->nullable()->after('certificate_number');
            $table->enum('certificate_category', ['1', '2', '3'])->nullable()->after('certificate_issue_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('guides', function (Blueprint $table) {
            $table->dropColumn(['certificate_number', 'certificate_issue_date', 'certificate_category']);
        });
    }
};
