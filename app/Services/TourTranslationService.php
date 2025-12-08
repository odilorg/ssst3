<?php

namespace App\Services;

use App\Models\Language;
use App\Models\Tour;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TourTranslationService
{
    /**
     * Fields that should be translated
     */
    protected array $translatableFields = [
        'title',
        'short_description',
        'long_description',
        'seo_title',
        'seo_description',
        'seo_keywords',
        'meeting_point_address',
        'meeting_instructions',
        'cancellation_policy',
    ];

    /**
     * Translate a tour to specified language(s)
     *
     * @param Tour $tour
     * @param string|array $targetLanguages Language code(s) to translate to
     * @param bool $force Re-translate even if translation exists
     * @return array Results with success/failure information
     */
    public function translateTour(Tour $tour, string|array $targetLanguages, bool $force = false): array
    {
        $targetLanguages = is_array($targetLanguages) ? $targetLanguages : [$targetLanguages];
        $results = [];

        foreach ($targetLanguages as $targetLang) {
            try {
                // Validate language exists and is active
                $language = Language::where('code', $targetLang)->where('is_active', true)->first();

                if (!$language) {
                    $results[$targetLang] = [
                        'success' => false,
                        'error' => "Language '{$targetLang}' not found or inactive"
                    ];
                    continue;
                }

                // Get source language (default: English)
                $sourceLang = 'en';

                // Count fields that need translation
                $fieldsToTranslate = [];

                foreach ($this->translatableFields as $field) {
                    $translations = $tour->getTranslations($field);

                    // Skip if translation exists and not forcing
                    if (!$force && isset($translations[$targetLang]) && !empty($translations[$targetLang])) {
                        continue;
                    }

                    // Get source text
                    $sourceText = $translations[$sourceLang] ?? $translations['ru'] ?? null;

                    if ($sourceText) {
                        $fieldsToTranslate[$field] = $sourceText;
                    }
                }

                if (empty($fieldsToTranslate)) {
                    $results[$targetLang] = [
                        'success' => true,
                        'message' => 'All fields already translated',
                        'fields_translated' => 0
                    ];
                    continue;
                }

                // Perform AI translation
                $translations = $this->translateFields($fieldsToTranslate, $sourceLang, $targetLang);

                // Save translations to the tour
                foreach ($translations as $field => $translatedText) {
                    $tour->setTranslation($field, $targetLang, $translatedText);
                }

                $tour->save();

                $results[$targetLang] = [
                    'success' => true,
                    'fields_translated' => count($translations)
                ];

            } catch (\Exception $e) {
                Log::error("Translation failed for tour #{$tour->id} to {$targetLang}: " . $e->getMessage());

                $results[$targetLang] = [
                    'success' => false,
                    'error' => $e->getMessage()
                ];
            }
        }

        return $results;
    }

    /**
     * Translate multiple fields using AI
     *
     * @param array $fields Field => text pairs to translate
     * @param string $sourceLang Source language code
     * @param string $targetLang Target language code
     * @return array Translated field => text pairs
     */
    protected function translateFields(array $fields, string $sourceLang, string $targetLang): array
    {
        $prompt = $this->buildTranslationPrompt($fields, $sourceLang, $targetLang);

        $response = Http::timeout(120)
            ->withHeaders([
                'Authorization' => 'Bearer ' . config('services.openai.key'),
                'Content-Type' => 'application/json',
            ])
            ->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-4o-mini', // Cost-effective and high-quality for translations
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => $this->getSystemPrompt($targetLang)
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ],
                'temperature' => 0.3, // Lower temperature for more consistent translations
                'max_tokens' => 4000,
                'response_format' => ['type' => 'json_object'], // Ensure JSON response
            ]);

        $data = $response->json();

        if (!isset($data['choices'][0]['message']['content'])) {
            throw new \Exception('Invalid API response structure');
        }

        $content = $data['choices'][0]['message']['content'];

        // Parse the JSON response
        return $this->parseTranslationResponse($content, $fields);
    }

    /**
     * Build the system prompt for translation
     */
    protected function getSystemPrompt(string $targetLang): string
    {
        $languageNames = [
            'es' => 'Spanish',
            'fr' => 'French',
            'de' => 'German',
            'ru' => 'Russian',
            'en' => 'English',
        ];

        $targetLanguageName = $languageNames[$targetLang] ?? $targetLang;

        return "You are a professional translator specializing in travel and tourism content.
Your task is to translate tour descriptions and information into {$targetLanguageName}.

IMPORTANT GUIDELINES:
1. Maintain the same tone and style - engaging, informative, and promotional
2. Preserve all formatting (markdown, HTML tags, line breaks)
3. Keep proper nouns (city names, monument names) in their original form
4. Adapt culturally when necessary but stay accurate to the source
5. For SEO fields, use natural, search-friendly language
6. Maintain the same level of detail and enthusiasm
7. Return ONLY valid JSON with no additional text or explanation

The response must be a valid JSON object mapping field names to translated text.";
    }

    /**
     * Build the translation prompt
     */
    protected function buildTranslationPrompt(array $fields, string $sourceLang, string $targetLang): string
    {
        $languageNames = [
            'es' => 'Spanish',
            'fr' => 'French',
            'de' => 'German',
            'ru' => 'Russian',
            'en' => 'English',
        ];

        $sourceLanguageName = $languageNames[$sourceLang] ?? $sourceLang;
        $targetLanguageName = $languageNames[$targetLang] ?? $targetLang;

        $fieldsJson = json_encode($fields, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        return "Translate the following tour fields from {$sourceLanguageName} to {$targetLanguageName}.

Source content (JSON format):
{$fieldsJson}

Return the translations in the EXACT same JSON structure with the same field names, but with translated values.
Ensure the response is valid JSON only, no additional text.";
    }

    /**
     * Parse the AI translation response
     */
    protected function parseTranslationResponse(string $content, array $originalFields): array
    {
        // Try to extract JSON from the response
        $content = trim($content);

        // Remove markdown code blocks if present
        $content = preg_replace('/^```json\s*/m', '', $content);
        $content = preg_replace('/\s*```$/m', '', $content);

        $decoded = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('Failed to parse AI translation response: ' . json_last_error_msg());
        }

        // Validate that we got translations for all fields
        foreach (array_keys($originalFields) as $field) {
            if (!isset($decoded[$field]) || empty($decoded[$field])) {
                throw new \Exception("Missing translation for field: {$field}");
            }
        }

        return $decoded;
    }

    /**
     * Get translation progress for a tour
     */
    public function getTranslationProgress(Tour $tour): array
    {
        $activeLanguages = Language::where('is_active', true)->pluck('code')->toArray();
        $progress = [];

        foreach ($activeLanguages as $langCode) {
            $totalFields = count($this->translatableFields);
            $translatedFields = 0;

            foreach ($this->translatableFields as $field) {
                $translations = $tour->getTranslations($field);
                if (isset($translations[$langCode]) && !empty($translations[$langCode])) {
                    $translatedFields++;
                }
            }

            $progress[$langCode] = [
                'total' => $totalFields,
                'translated' => $translatedFields,
                'percentage' => $totalFields > 0 ? round(($translatedFields / $totalFields) * 100, 2) : 0,
                'complete' => $translatedFields === $totalFields
            ];
        }

        return $progress;
    }

    /**
     * Get all translatable fields
     */
    public function getTranslatableFields(): array
    {
        return $this->translatableFields;
    }
}
