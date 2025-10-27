<?php

namespace App\Services;

use App\Models\Lead;
use App\Models\LeadAIConversation;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class AIEmailService
{
    protected string $apiKey;
    protected string $baseUrl;
    protected string $model;
    protected string $provider;

    public function __construct(string $provider = 'deepseek')
    {
        $this->provider = $provider;
        $this->configureProvider($provider);
    }

    protected function configureProvider(string $provider): void
    {
        switch ($provider) {
            case 'openai':
                $this->apiKey = env('OPENAI_REAL_API_KEY', config('services.openai.api_key'));
                $this->baseUrl = 'https://api.openai.com';
                $this->model = 'gpt-4o';
                break;

            case 'openai-mini':
                $this->apiKey = env('OPENAI_REAL_API_KEY', config('services.openai.api_key'));
                $this->baseUrl = 'https://api.openai.com';
                $this->model = 'gpt-4o-mini';
                break;

            case 'deepseek':
            default:
                $this->apiKey = env('DEEPSEEK_API_KEY', config('services.openai.api_key'));
                $this->baseUrl = env('OPENAI_BASE_URL', 'https://api.deepseek.com');
                $this->model = 'deepseek-chat';
                break;
        }
    }

    /**
     * Generate personalized email for a lead
     */
    public function generateEmail(
        Lead $lead,
        string $emailType = 'initial',
        string $tone = 'professional',
        bool $researchWebsite = true
    ): array {
        try {
            // Build context
            $context = $this->buildLeadContext($lead);

            // Build prompt
            $prompt = $this->buildEmailPrompt($lead, $context, $emailType, $tone);

            // Call AI
            $response = $this->callAI($prompt);

            // Parse response
            $emailData = $this->parseAIResponse($response);

            // Log conversation
            $this->logConversation($lead, $prompt, $response, [
                'email_type' => $emailType,
                'tone' => $tone,
            ]);

            return [
                'success' => true,
                'subject' => $emailData['subject'],
                'body' => $emailData['body'],
                'metadata' => [
                    'generated_at' => now()->toIso8601String(),
                    'email_type' => $emailType,
                    'tone' => $tone,
                    'ai_provider' => $this->provider,
                    'ai_model' => $this->model,
                ],
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    protected function buildLeadContext(Lead $lead): array
    {
        return [
            'company_name' => $lead->company_name,
            'website' => $lead->website,
            'country' => $lead->country,
            'city' => $lead->city,
            'contact_name' => $lead->contact_name,
            'contact_position' => $lead->contact_position,
            'tour_types' => $lead->tour_types,
            'certifications' => $lead->certifications,
            'has_uzbekistan_partner' => $lead->has_uzbekistan_partner,
            'quality_score' => $lead->quality_score,
            'notes' => $lead->notes,
        ];
    }

    protected function buildEmailPrompt(Lead $lead, array $context, string $emailType, string $tone): string
    {
        $hasUz = $lead->has_uzbekistan_partner ? 'YES' : 'NO';
        $contextJson = json_encode($context, JSON_PRETTY_PRINT);

        return <<<PROMPT
You are a B2B email writer for a tourism DMC in Uzbekistan.

CONTEXT:
{$contextJson}

Has Uzbekistan already: {$hasUz}
Email Type: {$emailType}
Tone: {$tone}

Write a personalized partnership email.
Requirements:
- Subject: 50-70 chars
- Body: 200-300 words
- Reference specific company details
- Professional but personal
- Soft call-to-action

Return JSON:
{
  "subject": "...",
  "body": "..."
}
PROMPT;
    }

    protected function callAI(string $prompt): string
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json',
        ])->timeout(60)->post("{$this->baseUrl}/v1/chat/completions", [
            'model' => $this->model,
            'messages' => [['role' => 'user', 'content' => $prompt]],
            'temperature' => 0.7,
            'max_tokens' => 1000,
        ]);

        if (!$response->successful()) {
            throw new \Exception('AI API failed: ' . $response->body());
        }

        return $response->json()['choices'][0]['message']['content'] ?? '';
    }

    protected function parseAIResponse(string $response): array
    {
        // Try to extract JSON
        if (preg_match('/\{[^}]+\}/', $response, $matches)) {
            $parsed = json_decode($matches[0], true);
            if ($parsed && isset($parsed['subject'], $parsed['body'])) {
                return $parsed;
            }
        }

        // Fallback
        return [
            'subject' => 'Partnership Opportunity - Uzbekistan Tourism',
            'body' => $response,
        ];
    }

    protected function logConversation(Lead $lead, string $prompt, string $response, array $metadata): void
    {
        try {
            LeadAIConversation::create([
                'lead_id' => $lead->id,
                'user_id' => auth()->id() ?? 1,
                'role' => 'assistant',
                'message' => $response,
                'metadata' => $metadata,
            ]);
        } catch (\Exception $e) {
            \Log::error('AI log failed: ' . $e->getMessage());
        }
    }

    /**
     * Generate subject line variations
     */
    public function generateSubjectLines(Lead $lead, int $count = 3): array
    {
        $prompt = "Generate {$count} email subject lines for partnership with {$lead->company_name} in {$lead->country}. Return JSON array.";

        try {
            $response = $this->callAI($prompt);
            $parsed = json_decode($response, true);
            return is_array($parsed) ? $parsed : ["Partnership Opportunity - {$lead->company_name}"];
        } catch (\Exception $e) {
            return ["Partnership Opportunity - {$lead->company_name}"];
        }
    }
}
