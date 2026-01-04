<?php

namespace App\Services\OpenAI;

use App\Models\Tour;
use App\Models\TourTranslation;
use App\Models\TranslationLog;
use App\Models\Setting;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use OpenAI;

class TranslationService
{
    protected $client;
    protected $model;
    protected $temperature;

    public function __construct()
    {
        $apiKey = Setting::get('ai_translation_api_key');

        if (!$apiKey) {
            throw new \Exception('OpenAI API key not configured. Please set it in AI Translation Settings.');
        }

        $this->client = OpenAI::client($apiKey);
        $this->model = config('ai-translation.openai.model', 'gpt-4-turbo');
        $this->temperature = config('ai-translation.openai.temperature', 0.3);
    }

    /**
     * Translate a single field
     */
    public function translateField(string $text, string $targetLocale, string $sourceLocale = 'en', string $section = 'content'): string
    {
        if (empty($text)) {
            return '';
        }

        $systemPrompt = $this->getSystemPrompt($targetLocale);
        $userPrompt = $this->getUserPrompt($text, $targetLocale, $sourceLocale, $section);

        try {
            $response = $this->client->chat()->create([
                'model' => $this->model,
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user', 'content' => $userPrompt],
                ],
                'temperature' => $this->temperature,
            ]);

            return trim($response->choices[0]->message->content);
        } catch (\Exception $e) {
            Log::error('AI Translation failed for field', [
                'section' => $section,
                'target_locale' => $targetLocale,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Translate entire tour to target locale
     */
    public function translateTour(Tour $tour, string $targetLocale, array $sectionsToTranslate = []): array
    {
        $sourceLocale = 'en'; // Default source
        $sourceTranslation = $tour->translations()->where('locale', $sourceLocale)->first();

        if (!$sourceTranslation) {
            throw new \Exception('Source translation (English) not found for this tour.');
        }

        $sections = config('ai-translation.sections');

        // If no sections specified, translate all
        if (empty($sectionsToTranslate)) {
            $sectionsToTranslate = array_keys($sections);
        }

        $translations = [];
        $totalTokens = 0;

        foreach ($sectionsToTranslate as $field) {
            if (!isset($sections[$field])) {
                continue;
            }

            $sourceValue = $sourceTranslation->{$field};

            // Fallback to Tour model for JSON fields if translation doesn't have them
            if (empty($sourceValue)) {
                if ($field === 'requirements_json' && !empty($tour->requirements)) {
                    $sourceValue = $tour->requirements;
                } elseif ($field === 'faq_json' && !empty($tour->faqs)) {
                    $sourceValue = $tour->faqs;
                } elseif ($field === 'highlights_json' && !empty($tour->highlights)) {
                    $sourceValue = $tour->highlights;
                } elseif ($field === 'itinerary_json' && !empty($tour->itinerary)) {
                    $sourceValue = $tour->itinerary;
                } elseif ($field === 'included_json' && !empty($tour->included_items)) {
                    $sourceValue = $tour->included_items;
                } elseif ($field === 'excluded_json' && !empty($tour->excluded_items)) {
                    $sourceValue = $tour->excluded_items;
                }
            }

            // Skip if still empty after fallback
            if (empty($sourceValue)) {
                continue;
            }

            // Handle JSON fields (highlights, itinerary, FAQ, etc.)
            if (isset($sections[$field]['is_json']) && $sections[$field]['is_json']) {
                $translations[$field] = $this->translateJsonField($sourceValue, $targetLocale, $sourceLocale, $field);
            } else {
                // Handle special case for slug
                if ($field === 'slug') {
                    $translations[$field] = $this->generateSlug($sourceTranslation->title ?? $tour->title, $targetLocale);
                } else {
                    $translations[$field] = $this->translateField($sourceValue, $targetLocale, $sourceLocale, $field);
                }
            }
        }

        return [
            'translations' => $translations,
            'tokens_used' => $totalTokens,
        ];
    }

    /**
     * Translate JSON field (arrays of items like highlights, FAQs, etc.)
     */
    protected function translateJsonField($jsonData, string $targetLocale, string $sourceLocale, string $fieldName): array
    {
        if (!is_array($jsonData)) {
            return [];
        }

        $translated = [];

        foreach ($jsonData as $item) {
            if (is_array($item)) {
                // Handle structured arrays (FAQs, itinerary, etc.)
                $translatedItem = [];
                foreach ($item as $key => $value) {
                    if (is_string($value) && !empty($value)) {
                        $translatedItem[$key] = $this->translateField($value, $targetLocale, $sourceLocale, $fieldName);
                    } else {
                        $translatedItem[$key] = $value;
                    }
                }
                $translated[] = $translatedItem;
            } elseif (is_string($item)) {
                // Handle simple string arrays (highlights, included, excluded)
                // Keep as simple strings, not wrapped in ['text' => ...]
                $translated[] = $this->translateField($item, $targetLocale, $sourceLocale, $fieldName);
            }
        }

        return $translated;
    }

    /**
     * Generate slug from translated title
     */
    protected function generateSlug(string $title, string $locale): string
    {
        // Translate title first
        $translatedTitle = $this->translateField($title, $locale, 'en', 'title');

        // Generate slug (transliterate to Latin if needed)
        return Str::slug($translatedTitle);
    }

    /**
     * Get system prompt for translation
     */
    protected function getSystemPrompt(string $locale): string
    {
        $localeNames = config('ai-translation.locale_names', []);
        $languageName = $localeNames[$locale] ?? $locale;

        return str_replace('{locale}', $languageName, config('ai-translation.prompts.system'));
    }

    /**
     * Get user prompt for translation
     */
    protected function getUserPrompt(string $content, string $targetLocale, string $sourceLocale, string $section): string
    {
        $localeNames = config('ai-translation.locale_names', []);
        $targetLanguage = $localeNames[$targetLocale] ?? $targetLocale;
        $sourceLanguage = $localeNames[$sourceLocale] ?? $sourceLocale;
        $sectionLabel = config("ai-translation.sections.{$section}.label", $section);

        $template = config('ai-translation.prompts.user_template');

        return str_replace(
            ['{section}', '{source_language}', '{target_language}', '{content}'],
            [$sectionLabel, $sourceLanguage, $targetLanguage, $content],
            $template
        );
    }

    /**
     * Estimate translation cost
     */
    public function estimateCost(int $inputTokens, int $outputTokens): float
    {
        $costs = config('ai-translation.cost_per_1k_tokens');
        $modelCost = $costs[$this->model] ?? $costs['gpt-4-turbo'];

        $inputCost = ($inputTokens / 1000) * $modelCost['input'];
        $outputCost = ($outputTokens / 1000) * $modelCost['output'];

        return round($inputCost + $outputCost, 4);
    }

    /**
     * Rough token count estimate (4 chars ≈ 1 token)
     */
    public function estimateTokens(string $text): int
    {
        return (int) ceil(strlen($text) / 4);
    }

    /**
     * Validate API key
     */
    public function validateApiKey(): bool
    {
        try {
            // Test with a simple request
            $response = $this->client->chat()->create([
                'model' => $this->model,
                'messages' => [
                    ['role' => 'user', 'content' => 'Test'],
                ],
                'max_tokens' => 5,
            ]);

            return isset($response->choices[0]);
        } catch (\Exception $e) {
            Log::error('API key validation failed', ['error' => $e->getMessage()]);
            return false;
        }
    }
}
