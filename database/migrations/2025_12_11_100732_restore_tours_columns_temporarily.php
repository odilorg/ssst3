<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Restore columns that were renamed in the failed migration
        if (Schema::hasColumn('tours', 'title_old')) {
            Schema::table('tours', function (Blueprint $table) {
                $table->renameColumn('title_old', 'title');
                $table->renameColumn('short_description_old', 'short_description');
                $table->renameColumn('long_description_old', 'long_description');
                $table->renameColumn('seo_title_old', 'seo_title');
                $table->renameColumn('seo_description_old', 'seo_description');
                $table->renameColumn('seo_keywords_old', 'seo_keywords');
            });
        }
    }

    public function down(): void
    {
        //
    }
};
