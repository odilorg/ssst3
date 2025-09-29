<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            SpokenLanguageSeeder::class,
            CitySeeder::class,
            CompanySeeder::class,
            CustomerSeeder::class,
            DriverSeeder::class,
            GuideSeeder::class,
            HotelSeeder::class,
            MonumentSeeder::class,
            OilChangeSeeder::class,
            RestaurantSeeder::class,
            TourSeeder::class,
            TransportTypeSeeder::class,
            TransportSeeder::class,
            CityDistanceSeeder::class,
        ]);
    }
}
