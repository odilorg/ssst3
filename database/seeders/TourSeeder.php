<?php

namespace Database\Seeders;

use App\Models\Tour;
use App\Models\City;
use Illuminate\Database\Seeder;

class TourSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get or create Samarkand city
        $samarkand = City::firstOrCreate(
            ['name' => 'Samarkand'],
            ['description' => 'Ancient city on the Silk Road']
        );

        $tashkent = City::firstOrCreate(
            ['name' => 'Tashkent'],
            ['description' => 'Capital of Uzbekistan']
        );

        $tours = [
            // 1. SINGLE-DAY TOUR (4 hours)
            [
                'title' => 'Samarkand City Tour: Registan Square and Historic Monuments',
                'slug' => 'samarkand-city-tour',
                'short_description' => 'Explore UNESCO World Heritage sites including Registan Square, Shah-i-Zinda necropolis, and Bibi-Khanym Mosque',
                'long_description' => '<p>Come and spend your day discovering the beauty, history, and culture of Samarkand, one of the oldest continuously inhabited cities in Central Asia. This comprehensive walking tour takes you through the heart of this ancient Silk Road city.</p>

<p>Our journey begins at the magnificent Registan Square, the centerpiece of Samarkand and one of the most iconic architectural ensembles in the Islamic world. You\'ll explore three grand madrasahs dating from the 15th-17th centuries.</p>

<p>Next, we\'ll visit the Shah-i-Zinda necropolis, a stunning collection of mausoleums featuring some of the finest tile work in the Islamic world.</p>

<p>Finally, we\'ll explore the grand Bibi-Khanym Mosque, once one of the largest mosques in the Islamic world, built by Timur (Tamerlane) after his Indian campaign.</p>',

                // Duration
                'duration_days' => 1,
                'duration_text' => '4 hours',

                // Pricing
                'price_per_person' => 50.00,
                'currency' => 'USD',

                // Capacity
                'max_guests' => 10,
                'min_guests' => 1,

                // Images
                'hero_image' => 'images/tours/samarkand/hero-main.webp',
                'gallery_images' => [
                    ['path' => 'images/tours/samarkand/registan-1.webp', 'alt' => 'Registan Square detail view'],
                    ['path' => 'images/tours/samarkand/shah-i-zinda.webp', 'alt' => 'Shah-i-Zinda necropolis blue domes'],
                    ['path' => 'images/tours/samarkand/bibi-khanym.webp', 'alt' => 'Bibi-Khanym Mosque entrance'],
                    ['path' => 'images/tours/samarkand/street-view.webp', 'alt' => 'Traditional street in old Samarkand'],
                ],

                // Content
                'highlights' => [
                    'Explore the legendary Registan Square with three magnificent madrasahs from the 15th-17th centuries',
                    'Walk through the stunning Shah-i-Zinda necropolis with its corridor of azure blue domes',
                    'Visit the grand Bibi-Khanym Mosque, once among the largest mosques in the Islamic world',
                    'Learn about Timur\'s empire and the Silk Road trade from an expert local guide',
                    'Discover intricate tile work, geometric patterns, and Persian-Turkic architectural fusion',
                    'Enjoy plenty of time for photography at UNESCO World Heritage sites',
                ],

                'included_items' => [
                    'Hotel pickup and drop-off (Samarkand city hotels)',
                    'Professional English-speaking guide',
                    'Entrance fees to all monuments (Registan, Shah-i-Zinda, Bibi-Khanym)',
                    'Bottled water',
                    'Small group tour (max 10 guests)',
                ],

                'excluded_items' => [
                    'Tips and gratuities for guide (optional)',
                    'Lunch (available for purchase at local restaurants)',
                    'Personal expenses and souvenirs',
                    'Photography fees inside certain monuments (if applicable)',
                ],

                'languages' => ['English', 'Russian', 'French'],

                'requirements' => [
                    'Moderate walking - approximately 3km total',
                    'Dress modestly (shoulders and knees covered)',
                    'Not wheelchair accessible',
                    'Bring comfortable walking shoes',
                    'Bring sun protection (hat, sunscreen)',
                    'Bring local currency for tips and purchases',
                ],

                // Tour Meta
                'tour_type' => 'private',
                'city_id' => $samarkand->id,
                'is_active' => true,

                // Ratings
                'rating' => 5.0,
                'review_count' => 47,

                // Booking Settings
                'min_booking_hours' => 24,
                'has_hotel_pickup' => true,
                'pickup_radius_km' => 5,

                // Meeting Point
                'meeting_point_address' => 'Registan Square West Gate, Registan Street, Samarkand 140100, Uzbekistan',
                'meeting_instructions' => 'If you prefer, you can meet us at Registan Square West Gate at 09:30 AM. Look for your guide holding a "Jahongir Travel" sign.',
                'meeting_lat' => 39.6542,
                'meeting_lng' => 66.9597,

                // Cancellation
                'cancellation_hours' => 24,
                'cancellation_policy' => 'Free cancellation up to 24 hours before the tour start time. For a full refund, cancel at least 24 hours before the scheduled departure time. Weather-dependent: If canceled due to poor weather, you\'ll be offered a different date or a full refund.',
            ],

            // 2. MULTI-DAY TOUR (5 days)
            [
                'title' => '5-Day Silk Road Classic: Tashkent, Samarkand & Bukhara',
                'slug' => '5-day-silk-road-classic',
                'short_description' => 'Discover the highlights of Uzbekistan\'s ancient Silk Road cities with expert guides',
                'long_description' => '<p>Experience the magic of the Silk Road on this comprehensive 5-day journey through Uzbekistan\'s most iconic cities. From the modern capital Tashkent to the ancient marvels of Samarkand and Bukhara, this tour offers the perfect introduction to Central Asian culture.</p>

<p>You\'ll explore UNESCO World Heritage sites, stay in comfortable hotels, enjoy authentic Uzbek cuisine, and learn about the rich history that shaped this crossroads of civilizations.</p>',

                // Duration
                'duration_days' => 5,
                'duration_text' => '5 Days / 4 Nights',

                // Pricing
                'price_per_person' => 890.00,
                'currency' => 'USD',

                // Capacity
                'max_guests' => 12,
                'min_guests' => 2,

                // Images
                'hero_image' => 'images/tours/silk-road/hero-main.webp',
                'gallery_images' => [
                    ['path' => 'images/tours/silk-road/tashkent.webp', 'alt' => 'Tashkent city center'],
                    ['path' => 'images/tours/silk-road/samarkand-registan.webp', 'alt' => 'Registan Square at sunset'],
                    ['path' => 'images/tours/silk-road/bukhara-ark.webp', 'alt' => 'Bukhara Ark Fortress'],
                    ['path' => 'images/tours/silk-road/train.webp', 'alt' => 'High-speed train between cities'],
                ],

                // Content
                'highlights' => [
                    'Visit three UNESCO World Heritage sites across Uzbekistan',
                    'Explore the magnificent Registan Square in Samarkand',
                    'Discover the ancient city of Bukhara with over 140 architectural monuments',
                    'Experience modern Tashkent and its blend of Soviet and Islamic architecture',
                    'Travel by comfortable high-speed train between cities',
                    'Stay in 4-star hotels with breakfast included',
                    'Enjoy authentic Uzbek dinners and traditional plov',
                    'Small group tour with maximum 12 participants',
                ],

                'included_items' => [
                    'Airport pickup and drop-off',
                    '4 nights accommodation in 4-star hotels',
                    'Daily breakfast at hotels',
                    '3 traditional Uzbek dinners',
                    'High-speed train tickets (Tashkent-Samarkand-Bukhara)',
                    'All entrance fees to monuments and museums',
                    'Professional English-speaking guide throughout',
                    'Air-conditioned transportation',
                ],

                'excluded_items' => [
                    'International flights',
                    'Visa fees (if applicable)',
                    'Travel insurance',
                    'Lunches',
                    'Personal expenses',
                    'Tips for guides and drivers',
                ],

                'languages' => ['English', 'Russian', 'German', 'Spanish'],

                'requirements' => [
                    'Moderate fitness level required',
                    'Valid passport with 6 months validity',
                    'Comfortable walking shoes essential',
                    'Dress respectfully when visiting mosques',
                    'Suitable for ages 12 and above',
                ],

                // Tour Meta
                'tour_type' => 'group',
                'city_id' => $tashkent->id,
                'is_active' => true,

                // Ratings
                'rating' => 4.8,
                'review_count' => 124,

                // Booking Settings
                'min_booking_hours' => 72, // 3 days advance
                'has_hotel_pickup' => true,
                'pickup_radius_km' => 10,

                // Meeting Point
                'meeting_point_address' => 'Tashkent International Airport, Arrival Hall',
                'meeting_instructions' => 'Our guide will meet you at the airport arrival hall holding a sign with your name.',
                'meeting_lat' => 41.2995,
                'meeting_lng' => 69.2401,

                // Cancellation
                'cancellation_hours' => 72,
                'cancellation_policy' => 'Free cancellation up to 72 hours (3 days) before tour start. Cancellations within 72 hours: 50% refund. No refund within 24 hours of tour start.',
            ],

            // 3. SINGLE-DAY TOUR (8 hours)
            [
                'title' => 'Full Day Bukhara City Tour',
                'slug' => 'full-day-bukhara-city-tour',
                'short_description' => 'Comprehensive exploration of Bukhara\'s 140+ architectural monuments',
                'long_description' => '<p>Spend a full day exploring Bukhara, one of Central Asia\'s most complete medieval cities. With over 140 architectural monuments, Bukhara is a living museum of Islamic architecture.</p>',

                // Duration
                'duration_days' => 1,
                'duration_text' => '8 hours (Full Day)',

                // Pricing
                'price_per_person' => 75.00,
                'currency' => 'USD',

                // Capacity
                'max_guests' => 8,
                'min_guests' => 1,

                // Images
                'hero_image' => 'images/tours/bukhara/hero-main.webp',
                'gallery_images' => [],

                // Content
                'highlights' => [
                    'Visit the ancient Ark Fortress (5th century)',
                    'Explore the Poi Kalyan complex with its 47m minaret',
                    'Discover the Lyab-i Hauz ensemble and its reflecting pool',
                    'Shop in historic trading domes (toki)',
                    'Visit the summer palace of the last Emir',
                ],

                'included_items' => [
                    'Hotel pickup and drop-off',
                    'Professional guide',
                    'All entrance fees',
                    'Traditional lunch',
                    'Water and snacks',
                ],

                'excluded_items' => [
                    'Personal expenses',
                    'Tips',
                    'Shopping purchases',
                ],

                'languages' => ['English', 'Russian'],

                'requirements' => [
                    'Full day walking tour',
                    'Comfortable shoes required',
                    'Modest dress code',
                ],

                // Tour Meta
                'tour_type' => 'private',
                'city_id' => $samarkand->id, // Using Samarkand as placeholder
                'is_active' => true,

                // Ratings
                'rating' => 4.9,
                'review_count' => 83,

                // Booking Settings
                'min_booking_hours' => 24,
                'has_hotel_pickup' => true,
                'pickup_radius_km' => 5,

                // Meeting Point
                'meeting_point_address' => 'Lyab-i Hauz, Bukhara Old Town',
                'meeting_instructions' => 'Meet at the Lyab-i Hauz reflecting pool at 9:00 AM',
                'meeting_lat' => 39.7750,
                'meeting_lng' => 64.4225,

                // Cancellation
                'cancellation_hours' => 24,
                'cancellation_policy' => 'Free cancellation up to 24 hours before tour start.',
            ],
        ];

        foreach ($tours as $tourData) {
            Tour::updateOrCreate(
                ['slug' => $tourData['slug']],
                $tourData
            );
        }
    }
}
