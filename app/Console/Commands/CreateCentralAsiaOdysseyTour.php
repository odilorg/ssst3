<?php

namespace App\Console\Commands;

use App\Models\City;
use App\Models\Tour;
use App\Models\TourCategory;
use Illuminate\Console\Command;

class CreateCentralAsiaOdysseyTour extends Command
{
    protected $signature = 'create:central-asia-odyssey-tour';
    protected $description = 'Create 14-day Central Asia Odyssey multi-country tour';

    public function handle()
    {
        $this->info('Creating Central Asia Odyssey 4-Country Tour...');

        // Get Tashkent city (tour ends here)
        $tashkent = City::where('name', 'Tashkent')->first();
        if (!$tashkent) {
            $this->error('Tashkent city not found!');
            return 1;
        }

        $tour = Tour::create([
            // BASIC INFORMATION
            'title' => 'Central Asia Grand Odyssey: 4 Countries, 14 Days of Silk Road Discovery',
            'slug' => 'central-asia-grand-odyssey-4-countries-14-days',
            'short_description' => 'Journey through Kazakhstan, Kyrgyzstan, Tajikistan, and Uzbekistan - from alpine lakes and soaring peaks to ancient Silk Road cities and UNESCO treasures',
            'long_description' => '<h2>The Ultimate Central Asian Adventure</h2>
<p>This is the definitive Central Asian journey—a 14-day odyssey through four distinct countries, each with its own character, landscape, and cultural identity. From the cosmopolitan sophistication of Almaty to the mountain wilderness of Kyrgyzstan, from the ancient Sogdian heritage of Tajikistan to the architectural masterpieces of Uzbekistan\'s Silk Road cities, this tour reveals the full spectrum of Central Asia.</p>

<h3>Why This Tour is Extraordinary</h3>
<ul>
    <li><strong>Four Countries, One Epic Journey:</strong> Kazakhstan\'s modernity and mountains, Kyrgyzstan\'s nomadic traditions and alpine beauty, Tajikistan\'s Sogdian heritage and Fann Mountain vistas, Uzbekistan\'s triple UNESCO World Heritage sites</li>
    <li><strong>Dramatic Landscape Diversity:</strong> From Almaty\'s snow-capped Tian Shan peaks to Kyrgyzstan\'s alpine meadows, from Tajikistan\'s turquoise Iskanderkul Lake to Uzbekistan\'s golden desert cities</li>
    <li><strong>Three Border Crossings:</strong> Experience the cultural shifts as you cross from Kazakhstan to Kyrgyzstan, Kyrgyzstan to Uzbekistan (via Osh-Fergana), and Tajikistan to Uzbekistan</li>
    <li><strong>Ancient Silk Road Heritage:</strong> Walk through Penjikent\'s 5th-century Sogdian ruins, explore Samarkand\'s Registan, Bukhara\'s living medina, and Khiva\'s walled Itchan Kala</li>
    <li><strong>Soviet to Independent:</strong> Witness the evolution from Soviet-era Almaty and Bishkek to the revitalized Islamic architecture of Uzbekistan\'s restored cities</li>
    <li><strong>Fergana Valley Crafts:</strong> Deep dive into Central Asia\'s artisan heartland—Margilan\'s silk factory and Rishtan\'s ceramics workshops</li>
    <li><strong>Optimized Logistics:</strong> High-speed Afrosiyob trains between Uzbek cities, scenic mountain drives, domestic flights minimizing backtracking</li>
</ul>

<h3>Perfect For</h3>
<p>Adventurous cultural travelers, history enthusiasts, photographers, and those seeking to understand Central Asia\'s complexity beyond just Uzbekistan. This tour requires moderate fitness for some longer driving days and border crossings, but rewards with unparalleled cultural diversity. Ideal for ages 25-70 who appreciate both natural landscapes and historical architecture.</p>

<h3>What Makes This Different</h3>
<p>Unlike tours that focus solely on Uzbekistan, this odyssey provides context. You\'ll understand how Kyrgyzstan\'s nomadic culture contrasts with Uzbekistan\'s settled oasis civilizations, how Soviet influence manifests differently in each republic, and how the Fergana Valley connects (and divides) three nations. Our expert guides are changed at each border to ensure local expertise and language skills.</p>',

            // DURATION & TYPE
            'duration_days' => 14,
            'duration_text' => '14 days / 13 nights',
            'tour_type' => 'group_only',
            'city_id' => $tashkent->id, // Ends in Tashkent
            'is_active' => true,

            // PRICING & CAPACITY
            'price_per_person' => 2895.00, // Multi-country premium
            'currency' => 'USD',
            'max_guests' => 12,
            'min_guests' => 4,

            // IMAGES
            'hero_image' => 'images/tours/central-asia-odyssey/registan-khiva-issyk-kul-collage.webp',
            'gallery_images' => json_encode([
                ['path' => 'images/tours/central-asia-odyssey/almaty-big-almaty-lake-mountains.webp', 'alt' => 'Big Almaty Lake turquoise waters Tian Shan mountains Kazakhstan'],
                ['path' => 'images/tours/central-asia-odyssey/bishkek-ala-too-square.webp', 'alt' => 'Ala-Too Square with Manas statue Bishkek Kyrgyzstan'],
                ['path' => 'images/tours/central-asia-odyssey/iskanderkul-lake-tajikistan.webp', 'alt' => 'Crystal turquoise Iskanderkul Lake Fann Mountains Tajikistan'],
                ['path' => 'images/tours/central-asia-odyssey/penjikent-ancient-sogdian-ruins.webp', 'alt' => 'Ancient Sogdian city ruins Penjikent Tajikistan 5th century'],
                ['path' => 'images/tours/central-asia-odyssey/margilan-silk-factory-ikat.webp', 'alt' => 'Traditional ikat silk weaving Yodgorlik factory Margilan Fergana'],
                ['path' => 'images/tours/central-asia-odyssey/samarkand-registan-three-madrasahs.webp', 'alt' => 'Registan Square three madrasahs sunset Samarkand Uzbekistan UNESCO'],
                ['path' => 'images/tours/central-asia-odyssey/bukhara-poi-kalyan-ensemble.webp', 'alt' => 'Poi Kalyan complex Kalyan minaret Bukhara Uzbekistan'],
                ['path' => 'images/tours/central-asia-odyssey/khiva-itchan-kala-sunset.webp', 'alt' => 'Khiva Itchan Kala walled city golden hour Uzbekistan UNESCO'],
            ]),

            // TOUR CONTENT
            'highlights' => [
                'Almaty\'s cultural blend - Ascension Cathedral, Panfilov Park, and vibrant Zeleny Bazaar',
                'Bishkek\'s Ala-Too Square and State History Museum - Kyrgyz cultural identity hub',
                'Osh city tour with Sulaiman Mountain - Kyrgyzstan\'s spiritual center and oldest Silk Road city',
                'Fergana Valley artisan immersion - Yodgorlik silk factory and Rishtan ceramic workshops',
                'Kokand Khan\'s Palace - Opulent 19th-century Khanate capital with stunning tilework',
                'Khujand Fortress and Tajikistan crossing - Experience Sughd province Silk Road heritage',
                'Iskanderkul Lake mountain excursion - Turquoise alpine lake in Fann Mountains (2,195m altitude)',
                'Ancient Penjikent & Sarazm UNESCO site - 5,500-year-old proto-urban settlement',
                'Samarkand double-day intensive - Registan, Shah-i-Zinda, Ulugbek Observatory, Bibi-Khanym',
                'Bukhara full-day immersion - Ark Fortress, Poi-Kalyan, Lyab-i-Hauz, Trading Domes',
                'Khiva Itchan Kala complete exploration - Medieval walled city with 50+ monuments',
                'Afrosiyob high-speed trains - Modern comfort connecting ancient cities at 210 km/h',
                'Tashkent finale - Soviet-era metro art, Hazrati Imam complex, Independence Square',
            ],

            'included_items' => [
                '13 nights accommodation (3-4 star hotels in all cities, boutique options in Uzbekistan)',
                'All breakfasts at hotels (13 breakfasts)',
                '10 lunches at local restaurants and traditional chaikhanas',
                '3 dinners (welcome dinner Almaty, farewell dinner Tashkent, 1 en route)',
                'All domestic flights (Bishkek-Osh, Urgench-Tashkent)',
                'High-speed Afrosiyob train tickets (Samarkand-Bukhara, Bukhara-Urgench/Khiva)',
                'All intercity transfers and border crossings in air-conditioned vehicles',
                'English-speaking guides in each country (local experts)',
                'All entrance fees to monuments, museums, fortresses, and archaeological sites',
                'Yodgorlik Silk Factory guided tour with workshop demonstration',
                'Rishtan ceramic workshop visit with master artisan',
                'Iskanderkul Lake excursion with waterfall hike',
                'Border crossing assistance and documentation support',
                'Porter service at train stations and hotels',
                'Bottled water during all transfers and activities',
            ],

            'excluded_items' => [
                'International flights to Almaty and from Tashkent',
                '4 lunches and 10 dinners (flexibility for personal dining preferences)',
                'Visa fees for Kazakhstan, Kyrgyzstan, Tajikistan, Uzbekistan (requirements vary by nationality)',
                'Travel and medical insurance (mandatory for this tour)',
                'Personal expenses and souvenirs',
                'Tips for guides and drivers (suggested: $10-12/day total)',
                'Alcoholic beverages',
                'Optional activities (horseback riding in Kyrgyzstan, additional museum visits)',
                'Camera/video fees at some monuments (typically $1-3)',
                'Single room supplement (available upon request)',
            ],

            'languages' => ['English', 'Russian', 'German', 'French'],

            'requirements' => [
                ['icon' => 'passport', 'title' => 'Visas & Documentation', 'text' => 'Passport valid 6 months beyond travel. Visa requirements vary by nationality: Many countries get visa-free entry to all 4 nations, but check current requirements. We provide invitation letters for Tajikistan visa if needed.'],
                ['icon' => 'walking', 'title' => 'Moderate Physical Fitness', 'text' => 'Tour involves 3-6 hours daily walking, some at altitude (Iskanderkul 2,195m). Border crossings require carrying luggage short distances. Comfortable walking shoes essential. Several long driving days (6-8 hours).'],
                ['icon' => 'car', 'title' => 'Long Driving Days', 'text' => 'Days 2, 5, 6, 7 involve 4-8 hour drives through mountains and valleys. Roads are generally good but can be winding. Stops for photos, meals, and rest breaks included. Motion sickness medication recommended.'],
                ['icon' => 'border', 'title' => 'Border Crossings', 'text' => '3 border crossings (KZ-KG, KG-UZ, TJ-UZ). Can take 30min-2 hours depending on queue. Keep passport accessible. Our guides handle all procedures. Patience required during immigration/customs.'],
                ['icon' => 'tshirt', 'title' => 'Varied Climate Zones', 'text' => 'Pack layers: Almaty/Bishkek cool (10-20°C), Iskanderkul cold (5-15°C), Uzbek cities hot (25-40°C summer). Modest dress for mosques (shoulders/knees covered). Mountains require jacket even in summer.'],
                ['icon' => 'medical', 'title' => 'Health & Altitude', 'text' => 'No vaccinations required but Hepatitis A/B recommended. Iskanderkul Lake at 2,195m - some may feel mild altitude effects. Bring personal medications and basic first aid. Travel insurance with medical evacuation essential.'],
            ],

            'include_global_requirements' => false,
            'include_global_faqs' => true,

            // MEETING POINT
            'meeting_point_address' => 'Almaty International Airport (ALA) - Arrivals Hall, Kazakhstan',
            'meeting_instructions' => 'Tour begins in Almaty, Kazakhstan. Our guide will meet you at airport arrivals with "Central Asia Odyssey" sign. Day 1 accommodates all arrival times with individual transfers. Please arrive by Day 1 to begin tour. Detailed pre-departure information packet sent 2 weeks before tour.',
            'meeting_lat' => 43.3521,
            'meeting_lng' => 77.0404,

            // BOOKING SETTINGS
            'min_booking_hours' => 2160, // 90 days (multi-country visa coordination)
            'has_hotel_pickup' => true,
            'pickup_radius_km' => 30,
            'cancellation_hours' => 2160, // 90 days

            // RATINGS
            'rating' => 4.88,
            'review_count' => 34
        ]);

        // ATTACH CATEGORIES
        $tour->categories()->attach([1, 2, 3, 4, 6]); // Cultural, Mountain, Family, Desert, Food & Craft

        // CREATE ITINERARY - 14 days
        $itinerary = [
            [
                'title' => 'Day 1: Arrival Almaty, Kazakhstan - Where Mountains Meet Modernity',
                'description' => '<h4>Welcome to Central Asia</h4>
<p><strong>Arrival Almaty:</strong> Individual meet-and-greet at Almaty International Airport regardless of arrival time. Transfer to centrally located hotel.</p>

<h4>Afternoon City Tour (for morning/midday arrivals):</h4>

<p><strong>Ascension (Zenkov) Cathedral (45 min):</strong> Begin in Panfilov Park with this extraordinary wooden cathedral built in 1907 without a single nail. Its kaleidoscopic color scheme and golden domes survived the devastating 1911 earthquake. One of the world\'s tallest wooden buildings.</p>

<p><strong>Panfilov Park Memorial (30 min):</strong> Pay respects at the monument honoring the 28 Panfilov Guardsmen who defended Moscow against Nazi invasion in 1941. This 18-hectare park with century-old oaks and maples is Almaty\'s green heart.</p>

<p><strong>Central Mosque (45 min):</strong> Visit one of Kazakhstan\'s largest mosques (capacity 7,000). The gold domes set against the backdrop of snow-capped Zailiysky Alatau mountains create a stunning view. Experience the peaceful courtyard.</p>

<p><strong>Zeleny Bazaar (60 min):</strong> Immerse yourself in Kazakhstan\'s culinary culture. Navigate stalls selling horse meat (kazy, zhaya), dried fruits, nuts, honey, and Kurt (dried yogurt balls). Sample local delicacies with your guide\'s help.</p>

<p><strong>19:00 - Welcome Dinner (Included):</strong> Traditional Kazakh restaurant. Meet fellow travelers. Try beshbarmak (Kazakhstan\'s national dish), samsa, and kumis (fermented mare\'s milk) if adventurous!</p>

<p><strong>Overnight:</strong> Almaty 3-star hotel</p>

<p><em>Meals: Dinner included. Breakfast and lunch on your own if arriving early.</em></p>',
                'type' => 'day',
                'default_start_time' => '00:00',
                'duration_minutes' => 1440,
                'sort_order' => 1
            ],

            [
                'title' => 'Day 2: Almaty to Bishkek, Kyrgyzstan - Border Crossing & Kyrgyz Capital',
                'description' => '<h4>Morning: Cross into Kyrgyzstan</h4>
<p><strong>08:00 - Breakfast & Hotel Checkout</strong></p>

<p><strong>09:00 - Drive Almaty-Bishkek (250km, 4-5 hours with border):</strong> Scenic drive through the Chui Valley. Stop at roadside markets selling apples, honey, and local snacks.</p>

<p><strong>Korday Border Crossing (1-2 hours):</strong> Exit Kazakhstan, enter Kyrgyzstan. Our guides handle procedures. Keep passport and customs forms ready.</p>

<p><strong>13:00 - Arrive Bishkek, Lunch (Included):</strong> Traditional Kyrgyz restaurant serving plov, lagman, and manty.</p>

<h4>Afternoon: Discover Bishkek</h4>
<p><strong>14:30 - Ala-Too Square (60 min):</strong> The political and cultural heart of Kyrgyzstan. See the White House (government building), flagpole with national flag, statue of Manas (epic hero), and monument to revolution victims. Learn about Kyrgyzstan\'s transition from Soviet republic to independent nation.</p>

<p><strong>State History Museum (75 min):</strong> Comprehensive exhibits on Kyrgyz nomadic heritage, Soviet era, and independence. Sculptures and artifacts from antiquity to 20th century. One of Central Asia\'s most important museums.</p>

<p><strong>Victory Square (30 min):</strong> Created in 1985 for WWII 40th anniversary. Eternal flame and memorial to fallen soldiers.</p>

<p><strong>Osh Bazaar (75 min):</strong> Bishkek\'s most vibrant market. Shop for ak kalpak (traditional white felt hats), colorful textiles, spices, dried fruits. Experience authentic Kyrgyz market culture.</p>

<p><strong>18:30 - Free Evening:</strong> Dinner on your own. Recommendations provided.</p>

<p><strong>Overnight:</strong> Bishkek 3-star hotel</p>

<p><em>Meals: Breakfast and lunch included.</em></p>',
                'type' => 'day',
                'default_start_time' => '08:00',
                'duration_minutes' => 1440,
                'sort_order' => 2
            ],

            [
                'title' => 'Day 3: Flight to Osh & Cross to Fergana Valley, Uzbekistan',
                'description' => '<h4>Morning: Fly South to Osh</h4>
<p><strong>06:00 - Early Breakfast & Hotel Checkout</strong></p>

<p><strong>08:00 - Flight Bishkek-Osh (1 hour):</strong> Soar over the dramatic Tian Shan mountains. Osh lies in the Fergana Valley, Kyrgyzstan\'s southern region.</p>

<p><strong>09:30 - Osh City Tour (2.5 hours):</strong></p>

<p><strong>Sulaiman Mountain (UNESCO):</strong> The sacred 5-peaked mountain at Osh\'s heart has been a pilgrimage site for 3,000 years. Muslims come to pray at the mosque atop the hill. Cave shrines and petroglyphs dot the slopes. Panoramic city views.</p>

<p><strong>Russian Orthodox Church (1904-1910):</strong> The only Russian church in Osh. Closed during Soviet times for storage, reopened 1995. Active parish today showcasing religious plurality.</p>

<p><strong>Lenin Monument:</strong> One of the few remaining Lenin statues in Central Asia—Soviet nostalgia preserved.</p>

<p><strong>Jayma Bazaar:</strong> Central Asia\'s largest market. Stroll through produce, spices, textiles, and livestock sections.</p>

<h4>Afternoon: Enter Uzbekistan - Fergana Valley</h4>
<p><strong>12:30 - Border Crossing to Uzbekistan (2-3 hours with lunch):</strong> Exit Kyrgyzstan at Dostyk, enter Uzbekistan. Change guides—say goodbye to your Kyrgyz guide, hello to Uzbek guide.</p>

<p><strong>16:00 - Arrive Fergana City:</strong> Check into hotel. Rest after border crossing.</p>

<p><strong>Evening Free:</strong> Explore Fergana\'s Al-Fergani Park independently.</p>

<p><strong>Overnight:</strong> Fergana 3-star hotel</p>

<p><em>Meals: Breakfast included. Lunch at border area (own expense).</em></p>',
                'type' => 'day',
                'default_start_time' => '06:00',
                'duration_minutes' => 1440,
                'sort_order' => 3
            ],

            [
                'title' => 'Day 4: Fergana Valley Crafts - Margilan Silk & Rishtan Ceramics',
                'description' => '<h4>Full Day Artisan Immersion</h4>
<p><strong>08:00 - Breakfast & Early Departure</strong></p>

<p><strong>08:30 - Margilan Yodgorlik Silk Factory (2.5 hours):</strong></p>

<p>Visit Central Asia\'s premier traditional silk workshop. Witness the complete process:</p>
<ul>
    <li>Silkworm cultivation - feeding on mulberry leaves</li>
    <li>Cocoon harvesting and thread extraction</li>
    <li>Natural dyeing with pomegranate skins, walnut, indigo</li>
    <li><strong>Ikat (abr) demonstration:</strong> The complex resist-dyeing technique before weaving</li>
    <li>Hand-loom weaving creating intricate patterns</li>
    <li>Difference between atlas (warp ikat) and adras (silk-cotton)</li>
</ul>

<p>Margilan has produced silk for 1,000 years. Understand why this Silk Road tradition endures.</p>

<p><strong>11:30 - Kumtepa Bazaar (45 min):</strong> Famous for tandoor-baked bread fresh from earth ovens. Sample different varieties. <em>Note: Closed Mondays.</em></p>

<p><strong>12:30 - Lunch (Included):</strong> Fergana-style cuisine.</p>

<p><strong>14:00 - Drive to Rishtan (60km, 1 hour)</strong></p>

<p><strong>15:00 - Rishtan Ceramic Workshop (2 hours):</strong></p>

<p>Rishtan has been Central Asia\'s ceramic capital for 900 years. Visit a master ceramicist\'s workshop:</p>
<ul>
    <li>Local red clay processing</li>
    <li>Potter\'s wheel demonstration</li>
    <li><strong>Ishkor glaze chemistry:</strong> The secret alkaline glaze from desert plant ash creating Rishtan\'s luminous blue</li>
    <li>Hand-painting with mineral pigments</li>
    <li>Traditional kiln tour</li>
    <li>Opportunity to purchase authentic pieces directly from artisan</li>
</ul>

<p><strong>17:30 - Drive to Kokand (1 hour)</strong></p>

<p><strong>18:30 - Arrive Kokand, Check-in</strong></p>

<p><strong>Overnight:</strong> Kokand 3-star hotel</p>

<p><em>Meals: Breakfast and lunch included.</em></p>',
                'type' => 'day',
                'default_start_time' => '08:00',
                'duration_minutes' => 1440,
                'sort_order' => 4
            ],

            [
                'title' => 'Day 5: Kokand Sightseeing & Drive to Khujand, Tajikistan',
                'description' => '<h4>Morning: Kokand - Former Khanate Capital</h4>
<p><strong>09:00 - Kokand Historical Tour (3 hours):</strong></p>

<p>Kokand was capital of the powerful Kokand Khanate (1709-1876) controlling the Fergana Valley.</p>

<p><strong>Khudayar Khan Palace (90 min):</strong> Built 1871, this opulent palace once covered 4 acres with 7 courtyards and 119 rooms. The surviving portion features spectacular Fergana-style tilework—more colorful than Samarkand or Bukhara. Half the building housed the Khan\'s harem. Now a museum of royal life.</p>

<p><strong>Jami Mosque (45 min):</strong> Built 1812 by Umar Khan. Centered on 22m minaret, the mosque features a 100m portico supported by 98 red-wood columns imported from India. Houses collection of suzani textiles and regional ceramics. Workshop on-site makes delicious pistachio halva (sample included!).</p>

<p><strong>Narbutabey Madrasah (45 min):</strong> Dating from 1799, this madrasah was closed by Bolsheviks, reopened post-independence, then closed again 2008. Visit the functioning mosque with original ceiling and the non-working madrasah building.</p>

<h4>Afternoon: Cross into Tajikistan</h4>
<p><strong>12:30 - Lunch (Included)</strong></p>

<p><strong>14:00 - Drive to Khujand (160km, 4-5 hours with border):</strong> Cross Uzbek-Tajik border. Scenic drive through Fergana Valley landscapes. Change to Tajik guide.</p>

<p><strong>19:00 - Arrive Khujand:</strong> Tajikistan\'s second-largest city in Sughd province. Check into hotel.</p>

<p><strong>Evening Free:</strong> Rest after long day.</p>

<p><strong>Overnight:</strong> Khujand 3-star hotel</p>

<p><em>Meals: Breakfast and lunch included.</em></p>',
                'type' => 'day',
                'default_start_time' => '09:00',
                'duration_minutes' => 1440,
                'sort_order' => 5
            ],

            [
                'title' => 'Day 6: Khujand Tour & Iskanderkul Lake Mountain Excursion',
                'description' => '<h4>Morning: Khujand - Ancient Silk Road City</h4>
<p><strong>08:30 - Khujand Exploration (3 hours):</strong></p>

<p>Khujand dates back 2,500 years as a major Silk Road stop in the fertile Fergana Valley.</p>

<p><strong>Khujand Fortress (75 min):</strong> Now a history museum, the fort houses exhibits narrating Tajikistan\'s development from early settlers to present. Walk over glass floors protecting ancient archaeological findings in their original place. Literally walk over history!</p>

<p><strong>Historical Museum of Archeology and Fortification (60 min):</strong> Local and national artifacts spanning centuries. Comprehensive introduction to Tajik culture.</p>

<p><strong>Panjshanbe Bazaar (45 min):</strong> Immerse yourself in local life at this vibrant market. Shop for dried fruits, spices, traditional crafts.</p>

<h4>Afternoon: Journey to Fann Mountains</h4>
<p><strong>12:00 - Lunch (Included)</strong></p>

<p><strong>13:30 - Drive to Iskanderkul Lake (120km, 3 hours):</strong> Climb through dramatic mountain scenery into the Fann Mountains.</p>

<p><strong>16:30 - Iskanderkul Lake (2 hours):</strong></p>

<p>At 2,195m altitude, this turquoise alpine lake is one of Tajikistan\'s most beautiful destinations. "Iskander" is the Persian name for Alexander the Great—legend says he passed through on his conquest.</p>

<p><strong>Activities:</strong></p>
<ul>
    <li>Lakeside walk with mountain reflections photography</li>
    <li>Short hike to the spectacular waterfall with viewing platform built over the cascade</li>
    <li>Bird watching and alpine scenery</li>
    <li>Tea at lakeside guesthouse</li>
</ul>

<p><strong>18:30 - Return Drive to Khujand (3 hours)</strong></p>

<p><strong>21:30 - Arrive Khujand</strong></p>

<p><strong>Overnight:</strong> Khujand 3-star hotel</p>

<p><em>Note: Long but incredibly rewarding day. Pack jacket and camera. Altitude may affect some travelers.</em></p>

<p><em>Meals: Breakfast and lunch included.</em></p>',
                'type' => 'day',
                'default_start_time' => '08:30',
                'duration_minutes' => 1440,
                'sort_order' => 6
            ],

            [
                'title' => 'Day 7: Penjikent Ancient Sogdian Ruins & Cross to Samarkand',
                'description' => '<h4>Morning: Mountain Pass to Penjikent</h4>
<p><strong>07:00 - Early Breakfast & Checkout</strong></p>

<p><strong>08:00 - Drive over Shahristan Pass (200km, 4-5 hours):</strong> Cross the 3,300m Shahristan Pass with breathtaking views of snow-capped peaks. One of the most scenic drives in Central Asia. Photo stops at viewpoints.</p>

<p><strong>12:30 - Arrive Penjikent, Lunch (Included)</strong></p>

<p><strong>14:00 - Ancient Penjikent & Museums (2.5 hours):</strong></p>

<p><strong>Rudaki Museum (60 min):</strong> Exhibits on Sogdian civilization, excavated artifacts from nearby archaeological sites, and the life of poet Rudaki (father of Persian poetry). Local customs and traditions displays.</p>

<p><strong>Ancient Penjikent Site (60 min):</strong> Walk through the ruins of this 5th-8th century Sogdian city. One of the oldest continuously inhabited sites in Central Asia. See residential quarters, palace, temples. Understand pre-Islamic Central Asian culture.</p>

<p><strong>Sarazm UNESCO Site (30 min, time permitting):</strong> If time allows, visit this 5,500-year-old proto-urban settlement (4th-3rd millennium BCE). Demonstrates early human settlement development in Central Asia. Evidence of trade with regions as far as the Indus Valley and Indian Ocean.</p>

<h4>Afternoon: Enter Uzbekistan</h4>
<p><strong>16:30 - Tajik-Uzbek Border Crossing at Jartepa (70km, 2 hours):</strong> Exit Tajikistan, enter Uzbekistan. Return to Uzbek guide.</p>

<p><strong>18:30 - Arrive Samarkand:</strong> The legendary Silk Road city. Check into centrally located hotel.</p>

<p><strong>19:30 - Evening Walk to Registan Square:</strong> First impressions of Samarkand\'s crown jewel under evening lights (optional).</p>

<p><strong>Overnight:</strong> Samarkand 3-4 star boutique hotel</p>

<p><em>Meals: Breakfast and lunch included.</em></p>',
                'type' => 'day',
                'default_start_time' => '07:00',
                'duration_minutes' => 1440,
                'sort_order' => 7
            ],

            [
                'title' => 'Day 8: Full Day Samarkand - Timurid Architecture Masterpieces',
                'description' => '<h4>The Heart of the Silk Road</h4>
<p>Samarkand - over 2,750 years old - was the capital of Timur\'s empire and birthplace of the Timurid Renaissance.</p>

<p><strong>09:00 - Registan Square (90 min):</strong> The iconic ensemble of three madrasahs (Islamic schools) surrounding the grand plaza. Study the evolution of architecture: Ulugbek Madrasah (1420) with astronomical precision, Sher-Dor (1636) with controversial tiger motifs, Tilya-Kori (1660) with gilded interior mosque.</p>

<p><strong>10:45 - Gur-e-Amir Mausoleum (60 min):</strong> Timur\'s final resting place featuring the stunning turquoise ribbed dome. See the world\'s largest jade tombstone. Hear the legend of the curse that fell upon Soviet archaeologists who opened the tomb in 1941.</p>

<p><strong>12:00 - Bibi-Khanym Mosque (60 min):</strong> Once the Islamic world\'s largest mosque, built 1399-1404 after Timur\'s Delhi campaign. The 165-foot minarets and 400 cupolas demonstrate Timur\'s ambition. Learn the romantic (and scandalous) legend of the architect\'s kiss.</p>

<p><strong>13:00 - Siab Bazaar & Lunch (90 min):</strong> Vibrant market perfect for sampling dried fruits, nuts, spices, and fresh tandoor bread. Lunch at local restaurant (included).</p>

<p><strong>14:45 - Shah-i-Zinda Necropolis (90 min):</strong> The artistic pinnacle of Central Asia. Avenue of 11 mausoleums featuring the world\'s finest Islamic tilework spanning 8 centuries. Climb the 44 sacred steps. Each building competes in beauty with complex majolica, geometric patterns, and azure blues.</p>

<p><strong>16:30 - Ulugbek Observatory (60 min):</strong> Ruins of the 15th-century observatory where Timur\'s grandson calculated the solar year to within 1 minute accuracy. See the massive marble sextant. Learn about medieval Islamic astronomy.</p>

<p><strong>17:45 - Afrosiyab Museum (45 min):</strong> View stunning 7th-century Sogdian murals showing pre-Islamic Silk Road cultural exchanges.</p>

<p><strong>Evening Free:</strong> Dinner on your own.</p>

<p><strong>Overnight:</strong> Samarkand boutique hotel</p>

<p><em>Meals: Breakfast and lunch included.</em></p>',
                'type' => 'day',
                'default_start_time' => '09:00',
                'duration_minutes' => 1440,
                'sort_order' => 8
            ],

            [
                'title' => 'Day 9: Samarkand Morning & Train to Bukhara',
                'description' => '<h4>Morning: Final Samarkand Experiences</h4>
<p><strong>09:00 - Konigil Paper Mill Village (90 min):</strong> Visit the last traditional Samarkandi paper workshop. For 1,000 years, this region produced the finest paper in the Islamic world. Watch mulberry bark transformed into paper using ancient techniques. Understand how Samarkand paper preserved countless manuscripts.</p>

<p><strong>10:45 - Free Time:</strong> Last-minute shopping, revisit favorite sites, or relax at hotel.</p>

<p><strong>12:30 - Lunch (Own Expense)</strong></p>

<h4>Afternoon: High-Speed Train to Bukhara</h4>
<p><strong>14:30 - Board Afrosiyob Train to Bukhara (90 min):</strong> Travel at 210 km/h in comfortable reclining seats. The 280km journey that once took 6+ hours by car now takes under 2 hours.</p>

<p><strong>16:00 - Arrive Bukhara:</strong> Transfer to boutique hotel in historic center (many are converted madrasahs or merchant houses). Check-in.</p>

<p><strong>17:00 - Orientation Walk (2 hours):</strong></p>

<p>Unlike museum-like Khiva, Bukhara (2,500 years old) is a living city where people still worship in ancient mosques and trade in medieval bazaars.</p>

<p><strong>Lyab-i-Hauz Complex:</strong> The atmospheric plaza around the ancient pool, surrounded by madrasahs and chaikhanas. Your evening gathering spot.</p>

<p><strong>Trading Domes:</strong> Medieval covered bazaars (Toki Sarrofon, Toki Telpak Furushon) still bustling with craftspeople and traders.</p>

<p><strong>Magoki Attori Mosque:</strong> Built atop a Zoroastrian fire temple and Buddhist monastery—layers of religious history.</p>

<p><strong>19:00 - Free Evening:</strong> Dinner at rooftop restaurant overlooking illuminated monuments (recommendations provided).</p>

<p><strong>Overnight:</strong> Bukhara boutique hotel</p>

<p><em>Meals: Breakfast included.</em></p>',
                'type' => 'day',
                'default_start_time' => '09:00',
                'duration_minutes' => 1440,
                'sort_order' => 9
            ],

            [
                'title' => 'Day 10: Full Day Bukhara - The Noble City',
                'description' => '<h4>Comprehensive Bukhara Immersion</h4>
<p>Bukhara has over 140 architectural monuments from 9th-17th centuries.</p>

<p><strong>09:00 - Ark Fortress (90 min):</strong> The massive citadel serving as Bukhara\'s royal residence for over 1,000 years. Walk the ramparts, visit throne room, see the prison pit. Tales of the last Emir and the Great Game between Russian and British empires.</p>

<p><strong>10:45 - Bolo Hauz Mosque (45 min):</strong> The Emir\'s personal mosque with 20 intricately carved wooden columns reflected in the hauz (pool).</p>

<p><strong>11:45 - Poi-Kalyan Complex (90 min):</strong></p>
<ul>
    <li><strong>Kalyan Minaret (1127):</strong> The 46m "Tower of Death" featuring 14 decorative brick pattern bands. So magnificent it even awed Genghis Khan—he spared it during his destruction of Bukhara.</li>
    <li><strong>Kalyan Mosque:</strong> One of Central Asia\'s largest, accommodating 12,000 worshippers</li>
    <li><strong>Mir-i-Arab Madrasah:</strong> Still-functioning Islamic school (exterior viewing). Twin turquoise domes.</li>
</ul>

<p><strong>13:15 - Lunch (Included)</strong></p>

<p><strong>14:30 - Ismail Samani Mausoleum (45 min):</strong> The 10th-century architectural masterpiece that survived Mongol destruction. Revolutionary brickwork patterns creating optical effects from every angle.</p>

<p><strong>15:30 - Chor Minor (30 min):</strong> The quirky "Four Minarets" building—one of Bukhara\'s most photogenic spots.</p>

<p><strong>16:15 - Ulugbek & Abdulaziz Khan Madrasahs (60 min):</strong> Contrasting architectural styles from different eras facing each other.</p>

<p><strong>17:30 - Free Time:</strong> Shopping in trading domes, traditional teahouse, or hotel rest.</p>

<p><strong>Evening Free:</strong> Dinner on your own.</p>

<p><strong>Overnight:</strong> Bukhara boutique hotel</p>

<p><em>Meals: Breakfast and lunch included.</em></p>',
                'type' => 'day',
                'default_start_time' => '09:00',
                'duration_minutes' => 1440,
                'sort_order' => 10
            ],

            [
                'title' => 'Day 11: Train to Khiva - The Walled Fairy-Tale City',
                'description' => '<h4>Morning: Rail Journey West</h4>
<p><strong>07:00 - Early Breakfast & Hotel Checkout</strong></p>

<p><strong>08:30 - Board Train to Urgench (6 hours):</strong> Long but scenic journey across the Kyzylkum Desert. Bring book, cards, or simply watch landscapes transform. Onboard bistro available.</p>

<p><strong>14:30 - Arrive Urgench, Transfer to Khiva (35km, 30 min)</strong></p>

<p><strong>15:30 - Arrive Khiva, Hotel Check-in:</strong> Hotel inside or near Itchan Kala walls.</p>

<h4>Afternoon: First Impressions of Itchan Kala</h4>
<p><strong>16:30 - Orientation Walk (2 hours):</strong></p>

<p>Entering Itchan Kala feels like stepping into Scheherazade\'s tales. Surrounded by Kyzylkum Desert, this walled city was a crucial Silk Road caravan stop.</p>

<p><strong>First glimpses:</strong></p>
<ul>
    <li>Walk through ancient gates into the 26-hectare UNESCO site</li>
    <li>Narrow cobbled streets between two-story clay houses</li>
    <li>Over 50 monuments—mosques, mausoleums, minarets, madrasahs</li>
    <li>The turquoise-tiled Kalta Minor minaret dominating the skyline</li>
</ul>

<p><strong>18:30 - Free Evening:</strong> Watch sunset from city walls. Dinner at local chaikhana.</p>

<p><strong>Overnight:</strong> Khiva heritage hotel</p>

<p><em>Meals: Breakfast included.</em></p>',
                'type' => 'day',
                'default_start_time' => '07:00',
                'duration_minutes' => 1440,
                'sort_order' => 11
            ],

            [
                'title' => 'Day 12: Khiva Full Day & Fly to Tashkent',
                'description' => '<h4>Morning: Complete Itchan Kala Exploration</h4>
<p><strong>09:00 - Comprehensive Tour (4 hours):</strong></p>

<p><strong>Kalta Minor Minaret (30 min):</strong> The stubby but spectacularly tiled tower. Learn why construction stopped at 29m (political intrigue—khan was murdered). Unique majolica decoration covering every surface.</p>

<p><strong>Kunya Ark Citadel (60 min):</strong> The fortified palace against western ramparts. Originally 12th century, current layout from 1688. Explore throne room, harem, reception halls. Views from ramparts.</p>

<p><strong>Pakhlavan Makhmud Mausoleum (45 min):</strong> Khiva\'s holiest site dedicated to poet-philosopher-wrestler. The largest dome in the city covered in brilliant blue tiles. Pilgrimage site with spiritual atmosphere.</p>

<p><strong>Juma Mosque (45 min):</strong> The "Friday Mosque" at Itchan Kala\'s heart features 213 wooden columns, some from the 10th century. Each capital uniquely carved—study the variations.</p>

<p><strong>Tash Khauli Palace (60 min):</strong> "Stone Courtyard" built 1830-1838. Example of Khorezm architectural grandeur. Explore harem courtyard with exquisite tilework—each room tells a story through pattern.</p>

<p><strong>Islam Khodja Complex (30 min):</strong> Climb the 56m minaret for 360° panoramic views over the walled city and surrounding desert.</p>

<p><strong>13:00 - Lunch (Included)</strong></p>

<h4>Afternoon: Flight to Tashkent</h4>
<p><strong>14:30 - Transfer to Urgench Airport</strong></p>

<p><strong>16:00 - Flight Urgench-Tashkent (1 hr 15 min)</strong></p>

<p><strong>17:30 - Arrive Tashkent:</strong> Transfer to hotel. Check-in.</p>

<p><strong>Evening Free:</strong> Rest or explore modern Tashkent independently.</p>

<p><strong>Overnight:</strong> Tashkent 3-star hotel</p>

<p><em>Meals: Breakfast and lunch included.</em></p>',
                'type' => 'day',
                'default_start_time' => '09:00',
                'duration_minutes' => 1440,
                'sort_order' => 12
            ],

            [
                'title' => 'Day 13: Tashkent City Tour - Capital & Cultural Hub',
                'description' => '<h4>Full Day Exploring Uzbekistan\'s Capital</h4>
<p>Tashkent (2,200+ years old) blends ancient Silk Road heritage with Soviet-era architecture and modern development.</p>

<p><strong>09:00 - Hazrati Imam Complex (90 min):</strong> The spiritual heart of Tashkent in the old city. Complex includes Tillya Sheikh Mosque, Abu Bakr Kaffal Shashi Mausoleum, Barak Khan Madrasah, and Imam al-Bukhari Islamic Institute. See the world\'s oldest Quran (7th-century Uthman Quran) stained with the caliph\'s blood.</p>

<p><strong>10:45 - Chorsu Bazaar (60 min):</strong> Central Asia\'s largest covered market. Navigate colorful stalls selling spices, dried fruits, fresh produce, bread, and crafts. Sample local snacks with your guide.</p>

<p><strong>12:00 - Tashkent Metro Tour (60 min):</strong> The first underground in Central Asia (opened 1977). Each station is a palace with unique architectural appearance: marble, granite, columns, colorful bas-reliefs, crystal chandeliers, handcrafted gypsum. Visit Alisher Navoi, Kosmonavtlar, and Pakhtakor stations.</p>

<p><strong>13:00 - Lunch (Included)</strong></p>

<p><strong>14:30 - Amir Timur Square (30 min):</strong> Central to modern city, this lush green space features statue of Tamerlane on horseback, fountains, and flowers.</p>

<p><strong>15:15 - Independence Square (45 min):</strong> Former "Red Square," now beautiful gardens and fountains frequented by local families. Monument to independence and WWII memorial.</p>

<p><strong>16:15 - Alisher Navoiy Theater (30 min, exterior):</strong> Designed by Alexey Shchusev, built 1942-1947. Celebrate 500th anniversary of poet Alisher Navoi. Stunning architecture.</p>

<p><strong>17:00 - Romanov Palace (30 min, exterior):</strong> Built 1891 for exiled Prince Nikolay Romanov. Now MFA Reception House. Beautiful restoration.</p>

<p><strong>18:00 - Free Time:</strong> Last-minute shopping or hotel rest.</p>

<p><strong>19:30 - Farewell Dinner (Included):</strong> Group celebration at upscale restaurant. Share memories, exchange contacts. Toast to completed odyssey!</p>

<p><strong>Overnight:</strong> Tashkent hotel</p>

<p><em>Meals: Breakfast and dinner included.</em></p>',
                'type' => 'day',
                'default_start_time' => '09:00',
                'duration_minutes' => 1440,
                'sort_order' => 13
            ],

            [
                'title' => 'Day 14: Departure from Tashkent - End of Central Asia Odyssey',
                'description' => '<h4>Morning: Tour Conclusion</h4>
<p><strong>Breakfast at Hotel:</strong> Timing depends on flight.</p>

<p><strong>Airport Transfer:</strong> Individual transfers to Tashkent International Airport based on flight times.</p>

<p><strong>Tour Concludes:</strong> Depart with memories of four incredible countries, countless UNESCO sites, mountain passes, border crossings, ancient ruins, artisan workshops, and the warmth of Central Asian hospitality.</p>

<h3>What You\'ve Experienced:</h3>
<ul>
    <li>✅ 4 Countries explored in depth</li>
    <li>✅ 3 UNESCO World Heritage sites (Samarkand, Bukhara, Khiva) plus Sulaiman Mountain</li>
    <li>✅ 3 Border crossings with cultural transitions</li>
    <li>✅ Alpine lakes, mountain passes, desert cities</li>
    <li>✅ 5,500 years of human history from Sarazm to present</li>
    <li>✅ Silk Road heritage from nomadic Kyrgyz to settled Uzbek civilizations</li>
    <li>✅ Artisan traditions: silk weaving, ceramics, paper-making</li>
    <li>✅ Soviet architecture to Islamic masterpieces</li>
    <li>✅ Memories to last a lifetime</li>
</ul>

<p><em>Safe travels! Рахмат (Tajik), Рахмет (Kazakh), Рахмат (Kyrgyz), Rahmat (Uzbek) - Thank you!</em></p>

<p><strong>END OF SERVICES</strong></p>',
                'type' => 'day',
                'default_start_time' => '00:00',
                'duration_minutes' => 1440,
                'sort_order' => 14
            ],
        ];

        foreach ($itinerary as $item) {
            $tour->itineraryItems()->create($item);
        }

        // Update city tour count
        $tourCount = $tashkent->tours()->where('is_active', true)->count();
        $tashkent->tour_count_cache = $tourCount;
        $tashkent->save();

        $this->info("✅ Central Asia 4-Country Odyssey created successfully!");
        $this->info("Tour ID: {$tour->id}");
        $this->info("Title: {$tour->title}");
        $this->info("Countries: Kazakhstan → Kyrgyzstan → Tajikistan → Uzbekistan");
        $this->info("Duration: 14 days / 13 nights");
        $this->info("Price: $2,895 USD per person");
        $this->info("Border crossings: 3 (KZ-KG, KG-UZ, TJ-UZ)");
        $this->info("URL: http://127.0.0.1:8000/tours/{$tour->slug}");

        return 0;
    }
}
