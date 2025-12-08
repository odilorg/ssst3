<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $columns = [
            'name_translations',
            'tagline_translations',
            'description_translations',
            'short_description_translations',
            'long_description_translations',
            'meta_title_translations',
            'meta_description_translations'
        ];

        Schema::table('cities', function (Blueprint $table) use ($columns) {
            foreach ($columns as $column) {
                if (Schema::hasColumn('cities', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }

    public function down(): void
    {
        // No rollback needed
    }
};
