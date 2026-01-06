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
        Schema::create('tour_operator_reminders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->onDelete('cascade');
            $table->enum('reminder_type', ['immediate', '7_days', '3_days', '1_day', 'morning_of']);
            $table->date('scheduled_for');
            $table->timestamp('sent_at')->nullable();
            $table->boolean('email_sent')->default(false);
            $table->boolean('telegram_sent')->default(false);
            $table->timestamp('cancelled_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            // Prevent duplicate reminders
            $table->unique(['booking_id', 'reminder_type', 'scheduled_for'], 'unique_booking_reminder');
            
            // Index for efficient queries
            $table->index(['scheduled_for', 'sent_at']);
            $table->index('reminder_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tour_operator_reminders');
    }
};
