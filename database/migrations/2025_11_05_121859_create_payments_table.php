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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            // Foreign key to booking
            $table->foreignId('booking_id')
                ->constrained('bookings')
                ->onDelete('cascade')
                ->comment('Booking this payment belongs to');

            // Amount (negative for refunds)
            $table->decimal('amount', 12, 2)
                ->comment('Payment amount (negative for refunds)');

            // Payment method
            $table->string('payment_method', 100)
                ->comment('Payment method (e.g., octo_uzcard, octo_visa, bank_transfer)');

            // Status
            $table->enum('status', ['pending', 'completed', 'failed', 'refunded'])
                ->default('pending')
                ->comment('Payment transaction status');

            // Payment type
            $table->enum('payment_type', ['deposit', 'balance', 'full', 'refund'])
                ->nullable()
                ->comment('Type of payment');

            // Gateway transaction details
            $table->string('transaction_id', 255)
                ->nullable()
                ->comment('Payment gateway transaction ID (e.g., OCTO payment UUID)');

            $table->json('gateway_response')
                ->nullable()
                ->comment('Full gateway response (webhook payload)');

            // Processing timestamp
            $table->timestamp('processed_at')
                ->nullable()
                ->comment('When payment was processed');

            $table->timestamps();

            // Indexes for queries
            $table->index(['booking_id', 'status'], 'idx_booking_status');
            $table->index('transaction_id', 'idx_transaction_id');
            $table->index('payment_type', 'idx_payment_type');
            $table->index('processed_at', 'idx_processed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
