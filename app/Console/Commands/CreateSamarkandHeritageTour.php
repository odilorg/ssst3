<?php

namespace App\Console\Commands;

use App\Models\City;
use App\Models\Tour;
use App\Models\TourCategory;
use Illuminate\Console\Command;

class CreateSamarkandHeritageTour extends Command
{
    protected $signature = 'create:samarkand-heritage-tour';
    protected $description = 'Create complete Samarkand Heritage UNESCO tour with all details';

    public function handle()
    {
        $this->info('Creating Samarkand Heritage Tour...');

        // Get required data
        $samarkand = City::where('name', 'Samarkand')->first();
        if (!$samarkand) {
            $this->error('Samarkand city not found!');
            return 1;
        }

        $tour = Tour::create([
            // BASIC INFORMATION
            'title' => 'Samarkand Heritage: Full-Day UNESCO Sites Explorer',
            'slug' => 'samarkand-heritage-full-day-unesco-explorer',
            'short_description' => 'Discover Samarkand\'s most iconic monuments in one comprehensive day - Registan, Shah-i-Zinda, Gur-e-Amir, and Bibi-Khanym with expert guide',
            'long_description' => '<h2>Explore the Pearl of the Silk Road</h2>
<p>Immerse yourself in the timeless beauty of Samarkand, where azure domes pierce the sky and intricate tilework tells stories of empires past. This carefully curated full-day tour takes you through the city\'s most spectacular UNESCO World Heritage sites, guided by local experts who bring history to life.</p>

<h3>Why This Tour?</h3>
<ul>
    <li><strong>Comprehensive Coverage:</strong> Visit all four major monuments in one perfectly paced day</li>
    <li><strong>Skip-the-Line Access:</strong> Pre-arranged tickets mean more time exploring, less time queuing</li>
    <li><strong>Expert Storytelling:</strong> Professional guides share fascinating historical context and local legends</li>
    <li><strong>Photo Opportunities:</strong> Strategic timing for the best natural light at each location</li>
    <li><strong>Cultural Immersion:</strong> Traditional tea ceremony and bazaar experience included</li>
</ul>

<h3>Perfect For</h3>
<p>History enthusiasts, architecture lovers, photographers, and first-time visitors to Samarkand wanting a thorough introduction to the city\'s treasures. Suitable for all ages and fitness levels.</p>',

            // DURATION & TYPE
            'duration_days' => 1,
            'duration_text' => '8 hours (9:00 AM - 5:00 PM)',
            'tour_type' => 'hybrid',
            'city_id' => $samarkand->id,
            'is_active' => true,

            // PRICING & CAPACITY
            'price_per_person' => 65.00,
            'currency' => 'USD',
            'max_guests' => 8,
            'min_guests' => 1,

            // IMAGES
            'hero_image' => 'images/tours/samarkand-heritage/registan-main.webp',
            'gallery_images' => json_encode([
                ['path' => 'images/tours/samarkand-heritage/registan-square.webp', 'alt' => 'Registan Square three madrasahs at golden hour'],
                ['path' => 'images/tours/samarkand-heritage/shah-i-zinda.webp', 'alt' => 'Shah-i-Zinda necropolis blue tilework'],
                ['path' => 'images/tours/samarkand-heritage/gur-e-amir.webp', 'alt' => 'Gur-e-Amir Mausoleum turquoise dome'],
                ['path' => 'images/tours/samarkand-heritage/bibi-khanym.webp', 'alt' => 'Bibi-Khanym Mosque grand entrance'],
            ]),

            // TOUR CONTENT
            'highlights' => ['Registan Square - Three stunning madrasahs from 15th-17th centuries', 'Shah-i-Zinda - Sacred avenue of 11 mesmerizing mausoleums', 'Gur-e-Amir - Magnificent tomb of Timur the Great', 'Bibi-Khanym Mosque - Once the largest mosque in Central Asia', 'Traditional tea ceremony in historic chaikhana', 'Siab Bazaar - Authentic local market experience', 'Professional photography assistance at each location'],

            'included_items' => ['Hotel pickup and drop-off (within 5km of city center)', 'English-speaking professional historian guide', 'All entrance fees to monuments (approx. $15 value)', 'Transportation in comfortable air-conditioned vehicle', 'Traditional Uzbek tea ceremony with fresh bread', 'Bottled water throughout the day'],

            'excluded_items' => ['Lunch (guide will recommend excellent local restaurants)', 'Personal expenses and souvenirs', 'Tips for guide and driver (appreciated but not required)', 'Travel insurance'],

            'languages' => ['English', 'Russian', 'French', 'German', 'Spanish'],

            'requirements' => [
                ['icon' => 'walking', 'title' => 'Moderate Walking Required', 'text' => 'This tour involves approximately 3-4 hours of walking on mostly flat surfaces. Comfortable walking shoes strongly recommended.'],
                ['icon' => 'tshirt', 'title' => 'Modest Dress Code', 'text' => 'When entering religious sites shoulders and knees must be covered. Women should bring a headscarf.'],
                ['icon' => 'sun', 'title' => 'Sun Protection Essential', 'text' => 'Samarkand can be very hot (35-40°C) in summer. Bring sunscreen, sunglasses, and a hat.'],
                ['icon' => 'camera', 'title' => 'Photography Allowed', 'text' => 'Photography permitted at all sites. Flash photography prohibited inside mausoleums.'],
            ],

            'include_global_requirements' => false,
            'include_global_faqs' => true,

            // MEETING POINT
            'meeting_point_address' => 'Your hotel lobby (if within 5km of city center) OR Registan Square main entrance',
            'meeting_instructions' => 'For hotel pickup: Wait in lobby 5 minutes before scheduled time. Driver will have "Samarkand Heritage Tour" sign. For Registan meeting: Stand at main ticket office entrance.',
            'meeting_lat' => 39.6542,
            'meeting_lng' => 66.9597,

            // BOOKING SETTINGS
            'min_booking_hours' => 12,
            'has_hotel_pickup' => true,
            'pickup_radius_km' => 5,
            'cancellation_hours' => 24,

            // RATINGS
            'rating' => 4.85,
            'review_count' => 142
        ]);

        // ATTACH CATEGORIES
        $tour->categories()->attach([1, 5]); // Cultural & Historical + City Walks

        // CREATE ITINERARY
        $itinerary = [
            ['title' => 'Hotel Pickup & Introduction', 'description' => '<p>Guide meets you at hotel. Brief introduction during drive to Registan Square.</p>', 'type' => 'stop', 'default_start_time' => '09:00', 'duration_minutes' => 20, 'sort_order' => 1],
            ['title' => 'Registan Square - The Heart of Samarkand', 'description' => '<p>Explore three magnificent madrasahs around central plaza. Learn about Timurid dynasty and Islamic education.</p>', 'type' => 'stop', 'default_start_time' => '09:20', 'duration_minutes' => 90, 'sort_order' => 2],
            ['title' => 'Shah-i-Zinda Necropolis', 'description' => '<p>Avenue of 11 stunning mausoleums with finest tilework in Islamic world. Climb 44 sacred steps.</p>', 'type' => 'stop', 'default_start_time' => '11:00', 'duration_minutes' => 75, 'sort_order' => 3],
            ['title' => 'Traditional Tea Break', 'description' => '<p>Rest at local chaikhana with traditional Uzbek tea ceremony and fresh bread.</p>', 'type' => 'stop', 'default_start_time' => '12:15', 'duration_minutes' => 30, 'sort_order' => 4],
            ['title' => 'Lunch Break (Own Expense)', 'description' => '<p>Free time for lunch at recommended restaurants. Guide provides suggestions.</p>', 'type' => 'stop', 'default_start_time' => '12:45', 'duration_minutes' => 60, 'sort_order' => 5],
            ['title' => 'Gur-e-Amir Mausoleum', 'description' => '<p>Timur\'s tomb with turquoise dome and gilded interior. Learn about legendary conqueror.</p>', 'type' => 'stop', 'default_start_time' => '13:45', 'duration_minutes' => 50, 'sort_order' => 6],
            ['title' => 'Bibi-Khanym Mosque', 'description' => '<p>Once largest mosque in Islamic world. Hear romantic legend and see ongoing restoration.</p>', 'type' => 'stop', 'default_start_time' => '14:35', 'duration_minutes' => 50, 'sort_order' => 7],
            ['title' => 'Siab Bazaar', 'description' => '<p>Authentic local market with spices, produce, crafts. Guide helps with bargaining.</p>', 'type' => 'stop', 'default_start_time' => '15:25', 'duration_minutes' => 40, 'sort_order' => 8],
            ['title' => 'Return to Hotel', 'description' => '<p>Relaxing drive back with final Q&A and evening recommendations.</p>', 'type' => 'stop', 'default_start_time' => '16:05', 'duration_minutes' => 25, 'sort_order' => 9],
        ];

        foreach ($itinerary as $item) {
            $tour->itineraryItems()->create($item);
        }

        // Update city tour count cache
        $tourCount = $samarkand->tours()->where('is_active', true)->count();
        $samarkand->tour_count_cache = $tourCount;
        $samarkand->save();

        $this->info("✅ Tour created successfully!");
        $this->info("Tour ID: {$tour->id}");
        $this->info("Title: {$tour->title}");
        $this->info("URL: http://127.0.0.1:8000/tours/{$tour->slug}");
        $this->info("Itinerary items: " . $tour->itineraryItems()->count());

        return 0;
    }
}
