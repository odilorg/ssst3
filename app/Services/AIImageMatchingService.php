<?php

namespace App\Services;

use App\Models\Tour;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

class AIImageMatchingService
{
    private string $apiKey;
    private string $model;
    private ImageDiscoveryService $imageDiscovery;

    public function __construct()
    {
        $this->apiKey = config('services.openai.api_key');
        $this->model = config('services.openai.vision_model', 'gpt-4o');
        $this->imageDiscovery = new ImageDiscoveryService();
    }

    /**
     * Select the best images for a tour using AI
     *
     * @param Tour $tour
     * @param array $candidateImages
     * @return array
     * @throws \Exception
     */
    public function selectImagesForTour(Tour $tour, array $candidateImages): array
    {
        if (empty($this->apiKey)) {
            throw new \Exception('OpenAI API key not configured. Add OPENAI_API_KEY to .env');
        }

        if (count($candidateImages) < 5) {
            throw new \Exception("Not enough images. Found " . count($candidateImages) . ", need at least 5");
        }

        // Limit to first 15 images to avoid token limits
        $imagesToAnalyze = array_slice($candidateImages, 0, 15);

        // Build tour context
        $tourContext = $this->buildTourContext($tour);

        // Encode images to base64
        $encodedImages = $this->encodeImages($imagesToAnalyze);

        // Call OpenAI API
        $response = $this->callOpenAI($tourContext, $encodedImages);

        // Parse and validate response
        $selectedImages = $this->parseAIResponse($response, $imagesToAnalyze);

        return $selectedImages;
    }

    /**
     * Build tour context for AI prompt
     *
     * @param Tour $tour
     * @return array
     */
    private function buildTourContext(Tour $tour): array
    {
        $description = $tour->short_description ?? $tour->long_description;
        $description = strip_tags($description);
        $description = substr($description, 0, 500); // Limit length

        return [
            'title' => $tour->title,
            'description' => $description,
            'duration' => $tour->duration_days . ' days',
            'city' => $tour->city?->name ?? 'Unknown',
            'type' => $tour->tour_type,
            'highlights' => is_array($tour->highlights) ? implode(', ', array_slice($tour->highlights, 0, 5)) : '',
        ];
    }

    /**
     * Encode images to base64
     *
     * @param array $images
     * @return array
     */
    private function encodeImages(array $images): array
    {
        $encoded = [];

        foreach ($images as $index => $image) {
            $base64 = $this->imageDiscovery->encodeImageToBase64($image['full_path']);

            if ($base64) {
                $encoded[] = [
                    'index' => $index,
                    'base64' => $base64,
                    'extension' => $image['extension'],
                    'filename' => $image['filename'],
                    'relative_path' => $image['relative_path'],
                ];
            }
        }

        return $encoded;
    }

    /**
     * Call OpenAI Vision API
     *
     * @param array $tourContext
     * @param array $encodedImages
     * @return array
     * @throws GuzzleException
     */
    private function callOpenAI(array $tourContext, array $encodedImages): array
    {
        $client = new Client([
            'timeout' => 120,
            'verify' => false, // For local development
        ]);

        $messages = [
            [
                'role' => 'system',
                'content' => 'You are an expert travel photographer and tour marketing specialist. Your task is to select the most appropriate images for a tour package based on the tour details. You must respond ONLY with valid JSON, no other text or explanation.'
            ],
            [
                'role' => 'user',
                'content' => $this->buildMessageContent($tourContext, $encodedImages)
            ]
        ];

        Log::info('Calling OpenAI Vision API', [
            'tour' => $tourContext['title'],
            'image_count' => count($encodedImages)
        ]);

        try {
            $response = $client->post('https://api.openai.com/v1/chat/completions', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'model' => $this->model,
                    'messages' => $messages,
                    'max_tokens' => 1000,
                    'temperature' => 0.3,
                ]
            ]);

            $result = json_decode($response->getBody()->getContents(), true);

            Log::info('OpenAI API response received', [
                'usage' => $result['usage'] ?? null
            ]);

