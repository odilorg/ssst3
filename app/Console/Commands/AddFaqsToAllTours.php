<?php

namespace App\Console\Commands;

use App\Models\Tour;
use App\Models\TourFaq;
use Illuminate\Console\Command;

class AddFaqsToAllTours extends Command
{
    protected $signature = 'tours:add-all-faqs';
    protected $description = 'Add comprehensive FAQs to all tours missing FAQs';

    public function handle()
    {
        $this->info('Adding FAQs to tours without FAQs...');

        // Get tours without FAQs
        $tours = Tour::doesntHave('faqs')->get();

        if ($tours->isEmpty()) {
            $this->info('All tours already have FAQs!');
            return Command::SUCCESS;
        }

        $this->info("Found {$tours->count()} tours without FAQs");

        foreach ($tours as $tour) {
            $this->info("Processing: {$tour->title}");
            $this->addGenericFaqs($tour);
        }

        $this->info('✅ All FAQs added successfully!');

        return Command::SUCCESS;
    }

    private function addGenericFaqs($tour)
    {
        // Determine tour type and add appropriate FAQs
        $duration = $tour->duration_days;

        if ($duration == 1) {
            $this->addDayTourFaqs($tour);
        } elseif ($duration <= 3) {
            $this->addShortTourFaqs($tour);
        } elseif ($duration <= 7) {
            $this->addMediumTourFaqs($tour);
        } else {
            $this->addLongTourFaqs($tour);
        }

        $this->info("  ✓ Added FAQs to: {$tour->title}");
    }

    private function addDayTourFaqs($tour)
    {
        $faqs = [
            [
                'question' => 'What time does the tour start and end?',
                'answer' => 'The tour typically starts between 8:00-9:00 AM and ends around 5:00-6:00 PM. We offer flexible pickup times depending on your schedule. The exact timing will be confirmed when you book and can be adjusted to match your hotel location and preferences.',
                'sort_order' => 1
            ],
            [
                'question' => 'Is hotel pickup included?',
                'answer' => 'Yes! We provide complimentary hotel pickup and drop-off from any hotel in the city center. If your hotel is outside the central area, we may arrange a convenient meeting point or charge a small supplement for distant locations. Please provide your hotel details when booking.',
                'sort_order' => 2
            ],
            [
                'question' => 'What is included in the tour price?',
                'answer' => 'The tour price includes: professional English-speaking guide, comfortable air-conditioned transportation, all entrance fees to monuments and museums, and bottled water. Lunch and personal expenses are not included unless specifically mentioned in the itinerary.',
                'sort_order' => 3
            ],
            [
                'question' => 'How much walking is involved?',
                'answer' => 'This is a moderate walking tour covering approximately 3-5 kilometers throughout the day. Most sites involve flat terrain with some stairs and uneven surfaces at historical monuments. Comfortable walking shoes are essential. There are frequent breaks, and the pace is relaxed to allow time for photos and rest.',
                'sort_order' => 4
            ],
            [
                'question' => 'Can the tour be customized?',
                'answer' => 'Absolutely! As a private tour, we can adjust the itinerary to match your interests. Want to spend more time at a specific site, skip something, or add a workshop/bazaar visit? Just let your guide know. We\'re flexible and aim to create your perfect day.',
                'sort_order' => 5
            ],
            [
                'question' => 'What should I bring?',
                'answer' => 'ESSENTIALS: Comfortable walking shoes, sun protection (hat, sunglasses, SPF), water bottle (we provide refills), camera, and cash for souvenirs/lunch. CLOTHING: Dress modestly when visiting mosques (shoulders and knees covered, women bring lightweight scarf). Layers are recommended as interiors can be cool even on hot days.',
                'sort_order' => 6
            ],
            [
                'question' => 'Is lunch included?',
                'answer' => 'Lunch is typically not included to give you flexibility to choose based on your preferences and budget. Your guide will recommend excellent local restaurants ranging from traditional chaikhanas (teahouses) to upscale dining. Expect to pay $5-15 USD for a good meal. We can also include lunch in your package if requested.',
                'sort_order' => 7
            ],
            [
                'question' => 'What is your cancellation policy?',
                'answer' => 'FREE CANCELLATION: Full refund if you cancel 24 hours before the tour start time. Cancellations within 24 hours: No refund. Weather-related cancellations (extremely rare): Full refund or reschedule at no charge. We understand plans change - just notify us as early as possible.',
                'sort_order' => 8
            ]
        ];

        foreach ($faqs as $faq) {
            TourFaq::create([
                'tour_id' => $tour->id,
                'question' => $faq['question'],
                'answer' => $faq['answer'],
                'sort_order' => $faq['sort_order'],
                'is_active' => true,
            ]);
        }
    }

