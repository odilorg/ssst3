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
        // Step 0: Drop check constraints that prevent column renaming
        $this->dropCheckConstraints();

        // Step 1: Rename existing columns to *_old (for safety/rollback)
        Schema::table('tours', function (Blueprint $table) {
            $table->renameColumn('title', 'title_old');
            $table->renameColumn('short_description', 'short_description_old');
            $table->renameColumn('long_description', 'long_description_old');
            $table->renameColumn('seo_title', 'seo_title_old');
            $table->renameColumn('seo_description', 'seo_description_old');
            $table->renameColumn('seo_keywords', 'seo_keywords_old');
            $table->renameColumn('highlights', 'highlights_old');
            $table->renameColumn('included_items', 'included_items_old');
            $table->renameColumn('excluded_items', 'excluded_items_old');
        });

        // Step 2: Add new JSON columns
        Schema::table('tours', function (Blueprint $table) {
            $table->json('title')->nullable()->after('id');
            $table->json('short_description')->nullable()->after('slug');
            $table->json('long_description')->nullable()->after('short_description');
            $table->json('seo_title')->nullable()->after('seo_keywords_old');
            $table->json('seo_description')->nullable()->after('seo_title');
            $table->json('seo_keywords')->nullable()->after('seo_description');
            $table->json('highlights')->nullable()->after('gallery_images');
            $table->json('included_items')->nullable()->after('highlights');
            $table->json('excluded_items')->nullable()->after('included_items');
        });

        // Step 3: Migrate existing data to English locale
        $tours = DB::table('tours')->get();

        echo "Migrating " . count($tours) . " tours to translatable format...\n";

        foreach ($tours as $tour) {
            $update = [
                'title' => json_encode(['en' => $tour->title_old]),
                'short_description' => json_encode(['en' => $tour->short_description_old]),
                'long_description' => json_encode(['en' => $tour->long_description_old]),
                'seo_title' => json_encode(['en' => $tour->seo_title_old]),
                'seo_description' => json_encode(['en' => $tour->seo_description_old]),
                'seo_keywords' => json_encode(['en' => $tour->seo_keywords_old]),
            ];

            // Handle JSON array fields (highlights, included_items, excluded_items)
            // These are already JSON arrays, we need to wrap them in locale structure
            if ($tour->highlights_old) {
                $highlightsArray = json_decode($tour->highlights_old, true);
                $update['highlights'] = json_encode(['en' => $highlightsArray ?? []]);
            }

            if ($tour->included_items_old) {
                $includedArray = json_decode($tour->included_items_old, true);
                $update['included_items'] = json_encode(['en' => $includedArray ?? []]);
            }

            if ($tour->excluded_items_old) {
                $excludedArray = json_decode($tour->excluded_items_old, true);
                $update['excluded_items'] = json_encode(['en' => $excludedArray ?? []]);
            }

            DB::table('tours')->where('id', $tour->id)->update($update);
        }

        echo "Migration completed successfully!\n";
    }

    /**
     * Drop check constraints that prevent column renaming
     */
    private function dropCheckConstraints(): void
    {
        $constraints = DB::select("
            SELECT CONSTRAINT_NAME 
            FROM information_schema.TABLE_CONSTRAINTS 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'tours' 
            AND CONSTRAINT_TYPE = 'CHECK'
        ");

        foreach ($constraints as $constraint) {
            try {
                DB::statement("ALTER TABLE tours DROP CHECK {$constraint->CONSTRAINT_NAME}");
                echo "Dropped constraint: {$constraint->CONSTRAINT_NAME}\n";
            } catch (\Exception $e) {
                echo "Warning: Could not drop constraint {$constraint->CONSTRAINT_NAME}: {$e->getMessage()}\n";
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restore from old columns
        if (Schema::hasColumn('tours', 'title_old')) {
            // Drop new JSON columns
            Schema::table('tours', function (Blueprint $table) {
                $table->dropColumn([
                    'title', 'short_description', 'long_description',
                    'seo_title', 'seo_description', 'seo_keywords',
                    'highlights', 'included_items', 'excluded_items'
                ]);
            });

            // Rename old columns back to original names
            Schema::table('tours', function (Blueprint $table) {
                $table->renameColumn('title_old', 'title');
                $table->renameColumn('short_description_old', 'short_description');
                $table->renameColumn('long_description_old', 'long_description');
                $table->renameColumn('seo_title_old', 'seo_title');
                $table->renameColumn('seo_description_old', 'seo_description');
                $table->renameColumn('seo_keywords_old', 'seo_keywords');
                $table->renameColumn('highlights_old', 'highlights');
                $table->renameColumn('included_items_old', 'included_items');
                $table->renameColumn('excluded_items_old', 'excluded_items');
            });
        }
    }
};
