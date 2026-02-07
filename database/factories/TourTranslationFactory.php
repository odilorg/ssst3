<?php

namespace Database\Factories;

use App\Models\Tour;
use App\Models\TourTranslation;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class TourTranslationFactory extends Factory
{
    protected $model = TourTranslation::class;

    public function definition(): array
    {
        $title = $this->faker->sentence(4);

        return [
            'tour_id' => Tour::factory(),
            'locale' => 'en',
            'title' => $title,
            'slug' => Str::slug($title),
            'excerpt' => $this->faker->paragraph(2),
            'content' => $this->faker->paragraphs(3, true),
            'seo_title' => null,
            'seo_description' => null,
            'highlights_json' => null,
            'itinerary_json' => null,
            'included_json' => null,
            'excluded_json' => null,
            'faq_json' => null,
            'requirements_json' => null,
            'cancellation_policy' => null,
            'meeting_instructions' => null,
        ];
    }

    public function locale(string $locale): static
    {
        return $this->state(fn (array $attributes) => [
            'locale' => $locale,
        ]);
    }

    public function russian(): static
    {
        return $this->state(fn (array $attributes) => [
            'locale' => 'ru',
            'title' => 'Тур по Самарканду',
            'slug' => 'tur-po-samarkandy',
            'excerpt' => 'Краткое описание тура',
            'content' => 'Полное описание тура на русском языке',
        ]);
    }
}
