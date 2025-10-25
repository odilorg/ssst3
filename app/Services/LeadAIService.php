<?php

namespace App\Services;

use App\Models\Lead;
use App\Models\LeadAIAction;
use App\Models\LeadAIConversation;
use App\Models\LeadEnrichment;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LeadAIService
{
    /**
     * Main chat interface - handles conversation with context
     */
    public function chat(Lead $lead, string $userMessage, int $userId): array
    {
        try {
            // Log user message
            LeadAIConversation::create([
                'lead_id' => $lead->id,
                'user_id' => $userId,
                'role' => 'user',
                'message' => $userMessage,
            ]);

            // Build context-aware prompt
            $systemPrompt = $this->buildCopilotSystemPrompt($lead);
            $conversationHistory = LeadAIConversation::getHistory($lead->id, 10);

            // Call DeepSeek API
            $response = $this->callDeepSeek([
                ['role' => 'system', 'content' => $systemPrompt],
                ...$conversationHistory,
                ['role' => 'user', 'content' => $userMessage],
            ], 0.7, 1500);

            $aiMessage = $response['choices'][0]['message']['content'];
            $tokensUsed = $response['usage']['total_tokens'];
            $cost = $this->calculateCost($response['usage']);

            // Log AI response
            LeadAIConversation::create([
                'lead_id' => $lead->id,
                'user_id' => $userId,
                'role' => 'assistant',
                'message' => $aiMessage,
                'metadata' => [
                    'tokens' => $tokensUsed,
                    'cost' => $cost,
                ],
            ]);

            // Track as action
            LeadAIAction::create([
                'lead_id' => $lead->id,
                'user_id' => $userId,
                'action_type' => 'chat',
                'input_data' => ['message' => $userMessage],
                'output_data' => ['response' => $aiMessage],
                'status' => 'completed',
                'tokens_used' => $tokensUsed,
                'cost' => $cost,
                'completed_at' => now(),
            ]);

            return [
                'success' => true,
                'message' => $aiMessage,
                'tokens' => $tokensUsed,
                'cost' => $cost,
            ];

        } catch (\Exception $e) {
            Log::error('Lead AI Chat Error', [
                'lead_id' => $lead->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Sorry, I encountered an error. Please try again.',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Enrich lead by analyzing their website
     */
    public function enrichLead(Lead $lead, int $userId): array
    {
        // Create action record
        $action = LeadAIAction::create([
            'lead_id' => $lead->id,
            'user_id' => $userId,
            'action_type' => 'enrich_lead',
            'input_data' => [
                'website' => $lead->website,
                'company_name' => $lead->company_name,
            ],
            'status' => 'pending',
        ]);

        try {
            if (empty($lead->website)) {
                throw new \Exception('No website URL provided for this lead');
            }

            // Snapshot before
            $fieldsBefore = $lead->only([
                'description', 'tour_types', 'target_markets',
                'business_type', 'annual_volume', 'certifications'
            ]);

            // Build enrichment prompt
            $prompt = $this->buildEnrichmentPrompt($lead);

            // Call AI
            $response = $this->callDeepSeek([
                ['role' => 'system', 'content' => 'You are an expert at analyzing tour operator businesses. Extract structured data from websites.'],
                ['role' => 'user', 'content' => $prompt],
            ], 0.5, 2000);

            $aiResponse = $response['choices'][0]['message']['content'];
            $enrichmentData = $this->parseEnrichmentResponse($aiResponse);

            // Update lead with enriched data
            $fieldsToUpdate = [];
            foreach ($enrichmentData as $field => $value) {
                if (!empty($value) && empty($lead->$field) && $field !== 'insights') {
                    $fieldsToUpdate[$field] = $value;
                }
            }

            if (!empty($fieldsToUpdate)) {
                $lead->update($fieldsToUpdate);
            }

            // Snapshot after
            $fieldsAfter = $lead->fresh()->only([
                'description', 'tour_types', 'target_markets',
                'business_type', 'annual_volume', 'certifications'
            ]);

            $fieldsChanged = array_keys($fieldsToUpdate);

            // Record enrichment
            LeadEnrichment::create([
                'lead_id' => $lead->id,
                'user_id' => $userId,
                'action_id' => $action->id,
                'fields_before' => $fieldsBefore,
                'fields_after' => $fieldsAfter,
                'fields_changed' => $fieldsChanged,
                'ai_insights' => $enrichmentData['insights'] ?? null,
                'source' => 'website_analysis',
            ]);

            // Update action record
            $tokensUsed = $response['usage']['total_tokens'];
            $cost = $this->calculateCost($response['usage']);

            $action->update([
                'output_data' => $enrichmentData,
                'result_summary' => count($fieldsChanged) . ' fields updated: ' . implode(', ', $fieldsChanged),
                'status' => 'completed',
                'tokens_used' => $tokensUsed,
                'cost' => $cost,
                'completed_at' => now(),
            ]);

            return [
                'success' => true,
                'fields_updated' => $fieldsChanged,
                'data' => $enrichmentData,
                'cost' => $cost,
            ];

        } catch (\Exception $e) {
            $action->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
                'completed_at' => now(),
            ]);

            Log::error('Lead Enrichment Error', [
                'lead_id' => $lead->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Generate personalized email
     */
    public function generateEmail(Lead $lead, string $purpose, string $tone, int $userId): array
    {
        $action = LeadAIAction::create([
            'lead_id' => $lead->id,
            'user_id' => $userId,
            'action_type' => 'generate_email',
            'input_data' => [
                'purpose' => $purpose,
                'tone' => $tone,
            ],
            'status' => 'pending',
        ]);

        try {
            $prompt = $this->buildEmailPrompt($lead, $purpose, $tone);

            $response = $this->callDeepSeek([
                ['role' => 'system', 'content' => 'You are an expert sales email writer specializing in B2B tour operator partnerships.'],
                ['role' => 'user', 'content' => $prompt],
            ], 0.8, 1000);

            $aiResponse = $response['choices'][0]['message']['content'];
            $emailData = $this->parseEmailResponse($aiResponse);

            $tokensUsed = $response['usage']['total_tokens'];
            $cost = $this->calculateCost($response['usage']);

            $action->update([
                'output_data' => $emailData,
                'result_summary' => 'Email generated: ' . ($emailData['subject'] ?? 'No subject'),
                'status' => 'completed',
                'tokens_used' => $tokensUsed,
                'cost' => $cost,
                'completed_at' => now(),
            ]);

            return [
                'success' => true,
                'email' => $emailData,
                'cost' => $cost,
            ];

        } catch (\Exception $e) {
            $action->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
                'completed_at' => now(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Suggest next follow-up action and timing
     */
    public function suggestFollowup(Lead $lead, int $userId): array
    {
        $action = LeadAIAction::create([
            'lead_id' => $lead->id,
            'user_id' => $userId,
            'action_type' => 'suggest_followup',
            'input_data' => [
                'status' => $lead->status,
                'last_contacted' => $lead->last_contacted_at,
            ],
            'status' => 'pending',
        ]);

        try {
            $prompt = $this->buildFollowupPrompt($lead);

            $response = $this->callDeepSeek([
                ['role' => 'system', 'content' => 'You are an expert sales strategist specializing in lead nurturing and follow-up timing.'],
                ['role' => 'user', 'content' => $prompt],
            ], 0.7, 800);

            $aiResponse = $response['choices'][0]['message']['content'];
            $followupData = $this->parseFollowupResponse($aiResponse);

            $tokensUsed = $response['usage']['total_tokens'];
            $cost = $this->calculateCost($response['usage']);

            $action->update([
                'output_data' => $followupData,
                'result_summary' => $followupData['action'] ?? 'Follow-up suggested',
                'status' => 'completed',
                'tokens_used' => $tokensUsed,
                'cost' => $cost,
                'completed_at' => now(),
            ]);

            return [
                'success' => true,
                'followup' => $followupData,
                'cost' => $cost,
            ];

        } catch (\Exception $e) {
            $action->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
                'completed_at' => now(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Call DeepSeek API
     */
    protected function callDeepSeek(array $messages, float $temperature = 0.7, int $maxTokens = 2000): array
    {
        $response = Http::timeout(120)
            ->withHeaders([
                'Authorization' => 'Bearer ' . config('openai.api_key'),
                'Content-Type' => 'application/json',
            ])
            ->post('https://api.deepseek.com/v1/chat/completions', [
                'model' => 'deepseek-chat',
                'messages' => $messages,
                'temperature' => $temperature,
                'max_tokens' => $maxTokens,
            ]);

        $data = $response->json();

        if (!isset($data['choices'][0]['message']['content'])) {
            throw new \Exception('Invalid API response');
        }

        return $data;
    }

    /**
     * Build copilot system prompt with lead context
     */
    protected function buildCopilotSystemPrompt(Lead $lead): string
    {
        $context = "You are an AI sales assistant helping manage a lead in our CRM.\n\n";
        $context .= "CURRENT LEAD CONTEXT:\n";
        $context .= "Company: {$lead->company_name}\n";
        $context .= "Country: {$lead->country}\n";
        $context .= "Status: {$lead->status}\n";
        $context .= "Website: " . ($lead->website ?? 'Not provided') . "\n";
        $context .= "Last Contact: " . ($lead->last_contacted_at ? $lead->last_contacted_at->diffForHumans() : 'Never') . "\n";

        if ($lead->tour_types) {
            $context .= "Tour Types: " . implode(', ', $lead->tour_types) . "\n";
        }

        $context .= "\nYou can help with:\n";
        $context .= "- Researching the company\n";
        $context .= "- Generating personalized emails\n";
        $context .= "- Suggesting follow-up strategies\n";
        $context .= "- Enriching lead data\n";
        $context .= "- Answering questions about the lead\n\n";
        $context .= "Be concise, actionable, and professional.";

        return $context;
    }

    /**
     * Build enrichment prompt
     */
    protected function buildEnrichmentPrompt(Lead $lead): string
    {
        $prompt = "Analyze this tour operator company and extract structured data:\n\n";
        $prompt .= "Company: {$lead->company_name}\n";
        $prompt .= "Website: {$lead->website}\n";
        $prompt .= "Country: {$lead->country}\n\n";

        $prompt .= "Please research and provide:\n";
        $prompt .= "1. Company description (2-3 sentences)\n";
        $prompt .= "2. Tour types they offer (array: cultural, adventure, luxury, budget, family, etc.)\n";
        $prompt .= "3. Target markets/regions (array of countries/regions)\n";
        $prompt .= "4. Business type (inbound, outbound, dmc, ota, hybrid)\n";
        $prompt .= "5. Estimated annual volume (small: <500, medium: 500-2000, large: 2000-10000, enterprise: >10000)\n";
        $prompt .= "6. Certifications (array if visible)\n";
        $prompt .= "7. Key insights (what makes them unique, partnership potential)\n\n";

        $prompt .= "Return ONLY valid JSON with this structure:\n";
        $prompt .= json_encode([
            'description' => 'Company description',
            'tour_types' => ['cultural', 'adventure'],
            'target_markets' => ['Europe', 'USA'],
            'business_type' => 'dmc',
            'annual_volume' => 'medium',
            'certifications' => ['ISO 9001'],
            'insights' => 'Key insights about partnership potential',
        ], JSON_PRETTY_PRINT);

        return $prompt;
    }

    /**
     * Build email generation prompt
     */
    protected function buildEmailPrompt(Lead $lead, string $purpose, string $tone): string
    {
        $prompt = "Generate a personalized B2B email for this tour operator lead:\n\n";
        $prompt .= "LEAD INFO:\n";
        $prompt .= "Company: {$lead->company_name}\n";
        $prompt .= "Country: {$lead->country}\n";
        $prompt .= "Status: {$lead->status}\n";

        if ($lead->tour_types) {
            $prompt .= "Tour Types: " . implode(', ', $lead->tour_types) . "\n";
        }

        if ($lead->target_markets) {
            $prompt .= "Target Markets: " . implode(', ', $lead->target_markets) . "\n";
        }

        $prompt .= "\nEMAIL PURPOSE: {$purpose}\n";
        $prompt .= "TONE: {$tone}\n\n";

        $prompt .= "OUR COMPANY: Uzbekistan tour operator specializing in Silk Road tours\n\n";

        $prompt .= "Generate:\n";
        $prompt .= "1. Subject line (compelling, under 60 chars)\n";
        $prompt .= "2. Email body (personalized, 150-250 words)\n";
        $prompt .= "3. Call to action\n\n";

        $prompt .= "Return ONLY valid JSON:\n";
        $prompt .= json_encode([
            'subject' => 'Subject line',
            'body' => 'Email body with personalization',
            'cta' => 'Call to action',
        ], JSON_PRETTY_PRINT);

        return $prompt;
    }

    /**
     * Build follow-up suggestion prompt
     */
    protected function buildFollowupPrompt(Lead $lead): string
    {
        $prompt = "Suggest the best next follow-up action for this lead:\n\n";
        $prompt .= "Company: {$lead->company_name}\n";
        $prompt .= "Status: {$lead->status}\n";
        $prompt .= "Last Contacted: " . ($lead->last_contacted_at ? $lead->last_contacted_at->format('Y-m-d H:i') : 'Never') . "\n";
        $prompt .= "Next Follow-up: " . ($lead->next_followup_at ? $lead->next_followup_at->format('Y-m-d H:i') : 'Not set') . "\n\n";

        $prompt .= "Provide:\n";
        $prompt .= "1. Recommended action (call, email, send proposal, etc.)\n";
        $prompt .= "2. Suggested timing (date/time)\n";
        $prompt .= "3. Key talking points or content suggestions\n";
        $prompt .= "4. Rationale for this approach\n\n";

        $prompt .= "Return ONLY valid JSON:\n";
        $prompt .= json_encode([
            'action' => 'send_email',
            'timing' => '2025-10-27 10:00',
            'talking_points' => ['Point 1', 'Point 2'],
            'rationale' => 'Why this approach makes sense',
        ], JSON_PRETTY_PRINT);

        return $prompt;
    }

    /**
     * Parse enrichment response
     */
    protected function parseEnrichmentResponse(string $response): array
    {
        $response = preg_replace('/^```json\s*/m', '', $response);
        $response = preg_replace('/\s*```$/m', '', $response);
        $response = trim($response);

        $data = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('Invalid JSON response from AI: ' . json_last_error_msg());
        }

        return $data;
    }

    /**
     * Parse email response
     */
    protected function parseEmailResponse(string $response): array
    {
        return $this->parseEnrichmentResponse($response);
    }

    /**
     * Parse follow-up response
     */
    protected function parseFollowupResponse(string $response): array
    {
        return $this->parseEnrichmentResponse($response);
    }

    /**
     * Calculate cost (DeepSeek pricing)
     */
    protected function calculateCost(array $usage): float
    {
        $inputCost = ($usage['prompt_tokens'] / 1000000) * 0.14;
        $outputCost = ($usage['completion_tokens'] / 1000000) * 0.28;
        return round($inputCost + $outputCost, 6);
    }
}
