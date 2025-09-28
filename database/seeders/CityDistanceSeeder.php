<?php

namespace Database\Seeders;

use App\Models\CityDistance;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CityDistanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $distances = [
            ['city_from_to' => 'Tashkent - Samarkand', 'distance_km' => 300.50],
            ['city_from_to' => 'Tashkent - Bukhara', 'distance_km' => 600.75],
            ['city_from_to' => 'Tashkent - Khiva', 'distance_km' => 1000.25],
            ['city_from_to' => 'Samarkand - Bukhara', 'distance_km' => 280.00],
            ['city_from_to' => 'Samarkand - Khiva', 'distance_km' => 750.50],
            ['city_from_to' => 'Bukhara - Khiva', 'distance_km' => 450.75],
            ['city_from_to' => 'Tashkent - Fergana', 'distance_km' => 320.00],
            ['city_from_to' => 'Tashkent - Namangan', 'distance_km' => 290.25],
            ['city_from_to' => 'Tashkent - Andijan', 'distance_km' => 350.50],
            ['city_from_to' => 'Samarkand - Termez', 'distance_km' => 400.00],
            ['city_from_to' => 'Tashkent - Nukus', 'distance_km' => 1200.75],
            ['city_from_to' => 'Bukhara - Nukus', 'distance_km' => 650.25],
            ['city_from_to' => 'Tashkent - Urgench', 'distance_km' => 950.50],
            ['city_from_to' => 'Samarkand - Shakhrisabz', 'distance_km' => 80.00],
            ['city_from_to' => 'Tashkent - Kokand', 'distance_km' => 220.75],
        ];

        foreach ($distances as $distance) {
            CityDistance::create($distance);
        }
    }
}
