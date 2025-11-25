<?php

namespace Database\Seeders;

use App\Models\Review;
use App\Models\Tour;
use Illuminate\Database\Seeder;

class RealReviewsSeeder extends Seeder
{
    /**
     * Seed real TripAdvisor reviews for homepage
     */
    public function run(): void
    {
        // Get first tour to attach reviews to
        $tour = Tour::first();

        if (!$tour) {
            $this->command->error('No tours found. Please seed tours first.');
            return;
        }

        // Clear old reviews first
        Review::where('source', 'tripadvisor')
            ->whereIn('reviewer_name', ['Billy H', 'Jhonoson', 'Dalia', 'Cai A', 'Andrea C'])
            ->delete();

        $realReviews = [
            [
                'tour_id' => $tour->id,
                'reviewer_name' => 'Jhonoson',
                'reviewer_location' => 'Newcastle upon Tyne, UK',
                'rating' => 5,
                'title' => 'Amazing City tour',
                'content' => 'The City Tour of Samarkand with Jahongir Travel offers an in-depth exploration of this historic Silk Road city. Highlights include visits to the Registan Square, Shah-i-Zinda necropolis, and Bibi-Khanym Mosque, showcasing stunning Islamic architecture and rich history. The tour also covers the Gur-e-Amir Mausoleum and the Ulugh Beg Observatory, providing insights into Samarkand\'s cultural and scientific heritage. With knowledgeable guides and a well-organized itinerary, Jahongir Travel ensures a comprehensive and engaging experience. This tour is perfect for history enthusiasts and those wanting to immerse themselves in the architectural and cultural wonders of Samarkand.',
                'source' => 'tripadvisor',
                'is_verified' => false,
                'is_approved' => true,
                'created_at' => now()->subDays(5),
            ],
            [
                'tour_id' => $tour->id,
                'reviewer_name' => 'Cai A',
                'reviewer_location' => 'Traveler',
                'rating' => 5,
                'title' => 'Great Shahrizab tour with excellent driver',
                'content' => 'The Shahrizab tour was great! Our driver (ILHOM) was very good and extremely accommodating. He shared helpful knowledge and information about the places we visited, which made the trip even more meaningful. He also took us to some beautiful spots for photos. Shahrizab itself is a very nice place, and we enjoyed a scenic and relaxing drive throughout the tour. Highly recommended!',
                'source' => 'tripadvisor',
                'is_verified' => false,
                'is_approved' => true,
                'created_at' => now()->subDays(2),
            ],
            [
                'tour_id' => $tour->id,
                'reviewer_name' => 'Andrea C',
                'reviewer_location' => 'Local Guide',
                'rating' => 5,
                'title' => 'Best experience in Tajikistan and Uzbekistan',
                'content' => 'This experience is surely the best if you want to have a first glimpse of Tajikistan, especially you are doing a vacation in Uzbekistan as my group of friends. The journey let us see wonderful landscapes as the Seven Lakes and also to know different information about this country. This was possible due to our great guide that answered to any our curiosity and was committed to make us feel as much comfortable as possible. The last mention need to be referred to our driver who managed to lead us on the top of the possible path through a challenging road, making us feel safety as we were driving in a car parking. The best driver I ever met.',
                'source' => 'tripadvisor',
                'is_verified' => false,
                'is_approved' => true,
                'created_at' => now()->subDays(98),
            ],
        ];

        foreach ($realReviews as $review) {
            Review::create($review);
        }

        $this->command->info('✅ Real TripAdvisor reviews seeded successfully!');
    }
}
