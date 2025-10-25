<?php

namespace App\Filament\Resources\Leads\Widgets;

use App\Models\Lead;
use App\Models\LeadAIAction;
use App\Models\LeadAIConversation;
use App\Services\LeadAIService;
use Filament\Widgets\Widget;
use Livewire\Attributes\On;

class LeadAICopilotWidget extends Widget
{
    protected string $view = 'filament.resources.leads.widgets.lead-a-i-copilot-widget';

    public ?Lead $record = null;

    public string $message = '';
    public array $messages = [];
    public bool $isLoading = false;
    public float $totalCost = 0;
    public bool $showEmailModal = false;
    public string $emailPurpose = 'initial_outreach';
    public string $emailTone = 'professional';

    protected int | string | array $columnSpan = 'full';

    public function mount(): void
    {
        if ($this->record) {
            $this->loadConversation();
            $this->totalCost = LeadAIAction::getTotalCost($this->record->id);
        }
    }

    public function loadConversation()
    {
        $this->messages = LeadAIConversation::where('lead_id', $this->record->id)
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(fn($msg) => [
                'role' => $msg->role,
                'content' => $msg->message,
                'timestamp' => $msg->created_at->format('H:i'),
            ])
            ->toArray();
    }

    public function sendMessage()
    {
        if (empty(trim($this->message))) {
            return;
        }

        $userMessage = $this->message;
        $this->message = '';
        $this->isLoading = true;

        $this->messages[] = [
            'role' => 'user',
            'content' => $userMessage,
            'timestamp' => now()->format('H:i'),
        ];

        $aiService = app(LeadAIService::class);
        $result = $aiService->chat($this->record, $userMessage, auth()->id());

        $this->messages[] = [
            'role' => 'assistant',
            'content' => $result['message'],
            'timestamp' => now()->format('H:i'),
        ];

        $this->totalCost += $result['cost'] ?? 0;
        $this->isLoading = false;

        $this->dispatch('lead-updated');
    }

    public function enrichLead()
    {
        $this->isLoading = true;

        $aiService = app(LeadAIService::class);
        $result = $aiService->enrichLead($this->record, auth()->id());

        if ($result['success']) {
            $fieldsUpdated = $result['fields_updated'];
            if (count($fieldsUpdated) > 0) {
                $this->messages[] = [
                    'role' => 'assistant',
                    'content' => "✅ Lead enriched! Updated fields: " . implode(', ', $fieldsUpdated),
                    'timestamp' => now()->format('H:i'),
                ];
            } else {
                $this->messages[] = [
                    'role' => 'assistant',
                    'content' => "ℹ️ No new information found to enrich this lead.",
                    'timestamp' => now()->format('H:i'),
                ];
            }

            $this->totalCost += $result['cost'] ?? 0;
            $this->dispatch('lead-updated');
            $this->record->refresh();
        } else {
            $this->messages[] = [
                'role' => 'assistant',
                'content' => "❌ Enrichment failed: " . $result['error'],
                'timestamp' => now()->format('H:i'),
            ];
        }

        $this->isLoading = false;
    }

    public function generateEmail()
    {
        $this->isLoading = true;

        $aiService = app(LeadAIService::class);
        $result = $aiService->generateEmail(
            $this->record,
            $this->emailPurpose,
            $this->emailTone,
            auth()->id()
        );

        if ($result['success']) {
            $email = $result['email'];
            $emailText = "📧 **Generated Email**\n\n";
            $emailText .= "**Subject:** {$email['subject']}\n\n";
            $emailText .= "**Body:**\n{$email['body']}\n\n";
            $emailText .= "**CTA:** {$email['cta']}";

            $this->messages[] = [
                'role' => 'assistant',
                'content' => $emailText,
                'timestamp' => now()->format('H:i'),
            ];

            $this->totalCost += $result['cost'] ?? 0;
        } else {
            $this->messages[] = [
                'role' => 'assistant',
                'content' => "❌ Email generation failed: " . $result['error'],
                'timestamp' => now()->format('H:i'),
            ];
        }

        $this->showEmailModal = false;
        $this->isLoading = false;
    }

    public function suggestFollowup()
    {
        $this->isLoading = true;

        $aiService = app(LeadAIService::class);
        $result = $aiService->suggestFollowup($this->record, auth()->id());

        if ($result['success']) {
            $followup = $result['followup'];
            $followupText = "📅 **Follow-up Suggestion**\n\n";
            $followupText .= "**Action:** {$followup['action']}\n";
            $followupText .= "**Timing:** {$followup['timing']}\n";
            $followupText .= "**Talking Points:**\n";
            foreach ($followup['talking_points'] as $point) {
                $followupText .= "- {$point}\n";
            }
            $followupText .= "\n**Rationale:** {$followup['rationale']}";

            $this->messages[] = [
                'role' => 'assistant',
                'content' => $followupText,
                'timestamp' => now()->format('H:i'),
            ];

            $this->totalCost += $result['cost'] ?? 0;
        } else {
            $this->messages[] = [
                'role' => 'assistant',
                'content' => "❌ Follow-up suggestion failed: " . $result['error'],
                'timestamp' => now()->format('H:i'),
            ];
        }

        $this->isLoading = false;
    }

    public function getColumnSpan(): string | array | int
    {
        return $this->columnSpan;
    }
}
