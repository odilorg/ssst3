<?php

namespace Database\Seeders;

use App\Models\Tour;
use App\Models\Review;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get Samarkand City Tour
        $samarkandTour = Tour::where('slug', 'samarkand-city-tour')->first();
        if ($samarkandTour) {
            $reviews = [
                [
                    'tour_id' => $samarkandTour->id,
                    'reviewer_name' => 'Sarah Mitchell',
                    'reviewer_location' => 'United States',
                    'rating' => 5,
                    'title' => 'Absolutely breathtaking!',
                    'content' => 'Our guide was incredibly knowledgeable about the history of Samarkand and made every monument come alive with stories. The Registan Square at sunset was magical. Highly recommend this tour to anyone visiting Uzbekistan.',
                    'source' => 'website',
                    'is_verified' => false,
                    'is_approved' => true,
                    'created_at' => now()->subDays(15),
                ],
                [
                    'tour_id' => $samarkandTour->id,
                    'reviewer_name' => 'James Chen',
                    'reviewer_location' => 'Singapore',
                    'rating' => 5,
                    'title' => 'Perfect for photography enthusiasts',
                    'content' => 'Our guide knew all the best spots and angles for photos. The Shah-i-Zinda necropolis was stunning with its blue tiles. Great pace, not rushed at all. Worth every penny!',
                    'source' => 'website',
                    'is_verified' => false,
                    'is_approved' => true,
                    'created_at' => now()->subDays(22),
                ],
                [
                    'tour_id' => $samarkandTour->id,
                    'reviewer_name' => 'Emma Dubois',
                    'reviewer_location' => 'France',
                    'rating' => 5,
                    'title' => 'Unforgettable experience',
                    'content' => 'The guide spoke excellent French and English. The small group size meant we could ask lots of questions. Bibi-Khanym Mosque was incredible. Hotel pickup was punctual. Thank you, Jahongir Travel!',
                    'source' => 'website',
                    'is_verified' => false,
                    'is_approved' => true,
                    'created_at' => now()->subDays(34),
                ],
                [
                    'tour_id' => $samarkandTour->id,
                    'reviewer_name' => 'Michael O\'Brien',
                    'reviewer_location' => 'Australia',
                    'rating' => 5,
                    'title' => 'Best tour we did in Central Asia!',
                    'content' => 'The architecture is mind-blowing and our guide\'s passion for Samarkand\'s history was contagious. Four hours flew by. Great value for money. Would definitely book with Jahongir Travel again.',
                    'source' => 'website',
                    'is_verified' => false,
                    'is_approved' => true,
                    'created_at' => now()->subDays(48),
                ],
                [
                    'tour_id' => $samarkandTour->id,
                    'reviewer_name' => 'Ana Rodriguez',
                    'reviewer_location' => 'Spain',
                    'rating' => 5,
                    'title' => 'Highly recommend',
                    'content' => 'Perfect introduction to Samarkand! Our guide was friendly and knowledgeable. Learned so much about Silk Road history. The entrance fees being included was very convenient.',
                    'source' => 'tripadvisor',
                    'is_verified' => false,
                    'is_approved' => true,
                    'created_at' => now()->subDays(60),
                ],
                [
                    'tour_id' => $samarkandTour->id,
                    'reviewer_name' => 'David Thompson',
                    'reviewer_location' => 'United Kingdom',
                    'rating' => 4,
                    'title' => 'Great tour, minor suggestion',
                    'content' => 'Excellent tour overall. The sites were amazing and our guide was great. Would have liked a bit more time at Registan Square for photos, but understand the time constraints. Still highly recommend!',
                    'source' => 'website',
                    'is_verified' => false,
                    'is_approved' => true,
                    'created_at' => now()->subDays(71),
                ],
            ];

            foreach ($reviews as $review) {
                Review::create($review);
            }
        }

        // Get 5-Day Silk Road Classic Tour
        $silkRoadTour = Tour::where('slug', '5-day-silk-road-classic')->first();
        if ($silkRoadTour) {
            $reviews = [
                [
                    'tour_id' => $silkRoadTour->id,
                    'reviewer_name' => 'Linda and Tom Peterson',
                    'reviewer_location' => 'Canada',
                    'rating' => 5,
                    'title' => 'Trip of a lifetime!',
                    'content' => 'This 5-day tour exceeded all expectations. Hotels were comfortable, guides were knowledgeable, and the sites were breathtaking. The high-speed train between cities was a highlight. Perfect balance of guided tours and free time.',
                    'source' => 'website',
                    'is_verified' => false,
                    'is_approved' => true,
                    'created_at' => now()->subDays(10),
                ],
                [
                    'tour_id' => $silkRoadTour->id,
                    'reviewer_name' => 'Hans Mueller',
                    'reviewer_location' => 'Germany',
                    'rating' => 5,
                    'title' => 'Outstanding organization',
                    'content' => 'Everything was perfectly organized from start to finish. Our German-speaking guide was excellent. The small group size made it feel more personal. Highly recommended for first-time visitors to Uzbekistan.',
                    'source' => 'tripadvisor',
                    'is_verified' => false,
                    'is_approved' => true,
                    'created_at' => now()->subDays(18),
                ],
                [
                    'tour_id' => $silkRoadTour->id,
                    'reviewer_name' => 'Yuki Tanaka',
                    'reviewer_location' => 'Japan',
                    'rating' => 5,
                    'title' => 'Amazing cultural experience',
                    'content' => 'The tour provided deep insights into Silk Road history. Hotels were clean and well-located. Traditional dinners were delicious. Our group of 8 had great chemistry. Worth every dollar!',
                    'source' => 'website',
                    'is_verified' => false,
                    'is_approved' => true,
                    'created_at' => now()->subDays(25),
                ],
                [
                    'tour_id' => $silkRoadTour->id,
                    'reviewer_name' => 'Maria Santos',
                    'reviewer_location' => 'Brazil',
                    'rating' => 4,
                    'title' => 'Excellent tour with minor notes',
                    'content' => 'Wonderful tour overall. The sites are incredible and our guide was knowledgeable. Wish there was more free time for shopping in Bukhara. Otherwise, everything was great!',
                    'source' => 'website',
                    'is_verified' => false,
                    'is_approved' => true,
                    'created_at' => now()->subDays(40),
                ],
                [
                    'tour_id' => $silkRoadTour->id,
                    'reviewer_name' => 'Robert and Jennifer Kim',
                    'reviewer_location' => 'South Korea',
                    'rating' => 5,
                    'title' => 'Perfect honeymoon trip',
                    'content' => 'We chose this tour for our honeymoon and it was perfect! Romantic settings, amazing history, great food. The train rides between cities were comfortable. Thank you for making our trip special!',
                    'source' => 'tripadvisor',
                    'is_verified' => false,
                    'is_approved' => true,
                    'created_at' => now()->subDays(52),
                ],
            ];

            foreach ($reviews as $review) {
                Review::create($review);
            }
        }

        // Get Bukhara City Tour
        $bukharaTour = Tour::where('slug', 'full-day-bukhara-city-tour')->first();
        if ($bukharaTour) {
            $reviews = [
                [
                    'tour_id' => $bukharaTour->id,
                    'reviewer_name' => 'Patricia Williams',
                    'reviewer_location' => 'United States',
                    'rating' => 5,
                    'title' => 'Bukhara is magical!',
                    'content' => 'Full day well spent exploring this beautiful city. The Ark Fortress and Poi Kalyan were highlights. Lunch was delicious. Guide was very knowledgeable about local history.',
                    'source' => 'website',
                    'is_verified' => false,
                    'is_approved' => true,
                    'created_at' => now()->subDays(12),
                ],
                [
                    'tour_id' => $bukharaTour->id,
                    'reviewer_name' => 'Giovanni Rossi',
                    'reviewer_location' => 'Italy',
                    'rating' => 5,
                    'title' => 'Comprehensive city tour',
                    'content' => 'Saw all the major sites and learned so much about Bukhara\'s role in the Silk Road. The trading domes were fascinating. Great value for a full day tour with lunch included.',
                    'source' => 'tripadvisor',
                    'is_verified' => false,
                    'is_approved' => true,
                    'created_at' => now()->subDays(20),
                ],
                [
                    'tour_id' => $bukharaTour->id,
                    'reviewer_name' => 'Sophie Laurent',
                    'reviewer_location' => 'France',
                    'rating' => 4,
                    'title' => 'Very good tour',
                    'content' => 'Enjoyed the tour very much. The guide was friendly and informative. Lunch was good but could have been better. Overall, great way to see Bukhara in one day.',
                    'source' => 'website',
                    'is_verified' => false,
                    'is_approved' => true,
                    'created_at' => now()->subDays(35),
                ],
                [
                    'tour_id' => $bukharaTour->id,
                    'reviewer_name' => 'Ahmed Al-Rashid',
                    'reviewer_location' => 'United Arab Emirates',
                    'rating' => 5,
                    'title' => 'Rich history and architecture',
                    'content' => 'As an architecture enthusiast, I was blown away by Bukhara\'s buildings. The guide explained the different architectural styles very well. Highly recommended for history buffs.',
                    'source' => 'website',
                    'is_verified' => false,
                    'is_approved' => true,
                    'created_at' => now()->subDays(45),
                ],
            ];

            foreach ($reviews as $review) {
                Review::create($review);
            }
        }
    }
}
