<?php

return [
    /*
    |--------------------------------------------------------------------------
    | AI Translation Feature Toggle
    |--------------------------------------------------------------------------
    |
    | Enable or disable AI-powered tour translation feature.
    |
    */
    'enabled' => env('AI_TRANSLATION_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | OpenAI Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for OpenAI API integration.
    | API key is stored encrypted in database via Settings model.
    |
    */
    'openai' => [
        'model' => env('OPENAI_MODEL', 'gpt-4-turbo'),
        'temperature' => 0.3, // Lower = more consistent, higher = more creative
        'max_tokens' => 4000,
    ],

    /*
    |--------------------------------------------------------------------------
    | DeepSeek Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for DeepSeek API integration (OpenAI-compatible).
    | Much cheaper than OpenAI with comparable quality.
    |
    */
    'deepseek' => [
        'model' => env('DEEPSEEK_MODEL', 'deepseek-chat'),
        'base_uri' => 'https://api.deepseek.com',
    ],

    /*
    |--------------------------------------------------------------------------
    | Translation Prompts
    |--------------------------------------------------------------------------
    |
    | System and user prompts for context-aware tourism translation.
    |
    */
    'prompts' => [
        'system' => 'You are a professional tourism translator specializing in {locale}. Your translations should:
- Preserve all HTML tags exactly as they appear (do not modify or remove any HTML)
- Use natural, engaging tourism language appropriate for travel industry
- Maintain the original tone (informative, inviting, professional)
- Keep all numbers, dates, prices, and proper nouns unchanged
- Preserve list structures and formatting
- Use culturally appropriate expressions for the target language

CRITICAL RULES:
- Return ONLY the translated text without any explanations, notes, or additional commentary
- Do NOT add markdown formatting (no **, *, _, etc.)
- Do NOT add new HTML tags that were not in the original
- Do NOT expand or elaborate on the content - translate only what is given
- For FAQ questions: translate the question ONLY, do not include the answer
- For FAQ answers: translate the answer ONLY, do not repeat the question
- Keep translations concise and direct - same length as original',

        'user_template' => 'Translate this tour {section} from {source_language} to {target_language}:

{content}

IMPORTANT: Return ONLY the direct translation. No markdown, no extra formatting, no elaboration.',
    ],

    /*
    |--------------------------------------------------------------------------
    | Section Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for each translatable section including token limits
    | and translation priority.
    |
    */
    'sections' => [
        'title' => [
            'label' => 'Title',
            'max_tokens' => 50,
            'priority' => 1,
        ],
        'slug' => [
            'label' => 'URL Slug',
            'max_tokens' => 50,
            'priority' => 2,
            'transliterate' => true, // Convert to Latin characters
        ],
        'excerpt' => [
            'label' => 'Excerpt',
            'max_tokens' => 200,
            'priority' => 3,
        ],
        'content' => [
            'label' => 'Description',
            'max_tokens' => 2000,
            'priority' => 4,
        ],
        'highlights_json' => [
            'label' => 'Highlights',
            'max_tokens' => 500,
            'priority' => 5,
            'is_json' => true,
        ],
        'itinerary_json' => [
            'label' => 'Itinerary',
            'max_tokens' => 3000,
            'priority' => 6,
            'is_json' => true,
        ],
        'included_json' => [
            'label' => 'Included Items',
            'max_tokens' => 500,
            'priority' => 7,
            'is_json' => true,
        ],
        'excluded_json' => [
            'label' => 'Excluded Items',
            'max_tokens' => 500,
            'priority' => 8,
            'is_json' => true,
        ],
        'faq_json' => [
            'label' => 'FAQs',
            'max_tokens' => 1500,
            'priority' => 9,
            'is_json' => true,
        ],
        'requirements_json' => [
            'label' => 'Requirements',
            'max_tokens' => 400,
            'priority' => 10,
            'is_json' => true,
        ],
        'cancellation_policy' => [
            'label' => 'Cancellation Policy',
            'max_tokens' => 1000,
            'priority' => 11,
        ],
        'meeting_instructions' => [
            'label' => 'Meeting Instructions',
            'max_tokens' => 1000,
            'priority' => 12,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Cost Estimates (per 1,000 tokens)
    |--------------------------------------------------------------------------
    |
    | Pricing for different OpenAI models.
    | Updated as of January 2025.
    |
    */
    'cost_per_1k_tokens' => [
        'gpt-4-turbo' => [
            'input' => 0.01,
            'output' => 0.03,
        ],
        'gpt-4' => [
            'input' => 0.03,
            'output' => 0.06,
        ],
        'gpt-3.5-turbo' => [
            'input' => 0.0005,
            'output' => 0.0015,
        ],
        'deepseek-chat' => [
            'input' => 0.00014,  // $0.14 per 1M tokens
            'output' => 0.00028, // $0.28 per 1M tokens
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    |
    | Prevent abuse and control API costs.
    |
    */
    'rate_limit' => [
        'max_per_hour' => 10, // Max translations per user per hour
        'max_per_day' => 50,  // Max translations per user per day
    ],

    /*
    |--------------------------------------------------------------------------
    | Cost Limits
    |--------------------------------------------------------------------------
    |
    | Alert thresholds for translation costs.
    |
    */
    'cost_limits' => [
        'daily_usd' => 10.00,
        'monthly_usd' => 100.00,
        'alert_threshold' => 0.80, // Alert at 80% of limit
    ],

    /*
    |--------------------------------------------------------------------------
    | Locale Mappings
    |--------------------------------------------------------------------------
    |
    | Map locale codes to full language names for better AI context.
    |
    */
    'locale_names' => [
        'en' => 'English',
        'ru' => 'Russian',
        'uz' => 'Uzbek',
        'fr' => 'French',
        'es' => 'Spanish',
        'de' => 'German',
        'zh' => 'Chinese',
        'ar' => 'Arabic',
    ],
];
