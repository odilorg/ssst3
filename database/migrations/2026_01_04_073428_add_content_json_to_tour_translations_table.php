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
        Schema::table('tour_translations', function (Blueprint $table) {
            // Tour content sections as JSON
            $table->json('highlights_json')->nullable()->after('content');
            $table->json('itinerary_json')->nullable()->after('highlights_json');
            $table->json('included_json')->nullable()->after('itinerary_json');
            $table->json('excluded_json')->nullable()->after('included_json');
            $table->json('faq_json')->nullable()->after('excluded_json');
            $table->json('requirements_json')->nullable()->after('faq_json');
            $table->text('cancellation_policy')->nullable()->after('requirements_json');
            $table->text('meeting_instructions')->nullable()->after('cancellation_policy');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tour_translations', function (Blueprint $table) {
            $table->dropColumn([
                'highlights_json',
                'itinerary_json',
                'included_json',
                'excluded_json',
                'faq_json',
                'requirements_json',
                'cancellation_policy',
                'meeting_instructions',
            ]);
        });
    }
};
