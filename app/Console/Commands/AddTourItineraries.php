<?php

namespace App\Console\Commands;

use App\Models\Tour;
use App\Models\ItineraryItem;
use App\Models\City;
use Illuminate\Console\Command;

class AddTourItineraries extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'add:itineraries';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add detailed itineraries to 4-8 day tours';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting to add itineraries for multi-day tours...');

        // Update Khiva tour to 4 days
        $this->updateKhivaTour();
        $this->info('Updated Khiva tour to 4 days');

        // Add itinerary for Complete Silk Road Heritage (7 days)
        $this->addCompleteSilkRoadItinerary();
        $this->info('Added 7-day itinerary for Complete Silk Road Heritage');

        // Add itinerary for Khiva and Beyond (4 days)
        $this->addKhivaFourDayItinerary();
        $this->info('Added 4-day itinerary for Khiva and Beyond');

        $this->info('All itineraries have been added successfully!');
        return 0;
    }

    private function updateKhivaTour()
    {
        $tour = Tour::where('slug', 'khiva-and-beyond-fortresses-and-legends')->first();
        if ($tour) {
            $tour->update([
                'duration_days' => 4,
                'duration_text' => '4 Days / 3 Nights',
                'price_per_person' => 450.00
            ]);
        }
    }

    private function addCompleteSilkRoadItinerary()
    {
        $tour = Tour::where('slug', 'complete-silk-road-heritage-7-day-comprehensive-tour')->first();
        if (!$tour) {
            $this->error('Complete Silk Road tour not found');
            return;
        }

        // Get cities
        $tashkent = City::where('name', 'Tashkent')->first();
        $samarkand = City::where('name', 'Samarkand')->first();
        $bukhara = City::where('name', 'Bukhara')->first();
        $khiva = City::where('name', 'Khiva')->first();

        $itineraries = [
            // Day 1
            [
                'tour_id' => $tour->id,
                'day_number' => 1,
                'city' => $tashkent,
                'title' => 'Arrival in Tashkent - Modern Capital City',
                'activities' => 'Airport pickup and hotel check-in. Afternoon city tour including Independence Square, Hazrat Imam Complex, and Tashkent Metro stations. Evening welcome dinner with traditional Uzbek cuisine.',
                'meals' => 'Dinner',
                'accommodation' => 'Hotel in Tashkent',
                'transport' => 'Private van',
                'sort_order' => 1
            ],
            // Day 2
            [
                'tour_id' => $tour->id,
                'day_number' => 2,
                'city' => $samarkand,
                'title' => 'Samarkand - Jewel of the Silk Road',
                'activities' => 'Morning high-speed train to Samarkand (2 hours). Full day exploring Registan Square, Gur-e-Amir Mausoleum, Shah-i-Zinda Necropolis, and Bibi-Khanym Mosque. Evening at leisure in the old city.',
                'meals' => 'Breakfast, Dinner',
                'accommodation' => 'Hotel in Samarkand',
                'transport' => 'High-speed train + walking',
                'sort_order' => 2
            ],
            // Day 3
            [
                'tour_id' => $tour->id,
                'day_number' => 3,
                'city' => $samarkand,
                'title' => 'Samarkand - Observatory and Paper Workshop',
                'activities' => 'Morning visit to Ulugh Beg Observatory. Afternoon workshop at Samarkand Paper Making Center. Explore local bazaars and interact with local artisans. Evening train to Bukhara.',
                'meals' => 'Breakfast, Lunch',
                'accommodation' => 'Hotel in Bukhara',
                'transport' => 'Walking + Train',
                'sort_order' => 3
            ],
            // Day 4
            [
                'tour_id' => $tour->id,
                'day_number' => 4,
                'city' => $bukhara,
                'title' => 'Bukhara - Trading Center of the Silk Road',
                'activities' => 'Full day in Bukhara: Ark Fortress, Kalyan Minaret, Poi Kalyan Complex, Samanid Mausoleum, and Lab-i Hauz complex. Visit traditional craft workshops including carpet weaving and embroidery.',
                'meals' => 'Breakfast, Dinner',
                'accommodation' => 'Hotel in Bukhara',
                'transport' => 'Walking + Private van',
                'sort_order' => 4
            ],
            // Day 5
            [
                'tour_id' => $tour->id,
                'day_number' => 5,
                'city' => $bukhara,
                'title' => 'Bukhara - Trading Domes and Dinner with Locals',
                'activities' => 'Morning visit to Trading Domes. Lunch with a local family to experience authentic home-cooked Uzbek cuisine. Afternoon at Chor Minar or continue exploring favorite monuments. Evening free for shopping or relaxation.',
                'meals' => 'Breakfast, Lunch, Dinner',
                'accommodation' => 'Hotel in Bukhara',
                'transport' => 'Walking',
                'sort_order' => 5
            ],
            // Day 6
            [
                'tour_id' => $tour->id,
                'day_number' => 6,
                'city' => $khiva,
                'title' => 'Khiva - Inside the Walled City',
                'activities' => 'Drive to Khiva (5 hours through desert). Afternoon tour of Ichan-Kala (inner walled city): Kunya Ark, Islam Khoja Minaret, and Pakhlavan Makhmud Mausoleum. Overnight inside the historic walls.',
                'meals' => 'Breakfast, Dinner',
                'accommodation' => 'Hotel in Khiva',
                'transport' => 'Private van',
                'sort_order' => 6
            ],
            // Day 7
            [
                'tour_id' => $tour->id,
                'day_number' => 7,
                'city' => $khiva,
                'title' => 'Khiva - Departure Day',
                'activities' => 'Morning free time to revisit favorite spots or purchase souvenirs. After lunch, transfer to Urgench airport or drive back to Tashkent (optional, 12 hours with stops). End of tour.',
                'meals' => 'Breakfast, Lunch',
                'accommodation' => 'Check-out',
                'transport' => 'Private van or Flight',
                'sort_order' => 7
            ]
        ];

        foreach ($itineraries as $item) {
            ItineraryItem::create([
                'tour_id' => $item['tour_id'],
                'day_number' => $item['day_number'],
                'city_id' => $item['city']->id,
                'title' => $item['title'],
                'description' => $item['activities'],
                'meals' => $item['meals'],
                'accommodation' => $item['accommodation'],
                'transport' => $item['transport'],
                'sort_order' => $item['sort_order'],
            ]);

            $this->line("  ✓ Added Day {$item['day_number']}: {$item['title']}");
        }
    }

    private function addKhivaFourDayItinerary()
    {
        $tour = Tour::where('slug', 'khiva-and-beyond-fortresses-and-legends')->first();
        if (!$tour) {
            $this->error('Khiva tour not found');
            return;
        }

        // Get cities
        $khiva = City::where('name', 'Khiva')->first();

        $itineraries = [
            // Day 1
            [
                'tour_id' => $tour->id,
                'day_number' => 1,
                'city' => $khiva,
                'title' => 'Arrival and Ichan-Kala Exploration',
                'activities' => 'Arrive in Khiva and check into hotel inside Ichan-Kala. Afternoon guided tour of the inner walled city: Kunya Ark (Old Fortress), Khiva is oldest structure; Islam Khoja Minaret and Medressa; Pakhlavan Makhmud Complex. Sunset photography from the city walls.',
                'meals' => 'Dinner',
                'accommodation' => 'Hotel inside Ichan-Kala',
                'transport' => 'Walking',
                'sort_order' => 1
            ],
            // Day 2
            [
                'tour_id' => $tour->id,
                'day_number' => 2,
                'city' => $khiva,
                'title' => 'Palace Complex and Traditional Crafts',
                'activities' => 'Morning visit to Tash Khaorov Palace and Harem. Lunch at a traditional teahouse. Afternoon workshop with local craftsmen - pottery and woodcarving. Evening free to explore the bazaar and try local sweets.',
                'meals' => 'Breakfast, Lunch, Dinner',
                'accommodation' => 'Hotel inside Ichan-Kala',
                'transport' => 'Walking',
                'sort_order' => 2
            ],
            // Day 3
            [
                'tour_id' => $tour->id,
                'day_number' => 3,
                'city' => $khiva,
                'title' => 'Ayaz-Kala Desert Fortresses',
                'activities' => 'Full day excursion to the Ayaz-Kala fortresses (1.5 hours drive). Explore three ancient fortresses dating from the 7th-12th centuries. Picnic lunch with traditional nomadic food. Learn about ancient desert life and the Great Silk Road routes. Sunset views from fortress walls.',
                'meals' => 'Breakfast, Lunch, Dinner',
                'accommodation' => 'Hotel inside Ichan-Kala',
                'transport' => '4WD vehicle',
                'sort_order' => 3
            ],
            // Day 4
            [
                'tour_id' => $tour->id,
                'day_number' => 4,
                'city' => $khiva,
                'title' => 'Toprak-Kala and Departure',
                'activities' => 'Morning visit to Toprak-Kala archaeological site - one of the best-preserved ancient cities in Central Asia. Explore ruins and museum. Lunch back in Khiva. Afternoon free for last-minute shopping or another visit to favorite monuments. Transfer to airport or next destination.',
                'meals' => 'Breakfast, Lunch',
                'accommodation' => 'Check-out',
                'transport' => 'Private van',
                'sort_order' => 4
            ]
        ];

        foreach ($itineraries as $item) {
            ItineraryItem::create([
                'tour_id' => $item['tour_id'],
                'day_number' => $item['day_number'],
                'city_id' => $item['city']->id,
                'title' => $item['title'],
                'description' => $item['activities'],
                'meals' => $item['meals'],
                'accommodation' => $item['accommodation'],
                'transport' => $item['transport'],
                'sort_order' => $item['sort_order'],
            ]);

            $this->line("  ✓ Added Day {$item['day_number']}: {$item['title']}");
        }
    }
}
