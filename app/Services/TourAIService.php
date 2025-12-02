<?php

namespace App\Services;

use App\Models\City;
use App\Models\TourCategory;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class TourAIService
{
    protected string $apiKey;
    protected string $model = 'gpt-4o-mini';

    public function __construct()
    {
        $this->apiKey = env('OPENAI_REAL_API_KEY', config('openai.api_key'));
    }

    /**
     * Generate a complete tour using OpenAI
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
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => $this->model,
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
                    'response_format' => ['type' => 'json_object'],
                ]);

            $data = $response->json();

            if (!isset($data['choices'][0]['message']['content'])) {
                throw new \Exception('Invalid API response structure');
            }

            $content = $data['choices'][0]['message']['content'];

            // Parse JSON response
            $tourData = $this->parseAIResponse($content);
            $tourData = $this->enhanceTourData($tourData, $params);

            // Add metadata
            $tourData['_meta'] = [
                'tokens_used' => $data['usage']['total_tokens'] ?? 0,
                'prompt_tokens' => $data['usage']['prompt_tokens'] ?? 0,
                'completion_tokens' => $data['usage']['completion_tokens'] ?? 0,
                'cost' => $this->calculateCost((object) ($data['usage'] ?? [])),
            ];

            return $tourData;

        } catch (\Exception $e) {
            Log::error('OpenAI API Error', [
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
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => $this->model,
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
            Log::error('OpenAI Regenerate Day Error', [
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
        return 'You are an expert tour planner specializing in Uzbekistan Silk Road tours. ' .
               'Create detailed, realistic itineraries with specific landmarks in Uzbekistan. ' .
               'Include 3-5 highlights, 5-8 included items, 3-5 excluded items. ' .
               'Each day should have 2-4 stops with realistic timing. ' .
               'ALWAYS respond with valid JSON only, no additional text.';
    }

    /**
     * Enhance tour data with database lookups
     */
    protected function enhanceTourData(array $tourData, array $params): array
    {
        $tourData['slug'] = Str::slug($tourData['title']);

        // Match city
        $destinations = $params['destinations'] ?? '';
        $city = $this->findMatchingCity($destinations);
        if ($city) {
            $tourData['city_id'] = $city->id;
        }

        // Match category
        $tourStyle = $params['tour_style'] ?? 'cultural_heritage';
        $category = $this->findMatchingCategory($tourStyle);
        if ($category) {
            $tourData['category_ids'] = [$category->id];
        }

        $tourData['tour_type'] = match($tourStyle) {
            'luxury_experience' => 'private_only',
            'budget_friendly' => 'group_only',
            default => 'hybrid',
        };

        $tourData['currency'] = $tourData['currency'] ?? 'USD';
        $tourData['min_guests'] = $tourData['min_guests'] ?? 1;
        $tourData['max_guests'] = $tourData['max_guests'] ?? 15;
        $tourData['min_booking_hours'] = 24;
        $tourData['cancellation_hours'] = 24;
        $tourData['has_hotel_pickup'] = true;
        $tourData['is_active'] = false;
        $tourData['schema_enabled'] = true;

        if (empty($tourData['seo_title'])) {
            $tourData['seo_title'] = Str::limit($tourData['title'] . ' | Uzbekistan Tours', 60);
        }
        if (empty($tourData['seo_description'])) {
            $tourData['seo_description'] = Str::limit($tourData['short_description'] ?? $tourData['description'] ?? '', 160);
        }

        return $tourData;
    }

    protected function findMatchingCity(string $destinations): ?City
    {
        $parts = array_map('trim', explode(',', $destinations));
        foreach ($parts as $dest) {
            $city = City::where('name', 'LIKE', "%{$dest}%")->first();
            if ($city) return $city;
        }
        return City::first();
    }

    protected function findMatchingCategory(string $tourStyle): ?TourCategory
    {
        $mapping = [
            'cultural_heritage' => ['cultural', 'heritage', 'history'],
            'adventure_nature' => ['adventure', 'nature'],
            'luxury_experience' => ['luxury', 'premium'],
            'budget_friendly' => ['budget', 'economy'],
            'family_friendly' => ['family'],
            'photography' => ['photography', 'photo'],
        ];

        $keywords = $mapping[$tourStyle] ?? ['cultural'];
        foreach ($keywords as $keyword) {
            $category = TourCategory::where('name', 'LIKE', "%{$keyword}%")
                ->where('is_active', true)
                ->first();
            if ($category) return $category;
        }
        return TourCategory::where('is_active', true)->first();
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
     * Calculate cost based on OpenAI GPT-4o-mini pricing
     * GPT-4o-mini pricing: $0.15 per million input tokens, $0.60 per million output tokens
     */
    protected function calculateCost(object $usage): float
    {
        $inputTokens = $usage->prompt_tokens ?? 0;
        $outputTokens = $usage->completion_tokens ?? 0;

        $inputCost = ($inputTokens / 1000000) * 0.15;
        $outputCost = ($outputTokens / 1000000) * 0.60;

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
            'short_description' => 'A compelling 1-2 sentence hook for the tour',
            'duration_days' => 8,
            'description' => 'Detailed 2-3 paragraph tour overview with highlights',
            'highlights' => [
                'Visit the iconic Registan Square',
                'Experience traditional Uzbek hospitality',
                'Explore ancient Silk Road architecture',
            ],
            'included' => [
                'Professional English-speaking guide',
                'All entrance fees',
                'Hotel pickup and drop-off',
                'Comfortable air-conditioned transport',
                'Bottled water during tours',
            ],
            'excluded' => [
                'International flights',
                'Travel insurance',
                'Personal expenses',
                'Tips and gratuities',
            ],
            'requirements' => [
                'Comfortable walking shoes recommended',
                'Modest dress code for religious sites',
            ],
            'private_price' => 150,
            'group_price' => 75,
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
