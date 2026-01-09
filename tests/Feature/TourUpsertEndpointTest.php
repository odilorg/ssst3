<?php

namespace Tests\Feature;

use App\Models\Tour;
use App\Models\TourTranslation;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class TourUpsertEndpointTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        config(['services.internal_api.key' => 'test-key']);

        // Clean up any test tours from previous runs
        Tour::where('slug', 'like', 'test-%')->forceDelete();
    }

    protected function tearDown(): void
    {
        // Additional cleanup
        Tour::where('slug', 'like', 'test-%')->forceDelete();
        parent::tearDown();
    }

    /**
     * Test upsert endpoint requires API key
     */
    public function test_upsert_endpoint_requires_api_key(): void
    {
        $response = $this->postJson('/api/internal/tours/upsert', []);

        $response->assertStatus(401)
            ->assertJson(['ok' => false]);
    }

    /**
     * Test creating a new tour via upsert
     */
    public function test_upsert_creates_new_tour(): void
    {
        $payload = [
            'fallback_mode' => 'allowed', // Use allowed mode to auto-fill templates
            'tour' => [
                'slug' => 'test-new-tour-create',
                'duration_days' => 3,
                'tour_type' => 'private_only',
                'is_active' => true,
            ],
            'translations' => [
                'en' => [
                    'locale' => 'en',
                    'title' => 'Test New Tour',
                    'excerpt' => 'A wonderful test tour',
                    'itinerary_json' => [
                        ['day' => 1, 'title' => 'Day One'],
                        ['day' => 2, 'title' => 'Day Two'],
                        ['day' => 3, 'title' => 'Day Three'],
                    ],
                ],
            ],
        ];

        $response = $this->postJson('/api/internal/tours/upsert', $payload, [
            'X-Internal-Api-Key' => 'test-key',
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'ok' => true,
                'slug' => 'test-new-tour-create',
                'action' => 'created',
            ])
            ->assertJsonStructure([
                'ok',
                'tour_id',
                'slug',
                'url',
                'action',
                'warnings',
            ]);

        // Verify in database
        $tour = Tour::where('slug', 'test-new-tour-create')->first();
        $this->assertNotNull($tour);
        $this->assertEquals(3, $tour->duration_days);
        $this->assertEquals('private_only', $tour->tour_type);
    }

    /**
     * Test updating an existing tour via upsert
     */
    public function test_upsert_updates_existing_tour(): void
    {
        // First create a tour
        $tour = Tour::create([
            'slug' => 'test-existing-tour',
            'duration_days' => 2,
            'tour_type' => 'private_only',
            'is_active' => false,
            'price_per_person' => 100.00,
        ]);

        TourTranslation::create([
            'tour_id' => $tour->id,
            'locale' => 'en',
            'title' => 'Original Title',
            'itinerary_json' => [['day' => 1, 'title' => 'Original Day 1']],
        ]);

        // Now upsert with updated data
        $payload = [
            'fallback_mode' => 'allowed',
            'tour' => [
                'slug' => 'test-existing-tour',
                'duration_days' => 5,
                'is_active' => true,
            ],
            'translations' => [
                'en' => [
                    'locale' => 'en',
                    'title' => 'Updated Title',
                    'excerpt' => 'Updated excerpt',
                    'itinerary_json' => [
                        ['day' => 1, 'title' => 'Updated Day 1'],
                        ['day' => 2, 'title' => 'Day 2'],
                        ['day' => 3, 'title' => 'Day 3'],
                        ['day' => 4, 'title' => 'Day 4'],
                        ['day' => 5, 'title' => 'Day 5'],
                    ],
                ],
            ],
        ];

        $response = $this->postJson('/api/internal/tours/upsert', $payload, [
            'X-Internal-Api-Key' => 'test-key',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'ok' => true,
                'slug' => 'test-existing-tour',
                'action' => 'updated',
                'tour_id' => $tour->id,
            ]);

        // Verify tour was updated
        $tour->refresh();
        $this->assertEquals(5, $tour->duration_days);
        $this->assertTrue((bool)$tour->is_active);

        // Verify translation was updated
        $translation = $tour->translations()->where('locale', 'en')->first();
        $this->assertEquals('Updated Title', $translation->title);
    }

    /**
     * Test upsert is idempotent - calling twice produces same result
     */
    public function test_upsert_is_idempotent(): void
    {
        $payload = [
            'fallback_mode' => 'allowed',
            'tour' => [
                'slug' => 'test-idempotent-tour',
                'duration_days' => 4,
                'tour_type' => 'group_only',
            ],
            'translations' => [
                'en' => [
                    'locale' => 'en',
                    'title' => 'Idempotent Tour',
                    'itinerary_json' => [
                        ['day' => 1, 'title' => 'Day 1'],
                        ['day' => 2, 'title' => 'Day 2'],
                        ['day' => 3, 'title' => 'Day 3'],
                        ['day' => 4, 'title' => 'Day 4'],
                    ],
                ],
            ],
        ];

        // First call - creates
        $response1 = $this->postJson('/api/internal/tours/upsert', $payload, [
            'X-Internal-Api-Key' => 'test-key',
        ]);

        $response1->assertStatus(201)
            ->assertJson(['action' => 'created']);

        $tourId = $response1->json('tour_id');

        // Second call - updates (same data)
        $response2 = $this->postJson('/api/internal/tours/upsert', $payload, [
            'X-Internal-Api-Key' => 'test-key',
        ]);

        $response2->assertStatus(200)
            ->assertJson([
                'action' => 'updated',
                'tour_id' => $tourId,
            ]);

        // Should still be only one tour with this slug
        $this->assertEquals(1, Tour::where('slug', 'test-idempotent-tour')->count());
    }

    /**
     * Test upsert with multiple translations
     */
    public function test_upsert_with_multiple_translations(): void
    {
        $payload = [
            'fallback_mode' => 'allowed',
            'tour' => [
                'slug' => 'test-multilang-tour',
                'duration_days' => 2,
            ],
            'translations' => [
                'en' => [
                    'locale' => 'en',
                    'title' => 'English Title',
                    'itinerary_json' => [
                        ['day' => 1, 'title' => 'Day 1'],
                        ['day' => 2, 'title' => 'Day 2'],
                    ],
                ],
                'ru' => [
                    'locale' => 'ru',
                    'title' => 'Russian Title',
                    'itinerary_json' => [
                        ['day' => 1, 'title' => 'Day 1 RU'],
                        ['day' => 2, 'title' => 'Day 2 RU'],
                    ],
                ],
            ],
        ];

        $response = $this->postJson('/api/internal/tours/upsert', $payload, [
            'X-Internal-Api-Key' => 'test-key',
        ]);

        $response->assertStatus(201)
            ->assertJson(['ok' => true]);

        $tour = Tour::where('slug', 'test-multilang-tour')->first();

        // Verify both translations exist
        $this->assertEquals(2, $tour->translations()->count());
    }

    /**
     * Test upsert saves JSON fields correctly
     */
    public function test_upsert_saves_json_fields_correctly(): void
    {
        $payload = [
            'fallback_mode' => 'allowed',
            'tour' => [
                'slug' => 'test-json-fields-tour',
                'duration_days' => 1,
            ],
            'translations' => [
                'en' => [
                    'locale' => 'en',
                    'title' => 'JSON Fields Test',
                    'highlights_json' => [
                        ['text' => 'Highlight 1'],
                        ['text' => 'Highlight 2'],
                    ],
                    'itinerary_json' => [
                        [
                            'day' => 1,
                            'title' => 'Full Day Tour',
                            'description' => 'A wonderful day',
                        ],
                    ],
                    'included_json' => [
                        ['text' => 'Professional guide'],
                    ],
                    'excluded_json' => [
                        ['text' => 'Meals'],
                    ],
                    'faq_json' => [
                        ['question' => 'What to bring?', 'answer' => 'Comfortable shoes'],
                    ],
                    'requirements_json' => [
                        ['title' => 'Walking', 'text' => 'Moderate walking required'],
                    ],
                    'cancellation_policy' => 'Free cancellation 24h before',
                ],
            ],
        ];

        $response = $this->postJson('/api/internal/tours/upsert', $payload, [
            'X-Internal-Api-Key' => 'test-key',
        ]);

        $response->assertStatus(201);

        $tour = Tour::where('slug', 'test-json-fields-tour')->first();
        $translation = $tour->translations()->where('locale', 'en')->first();

        // Verify JSON fields
        $this->assertCount(2, $translation->highlights_json);
        $this->assertEquals('Highlight 1', $translation->highlights_json[0]['text']);
        $this->assertEquals('Free cancellation 24h before', $translation->cancellation_policy);
    }

    /**
     * Test upsert validation fails for invalid data
     */
    public function test_upsert_validates_payload(): void
    {
        // Missing required fields
        $response = $this->postJson('/api/internal/tours/upsert', [
            'tour' => [],
            'translations' => [],
        ], [
            'X-Internal-Api-Key' => 'test-key',
        ]);

        $response->assertStatus(422)
            ->assertJson(['ok' => false]);
    }

    /**
     * Test upsert returns correct URL in response
     */
    public function test_upsert_returns_correct_url(): void
    {
        $payload = [
            'fallback_mode' => 'allowed',
            'tour' => [
                'slug' => 'test-url-tour',
                'duration_days' => 1,
            ],
            'translations' => [
                'en' => [
                    'locale' => 'en',
                    'title' => 'URL Test Tour',
                    'itinerary_json' => [
                        ['day' => 1, 'title' => 'Day 1'],
                    ],
                ],
            ],
        ];

        $response = $this->postJson('/api/internal/tours/upsert', $payload, [
            'X-Internal-Api-Key' => 'test-key',
        ]);

        $response->assertStatus(201);
        $this->assertStringContainsString('/tours/test-url-tour', $response->json('url'));
    }
}
