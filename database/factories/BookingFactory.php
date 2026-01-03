<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\Customer;
use App\Models\Tour;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Booking>
 */
class BookingFactory extends Factory
{
    protected $model = Booking::class;

    public function definition(): array
    {
        return [
            'reference' => 'BK-' . strtoupper(uniqid()),
            'customer_id' => Customer::factory(),
            'tour_id' => Tour::factory(),
            'type' => 'private',
            'start_date' => fake()->dateTimeBetween('+1 week', '+3 months'),
            'pax_total' => fake()->numberBetween(1, 10),
            'status' => 'confirmed',
            'payment_status' => 'pending',
            'currency' => 'USD',
            'total_price' => fake()->numberBetween(500, 5000),
        ];
    }
}
