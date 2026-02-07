<?php

namespace Database\Seeders;

use App\Models\Tour;
use App\Models\TourDeparture;
use App\Models\City;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class PrivateGroupTourSeeder extends Seeder
{
    public function run(): void
    {
        $tashkent = City::firstOrCreate(['name' => 'Tashkent'], ['slug' => 'tashkent']);
        $samarkand = City::firstOrCreate(['name' => 'Samarkand'], ['slug' => 'samarkand']);

        // 1. PRIVATE-ONLY TOUR
        $privateTour = Tour::create([
            'title' => 'Private Tashkent City Discovery',
            'slug' => 'private-tashkent-city-discovery',
            'short_description' => 'Explore the capital of Uzbekistan with your own private guide',
            'long_description' => 'Discover the rich history and modern culture of Tashkent on this private full-day tour.',
            'duration_days' => 1,
            'duration_text' => '8 hours',
            'city_id' => $tashkent->id,
            'is_active' => true,

            // Private tour settings
            'supports_private' => true,
            'supports_group' => false,
            'private_base_price' => 75.00,
            'private_min_guests' => 1,
            'private_max_guests' => 8,

            'currency' => 'USD',
            'show_price' => true,

            'highlights' => [
                'Private guide exclusively for your group',
                'Flexible itinerary tailored to your interests',
                'Hotel pickup and drop-off included',
                'Visit Chorsu Bazaar, Independence Square, and more'
            ],

            'minimum_advance_days' => 3,
        ]);

        // 2. GROUP-ONLY TOUR
        $groupTour = Tour::create([
            'title' => 'Samarkand Shared Group Experience',
            'slug' => 'samarkand-shared-group-experience',
            'short_description' => 'Join fellow travelers on this budget-friendly group tour',
            'long_description' => 'Meet other travelers and explore Samarkand\'s UNESCO sites together.',
            'duration_days' => 2,
            'duration_text' => '2 days / 1 night',
            'city_id' => $samarkand->id,
            'is_active' => true,

            // Group tour settings
            'supports_private' => false,
            'supports_group' => true,

            'currency' => 'USD',
            'show_price' => true,

            'highlights' => [
                'Small group experience (max 12 people)',
                'English-speaking guide',
                'Visit Registan Square, Shah-i-Zinda, Gur-e-Amir',
                'One night accommodation included'
            ],

            'minimum_advance_days' => 14,
        ]);

        // Create group departures for the group tour
        $this->createGroupDepartures($groupTour);

        // 3. MIXED TOUR (Both Private and Group)
        $mixedTour = Tour::create([
            'title' => 'Silk Road Adventure - Private or Group',
            'slug' => 'silk-road-adventure-flexible',
            'short_description' => 'Choose your style: Private exclusivity or group camaraderie',
            'long_description' => 'Experience the ancient Silk Road with the option of private or group travel.',
            'duration_days' => 3,
            'duration_text' => '3 days / 2 nights',
            'city_id' => $samarkand->id,
            'is_active' => true,

            // Mixed tour settings
            'supports_private' => true,
            'supports_group' => true,
            'private_base_price' => 150.00,
            'private_min_guests' => 2,
            'private_max_guests' => 10,

            'currency' => 'USD',
            'show_price' => true,

            'highlights' => [
                'Flexible booking: Choose private or join a group',
                'Visit Samarkand, Bukhara, and Khiva',
                'Expert local guides',
                '2 nights in boutique hotels'
            ],

            'minimum_advance_days' => 30,
        ]);

        // Create group departures for the mixed tour
        $this->createGroupDepartures($mixedTour);

        $this->command->info('✓ Created 3 test tours');
        $this->command->info('  - Private-only: ' . $privateTour->title);
        $this->command->info('  - Group-only: ' . $groupTour->title);
        $this->command->info('  - Mixed: ' . $mixedTour->title);
    }

    private function createGroupDepartures(Tour $tour): void
    {
        $basePrice = 95.00; // Group tour price per person

        // Create 5 upcoming departures
        for ($i = 1; $i <= 5; $i++) {
            $startDate = Carbon::now()->addDays(30 + ($i * 14)); // Every 2 weeks
            $endDate = $startDate->copy()->addDays($tour->duration_days - 1);

            // Simulate different booking states
            $maxPax = 12;
            $bookedPax = match($i) {
                1 => 10, // Filling fast
                2 => 5,  // Half full
                3 => 2,  // Just started
                4 => 0,  // Empty
                5 => 0,  // Empty
            };

            TourDeparture::create([
                'tour_id' => $tour->id,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'price_per_person' => $basePrice + ($i * 5), // Slight price variation
                'max_pax' => $maxPax,
                'booked_pax' => $bookedPax,
                'min_pax' => 4,
                'status' => $bookedPax >= $maxPax ? 'full' : ($bookedPax >= 4 ? 'guaranteed' : 'open'),
                'departure_type' => 'group',
                'notes' => "Departure {$i} for {$tour->title}",
            ]);
        }

        $this->command->info("  ✓ Created 5 group departures for: {$tour->title}");
    }
}