    private function addShortTourFaqs($tour)
    {
        $faqs = [
            [
                'question' => 'Do I need a visa for Uzbekistan?',
                'answer' => 'Most nationalities (including US, EU, UK, Canada, Australia, Japan, South Korea) can enter Uzbekistan visa-free for up to 30 days for tourism. Citizens of some countries may require an e-visa, which is easy to obtain online (takes 2-3 days, costs ~$20). Check the Uzbekistan Ministry of Foreign Affairs website or contact us for your specific nationality. Your passport must be valid for at least 6 months beyond your travel dates.',
                'sort_order' => 1
            ],
            [
                'question' => 'What type of accommodation is provided?',
                'answer' => 'We use comfortable 3-4 star hotels or boutique guesthouses with private bathrooms, air conditioning, WiFi, and breakfast included. In Bukhara and Samarkand, we prefer hotels within or near the historic old town for authentic atmosphere and convenient walking access to monuments. Single rooms available for a supplement. Let us know your preferences!',
                'sort_order' => 2
            ],
            [
                'question' => 'Are meals included?',
                'answer' => 'Breakfasts are included daily at your hotel. Lunches and dinners may be included depending on the specific tour (check itinerary details). When meals are not included, your guide recommends excellent local restaurants. Expect to budget $10-20 USD per meal for good quality dining. Uzbek cuisine is delicious: plov (rice pilaf), shashlik (kebabs), lagman (noodles), fresh bread, and more!',
                'sort_order' => 3
            ],
            [
                'question' => 'How much walking is involved? What fitness level is needed?',
                'answer' => 'This tour involves MODERATE activity: walking 3-5 hours daily through old towns, mosques, and bazaars. Terrain includes cobblestone streets, some stairs, and uneven surfaces at ancient sites. If you can comfortably walk for 2-3 hours with breaks, you\'ll be fine. The pace is leisurely with plenty of photo stops and rest time. Ages 8-80 regularly complete this tour without difficulty.',
                'sort_order' => 4
            ],
            [
                'question' => 'What should I pack?',
                'answer' => 'CLOTHING: Layers for varying temperatures, comfortable walking shoes (broken in!), modest clothing for mosque visits (shoulders/knees covered, scarf for women). ESSENTIALS: Sun protection (hat, sunglasses, SPF 50+), reusable water bottle, daypack, camera, power adapter (220V, European plugs), any personal medications. MONEY: Mix of cash (USD or local som) and credit card (works in cities but not everywhere). OPTIONAL: Headlamp for poorly lit areas, wet wipes, hand sanitizer.',
                'sort_order' => 5
            ],
            [
                'question' => 'Can I have a single room? What is the cost?',
                'answer' => 'Yes, single rooms are available for a supplement typically $25-40 per night depending on the hotel. This ensures you have a private room throughout the tour. If you\'re a solo traveler willing to share with another same-gender guest (if available), you can avoid the supplement. Request single accommodation when booking.',
                'sort_order' => 6
            ],
            [
                'question' => 'What currency should I bring? Can I use credit cards?',
                'answer' => 'CURRENCY: Uzbek Som (UZS). US DOLLARS or EUROS are widely accepted for exchange - bring clean, undamaged bills. CREDIT CARDS: Visa/Mastercard work at most hotels, some restaurants, and ATMs in major cities, but many places are CASH ONLY (bazaars, smaller restaurants, entrance fees). RECOMMENDATION: Exchange $100-200 upon arrival at the airport or hotel (good rates). Keep mix of large and small bills. ATMs available in Tashkent, Samarkand, Bukhara.',
                'sort_order' => 7
            ],
            [
                'question' => 'Is WiFi available?',
                'answer' => 'Yes, all hotels provide free WiFi (quality varies from good to moderate). Many cafes and restaurants in cities also offer WiFi. Local SIM cards with data are inexpensive ($5-10 for 5-10GB) and work well in cities - we can help you purchase one on arrival. Mobile coverage in rural areas between cities may be limited.',
                'sort_order' => 8
            ],
            [
                'question' => 'Can you accommodate dietary restrictions (vegetarian, vegan, allergies)?',
                'answer' => 'Yes! Please inform us of any dietary requirements when booking. VEGETARIAN: Easily accommodated - Uzbek cuisine includes many vegetable dishes, salads, fresh bread, dairy products. VEGAN: Possible but more limited (many dishes contain dairy/eggs) - requires advance notice. ALLERGIES/GLUTEN-FREE: Inform us and we\'ll communicate with restaurants. Your guide will help explain restrictions in local language. Bringing some backup snacks is wise.',
                'sort_order' => 9
            ],
            [
                'question' => 'What is your cancellation policy?',
                'answer' => 'CANCELLATIONS: More than 14 days before tour start: Full refund minus $100 processing fee. 7-14 days before: 50% refund. Less than 7 days: No refund. We recommend comprehensive travel insurance that covers trip cancellation for any reason. CHANGES: We accommodate schedule changes when possible (subject to availability). COVID/WEATHER: Flexible rebooking if circumstances prevent travel.',
                'sort_order' => 10
            ]
        ];

        foreach ($faqs as $faq) {
            TourFaq::create([
                'tour_id' => $tour->id,
                'question' => $faq['question'],
                'answer' => $faq['answer'],
                'sort_order' => $faq['sort_order'],
                'is_active' => true,
            ]);
        }
    }

