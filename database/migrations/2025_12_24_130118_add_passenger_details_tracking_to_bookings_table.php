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
        Schema::table('bookings', function (Blueprint $table) {
            $table->timestamp('passenger_details_submitted_at')->nullable()->after('payment_status');
            $table->string('passenger_details_url_token', 64)->unique()->nullable()->after('passenger_details_submitted_at');
            $table->timestamp('last_reminder_sent_at')->nullable()->after('passenger_details_url_token');
            $table->integer('reminder_count')->default(0)->after('last_reminder_sent_at');

            // Add index for querying bookings needing reminders
            $table->index(['start_date', 'passenger_details_submitted_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropIndex(['start_date', 'passenger_details_submitted_at']);
            $table->dropColumn([
                'passenger_details_submitted_at',
                'passenger_details_url_token',
                'last_reminder_sent_at',
                'reminder_count'
            ]);
        });
    }
};
