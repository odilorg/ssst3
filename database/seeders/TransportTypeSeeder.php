<?php

namespace Database\Seeders;

use App\Models\TransportType;
use App\Models\TransportPrice;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TransportTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $transportTypes = [
            [
                'type' => 'Mercedes Sprinter',
                'category' => 'bus',
                'running_days' => null,
                'prices' => [
                    ['price_type' => 'per_day', 'cost' => 150.00],
                    ['price_type' => 'per_pickup_dropoff', 'cost' => 25.00],
                ]
            ],
            [
                'type' => 'Toyota Hiace',
                'category' => 'mikro_bus',
                'running_days' => null,
                'prices' => [
                    ['price_type' => 'per_day', 'cost' => 120.00],
                    ['price_type' => 'per_pickup_dropoff', 'cost' => 20.00],
                ]
            ],
            [
                'type' => 'Hyundai Starex',
                'category' => 'mini_van',
                'running_days' => null,
                'prices' => [
                    ['price_type' => 'per_day', 'cost' => 100.00],
                    ['price_type' => 'per_pickup_dropoff', 'cost' => 18.00],
                ]
            ],
            [
                'type' => 'Toyota Camry',
                'category' => 'car',
                'running_days' => null,
                'prices' => [
                    ['price_type' => 'per_day', 'cost' => 80.00],
                    ['price_type' => 'per_pickup_dropoff', 'cost' => 15.00],
                    ['price_type' => 'po_gorodu', 'cost' => 5.00],
                ]
            ],
            [
                'type' => 'Uzbekistan Airways',
                'category' => 'air',
                'running_days' => null,
                'prices' => [
                    ['price_type' => 'economy', 'cost' => 200.00],
                    ['price_type' => 'business', 'cost' => 350.00],
                ]
            ],
            [
                'type' => 'Afrosiyob High-Speed Train',
                'category' => 'rail',
                'running_days' => ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'],
                'prices' => [
                    ['price_type' => 'economy', 'cost' => 25.00],
                    ['price_type' => 'business', 'cost' => 45.00],
                    ['price_type' => 'vip', 'cost' => 80.00],
                ]
            ],
            [
                'type' => 'Regular Train',
                'category' => 'rail',
                'running_days' => ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'],
                'prices' => [
                    ['price_type' => 'economy', 'cost' => 15.00],
                    ['price_type' => 'business', 'cost' => 25.00],
                ]
            ],
            [
                'type' => 'Luxury Bus',
                'category' => 'bus',
                'running_days' => null,
                'prices' => [
                    ['price_type' => 'per_day', 'cost' => 200.00],
                    ['price_type' => 'per_pickup_dropoff', 'cost' => 35.00],
                    ['price_type' => 'vip', 'cost' => 250.00],
                ]
            ],
            [
                'type' => 'Ford Transit',
                'category' => 'mikro_bus',
                'running_days' => null,
                'prices' => [
                    ['price_type' => 'per_day', 'cost' => 110.00],
                    ['price_type' => 'per_pickup_dropoff', 'cost' => 19.00],
                ]
            ],
            [
                'type' => 'Chevrolet Captiva',
                'category' => 'car',
                'running_days' => null,
                'prices' => [
                    ['price_type' => 'per_day', 'cost' => 90.00],
                    ['price_type' => 'per_pickup_dropoff', 'cost' => 17.00],
                    ['price_type' => 'po_gorodu', 'cost' => 6.00],
                ]
            ],
        ];

        foreach ($transportTypes as $typeData) {
            $prices = $typeData['prices'];
            unset($typeData['prices']);
            
            $transportType = TransportType::create($typeData);
            
            foreach ($prices as $priceData) {
                $priceData['transport_type_id'] = $transportType->id;
                $priceData['currency'] = 'USD';
                TransportPrice::create($priceData);
            }
        }
    }
}
