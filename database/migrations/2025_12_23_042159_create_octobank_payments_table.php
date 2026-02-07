<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('octobank_payments', function (Blueprint $table) {
            $table->id();
            
            // Reference to booking
            $table->foreignId('booking_id')->constrained()->onDelete('cascade');
            
            // Octobank identifiers
            $table->string('octo_payment_uuid')->nullable()->index();
            $table->string('octo_shop_transaction_id')->unique();
            
            // Payment details
            $table->decimal('amount', 15, 2);
            $table->string('currency', 3)->default('UZS');
            $table->string('description')->nullable();
            
            // Payment status
            $table->enum('status', [
                'created',      // Payment initiated
                'waiting',      // Waiting for user to complete
                'succeeded',    // Payment successful
                'failed',       // Payment failed
                'refunded',     // Fully refunded
                'partial_refund', // Partially refunded
                'cancelled',    // Cancelled by user or timeout
            ])->default('created');
            
            // Octobank response data
            $table->string('octo_payment_url')->nullable();
            $table->string('payment_method')->nullable(); // uzcard, humo, visa, mastercard
            $table->string('masked_pan')->nullable(); // ****1234
            $table->string('card_holder')->nullable();
            
            // Card tokenization for returning customers
            $table->string('card_token')->nullable()->index();
            $table->string('card_recurrent_token')->nullable(); // For recurring payments
            $table->timestamp('card_token_expires_at')->nullable();
            
            // Refund tracking
            $table->decimal('refunded_amount', 15, 2)->default(0);
            $table->string('refund_reason')->nullable();
            
            // Webhook tracking
            $table->timestamp('webhook_received_at')->nullable();
            $table->json('webhook_payload')->nullable();
            
            // Error tracking
            $table->string('error_code')->nullable();
            $table->text('error_message')->nullable();
            
            // Metadata
            $table->json('request_payload')->nullable();
            $table->json('response_payload')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            
            $table->timestamps();
            
            // Indexes for common queries
            $table->index(['status', 'created_at']);
            $table->index('created_at');
        });
        
        // Add payment-related columns to bookings table if not exists
        if (!Schema::hasColumn('bookings', 'payment_status')) {
            Schema::table('bookings', function (Blueprint $table) {
                $table->enum('payment_status', [
                    'pending',
                    'paid',
                    'failed',
                    'refunded',
                    'partial_refund',
                ])->default('pending')->after('status');
                $table->decimal('amount_paid', 15, 2)->default(0)->after('payment_status');
                $table->timestamp('paid_at')->nullable()->after('amount_paid');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('octobank_payments');
        
        if (Schema::hasColumn('bookings', 'payment_status')) {
            Schema::table('bookings', function (Blueprint $table) {
                $table->dropColumn(['payment_status', 'amount_paid', 'paid_at']);
            });
        }
    }
};
