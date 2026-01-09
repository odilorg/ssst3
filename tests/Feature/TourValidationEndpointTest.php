<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TourValidationEndpointTest extends TestCase
{
    /**
     * Test validation endpoint requires API key
     */
    public function test_validation_endpoint_requires_api_key(): void
    {
        $response = $this->postJson('/api/internal/tours/validate', []);

        $response->assertStatus(401)
            ->assertJson([
                'ok' => false,
            ])
            ->assertJsonPath('errors.0.field', 'auth');
    }

    /**
     * Test validation endpoint rejects invalid API key
     */
    public function test_validation_endpoint_rejects_invalid_api_key(): void
    {
        $response = $this->postJson('/api/internal/tours/validate', [], [
            'X-Internal-Api-Key' => 'wrong-key',
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'ok' => false,
            ]);
    }

    /**
     * Test validation fails when slug is missing
     */
    public function test_validation_fails_when_slug_missing(): void
    {
        config(['services.internal_api.key' => 'test-key']);

        $response = $this->postJson('/api/internal/tours/validate', [
            'tour' => [],
            'translations' => [
                'en' => [
                    'locale' => 'en',
                    'title' => 'Test Tour',
                    'itinerary_json' => [
                        ['day' => 1, 'title' => 'Day 1'],
                    ],
                ],
            ],
        ], [
            'X-Internal-Api-Key' => 'test-key',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'ok' => false,
            ])
            ->assertJsonPath('errors.0.field', 'tour.slug');
    }

    /**
     * Test validation fails when English translation is missing
     */
    public function test_validation_fails_when_english_translation_missing(): void
    {
        config(['services.internal_api.key' => 'test-key']);

        $response = $this->postJson('/api/internal/tours/validate', [
            'tour' => [
                'slug' => 'test-tour',
            ],
            'translations' => [
                'ru' => [
                    'locale' => 'ru',
                    'title' => 'Тестовый тур',
                    'itinerary_json' => [
                        ['day' => 1, 'title' => 'День 1'],
                    ],
                ],
            ],
        ], [
            'X-Internal-Api-Key' => 'test-key',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'ok' => false,
            ]);
    }

    /**
     * Test validation fails when English title is missing
     */
    public function test_validation_fails_when_english_title_missing(): void
    {
        config(['services.internal_api.key' => 'test-key']);

        $response = $this->postJson('/api/internal/tours/validate', [
            'tour' => [
                'slug' => 'test-tour',
            ],
            'translations' => [
                'en' => [
                    'locale' => 'en',
                    'itinerary_json' => [
                        ['day' => 1, 'title' => 'Day 1'],
                    ],
                ],
            ],
        ], [
            'X-Internal-Api-Key' => 'test-key',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'ok' => false,
            ]);
    }

    /**
     * Test validation fails when itinerary is missing
     */
    public function test_validation_fails_when_itinerary_missing(): void
    {
        config(['services.internal_api.key' => 'test-key']);

        $response = $this->postJson('/api/internal/tours/validate', [
            'tour' => [
                'slug' => 'test-tour',
            ],
            'translations' => [
                'en' => [
                    'locale' => 'en',
                    'title' => 'Test Tour',
                ],
            ],
        ], [
            'X-Internal-Api-Key' => 'test-key',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'ok' => false,
            ]);
    }

    /**
     * Test validation fails when itinerary days are not sequential
     */
    public function test_validation_fails_when_itinerary_days_not_sequential(): void
    {
        config(['services.internal_api.key' => 'test-key']);

        $response = $this->postJson('/api/internal/tours/validate', [
            'tour' => [
                'slug' => 'test-tour',
            ],
            'translations' => [
                'en' => [
                    'locale' => 'en',
                    'title' => 'Test Tour',
                    'itinerary_json' => [
                        ['day' => 1, 'title' => 'Day 1'],
                        ['day' => 3, 'title' => 'Day 3'], // Missing day 2
                    ],
                ],
            ],
        ], [
            'X-Internal-Api-Key' => 'test-key',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'ok' => false,
            ]);
    }

    /**
     * Test validation fails when itinerary days don't start at 1
     */
    public function test_validation_fails_when_itinerary_days_dont_start_at_one(): void
    {
        config(['services.internal_api.key' => 'test-key']);

        $response = $this->postJson('/api/internal/tours/validate', [
            'tour' => [
                'slug' => 'test-tour',
            ],
            'translations' => [
                'en' => [
                    'locale' => 'en',
                    'title' => 'Test Tour',
                    'itinerary_json' => [
                        ['day' => 2, 'title' => 'Day 2'],
                        ['day' => 3, 'title' => 'Day 3'],
                    ],
                ],
            ],
        ], [
            'X-Internal-Api-Key' => 'test-key',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'ok' => false,
            ]);
    }

    /**
     * Test validation fails with invalid slug format
     */
    public function test_validation_fails_with_invalid_slug_format(): void
    {
        config(['services.internal_api.key' => 'test-key']);

        $response = $this->postJson('/api/internal/tours/validate', [
            'tour' => [
                'slug' => 'Invalid Slug With Spaces',
            ],
            'translations' => [
                'en' => [
                    'locale' => 'en',
                    'title' => 'Test Tour',
                    'itinerary_json' => [
                        ['day' => 1, 'title' => 'Day 1'],
                    ],
                ],
            ],
        ], [
            'X-Internal-Api-Key' => 'test-key',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'ok' => false,
            ]);
    }

    /**
     * Test validation passes with valid minimal payload
     */
    public function test_validation_passes_with_valid_minimal_payload(): void
    {
        config(['services.internal_api.key' => 'test-key']);

        $response = $this->postJson('/api/internal/tours/validate', [
            'tour' => [
                'slug' => 'test-tour-samarkand',
            ],
            'translations' => [
                'en' => [
                    'locale' => 'en',
                    'title' => 'Test Tour to Samarkand',
                    'itinerary_json' => [
                        ['day' => 1, 'title' => 'Day 1: Arrival'],
                    ],
                ],
            ],
        ], [
            'X-Internal-Api-Key' => 'test-key',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'ok' => true,
            ])
            ->assertJsonPath('validated_locales', ['en']);
    }

    /**
     * Test validation passes with full payload including all JSON sections
     */
    public function test_validation_passes_with_full_payload(): void
    {
        config(['services.internal_api.key' => 'test-key']);

        $response = $this->postJson('/api/internal/tours/validate', [
            'schema_version' => '1.0',
            'tour' => [
                'slug' => 'samarkand-5-day-tour',
                'duration_days' => 5,
                'duration_text' => '5 Days / 4 Nights',
                'tour_type' => 'private_only',
                'is_active' => true,
                'supports_private' => true,
                'supports_group' => false,
                'private_base_price' => 450.00,
                'currency' => 'USD',
                'show_price' => true,
                'minimum_advance_days' => 45,
                'min_booking_hours' => 48,
                'cancellation_hours' => 72,
            ],
            'translations' => [
                'en' => [
                    'locale' => 'en',
                    'title' => 'Samarkand 5-Day Silk Road Adventure',
                    'slug' => 'samarkand-5-day-silk-road-adventure',
                    'excerpt' => 'Discover the ancient wonders of the Silk Road...',
                    'content' => '<p>Full tour description here...</p>',
                    'seo_title' => 'Samarkand Tour | Jahongir Travel',
                    'seo_description' => 'Experience the magic of Samarkand...',
                    'highlights_json' => [
                        ['text' => 'Visit the magnificent Registan Square'],
                        ['text' => 'Explore the Shah-i-Zinda necropolis'],
                        ['text' => 'Traditional Uzbek cooking class'],
                    ],
                    'itinerary_json' => [
                        [
                            'day' => 1,
                            'title' => 'Arrival in Samarkand',
                            'description' => 'Welcome to Uzbekistan!',
                            'activities' => [
                                ['time' => '14:00', 'title' => 'Airport pickup'],
                                ['time' => '16:00', 'title' => 'Hotel check-in'],
                            ],
                        ],
                        [
                            'day' => 2,
                            'title' => 'Registan Square',
                            'description' => 'Full day exploring Registan.',
                        ],
                        [
                            'day' => 3,
                            'title' => 'Shah-i-Zinda',
                            'description' => 'Visit the necropolis.',
                        ],
                        [
                            'day' => 4,
                            'title' => 'Ulugbek Observatory',
                            'description' => 'Science and history day.',
                        ],
                        [
                            'day' => 5,
                            'title' => 'Departure',
                            'description' => 'Transfer to airport.',
                        ],
                    ],
                    'included_json' => [
                        ['text' => 'Professional English-speaking guide'],
                        ['text' => 'Air-conditioned transport'],
                        ['text' => '4 nights accommodation'],
                    ],
                    'excluded_json' => [
                        ['text' => 'International flights'],
                        ['text' => 'Personal expenses'],
                    ],
                    'faq_json' => [
                        [
                            'question' => 'What should I pack?',
                            'answer' => 'Comfortable walking shoes and sun protection.',
                        ],
                    ],
                    'requirements_json' => [
                        [
                            'icon' => 'walking',
                            'title' => 'Moderate walking required',
                            'text' => 'Tour involves walking 5-8km per day.',
                        ],
                    ],
                    'cancellation_policy' => 'Free cancellation up to 72 hours before tour start.',
                    'meeting_instructions' => 'Meet at hotel lobby at 08:00.',
                ],
                'ru' => [
                    'locale' => 'ru',
                    'title' => 'Самарканд: 5-дневный тур по Шёлковому пути',
                    'slug' => 'samarkand-5-dnevnyj-tur',
                    'itinerary_json' => [
                        ['day' => 1, 'title' => 'Прибытие в Самарканд'],
                        ['day' => 2, 'title' => 'Площадь Регистан'],
                        ['day' => 3, 'title' => 'Шахи-Зинда'],
                        ['day' => 4, 'title' => 'Обсерватория Улугбека'],
                        ['day' => 5, 'title' => 'Отъезд'],
                    ],
                ],
            ],
        ], [
            'X-Internal-Api-Key' => 'test-key',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'ok' => true,
                'schema_version' => '1.0',
            ])
            ->assertJsonPath('validated_locales', ['en', 'ru']);
    }

    /**
     * Test highlights_json must be array if provided
     */
    public function test_highlights_must_be_array(): void
    {
        config(['services.internal_api.key' => 'test-key']);

        $response = $this->postJson('/api/internal/tours/validate', [
            'tour' => [
                'slug' => 'test-tour',
            ],
            'translations' => [
                'en' => [
                    'locale' => 'en',
                    'title' => 'Test Tour',
                    'itinerary_json' => [
                        ['day' => 1, 'title' => 'Day 1'],
                    ],
                    'highlights_json' => 'not an array',
                ],
            ],
        ], [
            'X-Internal-Api-Key' => 'test-key',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'ok' => false,
            ]);
    }

    /**
     * Test cancellation_policy can be null
     */
    public function test_cancellation_policy_can_be_null(): void
    {
        config(['services.internal_api.key' => 'test-key']);

        $response = $this->postJson('/api/internal/tours/validate', [
            'tour' => [
                'slug' => 'test-tour',
            ],
            'translations' => [
                'en' => [
                    'locale' => 'en',
                    'title' => 'Test Tour',
                    'itinerary_json' => [
                        ['day' => 1, 'title' => 'Day 1'],
                    ],
                    'cancellation_policy' => null,
                    'meeting_instructions' => null,
                ],
            ],
        ], [
            'X-Internal-Api-Key' => 'test-key',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'ok' => true,
            ]);
    }
}
