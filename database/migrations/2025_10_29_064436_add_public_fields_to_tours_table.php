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
        Schema::table('tours', function (Blueprint $table) {
            // Identification - Add slug without unique constraint first
            if (!Schema::hasColumn('tours', 'slug')) {
                $table->string('slug')->nullable()->after('id');
            }

            // Pricing
            if (!Schema::hasColumn('tours', 'price_per_person')) {
                $table->decimal('price_per_person', 10, 2)->nullable()->after('duration_days');
            }
            if (!Schema::hasColumn('tours', 'currency')) {
                $table->string('currency', 3)->default('USD')->after('price_per_person');
            }

            // Capacity
            if (!Schema::hasColumn('tours', 'max_guests')) {
                $table->tinyInteger('max_guests')->default(10)->after('currency');
            }
            if (!Schema::hasColumn('tours', 'min_guests')) {
                $table->tinyInteger('min_guests')->default(1)->after('max_guests');
            }

            // Images - Handle old 'image' column
            if (Schema::hasColumn('tours', 'image') && !Schema::hasColumn('tours', 'hero_image')) {
                $table->renameColumn('image', 'hero_image');
            } else if (!Schema::hasColumn('tours', 'hero_image')) {
                $table->string('hero_image', 500)->nullable()->after('long_description');
            }

            if (!Schema::hasColumn('tours', 'gallery_images')) {
                $table->json('gallery_images')->nullable()->after('hero_image');
            }

            // Content Arrays (JSON)
            if (!Schema::hasColumn('tours', 'highlights')) {
                $table->json('highlights')->nullable()->after('gallery_images');
            }
            if (!Schema::hasColumn('tours', 'included_items')) {
                $table->json('included_items')->nullable()->after('highlights');
            }
            if (!Schema::hasColumn('tours', 'excluded_items')) {
                $table->json('excluded_items')->nullable()->after('included_items');
            }
            if (!Schema::hasColumn('tours', 'languages')) {
                $table->json('languages')->nullable()->after('excluded_items');
            }
            if (!Schema::hasColumn('tours', 'requirements')) {
                $table->json('requirements')->nullable()->after('languages');
            }

            // Tour Meta
            if (!Schema::hasColumn('tours', 'tour_type')) {
                $table->enum('tour_type', ['private', 'group', 'day_trip'])->default('private')->after('requirements');
            }
            if (!Schema::hasColumn('tours', 'city_id')) {
                $table->foreignId('city_id')->nullable()->after('tour_type')->constrained()->onDelete('set null');
            }
            if (!Schema::hasColumn('tours', 'duration_text')) {
                $table->string('duration_text', 100)->nullable()->after('duration_days')->comment('e.g., "4 hours", "Full Day"');
            }

            // Ratings (calculated/cached)
            if (!Schema::hasColumn('tours', 'rating')) {
                $table->decimal('rating', 3, 2)->default(0.00)->after('is_active');
            }
            if (!Schema::hasColumn('tours', 'review_count')) {
                $table->integer('review_count')->default(0)->after('rating');
            }

            // Booking Settings
            if (!Schema::hasColumn('tours', 'min_booking_hours')) {
                $table->integer('min_booking_hours')->default(24)->after('review_count')->comment('Minimum hours before tour start');
            }
            if (!Schema::hasColumn('tours', 'has_hotel_pickup')) {
                $table->boolean('has_hotel_pickup')->default(true)->after('min_booking_hours');
            }
            if (!Schema::hasColumn('tours', 'pickup_radius_km')) {
                $table->tinyInteger('pickup_radius_km')->nullable()->after('has_hotel_pickup');
            }

            // Meeting Point
            if (!Schema::hasColumn('tours', 'meeting_point_address')) {
                $table->text('meeting_point_address')->nullable()->after('pickup_radius_km');
            }
            if (!Schema::hasColumn('tours', 'meeting_instructions')) {
                $table->text('meeting_instructions')->nullable()->after('meeting_point_address');
            }
            if (!Schema::hasColumn('tours', 'meeting_lat')) {
                $table->decimal('meeting_lat', 10, 8)->nullable()->after('meeting_instructions');
            }
            if (!Schema::hasColumn('tours', 'meeting_lng')) {
                $table->decimal('meeting_lng', 11, 8)->nullable()->after('meeting_lat');
            }

            // Cancellation Policy
            if (!Schema::hasColumn('tours', 'cancellation_hours')) {
                $table->integer('cancellation_hours')->default(24)->after('meeting_lng');
            }
            if (!Schema::hasColumn('tours', 'cancellation_policy')) {
                $table->text('cancellation_policy')->nullable()->after('cancellation_hours');
            }
        });

        // Generate slugs for existing tours
        $tours = DB::table('tours')->whereNull('slug')->orWhere('slug', '')->get();
        foreach ($tours as $tour) {
            $slug = \Illuminate\Support\Str::slug($tour->title);
            // Handle duplicate slugs by appending ID
            $existingSlug = DB::table('tours')->where('slug', $slug)->where('id', '!=', $tour->id)->first();
            if ($existingSlug) {
                $slug = $slug . '-' . $tour->id;
            }
            DB::table('tours')->where('id', $tour->id)->update(['slug' => $slug]);
        }

        // Now add unique constraint if not exists
        Schema::table('tours', function (Blueprint $table) {
            if (Schema::hasColumn('tours', 'slug')) {
                $table->string('slug')->nullable(false)->change();

                // Check if unique constraint doesn't already exist
                $sm = Schema::getConnection()->getDoctrineSchemaManager();
                $indexesFound = $sm->listTableIndexes('tours');
                $hasUniqueSlug = false;
                foreach ($indexesFound as $index) {
                    if ($index->isUnique() && in_array('slug', $index->getColumns())) {
                        $hasUniqueSlug = true;
                        break;
                    }
                }

                if (!$hasUniqueSlug) {
                    $table->unique('slug');
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tours', function (Blueprint $table) {
            $table->dropForeign(['city_id']);

            $table->dropColumn([
                'slug',
                'price_per_person',
                'currency',
                'max_guests',
                'min_guests',
                'hero_image',
                'gallery_images',
                'highlights',
                'included_items',
                'excluded_items',
                'languages',
                'requirements',
                'tour_type',
                'city_id',
                'duration_text',
                'rating',
                'review_count',
                'min_booking_hours',
                'has_hotel_pickup',
                'pickup_radius_km',
                'meeting_point_address',
                'meeting_instructions',
                'meeting_lat',
                'meeting_lng',
                'cancellation_hours',
                'cancellation_policy',
            ]);
        });
    }
};
