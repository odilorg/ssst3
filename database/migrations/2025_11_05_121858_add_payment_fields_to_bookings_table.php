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
        Schema::table('bookings', function (Blueprint $table) {
            // Only add columns that don't already exist

            // Customer information
            if (!Schema::hasColumn('bookings', 'customer_name')) {
                $table->string('customer_name', 255)->nullable()->comment('Customer full name');
            }

            if (!Schema::hasColumn('bookings', 'customer_email')) {
                $table->string('customer_email', 255)->nullable()->comment('Customer email address');
            }

            if (!Schema::hasColumn('bookings', 'customer_phone')) {
                $table->string('customer_phone', 50)->nullable()->comment('Customer phone number');
            }

            if (!Schema::hasColumn('bookings', 'customer_country')) {
                $table->string('customer_country', 100)->nullable()->comment('Customer country');
            }

            // Financial tracking (add missing ones)
            if (!Schema::hasColumn('bookings', 'amount_remaining')) {
                $table->decimal('amount_remaining', 12, 2)->default(0)->comment('Amount still to be paid');
            }

            if (!Schema::hasColumn('bookings', 'discount_applied')) {
                $table->decimal('discount_applied', 12, 2)->default(0)->comment('Discount amount (e.g., 10% for full payment)');
            }

            if (!Schema::hasColumn('bookings', 'balance_due_date')) {
                $table->date('balance_due_date')->nullable()->comment('Date when remaining balance is due');
            }

            // Special requests and notes
            if (!Schema::hasColumn('bookings', 'special_requests')) {
                $table->text('special_requests')->nullable()->comment('Customer special requests (dietary, accessibility, etc.)');
            }

            if (!Schema::hasColumn('bookings', 'inquiry_notes')) {
                $table->text('inquiry_notes')->nullable()->comment('Notes for request-to-book inquiries');
            }

            // Terms agreement timestamp
            if (!Schema::hasColumn('bookings', 'terms_agreed_at')) {
                $table->timestamp('terms_agreed_at')->nullable()->comment('When customer agreed to terms & conditions');
            }
        });

        // Add indexes only if they don't exist
        $indexes = DB::select("SHOW INDEXES FROM bookings");
        $existingIndexes = array_column($indexes, 'Key_name');

        Schema::table('bookings', function (Blueprint $table) use ($existingIndexes) {
            if (!in_array('idx_booking_type', $existingIndexes)) {
                $table->index('booking_type', 'idx_booking_type');
            }
            if (!in_array('idx_balance_due_date', $existingIndexes)) {
                $table->index('balance_due_date', 'idx_balance_due_date');
            }
            if (!in_array('idx_customer_email', $existingIndexes)) {
                $table->index('customer_email', 'idx_customer_email');
            }
        });

        // Update status enum to include new values
        DB::statement("ALTER TABLE bookings MODIFY COLUMN status ENUM(
            'draft',
            'inquiry',
            'pending_payment',
            'confirmed',
            'in_progress',
            'completed',
            'cancelled',
            'declined'
        ) DEFAULT 'draft'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Drop new columns if they exist
            $columns = ['customer_name', 'customer_email', 'customer_phone', 'customer_country',
                       'amount_remaining', 'discount_applied', 'balance_due_date',
                       'special_requests', 'inquiry_notes', 'terms_agreed_at'];

            foreach ($columns as $column) {
                if (Schema::hasColumn('bookings', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        // Revert status enum
        DB::statement("ALTER TABLE bookings MODIFY COLUMN status ENUM(
            'draft',
            'pending',
            'confirmed',
            'in_progress',
            'completed',
            'cancelled'
        ) DEFAULT 'draft'");
    }
};
