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
        Schema::table('monuments', function (Blueprint $table) {
            // Add new pricing fields
            $table->decimal('foreigner_adult_price', 10, 2)->nullable()->after('ticket_price');
            $table->decimal('foreigner_child_price', 10, 2)->nullable()->after('foreigner_adult_price');
            $table->decimal('local_adult_price', 10, 2)->nullable()->after('foreigner_child_price');
            $table->decimal('local_child_price', 10, 2)->nullable()->after('local_adult_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('monuments', function (Blueprint $table) {
            $table->dropColumn([
                'foreigner_adult_price',
                'foreigner_child_price',
                'local_adult_price',
                'local_child_price',
            ]);
        });
    }
};
