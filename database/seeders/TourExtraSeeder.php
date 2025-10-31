<?php

namespace Database\Seeders;

use App\Models\Tour;
use App\Models\TourExtra;
use Illuminate\Database\Seeder;

class TourExtraSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get Samarkand City Tour
        $samarkandTour = Tour::where('slug', 'samarkand-city-tour')->first();
        if ($samarkandTour) {
            $extras = [
                [
                    'tour_id' => $samarkandTour->id,
                    'name' => 'Airport Transfer',
                    'description' => 'Private transfer from/to Samarkand Airport or Train Station. Comfortable vehicle with English-speaking driver.',
                    'price' => 30.00,
                    'currency' => 'USD',
                    'price_unit' => 'per_group',
                    'icon' => 'car',
                    'is_active' => true,
                    'sort_order' => 1,
                ],
                [
                    'tour_id' => $samarkandTour->id,
                    'name' => 'Traditional Uzbek Lunch',
                    'description' => 'Enjoy an authentic 3-course Uzbek meal at a traditional restaurant. Includes plov (pilaf), fresh salads, samsa, and green tea. Vegetarian options available.',
                    'price' => 15.00,
                    'currency' => 'USD',
                    'price_unit' => 'per_person',
                    'icon' => 'utensils',
                    'is_active' => true,
                    'sort_order' => 2,
                ],
                [
                    'tour_id' => $samarkandTour->id,
                    'name' => 'Professional Photography Service',
                    'description' => 'Capture your memories with a professional photographer. Receive 50+ high-resolution edited photos delivered within 48 hours via digital download.',
                    'price' => 80.00,
                    'currency' => 'USD',
                    'price_unit' => 'per_session',
                    'icon' => 'camera',
                    'is_active' => true,
                    'sort_order' => 3,
                ],
                [
                    'tour_id' => $samarkandTour->id,
                    'name' => 'Guided Shopping Experience',
                    'description' => 'Visit authentic local markets and artisan workshops. Your guide will help you find quality souvenirs, textiles, and ceramics at fair prices. Includes transport to 3 recommended shops.',
                    'price' => 20.00,
                    'currency' => 'USD',
                    'price_unit' => 'per_group',
                    'icon' => 'shopping-bag',
                    'is_active' => true,
                    'sort_order' => 4,
                ],
            ];

            foreach ($extras as $extra) {
                TourExtra::create($extra);
            }
        }

        // Get 5-Day Silk Road Classic Tour
        $silkRoadTour = Tour::where('slug', '5-day-silk-road-classic')->first();
        if ($silkRoadTour) {
            $extras = [
                [
                    'tour_id' => $silkRoadTour->id,
                    'name' => 'Hotel Room Upgrade',
                    'description' => 'Upgrade to 5-star deluxe accommodation throughout the tour. Includes superior rooms with luxury amenities.',
                    'price' => 200.00,
                    'currency' => 'USD',
                    'price_unit' => 'per_person',
                    'icon' => 'star',
                    'is_active' => true,
                    'sort_order' => 1,
                ],
                [
                    'tour_id' => $silkRoadTour->id,
                    'name' => 'All Meals Package',
                    'description' => 'Add all lunches and dinners throughout the tour. Includes traditional Uzbek cuisine at carefully selected restaurants.',
                    'price' => 120.00,
                    'currency' => 'USD',
                    'price_unit' => 'per_person',
                    'icon' => 'utensils',
                    'is_active' => true,
                    'sort_order' => 2,
                ],
                [
                    'tour_id' => $silkRoadTour->id,
                    'name' => 'Traditional Silk Workshop Experience',
                    'description' => 'Visit a silk factory in Bukhara and learn about traditional silk production. Includes hands-on demonstration and workshop tour.',
                    'price' => 35.00,
                    'currency' => 'USD',
                    'price_unit' => 'per_person',
                    'icon' => 'scissors',
                    'is_active' => true,
                    'sort_order' => 3,
                ],
                [
                    'tour_id' => $silkRoadTour->id,
                    'name' => 'Cooking Class Experience',
                    'description' => 'Learn to cook traditional Uzbek plov with a local family. Includes market visit, hands-on cooking, and dinner with the family.',
                    'price' => 45.00,
                    'currency' => 'USD',
                    'price_unit' => 'per_person',
                    'icon' => 'chef-hat',
                    'is_active' => true,
                    'sort_order' => 4,
                ],
            ];

            foreach ($extras as $extra) {
                TourExtra::create($extra);
            }
        }

        // Get Bukhara City Tour
        $bukharaTour = Tour::where('slug', 'full-day-bukhara-city-tour')->first();
        if ($bukharaTour) {
            $extras = [
                [
                    'tour_id' => $bukharaTour->id,
                    'name' => 'Hotel Pickup Extension',
                    'description' => 'Extend pickup service beyond 5km radius. Includes pick up from hotels outside central Bukhara.',
                    'price' => 15.00,
                    'currency' => 'USD',
                    'price_unit' => 'per_group',
                    'icon' => 'car',
                    'is_active' => true,
                    'sort_order' => 1,
                ],
                [
                    'tour_id' => $bukharaTour->id,
                    'name' => 'Carpet Shopping Assistance',
                    'description' => 'Expert guide to help you select and negotiate for authentic Bukhara carpets. Includes visit to certified carpet workshops.',
                    'price' => 25.00,
                    'currency' => 'USD',
                    'price_unit' => 'per_group',
                    'icon' => 'shopping-cart',
                    'is_active' => true,
                    'sort_order' => 2,
                ],
                [
                    'tour_id' => $bukharaTour->id,
                    'name' => 'Traditional Tea Ceremony',
                    'description' => 'Experience a traditional Uzbek tea ceremony at a historic chaikhana (tea house). Includes tea, sweets, and cultural presentation.',
                    'price' => 18.00,
                    'currency' => 'USD',
                    'price_unit' => 'per_person',
                    'icon' => 'coffee',
                    'is_active' => true,
                    'sort_order' => 3,
                ],
            ];

            foreach ($extras as $extra) {
                TourExtra::create($extra);
            }
        }
    }
}