            return $result;

        } catch (GuzzleException $e) {
            Log::error('OpenAI API call failed', [
                'error' => $e->getMessage(),
                'code' => $e->getCode()
            ]);
            throw $e;
        }
    }

    /**
     * Build message content with images
     *
     * @param array $tourContext
     * @param array $encodedImages
     * @return array
     */
    private function buildMessageContent(array $tourContext, array $encodedImages): array
    {
        $content = [];

        // Add text prompt
        $content[] = [
            'type' => 'text',
            'text' => $this->buildPrompt($tourContext, count($encodedImages))
        ];

        // Add each image with label
        foreach ($encodedImages as $image) {
            $content[] = [
                'type' => 'image_url',
                'image_url' => [
                    'url' => "data:image/{$image['extension']};base64,{$image['base64']}",
                    'detail' => 'low' // Use 'low' to reduce token usage
                ]
            ];

            $content[] = [
                'type' => 'text',
                'text' => "Image #{$image['index']}: {$image['filename']}"
            ];
        }

        return $content;
    }

    /**
     * Build AI prompt
     *
     * @param array $tourContext
     * @param int $imageCount
     * @return string
     */
    private function buildPrompt(array $tourContext, int $imageCount): string
    {
        $highlights = !empty($tourContext['highlights']) ? "\n- Highlights: {$tourContext['highlights']}" : '';

        return <<<PROMPT
I need you to select 5 images for this tour package:

**Tour Details:**
- Title: {$tourContext['title']}
- Description: {$tourContext['description']}
- Duration: {$tourContext['duration']}
- Main City: {$tourContext['city']}
- Type: {$tourContext['type']}{$highlights}

**Your Task:**
From the {$imageCount} images provided, select exactly 5 images:

1. **ONE hero image** - The most compelling, iconic image that best represents this tour. This will be the main featured image.
2. **FOUR gallery images** - Supporting images showing variety (landscapes, architecture, culture, activities).

**Selection Criteria:**
- Images MUST be relevant to the tour location, theme, and description
- Hero image should be stunning, high-quality, and immediately captivating
- Hero image should ideally show the most iconic landmark or scene from the tour
- Gallery should show diversity of experiences (don't select 4 similar images)
- Prioritize authentic cultural, architectural, and natural beauty shots
- Avoid generic or low-quality images

**Important Rules:**
- All 5 selected images must have DIFFERENT index numbers (no duplicates)
- Image indices must be between 0 and {$imageCount}
- Respond ONLY with valid JSON, no other text

**Output Format (JSON only):**
```json
{
  "hero": {
    "index": <image_number>,
    "reason": "<brief reason why this is the best hero image>"
  },
  "gallery": [
    {
      "index": <image_number>,
      "reason": "<why this supports the tour>"
    },
    {
      "index": <image_number>,
      "reason": "<why this supports the tour>"
    },
    {
      "index": <image_number>,
      "reason": "<why this supports the tour>"
    },
    {
      "index": <image_number>,
      "reason": "<why this supports the tour>"
    }
  ]
}
```

Respond now with ONLY the JSON, no markdown code blocks, no explanation.
PROMPT;
    }

    /**
     * Parse AI response and map to actual image data
     *
     * @param array $apiResponse
     * @param array $originalImages
     * @return array
     * @throws \Exception
     */
    private function parseAIResponse(array $apiResponse, array $originalImages): array
    {
        if (!isset($apiResponse['choices'][0]['message']['content'])) {
            throw new \Exception('Invalid API response structure');
        }

        $content = $apiResponse['choices'][0]['message']['content'];

        // Try to extract JSON from response (in case AI wrapped it in markdown)
        if (str_contains($content, '```json')) {
            preg_match('/```json\s*(.*?)\s*```/s', $content, $matches);
            $content = $matches[1] ?? $content;
        } elseif (str_contains($content, '```')) {
            preg_match('/```\s*(.*?)\s*```/s', $content, $matches);
            $content = $matches[1] ?? $content;
        }

        $content = trim($content);

        $selection = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::error('Failed to parse AI JSON response', [
                'content' => $content,
                'json_error' => json_last_error_msg()
            ]);
            throw new \Exception('Failed to parse AI response: ' . json_last_error_msg());
        }

        // Validate structure
        if (!isset($selection['hero']['index']) || !isset($selection['gallery'])) {
            throw new \Exception('AI response missing required fields (hero or gallery)');
        }

        if (count($selection['gallery']) !== 4) {
            throw new \Exception('AI must select exactly 4 gallery images, got ' . count($selection['gallery']));
        }

        // Map indices to actual images
        $heroIndex = $selection['hero']['index'];
        if (!isset($originalImages[$heroIndex])) {
            throw new \Exception("Invalid hero image index: {$heroIndex}");
        }

        $result = [
            'hero' => array_merge($originalImages[$heroIndex], [
                'reason' => $selection['hero']['reason'] ?? 'Selected as hero image'
            ]),
            'gallery' => []
        ];

        // Validate no duplicates
        $selectedIndices = [$heroIndex];

        foreach ($selection['gallery'] as $galleryItem) {
            $index = $galleryItem['index'];

            if (!isset($originalImages[$index])) {
                throw new \Exception("Invalid gallery image index: {$index}");
            }

            if (in_array($index, $selectedIndices)) {
                throw new \Exception("Duplicate image index selected: {$index}");
            }

            $selectedIndices[] = $index;

            $result['gallery'][] = array_merge($originalImages[$index], [
                'reason' => $galleryItem['reason'] ?? 'Selected for gallery'
            ]);
        }

        Log::info('AI image selection parsed successfully', [
            'hero' => $result['hero']['filename'],
            'gallery_count' => count($result['gallery'])
        ]);

        return $result;
    }
}
