<?php

namespace Database\Factories;

use App\Models\Transport;
use App\Models\TransportType;
use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

class TransportFactory extends Factory
{
    protected $model = Transport::class;

    public function definition(): array
    {
        return [
            'transport_type_id' => TransportType::factory(),
            'company_id' => Company::factory(),
            'plate_number' => strtoupper(fake()->bothify('??###??')),
            'model' => fake()->randomElement(['Toyota Camry', 'Mercedes S-Class', 'Ford Transit', 'Hyundai Starex']),
            'daily_rate' => fake()->randomFloat(2, 50, 200),
        ];
    }
}
