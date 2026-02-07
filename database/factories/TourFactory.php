<?php

namespace Database\Factories;

use App\Models\Tour;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tour>
 */
class TourFactory extends Factory
{
    protected $model = Tour::class;

    public function definition(): array
    {
        $title = fake()->sentence(3);

        return [
            'title' => $title,
            'slug' => Str::slug($title) . '-' . uniqid(),
            'short_description' => fake()->paragraph(),
            'long_description' => fake()->paragraphs(3, true),
            'duration_days' => fake()->numberBetween(1, 14),
            'price_per_person' => fake()->numberBetween(100, 2000),
            'is_active' => true,
        ];
    }
}
