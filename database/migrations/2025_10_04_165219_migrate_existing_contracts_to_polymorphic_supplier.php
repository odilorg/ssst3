<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Migrate existing contracts to use polymorphic supplier
        DB::table('contracts')->update([
            'supplier_type' => 'App\\Models\\Company',
            'supplier_id' => DB::raw('supplier_company_id')
        ]);

        // Now make columns NOT NULL and drop old column
        Schema::table('contracts', function (Blueprint $table) {
            $table->string('supplier_type')->nullable(false)->change();
            $table->unsignedBigInteger('supplier_id')->nullable(false)->change();

            // Drop the old foreign key and column
            $table->dropForeign(['supplier_company_id']);
            $table->dropColumn('supplier_company_id');

            // Update index
            $table->dropIndex(['supplier_company_id', 'status']);
            $table->index(['supplier_type', 'supplier_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contracts', function (Blueprint $table) {
            // Re-add the old column
            $table->foreignId('supplier_company_id')->after('id')->constrained('companies')->onDelete('cascade');

            // Remove the new index
            $table->dropIndex(['supplier_type', 'supplier_id', 'status']);

            // Re-add old index
            $table->index(['supplier_company_id', 'status']);
        });

        // Migrate data back (only works if all suppliers are companies)
        DB::table('contracts')
            ->where('supplier_type', 'App\\Models\\Company')
            ->update([
                'supplier_company_id' => DB::raw('supplier_id')
            ]);

        // Drop polymorphic columns
        Schema::table('contracts', function (Blueprint $table) {
            $table->dropColumn(['supplier_type', 'supplier_id']);
        });
    }
};
