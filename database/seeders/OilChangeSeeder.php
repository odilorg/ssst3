<?php

namespace Database\Seeders;

use App\Models\OilChange;
use App\Models\Transport;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OilChangeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $transports = Transport::whereIn('category', ['bus', 'car', 'mikro_bus', 'mini_van'])->get();
        
        if ($transports->isEmpty()) {
            $this->command->error('Please run TransportSeeder first!');
            return;
        }

        $oilChanges = [
            [
                'transport_id' => $transports->first()->id,
                'oil_change_date' => now()->subMonths(2),
                'mileage_at_change' => 45000,
                'cost' => 45.00,
                'oil_type' => 'Castrol GTX 5W-30',
                'service_center' => 'Auto Service Center Tashkent',
                'notes' => 'Regular oil change with filter replacement',
                'other_services' => [
                    ['service_name' => 'Oil Filter Replacement', 'service_cost' => 15.00],
                    ['service_name' => 'Air Filter Cleaning', 'service_cost' => 8.00],
                ],
                'next_change_date' => now()->addMonths(4),
                'next_change_mileage' => 55000,
            ],
            [
                'transport_id' => $transports->skip(1)->first()->id,
                'oil_change_date' => now()->subMonths(1),
                'mileage_at_change' => 32000,
                'cost' => 38.00,
                'oil_type' => 'Mobil 1 10W-40',
                'service_center' => 'Quick Lube Express',
                'notes' => 'High-performance oil change',
                'other_services' => [
                    ['service_name' => 'Oil Filter Replacement', 'service_cost' => 12.00],
                ],
                'next_change_date' => now()->addMonths(5),
                'next_change_mileage' => 40000,
            ],
            [
                'transport_id' => $transports->skip(2)->first()->id,
                'oil_change_date' => now()->subWeeks(3),
                'mileage_at_change' => 78000,
                'cost' => 65.00,
                'oil_type' => 'Shell Helix Ultra 5W-40',
                'service_center' => 'Professional Auto Care',
                'notes' => 'Full service including oil change and inspection',
                'other_services' => [
                    ['service_name' => 'Oil Filter Replacement', 'service_cost' => 18.00],
                    ['service_name' => 'Fuel Filter Replacement', 'service_cost' => 25.00],
                    ['service_name' => 'Brake Fluid Check', 'service_cost' => 12.00],
                ],
                'next_change_date' => now()->addMonths(3),
                'next_change_mileage' => 93000,
            ],
            [
                'transport_id' => $transports->skip(3)->first()->id,
                'oil_change_date' => now()->subWeeks(1),
                'mileage_at_change' => 25000,
                'cost' => 32.00,
                'oil_type' => 'Total Quartz 9000 5W-30',
                'service_center' => 'City Auto Service',
                'notes' => 'Standard oil change service',
                'other_services' => [
                    ['service_name' => 'Oil Filter Replacement', 'service_cost' => 10.00],
                ],
                'next_change_date' => now()->addMonths(6),
                'next_change_mileage' => 35000,
            ],
            [
                'transport_id' => $transports->first()->id,
                'oil_change_date' => now()->subDays(5),
                'mileage_at_change' => 52000,
                'cost' => 48.00,
                'oil_type' => 'Castrol GTX 5W-30',
                'service_center' => 'Auto Service Center Tashkent',
                'notes' => 'Follow-up oil change after 5000km',
                'other_services' => [
                    ['service_name' => 'Oil Filter Replacement', 'service_cost' => 15.00],
                    ['service_name' => 'Coolant Top-up', 'service_cost' => 5.00],
                ],
                'next_change_date' => now()->addMonths(6),
                'next_change_mileage' => 62000,
            ],
        ];

        foreach ($oilChanges as $oilChangeData) {
            OilChange::create($oilChangeData);
        }
    }
}
