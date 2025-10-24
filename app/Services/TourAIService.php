<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TourAIService
{
    /**
     * Generate a complete tour using DeepSeek AI
     *
     * @param array $params
     * @return array
     * @throws \Exception
     */
    public function generateTour(array $params): array
    {
        try {
            $prompt = $this->buildPrompt($params);

            $response = Http::timeout(120)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . config('openai.api_key'),
                    'Content-Type' => 'application/json',
                ])
                ->post('https://api.deepseek.com/v1/chat/completions', [
                    'model' => 'deepseek-chat',
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => $this->getSystemPrompt()
                        ],
                        [
                            'role' => 'user',
                            'content' => $prompt
                        ]
                    ],
                    'temperature' => 0.7,
                    'max_tokens' => 4000,
                ]);

            $data = $response->json();

            if (!isset($data['choices'][0]['message']['content'])) {
                throw new \Exception('Invalid API response structure');
            }

            $content = $data['choices'][0]['message']['content'];

            // Parse JSON response
            $tourData = $this->parseAIResponse($content);

            // Add metadata
            $tourData['_meta'] = [
                'tokens_used' => $data['usage']['total_tokens'] ?? 0,
                'prompt_tokens' => $data['usage']['prompt_tokens'] ?? 0,
                'completion_tokens' => $data['usage']['completion_tokens'] ?? 0,
                'cost' => $this->calculateCost((object) ($data['usage'] ?? [])),
            ];

            return $tourData;

        } catch (\Exception $e) {
            Log::error('DeepSeek API Error', [
                'message' => $e->getMessage(),
                'params' => $params,
            ]);

            throw new \Exception('AI generation failed: ' . $e->getMessage());
        }
    }

    /**
     * Regenerate a specific day of a tour
     *
     * @param \App\Models\Tour $tour
     * @param int $dayNumber
     * @param string|null $customPrompt
     * @return array
     */
    public function regenerateDay($tour, int $dayNumber, ?string $customPrompt = null): array
    {
        // Get existing tour context
        $days = $tour->itineraryItems()->where('type', 'day')->orderBy('sort_order')->get();
        $targetDay = $days[$dayNumber] ?? null;

        if (!$targetDay) {
            throw new \Exception('Day not found');
        }

        $prompt = "Regenerate day {$dayNumber} of this {$tour->duration_days}-day tour: {$tour->title}\n\n";
        $prompt .= "Current day title: {$targetDay->title}\n\n";

        if ($customPrompt) {
            $prompt .= "Special instructions: {$customPrompt}\n\n";
        }

        $prompt .= "Generate a detailed itinerary for this day with multiple stops, including timing and descriptions.";
        $prompt .= " Return ONLY a JSON object with this structure:\n";
        $prompt .= json_encode([
            'title' => 'Day title',
            'description' => 'Day overview',
            'default_start_time' => 'HH:MM',
            'stops' => [
                [
                    'title' => 'Stop name',
                    'description' => 'What to see/do',
                    'default_start_time' => 'HH:MM',
                    'duration_minutes' => 60
                ]
            ]
        ], JSON_PRETTY_PRINT);

        try {
            $response = Http::timeout(120)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . config('openai.api_key'),
                    'Content-Type' => 'application/json',
                ])
                ->post('https://api.deepseek.com/v1/chat/completions', [
                    'model' => 'deepseek-chat',
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'You are an expert tour planner. Always respond with valid JSON only, no additional text.'
                        ],
                        [
                            'role' => 'user',
                            'content' => $prompt
                        ]
                    ],
                    'temperature' => 0.8,
                    'max_tokens' => 2000,
                ]);

            $data = $response->json();
            $content = $data['choices'][0]['message']['content'] ?? throw new \Exception('Invalid API response');
            return json_decode($content, true);

        } catch (\Exception $e) {
            Log::error('DeepSeek Regenerate Day Error', [
                'message' => $e->getMessage(),
                'tour_id' => $tour->id,
                'day_number' => $dayNumber,
            ]);

            throw new \Exception('Day regeneration failed: ' . $e->getMessage());
        }
    }

    /**
     * Build the user prompt from parameters
     */
    protected function buildPrompt(array $params): string
    {
        $destinations = $params['destinations'];
        $duration = $params['duration_days'];
        $style = $this->getTourStyleDescription($params['tour_style']);
        $interests = $params['special_interests'] ?? '';
        $notes = $params['additional_notes'] ?? '';

        $prompt = "Create a detailed {$duration}-day {$style} tour covering: {$destinations}\n\n";

        if ($interests) {
            $prompt .= "Focus on these interests: {$interests}\n\n";
        }

        if ($notes) {
            $prompt .= "Additional requirements: {$notes}\n\n";
        }

        $prompt .= "Generate a complete tour itinerary with:\n";
        $prompt .= "- An engaging tour title\n";
        $prompt .= "- A 2-3 sentence overview\n";
        $prompt .= "- Daily itineraries with multiple stops per day\n";
        $prompt .= "- Realistic timing (start times, durations)\n";
        $prompt .= "- Specific activities and descriptions\n\n";

        $prompt .= "Return ONLY valid JSON matching this structure:\n";
        $prompt .= json_encode($this->getExpectedStructure(), JSON_PRETTY_PRINT);

        return $prompt;
    }

    /**
     * Get system prompt for tour generation
     */
    protected function getSystemPrompt(): string
    {
        return "You are an expert international tour planner with deep knowledge of destinations worldwide. " .
               "You create detailed, realistic, and engaging tour itineraries. " .
               "Always consider practical logistics like travel time, opening hours, and seasonal factors. " .
               "Respond ONLY with valid JSON, no additional commentary or explanation.";
    }

    /**
     * Parse AI response and validate structure
     */
    protected function parseAIResponse(string $response): array
    {
        // Remove markdown code blocks if present
        $response = preg_replace('/^```json\s*/m', '', $response);
        $response = preg_replace('/\s*```$/m', '', $response);
        $response = trim($response);

        $data = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('Invalid JSON response from AI: ' . json_last_error_msg());
        }

        // Validate structure
        if (!isset($data['title']) || !isset($data['days']) || !is_array($data['days'])) {
            throw new \Exception('AI response missing required fields');
        }

        return $data;
    }

    /**
     * Calculate cost based on DeepSeek pricing
     * DeepSeek pricing: ~$0.14 per million input tokens, ~$0.28 per million output tokens
     */
    protected function calculateCost(object $usage): float
    {
        $inputCost = ($usage->prompt_tokens / 1000000) * 0.14;
        $outputCost = ($usage->completion_tokens / 1000000) * 0.28;

        return round($inputCost + $outputCost, 6);
    }

    /**
     * Get tour style description
     */
    protected function getTourStyleDescription(string $style): string
    {
        return match($style) {
            'cultural_heritage' => 'cultural heritage',
            'adventure_nature' => 'adventure and nature',
            'luxury_experience' => 'luxury',
            'budget_friendly' => 'budget-friendly',
            'family_friendly' => 'family-friendly',
            'photography' => 'photography-focused',
            default => 'cultural',
        };
    }

    /**
     * Get expected JSON structure for AI
     */
    protected function getExpectedStructure(): array
    {
        return [
            'title' => 'Engaging Tour Title',
            'duration_days' => 8,
            'description' => 'Brief 2-3 sentence tour overview',
            'days' => [
                [
                    'title' => 'Day Title (e.g., Arrival in Tashkent)',
                    'description' => 'Day overview paragraph',
                    'default_start_time' => '09:00',
                    'stops' => [
                        [
                            'title' => 'Activity or Site Name',
                            'description' => 'What to see, do, or experience here',
                            'default_start_time' => '10:00',
                            'duration_minutes' => 90
                        ],
                        [
                            'title' => 'Next Activity',
                            'description' => 'Details about this stop',
                            'default_start_time' => '14:00',
                            'duration_minutes' => 120
                        ]
                    ]
                ]
            ]
        ];
    }
}
