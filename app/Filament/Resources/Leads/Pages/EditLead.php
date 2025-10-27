<?php

namespace App\Filament\Resources\Leads\Pages;

use App\Filament\Resources\Leads\LeadResource;
use App\Filament\Resources\Leads\Widgets\LeadAICopilotWidget;
use App\Services\AIEmailService;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
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
                    $aiService = app(AIEmailService::class);

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
