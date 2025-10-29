<?php

namespace Database\Seeders;

use App\Models\Tour;
use App\Models\TourFaq;
use Illuminate\Database\Seeder;

class TourFaqSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get Samarkand City Tour
        $samarkandTour = Tour::where('slug', 'samarkand-city-tour')->first();
        if ($samarkandTour) {
            $faqs = [
                [
                    'tour_id' => $samarkandTour->id,
                    'question' => 'What should I bring?',
                    'answer' => 'Comfortable walking shoes, sun protection (hat, sunscreen, sunglasses), camera, water bottle, and local currency (Uzbek som) for tips and souvenirs. We also recommend bringing a scarf for women to cover shoulders when entering religious sites.',
                    'sort_order' => 1,
                ],
                [
                    'tour_id' => $samarkandTour->id,
                    'question' => 'What is not allowed on this tour?',
                    'answer' => 'Smoking inside historical monuments, touching ancient artifacts or walls, flash photography inside certain buildings (external photography is always allowed), and climbing on ancient structures. Please be respectful of these UNESCO World Heritage sites.',
                    'sort_order' => 2,
                ],
                [
                    'tour_id' => $samarkandTour->id,
                    'question' => 'Is the tour suitable for children?',
                    'answer' => 'Yes, this tour is family-friendly and suitable for children aged 6 and above. The walking pace is moderate, and we can adjust the tour content to keep younger visitors engaged. Children under 12 receive a 50% discount.',
                    'sort_order' => 3,
                ],
                [
                    'tour_id' => $samarkandTour->id,
                    'question' => 'What happens if it rains?',
                    'answer' => 'The tour operates in most weather conditions. Samarkand has relatively little rain, but if heavy rain is forecasted, we\'ll contact you to reschedule or offer a full refund. Light rain doesn\'t typically affect the tour as many sites have covered areas.',
                    'sort_order' => 4,
                ],
                [
                    'tour_id' => $samarkandTour->id,
                    'question' => 'Can I customize the tour?',
                    'answer' => 'Yes! As a private tour, we can customize the itinerary to match your interests. Want to spend more time at photography spots? Interested in local crafts? Just let us know when booking.',
                    'sort_order' => 5,
                ],
                [
                    'tour_id' => $samarkandTour->id,
                    'question' => 'Is lunch included?',
                    'answer' => 'Lunch is not included in the tour price, but we can recommend excellent local restaurants. Alternatively, we can arrange lunch at an additional cost if you let us know in advance.',
                    'sort_order' => 6,
                ],
                [
                    'tour_id' => $samarkandTour->id,
                    'question' => 'Do I need a visa for Uzbekistan?',
                    'answer' => 'Many nationalities can now visit Uzbekistan visa-free for tourism. Please check with your local Uzbek embassy or consulate for the latest requirements. We can provide an invitation letter if needed.',
                    'sort_order' => 7,
                ],
                [
                    'tour_id' => $samarkandTour->id,
                    'question' => 'How much walking is involved?',
                    'answer' => 'The tour involves approximately 3-4 km of walking total, with several breaks. Most walking is on flat surfaces, though there are 40+ steps at Shah-i-Zinda. The pace is leisurely with plenty of time for photos.',
                    'sort_order' => 8,
                ],
            ];

            foreach ($faqs as $faq) {
                TourFaq::create($faq);
            }
        }

        // Get 5-Day Silk Road Classic Tour
        $silkRoadTour = Tour::where('slug', '5-day-silk-road-classic')->first();
        if ($silkRoadTour) {
            $faqs = [
                [
                    'tour_id' => $silkRoadTour->id,
                    'question' => 'What type of accommodation is provided?',
                    'answer' => 'We provide 4-star hotel accommodation throughout the tour. All hotels are centrally located, clean, comfortable, and include breakfast. Room upgrades to 5-star hotels are available on request.',
                    'sort_order' => 1,
                ],
                [
                    'tour_id' => $silkRoadTour->id,
                    'question' => 'How do we travel between cities?',
                    'answer' => 'We use Uzbekistan\'s modern high-speed train (Afrosiyob) which connects Tashkent, Samarkand, and Bukhara. The trains are comfortable, air-conditioned, and offer a scenic journey through the countryside.',
                    'sort_order' => 2,
                ],
                [
                    'tour_id' => $silkRoadTour->id,
                    'question' => 'Is this a group tour or private?',
                    'answer' => 'This is a small group tour with a maximum of 12 participants. This size allows for personalized attention while keeping costs reasonable. Private tour options are available on request.',
                    'sort_order' => 3,
                ],
                [
                    'tour_id' => $silkRoadTour->id,
                    'question' => 'What meals are included?',
                    'answer' => 'Daily breakfast at hotels and 3 traditional Uzbek dinners are included. Lunches are not included to give you flexibility to explore local cuisine at your own pace. Your guide will recommend excellent restaurants.',
                    'sort_order' => 4,
                ],
                [
                    'tour_id' => $silkRoadTour->id,
                    'question' => 'Can I extend the tour?',
                    'answer' => 'Absolutely! We can add extra days in Khiva, the Fergana Valley, or mountain regions. Contact us to discuss custom extensions before or after your tour.',
                    'sort_order' => 5,
                ],
                [
                    'tour_id' => $silkRoadTour->id,
                    'question' => 'What is the physical difficulty level?',
                    'answer' => 'This tour requires moderate fitness. You\'ll walk 5-8 km per day exploring cities, with rest breaks. Most sites are accessible, though some have stairs. If you have mobility concerns, please let us know in advance.',
                    'sort_order' => 6,
                ],
            ];

            foreach ($faqs as $faq) {
                TourFaq::create($faq);
            }
        }

        // Get Bukhara City Tour
        $bukharaTour = Tour::where('slug', 'full-day-bukhara-city-tour')->first();
        if ($bukharaTour) {
            $faqs = [
                [
                    'tour_id' => $bukharaTour->id,
                    'question' => 'What time does the tour start?',
                    'answer' => 'The tour typically starts at 9:00 AM and ends around 5:00 PM. We can adjust the start time to suit your schedule for private tours.',
                    'sort_order' => 1,
                ],
                [
                    'tour_id' => $bukharaTour->id,
                    'question' => 'Is lunch included?',
                    'answer' => 'Yes, a traditional Uzbek lunch at a local restaurant is included in the tour price. Vegetarian options are available - please let us know when booking.',
                    'sort_order' => 2,
                ],
                [
                    'tour_id' => $bukharaTour->id,
                    'question' => 'Can we shop during the tour?',
                    'answer' => 'Yes! Bukhara is famous for its handicrafts. We\'ll visit traditional trading domes where you can shop for carpets, ceramics, and silk. Your guide can help with negotiations.',
                    'sort_order' => 3,
                ],
                [
                    'tour_id' => $bukharaTour->id,
                    'question' => 'What currency should I bring?',
                    'answer' => 'The local currency is Uzbek Som (UZS). ATMs are available in Bukhara, but it\'s good to have some cash for small purchases. US Dollars and Euros can be exchanged locally.',
                    'sort_order' => 4,
                ],
                [
                    'tour_id' => $bukharaTour->id,
                    'question' => 'Are there restrooms available?',
                    'answer' => 'Yes, clean restrooms are available at all major sites and restaurants. Your guide will ensure you have regular comfort breaks throughout the day.',
                    'sort_order' => 5,
                ],
            ];

            foreach ($faqs as $faq) {
                TourFaq::create($faq);
            }
        }
    }
}
