<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BlogAIService
{
    public function generateBlogPost(array $params): array
    {
        try {
            $prompt = $this->buildPrompt($params);

            $response = Http::timeout(120)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . config('openai.api_key'),
                    'Content-Type' => 'application/json',
                ])
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => 'gpt-4o-mini',
                    'messages' => [
                        ['role' => 'system', 'content' => $this->getSystemPrompt()],
                        ['role' => 'user', 'content' => $prompt]
                    ],
                    'temperature' => 0.7,
                    'max_tokens' => 4000,
                ]);

            $data = $response->json();

            if (!$response->successful() || !isset($data['choices'][0]['message']['content'])) {
                Log::error('OpenAI API Response', ['status' => $response->status(), 'body' => $response->body()]);
                throw new \Exception('Invalid API response from OpenAI: ' . $response->body());
            }

            $content = $data['choices'][0]['message']['content'];
            $blogData = $this->parseAIResponse($content);

            $blogData['_meta'] = [
                'tokens_used' => $data['usage']['total_tokens'] ?? 0,
                'prompt_tokens' => $data['usage']['prompt_tokens'] ?? 0,
                'completion_tokens' => $data['usage']['completion_tokens'] ?? 0,
                'cost' => $this->calculateCost((object) ($data['usage'] ?? [])),
            ];

            return $blogData;

        } catch (\Exception $e) {
            Log::error('OpenAI API Error (Blog)', [
                'message' => $e->getMessage(),
                'params' => $params,
            ]);
            throw new \Exception('Blog AI generation failed: ' . $e->getMessage());
        }
    }

    protected function getSystemPrompt(): string
    {
        return 'You are an expert travel blog writer specializing in Uzbekistan tourism and Central Asian travel. Write engaging, SEO-optimized content with proper HTML formatting using h2, h3, p, ul, li tags. Include practical details like prices, timing, and tips. Never use h1 tags. Write in a friendly, informative tone that helps travelers plan their trips.';
    }

    protected function buildPrompt(array $params): string
    {
        $topic = $params['topic'];
        $keywords = $params['keywords'] ?? '';
        $audience = $params['target_audience'] ?? 'general';
        $tone = $params['tone'] ?? 'casual';
        $wordCount = $params['word_count'] ?? 1000;
        $notes = $params['additional_notes'] ?? '';

        $prompt = "Generate a travel blog post about: {$topic}\n\n";
        $prompt .= "Target audience: {$audience}\n";
        $prompt .= "Tone: {$tone}\n";
        $prompt .= "Word count: approximately {$wordCount} words\n";
        
        if ($keywords) {
            $prompt .= "Keywords: {$keywords}\n";
        }
        
        if ($notes) {
            $prompt .= "Additional notes: {$notes}\n";
        }

        $prompt .= "\nReturn ONLY valid JSON with this structure (no markdown code blocks):\n";
        $prompt .= '{"title": "SEO title 50-60 chars", "excerpt": "150-200 char summary", "content": "HTML formatted content with h2, h3, p, ul tags", "suggested_tags": ["tag1", "tag2", "tag3"], "suggested_category": "category name", "meta_title": "60 char SEO title", "meta_description": "160 char SEO description", "reading_time": 8}';

        return $prompt;
    }

    protected function parseAIResponse(string $content): array
    {
        $content = preg_replace('/^```json\s*/', '', $content);
        $content = preg_replace('/\s*```$/', '', $content);
        $content = trim($content);

        $data = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::error('JSON Parse Error', ['content' => $content, 'error' => json_last_error_msg()]);
            throw new \Exception('Failed to parse JSON: ' . json_last_error_msg());
        }

        $required = ['title', 'excerpt', 'content'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                throw new \Exception("Missing required field: {$field}");
            }
        }

        return $data;
    }

    protected function calculateCost(object $usage): float
    {
        $inputTokens = $usage->prompt_tokens ?? 0;
        $outputTokens = $usage->completion_tokens ?? 0;
        
        // GPT-4o-mini pricing: $0.150 per 1M input tokens, $0.600 per 1M output tokens
        $inputCost = ($inputTokens / 1000000) * 0.150;
        $outputCost = ($outputTokens / 1000000) * 0.600;
        
        return round($inputCost + $outputCost, 4);
    }
}
