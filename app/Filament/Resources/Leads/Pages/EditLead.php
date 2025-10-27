<?php

namespace App\Filament\Resources\Leads\Pages;

use App\Filament\Resources\Leads\LeadResource;
use App\Filament\Resources\Leads\Widgets\LeadAICopilotWidget;
use App\Services\AIEmailService;
use App\Services\EmailService;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditLead extends EditRecord
{
    protected static string $resource = LeadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('generateEmail')
                ->label('Generate Email with AI')
                ->icon('heroicon-o-sparkles')
                ->color('primary')
                ->form([
                    Select::make('ai_provider')
                        ->label('AI Provider')
                        ->options([
                            'deepseek' => '⚡ DeepSeek (~$0.001/email) - Fast & Cheap',
                            'openai-mini' => '🤖 ChatGPT-4o-mini (~$0.01/email) - Balanced',
                            'openai' => '🚀 ChatGPT-4o (~$0.05/email) - Best Quality',
                        ])
                        ->default('deepseek')
                        ->required()
                        ->helperText('Choose AI model based on priority and budget'),

                    Select::make('tone')
                        ->label('Email Tone')
                        ->options([
                            'professional' => 'Professional',
                            'friendly' => 'Friendly',
                            'persuasive' => 'Persuasive',
                            'consultative' => 'Consultative',
                        ])
                        ->default('professional')
                        ->required()
                        ->helperText('Choose the tone for your email'),

                    Select::make('email_type')
                        ->label('Email Type')
                        ->options([
                            'initial' => 'Initial Outreach',
                            'followup' => 'Follow-up',
                            'proposal' => 'Partnership Proposal',
                            'reengagement' => 'Re-engagement',
                        ])
                        ->default('initial')
                        ->required()
                        ->helperText('Type of email to generate'),

                    Textarea::make('custom_instructions')
                        ->label('Custom Instructions (Optional)')
                        ->rows(3)
                        ->placeholder('Any specific points to include or emphasize?')
                        ->helperText('Add custom context or requirements for the AI'),
                ])
                ->action(function (array $data) {
                    $aiService = new AIEmailService($data['ai_provider'] ?? 'deepseek');

                    $result = $aiService->generateEmail(
                        $this->record,
                        $data['email_type'] ?? 'initial',
                        $data['tone'] ?? 'professional',
                        true // researchWebsite
                    );

                    if ($result['success']) {
                        // Update the lead with generated email
                        $this->record->update([
                            'email_draft_subject' => $result['subject'],
                            'email_draft_body' => $result['body'],
                            'ai_email_metadata' => $result['metadata'],
                            'email_draft_notes' => isset($data['custom_instructions'])
                                ? "Custom: {$data['custom_instructions']}"
                                : "AI Generated with {$data['tone']} tone",
                        ]);

                        Notification::make()
                            ->title('Email Generated Successfully')
                            ->success()
                            ->body('AI has generated a personalized email. Review and edit in the form below.')
                            ->send();

                        // Refresh the form to show new data
                        $this->refreshFormData([
                            'email_draft_subject',
                            'email_draft_body',
                            'email_draft_notes',
                            'ai_email_metadata',
                        ]);
                    } else {
                        Notification::make()
                            ->title('Email Generation Failed')
                            ->danger()
                            ->body($result['error'] ?? 'An error occurred while generating the email.')
                            ->send();
                    }
                })
                ->modalHeading('Generate AI Email')
                ->modalDescription('Let AI create a personalized email for this lead based on their details.')
                ->modalSubmitActionLabel('Generate Email')
                ->modalWidth('lg'),

            Action::make('sendEmail')
                ->label('Send This Email Now')
                ->icon('heroicon-o-paper-airplane')
                ->color('success')
                ->visible(fn () => $this->record->email_draft_subject && $this->record->email_draft_body && $this->record->email)
                ->form([
                    Placeholder::make('preview')
                        ->label('Email Preview')
                        ->content(function () {
                            $subject = $this->record->email_draft_subject;
                            $body = $this->record->email_draft_body;

                            return new \Illuminate\Support\HtmlString(
                                '<div class="border rounded-lg p-4 bg-white dark:bg-gray-800">' .
                                '<div class="mb-3"><strong class="text-lg">To:</strong> ' . e($this->record->email) . ' (' . e($this->record->company_name) . ')</div>' .
                                '<div class="mb-3"><strong class="text-lg">Subject:</strong> ' . e($subject) . '</div>' .
                                '<div class="border-t pt-3 prose dark:prose-invert max-w-none">' .
                                '<strong>Body:</strong><br>' . $body .
                                '</div>' .
                                '</div>'
                            );
                        }),
                ])
                ->action(function () {
                    try {
                        $emailService = app(EmailService::class);

                        // Send AI-generated email
                        $emailLog = $emailService->sendAIGeneratedEmail($this->record);

                        // Update lead tracking fields
                        $this->record->update([
                            'total_emails_sent' => $this->record->total_emails_sent + 1,
                            'last_email_sent_at' => now(),
                            'status' => $this->record->status === 'new' ? 'contacted' : $this->record->status,
                        ]);

                        Notification::make()
                            ->title('Email Sent Successfully!')
                            ->success()
                            ->body("Email sent to {$this->record->email}")
                            ->send();

                        // Refresh the record
                        $this->record->refresh();
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Failed to Send Email')
                            ->danger()
                            ->body($e->getMessage())
                            ->send();
                    }
                })
                ->modalHeading('Send Email to ' . ($this->record->company_name ?? 'Lead'))
                ->modalDescription('Review the email before sending. This will be sent via Zoho SMTP.')
                ->modalSubmitActionLabel('Send Email Now')
                ->requiresConfirmation()
                ->modalWidth('lg'),

            DeleteAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            // LeadAICopilotWidget::class, // Disabled - using new "Generate Email with AI" button instead
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
