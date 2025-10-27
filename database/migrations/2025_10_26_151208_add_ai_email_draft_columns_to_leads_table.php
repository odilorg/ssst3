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
        Schema::table('leads', function (Blueprint $table) {
            // Email template selection
            $table->foreignId('selected_email_template_id')->nullable()->after('notes')
                ->constrained('email_templates')->nullOnDelete()
                ->comment('Which email template to use');

            // AI-generated/customized email draft
            $table->string('email_draft_subject')->nullable()->after('selected_email_template_id')
                ->comment('Customized subject line (can be AI-generated)');

            $table->text('email_draft_body')->nullable()->after('email_draft_subject')
                ->comment('Customized email body (can be AI-generated or manually edited)');

            $table->text('email_draft_notes')->nullable()->after('email_draft_body')
                ->comment('Strategy notes: why this approach, key points to emphasize');

            // AI generation metadata
            $table->json('ai_email_metadata')->nullable()->after('email_draft_notes')
                ->comment('Store AI insights, website research, generation parameters');

            // Email priority and scheduling
            $table->enum('email_priority', ['high', 'medium', 'low'])->default('medium')->after('ai_email_metadata')
                ->comment('Priority level for this outreach');

            $table->string('best_contact_time')->nullable()->after('email_priority')
                ->comment('Best time to contact (e.g., Morning EST, Avoid Mondays)');

            // Email tracking (quick reference)
            $table->timestamp('last_email_sent_at')->nullable()->after('best_contact_time')
                ->comment('Last time an email was sent to this lead');

            $table->enum('email_response_status', [
                'no_response',
                'replied',
                'interested',
                'not_interested',
                'auto_reply',
                'bounced'
            ])->default('no_response')->after('last_email_sent_at')
                ->comment('Email response tracking');

            $table->integer('total_emails_sent')->default(0)->after('email_response_status')
                ->comment('Total number of emails sent to this lead');

            // Indexes for performance
            $table->index('email_priority');
            $table->index('email_response_status');
            $table->index('last_email_sent_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropForeign(['selected_email_template_id']);
            $table->dropIndex(['email_priority']);
            $table->dropIndex(['email_response_status']);
            $table->dropIndex(['last_email_sent_at']);

            $table->dropColumn([
                'selected_email_template_id',
                'email_draft_subject',
                'email_draft_body',
                'email_draft_notes',
                'ai_email_metadata',
                'email_priority',
                'best_contact_time',
                'last_email_sent_at',
                'email_response_status',
                'total_emails_sent',
            ]);
        });
    }
};
