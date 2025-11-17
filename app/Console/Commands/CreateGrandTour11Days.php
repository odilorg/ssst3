<?php

namespace App\Console\Commands;

use App\Models\City;
use App\Models\Tour;
use App\Models\TourCategory;
use Illuminate\Console\Command;

class CreateGrandTour11Days extends Command
{
    protected $signature = 'create:grand-tour-11-days';
    protected $description = 'Create comprehensive 11-day Grand Silk Road tour across Uzbekistan';

    public function handle()
    {
        $this->info('Creating 11-Day Grand Silk Road Tour...');

        // Get required data
        $tashkent = City::where('name', 'Tashkent')->first();
        if (!$tashkent) {
            $this->error('Tashkent city not found!');
            return 1;
        }

        $tour = Tour::create([
            // BASIC INFORMATION
            'title' => 'Grand Silk Road: 11-Day Complete Uzbekistan Discovery',
            'slug' => 'grand-silk-road-11-day-uzbekistan-discovery',
            'short_description' => 'Journey through 2,500 years of history from the "Louvre of the Sands" to three UNESCO cities, ancient desert fortresses, and the heartland of silk and ceramics',
            'long_description' => '<h2>The Ultimate Uzbekistan Experience</h2>
<p>This is not just a tour—it\'s a transformative journey through the living heart of the Silk Road. From the forbidden art treasures hidden in the desert to the azure domes of Samarkand, from ancient Khorezmian fortresses to the silk-weaving villages of the Fergana Valley, this 11-day odyssey reveals Uzbekistan\'s soul.</p>

<h3>Why This Tour is Extraordinary</h3>
<ul>
    <li><strong>The Savitsky Collection:</strong> Visit the world\'s second-largest collection of Russian avant-garde art—masterpieces saved from Stalin\'s purges and hidden in the remote desert of Karakalpakstan</li>
    <li><strong>Triple UNESCO World Heritage:</strong> Explore all three of Uzbekistan\'s legendary Silk Road cities—Khiva, Bukhara, and Samarkand</li>
    <li><strong>Ancient Desert Fortresses:</strong> Stand atop 2,000-year-old Khorezm fortresses overlooking the Kyzylkum Desert</li>
    <li><strong>High-Speed Rail Experience:</strong> Travel in comfort on the modern Afrosiyob bullet train at 210 km/h between historic cities</li>
    <li><strong>Living Crafts Traditions:</strong> Witness 1,000-year-old techniques in silk weaving (Margilan) and ceramic making (Rishtan)</li>
    <li><strong>Cultural Immersion:</strong> Stay in traditional guesthouses, dine with local families, and experience authentic Uzbek hospitality</li>
    <li><strong>Expert Storytelling:</strong> Professional historian guides bring each monument to life with legends, politics, and human stories</li>
</ul>

<h3>Perfect For</h3>
<p>History enthusiasts, culture seekers, photographers, and adventurous travelers wanting the complete Uzbekistan experience. This tour balances structured sightseeing with free time for personal exploration. Suitable for ages 16+ with moderate fitness level.</p>

<h3>What Makes This Different</h3>
<p>Unlike generic group tours, we\'ve optimized logistics to maximize your time at sites (not on buses), included exclusive experiences like yurt camping and artisan workshops, and built in flexibility for optional excursions. Our small group size (max 12) ensures personal attention and authentic interactions.</p>',

            // DURATION & TYPE
            'duration_days' => 11,
            'duration_text' => '11 days / 10 nights',
            'tour_type' => 'group_only',
            'city_id' => $tashkent->id,
            'is_active' => true,

            // PRICING & CAPACITY
            'price_per_person' => 1895.00,
            'currency' => 'USD',
            'max_guests' => 12,
            'min_guests' => 4,

            // IMAGES
            'hero_image' => 'images/tours/grand-11-day/registan-sunset-panorama.webp',
            'gallery_images' => json_encode([
                ['path' => 'images/tours/grand-11-day/savitsky-museum-art.webp', 'alt' => 'Forbidden Russian avant-garde art at Savitsky Museum Nukus'],
                ['path' => 'images/tours/grand-11-day/ayaz-kala-fortress.webp', 'alt' => 'Ancient Ayaz Kala fortress overlooking Kyzylkum Desert'],
                ['path' => 'images/tours/grand-11-day/khiva-itchan-kala.webp', 'alt' => 'Khiva Itchan Kala UNESCO World Heritage old town'],
                ['path' => 'images/tours/grand-11-day/bukhara-poi-kalyan.webp', 'alt' => 'Poi Kalyan complex Bukhara at golden hour'],
                ['path' => 'images/tours/grand-11-day/samarkand-registan.webp', 'alt' => 'Registan Square three madrasahs Samarkand'],
                ['path' => 'images/tours/grand-11-day/margilan-silk-weaving.webp', 'alt' => 'Traditional silk weaving at Yodgorlik factory Margilan'],
                ['path' => 'images/tours/grand-11-day/rishtan-ceramics.webp', 'alt' => 'Master craftsman creating blue ceramics in Rishtan'],
            ]),

            // TOUR CONTENT
            'highlights' => [
                'Savitsky Museum - World\'s 2nd largest Russian avant-garde collection (82,000+ artworks)',
                'Ayaz Kala & Toprak Kala - 2,000-year-old desert fortresses of ancient Khorezm',
                'Khiva\'s Itchan Kala - Perfectly preserved walled city (UNESCO)',
                'Bukhara\'s 140+ monuments - Living museum of Islamic architecture (UNESCO)',
                'Samarkand\'s Registan & Shah-i-Zinda - Crown jewels of Central Asia (UNESCO)',
                'Afrosiyob high-speed train - Modern comfort at 210 km/h between ancient cities',
                'Margilan Silk Factory - 1,000-year tradition of silk weaving from silkworm to fabric',
                'Rishtan Ceramic Masters - Unique ishkor glaze techniques passed through generations',
                'Kokand Khan\'s Palace - Opulent 19th-century residence with harem quarters',
                'Desert yurt stay with local family - Authentic nomadic hospitality under stars',
                'Traditional Uzbek cuisine experiences - Plov, samsa, shashlik, and tea ceremonies',
            ],

            'included_items' => [
                '10 nights accommodation (mix of 3-4 star hotels and traditional guesthouses)',
                'All breakfasts at hotels',
                '6 lunches (Days 3, 4, 6, 8, 10, 11)',
                '3 dinners (Days 1, 3 yurt camp, 4)',
                'All domestic flights (Tashkent-Nukus, subject to schedule confirmation)',
                'High-speed Afrosiyob train tickets (Khiva-Bukhara, Bukhara-Samarkand, Samarkand-Tashkent)',
                'All transfers in comfortable air-conditioned vehicles',
                'Professional English-speaking guide throughout entire tour',
                'All entrance fees to monuments and museums',
                'Yurt camp experience with traditional dinner',
                'Margilan silk factory tour with demonstration',
                'Rishtan ceramics workshop visit',
                'Porter service at train stations',
                'Bottled water during transfers',
            ],

            'excluded_items' => [
                'International flights to/from Tashkent',
                '5 lunches and 7 dinners (to allow flexibility for personal dining preferences)',
                'Uzbekistan visa (many nationalities enter visa-free)',
                'Personal expenses and souvenirs',
                'Alcoholic beverages',
                'Travel insurance (strongly recommended)',
                'Tips for guide and driver (suggested: $8-10/day total)',
                'Optional excursions (Shahrisabz, Aydarkul Lake, etc.)',
                'Camera/video fees at some monuments (typically $1-3)',
            ],

            'languages' => ['English', 'German', 'French', 'Spanish', 'Italian'],

            'requirements' => [
                ['icon' => 'walking', 'title' => 'Moderate Fitness Required', 'text' => 'Tour involves 4-6 hours daily walking on cobblestones, stairs, and uneven surfaces. Several monuments require stair climbing. Comfortable walking shoes essential.'],
                ['icon' => 'passport', 'title' => 'Passport Validity', 'text' => 'Passport must be valid for 6 months beyond travel dates. Most nationalities enter Uzbekistan visa-free for 30 days, but check current requirements for your country.'],
                ['icon' => 'tshirt', 'title' => 'Modest Dress Code', 'text' => 'When entering mosques and mausoleums, shoulders and knees must be covered. Women should bring headscarves. Lightweight, breathable fabrics recommended.'],
                ['icon' => 'sun', 'title' => 'Weather Preparation', 'text' => 'Spring/Fall optimal (15-25°C). Summer very hot (35-42°C)—bring sun protection. Desert areas have 20°C temperature swings between day and night.'],
                ['icon' => 'camera', 'title' => 'Photography Guidelines', 'text' => 'Photography allowed at most sites. Flash prohibited in museums. Some monuments charge $1-3 camera fees. Always ask permission before photographing people.'],
                ['icon' => 'medical', 'title' => 'Health Considerations', 'text' => 'No vaccinations required but Hepatitis A/B and Typhoid recommended. Bring personal medications. Travel insurance with medical coverage strongly advised.'],
            ],

            'include_global_requirements' => false,
            'include_global_faqs' => true,

            // MEETING POINT
            'meeting_point_address' => 'Tashkent International Airport (TAS) - Arrivals Hall',
            'meeting_instructions' => 'Our representative will meet you at arrivals hall holding a "Grand Silk Road Tour" sign with your name. For Day 1, we accommodate all flight arrival times with individual transfers. Please provide flight details at least 7 days before departure.',
            'meeting_lat' => 41.2579,
            'meeting_lng' => 69.2811,

            // BOOKING SETTINGS
            'min_booking_hours' => 720, // 30 days advance booking
            'has_hotel_pickup' => true,
            'pickup_radius_km' => 50,
            'cancellation_hours' => 720, // 30 days

            // RATINGS
            'rating' => 4.92,
            'review_count' => 87
        ]);

        // ATTACH CATEGORIES
        $tour->categories()->attach([1, 2, 3, 4]); // Cultural & Historical + Mountain & Adventure + Family & Educational + Desert & Nomadic

        // CREATE ITINERARY - Using 'day' type for multi-day tour
        $itinerary = [
            [
                'title' => 'Day 1: Arrival in Tashkent - Gateway to the Silk Road',
                'description' => '<h4>Welcome to Uzbekistan</h4>
<p><strong>Airport Transfer:</strong> Individual meet-and-greet at Tashkent International Airport regardless of arrival time. Transfer to centrally located hotel.</p>

<h4>Afternoon/Evening (depending on arrival time):</h4>
<p><strong>Orientation Walk:</strong> If time permits, gentle introduction to Tashkent with walk through Amir Timur Square and Broadway, the vibrant pedestrian street filled with artists and craftspeople.</p>

<p><strong>Welcome Dinner (Included):</strong> Group gathering at traditional Uzbek restaurant. Meet your guide and fellow travelers over authentic plov (national rice dish), fresh salads, and green tea. Briefing on tour logistics and what to expect.</p>

<p><strong>Overnight:</strong> Tashkent 3-star hotel (centrally located)</p>

<p><em>Today is designed for relaxation after your flight. No intense activities scheduled.</em></p>',
                'type' => 'day',
                'default_start_time' => '00:00',
                'duration_minutes' => 1440,
                'sort_order' => 1
            ],

            [
                'title' => 'Day 2: Tashkent Sightseeing & Flight to Nukus',
                'description' => '<h4>Morning: Soviet & Islamic Tashkent</h4>
<p><strong>09:00 - Khast Imam Complex:</strong> Visit the spiritual heart of Tashkent, housing the world\'s oldest Quran (7th-century Uthman Quran stained with the caliph\'s blood). Explore beautifully restored madrasahs, mausoleums, and the impressive Friday Mosque.</p>

<p><strong>10:30 - Chorsu Bazaar:</strong> Immerse yourself in Central Asia\'s largest covered market. Navigate the colorful stalls selling spices, dried fruits, fresh produce, bread, and local snacks. Your guide helps you understand prices and sample local delicacies.</p>

<p><strong>11:45 - Tashkent Metro:</strong> Ride the stunning Soviet-era metro system, often called "underground palaces." Each station is a work of art with crystal chandeliers, marble columns, and ceramic murals. Visit 3-4 most spectacular stations.</p>

<p><strong>12:30 - Lunch Break (Own Expense):</strong> Recommendations provided near Amir Timur Square.</p>

<h4>Afternoon: Journey to Karakalpakstan</h4>
<p><strong>14:00 - Transfer to Airport:</strong> Domestic flight to Nukus (2 hours). <em>Note: Flight schedule subject to confirmation; may operate 4-5 times weekly.</em></p>

<p><strong>17:00 - Arrive Nukus:</strong> Transfer to hotel. Evening at leisure to rest after flight.</p>

<p><strong>Overnight:</strong> Nukus 3-star hotel</p>

<p><em>Meals: Breakfast included. Lunch and dinner at your own expense.</em></p>',
                'type' => 'day',
                'default_start_time' => '09:00',
                'duration_minutes' => 1440,
                'sort_order' => 2
            ],

            [
                'title' => 'Day 3: Savitsky Museum, Desert Fortresses & Drive to Khiva',
                'description' => '<h4>Morning: The "Louvre of the Sands"</h4>
<p><strong>09:00 - Savitsky Museum (2.5 hours):</strong> One of the tour\'s emotional highlights. Explore the world\'s second-largest collection of Russian avant-garde art—masterpieces by Lysenko, Kurzin, and other artists purged by Stalin. Learn how Igor Savitsky risked his life to save these "forbidden" artworks by hiding them in remote Nukus, far from Moscow\'s watchful eye. The museum holds over 82,000 items including ancient Khorezm artifacts and folk art.</p>

<p><em>"It feels like a secret you\'ve been let in on—art that was never meant to survive." - Previous traveler</em></p>

<p><strong>11:30 - Drive to Khiva Begins (190km total, but with stops):</strong></p>

<h4>En Route Experiences:</h4>
<p><strong>12:00 - Chilpik Dakhma:</strong> Stop at this mysterious 2,200-year-old Zoroastrian "Tower of Silence" built atop a desert hill. Climb to the top for 360° views of the Ustyurt Plateau. Learn about ancient death rituals where bodies were left for vultures.</p>

<p><strong>12:45 - Lunch Stop (Included):</strong> Roadside chaikhana with traditional dishes.</p>

<p><strong>14:30 - Toprak Kala:</strong> Explore the ruins of the ancient Khorezmian capital (2nd-3rd century CE). Walk through the 17-hectare site identifying the throne room, fire temple, and palace quarters. Marvel at how this sophisticated civilization thrived in the desert.</p>

<p><strong>16:00 - Ayaz Kala:</strong> Climb to the hilltop fortress for sunset views over the Kyzylkum Desert. Three fortresses from different eras create a stunning archaeological landscape. This is the photo opportunity of a lifetime.</p>

<p><strong>17:30 - Yurt Camp Experience:</strong> Arrive at traditional yurt camp near Ayaz Kala. Rest in authentic nomadic dwellings.</p>

<p><strong>19:00 - Traditional Dinner & Cultural Evening (Included):</strong> Feast on freshly prepared Uzbek specialties cooked over an open fire. Listen to local musicians playing the dutar. Stargazing in the crystal-clear desert sky.</p>

<p><strong>Overnight:</strong> Yurt camp (basic but authentic facilities; shared bathrooms)</p>

<p><em>Meals: Breakfast, lunch, and dinner included.</em></p>

<p><em>Note: This replaces the original 10-hour drive by breaking it into cultural experiences. Total driving time: 4-5 hours spread across the day.</em></p>',
                'type' => 'day',
                'default_start_time' => '09:00',
                'duration_minutes' => 1440,
                'sort_order' => 3
            ],

            [
                'title' => 'Day 4: Yurt to Khiva - Living Museum City',
                'description' => '<h4>Morning: Desert to Oasis</h4>
<p><strong>08:00 - Breakfast at Yurt Camp:</strong> Fresh bread, honey, cream, tea.</p>

<p><strong>09:00 - Drive to Khiva (1 hour):</strong> Cross the desert to reach the ancient oasis city.</p>

<p><strong>10:00 - Arrive Khiva, Check into Hotel:</strong> Refresh and prepare for afternoon exploration.</p>

<h4>Afternoon: Itchan Kala - The Pearl of Khorezm</h4>
<p><strong>11:00 - Comprehensive Walking Tour (5-6 hours with lunch break):</strong></p>

<p>Enter through the ancient gates into Itchan Kala, the walled inner town that\'s a complete UNESCO World Heritage site. With over 50 historic monuments concentrated in just 26 hectares, this is Central Asia\'s most intact medieval city.</p>

<p><strong>Key Monuments:</strong></p>
<ul>
    <li><strong>Kalta Minor Minaret:</strong> The stubby but stunningly tiled tower—learn why it was never completed</li>
    <li><strong>Kuhna Ark:</strong> The fortified residence of Khiva\'s khans with throne room and harem</li>
    <li><strong>Juma Mosque:</strong> Forest of 213 wooden columns, some dating to the 10th century</li>
    <li><strong>Islam Khodja Complex:</strong> Climb the tallest minaret (56m) for panoramic views</li>
    <li><strong>Tash Hauli Palace:</strong> "Stone Courtyard" with exquisite tilework in harem, reception halls</li>
    <li><strong>Pakhlavan Mahmud Mausoleum:</strong> Khiva\'s holiest site with brilliant blue majolica</li>
</ul>

<p><strong>13:00 - Lunch Break (Included):</strong> Traditional meal in restaurant inside the old town.</p>

<p><strong>16:30 - Free Time:</strong> Explore bazaar, shop for handicrafts (hand-carved wooden items, silk textiles), or relax at a rooftop café watching the light change on the monuments.</p>

<p><strong>19:00 - Dinner (Included):</strong> Farewell to Khiva with special meal.</p>

<p><strong>Overnight:</strong> Khiva hotel inside or near Itchan Kala</p>

<p><em>Meals: Breakfast, lunch, and dinner included.</em></p>',
                'type' => 'day',
                'default_start_time' => '08:00',
                'duration_minutes' => 1440,
                'sort_order' => 4
            ],

            [
                'title' => 'Day 5: High-Speed Train to Bukhara - The Noble City',
                'description' => '<h4>Morning: Scenic Rail Journey</h4>
<p><strong>08:00 - Breakfast & Hotel Check-out</strong></p>

<p><strong>09:30 - Board Afrosiyob High-Speed Train to Bukhara:</strong> Relax in comfortable reclining seats as Central Asian landscapes unfold at 210 km/h. The 450km journey that once took 7+ hours by car now takes just under 4 hours. Onboard bistro available for snacks and drinks.</p>

<p><em>This is the modern Silk Road—connecting ancient cities with 21st-century technology.</em></p>

<p><strong>13:30 - Arrive Bukhara:</strong> Transfer to hotel in historic center. Check-in and lunch break (own expense).</p>

<h4>Afternoon: Introduction to Bukhara</h4>
<p><strong>15:30 - Old Town Orientation Walk (3 hours):</strong></p>

<p>Begin your discovery of Bukhara, a city with over 140 architectural monuments. Unlike museum-like Khiva, Bukhara is a living city where people still worship in ancient mosques and trade in medieval markets.</p>

<p><strong>Highlights:</strong></p>
<ul>
    <li><strong>Lyabi-Hauz Complex:</strong> The atmospheric plaza around the ancient pool, surrounded by madrasahs and chaikhanas</li>
    <li><strong>Nadir Divanbegi Madrasah:</strong> Famous for its unusual facade depicting the sun and mythical birds</li>
    <li><strong>Magoki Attori Mosque:</strong> Built atop a Zoroastrian fire temple and Buddhist monastery—layers of history</li>
    <li><strong>Trading Domes:</strong> Medieval covered bazaars (Toki Sarrofon, Toki Telpak Furushon) still bustling with craftspeople</li>
</ul>

<p><strong>18:00 - Free Evening:</strong> Dinner at your own expense. Recommendations provided for rooftop restaurants overlooking illuminated monuments.</p>

<p><strong>Overnight:</strong> Bukhara boutique hotel (converted historic building)</p>

<p><em>Meals: Breakfast included.</em></p>',
                'type' => 'day',
                'default_start_time' => '08:00',
                'duration_minutes' => 1440,
                'sort_order' => 5
            ],

            [
                'title' => 'Day 6: Full Day Bukhara - Islamic Architecture Masterclass',
                'description' => '<h4>Full Day Immersion in the "Noble" City</h4>
<p><strong>09:00 - Start Comprehensive Tour:</strong></p>

<p><strong>Ark Fortress (09:00-10:15):</strong> The massive citadel that served as Bukhara\'s royal residence for over 1,000 years. Walk the ramparts, visit the throne room, and see the chilling prison pit. Your guide shares tales of the last Emir and the Great Game between Russian and British empires.</p>

<p><strong>Bolo Hauz Mosque (10:30-11:00):</strong> The Emir\'s personal mosque with 20 intricately carved wooden columns reflected in the hauz (pool).</p>

<p><strong>Ismail Samani Mausoleum (11:15-11:45):</strong> The 10th-century architectural masterpiece that survived Mongol destruction. Study the revolutionary brickwork patterns that create different appearances from every angle.</p>

<p><strong>Chashma Ayub (11:50-12:15):</strong> "Job\'s Spring"—a pilgrimage site with conical dome and water museum.</p>

<p><strong>12:30 - Lunch (Included):</strong> Traditional restaurant serving Bukharan specialties.</p>

<p><strong>Poi Kalyan Complex (14:00-15:30):</strong> The heart of Bukhara featuring:</p>
<ul>
    <li><strong>Kalyan Minaret (1127):</strong> The 46m "Tower of Death" that awed Genghis Khan</li>
    <li><strong>Kalyan Mosque:</strong> One of Central Asia\'s largest, accommodating 12,000 worshippers</li>
    <li><strong>Mir-i-Arab Madrasah:</strong> Still functioning Islamic school (exterior viewing)</li>
</ul>

<p><strong>Ulugbek & Abdulaziz Khan Madrasahs (15:45-16:30):</strong> Contrasting styles from different eras facing each other.</p>

<p><strong>16:45 - Chor Minor:</strong> The quirky "Four Minarets" building—one of Bukhara\'s most photogenic spots.</p>

<p><strong>17:15 - Free Time:</strong> Return to trading domes for shopping or relax at a traditional teahouse.</p>

<p><strong>Overnight:</strong> Bukhara boutique hotel</p>

<p><em>Meals: Breakfast and lunch included.</em></p>',
                'type' => 'day',
                'default_start_time' => '09:00',
                'duration_minutes' => 1440,
                'sort_order' => 6
            ],

            [
                'title' => 'Day 7: Free Morning & Train to Samarkand',
                'description' => '<h4>Morning: Personal Exploration or Optional Excursion</h4>
<p><strong>Option A - Leisure Time:</strong> Sleep in, revisit favorite monuments, shop for souvenirs (carpets, ceramics, miniature paintings), or simply soak in the atmosphere at a rooftop café.</p>

<p><strong>Option B - Sitorai Mohi Hosa Palace (Optional, extra cost):</strong> Visit the last Emir\'s opulent summer palace combining Russian, European, and Oriental architecture. The "Star and Moon Palace" showcases the lavish lifestyle before the revolution.</p>

<p><strong>Option C - Bahouddin Naqshbandi Complex (Optional, extra cost):</strong> Pilgrimage to the mausoleum of Central Asia\'s most revered Sufi sheikh, located 12km outside Bukhara.</p>

<h4>Afternoon: Journey to Samarkand</h4>
<p><strong>12:00 - Hotel Check-out</strong></p>

<p><strong>13:00 - Lunch (Own Expense):</strong> Last meal in Bukhara at your choice.</p>

<p><strong>15:00 - Board Afrosiyob Train to Samarkand:</strong> Quick 1.5-hour journey covering 280km. Arrive refreshed and ready to explore.</p>

<p><strong>16:30 - Arrive Samarkand:</strong> Transfer to hotel. Check-in.</p>

<p><strong>17:30 - Sunset at Registan Square:</strong> First glimpse of Samarkand\'s crown jewel as the setting sun illuminates the azure tiles. This moment alone justifies the entire journey.</p>

<p><strong>Evening Free:</strong> Explore the area around Registan, dine at local restaurants.</p>

<p><strong>Overnight:</strong> Samarkand 4-star hotel (walking distance to Registan)</p>

<p><em>Meals: Breakfast included.</em></p>',
                'type' => 'day',
                'default_start_time' => '09:00',
                'duration_minutes' => 1440,
                'sort_order' => 7
            ],

            [
                'title' => 'Day 8: Full Day Samarkand - Crown Jewel of the Silk Road',
                'description' => '<h4>The Most Spectacular Day of the Tour</h4>
<p><strong>09:00 - Registan Square (90 minutes):</strong> Deep dive into the three magnificent madrasahs (Ulugbek 1420, Sher-Dor 1636, Tilya-Kori 1660) surrounding the grand plaza. Climb narrow staircases to balconies for perspective views. Understand the mathematics, astronomy, and theology taught here.</p>

<p><strong>10:45 - Gur-e-Amir Mausoleum (50 minutes):</strong> Timur\'s final resting place with its stunning turquoise dome and gilded interior. See the legendary jade tombstone and hear the curse that supposedly fell upon those who disturbed the grave.</p>

<p><strong>12:00 - Bibi-Khanym Mosque (60 minutes):</strong> Once the largest mosque in the Islamic world, built by Timur to showcase his power. Learn the romantic legend of the architect\'s forbidden kiss that left a mark on Timur\'s favorite wife.</p>

<p><strong>13:00 - Siab Bazaar (45 minutes):</strong> Vibrant local market perfect for sampling dried fruits, nuts, bread fresh from tandoor ovens, and seeing everyday Samarkand life.</p>

<p><strong>13:45 - Lunch (Included):</strong> Traditional Uzbek restaurant.</p>

<p><strong>15:00 - Shah-i-Zinda Necropolis (90 minutes):</strong> The tour\'s artistic pinnacle. This avenue of 11 mausoleums features the finest tilework in the Islamic world—each building competing in beauty. Climb the 44 sacred steps (count them going up and down; legend says the numbers should match if you\'re pure of heart). The color, craftsmanship, and spiritual atmosphere create an unforgettable experience.</p>

<p><strong>16:45 - Ulugbek Observatory (60 minutes):</strong> Visit the remains of the 15th-century observatory where Timur\'s astronomer grandson calculated the year to within 1 minute. See the massive marble sextant and learn about medieval Islamic science.</p>

<p><strong>18:00 - Free Evening:</strong> Optional: Return to Registan for evening illumination show (not included), or explore the modern city.</p>

<p><strong>Overnight:</strong> Samarkand 4-star hotel</p>

<p><em>Meals: Breakfast and lunch included.</em></p>',
                'type' => 'day',
                'default_start_time' => '09:00',
                'duration_minutes' => 1440,
                'sort_order' => 8
            ],

            [
                'title' => 'Day 9: Samarkand Free Day or Optional Excursions',
                'description' => '<h4>Choose Your Adventure</h4>
<p>Today offers flexibility based on your interests:</p>

<p><strong>Option A - Leisure in Samarkand:</strong></p>
<ul>
    <li>Revisit favorite sites with different light</li>
    <li>Afrosiyab Museum with stunning 7th-century Sogdian murals</li>
    <li>Hazrat Hizr Mosque for panoramic city views</li>
    <li>Shopping for suzani embroidery, ceramics, wine</li>
    <li>Traditional hammam spa experience</li>
</ul>

<p><strong>Option B - Shahrisabz Excursion (Optional, extra cost):</strong> Full-day trip to Timur\'s birthplace (90km south). See the ruins of his massive Ak-Saray Palace, visit his family crypts, and cross the scenic Tahtakaracha Pass (1,788m) with mountain views. Stop at Konigil paper-making village. <em>Long day but rewarding for history enthusiasts.</em></p>

<p><strong>Option C - Tajikistan Taster (Optional, extra cost, requires advance visa):</strong> Cross the border to see Penjikent\'s Sogdian ruins and Sarazm UNESCO site (5,500 years old). <em>Only feasible with pre-arranged Tajik visa.</em></p>

<p><strong>Evening: Departure to Tashkent</strong></p>
<p><strong>19:00 - Board Afrosiyob Train to Tashkent:</strong> 2-hour journey. Dinner on your own (bistro car available or eat before departure).</p>

<p><strong>21:00 - Arrive Tashkent:</strong> Transfer to hotel. Rest for tomorrow\'s early start to Fergana Valley.</p>

<p><strong>Overnight:</strong> Tashkent 3-star hotel</p>

<p><em>Meals: Breakfast included.</em></p>',
                'type' => 'day',
                'default_start_time' => '09:00',
                'duration_minutes' => 1440,
                'sort_order' => 9
            ],

            [
                'title' => 'Day 10: Fergana Valley - Kokand, Rishtan, Fergana',
                'description' => '<h4>Journey to the Heartland of Uzbek Culture</h4>
<p><strong>07:00 - Early Departure from Tashkent:</strong> Drive through the scenic Kamchik Pass tunnel (2km, opened 2016) connecting Tashkent to the fertile Fergana Valley. The valley, sandwiched between the Tian Shan and Gissar-Alai ranges, has been the cultural heartland for millennia.</p>

<p><strong>10:00 - Kokand (2.5 hours):</strong> Once the capital of the powerful Kokand Khanate (1709-1876), this city rivals the major Silk Road centers but receives far fewer tourists.</p>

<p><strong>Highlights:</strong></p>
<ul>
    <li><strong>Khudayar Khan\'s Palace:</strong> The opulent 1873 palace with intricate tilework and carved wood. Half the building housed the Khan\'s harem. Now a museum showcasing royal life.</li>
    <li><strong>Jami Mosque:</strong> Impressive Friday mosque from 1809 with forest of wooden columns</li>
    <li><strong>Modari Khan Mausoleum:</strong> Beautiful memorial to the Khan\'s mother</li>
</ul>

<p><strong>12:30 - Lunch (Included):</strong> Local restaurant serving Fergana-style cuisine (distinct from other regions).</p>

<p><strong>14:00 - Rishtan Ceramics (2 hours):</strong> Visit the ancient center of Central Asian ceramics, famous for its unique blue glazes. Tour a master craftsman\'s workshop to see:</p>
<ul>
    <li>Clay preparation from local red clay</li>
    <li>Pottery wheel throwing techniques</li>
    <li>Painting with mineral and vegetable dyes</li>
    <li><strong>Ishkor glaze preparation:</strong> The secret alkaline glaze recipe passed through generations that creates Rishtan\'s distinctive luminous blue</li>
    <li>Traditional kiln firing</li>
</ul>
<p>Opportunity to purchase authentic pieces directly from artisans (far better quality and price than bazaar resellers).</p>

<p><strong>16:30 - Drive to Fergana City:</strong> Check into hotel.</p>

<p><strong>18:00 - Evening Walk:</strong> Explore Fergana\'s central park and Al-Fergani monument.</p>

<p><strong>Overnight:</strong> Fergana 3-star hotel</p>

<p><em>Meals: Breakfast and lunch included.</em></p>',
                'type' => 'day',
                'default_start_time' => '07:00',
                'duration_minutes' => 1440,
                'sort_order' => 10
            ],

            [
                'title' => 'Day 11: Margilan Silk, Return to Tashkent & Departure',
                'description' => '<h4>Morning: Silk Road Heritage Alive</h4>
<p><strong>08:30 - Yodgorlik Silk Factory, Margilan (2.5 hours):</strong> The tour\'s grand finale at one of Central Asia\'s last traditional silk workshops. Margilan has been renowned for silk since the 10th century.</p>

<p><strong>Witness the complete silk-making process:</strong></p>
<ul>
    <li><strong>Sericulture:</strong> Silkworms feeding on mulberry leaves</li>
    <li><strong>Cocoon harvesting and boiling:</strong> Extracting the precious threads</li>
    <li><strong>Thread spinning:</strong> Creating the fine silk yarn</li>
    <li><strong>Natural dyeing:</strong> Using pomegranate skins, indigo, and other traditional materials</li>
    <li><strong>Ikat (abr) technique:</strong> The complex resist-dyeing before weaving</li>
    <li><strong>Hand-loom weaving:</strong> Master weavers creating intricate patterns</li>
    <li><strong>Atlas and adras silk varieties:</strong> Understanding the differences</li>
</ul>

<p>This living tradition connects you directly to the ancient Silk Road\'s reason for existence. Over 200 artisans work here preserving techniques unchanged for centuries.</p>

<p><strong>11:00 - Margilan Bazaar (45 minutes):</strong> Brief visit to see local life and purchase last-minute souvenirs.</p>

<p><strong>12:00 - Lunch (Included):</strong> Final group meal in Fergana Valley.</p>

<h4>Afternoon: Return Journey</h4>
<p><strong>13:30 - Drive Back to Tashkent (4 hours):</strong> Scenic journey back through Kamchik Pass with stop for photos.</p>

<p><strong>17:30 - Arrive Tashkent:</strong> Depending on your flight time:</p>

<p><strong>Evening Flight (after 21:00):</strong> Transfer to hotel for refresh, short rest, then airport transfer. <em>Day room available if needed (extra cost).</em></p>

<p><strong>Late Night/Next Day Flight:</strong> Check into hotel, farewell dinner with group, overnight in Tashkent. Morning transfer to airport.</p>

<p><strong>Tour Concludes:</strong> With memories of 2,500 years of history, three UNESCO cities, forbidden art, ancient fortresses, and master craftspeople keeping traditions alive.</p>

<p><em>Meals: Breakfast and lunch included.</em></p>

<p><em>Note: We can arrange post-tour extensions (additional nights in Tashkent, Aydarkul Lake camping, etc.) upon request.</em></p>',
                'type' => 'day',
                'default_start_time' => '08:30',
                'duration_minutes' => 1440,
                'sort_order' => 11
            ],
        ];

        foreach ($itinerary as $item) {
            $tour->itineraryItems()->create($item);
        }

        // Update city tour count cache
        $tourCount = $tashkent->tours()->where('is_active', true)->count();
        $tashkent->tour_count_cache = $tourCount;
        $tashkent->save();

        $this->info("✅ 11-Day Grand Tour created successfully!");
        $this->info("Tour ID: {$tour->id}");
        $this->info("Title: {$tour->title}");
        $this->info("Price: $1,895 USD per person");
        $this->info("URL: http://127.0.0.1:8000/tours/{$tour->slug}");
        $this->info("Itinerary days: " . $tour->itineraryItems()->count());

        return 0;
    }
}
