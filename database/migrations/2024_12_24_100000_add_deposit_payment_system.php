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
        // 1. Update bookings table for deposit system
        Schema::table('bookings', function (Blueprint $table) {
            // Only add columns that don't exist yet
            if (!Schema::hasColumn('bookings', 'payment_type')) {
                $table->enum('payment_type', ['deposit', 'full', 'flexible'])->default('deposit')->after('total_price');
            }
            if (!Schema::hasColumn('bookings', 'deposit_percentage')) {
                $table->decimal('deposit_percentage', 5, 2)->default(30.00)->after('total_price');
            }
            if (!Schema::hasColumn('bookings', 'balance_paid_at')) {
                $table->timestamp('balance_paid_at')->nullable()->after('balance_due_date');
            }
            if (!Schema::hasColumn('bookings', 'discount_amount')) {
                $table->decimal('discount_amount', 10, 2)->default(0.00)->after('balance_due_date');
            }
            if (!Schema::hasColumn('bookings', 'discount_reason')) {
                $table->string('discount_reason')->nullable()->after('discount_amount');
            }
        });

        // 2. Create payment_transactions table
        if (!Schema::hasTable('payment_transactions')) {
            Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->onDelete('cascade');
            $table->enum('transaction_type', ['deposit', 'balance', 'full', 'refund', 'partial_refund']);
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('USD');
            $table->string('payment_method', 50)->nullable();
            $table->string('payment_gateway', 50)->default('octobank');
            $table->string('gateway_transaction_id')->nullable();
            $table->json('gateway_response')->nullable();
            $table->enum('status', ['pending', 'processing', 'success', 'failed', 'refunded'])->default('pending');
            $table->timestamp('processed_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            // Indexes for performance
            $table->index('booking_id');
            $table->index('status');
            $table->index('gateway_transaction_id');
            $table->index('transaction_type');
            });
        }

        // 3. Create payment_reminders table
        if (!Schema::hasTable('payment_reminders')) {
            Schema::create('payment_reminders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->onDelete('cascade');
            $table->enum('reminder_type', ['balance_45_days', 'balance_35_days', 'balance_30_days', 'balance_overdue']);
            $table->date('scheduled_date');
            $table->timestamp('sent_at')->nullable();
            $table->boolean('email_sent')->default(false);
            $table->boolean('sms_sent')->default(false);
            $table->text('response')->nullable();
            $table->timestamps();

            // Indexes and constraints
            $table->index(['scheduled_date', 'sent_at']);
            $table->unique(['booking_id', 'reminder_type']);
            });
        }

        // 4. Create cancellation_requests table
        if (!Schema::hasTable('cancellation_requests')) {
            Schema::create('cancellation_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->onDelete('cascade');
            $table->text('reason')->nullable();
            $table->timestamp('requested_at')->useCurrent();
            $table->integer('days_before_tour')->nullable();
            $table->decimal('original_amount', 10, 2)->nullable();
            $table->decimal('refund_percentage', 5, 2)->nullable();
            $table->decimal('refund_amount', 10, 2)->nullable();
            $table->decimal('processing_fee', 10, 2)->nullable();
            $table->enum('status', ['pending', 'approved', 'processing', 'refunded', 'rejected'])->default('pending');
            $table->foreignId('processed_by')->nullable()->constrained('users');
            $table->timestamp('processed_at')->nullable();
            $table->unsignedBigInteger('refund_transaction_id')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('status');
            $table->index('booking_id');
            $table->foreign('refund_transaction_id')->references('id')->on('payment_transactions');
            });
        }

        // 5. Create payment_schedules table for flexible payments
        if (!Schema::hasTable('payment_schedules')) {
            Schema::create('payment_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->onDelete('cascade');
            $table->integer('payment_number');
            $table->decimal('amount', 10, 2);
            $table->decimal('percentage', 5, 2)->nullable();
            $table->date('due_date');
            $table->timestamp('paid_at')->nullable();
            $table->unsignedBigInteger('transaction_id')->nullable();
            $table->enum('status', ['scheduled', 'pending', 'paid', 'overdue', 'cancelled'])->default('scheduled');
            $table->timestamps();

            // Indexes and constraints
            $table->index(['due_date', 'status']);
            $table->unique(['booking_id', 'payment_number']);
            $table->foreign('transaction_id')->references('id')->on('payment_transactions');
            });
        }

        // 6. Create payment_settings table for configuration
        if (!Schema::hasTable('payment_settings')) {
            Schema::create('payment_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('value');
            $table->string('description')->nullable();
            $table->timestamps();
            });

            // Insert default payment settings
            DB::table('payment_settings')->insert([
            [
                'key' => 'default_deposit_percentage',
                'value' => '30',
                'description' => 'Default deposit percentage for bookings',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'full_payment_discount_percentage',
                'value' => '3',
                'description' => 'Discount percentage for full payment',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'balance_due_days_before_tour',
                'value' => '30',
                'description' => 'Days before tour when balance is due',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'enable_flexible_payment',
                'value' => 'true',
                'description' => 'Enable flexible payment plans for tours over threshold',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'flexible_payment_threshold',
                'value' => '2000',
                'description' => 'Minimum tour price for flexible payment option',
                'created_at' => now(),
                'updated_at' => now()
            ]
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop tables in reverse order due to foreign key constraints
        Schema::dropIfExists('payment_settings');
        Schema::dropIfExists('payment_schedules');
        Schema::dropIfExists('cancellation_requests');
        Schema::dropIfExists('payment_reminders');
        Schema::dropIfExists('payment_transactions');

        // Remove columns from bookings table
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn([
                'payment_type',
                'deposit_percentage',
                'deposit_amount',
                'deposit_paid_at',
                'balance_amount',
                'balance_due_date',
                'balance_paid_at',
                'discount_amount',
                'discount_reason'
            ]);
        });
    }
};