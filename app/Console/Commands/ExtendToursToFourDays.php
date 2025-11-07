<?php

namespace App\Console\Commands;

use App\Models\Tour;
use App\Models\ItineraryItem;
use App\Models\City;
use Illuminate\Console\Command;

class ExtendToursToFourDays extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'extend:tours';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Convert 1-day tours to 4+ days and add detailed itineraries';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting to extend tours to 4+ days with detailed itineraries...');

        $this->extendSamarkandTour();
        $this->info('✓ Extended Golden Ring of Samarkand to 4 days');

        $this->extendChimganTour();
        $this->info('✓ Extended Chimgan Mountains to 4 days');

        $this->extendBukharaFamilyTour();
        $this->info('✓ Extended Bukhara for Families to 4 days');

        $this->extendTashkentTour();
        $this->info('✓ Extended Tashkent Modern to 4 days');

        $this->extendCulinaryTour();
        $this->info('✓ Extended Samarkand Culinary & Craft to 4 days');

        $this->info('All tours have been successfully extended to 4+ days!');
        return 0;
    }

    private function extendSamarkandTour()
    {
        $tour = Tour::where('slug', 'golden-ring-of-samarkand-a-historical-journey')->first();
        if (!$tour) return;

        $tour->update([
            'duration_days' => 4,
            'duration_text' => '4 Days / 3 Nights',
            'price_per_person' => 320.00
        ]);

        $samarkand = City::where('name', 'Samarkand')->first();

        $itineraries = [
            [
                'tour_id' => $tour->id,
                'day_number' => 1,
                'city_id' => $samarkand->id,
                'title' => 'Registan Square and Gur-e-Amir',
                'description' => 'Morning: Registan Square exploration - Ulugh Beg Madrasah, Tilya-Kori Madrasah, Sher-Dor Madrasah with detailed history and photo opportunities. Afternoon: Visit Gur-e-Amir Mausoleum (Timur\'s tomb), learn about Timurid dynasty and architectural innovations. Evening: Welcome dinner with traditional Samarkand cuisine and local wine tasting.',
                'meals' => 'Dinner',
                'accommodation' => 'Hotel in Samarkand',
                'transport' => 'Walking + Private van',
                'sort_order' => 1
            ],
            [
                'tour_id' => $tour->id,
                'day_number' => 2,
                'city_id' => $samarkand->id,
                'title' => 'Shah-i-Zinda and Bibi-Khanym',
                'description' => 'Morning: Shah-i-Zinda Necropolis - explore the avenue of mystical mausoleums, learn about Islamic burial traditions, photograph the stunning blue tilework. Afternoon: Bibi-Khanym Mosque - once among the largest mosques in the Islamic world, see the massive bronze candelabrum and learn the love story behind its construction. Evening: Shopping at Samarkand bazaar for authentic silk scarves and local crafts.',
                'meals' => 'Breakfast, Dinner',
                'accommodation' => 'Hotel in Samarkand',
                'transport' => 'Walking',
                'sort_order' => 2
            ],
            [
                'tour_id' => $tour->id,
                'day_number' => 3,
                'city_id' => $samarkand->id,
                'title' => 'Ulugh Beg Observatory and Afrosiyab',
                'description' => 'Morning: Ulugh Beg Observatory - discover the medieval astronomical achievements, see the remains of the giant sextant, learn about the Timurid renaissance in science. Afternoon: Afrosiyab ancient city - explore archaeological site and museum, see ancient murals and artifacts from pre-Mongol Samarkand. Evening: Traditional music concert featuring Uzbek classical music and folk songs.',
                'meals' => 'Breakfast, Lunch, Dinner',
                'accommodation' => 'Hotel in Samarkand',
                'transport' => 'Walking + Private van',
                'sort_order' => 3
            ],
            [
                'tour_id' => $tour->id,
                'day_number' => 4,
                'city_id' => $samarkand->id,
                'title' => 'Mausoleum of the Conqueror and Departure',
                'description' => 'Morning: Mausoleum of Saint Daniel and other monuments - pay respects at this sacred site, also visit nearby memorial complexes. Afternoon: Final shopping at local workshops - meet master craftsmen making traditional Samarkand paper, pottery, and woodcarving. After tea ceremony, transfer to train station or airport. End of tour.',
                'meals' => 'Breakfast, Lunch',
                'accommodation' => 'Check-out',
                'transport' => 'Walking + Private van',
                'sort_order' => 4
            ]
        ];

        foreach ($itineraries as $item) {
            ItineraryItem::create($item);
            $this->line("  → Added Day {$item['day_number']}: {$item['title']}");
        }
    }

    private function extendChimganTour()
    {
        $tour = Tour::where('slug', 'chimgan-mountains-hiking-and-scenic-beauty')->first();
        if (!$tour) return;

        $tour->update([
            'duration_days' => 4,
            'duration_text' => '4 Days / 3 Nights',
            'price_per_person' => 380.00
        ]);

        $chimgan = City::where('name', 'Chimgan Mountains')->first();

        $itineraries = [
            [
                'tour_id' => $tour->id,
                'day_number' => 1,
                'city_id' => $chimgan->id,
                'title' => 'Arrival and Pichkak Valley',
                'description' => 'Early morning drive from Tashkent to Chimgan Mountains (2 hours). Check into mountain guesthouse. Afternoon easy hike in Pichkak Valley - acclimatization walk with stunning views of the Alay mountains. Learn about local flora and fauna. Evening: BBQ dinner with nomadic family, traditional music, and stargazing.',
                'meals' => 'Lunch, Dinner',
                'accommodation' => 'Mountain guesthouse',
                'transport' => '4WD vehicle + Walking',
                'sort_order' => 1
            ],
            [
                'tour_id' => $tour->id,
                'day_number' => 2,
                'city_id' => $chimgan->id,
                'title' => 'Big Chimgan Peak Ascent',
                'description' => 'Full day hiking to Big Chimgan Peak (3309m) - challenging but rewarding trek with professional mountain guide. Learn mountaineering basics, wilderness survival skills. Picnic lunch on the mountain. Afternoon descent to guesthouse. Evening: Hot spring soak and dinner by the fireplace.',
                'meals' => 'Breakfast, Lunch, Dinner',
                'accommodation' => 'Mountain guesthouse',
                'transport' => 'Hiking',
                'sort_order' => 2
            ],
            [
                'tour_id' => $tour->id,
                'day_number' => 3,
                'city_id' => $chimgan->id,
                'title' => 'Panjshir Valley and Traditional Life',
                'description' => 'Full day exploration of Panjshir Valley - easier hiking to meadow areas with incredible panoramic views. Visit a local shepherd family, learn about transhumance (seasonal migration), help with sheep milking and cheese making. Traditional lunch of fresh dairy products and mountain herbs. Afternoon: Photography workshop focusing on landscape and wildlife photography.',
                'meals' => 'Breakfast, Lunch, Dinner',
                'accommodation' => 'Mountain guesthouse',
                'transport' => '4WD vehicle + Walking',
                'sort_order' => 3
            ],
            [
                'tour_id' => $tour->id,
                'day_number' => 4,
                'city_id' => $chimgan->id,
                'title' => 'Beldersay Canyon and Departure',
                'description' => 'Morning visit to Beldersay Canyon - explore dramatic rock formations and natural bridges. Optional rock climbing or rappelling for adventure enthusiasts. Visit local village school and interact with children. Lunch with village family. Afternoon return drive to Tashkent with stops for scenic photos. End of tour.',
                'meals' => 'Breakfast, Lunch',
                'accommodation' => 'Check-out',
                'transport' => '4WD vehicle + Walking',
                'sort_order' => 4
            ]
        ];

        foreach ($itineraries as $item) {
            ItineraryItem::create($item);
            $this->line("  → Added Day {$item['day_number']}: {$item['title']}");
        }
    }

    private function extendBukharaFamilyTour()
    {
        $tour = Tour::where('slug', 'bukhara-for-families-interactive-historical-adventure')->first();
        if (!$tour) return;

        $tour->update([
            'duration_days' => 4,
            'duration_text' => '4 Days / 3 Nights',
            'price_per_person' => 280.00
        ]);

        $bukhara = City::where('name', 'Bukhara')->first();

        $itineraries = [
            [
                'tour_id' => $tour->id,
                'day_number' => 1,
                'city_id' => $bukhara->id,
                'title' => 'Ark Fortress and Family Quest',
                'description' => 'Morning: Family-oriented tour of Ark Fortress - kids treasure hunt with clues leading to different parts of the fortress, learn about medieval rulers. Interactive storytelling session with costumed guides. Afternoon: Family photos in traditional Uzbek costumes for all ages. Evening: Dinner at family-friendly restaurant with kids menu and entertainment (puppet show).',
                'meals' => 'Dinner',
                'accommodation' => 'Hotel in Bukhara',
                'transport' => 'Walking',
                'sort_order' => 1
            ],
            [
                'tour_id' => $tour->id,
                'day_number' => 2,
                'city_id' => $bukhara->id,
                'title' => 'Kalyan Minaret and Craft Workshops',
                'description' => 'Morning: Visit Kalyan Minaret - safe climb for families, panoramic views of old city, learn about the minaret\'s 800-year history. Afternoon: Hands-on craft workshops - kids and parents try pottery, carpet weaving, and woodcarving. Take home your creations. Evening: Ice cream at historic cafes and browsing toy bazaar.',
                'meals' => 'Breakfast, Dinner',
                'accommodation' => 'Hotel in Bukhara',
                'transport' => 'Walking + Private van',
                'sort_order' => 2
            ],
            [
                'tour_id' => $tour->id,
                'day_number' => 3,
                'city_id' => $bukhara->id,
                'title' => 'Samanid Mausoleum and Lab-i Hauz',
                'description' => 'Morning: Samanid Mausoleum - architectural scavenger hunt for kids (find different geometric patterns), learn about the founder of Samanid dynasty. Afternoon: Lab-i Hauz complex - relax by the pond, feed ducks, enjoy ice cream. Poetry and music session where kids can learn Uzbek folk songs. Evening: Family game night with traditional Uzbek games and puzzles.',
                'meals' => 'Breakfast, Lunch, Dinner',
                'accommodation' => 'Hotel in Bukhara',
                'transport' => 'Walking',
                'sort_order' => 3
            ],
            [
                'tour_id' => $tour->id,
                'day_number' => 4,
                'city_id' => $bukhara->id,
                'title' => 'Trading Domes and Farewell',
                'description' => 'Morning: Visit Trading Domes - kids barter challenge (trade stickers for small souvenirs with local merchants), learn about ancient trading traditions. Lunch with local family - children learn to make plov and simple Uzbek dishes. Afternoon: Final souvenir shopping for kids (wooden toys, books, local sweets). Free time for parents. Departure.',
                'meals' => 'Breakfast, Lunch',
                'accommodation' => 'Check-out',
                'transport' => 'Walking + Private van',
                'sort_order' => 4
            ]
        ];

        foreach ($itineraries as $item) {
            ItineraryItem::create($item);
            $this->line("  → Added Day {$item['day_number']}: {$item['title']}");
        }
    }

    private function extendTashkentTour()
    {
        $tour = Tour::where('slug', 'tashkent-modern-a-city-walking-tour')->first();
        if (!$tour) return;

        $tour->update([
            'duration_days' => 4,
            'duration_text' => '4 Days / 3 Nights',
            'price_per_person' => 240.00
        ]);

        $tashkent = City::where('name', 'Tashkent')->first();

        $itineraries = [
            [
                'tour_id' => $tour->id,
                'day_number' => 1,
                'city_id' => $tashkent->id,
                'title' => 'Independence Square and Metro',
                'description' => 'Morning: Independence Square (Mustaqillik Maydoni) - see government buildings, presidential palace, eternal flame. Learn about Uzbekistan independence story. Afternoon: Tashkent Metro tour - ride and photograph all three lines, visit most beautiful stations (Kosmonavtlar, Chilanzar, Minor), learn metro art and Soviet architecture. Evening: Dinner at local restaurant and walk through night market.',
                'meals' => 'Dinner',
                'accommodation' => 'Hotel in Tashkent',
                'transport' => 'Metro + Walking',
                'sort_order' => 1
            ],
            [
                'tour_id' => $tour->id,
                'day_number' => 2,
                'city_id' => $tashkent->id,
                'title' => 'Modern Tashkent and Chorsu Bazaar',
                'description' => 'Morning: Explore modern business district - Amir Timur Street, Hyatt Hotel, guaranteed shopping center, banking district. Learn about Uzbekistan economic development. Afternoon: Chorsu Bazaar (New Section) - huge covered market with local produce, spices, textiles. Modern version of traditional bazaar. Evening: Visit Mall of Tashkent, the largest shopping center in Central Asia.',
                'meals' => 'Breakfast, Dinner',
                'accommodation' => 'Hotel in Tashkent',
                'transport' => 'Metro + Walking + Taxi',
                'sort_order' => 2
            ],
            [
                'tour_id' => $tour->id,
                'day_number' => 3,
                'city_id' => $tashkent->id,
                'title' => 'Hazrat Imam and Old City',
                'description' => 'Morning: Hazrat Imam Complex - explore the spiritual center of Tashkent, see Barak Khan Madrasah, Tilla Sheikh Mosque, Kaffal Shashi Mausoleum, and the oldest Quran in the world. Afternoon: Minor Mosque (Small Mosque) and adjacent park - perfect for photography and rest. Visit local art galleries and craft centers. Evening: Traditional tea ceremony with local poets and musicians.',
                'meals' => 'Breakfast, Lunch, Dinner',
                'accommodation' => 'Hotel in Tashkent',
                'transport' => 'Metro + Walking',
                'sort_order' => 3
            ],
            [
                'tour_id' => $tour->id,
                'day_number' => 4,
                'city_id' => $tashkent->id,
                'title' => 'Museums and Departure',
                'description' => 'Morning: State Museum of History - comprehensive overview of Uzbekistan history from ancient times to present. Interactive exhibits and modern presentations. Afternoon: Fine Arts Museum of Uzbekistan - see works by local artists, visit craft shop for authentic souvenirs. Last-minute shopping at local boutiques. Free time in the city center. Departure.',
                'meals' => 'Breakfast, Lunch',
                'accommodation' => 'Check-out',
                'transport' => 'Metro + Walking + Taxi',
                'sort_order' => 4
            ]
        ];

        foreach ($itineraries as $item) {
            ItineraryItem::create($item);
            $this->line("  → Added Day {$item['day_number']}: {$item['title']}");
        }
    }

    private function extendCulinaryTour()
    {
        $tour = Tour::where('slug', 'samarkand-culinary-craft-heritage')->first();
        if (!$tour) return;

        $tour->update([
            'duration_days' => 4,
            'duration_text' => '4 Days / 3 Nights',
            'price_per_person' => 420.00
        ]);

        $samarkand = City::where('name', 'Samarkand')->first();

        $itineraries = [
            [
                'tour_id' => $tour->id,
                'day_number' => 1,
                'city_id' => $samarkand->id,
                'title' => 'Plov Masterclass and Registan',
                'description' => 'Morning: Traditional plov (osh) cooking masterclass - learn to make Uzbekistan national dish, visit local bazaar to buy ingredients, cook with master chef. Afternoon: Registan Square visit with full guided tour - appreciate what you learned about Islamic architecture and tilework. Evening: Enjoy the plov you cooked with local musicians and dancers.',
                'meals' => 'Lunch, Dinner',
                'accommodation' => 'Hotel in Samarkand',
                'transport' => 'Walking + Cooking class',
                'sort_order' => 1
            ],
            [
                'tour_id' => $tour->id,
                'day_number' => 2,
                'city_id' => $samarkand->id,
                'title' => 'Bread Baking and Samsa Workshop',
                'description' => 'Morning: Visit tandoor (clay oven) bakery - learn about bread (non) making traditions, see how samsa and somsa are baked. Hands-on workshop: shape and bake your own samsa and non. Afternoon: Visit to spice bazaar - learn about Central Asian spices, create your own spice blend to take home. Tea ceremony with local tea masters. Evening: Dinner with your freshly baked bread and soups.',
                'meals' => 'Breakfast, Lunch, Dinner',
                'accommodation' => 'Hotel in Samarkand',
                'transport' => 'Walking + Cooking',
                'sort_order' => 2
            ],
            [
                'tour_id' => $tour->id,
                'day_number' => 3,
                'city_id' => $samarkand->id,
                'title' => 'Paper Making and Pottery',
                'description' => 'Full day at Samarkand Paper Making Center - learn ancient technique of making paper from mulberry bark (UNESCO heritage). Create your own decorated paper. Afternoon: Pottery workshop - throw clay on the wheel, make bowls or plates, paint with traditional patterns. Take home all your creations, properly fired and glazed. Evening: Cooking sweet treats - halva, nishalda, and traditional sweets.',
                'meals' => 'Breakfast, Lunch, Dinner',
                'accommodation' => 'Hotel in Samarkand',
                'transport' => 'Private van + Workshop',
                'sort_order' => 3
            ],
            [
                'tour_id' => $tour->id,
                'day_number' => 4,
                'city_id' => $samarkand->id,
                'title' => 'Dumpling Making and Farewell',
                'description' => 'Morning: Manti (dumplings) workshop - learn to make perfect dumplings, steam and enjoy with yogurt sauce. Learn to make lagman noodles from scratch. Afternoon: Final shopping at artisan workshops - purchase your own handcrafted items, get recipe book with all dishes learned. Group lunch featuring all specialties. Certificate ceremony and departure.',
                'meals' => 'Breakfast, Lunch',
                'accommodation' => 'Check-out',
                'transport' => 'Walking + Cooking + Private van',
                'sort_order' => 4
            ]
        ];

        foreach ($itineraries as $item) {
            ItineraryItem::create($item);
            $this->line("  → Added Day {$item['day_number']}: {$item['title']}");
        }
    }
}

