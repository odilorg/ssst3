<?php

namespace Tests\Feature;

use App\Models\Tour;
use App\Models\TourTranslation;
use App\Services\TourTemplates;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class TourFallbackModeTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        config(['services.internal_api.key' => 'test-key']);

        // Clean up any test tours from previous runs
        Tour::where('slug', 'like', 'test-fallback-%')->forceDelete();
    }

    protected function tearDown(): void
    {
        Tour::where('slug', 'like', 'test-fallback-%')->forceDelete();
        parent::tearDown();
    }

    /**
     * Test strict mode (default) requires all JSON fields
     */
    public function test_strict_mode_requires_all_json_fields(): void
    {
        $payload = [
            'fallback_mode' => 'strict',
            'tour' => [
                'slug' => 'test-fallback-strict',
                'duration_days' => 2,
            ],
            'translations' => [
                'en' => [
                    'locale' => 'en',
                    'title' => 'Strict Mode Test',
                    'itinerary_json' => [
                        ['day' => 1, 'title' => 'Day 1'],
                        ['day' => 2, 'title' => 'Day 2'],
                    ],
                    // Missing: highlights_json, included_json, excluded_json, faq_json, requirements_json
                ],
            ],
        ];

        $response = $this->postJson('/api/internal/tours/upsert', $payload, [
            'X-Internal-Api-Key' => 'test-key',
        ]);

        $response->assertStatus(422)
            ->assertJson(['ok' => false]);

        // Should have validation errors for missing fields
        $errors = collect($response->json('errors'))->pluck('field')->toArray();
        $this->assertContains('translations.en.highlights_json', $errors);
        $this->assertContains('translations.en.included_json', $errors);
        $this->assertContains('translations.en.excluded_json', $errors);
        $this->assertContains('translations.en.faq_json', $errors);
        $this->assertContains('translations.en.requirements_json', $errors);
    }

    /**
     * Test strict mode works when all fields provided
     */
    public function test_strict_mode_works_with_all_fields(): void
    {
        $payload = [
            'fallback_mode' => 'strict',
            'tour' => [
                'slug' => 'test-fallback-strict-full',
                'duration_days' => 1,
            ],
            'translations' => [
                'en' => [
                    'locale' => 'en',
                    'title' => 'Full Strict Test',
                    'itinerary_json' => [
                        ['day' => 1, 'title' => 'Day 1'],
                    ],
                    'highlights_json' => [
                        ['text' => 'Custom highlight'],
                    ],
                    'included_json' => [
                        ['text' => 'Custom included'],
                    ],
                    'excluded_json' => [
                        ['text' => 'Custom excluded'],
                    ],
                    'faq_json' => [
                        ['question' => 'Q?', 'answer' => 'A'],
                    ],
                    'requirements_json' => [
                        ['title' => 'Req', 'text' => 'Description'],
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
                'fallback_mode' => 'strict',
            ]);

        // No template warnings should be present
        $warnings = $response->json('warnings');
        $templateWarnings = array_filter($warnings, fn($w) => str_contains($w, 'StandardTemplate'));
        $this->assertEmpty($templateWarnings);
    }

    /**
     * Test allowed mode fills missing fields from templates
     */
    public function test_allowed_mode_fills_templates(): void
    {
        $payload = [
            'fallback_mode' => 'allowed',
            'tour' => [
                'slug' => 'test-fallback-allowed',
                'duration_days' => 2,
            ],
            'translations' => [
                'en' => [
                    'locale' => 'en',
                    'title' => 'Allowed Mode Test',
                    'itinerary_json' => [
                        ['day' => 1, 'title' => 'Day 1'],
                        ['day' => 2, 'title' => 'Day 2'],
                    ],
                    // Missing JSON fields - should be templated
                ],
            ],
        ];

        $response = $this->postJson('/api/internal/tours/upsert', $payload, [
            'X-Internal-Api-Key' => 'test-key',
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'ok' => true,
                'fallback_mode' => 'allowed',
            ]);

        // Should have template warnings
        $warnings = $response->json('warnings');
        $templateWarnings = array_filter($warnings, fn($w) => str_contains($w, 'StandardTemplate'));
        $this->assertCount(5, $templateWarnings); // 5 fields templated

        // Verify templates were applied
        $tour = Tour::where('slug', 'test-fallback-allowed')->first();
        $translation = $tour->translations()->where('locale', 'en')->first();

        // Check that templated fields match TourTemplates
        $this->assertEquals(
            TourTemplates::standardIncludedV1(),
            $translation->included_json
        );
        $this->assertEquals(
            TourTemplates::standardExcludedV1(),
            $translation->excluded_json
        );
        $this->assertEquals(
            TourTemplates::standardFaqV1(),
            $translation->faq_json
        );
        $this->assertEquals(
            TourTemplates::standardRequirementsV1(),
            $translation->requirements_json
        );
        $this->assertEquals(
            TourTemplates::standardHighlightsV1(),
            $translation->highlights_json
        );
    }

    /**
     * Test allowed mode still requires itinerary_json
     */
    public function test_allowed_mode_still_requires_itinerary(): void
    {
        $payload = [
            'fallback_mode' => 'allowed',
            'tour' => [
                'slug' => 'test-fallback-no-itinerary',
                'duration_days' => 1,
            ],
            'translations' => [
                'en' => [
                    'locale' => 'en',
                    'title' => 'No Itinerary Test',
                    // Missing itinerary_json - should still fail
                ],
            ],
        ];

        $response = $this->postJson('/api/internal/tours/upsert', $payload, [
            'X-Internal-Api-Key' => 'test-key',
        ]);

        $response->assertStatus(422)
            ->assertJson(['ok' => false]);

        // Should have validation error for itinerary
        $errors = collect($response->json('errors'))->pluck('field')->toArray();
        $this->assertContains('translations.en.itinerary_json', $errors);
    }

    /**
     * Test allowed mode uses provided fields instead of templates
     */
    public function test_allowed_mode_uses_provided_fields_over_templates(): void
    {
        $customIncluded = [
            ['text' => 'My custom included item'],
        ];

        $payload = [
            'fallback_mode' => 'allowed',
            'tour' => [
                'slug' => 'test-fallback-partial',
                'duration_days' => 1,
            ],
            'translations' => [
                'en' => [
                    'locale' => 'en',
                    'title' => 'Partial Custom Test',
                    'itinerary_json' => [
                        ['day' => 1, 'title' => 'Day 1'],
                    ],
                    'included_json' => $customIncluded, // Provided
                    // Missing: highlights_json, excluded_json, faq_json, requirements_json
                ],
            ],
        ];

        $response = $this->postJson('/api/internal/tours/upsert', $payload, [
            'X-Internal-Api-Key' => 'test-key',
        ]);

        $response->assertStatus(201);

        // Should have 4 template warnings (not 5 - included_json was provided)
        $warnings = $response->json('warnings');
        $templateWarnings = array_filter($warnings, fn($w) => str_contains($w, 'StandardTemplate'));
        $this->assertCount(4, $templateWarnings);

        // Verify custom included was used, not template
        $tour = Tour::where('slug', 'test-fallback-partial')->first();
        $translation = $tour->translations()->where('locale', 'en')->first();

        $this->assertEquals($customIncluded, $translation->included_json);
        // But excluded should be from template
        $this->assertEquals(
            TourTemplates::standardExcludedV1(),
            $translation->excluded_json
        );
    }

    /**
     * Test default fallback_mode is strict
     */
    public function test_default_fallback_mode_is_strict(): void
    {
        $payload = [
            // No fallback_mode specified - should default to strict
            'tour' => [
                'slug' => 'test-fallback-default',
                'duration_days' => 1,
            ],
            'translations' => [
                'en' => [
                    'locale' => 'en',
                    'title' => 'Default Mode Test',
                    'itinerary_json' => [
                        ['day' => 1, 'title' => 'Day 1'],
                    ],
                    // Missing JSON fields - should fail in strict mode
                ],
            ],
        ];

        $response = $this->postJson('/api/internal/tours/upsert', $payload, [
            'X-Internal-Api-Key' => 'test-key',
        ]);

        $response->assertStatus(422)
            ->assertJson(['ok' => false]);
    }

    /**
     * Test invalid fallback_mode value is rejected
     */
    public function test_invalid_fallback_mode_rejected(): void
    {
        $payload = [
            'fallback_mode' => 'invalid',
            'tour' => [
                'slug' => 'test-fallback-invalid',
                'duration_days' => 1,
            ],
            'translations' => [
                'en' => [
                    'locale' => 'en',
                    'title' => 'Invalid Mode Test',
                    'itinerary_json' => [
                        ['day' => 1, 'title' => 'Day 1'],
                    ],
                ],
            ],
        ];

        $response = $this->postJson('/api/internal/tours/upsert', $payload, [
            'X-Internal-Api-Key' => 'test-key',
        ]);

        $response->assertStatus(422)
            ->assertJson(['ok' => false]);

        $errors = collect($response->json('errors'))->pluck('field')->toArray();
        $this->assertContains('fallback_mode', $errors);
    }

    /**
     * Test templates have expected structure
     */
    public function test_templates_have_valid_structure(): void
    {
        // Test included template
        $included = TourTemplates::standardIncludedV1();
        $this->assertIsArray($included);
        $this->assertNotEmpty($included);
        foreach ($included as $item) {
            $this->assertArrayHasKey('text', $item);
        }

        // Test excluded template
        $excluded = TourTemplates::standardExcludedV1();
        $this->assertIsArray($excluded);
        $this->assertNotEmpty($excluded);
        foreach ($excluded as $item) {
            $this->assertArrayHasKey('text', $item);
        }

        // Test FAQ template
        $faq = TourTemplates::standardFaqV1();
        $this->assertIsArray($faq);
        $this->assertNotEmpty($faq);
        foreach ($faq as $item) {
            $this->assertArrayHasKey('question', $item);
            $this->assertArrayHasKey('answer', $item);
        }

        // Test requirements template
        $requirements = TourTemplates::standardRequirementsV1();
        $this->assertIsArray($requirements);
        $this->assertNotEmpty($requirements);
        foreach ($requirements as $item) {
            $this->assertArrayHasKey('title', $item);
            $this->assertArrayHasKey('text', $item);
        }

        // Test highlights template
        $highlights = TourTemplates::standardHighlightsV1();
        $this->assertIsArray($highlights);
        $this->assertNotEmpty($highlights);
        foreach ($highlights as $item) {
            $this->assertArrayHasKey('text', $item);
        }
    }

    /**
     * Test TourTemplates::get() method
     */
    public function test_templates_get_method(): void
    {
        $this->assertEquals(
            TourTemplates::standardIncludedV1(),
            TourTemplates::get('included', 'v1')
        );

        $this->assertEquals(
            TourTemplates::standardFaqV1(),
            TourTemplates::get('faq')
        );

        $this->assertNull(TourTemplates::get('nonexistent'));
        $this->assertNull(TourTemplates::get('included', 'v999'));
    }
}
