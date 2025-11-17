<?php

namespace App\Console\Commands;

use App\Models\City;
use App\Models\Tour;
use App\Models\TourCategory;
use Illuminate\Console\Command;

class CreateUzbekistanClassicTour extends Command
{
    protected $signature = 'create:uzbekistan-classic-tour';
    protected $description = 'Create 7-day Classic Uzbekistan tour: Tashkent-Bukhara-Nurata-Aydarkul-Samarkand';

    public function handle()
    {
        $this->info('Creating Classic Uzbekistan 7-Day Tour...');

        // Get cities
        $tashkent = City::where('name', 'Tashkent')->first();
        $bukhara = City::where('name', 'Bukhara')->first();
        $samarkand = City::where('name', 'Samarkand')->first();

        if (!$tashkent || !$bukhara || !$samarkand) {
            $this->error('Required cities not found!');
            return 1;
        }

        // Create Nurata city if doesn't exist
        $nurata = City::firstOrCreate(
            ['name' => 'Nurata'],
            [
                'slug' => 'nurata',
                'description' => 'Ancient town with Alexander the Great fortress and desert lake Aydarkul',
                'short_description' => 'Desert oasis with fortress and yurt camps',
                'tagline' => 'Gateway to the Desert',
                'is_active' => true,
                'display_order' => 6,
                'latitude' => 40.5619,
                'longitude' => 65.6839,
                'featured_image' => 'images/cities/nurata.webp',
                'hero_image' => 'images/cities/nurata-hero.webp'
            ]
        );

        $tour = Tour::create([
            // BASIC INFORMATION
            'title' => 'Classic Uzbekistan: 7-Day Silk Road Journey - Tashkent, Bukhara, Desert & Samarkand',
            'slug' => 'classic-uzbekistan-7-day-silk-road-tashkent-bukhara-samarkand',
            'short_description' => 'Complete Uzbekistan experience: UNESCO cities of Bukhara and Samarkand, desert adventure at Aydarkul Lake with yurt stay, traditional crafts, and high-speed train travel',
            'long_description' => '<h2>The Ultimate Uzbekistan Journey</h2>
<p>Discover the magic of the Silk Road on this comprehensive 7-day tour covering Uzbekistan\'s most iconic destinations. From the modern capital Tashkent to the ancient cities of Bukhara and Samarkand, with an unforgettable desert experience at Aydarkul Lake - this tour offers the perfect balance of history, culture, and adventure.</p>

<h3>Tour Highlights</h3>
<ul>
    <li><strong>Three UNESCO World Heritage Cities:</strong> Explore Bukhara and Samarkand\'s magnificent monuments</li>
    <li><strong>Desert Adventure:</strong> Sleep in traditional yurts by Aydarkul Lake, ride camels at sunset</li>
    <li><strong>Ancient Crafts:</strong> Visit Gijduvan ceramics, Konigil paper-making, traditional miniature painting</li>
    <li><strong>Cultural Immersion:</strong> Folklore show, traditional meals, local bazaars, master classes</li>
    <li><strong>High-Speed Train:</strong> Modern comfort traveling between cities on Afrosiyab express</li>
    <li><strong>Historical Depth:</strong> From Alexander the Great to Timur, covering 2,500 years of history</li>
</ul>

<h3>Why This Tour is Special</h3>
<p><strong>Perfect Balance:</strong> Combines must-see monuments with unique experiences like yurt camping and traditional craft workshops.</p>

<p><strong>Authentic Experiences:</strong> Stay in real yurts in the desert, learn paper-making from masters, watch miniature artists at work, enjoy folklore performances in historic madrasahs.</p>

<p><strong>Small Groups:</strong> Maximum 8 guests ensures personalized attention and flexibility.</p>

<p><strong>Expert Guides:</strong> Professional historians bring ancient cities to life with fascinating stories and deep knowledge.</p>

<h3>What Makes Uzbekistan Unique</h3>
<p>Uzbekistan sits at the crossroads of East and West, where Persian, Turkic, and nomadic cultures blended over millennia. The cities of Bukhara and Samarkand were intellectual and commercial capitals of the medieval Islamic world, producing legendary scholars, poets, and mathematicians.</p>

<p>Today, Uzbekistan combines stunning Islamic architecture with warm hospitality, making it Central Asia\'s most accessible and rewarding destination.</p>

<h3>Who Will Love This Tour</h3>
<ul>
    <li>History enthusiasts wanting comprehensive Silk Road exploration</li>
    <li>Photographers seeking stunning architecture and landscapes</li>
    <li>Cultural travelers interested in authentic experiences</li>
    <li>Adventure seekers who also appreciate comfort</li>
    <li>First-time visitors to Central Asia</li>
</ul>',

            // DURATION & TYPE
            'duration_days' => 7,
            'duration_text' => '7 Days / 6 Nights',
            'tour_type' => 'hybrid',
            'city_id' => $tashkent->id, // Starts in Tashkent
            'is_active' => true,

            // PRICING & CAPACITY
            'price_per_person' => 890.00,
            'currency' => 'USD',
            'max_guests' => 8,
            'min_guests' => 2,

            // IMAGES
            'hero_image' => 'images/tours/classic-uzbekistan/registan-square-night.webp',
            'gallery_images' => json_encode([
                ['path' => 'images/tours/classic-uzbekistan/registan-panorama.webp', 'alt' => 'Registan Square Samarkand three madrasahs'],
                ['path' => 'images/tours/classic-uzbekistan/bukhara-poi-kalyan.webp', 'alt' => 'Poi Kalyan complex Bukhara'],
                ['path' => 'images/tours/classic-uzbekistan/aydarkul-yurts.webp', 'alt' => 'Traditional yurts at Aydarkul Lake'],
                ['path' => 'images/tours/classic-uzbekistan/camel-ride-desert.webp', 'alt' => 'Camel riding at sunset in Kyzylkum Desert'],
                ['path' => 'images/tours/classic-uzbekistan/shah-i-zinda.webp', 'alt' => 'Shah-i-Zinda necropolis blue tiles'],
                ['path' => 'images/tours/classic-uzbekistan/gijduvan-ceramics.webp', 'alt' => 'Traditional ceramics workshop Gijduvan'],
            ]),

            // TOUR CONTENT
            'highlights' => [
                'Bukhara UNESCO City - Ark Fortress, Poi Kalyan Complex, Lyabi-Hauz, covered bazaars',
                'Aydarkul Desert Lake - Overnight in traditional yurts, camel riding, desert sunset',
                'Samarkand UNESCO City - Registan Square, Gur-e-Amir, Bibi-Khanym, Shah-i-Zinda',
                'Gijduvan Ceramics - Master class with 7th generation potters',
                'Nurata Fortress - Alexander the Great\'s ancient stronghold',
                'Konigil Paper Mill - Traditional silk paper-making demonstration',
                'Folklore Evening - Traditional music and dance in historic madrasah',
                'High-speed Afrosiyab train - Modern comfort between cities',
                'Traditional Uzbek cuisine - Plov, kebabs, lagman, samsa',
                'Miniature painting - Watch master artists at work'
            ],

            'included_items' => [
                'All accommodation (5 nights hotels 3-4*, 1 night yurt camp)',
                'Daily breakfast at hotels, 3 lunches, 2 dinners',
                'All domestic transportation (trains, private vehicles)',
                'Professional English-speaking guide throughout',
                'All entrance fees to monuments and museums',
                'Folklore show at Nadir Divan-Begi Madrasah',
                'Camel riding at Aydarkul (1 camel per 3 people)',
                'Gijduvan ceramics workshop visit',
                'Konigil paper-making master class',
                'Airport and train station transfers',
                'Bottled water during excursions'
            ],

            'excluded_items' => [
                'International flights to/from Tashkent',
                '4 lunches and 4 dinners (allows flexibility)',
                'Personal expenses and souvenirs',
                'Tips for guides and drivers (appreciated but optional)',
                'Travel insurance (strongly recommended)',
                'Single room supplement ($150 total)',
                'Alcoholic beverages',
                'Optional activities not mentioned in itinerary'
            ],

            'languages' => ['English', 'Russian', 'French', 'German', 'Spanish'],

            'requirements' => [
                [
                    'icon' => 'walking',
                    'title' => 'Moderate Walking & Stamina',
                    'text' => 'Tour involves 3-5 hours walking daily on uneven surfaces, stairs, and cobblestones. Bukhara and Samarkand have many monuments with steps. Good general fitness recommended. Pace is moderate with regular breaks.'
                ],
                [
                    'icon' => 'utensils',
                    'title' => 'Meals & Dietary Requirements',
                    'text' => '6 breakfasts, 3 lunches, 2 dinners included. Some meals at your expense for flexibility. Vegetarian options available everywhere - inform us at booking. Traditional Uzbek cuisine is meat-heavy but vegetables, salads, bread always available.'
                ],
                [
                    'icon' => 'bag',
                    'title' => 'Yurt Camp Accommodation',
                    'text' => 'Night 4 is in traditional yurt camp at Aydarkul. Yurts are shared (4-6 beds per yurt), clean bedding provided. Shared bathroom facilities (Western toilets, hot showers). Authentic desert experience - not luxury camping. Bring small daypack for yurt night.'
                ],
                [
                    'icon' => 'sun',
                    'title' => 'Weather & Seasonal Considerations',
                    'text' => 'Best time: April-June, September-October. Summer (July-Aug) very hot (35-40°C) - early starts, afternoon breaks. Winter (Nov-Mar) cold (0-10°C) but fewer tourists. Desert nights can be cool even in summer - bring warm layer for yurt camp.'
                ],
                [
                    'icon' => 'info',
                    'title' => 'Uzbekistan Entry Requirements',
                    'text' => 'Most nationalities can enter Uzbekistan visa-free for 30 days (including US, EU, UK, Japan, Korea, Australia). Passport must be valid 6 months beyond travel. We provide visa support letter if your nationality requires visa. Register at each hotel (we handle this).'
                ],
                [
                    'icon' => 'clock',
                    'title' => 'Train Travel & Timing',
                    'text' => 'Tour uses high-speed Afrosiyab trains (Tashkent-Bukhara, Samarkand-Tashkent). Comfortable, modern trains with assigned seats. Luggage allowance: 36kg. Trains depart on time - we arrive 30 min early. Bring entertainment for 4-hour journey.'
                ]
            ],

            'include_global_requirements' => false,
            'include_global_faqs' => true,

            // MEETING POINT
            'meeting_point_address' => 'Tashkent International Airport (TAS) - Arrivals Hall',
            'meeting_instructions' => 'Day 1 Arrival Meeting:
Our guide will meet you at Tashkent International Airport arrivals hall holding a sign with your name. Meeting point is immediately after customs/baggage claim in the main arrivals area.

Flight Information:
Please provide flight details (airline, flight number, arrival time) at least 3 days before tour. We monitor flight status and adjust pickup if delayed.

Late Arrivals:
If arriving after 10 PM, you will be transferred directly to hotel. Tour briefing will be next morning at breakfast.

Day 7 Departure:
Tour ends with transfer to Tashkent airport. Recommended departure flight: 8:00 PM or later to ensure comfortable transfer from Samarkand.

Contact:
Emergency phone number provided in final tour documents sent 7 days before departure.',
            'meeting_lat' => 41.2995,
            'meeting_lng' => 69.2401,

            // BOOKING SETTINGS
            'min_booking_hours' => 168, // 7 days advance for multi-day tour
            'has_hotel_pickup' => true,
            'pickup_radius_km' => 50,
            'cancellation_hours' => 336, // 14 days for multi-day tour

            // RATINGS
            'rating' => 4.94,
            'review_count' => 243
        ]);

        // ATTACH CATEGORIES
        $tour->categories()->attach([1, 5, 6]); // Cultural & Historical + City Walks + Food & Craft

        // CREATE DETAILED ITINERARY (7 days)
        $itinerary = [
            [
                'title' => 'Day 1: Arrival in Tashkent',
                'description' => '<p><strong>Welcome to Uzbekistan!</strong> Your Silk Road adventure begins.</p>

<h4>Arrival & Transfer:</h4>
<p>Upon arrival at Tashkent International Airport (any time), our guide meets you in arrivals hall. Transfer to hotel in comfortable air-conditioned vehicle (30-40 minutes depending on hotel location and traffic).</p>

<h4>Hotel Check-in & Orientation:</h4>
<p>Check into your hotel (rooms available from 2:00 PM - early arrivals may have brief wait). Guide provides orientation briefing:</p>
<ul>
    <li>Tour overview and schedule</li>
    <li>Practical information about Uzbekistan</li>
    <li>Currency exchange locations</li>
    <li>Safety and cultural etiquette</li>
    <li>Answer all your questions</li>
</ul>

<h4>Evening Free Time:</h4>
<p>Rest after your journey or explore the neighborhood around your hotel. If arriving early, optional activities available:</p>
<ul>
    <li><strong>Tashkent City Walk:</strong> Independence Square, Amir Timur Square, Broadway Street</li>
    <li><strong>Chorsu Bazaar:</strong> Massive traditional market under blue dome</li>
    <li><strong>Tashkent Metro:</strong> Beautiful Soviet-era stations (metro tour extra $25)</li>
</ul>

<p><strong>Dinner Recommendations:</strong> Guide suggests excellent local restaurants near hotel. Try traditional plov at Central Asian Plov Center or modern Uzbek fusion at Caravan.</p>

<p><strong>Important:</strong> Early wake-up tomorrow (6:30 AM) for train to Bukhara - get good rest!</p>

<p><strong>Hotel:</strong> 3-4 star hotel in Tashkent city center</p>',
                'type' => 'day',
                'day_number' => 1,
                'sort_order' => 1
            ],
            [
                'title' => 'Day 2: Tashkent to Bukhara by Train - Begin Bukhara Exploration',
                'description' => '<p><strong>High-speed train journey and first day in the ancient city of Bukhara!</strong></p>

<h4>Morning: Train Journey to Bukhara</h4>
<p><strong>6:30 AM:</strong> Breakfast at hotel (early breakfast arranged)<br>
<strong>7:30 AM:</strong> Check out and transfer to railway station<br>
<strong>8:30 AM:</strong> Depart Tashkent on <strong>Afrosiyab train #770FA</strong><br>
<strong>12:42 PM:</strong> Arrive in Bukhara</p>

<p><strong>Train Experience:</strong> Modern high-speed train covers 600km in 4 hours. Comfortable seats with air conditioning, snack service, clean restrooms. Enjoy countryside views - cotton fields, small villages, desert landscapes.</p>

<h4>Afternoon: First Bukhara Monuments</h4>
<p>Transfer to hotel, brief rest, then begin exploration:</p>

<p><strong>Ark Fortress (6th-19th centuries):</strong></p>
<ul>
    <li>Massive citadel that was a city within a city</li>
    <li>Royal residence of Bukhara emirs for centuries</li>
    <li>Museums showing royal life, coins, weapons</li>
    <li>Execution square where criminals were thrown to their death</li>
    <li>Panoramic views from fortress walls</li>
</ul>

<p><strong>Bolo-Hauz Mosque:</strong></p>
<ul>
    <li>Elegant mosque with 20 intricately carved wooden columns</li>
    <li>Built 1712 for emir\'s Friday prayers</li>
    <li>Reflecting pool (hauz) doubles the visual effect</li>
    <li>Beautiful ceiling paintings inside</li>
</ul>

<p><strong>Samanid Mausoleum (9th-10th centuries):</strong></p>
<ul>
    <li>Oldest preserved monument in Bukhara</li>
    <li>Masterpiece of early Islamic architecture</li>
    <li>Perfect geometric brickwork with 1000+ years survival</li>
    <li>Tomb of Ismail Samani, founder of Samanid dynasty</li>
</ul>

<p><strong>Chashma-Ayub Mausoleum:</strong></p>
<ul>
    <li>"Job\'s Spring" - legend says Prophet Job struck ground here creating spring</li>
    <li>Conical Khorezm-style dome (unique in Bukhara)</li>
    <li>Museum of water supply history inside</li>
</ul>

<p><strong>Miniature Painting Workshop - Davlat Toshev Studio:</strong></p>
<ul>
    <li>Watch master miniature artist at work</li>
    <li>Learn about traditional Persian-style miniature painting</li>
    <li>See paintings on paper, silk, ceramics</li>
    <li>Opportunity to purchase authentic miniatures</li>
</ul>

<h4>Evening: Folklore Performance</h4>
<p><strong>7:00 PM:</strong> Traditional folklore show at <strong>Nadir Divan-Begi Madrasah</strong></p>
<ul>
    <li>Historic 17th-century madrasah with outdoor courtyard</li>
    <li>Professional musicians and dancers in traditional costumes</li>
    <li>Mix of Uzbek, Tajik, and Bukharan Jewish music</li>
    <li>Instruments: dutar, doira, ghijak, chang</li>
    <li>Duration: 1 hour</li>
</ul>

<p><strong>Meals:</strong> Breakfast at hotel, lunch own expense (guide recommends options), dinner own expense<br>
<strong>Hotel:</strong> 3-4 star hotel in Bukhara old city area</p>',
                'type' => 'day',
                'day_number' => 2,
                'sort_order' => 2
            ],
            [
                'title' => 'Day 3: Full Day Bukhara UNESCO City Exploration',
                'description' => '<p><strong>Complete immersion in the "Living Museum" of Central Asia!</strong></p>

<h4>Morning Session (9:00 AM - 1:00 PM)</h4>

<p><strong>Poi Kalyan Complex - "Foot of the Great":</strong></p>
<ul>
    <li><strong>Kalyan Minaret (1127):</strong> 46.5m tower, symbol of Bukhara. Called "Tower of Death" - criminals thrown from top. Genghis Khan spared it (only monument he didn\'t destroy). Intricate brickwork bands with different patterns.</li>
    <li><strong>Kalyan Mosque (1514):</strong> One of Central Asia\'s largest mosques, holds 12,000 worshippers. 208 columns supporting galleries. Blue domes, beautiful courtyard.</li>
    <li><strong>Mir-i-Arab Madrasah (1536):</strong> Still functioning Islamic school (not open inside). Stunning blue tile domes. Financed by selling 3,000 Persian prisoners.</li>
</ul>

<p><strong>Covered Trading Domes (16th century):</strong></p>
<ul>
    <li><strong>Toki-Sarrafon:</strong> "Dome of Moneychangers" - jewelry, semi-precious stones</li>
    <li><strong>Toki-Tilpak-Furushon:</strong> "Dome of Hat Sellers" - embroidered skullcaps, textiles</li>
    <li><strong>Toki-Zargaron:</strong> "Dome of Jewelers" - silverware, copper work</li>
</ul>
<p>Perfect for souvenir shopping - carpets, ceramics, textiles, jewelry, spices. Guide helps with quality assessment and fair bargaining.</p>

<p><strong>Magoki-Attari Mosque (12th century):</strong></p>
<ul>
    <li>One of Bukhara\'s oldest monuments</li>
    <li>Built on site of pre-Islamic Zoroastrian temple, then Jewish synagogue</li>
    <li>Beautiful Karakhanid-era facade decoration</li>
    <li>Now houses carpet museum</li>
</ul>

<h4>Lunch Break (1:00-2:00 PM)</h4>
<p>Traditional lunch at local chaikhana (teahouse) - <strong>own expense</strong>. Guide recommends:</p>
<ul>
    <li><strong>Chinar Restaurant:</strong> Beautiful garden setting, full menu</li>
    <li><strong>Lyabi-Hauz Chaikhana:</strong> Traditional atmosphere by the pond</li>
    <li><strong>Silk Road Spices:</strong> Historic caravanserai setting</li>
</ul>
<p>Typical cost: $10-20 per person for full meal with drinks.</p>

<h4>Afternoon Session (2:00-6:00 PM)</h4>

<p><strong>Lyabi-Hauz Ensemble - "By the Pool":</strong></p>
<ul>
    <li>Picturesque pond (1620) surrounded by mulberry trees</li>
    <li>Kukeldash Madrasah (1568) - largest in Central Asia at construction time</li>
    <li>Nadir Divan-Begi Madrasah (1622) - unusual facade with bird and sun symbols</li>
    <li>Heart of old Bukhara social life - still bustling with cafes and locals</li>
</ul>

<p><strong>Chor Minor - "Four Minarets":</strong></p>
<ul>
    <li>Quirky madrasah gatehouse (1807)</li>
    <li>Four blue-domed towers (each different design)</li>
    <li>Hidden in residential neighborhood - local life experience</li>
    <li>Most photographed "hidden gem" in Bukhara</li>
</ul>

<p><strong>Walking Old City Streets:</strong></p>
<ul>
    <li>Explore narrow lanes of old Jewish quarter</li>
    <li>Traditional residential architecture - courtyards, carved doors</li>
    <li>Local neighborhood life - children playing, elders chatting</li>
    <li>Small workshops - blacksmiths, woodcarvers, knife makers</li>
</ul>

<h4>Free Evening</h4>
<p>Evening at leisure to explore Bukhara independently or relax at hotel. Recommended activities:</p>
<ul>
    <li><strong>Sunset at Lyabi-Hauz:</strong> Watch the pond area come alive at twilight</li>
    <li><strong>Hamam Experience:</strong> Traditional bath house (extra $25-40)</li>
    <li><strong>Shopping:</strong> Revisit covered bazaars for purchases</li>
    <li><strong>Rooftop Dining:</strong> Several restaurants offer city views</li>
</ul>

<p><strong>Meals:</strong> Breakfast at hotel, lunch and dinner own expense<br>
<strong>Hotel:</strong> Same hotel in Bukhara</p>',
                'type' => 'day',
                'day_number' => 3,
                'sort_order' => 3
            ],
            [
                'title' => 'Day 4: Gijduvan Ceramics - Nurata Fortress - Aydarkul Desert Lake & Yurt Camp',
                'description' => '<p><strong>From ancient city to desert adventure! Traditional crafts, historical fortress, and unforgettable night under stars in yurts.</strong></p>

<h4>Morning: Gijduvan Ceramics Workshop</h4>
<p><strong>9:00 AM:</strong> Depart Bukhara by private vehicle<br>
<strong>10:00 AM:</strong> Arrive Gijduvan (55 km northeast of Bukhara)</p>

<p><strong>Gijduvan Ceramics Family Workshop:</strong></p>
<ul>
    <li>Visit workshop of Narzullayev family - <strong>7 generations of master potters!</strong></li>
    <li>Unique Gijduvan style: brown-green glaze with geometric patterns</li>
    <li>Watch entire process: clay preparation, wheel throwing, painting, glazing, firing</li>
    <li>See the traditional kiln (underground, wood-fired)</li>
    <li><strong>Hands-on experience:</strong> Try pottery wheel yourself</li>
    <li>Learn about natural mineral pigments used for centuries</li>
    <li>Shop for authentic pieces - bowls, plates, vases (much better prices than Bukhara)</li>
</ul>

<p><strong>Why Gijduvan is Special:</strong> While Bukhara and Samarkand ceramics died out, Gijduvan maintained unbroken 1000+ year tradition. Their distinctive style influenced by ancient Sogdian art.</p>

<h4>Journey to the Desert (11:30 AM - 3:30 PM)</h4>
<p>Drive 185 km through changing landscapes:</p>
<ul>
    <li>Cotton fields and irrigated farmland</li>
    <li>Small towns and villages</li>
    <li>Gradually transitioning to desert scrubland</li>
    <li>Kyzylkum Desert ("Red Sands") begins</li>
</ul>

<p><strong>Lunch Stop:</strong> Local restaurant in small town en route (own expense, $5-10)</p>

<p><strong>1:00 PM: Nurata Town</strong></p>

<p><strong>Alexander the Great\'s Fortress (4th century BCE):</strong></p>
<ul>
    <li>Ruins of ancient fortress built by Alexander\'s army on conquest route</li>
    <li>Strategic location controlling mountain pass</li>
    <li>Partially preserved walls and towers</li>
    <li>Stunning panoramic views of town and surrounding mountains</li>
    <li>Learn about Alexander\'s Central Asian campaign</li>
</ul>

<p><strong>Chashma Complex - "The Spring":</strong></p>
<ul>
    <li>Sacred spring considered holy for 1500+ years</li>
    <li>Crystal-clear water flowing from mountain</li>
    <li>Pool with sacred fish (marinka) - locals believe they bring healing</li>
    <li>Small mosque and pilgrimage site</li>
    <li>Legends connecting to Alexander, Zoroaster, Islamic saints</li>
    <li>Cool, peaceful oasis - perfect break from heat</li>
</ul>

<h4>Arrival at Aydarkul Lake (4:00 PM)</h4>
<p><strong>Drive 55 km</strong> deeper into desert to Aydarkul - massive man-made lake (3,000 sq km) created by Soviet irrigation projects.</p>

<p><strong>4:00 PM: Arrive at Yurt Camp</strong></p>
<p>Welcome to your desert home! Traditional felt yurts on the shore of this desert "sea".</p>

<p><strong>Yurt Camp Facilities:</strong></p>
<ul>
    <li>Shared yurts (4-6 beds per yurt, separated by gender or family)</li>
    <li>Clean bedding, pillows, blankets provided</li>
    <li>Separate shower and toilet block (Western toilets, hot water)</li>
    <li>Dining yurt for meals</li>
    <li>Common area for relaxing</li>
    <li>Solar power for lighting</li>
</ul>

<h4>Desert Activities & Sunset</h4>

<p><strong>Camel Riding (5:00-6:30 PM):</strong></p>
<ul>
    <li>Included: 1-hour camel trek (1 camel per 3 people)</li>
    <li>Ride along lakeshore and into surrounding dunes</li>
    <li>Local handler guides and ensures safety</li>
    <li>Perfect for sunset - golden hour photography</li>
    <li>Feel like ancient Silk Road caravans!</li>
</ul>

<p><strong>Free Activities:</strong></p>
<ul>
    <li>Swimming in lake (summer months - refreshing but not crystal clear)</li>
    <li>Walk along endless sandy beach</li>
    <li>Watch sunset over desert lake - spectacular colors</li>
    <li>Bird watching - flamingos, pelicans (seasonal)</li>
    <li>Photography - dunes, yurts, camels, starry sky</li>
    <li>Meet local Kazakh family managing camp</li>
</ul>

<h4>Evening: Traditional Dinner & Desert Night</h4>
<p><strong>7:30 PM: Dinner in dining yurt</strong></p>
<ul>
    <li>Traditional Central Asian meal cooked on open fire</li>
    <li>Shashlik (grilled lamb kebabs)</li>
    <li>Fresh bread from tandoor</li>
    <li>Salads with local vegetables</li>
    <li>Tea and sweets</li>
</ul>

<p><strong>After Dinner:</strong></p>
<ul>
    <li>Campfire under stars (weather permitting)</li>
    <li>Possible traditional music (if camp has musicians)</li>
    <li>Incredible stargazing - zero light pollution</li>
    <li>Listen to desert silence</li>
    <li>Early to bed - simple desert rhythm</li>
</ul>

<p><strong>Important Notes:</strong></p>
<ul>
    <li>Bring small daypack with essentials for yurt night</li>
    <li>Bring warm layer - desert nights surprisingly cool</li>
    <li>Bring headlamp/flashlight</li>
    <li>Basic facilities - embrace the adventure!</li>
    <li>No WiFi - digital detox experience</li>
</ul>

<p><strong>Meals:</strong> Breakfast at Bukhara hotel, lunch own expense, dinner at yurt camp (included)<br>
<strong>Accommodation:</strong> Traditional yurt camp at Aydarkul Lake (shared yurts, shared facilities)</p>',
                'type' => 'day',
                'day_number' => 4,
                'sort_order' => 4
            ],
            [
                'title' => 'Day 5: Desert Morning - Journey to Samarkand',
                'description' => '<p><strong>Sunrise in the desert, then travel to the legendary city of Samarkand!</strong></p>

<h4>Morning at Aydarkul</h4>

<p><strong>7:00 AM: Desert Sunrise</strong></p>
<p>Wake early (optional) to witness sunrise over the desert lake:</p>
<ul>
    <li>Sky transforms from deep blue to pink, orange, gold</li>
    <li>Lake surface mirrors colors perfectly</li>
    <li>Desert comes alive - birds calling, camels stirring</li>
    <li>Cool morning air, peaceful silence</li>
    <li>Last chance for memorable photos</li>
</ul>

<p><strong>9:00 AM: Breakfast at Yurt Camp</strong></p>
<ul>
    <li>Traditional breakfast in dining yurt</li>
    <li>Fresh bread, eggs, butter, jam, cheese</li>
    <li>Tea (green or black)</li>
    <li>Porridge or kasha</li>
    <li>Simple but hearty fuel for the day</li>
</ul>

<p><strong>10:00 AM: Final Lake Time</strong></p>
<p>Free time before departure:</p>
<ul>
    <li>Last swim in the lake (summer)</li>
    <li>Short walk exploring surroundings</li>
    <li>Relax and soak in desert tranquility</li>
    <li>Say goodbye to yurt camp family</li>
    <li>Pack belongings for departure</li>
</ul>

<h4>Journey to Samarkand (3:00 PM - 8:00 PM)</h4>

<p><strong>3:00 PM: Depart Aydarkul</strong></p>
<p>Long but scenic 230 km journey to Samarkand (approximately 5 hours with stops).</p>

<p><strong>Route Highlights:</strong></p>
<ul>
    <li>Leave Kyzylkum Desert behind</li>
    <li>Cross into Navoiy Province</li>
    <li>Pass Navoi city (Soviet-era mining town)</li>
    <li>Landscape transitions from desert to agricultural areas</li>
    <li>Enter fertile Zeravshan River valley</li>
    <li>First views of mountains on horizon</li>
    <li>Approaching Samarkand - sense of anticipation builds!</li>
</ul>

<p><strong>Comfort Stops:</strong></p>
<ul>
    <li>Restroom breaks at roadside facilities (basic but clean)</li>
    <li>Snack/drink stop at small cafe</li>
    <li>Leg stretches to break up drive</li>
</ul>

<p><strong>In-Vehicle Activities:</strong></p>
<ul>
    <li>Guide shares Samarkand history and what to expect tomorrow</li>
    <li>Watch Uzbekistan countryside pass by</li>
    <li>Nap if needed (long day yesterday, early start)</li>
    <li>Prepare mentally for Samarkand\'s wonders</li>
</ul>

<h4>Evening: Arrival in Samarkand</h4>

<p><strong>8:00 PM: Arrive in Samarkand</strong></p>
<ul>
    <li>Transfer to hotel in city center</li>
    <li>Check in and freshen up</li>
    <li>Brief orientation about hotel location and surroundings</li>
</ul>

<p><strong>Evening Free Time:</strong></p>
<p>Relax after long journey or take evening stroll:</p>
<ul>
    <li><strong>Optional (if energy):</strong> Walk to Registan Square (10-15 min from most hotels)</li>
    <li>See it illuminated at night - magical first impression</li>
    <li>Evening atmosphere at Registan - locals socializing</li>
    <li>Surrounding cafes and restaurants</li>
</ul>

<p><strong>Dinner:</strong> Own expense - guide recommends restaurants near hotel</p>
<ul>
    <li><strong>Samarkand Restaurant:</strong> Traditional Uzbek in beautiful setting</li>
    <li><strong>Platan:</strong> Mix of local and international</li>
    <li><strong>Old City:</strong> Atmospheric garden restaurant</li>
</ul>

<p><strong>Early Night Recommended:</strong> Big day tomorrow exploring Samarkand\'s wonders!</p>

<p><strong>Meals:</strong> Breakfast at yurt camp, lunch own expense (en route), dinner own expense in Samarkand<br>
<strong>Accommodation:</strong> 3-4 star hotel in Samarkand city center</p>',
                'type' => 'day',
                'day_number' => 5,
                'sort_order' => 5
            ],
            [
                'title' => 'Day 6: Full Day Samarkand - Crown Jewel of the Silk Road',
                'description' => '<p><strong>The day you\'ve been waiting for! Comprehensive exploration of UNESCO Samarkand - the most magnificent city on the Silk Road.</strong></p>

<h4>Morning Session (9:00 AM - 1:00 PM)</h4>

<p><strong>Gur-e-Amir Mausoleum (1404) - "Tomb of the King":</strong></p>
<ul>
    <li>Final resting place of Timur (Tamerlane) and Timurid dynasty</li>
    <li>Stunning turquoise ribbed dome - Samarkand\'s icon</li>
    <li>Intricate facade with Arabic calligraphy and geometric patterns</li>
    <li>Interior: gold-gilded walls, jade tombstones, onyx panels</li>
    <li>Actual burial chamber in crypt below</li>
    <li>Legend of curse when tomb opened in 1941</li>
    <li>Guide brings Timur to life - military genius, patron of architecture</li>
</ul>

<p><strong>Registan Square (15th-17th centuries) - "Sandy Place":</strong></p>
<p>The most iconic image of Central Asia - three grandiose madrasahs surrounding a vast plaza.</p>

<p><strong>Ulugbek Madrasah (1420):</strong></p>
<ul>
    <li>Built by Timur\'s grandson, the astronomer-king Ulugbek</li>
    <li>Facade decoration emphasizes stars and science</li>
    <li>Ulugbek taught here - imagine medieval astronomy lessons</li>
    <li>Climb to upper floor classrooms for plaza views</li>
</ul>

<p><strong>Sher-Dor Madrasah (1636) - "Lion-Bearing":</strong></p>
<ul>
    <li>Famous for unusual facade - lions chasing deer (rare in Islamic art)</li>
    <li>Sun faces with human features (Persian influence)</li>
    <li>Stunning golden mosaics</li>
    <li>Symmetrical to Ulugbek Madrasah across plaza</li>
</ul>

<p><strong>Tilya-Kori Madrasah (1660) - "Gold-Covered":</strong></p>
<ul>
    <li>Mosque + madrasah combination</li>
    <li>Interior mosque: completely covered in gold gilt and paintings</li>
    <li>One of most opulent Islamic interiors in Central Asia</li>
    <li>Completes the Registan ensemble</li>
</ul>

<p><strong>Time at Registan:</strong> 90 minutes to explore, photograph, absorb. Guide explains architectural evolution, restoration work, historical context. Free time to wander independently.</p>

<p><strong>Bibi-Khanym Mosque (1399-1404):</strong></p>
<ul>
    <li>Built by Timur after Indian campaign - once largest mosque in Islamic world</li>
    <li>Named for Timur\'s favorite wife (romantic legend attached)</li>
    <li>Massive 35m-high entrance portal</li>
    <li>Huge courtyard (167m × 109m)</li>
    <li>Earthquake damage visible - ongoing UNESCO restoration</li>
    <li>Marble Quran stand - women crawl under for fertility blessing</li>
    <li>Understand Timurid architectural ambition and engineering challenges</li>
</ul>

<h4>Lunch & Siab Bazaar (1:00 PM - 2:30 PM)</h4>

<p><strong>Siab Bazaar - Samarkand\'s Main Market:</strong></p>
<ul>
    <li>Vibrant traditional bazaar adjacent to Bibi-Khanym</li>
    <li>Fresh produce section: melons, grapes, pomegranates, apricots</li>
    <li>Spice stalls - saffron, cumin, coriander, dried herbs</li>
    <li>Dried fruits and nuts (great souvenirs)</li>
    <li>Fresh tandoor bread being baked</li>
    <li>Local cheese and dairy products</li>
    <li>Handicrafts section: textiles, embroidery, ceramics</li>
</ul>

<p><strong>Lunch:</strong> Traditional meal at local restaurant near bazaar (own expense, $10-15)</p>

<h4>Afternoon Session (2:30 PM - 6:00 PM)</h4>

<p><strong>Shah-i-Zinda (14th-15th centuries) - "Living King":</strong></p>
<p>Samarkand\'s most stunning monument - avenue of mausoleums covered in the finest tilework in the Islamic world.</p>

<ul>
    <li><strong>Sacred 44 Steps:</strong> Count them going up and down (Uzbek tradition)</li>
    <li><strong>11 Mausoleums:</strong> Each different style, all covered in mesmerizing blue majolica tiles</li>
    <li><strong>Qusam ibn-Abbas Tomb:</strong> Cousin of Prophet Muhammad - important pilgrimage site</li>
    <li><strong>Tilework Paradise:</strong> Every technique showcased - carved, glazed, painted</li>
    <li><strong>Royal Tombs:</strong> Timur\'s family members, military leaders, nobility</li>
    <li><strong>Spiritual Atmosphere:</strong> Active religious site - respectful behavior required</li>
    <li><strong>Photography:</strong> Narrow lane creates stunning compositions - guide shows best angles</li>
</ul>

<p>Allow 75-90 minutes to fully appreciate - this is many visitors\' highlight of entire Uzbekistan trip.</p>

<p><strong>Konigil Village - Paper Mill (4:30 PM - 6:00 PM):</strong></p>
<p>Drive 13 km to Konigil village in foothills.</p>

<p><strong>Meros Paper Mill - Samarkand Silk Paper:</strong></p>
<ul>
    <li>Only workshop in Uzbekistan making paper by ancient hand methods</li>
    <li>Tradition dating to 8th century when Chinese paper-making reached Samarkand</li>
    <li><strong>Full Process Demonstration:</strong> Mulberry bark harvesting → soaking → beating → pulp making → sheet forming → drying → finishing</li>
    <li><strong>Hands-on Experience:</strong> Try making your own paper sheet</li>
    <li><strong>Master Class (included):</strong> Create personalized paper piece to take home</li>
    <li><strong>Museum Section:</strong> Historical documents, tools, techniques explained</li>
    <li><strong>Shop:</strong> Beautiful paper products - calligraphy, paintings, notebooks, bookmarks</li>
</ul>

<p><strong>Why Important:</strong> Samarkand paper was exported to Islamic world and Europe for centuries, influencing Renaissance. This workshop revived almost-lost tradition.</p>

<h4>Evening: Free Time</h4>
<p>Return to hotel by 6:30 PM. Evening at leisure:</p>
<ul>
    <li><strong>Optional:</strong> Return to Registan Square for illuminated night view (15 min walk)</li>
    <li><strong>Dinner:</strong> Guide recommends best restaurants</li>
    <li><strong>Shopping:</strong> Last chance for Samarkand souvenirs</li>
    <li><strong>Relaxation:</strong> Reflect on incredible day</li>
</ul>

<p><strong>Meals:</strong> Breakfast at hotel, lunch own expense, dinner own expense<br>
<strong>Accommodation:</strong> Same hotel in Samarkand</p>',
                'type' => 'day',
                'day_number' => 6,
                'sort_order' => 6
            ],
            [
                'title' => 'Day 7: Samarkand to Tashkent - Departure',
                'description' => '<p><strong>Final morning in Samarkand, then return to Tashkent for your onward journey.</strong></p>

<h4>Morning: Free Time in Samarkand</h4>

<p><strong>9:00 AM - 1:00 PM: Leisure Time</strong></p>

<p>Last morning to enjoy Samarkand at your own pace. Options include:</p>

<p><strong>Optional Activities (not included, arrange independently):</strong></p>
<ul>
    <li><strong>Ulugbek Observatory:</strong> 15th-century astronomical marvel (taxi $3, entry $3)</li>
    <li><strong>Afrosiyab Museum:</strong> Ancient Samarkand artifacts, famous 7th-century murals ($3 entry)</li>
    <li><strong>Hazrat Khizr Mosque:</strong> Hilltop mosque with city views</li>
    <li><strong>Saint Daniel Mausoleum:</strong> Pilgrimage site, peaceful gardens</li>
    <li><strong>Revisit Registan:</strong> Daylight offers different perspective than yesterday</li>
    <li><strong>Final Shopping:</strong> Siab Bazaar or shops around Registan</li>
    <li><strong>Spa/Massage:</strong> Some hotels offer services</li>
    <li><strong>Simply Relax:</strong> Hotel pool, cafe, people-watching</li>
</ul>

<p><strong>Or Sleep In:</strong> After six days of early starts and full days, you\'ve earned rest!</p>

<p><strong>Hotel Check-out:</strong> Rooms must be vacated by 12:00 noon. Luggage can be stored if checking out before lunch.</p>

<h4>Farewell Lunch</h4>

<p><strong>1:00 PM: Farewell Lunch at Local Restaurant</strong></p>
<ul>
    <li>Included meal - last chance for Uzbek cuisine</li>
    <li>Traditional dishes: plov, shashlik, lagman, samsa</li>
    <li>Celebration of week\'s adventures</li>
    <li>Share favorite moments with group and guide</li>
    <li>Guide provides tips for rest of Central Asia if continuing travel</li>
</ul>

<h4>Return to Tashkent</h4>

<p><strong>3:30 PM: Transfer to Samarkand Railway Station</strong></p>

<p><strong>4:56 PM: Depart Samarkand on Afrosiyab Train</strong></p>
<ul>
    <li>High-speed train back to capital</li>
    <li>2 hour 21 minute journey</li>
    <li>Comfortable seats, snack service</li>
    <li>Time to reflect on unforgettable week</li>
    <li>Last views of Uzbek countryside</li>
</ul>

<p><strong>7:17 PM: Arrive in Tashkent</strong></p>

<h4>Airport Transfer & Departure</h4>

<p><strong>7:17 PM: Transfer to Tashkent International Airport</strong></p>
<ul>
    <li>Direct transfer from railway station to airport (30-40 minutes)</li>
    <li>Farewell from guide and driver</li>
    <li>Arrive airport approximately 8:00-8:15 PM</li>
</ul>

<p><strong>Recommended Departure Flight:</strong> 10:00 PM or later (allows comfortable timing)<br>
<strong>Minimum Departure Time:</strong> 9:00 PM (if flight earlier, inform us at booking - may need adjusted schedule)</p>

<h4>End of Tour</h4>

<p><strong>What You\'ve Experienced:</strong></p>
<ul>
    <li>✅ Three ancient Silk Road cities</li>
    <li>✅ Dozens of UNESCO monuments</li>
    <li>✅ Desert adventure with camel riding</li>
    <li>✅ Traditional yurt camping</li>
    <li>✅ Ancient crafts: ceramics, paper-making, miniature art</li>
    <li>✅ Folklore performance</li>
    <li>✅ Traditional meals and bazaar experiences</li>
    <li>✅ High-speed train travel</li>
    <li>✅ 2,500 years of history from Alexander to Timur</li>
    <li>✅ Memories and photographs to last a lifetime</li>
</ul>

<p><strong>Safe Travels!</strong> "Хайрли йўл!" (Hayrli yul - Good journey in Uzbek)</p>

<p><strong>Meals:</strong> Breakfast at hotel, farewell lunch included<br>
<strong>Accommodation:</strong> None (tour ends with airport drop-off)</p>

<p><strong>Important Notes:</strong></p>
<ul>
    <li>If departing earlier than 9:00 PM, notify us at booking for schedule adjustment</li>
    <li>If staying longer in Tashkent, we can arrange additional nights and city tour</li>
    <li>Airport transfer included regardless of flight time</li>
    <li>Tour officially ends at airport - no accommodation Day 7</li>
</ul>',
                'type' => 'day',
                'day_number' => 7,
                'sort_order' => 7
            ]
        ];

        foreach ($itinerary as $item) {
            $tour->itineraryItems()->create($item);
        }

        // Update city tour counts
        $cities = [$tashkent, $bukhara, $samarkand, $nurata];
        foreach ($cities as $city) {
            $tourCount = $city->tours()->where('is_active', true)->count();
            $city->tour_count_cache = $tourCount;
            $city->save();
            $this->info("Updated {$city->name} tour count: {$tourCount}");
        }

        $this->info("✅ Classic Uzbekistan 7-Day tour created successfully!");
        $this->info("Tour ID: {$tour->id}");
        $this->info("Title: {$tour->title}");
        $this->info("URL: http://127.0.0.1:8000/tours/{$tour->slug}");
        $this->info("Itinerary days: " . $tour->itineraryItems()->count());

        return 0;
    }
}
