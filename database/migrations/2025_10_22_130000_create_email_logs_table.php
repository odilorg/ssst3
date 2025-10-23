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
        Schema::create('email_logs', function (Blueprint $table) {
            $table->id();

            // Relationships
            $table->foreignId('lead_id')->constrained()->onDelete('cascade');
            $table->foreignId('email_template_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('sent_by')->nullable()->constrained('users')->nullOnDelete();

            // Email Details
            $table->string('recipient_email');
            $table->string('recipient_name')->nullable();
            $table->string('subject');
            $table->text('body');

            // Sending Details
            $table->enum('status', [
                'pending',      // Queued but not sent yet
                'sent',         // Successfully sent
                'failed',       // Failed to send
                'bounced',      // Email bounced back
                'delivered',    // Confirmed delivery (if tracking enabled)
            ])->default('pending');

            $table->timestamp('sent_at')->nullable();
            $table->text('error_message')->nullable();

            // Metadata
            $table->string('message_id')->nullable();
            $table->json('headers')->nullable();

            $table->timestamps();

            // Indexes
            $table->index('lead_id');
            $table->index('status');
            $table->index('sent_at');
            $table->index('sent_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_logs');
    }
};
