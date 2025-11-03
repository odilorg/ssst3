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
        Schema::table('reviews', function (Blueprint $table) {
            $table->integer('spam_score')->default(0)->after('is_approved')
                ->comment('Spam detection score 0-100');
            $table->ipAddress('review_ip')->nullable()->after('spam_score')
                ->comment('IP address of reviewer');
            $table->string('review_user_agent', 255)->nullable()->after('review_ip')
                ->comment('Browser user agent');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropColumn(['spam_score', 'review_ip', 'review_user_agent']);
        });
    }
};
