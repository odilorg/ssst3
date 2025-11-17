<?php

namespace App\Console\Commands;

use App\Models\Tour;
use App\Models\TourFaq;
use Illuminate\Console\Command;

class AddFaqsToNewTours extends Command
{
    protected $signature = 'tours:add-faqs-to-new';
    protected $description = 'Add comprehensive FAQs to the 3 new Central Asia tours';

    public function handle()
    {
        $this->info('Adding FAQs to new tours...');

        // Tour 28: Kyrgyzstan Nomadic Adventure
        $this->addKyrgyzstanNomadFaqs();

        // Tour 29: Kazakhstan-Kyrgyzstan Nature Explorer
        $this->addKazakhstanKyrgyzstanFaqs();

        // Tour 30: Pamir Highway & Silk Road Odyssey
        $this->addPamirSilkRoadFaqs();

        $this->info('✅ All FAQs added successfully!');

        return Command::SUCCESS;
    }

    private function addKyrgyzstanNomadFaqs()
    {
        $tour = Tour::find(28);
        if (!$tour) {
            $this->warn('Tour 28 not found, skipping...');
            return;
        }

        $this->info('Adding FAQs to: ' . $tour->title);

        $faqs = [
            [
                'question' => 'What is the altitude at Song Kul Lake and will I experience altitude sickness?',
                'answer' => 'Song Kul Lake sits at 3,016 meters (9,895 feet) above sea level. Most travelers do not experience serious altitude sickness at this elevation, but you may feel mild symptoms like slight breathlessness, headache, or fatigue. We gradually ascend from Bishkek (750m), spending the first day at lower elevations to help with acclimatization. Drink plenty of water, avoid alcohol, and take it easy on your first day at the lake. If you have concerns, consult your doctor about altitude sickness medication like Diamox.',
                'sort_order' => 1
            ],
            [
                'question' => 'What are yurt camp facilities like? Are there bathrooms and showers?',
                'answer' => 'Yurt camps at Song Kul provide authentic nomadic accommodation with basic but clean facilities. Each yurt sleeps 3-5 guests on traditional beds with thick mattresses and warm blankets. Bathroom facilities are shared pit toilets located outside (bring a headlamp for nighttime visits). There are no hot showers at Song Kul, but some camps offer basic washing facilities with cold water. Most guests find this rustic experience part of the adventure. Hotels on other nights have modern bathrooms with hot showers.',
                'sort_order' => 2
            ],
            [
                'question' => 'How cold does it get at night, even in summer?',
                'answer' => 'Even in July-August, nighttime temperatures at Song Kul (3,016m) can drop to 5-10°C (41-50°F). Early/late season (June, September) can be near freezing at night. However, yurts are surprisingly warm with thick felt insulation and plenty of heavy blankets provided. Bring warm layers: fleece jacket, warm hat, thermal underwear, and a good sleeping bag liner if you have one. Days are pleasant (15-22°C), but dress in layers for the cool mountain air.',
                'sort_order' => 3
            ],
            [
                'question' => 'Do I need horseback riding experience? What if I\'ve never ridden before?',
                'answer' => 'No riding experience is necessary! The horseback riding at Song Kul is suitable for complete beginners. Kyrgyz horses are small, sturdy, and well-trained. Rides are on gentle terrain across meadows at a walking pace, and herders accompany you to ensure safety. If you\'re uncomfortable riding, you can walk alongside or skip this activity - it\'s not mandatory. For those who love it, additional riding time can usually be arranged with the nomadic families.',
                'sort_order' => 4
            ],
            [
                'question' => 'What about food at the yurt camps? Are there vegetarian options?',
                'answer' => 'Meals at yurt camps are simple but hearty, featuring traditional Kyrgyz dishes: bread, fresh dairy products (cream, yogurt, butter), potatoes, rice, vegetables, and usually mutton or horse meat. Vegetarians can be accommodated - meals will include eggs, dairy, bread, vegetables, and rice. Please inform us of dietary requirements when booking. Food is very fresh as families make everything daily. Tea (black or milk tea) is served constantly. Bring snacks if you have specific preferences.',
                'sort_order' => 5
            ],
            [
                'question' => 'Is travel insurance required? What should it cover?',
                'answer' => 'Yes, comprehensive travel insurance is mandatory for this tour. Your policy should cover: (1) Medical expenses including emergency evacuation from remote areas, (2) Adventure activities including horseback riding and trekking up to 4,000m, (3) Trip cancellation and interruption, (4) Lost/delayed baggage. Kyrgyzstan has limited medical facilities outside Bishkek, so evacuation coverage is critical. We recommend World Nomads, SafetyWing, or IMG Global for adventure travel coverage. Verify your policy covers Central Asia and high-altitude activities.',
                'sort_order' => 6
            ],
            [
                'question' => 'Do I need a visa for Kyrgyzstan?',
                'answer' => 'Most nationalities (including US, EU, UK, Canada, Australia, Japan, South Korea) can enter Kyrgyzstan visa-free for up to 60 days. Some nationalities can apply for a free e-visa online. Check the Kyrgyz Ministry of Foreign Affairs website or consult us for your specific nationality. Your passport must be valid for at least 6 months beyond your travel dates.',
                'sort_order' => 7
            ],
            [
                'question' => 'Can I charge my phone/camera? Is there electricity and WiFi?',
                'answer' => 'Hotels in cities (Bishkek, Karakol) have standard electricity (220V, European plugs) and WiFi. At Song Kul yurt camps, there is NO electricity or WiFi - this is part of the remote experience. Some camps may have solar panels for limited charging (2-4 hours in evening), but don\'t count on it. Bring: fully charged power banks, extra camera batteries, and a headlamp/flashlight. This digital detox is wonderful for star photography and connecting with nature. SIM cards work in Bishkek/Karakol but not at Song Kul.',
                'sort_order' => 8
            ],
            [
                'question' => 'How physically demanding is this tour? What fitness level do I need?',
                'answer' => 'This tour requires moderate fitness. You should be comfortable: (1) Hiking 2-4 hours on uneven terrain (Altyn-Arashan trek), (2) Riding a horse for 2-3 hours, (3) Walking on trails at 3,000m altitude, (4) Getting in/out of 4WD vehicles on rough roads. The Altyn-Arashan trek gains ~300m elevation over 3-4 hours - a steady uphill walk. If you can climb 3-4 flights of stairs without stopping, you should be fine. Ages 12-70 typically manage well with reasonable fitness.',
                'sort_order' => 9
            ],
            [
                'question' => 'What should I pack? Any specific items needed?',
                'answer' => 'ESSENTIAL ITEMS: (1) Warm layers for high altitude nights (fleece, down jacket, thermal underwear), (2) Sun protection (hat, sunglasses, SPF 50+ - UV is intense at altitude), (3) Good hiking boots (broken in!), (4) Headlamp with extra batteries, (5) Power bank for devices, (6) Small daypack for daily excursions, (7) Reusable water bottle, (8) Toiletries (yurt camps have no showers, bring wet wipes), (9) First aid kit with any personal medications, (10) Snacks you enjoy. OPTIONAL: Sleeping bag liner, trekking poles, camera with extra batteries.',
                'sort_order' => 10
            ],
            [
                'question' => 'Can this tour accommodate families with children or seniors?',
                'answer' => 'Yes, with considerations. Children 8+ with reasonable fitness do well - they often love the yurt experience and horseback riding. The altitude requires gradual acclimatization. For seniors, it depends on fitness level and comfort with basic facilities. The trekking can be shortened, and horseback riding is optional. The main challenges are: altitude (3,000m), basic yurt camp facilities (no hot showers, pit toilets), long driving on rough roads (4-6 hours some days). Active families and adventurous seniors regularly enjoy this tour.',
                'sort_order' => 11
            ],
            [
                'question' => 'What is the cancellation policy?',
                'answer' => 'Cancellations more than 30 days before departure: Full refund minus $200 processing fee. Cancellations 15-30 days before: 50% refund. Cancellations less than 15 days before: No refund. We understand plans change - that\'s why we strongly recommend trip cancellation insurance. Weather-related changes (rare, but mountain passes can close): We provide alternative itinerary or postpone with no penalty. COVID-related: Flexible rebooking if travel restrictions prevent your trip.',
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

        $this->info('  ✓ Added ' . count($faqs) . ' FAQs to Kyrgyzstan Nomadic Adventure');
    }

    private function addKazakhstanKyrgyzstanFaqs()
    {
        $tour = Tour::find(29);
        if (!$tour) {
            $this->warn('Tour 29 not found, skipping...');
            return;
        }

        $this->info('Adding FAQs to: ' . $tour->title);

        $faqs = [
            [
                'question' => 'Do I need visas for Kazakhstan and Kyrgyzstan?',
                'answer' => 'KAZAKHSTAN: Most nationalities (US, EU, UK, Canada, Australia, Japan, etc.) can enter visa-free for up to 30 days. Some nationalities need e-visa (easy online application). KYRGYZSTAN: Visa-free for most Western nationalities for up to 60 days. Your passport must be valid 6 months beyond travel. We provide full visa guidance upon booking. Border crossings between the two countries are straightforward with tourist passports - our guide handles all formalities.',
                'sort_order' => 1
            ],
            [
                'question' => 'How long are the driving days? Are the roads rough?',
                'answer' => 'Driving days range from 3-8 hours depending on the day. The longest day (Day 2: Charyn Canyon to Zharkent) is about 5 hours total with stops. Roads in Kazakhstan vary: paved highways between cities, but some off-road sections in Altyn Emel National Park (rocky tracks). In Kyrgyzstan, mountain roads can be winding. We use comfortable 4WD vehicles with air conditioning for rough sections. Frequent photo stops and breaks make long drives manageable. Bring neck pillow and entertainment if you prefer.',
                'sort_order' => 2
            ],
            [
                'question' => 'What wildlife might we see in Altyn Emel National Park?',
                'answer' => 'Altyn Emel protects endangered species in 520,000 hectares of desert and mountains. COMMONLY SEEN: Goitered gazelles (herds on open plains), kulans/Asian wild ass (reintroduced, about 2,500 now), golden eagles soaring overhead, desert monitor lizards, ground squirrels, ravens. OCCASIONALLY SEEN: Przewalski\'s horses (critically endangered, reintroduced from Mongolia - about 100 individuals), corsac foxes, steppe eagles, vultures. RARE: Snow leopards in high mountains (almost never seen), wolves, caracal lynx. Best viewing: Early morning and late afternoon. Bring binoculars!',
                'sort_order' => 3
            ],
            [
                'question' => 'Can I swim in Issyk-Kul Lake? What is the water temperature?',
                'answer' => 'Yes! Issyk-Kul is one of the world\'s largest alpine lakes that never freezes, making it swimmable. WATER TEMPERATURE: June: 15-17°C (cool but refreshing), July-August: 18-22°C (very pleasant), September: 16-18°C (brisk). The lake is slightly salty (like tears) which adds buoyancy. Beaches are pebbly/sandy. Many locals believe the water has healing properties due to mineral content. We schedule beach time on Day 5. Bring swimsuit and quick-dry towel. Even if you don\'t swim, walking the shore is beautiful.',
                'sort_order' => 4
            ],
            [
                'question' => 'What is the "Singing Dunes" phenomenon? Will they actually sing?',
                'answer' => 'The Singing Dunes (Dune Akkum) produce a mysterious humming/organ-like sound when sand cascades down slopes. This rare acoustic phenomenon occurs due to: electrostatic charges between sand grains, the specific size and shape of sand particles, and dry weather conditions. WHEN IT SINGS: Best on windy days or when you slide/run down the dunes, disturbing large amounts of sand. Not guaranteed every visit (needs right conditions), but the 150-meter climb and 360° desert views are spectacular regardless. The dunes are among the world\'s few documented "singing" or "booming" dunes.',
                'sort_order' => 5
            ],
            [
                'question' => 'What is the altitude on this tour? Will I experience altitude sickness?',
                'answer' => 'This tour has moderate altitudes: Almaty (800m), Charyn Canyon (1,200m), Issyk-Kul Lake (1,607m), Ala-Archa National Park (2,000-2,500m on trek). Only the Ala-Archa trek reaches 2,500m, which is generally safe for most people. Altitude sickness is VERY RARE below 3,000m. You may feel slightly breathless during the Ala-Archa hike, but we ascend gradually. Drink plenty of water, take breaks, and you\'ll be fine. This is much lower than the Kyrgyzstan nomadic tour (3,000m+) or Pamir Highway (4,600m+).',
                'sort_order' => 6
            ],
            [
                'question' => 'What fitness level is required for the Ala-Archa National Park trek?',
                'answer' => 'We offer 3 options to match fitness levels: OPTION 1 (Moderate): Hike to Ak-Sai Waterfall - 4-5 hours round trip, 300m elevation gain, well-marked trail. Most guests choose this. OPTION 2 (Easy): Valley walk - 2-3 hours, mostly flat, suitable for families/seniors. OPTION 3 (Advanced): Ratsek Hut - 6-7 hours, 700m gain, for experienced hikers. If you can hike uphill for 2-3 hours with breaks, Option 1 works. The trail is clear, not technical. Bring good hiking boots and trekking poles if you have knee issues. Your guide assesses fitness on Day 1 and recommends the best option.',
                'sort_order' => 7
            ],
            [
                'question' => 'What type of accommodation is provided? Can I get a single room?',
                'answer' => 'ACCOMMODATION MIX: (1) Hotels in Almaty and Bishkek: Comfortable 3-star hotels with private bathrooms, WiFi, breakfast. (2) Guesthouses in Zharkent, Karakol, Issyk-Kul: Family-run or small hotels, clean private rooms, shared or private bathrooms, local hospitality. (3) Guesthouse/yurt in Altyn Emel (1 night): Basic but authentic experience, shared facilities. SINGLE ROOMS: Available in most locations for a supplement ($150-200 total for the tour). At Altyn Emel, single accommodation may not be possible (yurt/guesthouse limitations). Request single room when booking.',
                'sort_order' => 8
            ],
            [
                'question' => 'What currency should I bring? Can I use credit cards?',
                'answer' => 'KAZAKHSTAN: Tenge (KZT). KYRGYZSTAN: Som (KGS). US DOLLARS or EUROS are widely accepted for exchange. CREDIT CARDS: Work in Almaty and Bishkek (hotels, restaurants, ATMs), but NOT in rural areas, parks, or small towns. Bring CASH for: souvenirs, extra meals, tips, national park snacks. ATMs in Almaty and Bishkek work with Visa/Mastercard. We recommend: Exchange $200-300 in Almaty for Kazakhstan leg, exchange $100-150 for Kyrgyzstan. Your guide helps with currency exchange at good rates.',
                'sort_order' => 9
            ],
            [
                'question' => 'Is the border crossing difficult? How long does it take?',
                'answer' => 'The Kazakhstan-Kyrgyzstan border crossing (Day 4 at Kegen) is straightforward for tourists with proper documents. PROCESS: (1) Exit Kazakhstan immigration (stamp out), (2) Drive across no-man\'s land (~5 km), (3) Enter Kyrgyzstan immigration (stamp in). TIME: Usually 30-60 minutes total, occasionally up to 90 minutes if lines are long. OUR GUIDE handles all communication, checks documents beforehand, and knows the procedures. You just need: Valid passport with both country visas/visa-free entry, completed migration cards. Relax - we do this regularly without issues!',
                'sort_order' => 10
            ],
            [
                'question' => 'What meals are included? What if I\'m vegetarian/vegan?',
                'answer' => 'INCLUDED: 7 breakfasts (all hotels), 5 lunches (picnic-style during excursions), 4 dinners. MEALS AT OWN EXPENSE: 3 lunches, 3 dinners in cities (gives you flexibility to choose). Central Asian cuisine features: bread (lepyoshka), rice pilaf, noodles (lagman), dumplings (manti), kebabs, salads, yogurt, tea. VEGETARIAN: Easily accommodated - meals include vegetable dishes, dairy, eggs, salads, bread. Inform us when booking. VEGAN: More challenging but possible with advance notice. Major cities have vegetarian restaurants; rural areas less so. Bring snacks you love (nuts, energy bars) for long driving days.',
                'sort_order' => 11
            ],
            [
                'question' => 'Can I extend this tour to add more days or combine with other tours?',
                'answer' => 'Absolutely! Popular extensions: (1) Add 3-4 days Song Kul Lake yurt stay in Kyrgyzstan (combines wonderfully after this tour), (2) Extend to Tashkent/Samarkand in Uzbekistan (1-hour flight from Bishkek), (3) Add Big Almaty Lake trek or Shymbulak ski resort near Almaty, (4) Spend extra days in Issyk-Kul exploring more villages, hot springs, or heli-skiing. We can customize combinations with our other tours. Contact us with your interests and available time - we design personalized itineraries. Best value: Combine this 8-day tour with 7-day Kyrgyzstan Nomadic for a 15-day epic!',
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

        $this->info('  ✓ Added ' . count($faqs) . ' FAQs to Kazakhstan-Kyrgyzstan Nature Explorer');
    }

    private function addPamirSilkRoadFaqs()
    {
        $tour = Tour::find(30);
        if (!$tour) {
            $this->warn('Tour 30 not found, skipping...');
            return;
        }

        $this->info('Adding FAQs to: ' . $tour->title);

        $faqs = [
            [
                'question' => 'Is the Pamir Highway dangerous? Are the roads safe?',
                'answer' => 'The Pamir Highway is an adventure, not a danger. ROAD CONDITIONS: Mostly paved but with rough sections, occasional landslides (cleared quickly), and steep drop-offs (no guardrails in places). We use experienced drivers who know every curve and have driven this route hundreds of times. Russian 4WD vehicles or Land Cruisers are built for these conditions. SAFETY CONSIDERATIONS: Altitude is the main challenge, not accidents. We drive cautiously, make frequent stops, and never rush. Mudslides/rockslides occur mainly in spring (we avoid those months). Summer (June-Sept) is safest. Medical facilities are limited - that\'s why insurance with evacuation is mandatory.',
                'sort_order' => 1
            ],
            [
                'question' => 'What about altitude sickness at 4,655m? How do you handle this?',
                'answer' => 'Ak-Baital Pass (4,655m) is the highest point - higher than Mont Blanc! ACCLIMATIZATION STRATEGY: We ascend gradually over 4 days: Dushanbe (800m) → Kalaikhum (1,300m) → Khorog (2,200m) → Murghab (3,600m) → Ak-Baital Pass (4,655m). This reduces altitude sickness risk. SYMPTOMS: Mild headache, breathlessness, fatigue, poor sleep are common above 3,000m. Serious issues (pulmonary/cerebral edema) are rare with gradual ascent. PREVENTION: (1) Drink 4-5 liters water daily, (2) Avoid alcohol, (3) Walk slowly, rest often, (4) Consider Diamox (consult doctor). We carry oxygen and descend immediately if severe symptoms occur. Most guests handle it well with our acclimatization schedule.',
                'sort_order' => 2
            ],
            [
                'question' => 'What are Pamiri homestays like? Are they comfortable?',
                'answer' => 'Pamiri homestays are authentic family experiences - not hotels, but warm hospitality. FACILITIES: Private or shared rooms with clean mattresses/beds, heavy blankets (nights are cold even in summer). Shared pit toilets outside (no flush toilets). NO hot showers in most homestays (cold water for washing). Some homes have basic electricity (solar panels), others don\'t. MEALS: Delicious homemade food - fresh bread, yogurt, potatoes, rice, eggs, tea, sometimes chicken or mutton. Families eat with you and share stories. EXPERIENCE: This is the heart of the tour - learning about Pamiri culture, Ismaili traditions, mountain life. Bring wet wipes, sense of adventure, and appreciation for simple living. After 3-4 nights, Uzbekistan hotels feel luxurious!',
                'sort_order' => 3
            ],
            [
                'question' => 'Do I need special permits for the Pamir Highway and GBAO region?',
                'answer' => 'YES - the Gorno-Badakhshan Autonomous Oblast (GBAO) requires a special permit in addition to your Tajikistan visa. GBAO PERMIT: Costs $20-30, obtained with your Tajikistan e-visa application or separately. The permit covers Khorog, Wakhan Corridor, Murghab, Pamir Highway, and Karakul Lake. WE HANDLE THIS: We provide detailed instructions when you book and can arrange the permit for you. Just send us your passport details. The permit is stamped in your passport or issued as a separate document. IMPORTANT: Without GBAO permit, you cannot travel on the Pamir Highway or enter Gorno-Badakhshan region. Start visa/permit process 30-45 days before travel.',
                'sort_order' => 4
            ],
            [
                'question' => 'Can I see into Afghanistan from the Wakhan Corridor?',
                'answer' => 'Absolutely - the Wakhan Corridor is one of the tour highlights! WHAT YOU\'LL SEE: The Panj River forms the border between Tajikistan and Afghanistan. From the road, you\'ll see: Afghan villages on the opposite bank (often just 100-200 meters away), farmers working fields, children playing, sometimes people waving. Behind them rise the spectacular Hindu Kush mountains reaching 7,000+ meters. INTERACTION: You cannot cross into Afghanistan (border is closed to tourists), but you can wave across the river. Sometimes locals shout greetings. It\'s a fascinating glimpse into one of the world\'s most remote regions. SAFETY: Completely safe - you\'re in Tajikistan the entire time, and the Afghan Wakhan region is peaceful (Pamir Afghans are not Taliban).',
                'sort_order' => 5
            ],
            [
                'question' => 'What is the weather like? When is the best time to go?',
                'answer' => 'BEST TIME: June to mid-September (our tour season). MAY/OCTOBER: Possible but cold (snow on high passes, some roads closed). NOVEMBER-APRIL: Pamir Highway is closed - impassable due to snow. SUMMER WEATHER (June-Sept): TAJIKISTAN HIGH PAMIRS (Murghab, Karakul): Days 15-25°C, nights 0-10°C (can freeze). Low humidity, intense sun. WAKHAN CORRIDOR (Khorog, Kalaikhum): Days 25-30°C, nights 10-15°C. Pleasant. UZBEKISTAN DESERT (June-Sept): Days 35-40°C (hot!), nights 20-25°C. Dry heat. CLOTHING: Layer system essential - you experience everything from desert heat to alpine cold in one day. Pack for extremes!',
                'sort_order' => 6
            ],
            [
                'question' => 'I need visas for Tajikistan, Kyrgyzstan, and Uzbekistan - is this complicated?',
                'answer' => 'It sounds complex but is actually straightforward! TAJIKISTAN: E-visa online ($50-60 + $20-30 GBAO permit). Process takes 2-3 days. We provide full instructions. KYRGYZSTAN: Visa-free for most nationalities (US, EU, UK, Canada, etc.) for 60 days. UZBEKISTAN: Visa-free for most nationalities for 30 days. PROCESS: (1) Get Tajikistan e-visa with GBAO permit 30 days before travel (we guide you), (2) Check if you need Kyrgyzstan visa (usually no), (3) Uzbekistan usually no visa needed. We provide a detailed visa guide document upon booking with links, requirements, and support. Your passport must be valid 6 months beyond travel with blank pages for stamps.',
                'sort_order' => 7
            ],
            [
                'question' => 'How long are the driving days? Is it exhausting?',
                'answer' => 'PAMIR HIGHWAY SECTION (Days 2-6): Long drives of 6-9 hours, but with many stops for sightseeing, photos, meals, rest. The scenery is so stunning that time passes quickly - you want to stop constantly! Roads are slow due to conditions (bumpy, winding), not distance. UZBEKISTAN SECTION (Days 7-14): Much shorter - Afrosiyob high-speed trains between cities (1.5-4 hours), comfortable buses for day trips. Day 11 (Bukhara-Khiva) is longest at 7 hours but paved highway. TIPS TO MANAGE: Bring neck pillow, entertainment, snacks. Our vehicles stop every 2 hours. Most guests say the drives are part of the adventure, not a burden. The Pamir scenery makes it worth it!',
                'sort_order' => 8
            ],
            [
                'question' => 'What food should I expect? Can dietary restrictions be accommodated?',
                'answer' => 'TAJIKISTAN PAMIRS: Simple mountain food - bread, potatoes, rice, eggs, yogurt, tea, vegetables, occasional mutton. Fresh and homemade but not fancy. UZBEKISTAN CITIES: Excellent cuisine - plov (rice pilaf), shashlik (kebabs), lagman (noodles), somsa (pastries), fresh salads, bread, tea. More variety and restaurants. DIETARY RESTRICTIONS: VEGETARIAN: Easily accommodated throughout. Tajik homestays make vegetable dishes, eggs, dairy. Uzbekistan has good vegetarian options. VEGAN: More challenging in Tajikistan (dairy is staple), but possible with advance notice. Bring supplements. GLUTEN-FREE: Difficult (bread is central) but manageable with planning. ALLERGIES: Inform us when booking - we communicate to all homestays and restaurants.',
                'sort_order' => 9
            ],
            [
                'question' => 'Can I use my phone and internet on this tour?',
                'answer' => 'CONNECTIVITY VARIES BY LOCATION: TAJIKISTAN CITIES (Dushanbe, Khorog): Good mobile coverage and WiFi at hotels. PAMIR HIGHWAY REMOTE AREAS (Wakhan, Murghab, Karakul): NO coverage for days at a time. WiFi at some guesthouses (slow/unreliable). UZBEKISTAN CITIES (Samarkand, Bukhara, Khiva, Tashkent): Excellent WiFi at hotels, good mobile coverage. RECOMMENDATIONS: (1) Buy local SIM cards in Dushanbe (Tcell or Megafon) for Tajikistan, separate SIM in Tashkent for Uzbekistan. (2) Download offline maps (Maps.me). (3) Inform work/family you\'ll be offline Days 2-6 (Pamirs). (4) Embrace the digital detox - Pamir stargazing is incredible without screen distraction!',
                'sort_order' => 10
            ],
            [
                'question' => 'Is this tour suitable for solo travelers?',
                'answer' => 'Absolutely! About 30-40% of our guests are solo travelers. GROUP DYNAMICS: Small groups (4-10 people) mean you get to know everyone well. Shared experiences (climbing passes, homestay dinners, Registan sunsets) create strong bonds. Many solo travelers become lifelong friends. SAFETY: You\'re always with the group and guide - never truly alone. Central Asia is very safe for solo travel. SINGLE SUPPLEMENT: Optional private rooms cost extra ($450 for entire tour). Many solos choose shared accommodation to save money and meet people. AGES: Wide range (25-70), mostly adventurous spirits who love culture, nature, and off-beaten-path experiences. Perfect for meeting like-minded travelers!',
                'sort_order' => 11
            ],
            [
                'question' => 'What about travel insurance? What should it cover?',
                'answer' => 'MANDATORY comprehensive travel insurance is non-negotiable for this tour. REQUIRED COVERAGE: (1) Medical expenses including HIGH-ALTITUDE activities (up to 5,000m), (2) Emergency evacuation from remote areas (helicopter rescue from Pamirs costs $15,000-30,000!), (3) Trip cancellation/interruption, (4) Lost/delayed baggage, (5) Adventure activities (trekking, rough terrain driving), (6) Coverage in Tajikistan, Kyrgyzstan, AND Uzbekistan. RECOMMENDED PROVIDERS: World Nomads (covers adventure travel + high altitude), Global Rescue (evacuation specialists), IMG Global, Safety Wing. VERIFY: Policy explicitly covers Central Asia and altitudes above 4,000m. We require proof of insurance before final payment. Do NOT skip this - medical facilities in Pamirs are non-existent.',
                'sort_order' => 12
            ],
            [
                'question' => 'Can I combine this tour with a trek or extend in Tajikistan/Uzbekistan?',
                'answer' => 'Yes! Popular extensions: TAJIKISTAN: (1) Fann Mountains trek (3-5 days) - spectacular alpine lakes before Pamir Highway, (2) Extra days in Wakhan Corridor exploring ancient fortresses, (3) Penjikent and Sarazm archaeological sites. UZBEKISTAN: (1) Add Fergana Valley (3-4 days) - silk workshops, ceramics, less touristy, (2) Extend in Samarkand/Bukhara (more crafts, cooking class, stay in B&Bs), (3) Termez on Afghan border (Buddhist sites). COMBINE WITH OTHER TOURS: Pair with Kyrgyzstan nomadic tour (7 days Song Kul) for ultimate 3-week Central Asia odyssey. We customize based on your time and interests. Contact us 2-3 months in advance for extensions.',
                'sort_order' => 13
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

        $this->info('  ✓ Added ' . count($faqs) . ' FAQs to Pamir Highway & Silk Road Odyssey');
    }
}
