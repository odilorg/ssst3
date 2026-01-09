<?php

namespace App\Services;

/**
 * Standard templates for tour content fields.
 *
 * These templates provide safe, neutral fallback content
 * when fields are not provided in "allowed" fallback mode.
 *
 * IMPORTANT: Templates should be:
 * - Short and neutral
 * - Not AI-generated or hallucinated
 * - Generic enough to apply to any tour
 * - Safe to display publicly
 */
class TourTemplates
{
    /**
     * Standard included items template (v1)
     * Generic items commonly included in tours.
     */
    public static function standardIncludedV1(): array
    {
        return [
            ['text' => 'Professional English-speaking guide'],
            ['text' => 'Transportation as per itinerary'],
            ['text' => 'Entrance fees to sites mentioned'],
            ['text' => 'Bottled water during the tour'],
        ];
    }

    /**
     * Standard excluded items template (v1)
     * Generic items commonly NOT included in tours.
     */
    public static function standardExcludedV1(): array
    {
        return [
            ['text' => 'Meals and beverages (unless specified)'],
            ['text' => 'Personal expenses and souvenirs'],
            ['text' => 'Tips and gratuities'],
            ['text' => 'Travel insurance'],
        ];
    }

    /**
     * Standard requirements template (v1)
     * Generic requirements for tour participants.
     */
    public static function standardRequirementsV1(): array
    {
        return [
            [
                'icon' => 'walking',
                'title' => 'Comfortable footwear',
                'text' => 'Wear comfortable walking shoes',
            ],
            [
                'icon' => 'sun',
                'title' => 'Weather protection',
                'text' => 'Bring sunscreen and hat in summer, warm layers in winter',
            ],
            [
                'icon' => 'camera',
                'title' => 'Camera',
                'text' => 'Bring a camera for photos (optional)',
            ],
        ];
    }

    /**
     * Standard FAQ template (v1)
     * Generic frequently asked questions.
     */
    public static function standardFaqV1(): array
    {
        return [
            [
                'question' => 'What should I bring?',
                'answer' => 'Comfortable shoes, weather-appropriate clothing, camera, and some local currency for personal expenses.',
            ],
            [
                'question' => 'Is the tour suitable for children?',
                'answer' => 'Please contact us for specific age recommendations for this tour.',
            ],
            [
                'question' => 'What is the cancellation policy?',
                'answer' => 'Free cancellation up to 24 hours before the tour. Please see our terms for details.',
            ],
        ];
    }

    /**
     * Standard highlights template (v1)
     * This is intentionally empty as highlights should always be tour-specific.
     * In strict mode, highlights_json is required.
     * In allowed mode, we don't template highlights - they should be provided.
     */
    public static function standardHighlightsV1(): array
    {
        return [
            ['text' => 'Explore local culture and heritage'],
            ['text' => 'Experience authentic local hospitality'],
            ['text' => 'Professional guided experience'],
        ];
    }

    /**
     * Get template by name.
     *
     * @param string $name Template name (e.g., 'included', 'excluded', 'requirements', 'faq', 'highlights')
     * @param string $version Template version (default 'v1')
     * @return array|null Template data or null if not found
     */
    public static function get(string $name, string $version = 'v1'): ?array
    {
        $methodName = 'standard' . ucfirst($name) . ucfirst($version);

        if (method_exists(self::class, $methodName)) {
            return self::$methodName();
        }

        return null;
    }

    /**
     * Get all available template names.
     */
    public static function availableTemplates(): array
    {
        return [
            'included' => ['v1'],
            'excluded' => ['v1'],
            'requirements' => ['v1'],
            'faq' => ['v1'],
            'highlights' => ['v1'],
        ];
    }
}
