<?php

namespace App\Console\Commands;

use App\Models\City;
use App\Models\Tour;
use App\Models\TourCategory;
use App\Models\ItineraryItem;
use Illuminate\Console\Command;

class CreateKazakhstanKyrgyzstanNatureTour extends Command
{
    protected $signature = 'create:kazakhstan-kyrgyzstan-nature-tour';
    protected $description = 'Create Kazakhstan-Kyrgyzstan Nature Explorer: Canyons, Lakes & Mountains';

    public function handle()
    {
        $this->info('Creating Kazakhstan-Kyrgyzstan Nature Explorer Tour...');

        // Create/get cities
        $almaty = City::firstOrCreate(
            ['slug' => 'almaty'],
            [
                'name' => 'Almaty',
                'country' => 'Kazakhstan',
                'region' => 'Almaty Region',
                'latitude' => 43.2220,
                'longitude' => 76.8512,
                'description' => 'Kazakhstan\'s largest city and cultural capital, gateway to Tian Shan mountains',
                'is_popular' => true,
            ]
        );

        $bishkek = City::firstOrCreate(
            ['slug' => 'bishkek'],
            [
                'name' => 'Bishkek',
                'country' => 'Kyrgyzstan',
                'region' => 'Chuy Region',
                'latitude' => 42.8746,
                'longitude' => 74.5698,
                'description' => 'Capital city of Kyrgyzstan',
                'is_popular' => true,
            ]
        );

        // Get categories
        $categories = TourCategory::whereIn('slug', [
            'adventure',
            'nature-wildlife',
            'mountain',
        ])->get();

        // Create tour
        $tour = Tour::create([
            'title' => 'Kazakhstan-Kyrgyzstan Nature Explorer: Canyons, Singing Dunes & Alpine Lakes',
            'slug' => 'kazakhstan-kyrgyzstan-nature-explorer-canyons-lakes',

            'short_description' => 'Cross-border adventure through Kazakhstan and Kyrgyzstan. Explore Charyn Canyon, Altyn Emel\'s Singing Dunes, Issyk-Kul Lake, and Ala-Archa Gorge. 8 days of nature, wildlife, and mountain landscapes.',
            'long_description' => '<h2>Kazakhstan & Kyrgyzstan Nature Explorer</h2>
<p>Discover the raw natural beauty of Central Asia on this 8-day cross-border adventure combining Kazakhstan\'s dramatic canyons and desert landscapes with Kyrgyzstan\'s alpine paradise.</p>

<h3>Why This Tour is Unique</h3>
<ul>
    <li><strong>Charyn Canyon:</strong> Walk through the "Valley of Castles" - Kazakhstan\'s Grand Canyon with 12-million-year-old rock formations rising 300 meters</li>
    <li><strong>Altyn Emel National Park:</strong> Experience the legendary Singing Dunes (150m high dunes that hum in the wind), see rare wildlife, and visit ancient petroglyphs</li>
    <li><strong>Issyk-Kul Lake:</strong> Swim in the world\'s second-largest alpine lake (never freezes, surrounded by 5,000m peaks)</li>
    <li><strong>Jeti-Oguz & Skazka Canyon:</strong> Red rock formations, fairy tale canyons, and pristine mountain valleys</li>
    <li><strong>Ala-Archa National Park:</strong> Trek through Kyrgyzstan\'s premier alpine gorge with waterfalls and glaciers</li>
    <li><strong>Cross-Border Experience:</strong> Seamless journey through two countries with all logistics handled</li>
</ul>

<h3>Perfect For</h3>
<p>Nature lovers, photographers, hikers, and adventure seekers aged 25-65. Moderate fitness required for hiking. Ideal for those who want to experience Central Asia beyond cities - deserts, canyons, mountains, and lakes in one epic journey.</p>',

            'duration_days' => 8,
            'duration_text' => '8 days / 7 nights',
            'tour_type' => 'group_only',
            'city_id' => $almaty->id,

            'price_per_person' => 1495.00,
            'currency' => 'USD',
            'max_guests' => 12,
            'min_guests' => 4,

            'hero_image' => 'images/tours/kz-kg-nature/charyn-canyon-valley-castles.webp',
            'gallery_images' => json_encode([
                ['path' => 'images/tours/kz-kg-nature/altyn-emel-singing-dunes.webp', 'alt' => 'Singing Dunes Altyn Emel National Park Kazakhstan'],
                ['path' => 'images/tours/kz-kg-nature/issyk-kul-mountains.webp', 'alt' => 'Issyk-Kul Lake with Tian Shan mountains Kyrgyzstan'],
                ['path' => 'images/tours/kz-kg-nature/jeti-oguz-red-rocks.webp', 'alt' => 'Jeti-Oguz Seven Bulls red rock formations'],
                ['path' => 'images/tours/kz-kg-nature/skazka-canyon.webp', 'alt' => 'Skazka Fairy Tale Canyon Issyk-Kul'],
                ['path' => 'images/tours/kz-kg-nature/ala-archa-gorge.webp', 'alt' => 'Ala-Archa alpine gorge trekking Kyrgyzstan'],
                ['path' => 'images/tours/kz-kg-nature/big-almaty-lake.webp', 'alt' => 'Big Almaty Lake turquoise mountain lake Kazakhstan'],
            ]),

            'highlights' => [
                'Charyn Canyon "Valley of Castles" - 300m-high rock formations (Kazakhstan\'s Grand Canyon)',
                'Altyn Emel Singing Dunes - 150m sand dunes that produce mysterious humming sounds',
                'Aktau & Katutau Mountains - Multicolored desert mountains and ancient seabed fossils',
                'Tamgaly Tas petroglyphs - 3,000-year-old Bronze Age rock art',
                'Border crossing Kazakhstan-Kyrgyzstan with seamless logistics',
                'Issyk-Kul Lake - Second-largest alpine lake in the world, warm swimming',
                'Jeti-Oguz "Seven Bulls" - Dramatic red sandstone cliffs',
                'Skazka "Fairy Tale" Canyon - Wind-sculpted rock formations in surreal shapes',
                'Ala-Archa Gorge trekking - Alpine waterfalls and glacier views',
                'Big Almaty Lake - Turquoise glacial lake at 2,500m elevation',
                'Wildlife spotting: Przewalski\'s horses, kulans, gazelles, golden eagles',
                'Mix of yurt camps, guesthouses, and hotels for authentic experience',
            ],

            'included_items' => [
                '7 nights accommodation (hotels, guesthouses, 1 night yurt camp)',
                'All breakfasts (7)',
                '5 lunches (picnic style during excursions)',
                '4 dinners',
                'English-speaking guide throughout',
                '4WD vehicles for off-road sections and mountain roads',
                'All entrance fees to national parks and reserves',
                'Border crossing assistance and all necessary permits',
                'Airport transfers in Almaty and Bishkek',
                'Trekking in Ala-Archa National Park',
                'Charyn Canyon entrance and guided tour',
                'Altyn Emel National Park 2-day exploration',
            ],

            'excluded_items' => [
                'International flights to Almaty / from Bishkek',
                'Lunches and dinners not mentioned (approx $10-15 per meal)',
                'Personal travel insurance',
                'Tips for guides and drivers',
                'Alcoholic beverages',
                'Personal expenses and souvenirs',
                'Kazakhstan and Kyrgyzstan visa fees (if applicable - check e-visa requirements)',
            ],

            'requirements' => [
                'Moderate fitness - comfortable hiking 3-5 hours on uneven terrain',
                'Some off-road driving on rough mountain tracks',
                'Comfortable with basic facilities in remote areas (shared bathrooms at yurt camp)',
                'Valid passport with 6 months validity',
                'Most nationalities can enter Kazakhstan and Kyrgyzstan visa-free or via e-visa',
                'Travel insurance covering adventure activities strongly recommended',
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
            'title' => 'Arrival in Almaty - City Tour & Big Almaty Lake',
            'description' => '<p>Welcome to Almaty, Kazakhstan\'s cultural capital nestled at the foot of the Tian Shan mountains!</p>
<p><strong>Airport Pickup</strong> - Meet your guide and transfer to your hotel. Time to freshen up.</p>
<p><strong>Almaty City Highlights (afternoon):</strong></p>
<ul>
<li><strong>Panfilov Park & Zenkov Cathedral</strong> - One of the world\'s tallest wooden buildings (1904), built entirely without nails. Colorful Russian Orthodox architecture.</li>
<li><strong>Green Bazaar</strong> - Bustling central market with mountains of dried fruits, nuts, spices, fresh produce, and traditional Kazakh foods. Try kurt (dried yogurt balls) and kumys.</li>
<li><strong>Republic Square</strong> - Soviet-era monuments and Independence Monument</li>
</ul>
<p><strong>Big Almaty Lake</strong> - Late afternoon drive up winding mountain roads to this stunning turquoise glacial lake at 2,500m elevation. The color shifts from turquoise to deep blue depending on light and season. Surrounded by peaks over 4,000m. Perfect introduction to Kazakhstan\'s mountain landscapes.</p>
<p><strong>Welcome Dinner</strong> - Traditional Kazakh restaurant. Try beshbarmak (national dish of meat and noodles) and samsa pastries.</p>
<p><em>Meals: Dinner</em><br>
<em>Accommodation: Hotel in Almaty</em></p>',
            'duration' => '6 hours',
            'order' => 1,
        ]);

        // Day 2
        ItineraryItem::create([
            'tour_id' => $tour->id,
            'day_number' => 2,
            'type' => 'day',
            'title' => 'Almaty - Charyn Canyon - Zharkent',
            'description' => '<p>Today we journey east toward the Chinese border and one of Kazakhstan\'s natural wonders.</p>
<p><strong>Drive to Charyn Canyon (195 km, ~3 hours)</strong> - Cross the vast Kazakh steppe with distant mountain views.</p>
<p><strong>Charyn Canyon Exploration (3-4 hours)</strong></p>
<ul>
<li>Often called Central Asia\'s Grand Canyon, Charyn stretches 154 km with the Charyn River carving through 12-million-year-old sedimentary rock</li>
<li><strong>Valley of Castles:</strong> Descend into the most spectacular section where erosion has created towers, spires, and "castles" rising 300m high</li>
<li>Trek along the canyon floor - rust-red walls tower above, rock formations resemble medieval fortresses</li>
<li>Visit the Ash Grove - rare ash trees growing near the river, remnants from the Ice Age</li>
<li>Photography opportunities: dramatic shadows, layered geology, bizarre rock shapes</li>
<li>Possible wildlife: argali sheep, foxes, desert monitor lizards</li>
</ul>
<p><strong>Picnic Lunch</strong> - In the canyon with spectacular views.</p>
<p><strong>Drive to Zharkent</strong> - Continue east to this historic Silk Road town near the Chinese border. Visit the stunning Chinese-style Zharkent Mosque (1895) built by Dungan craftsmen.</p>
<p><strong>Overnight in Zharkent</strong> - Simple guesthouse in this border town.</p>
<p><em>Meals: Breakfast, Lunch, Dinner</em><br>
<em>Accommodation: Guesthouse in Zharkent</em><br>
<em>Driving: ~5 hours total</em></p>',
            'duration' => '10 hours',
            'order' => 2,
        ]);

        // Day 3
        ItineraryItem::create([
            'tour_id' => $tour->id,
            'day_number' => 3,
            'type' => 'day',
            'title' => 'Zharkent - Altyn Emel National Park - Singing Dunes',
            'description' => '<p>Enter Altyn Emel National Park - 520,000 hectares of desert, mountains, and rare wildlife.</p>
<p><strong>Morning Drive to Altyn Emel</strong> - Pass through dramatic landscapes: rocky desert, dry river valleys, distant mountain ranges.</p>
<p><strong>Tamgaly Tas Petroglyphs</strong> - Stop at riverside rocks carved with Buddhist inscriptions and images from 14th-16th centuries. Ancient Silk Road travelers left their mark here.</p>
<p><strong>Singing Dunes (Dune Akkum)</strong></p>
<ul>
<li>Arrive at these legendary sand dunes rising 150 meters and stretching 3 km</li>
<li>The dunes "sing" - produce a mysterious humming/organ-like sound when sand cascades down slopes on windy days (natural acoustic phenomenon caused by electrostatic charges)</li>
<li>Climb to the top (strenuous but rewarding - takes 30-40 min)</li>
<li>360° views: Ili River valley, snow-capped Tian Shan peaks, endless desert</li>
<li>Sand surfing down the slopes!</li>
<li>Sunrise/sunset here creates magical light on the golden dunes</li>
</ul>
<p><strong>Wildlife Watch</strong> - The park protects endangered species. You may see kulans (Asian wild ass), goitered gazelles, Przewalski\'s horses (reintroduced), golden eagles.</p>
<p><strong>Overnight</strong> - Simple guesthouse/yurt camp near park headquarters.</p>
<p><em>Meals: Breakfast, Lunch, Dinner</em><br>
<em>Accommodation: Guesthouse/yurt in Altyn Emel</em><br>
<em>Driving: ~4 hours (off-road sections)</em></p>',
            'duration' => '8 hours',
            'order' => 3,
        ]);

        // Day 4
        ItineraryItem::create([
            'tour_id' => $tour->id,
            'day_number' => 4,
            'type' => 'day',
            'title' => 'Altyn Emel - Aktau & Katutau Mountains - Border to Kyrgyzstan - Karakol',
            'description' => '<p>Explore Altyn Emel\'s "moon mountains" before crossing into Kyrgyzstan.</p>
<p><strong>Aktau Mountains</strong> - Drive deep into the park to these surreal multicolored mountains:</p>
<ul>
<li>Layers of white, red, pink, green, and yellow sedimentary rock</li>
<li>Former seabed from 30 million years ago when Tian Shan was underwater</li>
<li>Find fossils embedded in the rocks</li>
<li>Lunar landscape - feels like another planet</li>
<li>Short hikes among the striped hills</li>
</ul>
<p><strong>Katutau Mountains</strong> - Volcanic rock formations nearby creating dramatic contrast with Aktau\'s soft sediments. Ancient lava flows and volcanic cones.</p>
<p><strong>Picnic Lunch</strong> - In this otherworldly desert landscape.</p>
<p><strong>Border Crossing</strong> - Drive to Kazakhstan-Kyrgyzstan border at Kegen. Your guide handles all formalities (usually smooth, 30-60 min).</p>
<p><strong>Welcome to Kyrgyzstan!</strong> - Enter greener, more mountainous terrain. The landscape immediately changes - more water, forests, and dramatic peaks.</p>
<p><strong>Drive to Karakol</strong> - Follow the southern shore of Issyk-Kul Lake. Arrive in Karakol, the adventure capital of Kyrgyzstan.</p>
<p><em>Meals: Breakfast, Lunch</em><br>
<em>Accommodation: Guesthouse in Karakol</em><br>
<em>Driving: ~6 hours including border</em></p>',
            'duration' => '9 hours',
            'order' => 4,
        ]);

        // Day 5
        ItineraryItem::create([
            'tour_id' => $tour->id,
            'day_number' => 5,
            'type' => 'day',
            'title' => 'Karakol - Jeti-Oguz - Skazka Canyon - Issyk-Kul Lake',
            'description' => '<p>Explore the stunning southern shore of Issyk-Kul - red rock gorges and fairy tale canyons.</p>
<p><strong>Jeti-Oguz ("Seven Bulls") Valley</strong></p>
<ul>
<li>Dramatic red sandstone cliffs rising from green valleys</li>
<li>Legend: Seven bulls turned to stone, or a broken-hearted princess</li>
<li>Hike to "Broken Heart Rock" viewpoint (1 hour round trip, moderate)</li>
<li>Walk deeper into the valley - hot springs, rushing rivers, Tian Shan fir forests</li>
</ul>
<p><strong>Kok-Jaiyk Gorge</strong> - Side valley with pristine alpine scenery, wildflower meadows (summer), and waterfalls.</p>
<p><strong>Skazka "Fairy Tale" Canyon</strong></p>
<ul>
<li>Wind and rain have sculpted bizarre rock formations: castles, animals, faces, towers</li>
<li>Layers of red, orange, and yellow clay create a painted desert effect</li>
<li>Walk among the formations - each angle reveals new shapes</li>
<li>Sunset here is magical (golden light on red rocks)</li>
</ul>
<p><strong>Issyk-Kul Lake Swimming</strong> - The lake never freezes despite its 1,607m elevation. Water is slightly salty and believed to have healing properties. Beach time!</p>
<p><strong>Overnight</strong> - Lakeside guesthouse with mountain views across the water.</p>
<p><em>Meals: Breakfast, Lunch, Dinner</em><br>
<em>Accommodation: Guesthouse on Issyk-Kul</em><br>
<em>Driving: ~3 hours total</em></p>',
            'duration' => '8 hours',
            'order' => 5,
        ]);

        // Day 6
        ItineraryItem::create([
            'tour_id' => $tour->id,
            'day_number' => 6,
            'type' => 'day',
            'title' => 'Issyk-Kul Lake - Cholpon-Ata - Burana Tower - Bishkek',
            'description' => '<p>Journey along Issyk-Kul\'s northern shore toward the capital, visiting ancient sites.</p>
<p><strong>Scenic Lakeshore Drive</strong> - The road hugs the northern coast with snow-capped peaks reflecting in the blue waters. Mountain panoramas across the lake.</p>
<p><strong>Cholpon-Ata Petroglyphs</strong></p>
<ul>
<li>Open-air museum with over 2,000 rock carvings</li>
<li>Date from Bronze Age to early Middle Ages (3,000-1,000 years old)</li>
<li>See ibex, snow leopards, hunters, shamanic symbols, celestial maps</li>
<li>Sacred site for ancient Scythian and Saka nomads</li>
</ul>
<p><strong>Burana Tower</strong></p>
<ul>
<li>11th-century minaret from ancient Balasagun - medieval Silk Road city</li>
<li>Climb the narrow spiral staircase for valley views</li>
<li>"Stone garden" with balbals - Turkic stone warriors and grave markers</li>
<li>Small museum with artifacts from Sogdian traders</li>
</ul>
<p><strong>Arrival in Bishkek</strong> - Capital city with tree-lined boulevards, Soviet monuments, and lively cafe culture.</p>
<p><strong>Evening City Walk</strong> - Ala-Too Square, Oak Park, local bazaar. Free time to explore or relax.</p>
<p><em>Meals: Breakfast, Lunch</em><br>
<em>Accommodation: Hotel in Bishkek</em><br>
<em>Driving: ~5 hours</em></p>',
            'duration' => '8 hours',
            'order' => 6,
        ]);

        // Day 7
        ItineraryItem::create([
            'tour_id' => $tour->id,
            'day_number' => 7,
            'type' => 'day',
            'title' => 'Bishkek - Ala-Archa National Park Trekking',
            'description' => '<p>Venture into Kyrgyzstan\'s premier alpine national park for mountain trekking.</p>
<p><strong>Drive to Ala-Archa (40 km, 1 hour)</strong> - Leave the city and climb into the Kyrgyz Ala-Too mountains. The gorge opens dramatically with steep granite walls and rushing glacial river.</p>
<p><strong>Ala-Archa Gorge Trek</strong></p>
<ul>
<li><strong>Option 1 (Moderate):</strong> Hike to Ak-Sai Waterfall - 4-5 hours round trip, gain 300m elevation. Follow the river through conifer forest, cross wooden bridges, arrive at cascading waterfall. Glacier views of Uchitel Peak (4,527m).</li>
<li><strong>Option 2 (Easier):</strong> Valley walk to the alpine meadow viewpoint - 2-3 hours, gentle terrain. Perfect for families or those wanting lighter activity.</li>
<li><strong>Option 3 (Advanced):</strong> Hike toward Ratsek Hut - 6-7 hours round trip, 700m gain. For experienced hikers. Get closer to glaciers and high peaks.</li>
</ul>
<p><strong>Landscape:</strong> Tian Shan fir and juniper forests, wildflower meadows, snow-capped peaks rising to 4,500m, glaciers, marmots, possible ibex sightings</p>
<p><strong>Picnic Lunch</strong> - Mountain stream or meadow setting.</p>
<p><strong>Return to Bishkek</strong> - Afternoon arrival. Free evening to enjoy the capital.</p>
<p><strong>Farewell Dinner</strong> - Traditional Kyrgyz restaurant with live music. Try beshbarmak, lagman, shorpo soup, and samsa.</p>
<p><em>Meals: Breakfast, Lunch, Dinner</em><br>
<em>Accommodation: Hotel in Bishkek</em><br>
<em>Trekking: 4-7 hours depending on option</em></p>',
            'duration' => '8 hours',
            'order' => 7,
        ]);

        // Day 8
        ItineraryItem::create([
            'tour_id' => $tour->id,
            'day_number' => 8,
            'type' => 'day',
            'title' => 'Bishkek - Departure or Extension',
            'description' => '<p>Final morning in Bishkek before your onward journey.</p>
<p><strong>Morning Options (depending on flight time):</strong></p>
<ul>
<li><strong>Osh Bazaar:</strong> Bishkek\'s largest market - buy last-minute souvenirs (felt products, honey, nuts, traditional hats)</li>
<li><strong>Erkindik Boulevard:</strong> Stroll the main street, visit local cafes, browse bookshops</li>
<li><strong>Soviet History Museum:</strong> Fascinating exhibits on Kyrgyzstan\'s past</li>
<li>Simply relax at your hotel</li>
</ul>
<p><strong>Airport Transfer</strong> - Manas International Airport (30 min from city center).</p>
<p><strong>Departure</strong> - Say goodbye to Central Asia, carrying memories of canyons, singing dunes, alpine lakes, and mountains that touch the sky.</p>
<p><strong>Optional Extensions:</strong></p>
<ul>
<li>Add 3-4 days to visit Song Kul Lake yurt camps</li>
<li>Extend to Tajikistan for Pamir Highway adventure</li>
<li>Fly to Uzbekistan to explore Samarkand and Bukhara</li>
</ul>
<p><em>Meals: Breakfast</em><br>
<em>Accommodation: Not included (or optional extra night)</em></p>',
            'duration' => 'Half day',
            'order' => 8,
        ]);

        $this->info("✅ Tour created successfully: {$tour->title}");
        $this->info("Tour ID: {$tour->id}");
        $this->info("Slug: {$tour->slug}");

        return Command::SUCCESS;
    }
}
