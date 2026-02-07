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
        Schema::table('tours', function (Blueprint $table) {
            // Tour type support flags
            $table->boolean('supports_private')->default(true)->after('is_active');
            $table->boolean('supports_group')->default(false)->after('supports_private');

            // Private tour pricing configuration
            $table->decimal('private_base_price', 10, 2)->nullable()->after('price_per_person');
            $table->unsignedSmallInteger('private_min_guests')->default(1)->after('private_base_price');
            $table->unsignedSmallInteger('private_max_guests')->default(15)->after('private_min_guests');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tours', function (Blueprint $table) {
            $table->dropColumn([
                'supports_private',
                'supports_group',
                'private_base_price',
                'private_min_guests',
                'private_max_guests',
            ]);
        });
    }
};