    private function addMediumTourFaqs($tour)
    {
        $this->addShortTourFaqs($tour); // Uses same FAQs plus additional
    }

    private function addLongTourFaqs($tour)
    {
        $faqs = [
            [
                'question' => 'Do I need visas for this multi-country tour?',
                'answer' => 'Visa requirements vary by country: UZBEKISTAN: Visa-free for most nationalities (US, EU, UK, Canada, etc.) for 30 days. TAJIKISTAN: E-visa required ($50-60) + GBAO permit for Pamir regions ($20-30). KYRGYZSTAN: Visa-free for most Western nationalities for 60 days. KAZAKHSTAN: Visa-free for most nationalities for 30 days. We provide detailed visa instructions with required links and support upon booking. Start the process 30-45 days before travel. Your passport must be valid 6 months beyond travel with several blank pages.',
                'sort_order' => 1
            ],
            [
                'question' => 'What type of accommodation is provided?',
                'answer' => 'Accommodation varies to match each location: CITIES (Tashkent, Samarkand, Bukhara, Khiva, Almaty, Bishkek): Comfortable 3-4 star hotels with private bathrooms, AC, WiFi, breakfast. BOUTIQUE PROPERTIES: Historic buildings in old towns where possible. REMOTE AREAS (Pamirs, Altyn Emel, Song Kul): Family homestays, guesthouses, or yurt camps with basic but clean shared facilities - authentic experiences! SINGLE ROOMS: Available in most locations for a supplement ($300-450 total depending on tour length). Not always possible in very remote areas.',
                'sort_order' => 2
            ],
            [
                'question' => 'Are meals included? What food should I expect?',
                'answer' => 'INCLUDED MEALS: Typically all breakfasts, 60-70% of lunches (during excursions), and 40-50% of dinners. Specific meals listed in itinerary. NON-INCLUDED MEALS: Give you flexibility to explore local restaurants and manage budget/preferences. CUISINE: Central Asian staples include plov (rice pilaf), shashlik (kebabs), lagman (noodles), manti (dumplings), fresh bread, salads, tea. Each country has variations. DIETARY RESTRICTIONS: Vegetarian easily accommodated. Vegan possible with advance notice. Inform us when booking. Rural areas have simpler food than cities but always fresh and homemade.',
                'sort_order' => 3
            ],
            [
                'question' => 'How physically demanding is this tour? What fitness level do I need?',
                'answer' => 'This is an ACTIVE ADVENTURE tour requiring GOOD FITNESS: WALKING: 4-6 hours daily through cities, sites, and trekking. DRIVING: Long days (6-9 hours) on sometimes rough mountain roads. ALTITUDE: Parts of the tour reach 3,000-4,600m elevation - altitude acclimatization important. ACTIVITIES: Horseback riding, hiking uphill, getting in/out of 4WD vehicles. REQUIREMENTS: Able to hike 3-4 hours with elevation gain, comfortable with long travel days, no serious health conditions affected by altitude. Not recommended for those with mobility issues or heart/lung conditions. Ages 16-65 typically do well with good fitness.',
                'sort_order' => 4
            ],
            [
                'question' => 'What about altitude sickness? How do you handle this?',
                'answer' => 'Some portions reach HIGH ALTITUDE (3,000-4,600m depending on tour). ACCLIMATIZATION: Itineraries designed to ascend gradually, spending nights at increasing elevations. SYMPTOMS: Mild headache, breathlessness, fatigue are normal above 3,000m. Serious issues (pulmonary/cerebral edema) are rare with proper acclimatization. PREVENTION: (1) Drink 4-5 liters water daily, (2) Avoid alcohol, (3) Walk slowly, rest often, (4) Consider altitude medication (Diamox - consult doctor), (5) Eat light meals. SUPPORT: Guides carry oxygen and monitor guests. We descend immediately if severe symptoms occur. Travel insurance with medical coverage MANDATORY.',
                'sort_order' => 5
            ],
            [
                'question' => 'What should I pack for varying climates and altitudes?',
                'answer' => 'LAYERING SYSTEM ESSENTIAL - you\'ll experience desert heat to mountain cold: BASE LAYERS: Thermal underwear, moisture-wicking shirts. MID LAYERS: Fleece jacket, light down jacket. OUTER: Waterproof windbreaker, warm hat, gloves (even summer nights are cold at altitude!). BOTTOMS: Hiking pants, thermal leggings. FOOTWEAR: Broken-in hiking boots (essential!), comfortable sandals for evenings. ACCESSORIES: Sun hat, sunglasses (UV protection), SPF 50+, headlamp, trekking poles (if you use them). GEAR: Daypack, reusable water bottle, power bank, universal adapter. OPTIONAL: Sleeping bag liner for yurt camps.',
                'sort_order' => 6
            ],
            [
                'question' => 'How long are the driving days? Is it exhausting?',
                'answer' => 'LONG DRIVING DAYS are part of this adventure: RANGE: 3-9 hours depending on the day and route. Rough mountain roads are slower than highway miles. BREAKS: We stop every 2 hours for bathrooms, photos, snacks, leg stretching. Meal stops included. SCENERY: The landscapes are so spectacular that time passes quickly - you\'ll want to stop constantly for photos! Pamir Highway, Charyn Canyon, mountain passes offer jaw-dropping views. VEHICLES: Comfortable 4WD vehicles or minivans with air conditioning. TIPS: Bring neck pillow, entertainment (books, music), snacks. The drives ARE the adventure, not just transit. Most guests say it\'s worth it for the remote places we reach.',
                'sort_order' => 7
            ],
            [
                'question' => 'What about bathrooms and hygiene in remote areas?',
                'answer' => 'CITIES: Modern flush toilets at hotels, restaurants, sites. PUBLIC TOILETS: Usually squat-style but clean. Bring tissue (not always provided). REMOTE AREAS: Pit toilets (outhouses) at homestays, yurt camps, and mountain stops - basic but maintained. No flush toilets or toilet paper. SHOWERS: Hotels have hot water. Homestays/yurts may have limited or cold water only. Some days (Pamirs, Song Kul) you may go without hot shower for 2-3 nights. HYGIENE ESSENTIALS: Bring: wet wipes, hand sanitizer, toilet paper roll, feminine products, small travel towel. Embrace the adventure - it\'s temporary!',
                'sort_order' => 8
            ],
            [
                'question' => 'Can I use my phone and internet throughout the tour?',
                'answer' => 'CONNECTIVITY VARIES DRAMATICALLY: CITIES (Tashkent, Samarkand, Bukhara, Almaty, Bishkek, Dushanbe): Excellent WiFi at hotels and cafes. Good mobile coverage. SMALL TOWNS: Limited WiFi, spotty mobile service. REMOTE AREAS (Pamir Highway, Song Kul, Altyn Emel, mountain passes): NO COVERAGE for days at a time. Zero WiFi. RECOMMENDATIONS: (1) Buy local SIM cards in each country for cities ($5-10 for data), (2) Download offline maps (Maps.me, Google Maps), (3) Inform family you\'ll be offline 3-5 days during remote sections, (4) Embrace digital detox - star photography and nature are incredible! (5) Hotels sometimes have limited/slow WiFi - be patient.',
                'sort_order' => 9
            ],
            [
                'question' => 'Is travel insurance required? What should it cover?',
                'answer' => 'YES - COMPREHENSIVE TRAVEL INSURANCE IS MANDATORY. REQUIRED COVERAGE: (1) Medical expenses including HIGH-ALTITUDE activities (up to 5,000m), (2) Emergency medical evacuation from remote areas (helicopter rescue can cost $20,000-40,000!), (3) Trip cancellation and interruption, (4) Adventure activities (trekking, horseback riding, rough terrain driving), (5) Lost/delayed baggage, (6) Coverage in ALL countries on your itinerary. RECOMMENDED PROVIDERS: World Nomads, Global Rescue (evacuation specialists), IMG Global, SafetyWing. VERIFY: Policy explicitly covers Central Asia, high altitude, and adventure activities. We require PROOF OF INSURANCE before final payment. Medical facilities in remote areas are non-existent.',
                'sort_order' => 10
            ],
            [
                'question' => 'Is this tour suitable for solo travelers?',
                'answer' => 'ABSOLUTELY! Solo travelers are warmly welcomed: TYPICAL GROUP: 30-50% are solo travelers, rest are couples/friends. Small groups (4-12 people) mean everyone gets to know each other. BONDING: Shared adventures (mountain passes, homestay dinners, sunrise at Registan) create strong friendships. Many solos become lifelong travel buddies. SAFETY: You\'re always with the group and guide. Central Asia is very safe. Never truly alone. SINGLE SUPPLEMENT: Optional private rooms cost extra ($300-500 depending on tour length). Many solos share to save money and meet people. AGES: Wide range (25-70) of adventurous, culture-loving spirits. Perfect for making friends!',
                'sort_order' => 11
            ],
            [
                'question' => 'What is your cancellation policy?',
                'answer' => 'CANCELLATION TERMS: More than 45 days before departure: Full refund minus $250 processing fee. 30-45 days: 25% of tour cost forfeited. 15-30 days: 50% forfeited. Less than 15 days: No refund. TRAVEL INSURANCE: We STRONGLY RECOMMEND comprehensive trip cancellation insurance. This protects you if emergency prevents travel. WEATHER/POLITICAL: If weather makes roads impassable (rare) or political situation changes (extremely rare), we provide full refund or reschedule at no penalty. COVID-19: Flexible rebooking if travel restrictions prevent your trip. CHANGES: We try to accommodate schedule changes when possible (availability dependent, may incur fees).',
                'sort_order' => 12
            ]
        ];

        foreach ($faqs as $faq) {
            TourFaq::create([
                'tour_id' => $tour->id,
                'question' => $faq['question'],
                'answer' => $faq['answer'],
                'sort_order' => $faq['sort_order'],
                'is_active' => true,
            ]);
        }
    }
}
