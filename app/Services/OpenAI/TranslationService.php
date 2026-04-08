<?php

namespace App\Services\OpenAI;

use App\Models\BlogPost;
use App\Models\BlogPostTranslation;
use App\Models\City;
use App\Models\CityTranslation;
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
    protected string $model;
    protected float $temperature;
    protected string $provider;

    /** Accumulated input tokens across the current translate* call. */
    protected int $sessionInputTokens = 0;
    /** Accumulated output tokens across the current translate* call. */
    protected int $sessionOutputTokens = 0;

    public function __construct()
    {
        $apiKey = Setting::get('ai_translation_api_key');
        $this->provider = Setting::get('ai_translation_provider', 'openai');

        if (!$apiKey) {
            throw new \Exception('API key not configured. Please set it in AI Translation Settings.');
        }

        // Support different AI providers (OpenAI, DeepSeek, etc.)
        if ($this->provider === 'deepseek') {
            $this->client = OpenAI::factory()
                ->withApiKey($apiKey)
                ->withBaseUri('https://api.deepseek.com')
                ->make();
            $this->model = config('ai-translation.deepseek.model', 'deepseek-chat');
        } else {
            $this->client = OpenAI::client($apiKey);
            $this->model = config('ai-translation.openai.model', 'gpt-4-turbo');
        }

        // Use the temperature configured for the active provider.
        $this->temperature = (float) config("ai-translation.{$this->provider}.temperature",
            config('ai-translation.openai.temperature', 0.3));
    }

    /**
     * The actual model/provider being used — for accurate log entries.
     */
    public function getModel(): string
    {
        return $this->model;
    }

    /**
     * Total tokens used in the current translate* session.
     */
    public function getSessionTokensUsed(): int
    {
        return $this->sessionInputTokens + $this->sessionOutputTokens;
    }

    private function resetSessionTokens(): void
    {
        $this->sessionInputTokens = 0;
        $this->sessionOutputTokens = 0;
    }

    /**
     * Translate a single field with retry logic for rate limits.
     */
    public function translateField(string $text, string $targetLocale, string $sourceLocale = 'en', string $section = 'content'): string
    {
        if (empty($text)) {
            return '';
        }

        $systemPrompt = $this->getSystemPrompt($targetLocale);
        $userPrompt   = $this->getUserPrompt($text, $targetLocale, $sourceLocale, $section);

        // Pass per-section max_tokens if configured.
        $maxTokens = config("ai-translation.sections.{$section}.max_tokens");

        $requestParams = [
            'model'       => $this->model,
            'messages'    => [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user',   'content' => $userPrompt],
            ],
            'temperature' => $this->temperature,
        ];

        if ($maxTokens) {
            $requestParams['max_tokens'] = (int) $maxTokens;
        }

        $maxRetries = 3;
        $retryDelay = 2; // seconds

        for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
            try {
                $response = $this->client->chat()->create($requestParams);

                // Accumulate actual token usage from the API response.
                $this->sessionInputTokens  += $response->usage->promptTokens     ?? 0;
                $this->sessionOutputTokens += $response->usage->completionTokens ?? 0;

                // Add small delay between API calls to avoid rate limits.
                usleep(300000); // 300 ms

                $content = trim($response->choices[0]->message->content);

                // Guard: reject empty or meta-text responses.
                $this->assertValidTranslation($content, $section);

                return $content;

            } catch (\Exception $e) {
                $isRateLimit = str_contains($e->getMessage(), 'rate limit') ||
                               str_contains($e->getMessage(), 'Rate limit') ||
                               str_contains($e->getMessage(), '429');

                if ($isRateLimit && $attempt < $maxRetries) {
                    Log::warning('AI Translation rate limit hit, retrying...', [
                        'attempt' => $attempt,
                        'delay'   => $retryDelay * $attempt,
                    ]);
                    sleep($retryDelay * $attempt); // Exponential backoff
                    continue;
                }

                Log::error('AI Translation failed for field', [
                    'section'        => $section,
                    'target_locale'  => $targetLocale,
                    'error'          => $e->getMessage(),
                    'attempt'        => $attempt,
                ]);
                throw $e;
            }
        }

        throw new \Exception('Translation failed after ' . $maxRetries . ' attempts');
    }

    /**
     * Reject empty or obvious meta-text AI responses to avoid storing garbage.
     */
    private function assertValidTranslation(string $content, string $section): void
    {
        if ($content === '') {
            throw new \Exception("AI returned empty response for section '{$section}'");
        }

        // Only test short responses — long content is unlikely to be meta-text.
        if (strlen($content) < 200) {
            $lower = strtolower($content);
            $metaPatterns = ["i'm sorry", "i cannot", "i can't", 'as an ai', 'as a language model', 'sure, here'];

            foreach ($metaPatterns as $phrase) {
                if (str_contains($lower, $phrase)) {
                    throw new \Exception(
                        "AI returned meta-text instead of translation for '{$section}': " . substr($content, 0, 100)
                    );
                }
            }
        }
    }

    /**
     * Translate entire tour to target locale.
     */
    public function translateTour(Tour $tour, string $targetLocale, array $sectionsToTranslate = []): array
    {
        $this->resetSessionTokens();

        $sourceLocale      = 'en';
        $sourceTranslation = $tour->translations()->where('locale', $sourceLocale)->first();

        if (!$sourceTranslation) {
            throw new \Exception('Source translation (English) not found for this tour.');
        }

        $sections = config('ai-translation.sections');

        if (empty($sectionsToTranslate)) {
            $sectionsToTranslate = array_keys($sections);
        }

        $translations = [];

        foreach ($sectionsToTranslate as $field) {
            if (!isset($sections[$field])) {
                continue;
            }

            $sourceValue = $sourceTranslation->{$field};

            // Fallback to Tour model for JSON fields if translation doesn't have them.
            if (empty($sourceValue)) {
                if ($field === 'requirements_json' && !empty($tour->requirements)) {
                    $sourceValue = $tour->requirements;
                } elseif ($field === 'faq_json' && $tour->faqs && $tour->faqs->isNotEmpty()) {
                    $sourceValue = $tour->faqs->map(fn ($item) => [
                        'question' => $item->question_text ?? $item->question ?? '',
                        'answer'   => $item->answer_text  ?? $item->answer   ?? '',
                    ])->toArray();
                } elseif ($field === 'highlights_json' && !empty($tour->highlights)) {
                    $sourceValue = $tour->highlights;
                } elseif ($field === 'itinerary_json' && $tour->topLevelItems && $tour->topLevelItems->isNotEmpty()) {
                    $sourceValue = $tour->topLevelItems->map(fn ($item) => [
                        'title'            => $item->title            ?? '',
                        'description'      => $item->description      ?? '',
                        'duration_minutes' => $item->duration_minutes ?? null,
                    ])->toArray();
                } elseif ($field === 'included_json' && !empty($tour->included_items)) {
                    $sourceValue = $tour->included_items;
                } elseif ($field === 'excluded_json' && !empty($tour->excluded_items)) {
                    $sourceValue = $tour->excluded_items;
                }
            }

            if (empty($sourceValue)) {
                continue;
            }

            if (isset($sections[$field]['is_json']) && $sections[$field]['is_json']) {
                $translations[$field] = $this->translateJsonField($sourceValue, $targetLocale, $sourceLocale, $field);
            } elseif ($field === 'slug') {
                $translations[$field] = $this->generateSlug(
                    $sourceTranslation->title ?? $tour->title,
                    $targetLocale,
                    $tour->id
                );
            } else {
                $translations[$field] = $this->translateField($sourceValue, $targetLocale, $sourceLocale, $field);
            }
        }

        return [
            'translations'   => $translations,
            'tokens_used'    => $this->getSessionTokensUsed(),
            'tokens_input'   => $this->sessionInputTokens,
            'tokens_output'  => $this->sessionOutputTokens,
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
                $translated[] = $this->translateField($item, $targetLocale, $sourceLocale, $fieldName);
            }
        }

        return $translated;
    }

    /**
     * Generate unique slug from translated title.
     *
     * Fallback chain:
     * 1. Translate title → slug from that
     * 2. If non-Latin script produces empty slug → fall back to English title slug
     * 3. If still empty → guaranteed unique "tour-{id}-{locale}"
     * 4. Append counter/id to ensure DB uniqueness
     *
     * The translation step is wrapped in try-catch so a translation API
     * failure cannot propagate empty-slug poison into the database.
     */
    protected function generateSlug(string $title, string $locale, int $tourId): string
    {
        // Attempt to translate the title; fall back silently on any failure.
        try {
            $translatedTitle = $this->translateField($title, $locale, 'en', 'title');
            $baseSlug        = Str::slug($translatedTitle);
        } catch (\Exception $e) {
            Log::warning('Slug title translation failed, using English fallback', [
                'locale' => $locale,
                'error'  => $e->getMessage(),
            ]);
            $baseSlug = '';
        }

        // Non-Latin scripts (ja, zh, ko, ar, …) produce empty slugs from Str::slug.
        if (empty($baseSlug)) {
            $baseSlug = Str::slug($title); // English title
        }

        // Last resort: deterministic, locale-scoped, guaranteed non-empty.
        if (empty($baseSlug)) {
            $baseSlug = "tour-{$tourId}-{$locale}";
        }

        // Ensure uniqueness within (locale, slug), excluding the current tour.
        $slug    = $baseSlug;
        $counter = 2;

        while (TourTranslation::where('locale', $locale)
            ->where('slug', $slug)
            ->where('tour_id', '!=', $tourId)
            ->exists()) {
            $slug = $counter <= 10
                ? "{$baseSlug}-{$counter}"
                : "{$baseSlug}-{$tourId}-{$locale}"; // guaranteed unique after 10 attempts
            $counter++;
        }

        return $slug;
    }

    /**
     * Translate entire blog post to target locale.
     */
    public function translateBlogPost(BlogPost $blogPost, string $targetLocale, ?int $translationId = null): array
    {
        $this->resetSessionTokens();

        $sourceLocale      = 'en';
        $sourceTranslation = $blogPost->translations()->where('locale', $sourceLocale)->first();

        $source       = $sourceTranslation ?? $blogPost;
        $translations = [];

        foreach (['title', 'excerpt', 'content', 'seo_title', 'seo_description'] as $field) {
            $sourceValue = $source->{$field};

            if (empty($sourceValue)) {
                continue;
            }

            $translations[$field] = $this->translateField($sourceValue, $targetLocale, $sourceLocale, $field);
        }

        $englishTitle = $source->title ?? $blogPost->title;

        $translations['slug'] = $this->generateBlogPostSlug(
            $translations['title'] ?? $englishTitle,
            $englishTitle,
            $targetLocale,
            $blogPost->id,
            $translationId
        );

        return [
            'translations'  => $translations,
            'tokens_used'   => $this->getSessionTokensUsed(),
            'tokens_input'  => $this->sessionInputTokens,
            'tokens_output' => $this->sessionOutputTokens,
        ];
    }

    /**
     * Generate unique blog post slug with non-Latin fallback.
     */
    protected function generateBlogPostSlug(string $translatedTitle, string $englishTitle, string $locale, int $blogPostId, ?int $translationId = null): string
    {
        $baseSlug = Str::slug($translatedTitle);

        if (empty($baseSlug)) {
            $baseSlug = Str::slug($englishTitle);
        }

        if (empty($baseSlug)) {
            $baseSlug = "blog-post-{$blogPostId}-{$locale}";
        }

        $slug    = $baseSlug;
        $counter = 2;

        while (BlogPostTranslation::where('locale', $locale)
            ->where('slug', $slug)
            ->when($translationId, fn ($q) => $q->where('id', '!=', $translationId))
            ->exists()) {
            $slug = $counter <= 10
                ? "{$baseSlug}-{$counter}"
                : "{$baseSlug}-{$blogPostId}-{$locale}";
            $counter++;
        }

        return $slug;
    }

    /**
     * Get system prompt for translation.
     */
    protected function getSystemPrompt(string $locale): string
    {
        $localeNames  = config('ai-translation.locale_names', []);
        $languageName = $localeNames[$locale] ?? $locale;

        return str_replace('{locale}', $languageName, config('ai-translation.prompts.system'));
    }

    /**
     * Get user prompt for translation.
     */
    protected function getUserPrompt(string $content, string $targetLocale, string $sourceLocale, string $section): string
    {
        $localeNames    = config('ai-translation.locale_names', []);
        $targetLanguage = $localeNames[$targetLocale] ?? $targetLocale;
        $sourceLanguage = $localeNames[$sourceLocale] ?? $sourceLocale;
        $sectionLabel   = config("ai-translation.sections.{$section}.label", $section);

        $template = config('ai-translation.prompts.user_template');

        return str_replace(
            ['{section}', '{source_language}', '{target_language}', '{content}'],
            [$sectionLabel, $sourceLanguage, $targetLanguage, $content],
            $template
        );
    }

    /**
     * Estimate translation cost from actual input/output token counts.
     */
    public function estimateCost(int $inputTokens, int $outputTokens): float
    {
        $costs     = config('ai-translation.cost_per_1k_tokens');
        // Use gpt-4-turbo as explicit fallback so unknown models don't silently under-estimate.
        $modelCost = $costs[$this->model] ?? $costs['gpt-4-turbo'];

        $inputCost  = ($inputTokens  / 1000) * $modelCost['input'];
        $outputCost = ($outputTokens / 1000) * $modelCost['output'];

        return round($inputCost + $outputCost, 4);
    }

    /**
     * Rough token count estimate (4 chars ≈ 1 token).
     */
    public function estimateTokens(string $text): int
    {
        return (int) ceil(strlen($text) / 4);
    }

    /**
     * Translate entire city to target locale.
     */
    public function translateCity(City $city, string $targetLocale): array
    {
        $this->resetSessionTokens();

        $sourceLocale      = 'en';
        $sourceTranslation = $city->translations()->where('locale', $sourceLocale)->first();

        $source       = $sourceTranslation ?? $city;
        $translations = [];

        foreach (['name', 'tagline', 'short_description', 'description', 'seo_title', 'seo_description'] as $field) {
            $sourceValue = $source->{$field};

            if (empty($sourceValue)) {
                continue;
            }

            $translations[$field] = $this->translateField($sourceValue, $targetLocale, $sourceLocale, $field);
        }

        $englishName = $source->name ?? $city->name;

        $translations['slug'] = $this->generateCitySlug(
            $translations['name'] ?? $englishName,
            $englishName,
            $targetLocale,
            $city->id
        );

        return [
            'translations'  => $translations,
            'tokens_used'   => $this->getSessionTokensUsed(),
            'tokens_input'  => $this->sessionInputTokens,
            'tokens_output' => $this->sessionOutputTokens,
        ];
    }

    /**
     * Generate unique city slug with non-Latin fallback.
     */
    protected function generateCitySlug(string $translatedName, string $englishName, string $locale, int $cityId): string
    {
        $baseSlug = Str::slug($translatedName);

        if (empty($baseSlug)) {
            $baseSlug = Str::slug($englishName);
        }

        if (empty($baseSlug)) {
            $baseSlug = "city-{$cityId}-{$locale}";
        }

        $slug    = $baseSlug;
        $counter = 2;

        while (CityTranslation::where('locale', $locale)
            ->where('slug', $slug)
            ->where('city_id', '!=', $cityId)
            ->exists()) {
            $slug = $counter <= 10
                ? "{$baseSlug}-{$counter}"
                : "{$baseSlug}-{$cityId}-{$locale}";
            $counter++;
        }

        return $slug;
    }

    /**
     * Check rate limits for a user. Throws exception if exceeded.
     */
    public function checkRateLimits(int $userId): void
    {
        $maxPerHour = config('ai-translation.rate_limit.max_per_hour', 10);
        $maxPerDay  = config('ai-translation.rate_limit.max_per_day', 50);

        $hourlyCount = TranslationLog::where('user_id', $userId)
            ->where('created_at', '>=', now()->subHour())
            ->count();

        if ($hourlyCount >= $maxPerHour) {
            throw new \Exception("Rate limit exceeded: {$maxPerHour} translations per hour. Please wait before translating again.");
        }

        $dailyCount = TranslationLog::where('user_id', $userId)
            ->whereDate('created_at', today())
            ->count();

        if ($dailyCount >= $maxPerDay) {
            throw new \Exception("Daily limit exceeded: {$maxPerDay} translations per day. Please try again tomorrow.");
        }
    }

    /**
     * Check cost limits. Throws exception if exceeded.
     */
    public function checkCostLimits(): void
    {
        $dailyLimit   = config('ai-translation.cost_limits.daily_usd', 10.00);
        $monthlyLimit = config('ai-translation.cost_limits.monthly_usd', 100.00);

        $dailyCost = TranslationLog::getTotalCost('day');
        if ($dailyCost >= $dailyLimit) {
            throw new \Exception("Daily cost limit reached (\${$dailyLimit}). Translation paused until tomorrow.");
        }

        $monthlyCost = TranslationLog::getTotalCost('month');
        if ($monthlyCost >= $monthlyLimit) {
            throw new \Exception("Monthly cost limit reached (\${$monthlyLimit}). Contact admin to increase the limit.");
        }
    }

    /**
     * Estimate cost for translating content based on character count and current model.
     */
    public function estimateCostForContent(string $content): float
    {
        $inputTokens  = $this->estimateTokens($content);
        $outputTokens = $inputTokens; // Output roughly equals input for translations

        return $this->estimateCost($inputTokens, $outputTokens);
    }

    /**
     * Validate API key.
     */
    public function validateApiKey(): bool
    {
        try {
            $response = $this->client->chat()->create([
                'model'      => $this->model,
                'messages'   => [['role' => 'user', 'content' => 'Test']],
                'max_tokens' => 5,
            ]);

            return isset($response->choices[0]);
        } catch (\Exception $e) {
            Log::error('API key validation failed', ['error' => $e->getMessage()]);
            return false;
        }
    }
}
