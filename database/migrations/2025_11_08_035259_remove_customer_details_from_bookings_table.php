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
        Schema::table('bookings', function (Blueprint $table) {
            // Drop denormalized customer fields - using customer relationship instead
            // Guard: columns may not exist in fresh installs (already omitted from base migration)
            $toDrop = array_filter(
                ['customer_name', 'customer_email', 'customer_phone', 'customer_country'],
                fn ($col) => Schema::hasColumn('bookings', $col)
            );
            if (!empty($toDrop)) {
                $table->dropColumn(array_values($toDrop));
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Restore columns if needed to rollback
            $table->string('customer_name')->nullable()->after('customer_id');
            $table->string('customer_email')->nullable()->after('customer_name');
            $table->string('customer_phone')->nullable()->after('customer_email');
            $table->string('customer_country')->nullable()->after('customer_phone');
        });
    }
};
