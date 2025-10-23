<?php

namespace App\Services;

use App\Models\EmailLog;
use App\Models\EmailTemplate;
use App\Models\Lead;
use App\Mail\LeadEmail;
use Illuminate\Support\Facades\Mail;
use Exception;

class EmailService
{
    /**
     * Send an email to a lead using a template.
     *
     * @param Lead $lead
     * @param EmailTemplate $template
     * @param array $customVariables Additional variables to merge with lead data
     * @return EmailLog
     */
    public function sendToLead(Lead $lead, EmailTemplate $template, array $customVariables = []): EmailLog
    {
        // Prepare variables for template rendering
        $variables = $this->prepareVariables($lead, $customVariables);

        // Render the template
        $rendered = $template->render($variables);

        // Create email log entry
        $emailLog = EmailLog::create([
            'lead_id' => $lead->id,
            'email_template_id' => $template->id,
            'sent_by' => auth()->id(),
            'recipient_email' => $lead->email,
            'recipient_name' => $lead->contact_name,
            'subject' => $rendered['subject'],
            'body' => $rendered['body'],
            'status' => 'pending',
        ]);

        try {
            // Send the email
            Mail::to($lead->email, $lead->contact_name)
                ->send(new LeadEmail($rendered['subject'], $rendered['body']));

            // Mark as sent
            $emailLog->markAsSent();

            // Increment template usage counter
            $template->incrementUsage();

            // Update lead's last_contacted_at timestamp
            $lead->update(['last_contacted_at' => now()]);

        } catch (Exception $e) {
            // Mark as failed and log the error
            $emailLog->markAsFailed($e->getMessage());

            // Re-throw the exception so calling code can handle it
            throw $e;
        }

        return $emailLog;
    }

    /**
     * Prepare variables for template rendering from lead data.
     *
     * @param Lead $lead
     * @param array $customVariables
     * @return array
     */
    protected function prepareVariables(Lead $lead, array $customVariables = []): array
    {
        // Default variables from lead
        $variables = [
            'company_name' => $lead->company_name,
            'contact_name' => $lead->contact_name ?? 'there',
            'country' => $lead->country ?? '',
            'website' => $lead->website ?? '',
            'sender_name' => auth()->user()->name ?? config('app.name'),
            'sender_email' => auth()->user()->email ?? config('mail.from.address'),
            'sender_company' => config('app.name'),
        ];

        // Merge custom variables (custom variables take precedence)
        return array_merge($variables, $customVariables);
    }

    /**
     * Send bulk emails to multiple leads using the same template.
     *
     * @param array $leadIds
     * @param EmailTemplate $template
     * @param array $customVariables
     * @return array Array of EmailLog instances
     */
    public function sendBulk(array $leadIds, EmailTemplate $template, array $customVariables = []): array
    {
        $emailLogs = [];

        foreach ($leadIds as $leadId) {
            $lead = Lead::find($leadId);

            if (!$lead || !$lead->email) {
                continue;
            }

            try {
                $emailLogs[] = $this->sendToLead($lead, $template, $customVariables);
            } catch (Exception $e) {
                // Log error but continue with other emails
                logger()->error("Failed to send email to lead {$leadId}: " . $e->getMessage());
            }
        }

        return $emailLogs;
    }

    /**
     * Get email sending statistics.
     *
     * @return array
     */
    public function getStatistics(): array
    {
        return [
            'total_sent' => EmailLog::sent()->count(),
            'total_failed' => EmailLog::failed()->count(),
            'total_pending' => EmailLog::pending()->count(),
            'total_bounced' => EmailLog::bounced()->count(),
            'total_delivered' => EmailLog::delivered()->count(),
            'sent_today' => EmailLog::sent()->whereDate('sent_at', today())->count(),
            'sent_this_week' => EmailLog::sent()->whereBetween('sent_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'sent_this_month' => EmailLog::sent()->whereMonth('sent_at', now()->month)->count(),
        ];
    }

    /**
     * Preview email without sending.
     *
     * @param Lead $lead
     * @param EmailTemplate $template
     * @param array $customVariables
     * @return array
     */
    public function preview(Lead $lead, EmailTemplate $template, array $customVariables = []): array
    {
        $variables = $this->prepareVariables($lead, $customVariables);
        return $template->render($variables);
    }
}
