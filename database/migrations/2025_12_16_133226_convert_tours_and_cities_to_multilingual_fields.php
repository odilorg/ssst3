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
        // === PHASE 1: TOURS TABLE ===
        $this->convertToursTable();

        // === PHASE 2: CITIES TABLE ===
        $this->convertCitiesTable();
    }

    /**
     * Convert tours table fields to multilingual JSON
     */
    private function convertToursTable(): void
    {
        // Step 1: Add temporary columns to preserve data
        Schema::table('tours', function (Blueprint $table) {
            $table->string('title_temp')->nullable()->after('title');
            $table->string('short_description_temp')->nullable()->after('short_description');
            $table->longText('long_description_temp')->nullable()->after('long_description');
            $table->json('highlights_temp')->nullable()->after('highlights');
            $table->json('included_items_temp')->nullable()->after('included_items');
            $table->json('excluded_items_temp')->nullable()->after('excluded_items');
            $table->json('requirements_temp')->nullable()->after('requirements');
        });

        // Step 2: Copy existing data to temp columns
        DB::statement('UPDATE tours SET title_temp = title');
        DB::statement('UPDATE tours SET short_description_temp = short_description');
        DB::statement('UPDATE tours SET long_description_temp = long_description');
        DB::statement('UPDATE tours SET highlights_temp = highlights');
        DB::statement('UPDATE tours SET included_items_temp = included_items');
        DB::statement('UPDATE tours SET excluded_items_temp = excluded_items');
        DB::statement('UPDATE tours SET requirements_temp = requirements');

        // Step 3: Drop original columns
        Schema::table('tours', function (Blueprint $table) {
            $table->dropColumn([
                'title',
                'short_description',
                'long_description',
                'highlights',
                'included_items',
                'excluded_items',
                'requirements'
            ]);
        });

        // Step 4: Create new JSON columns
        Schema::table('tours', function (Blueprint $table) {
            $table->json('title')->after('slug');
            $table->json('short_description')->nullable()->after('title');
            $table->json('long_description')->nullable()->after('short_description');
            $table->json('highlights')->nullable();
            $table->json('included_items')->nullable();
            $table->json('excluded_items')->nullable();
            $table->json('requirements')->nullable();
        });

        // Step 5: Migrate data to JSON format with 'ru' locale
        $tours = DB::table('tours')->get();

        foreach ($tours as $tour) {
            $update = [];

            // Simple text fields: wrap in {"ru": "value"}
            if ($tour->title_temp) {
                $update['title'] = json_encode(['ru' => $tour->title_temp]);
            }
            if ($tour->short_description_temp) {
                $update['short_description'] = json_encode(['ru' => $tour->short_description_temp]);
            }
            if ($tour->long_description_temp) {
                $update['long_description'] = json_encode(['ru' => $tour->long_description_temp]);
            }

            // JSON array fields: wrap in {"ru": [...]}
            if ($tour->highlights_temp) {
                $highlights = json_decode($tour->highlights_temp, true);
                $update['highlights'] = json_encode(['ru' => $highlights ?: []]);
            }
            if ($tour->included_items_temp) {
                $included = json_decode($tour->included_items_temp, true);
                $update['included_items'] = json_encode(['ru' => $included ?: []]);
            }
            if ($tour->excluded_items_temp) {
                $excluded = json_decode($tour->excluded_items_temp, true);
                $update['excluded_items'] = json_encode(['ru' => $excluded ?: []]);
            }
            if ($tour->requirements_temp) {
                $requirements = json_decode($tour->requirements_temp, true);
                $update['requirements'] = json_encode(['ru' => $requirements ?: []]);
            }

            if (!empty($update)) {
                DB::table('tours')->where('id', $tour->id)->update($update);
            }
        }

        // Step 6: Drop temporary columns
        Schema::table('tours', function (Blueprint $table) {
            $table->dropColumn([
                'title_temp',
                'short_description_temp',
                'long_description_temp',
                'highlights_temp',
                'included_items_temp',
                'excluded_items_temp',
                'requirements_temp'
            ]);
        });
    }

    /**
     * Convert cities table fields to multilingual JSON
     */
    private function convertCitiesTable(): void
    {
        // Step 1: Add temporary columns
        Schema::table('cities', function (Blueprint $table) {
            $table->string('name_temp')->nullable()->after('name');
            $table->string('tagline_temp')->nullable()->after('tagline');
            $table->string('description_temp')->nullable()->after('description');
            $table->text('short_description_temp')->nullable()->after('short_description');
            $table->text('long_description_temp')->nullable()->after('long_description');
            $table->string('meta_title_temp')->nullable()->after('meta_title');
            $table->text('meta_description_temp')->nullable()->after('meta_description');
        });

        // Step 2: Copy existing data to temp columns
        DB::statement('UPDATE cities SET name_temp = name');
        DB::statement('UPDATE cities SET tagline_temp = tagline');
        DB::statement('UPDATE cities SET description_temp = description');
        DB::statement('UPDATE cities SET short_description_temp = short_description');
        DB::statement('UPDATE cities SET long_description_temp = long_description');
        DB::statement('UPDATE cities SET meta_title_temp = meta_title');
        DB::statement('UPDATE cities SET meta_description_temp = meta_description');

        // Step 3: Drop original columns
        Schema::table('cities', function (Blueprint $table) {
            $table->dropColumn([
                'name',
                'tagline',
                'description',
                'short_description',
                'long_description',
                'meta_title',
                'meta_description'
            ]);
        });

        // Step 4: Create new JSON columns
        Schema::table('cities', function (Blueprint $table) {
            $table->json('name')->after('id');
            $table->json('tagline')->nullable();
            $table->json('description')->nullable();
            $table->json('short_description')->nullable();
            $table->json('long_description')->nullable();
            $table->json('meta_title')->nullable();
            $table->json('meta_description')->nullable();
        });

        // Step 5: Migrate data to JSON format with 'ru' locale
        $cities = DB::table('cities')->get();

        foreach ($cities as $city) {
            $update = [];

            if ($city->name_temp) {
                $update['name'] = json_encode(['ru' => $city->name_temp]);
            }
            if ($city->tagline_temp) {
                $update['tagline'] = json_encode(['ru' => $city->tagline_temp]);
            }
            if ($city->description_temp) {
                $update['description'] = json_encode(['ru' => $city->description_temp]);
            }
            if ($city->short_description_temp) {
                $update['short_description'] = json_encode(['ru' => $city->short_description_temp]);
            }
            if ($city->long_description_temp) {
                $update['long_description'] = json_encode(['ru' => $city->long_description_temp]);
            }
            if ($city->meta_title_temp) {
                $update['meta_title'] = json_encode(['ru' => $city->meta_title_temp]);
            }
            if ($city->meta_description_temp) {
                $update['meta_description'] = json_encode(['ru' => $city->meta_description_temp]);
            }

            if (!empty($update)) {
                DB::table('cities')->where('id', $city->id)->update($update);
            }
        }

        // Step 6: Drop temporary columns
        Schema::table('cities', function (Blueprint $table) {
            $table->dropColumn([
                'name_temp',
                'tagline_temp',
                'description_temp',
                'short_description_temp',
                'long_description_temp',
                'meta_title_temp',
                'meta_description_temp'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // === REVERSE TOURS TABLE ===
        // Step 1: Add temp columns with JSON data
        Schema::table('tours', function (Blueprint $table) {
            $table->json('title_temp')->nullable()->after('title');
            $table->json('short_description_temp')->nullable()->after('short_description');
            $table->json('long_description_temp')->nullable()->after('long_description');
            $table->json('highlights_temp')->nullable()->after('highlights');
            $table->json('included_items_temp')->nullable()->after('included_items');
            $table->json('excluded_items_temp')->nullable()->after('excluded_items');
            $table->json('requirements_temp')->nullable()->after('requirements');
        });

        // Step 2: Copy JSON data to temp
        DB::statement('UPDATE tours SET title_temp = title');
        DB::statement('UPDATE tours SET short_description_temp = short_description');
        DB::statement('UPDATE tours SET long_description_temp = long_description');
        DB::statement('UPDATE tours SET highlights_temp = highlights');
        DB::statement('UPDATE tours SET included_items_temp = included_items');
        DB::statement('UPDATE tours SET excluded_items_temp = excluded_items');
        DB::statement('UPDATE tours SET requirements_temp = requirements');

        // Step 3: Drop JSON columns
        Schema::table('tours', function (Blueprint $table) {
            $table->dropColumn([
                'title',
                'short_description',
                'long_description',
                'highlights',
                'included_items',
                'excluded_items',
                'requirements'
            ]);
        });

        // Step 4: Create original columns
        Schema::table('tours', function (Blueprint $table) {
            $table->string('title')->after('slug');
            $table->string('short_description')->nullable();
            $table->longText('long_description')->nullable();
            $table->json('highlights')->nullable();
            $table->json('included_items')->nullable();
            $table->json('excluded_items')->nullable();
            $table->json('requirements')->nullable();
        });

        // Step 5: Extract 'ru' values back to original columns
        $tours = DB::table('tours')->get();
        foreach ($tours as $tour) {
            $update = [];

            // Extract text fields
            if ($tour->title_temp) {
                $titleData = json_decode($tour->title_temp, true);
                $update['title'] = $titleData['ru'] ?? '';
            }
            if ($tour->short_description_temp) {
                $shortData = json_decode($tour->short_description_temp, true);
                $update['short_description'] = $shortData['ru'] ?? null;
            }
            if ($tour->long_description_temp) {
                $longData = json_decode($tour->long_description_temp, true);
                $update['long_description'] = $longData['ru'] ?? null;
            }

            // Extract array fields
            if ($tour->highlights_temp) {
                $highlightsData = json_decode($tour->highlights_temp, true);
                $update['highlights'] = json_encode($highlightsData['ru'] ?? []);
            }
            if ($tour->included_items_temp) {
                $includedData = json_decode($tour->included_items_temp, true);
                $update['included_items'] = json_encode($includedData['ru'] ?? []);
            }
            if ($tour->excluded_items_temp) {
                $excludedData = json_decode($tour->excluded_items_temp, true);
                $update['excluded_items'] = json_encode($excludedData['ru'] ?? []);
            }
            if ($tour->requirements_temp) {
                $reqData = json_decode($tour->requirements_temp, true);
                $update['requirements'] = json_encode($reqData['ru'] ?? []);
            }

            if (!empty($update)) {
                DB::table('tours')->where('id', $tour->id)->update($update);
            }
        }

        // Step 6: Drop temp columns
        Schema::table('tours', function (Blueprint $table) {
            $table->dropColumn([
                'title_temp',
                'short_description_temp',
                'long_description_temp',
                'highlights_temp',
                'included_items_temp',
                'excluded_items_temp',
                'requirements_temp'
            ]);
        });

        // === REVERSE CITIES TABLE (similar pattern) ===
        Schema::table('cities', function (Blueprint $table) {
            $table->json('name_temp')->nullable()->after('name');
            $table->json('tagline_temp')->nullable()->after('tagline');
            $table->json('description_temp')->nullable()->after('description');
            $table->json('short_description_temp')->nullable()->after('short_description');
            $table->json('long_description_temp')->nullable()->after('long_description');
            $table->json('meta_title_temp')->nullable()->after('meta_title');
            $table->json('meta_description_temp')->nullable()->after('meta_description');
        });

        DB::statement('UPDATE cities SET name_temp = name');
        DB::statement('UPDATE cities SET tagline_temp = tagline');
        DB::statement('UPDATE cities SET description_temp = description');
        DB::statement('UPDATE cities SET short_description_temp = short_description');
        DB::statement('UPDATE cities SET long_description_temp = long_description');
        DB::statement('UPDATE cities SET meta_title_temp = meta_title');
        DB::statement('UPDATE cities SET meta_description_temp = meta_description');

        Schema::table('cities', function (Blueprint $table) {
            $table->dropColumn([
                'name',
                'tagline',
                'description',
                'short_description',
                'long_description',
                'meta_title',
                'meta_description'
            ]);
        });

        Schema::table('cities', function (Blueprint $table) {
            $table->string('name')->after('id');
            $table->string('tagline')->nullable();
            $table->string('description')->nullable();
            $table->text('short_description')->nullable();
            $table->text('long_description')->nullable();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
        });

        $cities = DB::table('cities')->get();
        foreach ($cities as $city) {
            $update = [];

            if ($city->name_temp) {
                $data = json_decode($city->name_temp, true);
                $update['name'] = $data['ru'] ?? '';
            }
            if ($city->tagline_temp) {
                $data = json_decode($city->tagline_temp, true);
                $update['tagline'] = $data['ru'] ?? null;
            }
            if ($city->description_temp) {
                $data = json_decode($city->description_temp, true);
                $update['description'] = $data['ru'] ?? null;
            }
            if ($city->short_description_temp) {
                $data = json_decode($city->short_description_temp, true);
                $update['short_description'] = $data['ru'] ?? null;
            }
            if ($city->long_description_temp) {
                $data = json_decode($city->long_description_temp, true);
                $update['long_description'] = $data['ru'] ?? null;
            }
            if ($city->meta_title_temp) {
                $data = json_decode($city->meta_title_temp, true);
                $update['meta_title'] = $data['ru'] ?? null;
            }
            if ($city->meta_description_temp) {
                $data = json_decode($city->meta_description_temp, true);
                $update['meta_description'] = $data['ru'] ?? null;
            }

            if (!empty($update)) {
                DB::table('cities')->where('id', $city->id)->update($update);
            }
        }

        Schema::table('cities', function (Blueprint $table) {
            $table->dropColumn([
                'name_temp',
                'tagline_temp',
                'description_temp',
                'short_description_temp',
                'long_description_temp',
                'meta_title_temp',
                'meta_description_temp'
            ]);
        });
    }
};
