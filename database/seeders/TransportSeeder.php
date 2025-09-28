<?php

namespace Database\Seeders;

use App\Models\Amenity;
use App\Models\City;
use App\Models\Company;
use App\Models\Driver;
use App\Models\Transport;
use App\Models\TransportPrice;
use App\Models\TransportType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TransportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create transport types
        $transportTypes = [
            ['type' => 'Mercedes Sprinter', 'category' => 'mikro_bus', 'running_days' => ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday']],
            ['type' => 'Toyota Hiace', 'category' => 'mini_van', 'running_days' => ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday']],
            ['type' => 'Mercedes Tourismo', 'category' => 'bus', 'running_days' => ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday']],
            ['type' => 'Toyota Camry', 'category' => 'car', 'running_days' => ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday']],
            ['type' => 'Boeing 737', 'category' => 'air', 'running_days' => ['monday', 'wednesday', 'friday', 'sunday']],
            ['type' => 'Afrosiyob Express', 'category' => 'rail', 'running_days' => ['tuesday', 'thursday', 'saturday']],
        ];

        foreach ($transportTypes as $typeData) {
            TransportType::create($typeData);
        }

        // Create transport prices
        $transportPrices = [
            ['transport_type_id' => 1, 'price_type' => 'per_km', 'cost' => 2.50],
            ['transport_type_id' => 1, 'price_type' => 'per_hour', 'cost' => 25.00],
            ['transport_type_id' => 2, 'price_type' => 'per_km', 'cost' => 3.00],
            ['transport_type_id' => 2, 'price_type' => 'per_hour', 'cost' => 30.00],
            ['transport_type_id' => 3, 'price_type' => 'per_km', 'cost' => 4.00],
            ['transport_type_id' => 3, 'price_type' => 'per_hour', 'cost' => 40.00],
            ['transport_type_id' => 4, 'price_type' => 'per_km', 'cost' => 1.50],
            ['transport_type_id' => 4, 'price_type' => 'per_hour', 'cost' => 15.00],
            ['transport_type_id' => 5, 'price_type' => 'per_seat', 'cost' => 150.00],
            ['transport_type_id' => 6, 'price_type' => 'per_seat', 'cost' => 80.00],
        ];

        foreach ($transportPrices as $priceData) {
            TransportPrice::create($priceData);
        }

        // Get existing data
        $firstCity = City::first();
        $firstCompany = Company::first();
        $drivers = Driver::all();
        $amenities = Amenity::all();

        if (!$firstCity || !$firstCompany || $drivers->isEmpty() || $amenities->isEmpty()) {
            $this->command->error('Please run CitySeeder, CompanySeeder, DriverSeeder, and HotelSeeder first!');
            return;
        }

        $transports = [
            [
                'plate_number' => '01 A 123 BC',
                'model' => 'Mercedes Sprinter 515',
                'number_of_seat' => 16,
                'category' => 'mikro_bus',
                'transport_type_id' => 1,
                'driver_id' => $drivers->first()->id,
                'city_id' => $firstCity->id,
                'fuel_type' => 'diesel',
                'oil_change_interval_months' => 6,
                'oil_change_interval_km' => 10000,
                'fuel_consumption' => 12.5,
                'fuel_remaining_liter' => 80.0,
                'company_id' => $firstCompany->id,
                'amenities' => ['Wi-Fi', 'Air Conditioning', 'TV'],
            ],
            [
                'plate_number' => '01 B 456 DE',
                'model' => 'Toyota Hiace 2023',
                'number_of_seat' => 12,
                'category' => 'mini_van',
                'transport_type_id' => 2,
                'driver_id' => $drivers->skip(1)->first()->id,
                'city_id' => $firstCity->id,
                'fuel_type' => 'benzin/propane',
                'oil_change_interval_months' => 6,
                'oil_change_interval_km' => 8000,
                'fuel_consumption' => 10.0,
                'fuel_remaining_liter' => 60.0,
                'company_id' => $firstCompany->id,
                'amenities' => ['Wi-Fi', 'Air Conditioning'],
            ],
            [
                'plate_number' => '01 C 789 FG',
                'model' => 'Mercedes Tourismo M',
                'number_of_seat' => 49,
                'category' => 'bus',
                'transport_type_id' => 3,
                'driver_id' => $drivers->skip(2)->first()->id,
                'city_id' => $firstCity->id,
                'fuel_type' => 'diesel',
                'oil_change_interval_months' => 3,
                'oil_change_interval_km' => 15000,
                'fuel_consumption' => 25.0,
                'fuel_remaining_liter' => 200.0,
                'company_id' => $firstCompany->id,
                'amenities' => ['Wi-Fi', 'Air Conditioning', 'TV', 'Mini Bar'],
            ],
            [
                'plate_number' => '01 D 012 HI',
                'model' => 'Toyota Camry 2023',
                'number_of_seat' => 4,
                'category' => 'car',
                'transport_type_id' => 4,
                'driver_id' => $drivers->skip(3)->first()->id,
                'city_id' => $firstCity->id,
                'fuel_type' => 'benzin/propane',
                'oil_change_interval_months' => 6,
                'oil_change_interval_km' => 10000,
                'fuel_consumption' => 8.5,
                'fuel_remaining_liter' => 50.0,
                'company_id' => $firstCompany->id,
                'amenities' => ['Air Conditioning'],
            ],
            [
                'model' => 'Boeing 737-800',
                'number_of_seat' => 189,
                'category' => 'air',
                'transport_type_id' => 5,
                'departure_time' => '08:00',
                'arrival_time' => '10:30',
                'company_id' => $firstCompany->id,
                'amenities' => ['Wi-Fi', 'Entertainment System', 'Meal Service'],
            ],
            [
                'model' => 'Afrosiyob Express',
                'number_of_seat' => 258,
                'category' => 'rail',
                'transport_type_id' => 6,
                'departure_time' => '07:30',
                'arrival_time' => '11:45',
                'company_id' => $firstCompany->id,
                'amenities' => ['Wi-Fi', 'Air Conditioning', 'Restaurant Car'],
            ],
        ];

        foreach ($transports as $transportData) {
            $amenities = $transportData['amenities'] ?? [];
            unset($transportData['amenities']);
            
            $transport = Transport::create([
                'plate_number' => $transportData['plate_number'] ?? null,
                'model' => $transportData['model'],
                'number_of_seat' => $transportData['number_of_seat'],
                'category' => $transportData['category'],
                'transport_type_id' => $transportData['transport_type_id'],
                'departure_time' => $transportData['departure_time'] ?? null,
                'arrival_time' => $transportData['arrival_time'] ?? null,
                'driver_id' => $transportData['driver_id'] ?? null,
                'city_id' => $transportData['city_id'] ?? null,
                'fuel_type' => $transportData['fuel_type'] ?? null,
                'oil_change_interval_months' => $transportData['oil_change_interval_months'] ?? null,
                'oil_change_interval_km' => $transportData['oil_change_interval_km'] ?? null,
                'fuel_consumption' => $transportData['fuel_consumption'] ?? null,
                'fuel_remaining_liter' => $transportData['fuel_remaining_liter'] ?? null,
                'company_id' => $transportData['company_id'],
                'images' => [],
            ]);
            
            // Attach amenities to transport
            if (!empty($amenities)) {
                $amenityIds = Amenity::whereIn('name', $amenities)->pluck('id');
                $transport->amenities()->attach($amenityIds);
            }
        }
    }
}
