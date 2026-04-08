<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('octobank_payments', function (Blueprint $table) {
            // USD amount at the time the link was generated (snapshot — never changes)
            $table->decimal('amount_usd', 10, 2)->nullable()->after('amount');

            // CBU exchange rate used for the USD→UZS conversion
            $table->decimal('fx_rate_used', 12, 4)->nullable()->after('amount_usd');

            // What the payment covers: deposit, balance, or a custom amount with a note
            $table->enum('purpose', ['deposit', 'balance', 'custom'])
                  ->nullable()
                  ->after('description');

            // Filament admin user ID who generated the link (nullable for API/system-generated links)
            $table->unsignedBigInteger('generated_by')->nullable()->after('purpose');

            // When the payment link expires (Octobank default is 30 min; we may set shorter)
            $table->timestamp('expires_at')->nullable()->after('generated_by');

            // Fast lookup: find active (non-terminal) links for a booking
            $table->index(['booking_id', 'status'], 'octobank_booking_status_idx');
        });
    }

    public function down(): void
    {
        Schema::table('octobank_payments', function (Blueprint $table) {
            $table->dropIndex('octobank_booking_status_idx');
            $table->dropColumn(['amount_usd', 'fx_rate_used', 'purpose', 'generated_by', 'expires_at']);
        });
    }
};
