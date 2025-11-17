<?php

namespace App\Console\Commands;

use App\Models\City;
use App\Models\Tour;
use App\Models\TourCategory;
use App\Models\ItineraryItem;
use Illuminate\Console\Command;

class CreatePamirSilkRoadTour extends Command
{
    protected $signature = 'create:pamir-silk-road-tour';
    protected $description = 'Create Pamir Highway & Silk Road Odyssey: Tajikistan-Uzbekistan Epic Journey';

    public function handle()
    {
        $this->info('Creating Pamir Highway & Silk Road Odyssey Tour...');

        // Get existing cities
        $dushanbe = City::firstOrCreate(
            ['slug' => 'dushanbe'],
            [
                'name' => 'Dushanbe',
                'country' => 'Tajikistan',
                'region' => 'Dushanbe Region',
                'latitude' => 38.5598,
                'longitude' => 68.7738,
                'description' => 'Capital of Tajikistan, gateway to the Pamir Mountains',
                'is_popular' => true,
            ]
        );

        $samarkand = City::where('slug', 'samarkand')->first();
        $bukhara = City::where('slug', 'bukhara')->first();

        // Get categories
        $categories = TourCategory::whereIn('slug', [
            'adventure',
            'cultural',
            'mountain',
        ])->get();

        // Create tour
        $tour = Tour::create([
            'title' => 'Pamir Highway & Silk Road Odyssey: Tajikistan to Uzbekistan Epic Journey',
            'slug' => 'pamir-highway-silk-road-odyssey-tajikistan-uzbekistan',

            'short_description' => 'Ultimate Central Asia adventure: Conquer the legendary Pamir Highway (2nd highest road in the world) through Tajikistan, then explore Uzbekistan\'s UNESCO Silk Road cities. 14 days from mountains to minarets.',
            'long_description' => '<h2>Pamir Highway & Silk Road Odyssey</h2>
<p>This is the adventure of a lifetime - combining two of Central Asia\'s most iconic experiences into one unforgettable 14-day journey.</p>

<p><strong>Part 1: Pamir Highway (Days 1-7)</strong> - Traverse the "Roof of the World" on the legendary Pamir Highway, the second-highest international highway on Earth. Drive through Tajikistan\'s stunning Wakhan Corridor, cross passes above 4,600m, visit remote Pamiri villages, stay in homestays, and witness landscapes that inspired ancient Silk Road travelers.</p>

<p><strong>Part 2: Silk Road Cities (Days 8-14)</strong> - Descend from the mountains into Uzbekistan\'s desert heartland. Explore the architectural wonders of Samarkand, Bukhara, and Tashkent - UNESCO World Heritage cities where Silk Road caravans once traded goods from China to Europe.</p>

<h3>Why This Tour is Extraordinary</h3>
<ul>
    <li><strong>Pamir Highway:</strong> 1,200 km of the world\'s most spectacular mountain road, reaching Ak-Baital Pass (4,655m) - higher than any point in Europe or North America</li>
    <li><strong>Wakhan Corridor:</strong> Afghanistan border region with ancient fortresses, hot springs, and views of 7,000m Hindu Kush peaks</li>
    <li><strong>Iskanderkul Lake:</strong> Turquoise alpine lake named after Alexander the Great</li>
    <li><strong>Pamiri Culture:</strong> Stay in family homestays, learn about Ismaili traditions, share meals with mountain communities</li>
    <li><strong>UNESCO Triple:</strong> Samarkand Registan, Bukhara Historic Centre, and multiple ancient monuments</li>
    <li><strong>Cross-Border Epic:</strong> Seamless journey through Tajikistan and Uzbekistan with all logistics handled</li>
</ul>

<h3>Perfect For</h3>
<p>Adventure travelers, photographers, culture enthusiasts aged 25-60 with good fitness. This is a serious adventure with long drives, high altitudes, and basic accommodations in the Pamirs, balanced with comfortable hotels in Uzbekistan. For those seeking the road less traveled combined with Silk Road grandeur.</p>',

            'duration_days' => 14,
            'duration_text' => '14 days / 13 nights',
            'tour_type' => 'group_only',
            'city_id' => $dushanbe->id,

            'price_per_person' => 2895.00,
            'currency' => 'USD',
            'max_guests' => 10,
            'min_guests' => 4,

            'hero_image' => 'images/tours/pamir-silk-road/pamir-highway-mountain-road.webp',
            'gallery_images' => json_encode([
                ['path' => 'images/tours/pamir-silk-road/ak-baital-pass-4655m.webp', 'alt' => 'Ak-Baital Pass 4655m Pamir Highway Tajikistan'],
                ['path' => 'images/tours/pamir-silk-road/wakhan-corridor-afghanistan.webp', 'alt' => 'Wakhan Corridor with Afghanistan border Tajikistan'],
                ['path' => 'images/tours/pamir-silk-road/iskanderkul-lake.webp', 'alt' => 'Iskanderkul turquoise alpine lake Tajikistan'],
                ['path' => 'images/tours/pamir-silk-road/pamiri-homestay.webp', 'alt' => 'Traditional Pamiri homestay family dinner'],
                ['path' => 'images/tours/pamir-silk-road/registan-samarkand.webp', 'alt' => 'Registan Square Samarkand Uzbekistan Silk Road'],
                ['path' => 'images/tours/pamir-silk-road/bukhara-poi-kalyan.webp', 'alt' => 'Poi-Kalyan Complex Bukhara Uzbekistan'],
            ]),

            'highlights' => [
                'Pamir Highway - 2nd highest international highway (reaches 4,655m at Ak-Baital Pass)',
                'Iskanderkul Lake & Fann Mountains - turquoise alpine lake named after Alexander the Great',
                'Kalaikhum - drive along Afghanistan border with Hindu Kush views',
                'Wakhan Corridor - remote frontier with ancient Silk Road fortresses',
                'Garm Chashma hot springs - natural thermal pools near Afghan border',
                'Yamchun Fortress & Bibi Fatima hot springs',
                'Khorog - capital of Gorno-Badakhshan region, Pamiri culture hub',
                'Murghab - highest town on Pamir Highway (3,600m elevation)',
                'Karakul Lake - stunning high-altitude lake (3,900m) with desert mountains',
                'Pamiri homestays - authentic family experiences, traditional meals',
                'Border crossing to Uzbekistan via ancient routes',
                'Samarkand Registan Square, Shah-i-Zinda, Gur-Emir Mausoleum',
                'Bukhara Ark Fortress, Poi-Kalyan Complex, historic trading domes',
                'High-speed Afrosiyob train between Silk Road cities',
            ],

            'included_items' => [
                '13 nights accommodation (hotels, homestays, guesthouses)',
                '13 breakfasts, 10 lunches, 10 dinners',
                'English-speaking guide throughout',
                '4WD vehicles for Pamir Highway (Russian UAZ or Land Cruiser)',
                'All entrance fees to monuments, museums, national parks',
                'GBAO permit for Gorno-Badakhshan region (required for Pamir Highway)',
                'Border crossing assistance Tajikistan-Uzbekistan',
                'Afrosiyob high-speed train Bukhara-Samarkand',
                'Pamiri homestay experiences with local families',
                'Airport transfers in Dushanbe and Tashkent',
                'Support vehicle for Pamir Highway section',
            ],

            'excluded_items' => [
                'International flights to Dushanbe / from Tashkent',
                'Tajikistan and Uzbekistan visa fees (if applicable)',
                'Meals not mentioned (3 lunches, 3 dinners - approx $10-15 per meal)',
                'Personal travel insurance',
                'Tips for guides, drivers, and homestay families',
                'Alcoholic beverages',
                'Personal expenses and souvenirs',
                'Single room supplement ($450 for entire tour)',
            ],

            'requirements' => [
                'Good physical fitness - able to handle high altitudes up to 4,655m',
                'Comfortable with long driving days (6-8 hours on rough mountain roads)',
                'Flexible mindset - Pamir Highway conditions can change (landslides, weather delays)',
                'Comfortable with basic homestay facilities in Tajikistan (shared bathrooms, simple beds)',
                'Valid passport with 6 months validity and blank visa pages',
                'Tajikistan e-visa required for most nationalities',
                'Uzbekistan visa-free for most nationalities or e-visa',
                'Travel insurance covering high-altitude adventure activities mandatory',
                'Recommended: altitude sickness medication (consult your doctor)',
            ],

            'is_active' => true,
        ]);

        // Attach categories
        $tour->categories()->attach($categories->pluck('id'));

        $this->info('Creating itinerary...');

        // Day 1
        ItineraryItem::create([
            'tour_id' => $tour->id,
            'day_number' => 1,
            'type' => 'day',
            'title' => 'Arrival in Dushanbe - Capital City Tour',
            'description' => '<p>Welcome to Tajikistan, land of towering peaks and ancient routes!</p>
<p><strong>Airport Pickup</strong> - Meet your guide at Dushanbe International Airport and transfer to your hotel in the capital.</p>
<p><strong>Dushanbe City Tour (afternoon):</strong></p>
<ul>
<li><strong>National Museum of Tajikistan:</strong> Incredible collection including 13-meter reclining Buddha from Kulyab (largest in Central Asia), ancient Sogdian artifacts, and exhibits on Pamiri culture</li>
<li><strong>Flagpole Park:</strong> Home to one of the world\'s tallest flagpoles (165m) - symbol of Tajik independence</li>
<li><strong>Rudaki Park:</strong> Green heart of the city, perfect for people-watching</li>
<li><strong>Green Bazaar:</strong> Bustling market with dried fruits, nuts, spices, and traditional handicrafts</li>
</ul>
<p><strong>Briefing</strong> - Evening meeting with your guide to discuss the Pamir Highway journey ahead, altitude acclimatization, and what to expect.</p>
<p><strong>Welcome Dinner</strong> - Traditional Tajik restaurant with osh (pilaf), shashlik, and sambusa.</p>
<p><em>Meals: Dinner</em><br>
<em>Accommodation: Hotel in Dushanbe</em></p>',
            'duration' => '5 hours',
            'order' => 1,
        ]);

        // Day 2
        ItineraryItem::create([
            'tour_id' => $tour->id,
            'day_number' => 2,
            'type' => 'day',
            'title' => 'Dushanbe - Iskanderkul Lake - Kalaikhum',
            'description' => '<p>Begin the legendary journey into the mountains.</p>
<p><strong>Drive to Iskanderkul Lake (135 km, ~3 hours)</strong> - Climb from Dushanbe (800m) into the Fann Mountains via Anzob Pass (3,372m). Dramatic gorges, rushing rivers, and your first taste of Tajik mountain roads.</p>
<p><strong>Iskanderkul Lake</strong> - Named after Alexander the Great (Iskander in Persian) who legend says stopped here during his conquests:</p>
<ul>
<li>Stunning turquoise glacial lake at 2,195m elevation</li>
<li>Surrounded by peaks reaching 5,000m</li>
<li>Hike to Snake Lake viewpoint (30 min) for panoramic views</li>
<li>Visit Fann Niagara waterfall - 38-meter cascade into emerald pool</li>
<li>Picnic lunch by the lake</li>
</ul>
<p><strong>Drive to Kalaikhum (280 km, ~6 hours)</strong> - Descend to the Panj River valley. From here the river forms the border with Afghanistan - you\'ll see Afghan villages on the opposite bank. Enter the world of the Pamir Highway.</p>
<p><strong>Overnight Kalaikhum</strong> - Simple guesthouse in this frontier town.</p>
<p><em>Meals: Breakfast, Lunch, Dinner</em><br>
<em>Accommodation: Guesthouse in Kalaikhum</em><br>
<em>Driving: ~9 hours total</em></p>',
            'duration' => '11 hours',
            'order' => 2,
        ]);

        // Day 3
        ItineraryItem::create([
            'tour_id' => $tour->id,
            'day_number' => 3,
            'type' => 'day',
            'title' => 'Kalaikhum - Wakhan Corridor - Khorog',
            'description' => '<p>Enter the legendary Wakhan Corridor along the Afghan border.</p>
<p><strong>Wakhan Corridor Drive</strong> - One of the most spectacular sections of the Pamir Highway:</p>
<ul>
<li>Road follows the Panj River separating Tajikistan from Afghanistan</li>
<li>Views of Hindu Kush mountains rising to 7,000m+ on the Afghan side</li>
<li>Wave to Afghan farmers across the river working their fields</li>
<li>Ancient Silk Road caravans used this same route</li>
</ul>
<p><strong>Stops Along the Way:</strong></p>
<ul>
<li><strong>Yamchun Fortress:</strong> 12th-century fort perched on cliff above the Panj River - scramble up for breathtaking valley views</li>
<li><strong>Bibi Fatima Hot Springs:</strong> Natural thermal springs cascading down cliffs, forming calcite terraces. Locals believe these waters heal ailments. Soak your feet!</li>
<li><strong>Garm Chashma:</strong> More hot springs in a spectacular gorge setting</li>
</ul>
<p><strong>Khorog Arrival</strong> - Capital of Gorno-Badakhshan Autonomous Region (GBAO). Elevation 2,200m. Gateway to the high Pamirs.</p>
<p><strong>Evening:</strong> Stroll through Khorog\'s bazaar, visit the Pamiri-style mosque, enjoy views of the Panj River valley.</p>
<p><em>Meals: Breakfast, Lunch, Dinner</em><br>
<em>Accommodation: Hotel in Khorog</em><br>
<em>Driving: ~7 hours (250 km)</em></p>',
            'duration' => '9 hours',
            'order' => 3,
        ]);

        // Day 4
        ItineraryItem::create([
            'tour_id' => $tour->id,
            'day_number' => 4,
            'type' => 'day',
            'title' => 'Khorog - Pamir Highway - Murghab',
            'description' => '<p>Ascend to the high Pamirs - the true "Roof of the World."</p>
<p><strong>Morning in Khorog:</strong> Visit the Pamiri Museum to learn about Ismaili culture and traditional Pamiri houses with their distinctive five pillars representing Islamic beliefs.</p>
<p><strong>Pamir Highway Ascent:</strong></p>
<ul>
<li>Leave the Wakhan valley and climb toward the Pamir plateau</li>
<li>Landscape transforms from green valleys to high-altitude desert</li>
<li>Cross multiple passes, each revealing more dramatic vistas</li>
<li>Sparse vegetation - only tough grasses and hardy shrubs survive</li>
</ul>
<p><strong>Murghab Arrival</strong> - One of the highest towns in the former Soviet Union at 3,600m elevation:</p>
<ul>
<li>Population: ~5,000 mostly Kyrgyz and Pamiri people</li>
<li>Soviet-era buildings in a stark, windswept valley</li>
<li>Important stop on the Pamir Highway for fuel and supplies</li>
<li>Tonight you\'ll really feel the altitude - take it easy, drink lots of water</li>
</ul>
<p><strong>Evening:</strong> Walk around town, visit the small bazaar, experience frontier life at the edge of civilization.</p>
<p><em>Meals: Breakfast, Lunch, Dinner</em><br>
<em>Accommodation: Guesthouse in Murghab</em><br>
<em>Driving: ~7 hours (290 km)</em></p>',
            'duration' => '9 hours',
            'order' => 4,
        ]);

        // Day 5
        ItineraryItem::create([
            'tour_id' => $tour->id,
            'day_number' => 5,
            'type' => 'day',
            'title' => 'Murghab - Karakul Lake - Ak-Baital Pass - Sary-Tash (Kyrgyzstan)',
            'description' => '<p>Conquer the highest point of the Pamir Highway and cross into Kyrgyzstan.</p>
<p><strong>Karakul Lake</strong> - Drive to this stunning high-altitude lake at 3,900m:</p>
<ul>
<li>Vast black lake surrounded by barren desert mountains</li>
<li>Formed by a meteor impact 25 million years ago</li>
<li>Colors shift from deep blue to black depending on light</li>
<li>Peak Muztag Ata (7,546m) visible on the border with China</li>
<li>Kyrgyz yurt camps dot the shores - nomadic herders graze yaks</li>
</ul>
<p><strong>Ak-Baital Pass (4,655m)</strong> - The climax of the Pamir Highway:</p>
<ul>
<li>Second-highest international paved road in the world</li>
<li>Higher than Mont Blanc (4,808m), Matterhorn (4,478m), or any peak in the Alps</li>
<li>Barren, moonlike landscape - you\'re above most vegetation</li>
<li>Photo stop at the pass marker - this is your summit moment!</li>
<li>Thin air - walk slowly, breathe deeply</li>
</ul>
<p><strong>Descent to Sary-Tash, Kyrgyzstan</strong> - Cross from Tajikistan into Kyrgyzstan via Kyzyl-Art Pass (4,282m). Your guide handles border formalities.</p>
<p><strong>Sary-Tash</strong> - Small village at the junction of roads to Kyrgyzstan, Tajikistan, and China. Overnight in basic guesthouse.</p>
<p><em>Meals: Breakfast, Lunch, Dinner</em><br>
<em>Accommodation: Guesthouse in Sary-Tash, Kyrgyzstan</em><br>
<em>Driving: ~6 hours (200 km) + border crossing</em></p>',
            'duration' => '8 hours',
            'order' => 5,
        ]);

        // Day 6
        ItineraryItem::create([
            'tour_id' => $tour->id,
            'day_number' => 6,
            'type' => 'day',
            'title' => 'Sary-Tash - Osh - Fergana Valley - Tajikistan Border - Khujand',
            'description' => '<p>Descend from the mountains and cross back into Tajikistan.</p>
<p><strong>Drive to Osh</strong> - Kyrgyzstan\'s second city at 1,000m elevation. Your lungs will appreciate the thicker air!</p>
<p><strong>Osh City Tour:</strong></p>
<ul>
<li><strong>Sulaiman-Too Sacred Mountain:</strong> UNESCO site, sacred for 3,000 years. Climb to the top for city views and visit cave shrines</li>
<li><strong>Osh Bazaar:</strong> One of Central Asia\'s oldest and largest markets - 2,000+ years of continuous trade. Mountains of dried fruits, spices, textiles</li>
<li>Lunch at local chaikhana</li>
</ul>
<p><strong>Fergana Valley Drive</strong> - Enter this fertile valley, heart of Central Asian silk production for centuries.</p>
<p><strong>Border to Tajikistan</strong> - Cross back into Tajikistan at Batken crossing.</p>
<p><strong>Khujand</strong> - Tajikistan\'s second-largest city, founded by Alexander the Great in 329 BCE as Alexandria Eschate ("Alexandria the Furthest").</p>
<p><em>Meals: Breakfast, Lunch, Dinner</em><br>
<em>Accommodation: Hotel in Khujand, Tajikistan</em><br>
<em>Driving: ~8 hours including border</em></p>',
            'duration' => '10 hours',
            'order' => 6,
        ]);

        // Day 7
        ItineraryItem::create([
            'tour_id' => $tour->id,
            'day_number' => 7,
            'type' => 'day',
            'title' => 'Khujand - Ancient Penjikent - Samarkand (Uzbekistan)',
            'description' => '<p>Cross into Uzbekistan and arrive at the legendary Samarkand.</p>
<p><strong>Khujand Morning:</strong></p>
<ul>
<li><strong>Panjshanbe Bazaar:</strong> Massive covered market under a beautiful blue dome</li>
<li><strong>Arbob Cultural Palace:</strong> Former Soviet collective farm headquarters - over-the-top socialist architecture</li>
</ul>
<p><strong>Ancient Penjikent</strong> - Stop at this Sogdian archaeological site:</p>
<ul>
<li>Ruins of a 5th-7th century Silk Road city</li>
<li>Mud-brick walls, temples, and residential quarters</li>
<li>Museum with incredible frescoes showing Sogdian life</li>
</ul>
<p><strong>Tajikistan-Uzbekistan Border</strong> - Cross at Jartepa border post. Farewell to the mountains, welcome to the desert!</p>
<p><strong>Samarkand Arrival</strong> - As the sun sets, arrive in one of the world\'s oldest continuously inhabited cities (founded 700 BCE). Tonight, the journey shifts from mountains to monuments.</p>
<p><strong>Evening:</strong> First glimpse of Registan Square illuminated at night - a magical introduction to Silk Road grandeur.</p>
<p><em>Meals: Breakfast, Lunch</em><br>
<em>Accommodation: Hotel in Samarkand, Uzbekistan</em><br>
<em>Driving: ~5 hours + border crossing</em></p>',
            'duration' => '8 hours',
            'order' => 7,
        ]);

        // Day 8
        ItineraryItem::create([
            'tour_id' => $tour->id,
            'day_number' => 8,
            'type' => 'day',
            'title' => 'Samarkand Full Day - UNESCO Silk Road Monuments',
            'description' => '<p>Explore Tamerlane\'s capital - the jewel of the Silk Road.</p>
<p><strong>Registan Square</strong> - The most iconic image of Central Asia:</p>
<ul>
<li>Three madrasahs (Islamic schools) built 15th-17th centuries</li>
<li>Ulugbek, Sher-Dor, and Tilya-Kori madrasahs</li>
<li>Intricate tilework, towering minarets, grand archways</li>
<li>Detailed guided tour explaining architecture and history</li>
</ul>
<p><strong>Gur-Emir Mausoleum</strong> - Tomb of Timur (Tamerlane):</p>
<ul>
<li>Stunning ribbed azure dome</li>
<li>Interior with jade tombstones and gold decoration</li>
<li>Final resting place of the conqueror who built an empire from Delhi to Damascus</li>
</ul>
<p><strong>Shah-i-Zinda Necropolis</strong> - "Street of mausoleums" with the finest Islamic tilework on Earth:</p>
<ul>
<li>11 mausoleums built 11th-19th centuries</li>
<li>Each a masterpiece of azure, turquoise, and cobalt majolica</li>
<li>Pilgrimage site - believed to hold a cousin of Prophet Muhammad</li>
</ul>
<p><strong>Ulugbek Observatory</strong> - Astronomical observatory built 1420s by Ulugbek, Timur\'s grandson:</p>
<ul>
<li>Calculated the length of a year to within 1 minute of modern measurements</li>
<li>Massive stone sextant ruins</li>
<li>Museum explaining medieval Islamic astronomy</li>
</ul>
<p><strong>Bibi Khanym Mosque</strong> - Once one of the Islamic world\'s largest mosques (1399-1404).</p>
<p><strong>Siyob Bazaar</strong> - Local market with spices, bread, dried fruits, suzani embroidery.</p>
<p><em>Meals: Breakfast, Lunch, Dinner</em><br>
<em>Accommodation: Hotel in Samarkand</em></p>',
            'duration' => '8 hours',
            'order' => 8,
        ]);

        // Day 9
        ItineraryItem::create([
            'tour_id' => $tour->id,
            'day_number' => 9,
            'type' => 'day',
            'title' => 'Samarkand - Afrosiyob Train - Bukhara',
            'description' => '<p>High-speed rail journey to the holy city of Bukhara.</p>
<p><strong>Morning in Samarkand:</strong> Free time for last photos at Registan or souvenir shopping.</p>
<p><strong>Afrosiyob High-Speed Train to Bukhara</strong> - Experience Uzbekistan\'s modern side:</p>
<ul>
<li>Comfortable business-class seats</li>
<li>Journey time: ~1.5 hours (vs 5 hours by road)</li>
<li>Speed: up to 250 km/h through the Kyzylkum Desert</li>
<li>Snacks and refreshments served</li>
</ul>
<p><strong>Bukhara Arrival</strong> - The "Noble Bukhara," one of the Islamic world\'s most sacred cities (250+ historic buildings).</p>
<p><strong>Afternoon Tour:</strong></p>
<ul>
<li><strong>Lyabi-Hauz Complex:</strong> Central plaza around a historic pool, lined with mulberry trees, madrasahs, and chaikhanas</li>
<li><strong>Chor-Minor:</strong> Quirky four-towered gatehouse (1807) - like a wedding cake of blue domes</li>
<li>Walk through the old town - every alley reveals architectural treasures</li>
</ul>
<p><strong>Evening:</strong> Dinner at a traditional restaurant. Optional: attend a folklore show with Silk Road music and dance.</p>
<p><em>Meals: Breakfast, Dinner</em><br>
<em>Accommodation: Boutique hotel in Bukhara historic center</em></p>',
            'duration' => '6 hours',
            'order' => 9,
        ]);

        // Day 10
        ItineraryItem::create([
            'tour_id' => $tour->id,
            'day_number' => 10,
            'type' => 'day',
            'title' => 'Bukhara Full Day - Living Museum of Silk Road',
            'description' => '<p>Immerse yourself in 2,500 years of history in Central Asia\'s holiest city.</p>
<p><strong>Ark Fortress</strong> - Massive citadel (5th century BCE foundations):</p>
<ul>
<li>Former residence of Bukharan emirs</li>
<li>Museums of local history and archaeology</li>
<li>Stunning city views from ramparts</li>
</ul>
<p><strong>Bolo Hauz Mosque</strong> - "Mosque of the Emir" with beautiful wooden columns.</p>
<p><strong>Poi-Kalyan Complex</strong> - Bukhara\'s architectural heart:</p>
<ul>
<li><strong>Kalyan Minaret (1127):</strong> 47-meter "Tower of Death" - never destroyed by Mongols due to its beauty</li>
<li><strong>Kalyan Mosque:</strong> One of Central Asia\'s largest, holds 10,000 worshippers</li>
<li><strong>Mir-i Arab Madrasah:</strong> Still-functioning Islamic school</li>
</ul>
<p><strong>Ulugbek & Abdulazizkhan Madrasahs</strong> - Facing pair showcasing different eras of Islamic architecture.</p>
<p><strong>Trading Domes (Toki):</strong> 16th-century covered bazaars:</p>
<ul>
<li>Toki Sarrafon (moneychangers)</li>
<li>Toki Telpak Furushon (cap sellers)</li>
<li>Toki Zargaron (jewelers)</li>
<li>Shop for suzani embroidery, ceramics, miniatures</li>
</ul>
<p><strong>Lab-i Hauz Evening</strong> - Return to the central pool for sunset. Enjoy tea and people-watching as the call to prayer echoes across ancient rooftops.</p>
<p><em>Meals: Breakfast, Lunch</em><br>
<em>Accommodation: Boutique hotel in Bukhara</em></p>',
            'duration' => '7 hours',
            'order' => 10,
        ]);

        // Day 11
        ItineraryItem::create([
            'tour_id' => $tour->id,
            'day_number' => 11,
            'type' => 'day',
            'title' => 'Bukhara - Desert Drive - Khiva',
            'description' => '<p>Cross the Kyzylkum Desert to the ancient oasis of Khiva.</p>
<p><strong>Morning in Bukhara:</strong> Last walk through the old town, final shopping, or visit sites missed yesterday.</p>
<p><strong>Kyzylkum Desert Drive (450 km, ~7 hours)</strong>:</p>
<ul>
<li>Cross the "Red Sands" desert separating Bukhara from Khiva</li>
<li>Follow the ancient Silk Road caravan route</li>
<li>Pass the Amu Darya River (ancient Oxus River - one of Central Asia\'s great rivers)</li>
<li>Watch landscape transform from steppe to desert to irrigated fields</li>
<li>Lunch stop in Navoiy or roadside chaikhana</li>
</ul>
<p><strong>Khiva Arrival</strong> - As you approach, the massive walls of Itchan Kala appear like a mirage in the desert.</p>
<p><strong>Evening:</strong> First walk into the walled old town - like stepping into a medieval time capsule. The architecture is so intact it feels like a film set.</p>
<p><em>Meals: Breakfast, Lunch, Dinner</em><br>
<em>Accommodation: Hotel inside Itchan Kala (the walled old city)</em><br>
<em>Driving: ~7 hours</em></p>',
            'duration' => '9 hours',
            'order' => 11,
        ]);

        // Day 12
        ItineraryItem::create([
            'tour_id' => $tour->id,
            'day_number' => 12,
            'type' => 'day',
            'title' => 'Khiva Full Day - Itchan Kala UNESCO Site',
            'description' => '<p>Explore the best-preserved Silk Road city - a living museum.</p>
<p><strong>Itchan Kala</strong> - The inner walled city (UNESCO World Heritage):</p>
<ul>
<li>2.5 km of clay walls enclosing 26 hectares of monuments</li>
<li>60+ historic buildings all within walking distance</li>
<li>Final major oasis before the Karakum Desert</li>
</ul>
<p><strong>Key Sites:</strong></p>
<ul>
<li><strong>Kalta Minor Minaret:</strong> Iconic blue-tiled stump - planned to be 70m but never finished</li>
<li><strong>Kunya Ark:</strong> "Old Fortress" - residence of Khivan khans with throne room and harem</li>
<li><strong>Juma Mosque (10th century):</strong> 213 wooden columns, each unique, some over 1,000 years old</li>
<li><strong>Tash Hauli Palace:</strong> "Stone Courtyard" - stunning tiled courtyards, harem, and throne room</li>
<li><strong>Islam Khodja Minaret:</strong> Climb Khiva\'s tallest minaret (56m) for rooftop views across the oasis</li>
<li><strong>Pahlavan Mahmud Mausoleum:</strong> Khiva\'s patron saint - beautiful tilework</li>
<li><strong>Museum of Applied Arts:</strong> Traditional Khorezm crafts</li>
</ul>
<p><strong>Workshops:</strong> Visit working artisans - wood carvers, ceramicists, miniature painters continuing centuries-old traditions.</p>
<p><strong>Sunset:</strong> Climb the city walls for golden-hour views of minarets and domes.</p>
<p><strong>Farewell Dinner:</strong> Traditional Khorezm cuisine in atmospheric restaurant.</p>
<p><em>Meals: Breakfast, Lunch, Dinner</em><br>
<em>Accommodation: Hotel in Itchan Kala</em></p>',
            'duration' => 'Full day',
            'order' => 12,
        ]);

        // Day 13
        ItineraryItem::create([
            'tour_id' => $tour->id,
            'day_number' => 13,
            'type' => 'day',
            'title' => 'Khiva - Flight to Tashkent - Capital City Tour',
            'description' => '<p>Fly to Uzbekistan\'s capital for the final chapter.</p>
<p><strong>Morning Flight Khiva-Tashkent</strong> - Domestic flight (~1.5 hours) over desert and mountains.</p>
<p><strong>Tashkent City Tour:</strong></p>
<ul>
<li><strong>Khast-Imam Complex:</strong> Religious heart of Tashkent with the oldest Quran in the world (Uthman Quran from 655 CE, with bloodstains of the Caliph Uthman)</li>
<li><strong>Chorsu Bazaar:</strong> Massive domed market - mountains of spices, dried fruits, bread</li>
<li><strong>Independence Square:</strong> Central Asia\'s largest public square</li>
<li><strong>Tashkent Metro:</strong> Stunning Soviet-era "underground palaces" with chandeliers, marble, and mosaics - one of the world\'s most beautiful metros</li>
<li><strong>Broadway (Sayilgoh Street):</strong> Pedestrian street with artists, cafes, and souvenir stalls</li>
<li><strong>Amir Timur Square:</strong> Monument to Uzbekistan\'s national hero</li>
</ul>
<p><strong>Free Evening:</strong> Last night to explore, shop, or enjoy Tashkent\'s cafe culture.</p>
<p><strong>Celebration Dinner:</strong> Reflect on your epic journey from the Roof of the World to the Silk Road cities.</p>
<p><em>Meals: Breakfast, Dinner</em><br>
<em>Accommodation: Hotel in Tashkent</em></p>',
            'duration' => '7 hours',
            'order' => 13,
        ]);

        // Day 14
        ItineraryItem::create([
            'tour_id' => $tour->id,
            'day_number' => 14,
            'type' => 'day',
            'title' => 'Tashkent - Departure',
            'description' => '<p>Final morning in Central Asia.</p>
<p><strong>Leisure Time</strong> - Depending on your flight schedule:</p>
<ul>
<li>Last visit to Chorsu Bazaar for souvenirs</li>
<li>Explore a metro station you missed</li>
<li>Relax at hotel</li>
<li>Visit Museum of Applied Arts (beautiful Uzbek crafts)</li>
</ul>
<p><strong>Airport Transfer</strong> - Your guide will arrange transfer to Tashkent International Airport.</p>
<p><strong>Departure</strong> - Fly home with memories of:</p>
<ul>
<li>Conquering the Pamir Highway and standing at 4,655m</li>
<li>Sleeping in Pamiri homestays and learning mountain traditions</li>
<li>Seeing Afghanistan from the Wakhan Corridor</li>
<li>Marveling at Samarkand\'s Registan at sunrise</li>
<li>Getting lost in Bukhara\'s ancient trading domes</li>
<li>Walking through Khiva\'s perfectly preserved medieval city</li>
<li>Two weeks that traced ancient Silk Road routes from mountains to deserts</li>
</ul>
<p><strong>Safe travels!</strong> До свидания! Xayr!</p>
<p><em>Meals: Breakfast</em><br>
<em>Accommodation: Not included</em></p>',
            'duration' => 'Half day',
            'order' => 14,
        ]);

        $this->info("✅ Tour created successfully: {$tour->title}");
        $this->info("Tour ID: {$tour->id}");
        $this->info("Slug: {$tour->slug}");

        return Command::SUCCESS;
    }
}
