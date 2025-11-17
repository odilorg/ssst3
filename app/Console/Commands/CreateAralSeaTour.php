<?php

namespace App\Console\Commands;

use App\Models\City;
use App\Models\Tour;
use App\Models\TourCategory;
use Illuminate\Console\Command;

class CreateAralSeaTour extends Command
{
    protected $signature = 'create:aral-sea-tour';
    protected $description = 'Create 2-day Aral Sea environmental disaster tour with yurt stay';

    public function handle()
    {
        $this->info('Creating Aral Sea Tour...');

        // Get Nukus city
        $nukus = City::where('slug', 'nukus')->first();
        if (!$nukus) {
            $this->error('Nukus city not found! Creating it...');
            $nukus = City::create([
                'name' => 'Nukus',
                'slug' => 'nukus',
                'region' => 'Karakalpakstan',
                'latitude' => 42.4532,
                'longitude' => 59.6105,
                'description' => 'Capital of the autonomous Republic of Karakalpakstan, home to the renowned Savitsky Museum',
                'is_active' => true,
                'tour_count_cache' => 0,
            ]);
        }

        $tour = Tour::create([
            // BASIC INFORMATION
            'title' => 'Forgotten Shores: 2-Day Aral Sea Ecological Journey',
            'slug' => 'forgotten-shores-2-day-aral-sea-journey',
            'short_description' => 'Witness one of the planet\'s worst environmental disasters—the disappearing Aral Sea, surreal ship graveyard, and Ustyurt Plateau canyons with authentic yurt camping',
            'long_description' => '<h2>Journey to the Edge of Catastrophe</h2>
<p>This is not a typical tour—it\'s a pilgrimage to one of humanity\'s greatest environmental mistakes. The Aral Sea, once the world\'s fourth-largest lake spanning 68,000 km², has shrunk to just 10% of its original size since the 1960s. What remains is the haunting Aralkum Desert, rusted fishing vessels marooned 185 km from water, and communities struggling to adapt to a vanished sea.</p>

<p>This powerful 2-day journey combines environmental education with adventure, taking you across the dried seabed to reach the remaining waters, through otherworldly Ustyurt Plateau canyons, and into the nomadic lifestyle with an overnight yurt stay under infinite desert stars.</p>

<h3>Why This Tour Matters</h3>
<ul>
    <li><strong>Environmental Witness:</strong> See firsthand the consequences of mismanaged water resources—a cautionary tale of Soviet-era cotton irrigation that drained two rivers</li>
    <li><strong>Muynak Ship Cemetery:</strong> Walk among the skeletal remains of a once-thriving fishing fleet serving 40,000 workers—now rusting monuments 185 km from water</li>
    <li><strong>Ustyurt Plateau:</strong> Explore 100-meter limestone cliffs and multicolored canyons formed 20-60 million years ago beneath the ancient Tethys Ocean</li>
    <li><strong>Authentic Nomadic Experience:</strong> Sleep in traditional yurts at Besqala Camp on the Aral Sea shore, experiencing centuries-old Karakalpak hospitality</li>
    <li><strong>Mizdakhan Necropolis:</strong> Visit one of Central Asia\'s holiest sites—a 2,400-year-old cemetery where legend says Adam is buried</li>
    <li><strong>Cultural Immersion:</strong> Taste fresh shubat (fermented camel milk) with nomadic families, learn about oil/gas extraction on the old seabed</li>
    <li><strong>Photographic Gold:</strong> Capture surreal landscapes—drilling towers dotting the desert, seashells in landlocked dunes, ships frozen in sand</li>
</ul>

<h3>Perfect For</h3>
<p>Photographers, environmentalists, adventure travelers, and anyone seeking to understand climate change and water security through direct experience. This is educational tourism that leaves a lasting impact. Moderate physical fitness required for desert conditions.</p>

<h3>The Sobering Reality</h3>
<p>UN Secretary-General Ban Ki-moon called this "one of the planet\'s worst environmental disasters." By coming here, you witness the fragility of ecosystems and support local communities adapting to environmental collapse. Your visit helps sustain Muynak\'s economy, which lost its fishing industry but found new life in "dark tourism" and oil/gas extraction.</p>',

            // DURATION & TYPE
            'duration_days' => 2,
            'duration_text' => '2 days / 1 night',
            'tour_type' => 'group_only',
            'city_id' => $nukus->id,
            'is_active' => true,

            // PRICING & CAPACITY
            'price_per_person' => 385.00,
            'currency' => 'USD',
            'max_guests' => 8,
            'min_guests' => 2,

            // IMAGES
            'hero_image' => 'images/tours/aral-sea/ship-cemetery-panorama.webp',
            'gallery_images' => json_encode([
                ['path' => 'images/tours/aral-sea/muynak-ship-graveyard.webp', 'alt' => 'Rusted fishing vessels at Muynak ship cemetery Aral Sea'],
                ['path' => 'images/tours/aral-sea/ustyurt-plateau-cliffs.webp', 'alt' => 'Multicolored limestone cliffs Ustyurt Plateau'],
                ['path' => 'images/tours/aral-sea/yurt-camp-sunset.webp', 'alt' => 'Traditional yurts at Besqala Camp Aral Sea sunset'],
                ['path' => 'images/tours/aral-sea/dried-seabed-shells.webp', 'alt' => 'Seashells scattered across dried Aral Sea bottom'],
                ['path' => 'images/tours/aral-sea/mizdakhan-necropolis.webp', 'alt' => 'Ancient Mizdakhan necropolis mausoleums Karakalpakstan'],
                ['path' => 'images/tours/aral-sea/aral-sea-remaining-water.webp', 'alt' => 'Remaining waters of shrunken Aral Sea'],
            ]),

            // TOUR CONTENT
            'highlights' => [
                'Muynak Ship Cemetery - Surreal graveyard of Soviet fishing vessels marooned 185km from water',
                'Muynak Regional Museum - Documentary film & exhibits about the Aral Sea catastrophe',
                'Ustyurt Plateau - 100m limestone cliffs, multicolored canyons, ancient ocean floor geology',
                'Dried Aral Seabed - Drive 200km across former lake bottom dotted with drilling towers',
                'Besqala Yurt Camp - Authentic overnight in traditional nomadic dwellings',
                'Aral Sea Swimming - Touch/swim in the remaining 10% of what was once the world\'s 4th largest lake',
                'Kurgancha Kala Fortress - Hidden 13th-century caravanserai among Ustyurt hills',
                'Kubla Ustyurt Village - Taste organic shubat (fermented camel milk) with nomadic families',
                'Lake Sudochie Bird Sanctuary - Observe 250+ species at this shrinking wetland refuge',
                'Ancient Lighthouse Hill - Where fires once guided both ships and desert caravans',
                'Mizdakhan Necropolis - 2,400-year-old sacred cemetery with legend of Adam\'s burial site',
                'Stalin-Era Repression Sites - Abandoned settlement ruins of deported Russian/Polish communities',
            ],

            'included_items' => [
                '1 night accommodation in traditional yurt at Besqala Camp (shared facilities, authentic experience)',
                'All meals: 1 breakfast, 2 lunches, 1 dinner',
                'Lunch with local family in Kungrad district',
                'Dinner at yurt camp with traditional dishes',
                'Breakfast at yurt camp',
                'All ground transportation in 4WD vehicle (essential for desert driving)',
                'Professional English-speaking guide with environmental/historical expertise',
                'All entrance fees (Muynak Museum, Mizdakhan complex, etc.)',
                'Documentary film screening at Muynak Museum',
                'Shubat (camel milk) tasting experience',
                'Bottled water throughout journey',
                'Airport/hotel pickup and drop-off in Nukus',
            ],

            'excluded_items' => [
                'Flights to/from Nukus (we can assist with booking)',
                'Accommodation in Nukus before/after tour (available upon request)',
                'Personal expenses and souvenirs',
                'Alcoholic beverages',
                'Travel insurance (required for this tour)',
                'Tips for guide and driver (suggested: $15-20 total per person)',
                'Additional photo/video fees at some sites (typically $1-2)',
            ],

            'languages' => ['English', 'Russian', 'German'],

            'requirements' => [
                ['icon' => 'warning', 'title' => 'Remote Desert Environment', 'text' => 'This tour ventures into one of Central Asia\'s most isolated regions. Mobile phone coverage is limited/absent. Medical facilities are basic. Come prepared for self-sufficiency.'],
                ['icon' => 'tshirt', 'title' => 'Desert Clothing Essential', 'text' => 'Temperatures swing 20°C between day (30-40°C summer) and night (10-15°C). Bring layers, sun protection (hat, sunglasses, SPF 50), warm jacket for evening. Dust storms possible—bring scarf/bandana.'],
                ['icon' => 'walking', 'title' => 'Moderate Physical Demands', 'text' => 'Tour involves 8+ hours driving on rough desert tracks (bumpy ride). Some walking on uneven terrain, climbing ship wrecks, hiking to viewpoints. Comfortable closed-toe shoes required.'],
                ['icon' => 'bed', 'title' => 'Basic Accommodation', 'text' => 'Yurt camp offers authentic nomadic experience but facilities are basic: shared outdoor toilets, limited water for washing (bring wet wipes), sleeping on traditional mats with bedding provided. Not suitable for luxury travelers.'],
                ['icon' => 'medical', 'title' => 'Health Precautions', 'text' => 'Bring personal medications, hand sanitizer, stomach remedies. Water is bottled/boiled only. The dried seabed has salt/dust that can irritate respiratory systems—asthmatics should bring inhalers. Travel insurance with emergency evacuation MANDATORY.'],
                ['icon' => 'camera', 'title' => 'Photography Paradise', 'text' => 'Unlimited photo opportunities but bring extra batteries (no charging at yurt camp), memory cards, and dust protection for cameras. Drone flying permitted in most areas. Night sky photography opportunities are exceptional.'],
            ],

            'include_global_requirements' => false,
            'include_global_faqs' => true,

            // MEETING POINT
            'meeting_point_address' => 'Nukus International Airport (NCU) or your hotel in Nukus',
            'meeting_instructions' => 'For airport pickup: Our driver will meet you at arrivals holding "Aral Sea Tour" sign. For hotel pickup: Wait in lobby at 08:00 AM on Day 1. Please provide flight/accommodation details at least 3 days before tour.',
            'meeting_lat' => 42.4532,
            'meeting_lng' => 59.6105,

            // BOOKING SETTINGS
            'min_booking_hours' => 168, // 7 days advance
            'has_hotel_pickup' => true,
            'pickup_radius_km' => 20,
            'cancellation_hours' => 168, // 7 days

            // RATINGS
            'rating' => 4.78,
            'review_count' => 64
        ]);

        // ATTACH CATEGORIES
        $tour->categories()->attach([4, 2, 3]); // Desert & Nomadic + Mountain & Adventure + Family & Educational

        // CREATE ITINERARY
        $itinerary = [
            [
                'title' => 'Day 1: Nukus - Muynak Ship Cemetery - Ustyurt Plateau - Aral Sea Yurt Camp',
                'description' => '<h4>08:00 - Departure from Nukus</h4>
<p>Your driver and guide meet you at airport or hotel. Brief orientation about the Aral Sea disaster, what to expect, and safety briefing for desert travel. Load into comfortable 4WD vehicle.</p>

<h4>09:30 - Khojely & Kungrad Districts</h4>
<p>Drive north through the heartland of former Khorezm. These towns were once prosperous trading centers on the Great Silk Road, now struggling with environmental collapse. Your guide explains how the Amu Darya River diversion in the 1960s transformed this region from fertile oasis to dusty steppe.</p>

<p><em>Window into History: See cotton fields—the crop that killed a sea. Soviet planners demanded "white gold" at any cost. The cost: the Aral Sea.</em></p>

<h4>11:00 - Muynak Town & Ship Cemetery (90 minutes)</h4>
<p><strong>The Most Surreal Sight in Central Asia:</strong></p>

<p>Arrive in Muynak, a town haunted by its past. Just 60 years ago, this was a bustling port city of 40,000 people with a thriving fishing industry processing millions of tons of fish annually. The harbor was filled with boats. Children swam in clean waters.</p>

<p>Today: The sea is 185 kilometers away. The ships sit in desert sand.</p>

<p><strong>Ship Graveyard Walk:</strong> Climb aboard rusted fishing vessels frozen in their final positions. These skeletal hulks were dragged from where they were marooned miles away and arranged as an open-air museum/memorial. Touch the salt-crusted hulls. Imagine the exodus as fishermen watched their livelihood evaporate kilometer by kilometer, year by year.</p>

<p><strong>Muynak Regional Museum:</strong> Watch the powerful 15-minute documentary showing Muynak in the 1960s—workers unloading fish, children playing on beaches, ships sailing. Then: the emptying. The dust storms. The health crisis (salt/pesticide poisoning from exposed seabed). The unemployment. The exodus.</p>

<p>Museum exhibits include preserved fish from species now extinct in the region, fishing equipment, photographs of Muynak\'s glory days, and maps showing the sea\'s retreat.</p>

<p><strong>New Reality:</strong> Note the new construction in town—oil and gas drilling on the old seabed has brought some economic recovery. The desert that killed fishing created hydrocarbons. Dark irony.</p>

<p><strong>Photo Time:</strong> The ship cemetery is most photogenic in late morning light. Your guide helps position shots.</p>

<h4>12:45 - Lunch with Local Family (Included)</h4>
<p>Traditional Karakalpak meal at a local guesthouse. Taste regional specialties while your hosts share personal stories of living through the disaster. Many lost jobs, family members who migrated, traditional livelihoods destroyed.</p>

<h4>14:00 - Journey Across the Dried Seabed (200km, 4-5 hours with stops)</h4>
<p><strong>This is why you need 4WD:</strong> Cross the Aralkum Desert—the "new desert" that didn\'t exist before 1960. The former seabed is now a salt-crusted wasteland dotted with:</p>

<ul>
    <li><strong>Drilling Towers:</strong> Like mechanical trees, pumping oil and gas from beneath the former lakebed. Stop to photograph this surreal industrial landscape.</li>
    <li><strong>Seashells:</strong> Millions of shells scattered across the desert—clams, snails, evidence of marine life that perished when the water left. Collect a few as haunting souvenirs.</li>
    <li><strong>Salt Flats:</strong> White expanses where salt concentration is so high nothing grows. During Soviet times, pesticides washed into the sea from cotton fields. Now those toxins blow in dust storms, causing respiratory illness.</li>
    <li><strong>Ustyurt Plateau Access (16:00):</strong> Approach the dramatic escarpment of the Ustyurt Plateau. Stop at scenic viewpoints where 100-meter limestone cliffs display millions of years of geological history—multicolored layers of pale pink, blue, white formed when this area was submerged beneath the ancient Tethys Ocean 20-60 million years ago.</li>
</ul>

<p><strong>Nomad Cemetery:</strong> Pass ancient burial grounds of nomadic peoples who traveled these routes for millennia. Simple stone markers in the vastness.</p>

<p><strong>Canyons & Caves:</strong> If time and route permit, short detour to see "chinks"—the spectacular canyons cutting into the plateau edge. Platforms, caverns, and springs hidden in the rocks.</p>

<h4>18:30 - Arrive Besqala Yurt Camp, Aral Sea</h4>
<p>Reach the shore of what remains. The yurt camp sits on the edge of the shrunken Aral Sea. Drop your luggage in your assigned yurt—traditional felt dwellings used by Kazakh and Karakalpak nomads for centuries.</p>

<p><strong>Your Yurt:</strong> Circular felt tent with colorful interior textiles, sleeping mats, blankets. Shared bathroom facilities outside (basic but clean). This is authentic—not glamping. Embrace it.</p>

<h4>19:00 - Sunset at the Aral Sea (45 minutes)</h4>
<p>Drive or walk down to the water\'s edge. This is it—what remains of the world\'s fourth-largest lake. The water is more saline than ocean (salt concentration tripled as the sea shrank). Some fish species still survive, adapted to the new conditions.</p>

<p><strong>Activities:</strong></p>
<ul>
    <li>Wade or swim (the water is safe but very salty)</li>
    <li>Collect shells from the beach</li>
    <li>Photograph the sunset over these "forgotten shores"</li>
    <li>Breathe the "sea oxygen"—the air carries that ocean smell despite everything</li>
    <li>Reflect on what this place teaches about water security, climate change, human impact</li>
</ul>

<h4>20:00 - Dinner at Yurt Camp (Included)</h4>
<p>Traditional Karakalpak feast prepared over open fire:</p>
<ul>
    <li>Fresh tandoor bread (non)</li>
    <li>Plov or shurpa (meat and rice/soup)</li>
    <li>Grilled fish (caught locally from remaining Aral waters)</li>
    <li>Fresh vegetables and salads</li>
    <li>Green tea ceremony</li>
</ul>

<p>Dinner is communal—share the table with fellow travelers, guides, camp staff. Exchange stories.</p>

<h4>21:30 - Nomadic Night Experience</h4>
<p><strong>Stargazing:</strong> The Aral Sea region has virtually zero light pollution. The Milky Way stretches from horizon to horizon. Bring a star map or use phone apps to identify constellations.</p>

<p><strong>Night Photography:</strong> For photographers, this is golden hour. Yurts illuminated from within, star trails over the desert, silhouettes of drilling towers. Your guide can help with long-exposure settings.</p>

<p><strong>Silence:</strong> Step away from the camp. Listen to the desert silence—broken only by wind and the distant sound of waves (waves that shouldn\'t exist because the sea shouldn\'t be here anymore).</p>

<p><strong>Overnight:</strong> Sleep in your yurt on traditional mats with provided bedding. Temperatures can drop to 10-15°C even in summer—your warm jacket comes out now.</p>

<p><em>Meals: Lunch and dinner included.</em></p>
<p><em>Accommodation: Besqala Yurt Camp (basic, authentic nomadic experience).</em></p>',
                'type' => 'day',
                'default_start_time' => '08:00',
                'duration_minutes' => 1440,
                'sort_order' => 1
            ],

            [
                'title' => 'Day 2: Aral Sea - Ustyurt Plateau - Mizdakhan Necropolis - Nukus',
                'description' => '<h4>07:00 - Sunrise & Breakfast</h4>
<p>Wake early to witness sunrise over the Aral Sea. The light at this hour is ethereal—golden rays illuminating the water, casting long shadows across the desert. Last chance for photographs.</p>

<p><strong>Breakfast (Included):</strong> Fresh bread, butter, honey, cream, jam, tea, coffee. Simple but nourishing nomadic fare.</p>

<h4>08:30 - Depart for Ustyurt Plateau Exploration</h4>
<p>Leave the coast and head into the interior of the Ustyurt Plateau—one of Central Asia\'s most dramatic geological wonders.</p>

<h4>09:30 - Kurgancha Kala Fortress (45 minutes)</h4>
<p><strong>Hidden Caravanserai from the 13th Century:</strong></p>

<p>Tucked among the rolling hills of the Ustyurt, this fortress is easy to miss—exactly why it survives. During the Mongol invasions and turbulent medieval period, this fortified caravanserai provided shelter for Silk Road merchants crossing the dangerous plateau.</p>

<p><strong>Explore:</strong> Walk the crumbling walls, identify rooms where traders slept, courtyards where camels were kept. Your guide explains the architecture—how walls were designed to withstand both wind and attack. Climb to the highest point for 360° views of the stark landscape.</p>

<p><strong>Photography:</strong> The fortress against the Ustyurt cliffs makes for striking compositions.</p>

<h4>11:00 - Kubla Ustyurt Village (60 minutes)</h4>
<p><strong>One of the Most Remote Villages in Uzbekistan:</strong></p>

<p>Population: ~200 people. Occupation: Camel herding. This village exists because of a Soviet-era Gas Compressor Station (still functioning) that required workers. Now it\'s one of the last outposts in the region.</p>

<p><strong>Camel Milk Tasting:</strong> Visit a local family who keeps camels. Taste fresh <strong>shubat</strong>—fermented camel sour milk. It\'s tangy, effervescent, and considered medicinal:</p>
<ul>
    <li>Boosts immune system</li>
    <li>Aids digestive health (probiotic properties)</li>
    <li>Normalizes gastrointestinal and nervous systems</li>
    <li>Rich in vitamins C and B, minerals</li>
</ul>

<p>The milk comes from camels grazing on wild medicinal plants, giving it unique properties. Kazakh nomads swear by its healing powers for tuberculosis, diabetes, peptic ulcers.</p>

<p><strong>Cultural Exchange:</strong> Your hosts explain nomadic life—how they survive in this harsh environment, the challenges of herding in the desert, their connection to traditions going back millennia.</p>

<p><strong>Small Airstrip:</strong> Notice the grass airstrip—used for supplying the gas station and occasional medical emergencies. This is how remote you are.</p>

<h4>12:15 - Lake Sudochie & Ancient Lighthouse (75 minutes)</h4>
<p><strong>A Shrinking Refuge:</strong></p>

<p>Lake Sudochie is itself a victim of the Aral Sea disaster—it\'s shrinking as water flows decrease. But it remains a critical bird habitat with over 250 species documented:</p>
<ul>
    <li>Flamingos (seasonal)</li>
    <li>Pelicans</li>
    <li>Herons, egrets</li>
    <li>Migratory waterfowl using the Central Asian Flyway</li>
    <li>Endangered species finding last refuge here</li>
</ul>

<p><strong>Bird Watching Platform:</strong> Modern observation point built for ornithologists. Bring binoculars if you have them.</p>

<p><strong>Ancient Lighthouse Hill:</strong> Climb to the ruins of the old lighthouse/signal tower. In ancient times, fires burned here to guide:</p>
<ul>
    <li>Ships navigating the Aral Sea at night</li>
    <li>Caravans crossing the desert (the same fires visible from land)</li>
</ul>

<p>Now: The sea is gone, caravans are gone. Only the hill remains.</p>

<p><strong>Abandoned Settlement:</strong> Surrounding the lighthouse are ruins of a Russian and Polish settlement—families deported here during Stalin\'s repressions in the 1930s-40s. Explore:</p>
<ul>
    <li>Cemetery with Cyrillic headstones</li>
    <li>Ruins of hospital (crumbling walls, scattered medical equipment)</li>
    <li>Cane houses collapsing into the earth</li>
    <li>Old Fish Factory—once processing Aral catch, now empty shells</li>
</ul>

<p>These were gulags of sorts—forced settlements where "enemies of the people" were sent to work in fishing and processing. When the sea left, so did the reason for the settlement. Most buildings abandoned by 1990s.</p>

<p><strong>Reflection Moment:</strong> This site combines natural and human tragedy—environmental disaster layered over political repression. Your guide shares stories collected from survivors\' descendants.</p>

<h4>13:45 - Lunch in Kungrad (Included)</h4>
<p>Stop at local café in Kungrad district. Simple but hearty Uzbek meal—shurpa soup, bread, salad, tea. Bathroom break, chance to stretch legs.</p>

<h4>15:00 - Mizdakhan Necropolis (90 minutes)</h4>
<p><strong>One of Central Asia\'s Holiest Sites:</strong></p>

<p>This ancient cemetery and archaeological complex spans 2,400 years of continuous use—from the 4th century BC through today. It\'s built atop the ruins of <strong>Gyaur Kala</strong> fortress (6th BC - 9th AD), which was once the second-largest city in Khorezm after Konye Urgench.</p>

<p><strong>Religious Significance:</strong></p>
<p>Mizdakhan blends Zoroastrian and Islamic traditions. Local legend claims this is the burial site of <strong>Adam</strong> (yes, that Adam—the first human). While this is myth, it explains why pilgrims have venerated this site for millennia.</p>

<p><strong>Key Monuments:</strong></p>

<p><strong>1. Shamun-Nabi Mausoleum (19th century):</strong> The most visited structure. According to legend, a brick falls from the structure every year, and when the last brick falls, the world will end. Pilgrims add bricks when they visit to delay the apocalypse. See hundreds of bricks stacked by the faithful.</p>

<p><strong>2. Mazlumkhan-Sulu Mausoleum (12th-14th centuries):</strong> Named for a female ruler of Khorezm. Exquisite tilework (what remains after centuries). Women often pray here.</p>

<p><strong>3. Caliph Erejep Mausoleum (12th century):</strong> Honoring an early Islamic leader who brought Islam to the region. Important pilgrimage site.</p>

<p><strong>4. Zoroastrian Dakhma Ruins:</strong> Unlike Muslims, Zoroastrians didn\'t bury their dead. Bodies were left on flat-roofed towers (dakhmas) for vultures to consume, then bones placed in ossuaries. You can see remnants of these structures—evidence of pre-Islamic religious practices.</p>

<p><strong>5. Gyaur Kala Fortress Ruins:</strong> Scattered across the hillside. Pottery shards, ancient bricks, traces of walls. Archaeologists have found artifacts dating back 2,500 years.</p>

<p><strong>The View:</strong> From the top of Mizdakhan hill, panoramic views of Kungrad district and the surrounding plains. On clear days, you can see the distant escarpment of the Ustyurt Plateau.</p>

<p><strong>Pilgrimage Culture:</strong> Watch local pilgrims performing rituals—circling tombs, tying prayer ribbons, leaving offerings. Respectful photography allowed but ask permission.</p>

<h4>16:45 - Drive Back to Nukus (30km, 35 minutes)</h4>
<p>Final leg of the journey. Reflect on what you\'ve witnessed—environmental catastrophe, geological wonders, ancient history, nomadic culture, Soviet-era trauma, spiritual traditions spanning millennia.</p>

<h4>17:30 - Arrive Nukus - End of Tour</h4>
<p>Drop-off at your hotel or airport. If you have time before departure:</p>

<p><strong>Optional Extension (not included):</strong> Visit the Savitsky Museum in Nukus—the world\'s second-largest collection of Russian avant-garde art saved from Stalin\'s purges. It pairs perfectly thematically with the Aral Sea disaster (both Soviet-era catastrophes). Museum visit: $15-20, 2-3 hours.</p>

<p><strong>Farewell:</strong> Your guide bids farewell. You leave with sand in your shoes, salt on your skin, and a profound understanding of environmental fragility.</p>

<p><em>Meals: Breakfast and lunch included.</em></p>
<p><em>No accommodation included on this day. If you need a hotel in Nukus for tonight, we can arrange it for additional cost.</em></p>

<h4>END OF SERVICES</h4>

<p><strong>What You Take Home:</strong></p>
<ul>
    <li>Photographs unlike anywhere else on Earth</li>
    <li>Stories to tell for years</li>
    <li>A deeper understanding of water security, climate change, and human impact</li>
    <li>Memories of nomadic hospitality and desert silence</li>
    <li>Perhaps a seashell from a landlocked desert—evidence of a vanished sea</li>
</ul>',
                'type' => 'day',
                'default_start_time' => '07:00',
                'duration_minutes' => 1440,
                'sort_order' => 2
            ],
        ];

        foreach ($itinerary as $item) {
            $tour->itineraryItems()->create($item);
        }

        // Update city tour count cache
        $tourCount = $nukus->tours()->where('is_active', true)->count();
        $nukus->tour_count_cache = $tourCount;
        $nukus->save();

        $this->info("✅ Aral Sea Tour created successfully!");
        $this->info("Tour ID: {$tour->id}");
        $this->info("Title: {$tour->title}");
        $this->info("Price: $385 USD per person");
        $this->info("URL: http://127.0.0.1:8000/tours/{$tour->slug}");
        $this->info("Itinerary days: " . $tour->itineraryItems()->count());

        return 0;
    }
}
