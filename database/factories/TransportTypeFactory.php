<?php

namespace Database\Factories;

use App\Models\TransportType;
use Illuminate\Database\Eloquent\Factories\Factory;

class TransportTypeFactory extends Factory
{
    protected $model = TransportType::class;

    public function definition(): array
    {
        return [
            'type' => fake()->randomElement(['Sedan', 'SUV', 'Van', 'Bus', 'Minibus']),
            'capacity' => fake()->numberBetween(4, 50),
        ];
    }
}
