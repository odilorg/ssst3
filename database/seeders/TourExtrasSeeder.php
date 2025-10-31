<?php

namespace Database\Seeders;

use App\Models\Tour;
use App\Models\TourExtra;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TourExtrasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get tours that don't have extras
        $toursWithoutExtras = Tour::withCount('extras')
            ->having('extras_count', '=', 0)
            ->get();

        if ($toursWithoutExtras->isEmpty()) {
            $this->command->warn('All tours already have extras!');
            return;
        }

        $this->command->info("Adding extras to {$toursWithoutExtras->count()} tours...");

        // Define the 4 standard extras
        $extras = [
            [
                'name' => 'Airport Transfer',
                'description' => 'Private transfer from/to Airport or Train Station. Comfortable vehicle with English-speaking driver.',
                'price' => 30.00,
                'price_unit' => 'per_group',
                'icon' => '<svg class="icon icon--car" width="22" height="18" viewBox="0 0 22 18" fill="currentColor" aria-hidden="true"><path d="M18 7l-2-4H6L4 7H0v8h2v3h3v-3h12v3h3v-3h2V7h-4zM7 4h8l1.5 3h-11L7 4zM5 13a2 2 0 110-4 2 2 0 010 4zm12 0a2 2 0 110-4 2 2 0 010 4z"/></svg>',
                'sort_order' => 1,
            ],
            [
                'name' => 'Traditional Uzbek Lunch',
                'description' => 'Enjoy an authentic 3-course Uzbek meal at a traditional restaurant. Includes plov (pilaf), fresh salads, samsa, and green tea. Vegetarian options available.',
                'price' => 15.00,
                'price_unit' => 'per_person',
                'icon' => '<svg class="icon icon--utensils" width="18" height="20" viewBox="0 0 18 20" fill="currentColor" aria-hidden="true"><path d="M4 0v7a2 2 0 002 2v11h2V9a2 2 0 002-2V0H8v7H6V0H4zm10 0v6c0 1.1-.9 2-2 2v12h2V8c1.1 0 2-.9 2-2V0h-2z"/></svg>',
                'sort_order' => 2,
            ],
            [
                'name' => 'Professional Photography Service',
                'description' => 'Capture your memories with a professional photographer. 1-hour session at iconic locations. Includes 50+ edited high-resolution photos delivered within 48 hours.',
                'price' => 80.00,
                'price_unit' => 'per_session',
                'icon' => '<svg class="icon icon--camera" width="20" height="18" viewBox="0 0 20 18" fill="currentColor" aria-hidden="true"><path d="M10 5a4 4 0 100 8 4 4 0 000-8zM2 4a2 2 0 00-2 2v10a2 2 0 002 2h16a2 2 0 002-2V6a2 2 0 00-2-2h-3L13 0H7L5 4H2zm8 11a5 5 0 110-10 5 5 0 010 10z"/></svg>',
                'sort_order' => 3,
            ],
            [
                'name' => 'Guided Shopping Experience',
                'description' => 'Visit authentic local markets and artisan workshops with a knowledgeable guide. Learn about traditional crafts, negotiate prices, and discover unique souvenirs.',
                'price' => 20.00,
                'price_unit' => 'per_group',
                'icon' => '<svg class="icon icon--gift" width="20" height="20" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M18 6h-3.17A3 3 0 0012 2a3 3 0 00-2.83 4H2a2 2 0 00-2 2v2h20V8a2 2 0 00-2-2zM9 4a1 1 0 112 0 1 1 0 01-2 0zM0 18a2 2 0 002 2h7V10H0v8zm11 2h7a2 2 0 002-2v-8h-9v10z"/></svg>',
                'sort_order' => 4,
            ],
        ];

        // Add extras to each tour
        foreach ($toursWithoutExtras as $tour) {
            foreach ($extras as $extraData) {
                TourExtra::create([
                    'tour_id' => $tour->id,
                    'name' => $extraData['name'],
                    'description' => $extraData['description'],
                    'price' => $extraData['price'],
                    'currency' => 'USD',
                    'price_unit' => $extraData['price_unit'],
                    'icon' => $extraData['icon'],
                    'is_active' => true,
                    'sort_order' => $extraData['sort_order'],
                ]);
            }

            $this->command->info("  ✓ Added 4 extras to: {$tour->title}");
        }

        $this->command->info('Tour extras seeded successfully!');
    }
}
