<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Step 1: Rename existing columns (for safety)
        Schema::table('tour_faqs', function (Blueprint $table) {
            $table->renameColumn('question', 'question_old');
            $table->renameColumn('answer', 'answer_old');
        });

        // Step 2: Add new JSON columns
        Schema::table('tour_faqs', function (Blueprint $table) {
            $table->json('question')->nullable()->after('id');
            $table->json('answer')->nullable()->after('question');
        });

        // Step 3: Migrate existing data to English locale
        $faqs = DB::table('tour_faqs')->get();

        foreach ($faqs as $faq) {
            DB::table('tour_faqs')->where('id', $faq->id)->update([
                'question' => json_encode(['en' => $faq->question_old ?? '']),
                'answer' => json_encode(['en' => $faq->answer_old ?? '']),
            ]);
        }

        echo "Migrated " . count($faqs) . " FAQs to translatable format\n";
    }

    public function down(): void
    {
        // Restore from old columns
        if (Schema::hasColumn('tour_faqs', 'question_old')) {
            Schema::table('tour_faqs', function (Blueprint $table) {
                $table->dropColumn(['question', 'answer']);
            });

            Schema::table('tour_faqs', function (Blueprint $table) {
                $table->renameColumn('question_old', 'question');
                $table->renameColumn('answer_old', 'answer');
            });
        }
    }
};
