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
        Schema::table('contracts', function (Blueprint $table) {
            // Add polymorphic columns (nullable for data migration)
            $table->string('supplier_type')->nullable()->after('id');
            $table->unsignedBigInteger('supplier_id')->nullable()->after('supplier_type');

            // Add indexes for polymorphic relationship
            $table->index(['supplier_type', 'supplier_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->dropIndex(['supplier_type', 'supplier_id']);
            $table->dropColumn(['supplier_type', 'supplier_id']);
        });
    }
};
