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
            // Only add columns that don't exist
            if (!Schema::hasColumn('tours', 'tour_type')) {
                $table->enum('tour_type', ['group_only', 'private_only', 'hybrid'])
                    ->default('group_only')
                    ->after('is_active')
                    ->comment('Type of tour offering');
            }

            if (!Schema::hasColumn('tours', 'group_price_per_person')) {
                $table->decimal('group_price_per_person', 10, 2)
                    ->nullable()
                    ->comment('Price per person for group bookings');
            }

            if (!Schema::hasColumn('tours', 'private_price_per_person')) {
                $table->decimal('private_price_per_person', 10, 2)
                    ->nullable()
                    ->comment('Price per person for private bookings');
            }

            if (!Schema::hasColumn('tours', 'private_minimum_charge')) {
                $table->decimal('private_minimum_charge', 10, 2)
                    ->nullable()
                    ->comment('Minimum total charge for private bookings');
            }

            if (!Schema::hasColumn('tours', 'booking_window_hours')) {
                $table->unsignedInteger('booking_window_hours')
                    ->default(72)
                    ->comment('Hours in advance required to book');
            }

            if (!Schema::hasColumn('tours', 'balance_due_days')) {
                $table->unsignedInteger('balance_due_days')
                    ->default(3)
                    ->comment('Days before tour that balance payment is due');
            }

            if (!Schema::hasColumn('tours', 'allow_last_minute_full_payment')) {
                $table->boolean('allow_last_minute_full_payment')
                    ->default(true)
                    ->comment('Allow bookings within window if full payment made');
            }

            if (!Schema::hasColumn('tours', 'requires_traveler_details')) {
                $table->boolean('requires_traveler_details')
                    ->default(false)
                    ->comment('Requires individual traveler info (for tickets, visas, etc.)');
            }

            // cancellation_policy already exists, skip it
        });

        // Add index only if it doesn't exist
        if (!DB::select("SHOW INDEXES FROM tours WHERE Key_name = 'idx_tour_type'")) {
            Schema::table('tours', function (Blueprint $table) {
                $table->index('tour_type', 'idx_tour_type');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tours', function (Blueprint $table) {
            $table->dropIndex('idx_tour_type');
            $table->dropColumn([
                'tour_type',
                'group_price_per_person',
                'private_price_per_person',
                'private_minimum_charge',
                'booking_window_hours',
                'balance_due_days',
                'allow_last_minute_full_payment',
                'requires_traveler_details',
                'cancellation_policy',
            ]);
        });
    }
};
