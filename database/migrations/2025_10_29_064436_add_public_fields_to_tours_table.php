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
            $table->string('slug')->nullable()->after('id');

            // Pricing
            $table->decimal('price_per_person', 10, 2)->after('duration_days');
            $table->string('currency', 3)->default('USD')->after('price_per_person');

            // Capacity
            $table->tinyInteger('max_guests')->default(10)->after('currency');
            $table->tinyInteger('min_guests')->default(1)->after('max_guests');

            // Images
            $table->string('hero_image', 500)->nullable()->after('long_description');
            $table->json('gallery_images')->nullable()->after('hero_image');

            // Content Arrays (JSON)
            $table->json('highlights')->nullable()->after('gallery_images');
            $table->json('included_items')->nullable()->after('highlights');
            $table->json('excluded_items')->nullable()->after('included_items');
            $table->json('languages')->nullable()->after('excluded_items');
            $table->json('requirements')->nullable()->after('languages');

            // Tour Meta
            $table->enum('tour_type', ['private', 'group', 'day_trip'])->default('private')->after('requirements');
            $table->foreignId('city_id')->nullable()->after('tour_type')->constrained()->onDelete('set null');
            $table->string('duration_text', 100)->nullable()->after('duration_days')->comment('e.g., "4 hours", "Full Day"');

            // Ratings (calculated/cached)
            $table->decimal('rating', 3, 2)->default(0.00)->after('is_active');
            $table->integer('review_count')->default(0)->after('rating');

            // Booking Settings
            $table->integer('min_booking_hours')->default(24)->after('review_count')->comment('Minimum hours before tour start');
            $table->boolean('has_hotel_pickup')->default(true)->after('min_booking_hours');
            $table->tinyInteger('pickup_radius_km')->nullable()->after('has_hotel_pickup');

            // Meeting Point
            $table->text('meeting_point_address')->nullable()->after('pickup_radius_km');
            $table->text('meeting_instructions')->nullable()->after('meeting_point_address');
            $table->decimal('meeting_lat', 10, 8)->nullable()->after('meeting_instructions');
            $table->decimal('meeting_lng', 11, 8)->nullable()->after('meeting_lat');

            // Cancellation Policy
            $table->integer('cancellation_hours')->default(24)->after('meeting_lng');
            $table->text('cancellation_policy')->nullable()->after('cancellation_hours');
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

        // Now add unique constraint
        Schema::table('tours', function (Blueprint $table) {
            $table->string('slug')->nullable(false)->change();
            $table->unique('slug');
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
