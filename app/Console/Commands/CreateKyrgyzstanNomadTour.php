<?php

namespace App\Console\Commands;

use App\Models\City;
use App\Models\Tour;
use App\Models\TourCategory;
use App\Models\ItineraryItem;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class CreateKyrgyzstanNomadTour extends Command
{
    protected $signature = 'create:kyrgyzstan-nomad-tour';
    protected $description = 'Create Kyrgyzstan Nomadic Adventure: Song Kul Lake & Alpine Valleys Tour';

    public function handle()
    {
        $this->info('Creating Kyrgyzstan Nomadic Adventure Tour...');

        // Create/get cities
        $bishkek = City::firstOrCreate(
            ['slug' => 'bishkek'],
            [
                'name' => 'Bishkek',
                'country' => 'Kyrgyzstan',
                'region' => 'Chuy Region',
                'latitude' => 42.8746,
                'longitude' => 74.5698,
                'description' => 'Capital city of Kyrgyzstan, gateway to the Tian Shan mountains',
                'is_popular' => true,
            ]
        );

        $karakol = City::firstOrCreate(
            ['slug' => 'karakol'],
            [
                'name' => 'Karakol',
                'country' => 'Kyrgyzstan',
                'region' => 'Issyk-Kul Region',
                'latitude' => 42.4906,
                'longitude' => 78.3935,
                'description' => 'Adventure capital on Issyk-Kul Lake, gateway to alpine valleys',
                'is_popular' => true,
            ]
        );

        // Get categories
        $categories = TourCategory::whereIn('slug', [
            'adventure',
            'cultural',
            'nature-wildlife',
            'mountain',
        ])->get();

        // Create tour
        $tour = Tour::create([
            'title' => 'Kyrgyzstan Nomadic Adventure: Song Kul Lake & Tian Shan Valleys',
            'slug' => 'kyrgyzstan-nomadic-adventure-song-kul-tian-shan',
            'description' => 'Experience authentic nomadic life in the Kyrgyz highlands. Sleep in traditional yurts at Song Kul Lake (3,016m), ride horses across endless meadows, learn ancient crafts from herder families, and trek through pristine alpine valleys. This immersive 7-day journey combines breathtaking mountain landscapes with deep cultural connection to Central Asia\'s last nomadic traditions.',

            'short_description' => 'Live like Kyrgyz nomads at Song Kul Lake (3,016m). 7-day adventure with yurt stays, horseback riding, alpine trekking, and authentic cultural immersion. June-September departures.',
            'long_description' => '<h2>Kyrgyzstan Nomadic Adventure</h2>
<p>Experience authentic nomadic life in the Kyrgyz highlands. Sleep in traditional yurts at Song Kul Lake (3,016m), ride horses across endless meadows, learn ancient crafts from herder families, and trek through pristine alpine valleys.</p>
<p>This immersive 7-day journey combines breathtaking mountain landscapes with deep cultural connection to Central Asia\'s last nomadic traditions.</p>',

            'duration_days' => 7,
            'duration_text' => '7 days / 6 nights',
            'tour_type' => 'group_only',
            'city_id' => $bishkek->id,

            'price_per_person' => 1295.00,
            'currency' => 'USD',
            'max_guests' => 12,
            'min_guests' => 4,

            'hero_image' => 'images/tours/kyrgyzstan-nomad/song-kul-yurts-mountains.webp',
            'gallery_images' => json_encode([
                ['path' => 'images/tours/kyrgyzstan-nomad/horseback-riding-song-kul.webp', 'alt' => 'Horseback riding at Song Kul Lake Kyrgyzstan'],
                ['path' => 'images/tours/kyrgyzstan-nomad/nomadic-family-yurt.webp', 'alt' => 'Traditional yurt stay with nomadic family'],
                ['path' => 'images/tours/kyrgyzstan-nomad/altyn-arashan-valley.webp', 'alt' => 'Altyn-Arashan valley hot springs trekking'],
                ['path' => 'images/tours/kyrgyzstan-nomad/issyk-kul-sunset.webp', 'alt' => 'Sunset over Issyk-Kul Lake'],
                ['path' => 'images/tours/kyrgyzstan-nomad/jeti-oguz-seven-bulls.webp', 'alt' => 'Jeti-Oguz Seven Bulls rock formations'],
                ['path' => 'images/tours/kyrgyzstan-nomad/traditional-felt-making.webp', 'alt' => 'Traditional felt-making workshop Kyrgyzstan'],
            ]),

            'highlights' => [
                '2 nights in authentic yurts at Song Kul Lake (3,016m) with nomadic families',
                'Horseback riding across alpine meadows like Kyrgyz nomads',
                'Hands-on workshops: felt-making, kumys tasting, traditional bread baking',
                'Trekking in Altyn-Arashan hot springs valley',
                'Jeti-Oguz "Seven Bulls" red rock formations',
                'Issyk-Kul Lake - world\'s second-largest alpine lake',
                'Burana Tower & ancient Silk Road petroglyphs',
                'Eagle hunting demonstration with local hunters',
            ],

            'included_items' => [
                'All accommodation (4 nights hotels, 2 nights yurt camps)',
                'All meals during yurt stays (breakfast, lunch, dinner)',
                'Breakfasts at hotels',
                'English-speaking guide throughout',
                '4WD transportation for mountain roads',
                'Horseback riding at Song Kul (2-3 hours)',
                'All entrance fees to national parks and sites',
                'Yurt camp experience with nomadic families',
                'Felt-making and bread-baking workshops',
                'Eagle hunting demonstration',
                'Airport transfers in Bishkek',
            ],

            'excluded_items' => [
                'International flights to/from Bishkek',
                'Lunches and dinners in cities (except at yurt camps)',
                'Personal travel insurance',
                'Tips for guides and drivers',
                'Alcoholic beverages',
                'Personal expenses and souvenirs',
                'Optional activities not mentioned in itinerary',
            ],

            'requirements' => [
                'Moderate fitness level - able to ride horses and hike 2-4 hours',
                'Comfortable with basic yurt camp facilities (shared bathrooms)',
                'Warm clothing for high-altitude nights (can be 5-10°C even in summer)',
                'Visa not required for most nationalities (check Kyrgyzstan e-visa)',
                'Travel insurance covering adventure activities recommended',
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
            'title' => 'Arrival in Bishkek - Burana Tower - Kochkor Village',
            'description' => '<p>Welcome to Kyrgyzstan! Meet your guide at Manas International Airport or your Bishkek hotel.</p>
<p><strong>Burana Tower (11th century)</strong> - Drive to this ancient Silk Road minaret near Tokmok. Explore the open-air museum of balbals (Turkic stone warriors) and climb the tower for panoramic Chuy Valley views.</p>
<p><strong>Konorchek Canyons</strong> - Short stop at these colorful red rock formations for photos.</p>
<p><strong>Kochkor Village</strong> - Arrive in this traditional town, gateway to Song Kul. Visit a women\'s felt-making cooperative and try your hand at creating shyrdak (traditional felt carpet). Learn how generations of Kyrgyz women have preserved this UNESCO-recognized craft.</p>
<p><strong>Dinner with local family</strong> - Enjoy homemade lagman noodles, shorpo soup, and lepyoshka bread. Overnight in family guesthouse.</p>
<p><em>Meals: Lunch, Dinner</em><br>
<em>Accommodation: Guesthouse in Kochkor</em><br>
<em>Driving: ~4 hours (270 km)</em></p>',
            'duration' => '8 hours',
            'order' => 1,
        ]);

        // Day 2
        ItineraryItem::create([
            'tour_id' => $tour->id,
            'day_number' => 2,
            'type' => 'day',
            'title' => 'Kochkor - Song Kul Lake (3,016m) - Yurt Camp Experience',
            'description' => '<p>Today we journey to the legendary Song Kul - one of Central Asia\'s most spectacular high-altitude lakes.</p>
<p><strong>Mountain Drive via Kalmak-Ashuu Pass (3,447m)</strong> - Switchback roads reveal jaw-dropping vistas of emerald valleys and snow-capped peaks. Stop for photos of grazing yaks and shepherd camps.</p>
<p><strong>Song Kul Lake Arrival</strong> - At 3,016 meters above sea level, this pristine alpine lake is surrounded by endless meadows dotted with white yurts. The air is pure, the silence profound.</p>
<p><strong>Yurt Camp Welcome</strong> - Meet your host family who migrate here each summer with their herds (May-September). Your yurt is simple but cozy - felt-lined walls, colorful carpets, and low wooden beds piled with thick blankets.</p>
<p><strong>Afternoon Activities:</strong></p>
<ul>
<li>Horseback riding across the jailoo (summer pasture) - 2-3 hours</li>
<li>Help with daily tasks: milking mares, fetching water, gathering dung for fuel</li>
<li>Learn to make bread in a kazan (cast-iron pot)</li>
<li>Taste kumys (fermented mare\'s milk) and kymyz (fresh yogurt)</li>
</ul>
<p><strong>Evening</strong> - Traditional dinner in the dining yurt: beshbarmak (meat with noodles), shorpo, fresh cream. As darkness falls, step outside for an unbelievable star show - the Milky Way stretches across the sky in dazzling clarity.</p>
<p><em>Meals: Breakfast, Lunch, Dinner</em><br>
<em>Accommodation: Traditional yurt at Song Kul Lake</em><br>
<em>Driving: ~3 hours (90 km off-road)</em></p>',
            'duration' => '7 hours',
            'order' => 2,
        ]);

        // Day 3
        ItineraryItem::create([
            'tour_id' => $tour->id,
            'day_number' => 3,
            'type' => 'day',
            'title' => 'Full Day at Song Kul Lake - Nomadic Immersion',
            'description' => '<p>Wake to the sounds of horses whinnying and shepherds calling their flocks. Today you truly live as a nomad.</p>
<p><strong>Morning Activities:</strong></p>
<ul>
<li>Join shepherds on horseback to move livestock to fresh pastures</li>
<li>Hike along the lakeshore - wildflowers carpet the meadows in summer</li>
<li>Visit neighboring yurt camps and meet other nomadic families</li>
<li>Photography opportunities: yurts reflected in crystal waters, herds against mountain backdrops</li>
</ul>
<p><strong>Felt-Making Workshop</strong> - Spend time with the women learning this ancient art. They\'ll show you how to lay wool, sprinkle hot water, roll and beat the felt until it becomes strong and beautiful. Create a small piece to take home.</p>
<p><strong>Lunch</strong> - Fresh bread, thick cream, honey, and tea around the fire.</p>
<p><strong>Afternoon</strong> - Free time to:</p>
<ul>
<li>Ride further into the surrounding valleys</li>
<li>Fish for trout in mountain streams</li>
<li>Simply sit and absorb the tranquility</li>
<li>Learn Kyrgyz songs and stories from your hosts</li>
</ul>
<p><strong>Evening</strong> - Farewell dinner with the family. They may perform traditional songs and invite you to try the komuz (three-stringed lute).</p>
<p><em>Meals: Breakfast, Lunch, Dinner</em><br>
<em>Accommodation: Traditional yurt at Song Kul Lake</em></p>',
            'duration' => 'Full day',
            'order' => 3,
        ]);

        // Day 4
        ItineraryItem::create([
            'tour_id' => $tour->id,
            'day_number' => 4,
            'type' => 'day',
            'title' => 'Song Kul - Bokonbaevo - Issyk-Kul Lake',
            'description' => '<p>Bid farewell to your nomadic hosts and descend to the legendary Issyk-Kul Lake.</p>
<p><strong>Breakfast at Song Kul</strong> - Last morning at 3,000 meters. Help pack kumys and fresh bread for the journey.</p>
<p><strong>Mountain Descent</strong> - Different route offers new perspectives: gorges, waterfalls, and distant views of the lake shimmering far below.</p>
<p><strong>Eagle Hunting Demonstration (Bokonbaevo)</strong> - Meet a berkutchi (eagle hunter) and his magnificent golden eagle. Watch the bird soar and dive, learn about this 4,000-year-old tradition. The bond between hunter and eagle is extraordinary - these birds live with families for decades.</p>
<p><strong>Issyk-Kul Lake Arrival</strong> - The "Pearl of Tian Shan" is the world\'s second-largest alpine lake (after Titicaca). At 1,607m elevation, surrounded by 5,000m peaks, it never freezes despite winter temperatures of -20°C.</p>
<p><strong>Lakeside Walk</strong> - Stroll the rocky beach, breathe the clean air, watch waves lap the shore. The water is slightly salty and said to have healing properties.</p>
<p><strong>Overnight</strong> - Comfortable guesthouse with hot showers (a welcome luxury after yurt life!).</p>
<p><em>Meals: Breakfast, Lunch</em><br>
<em>Accommodation: Guesthouse on Issyk-Kul Lake</em><br>
<em>Driving: ~5 hours (200 km)</em></p>',
            'duration' => '8 hours',
            'order' => 4,
        ]);

        // Day 5
        ItineraryItem::create([
            'tour_id' => $tour->id,
            'day_number' => 5,
            'type' => 'day',
            'title' => 'Jeti-Oguz Gorge - Karakol City',
            'description' => '<p>Explore the stunning southern shore of Issyk-Kul and the adventure capital of Karakol.</p>
<p><strong>Jeti-Oguz ("Seven Bulls")</strong> - These dramatic red rock formations rise from green valleys like ancient monuments. Legend says seven bulls turned to stone here; another tale speaks of a khan who broke his daughter\'s heart. Walk among the formations and up to "Broken Heart Rock" viewpoint (easy 30-min hike).</p>
<p><strong>Kok-Jaiyk Valley</strong> - Follow a rushing river into a side gorge lined with Tian Shan fir trees. Perfect spot for photos and picnic lunch.</p>
<p><strong>Karakol City</strong> - This charming town blends Russian, Dungan, and Kyrgyz cultures:</p>
<ul>
<li><strong>Dungan Mosque (1910)</strong> - Built entirely of wood without nails, in Chinese architectural style by the Muslim Dungan people</li>
<li><strong>Holy Trinity Orthodox Cathedral (1895)</strong> - Colorful wooden church built to withstand earthquakes</li>
<li><strong>Local bazaar</strong> - Browse dried fruits, honey, kurut (dried yogurt balls), handicrafts</li>
</ul>
<p><strong>Optional activity</strong> - Visit Przhevalsky Museum (dedicated to the Russian explorer) or relax at your guesthouse.</p>
<p><em>Meals: Breakfast, Lunch</em><br>
<em>Accommodation: Guesthouse in Karakol</em><br>
<em>Driving: ~2 hours (100 km)</em></p>',
            'duration' => '7 hours',
            'order' => 5,
        ]);

        // Day 6
        ItineraryItem::create([
            'tour_id' => $tour->id,
            'day_number' => 6,
            'type' => 'day',
            'title' => 'Altyn-Arashan Valley Trek - Hot Springs',
            'description' => '<p>Today\'s adventure takes you deep into one of Kyrgyzstan\'s most beautiful alpine valleys.</p>
<p><strong>Drive to Ak-Suu Trailhead</strong> - Morning departure in 4WD vehicles up a rough mountain road (some groups choose to hike from lower down - add 2 hours).</p>
<p><strong>Altyn-Arashan Valley Hike</strong> - Trek through pristine Tian Shan wilderness:</p>
<ul>
<li>Towering peaks surround you on all sides</li>
<li>Crystal-clear river rushes alongside the trail</li>
<li>Wildflower meadows (June-August)</li>
<li>Possible wildlife: ibex, marmots, golden eagles</li>
<li>Duration: 3-4 hours moderate trekking</li>
</ul>
<p><strong>Hot Springs</strong> - Arrive at natural thermal springs (40-50°C) fed by underground volcanic activity. Soak in outdoor pools while snow-capped Palatka Peak (4,260m) towers above. Local belief says these waters cure everything from arthritis to heartbreak!</p>
<p><strong>Picnic Lunch</strong> - Fresh bread, cheese, nuts, dried fruits in this spectacular setting.</p>
<p><strong>Afternoon Options:</strong></p>
<ul>
<li>Extended hike to Ala-Kul Lake (turquoise glacial lake) - for fit hikers only, 6-7 hours round trip</li>
<li>Relax at hot springs and explore the valley</li>
<li>Visit the small yurt camps and shepherds</li>
</ul>
<p><strong>Return to Karakol</strong> - Evening descent. Celebratory dinner at a local restaurant trying Karakol\'s famous ashlan-fu (cold Dungan noodle soup) and naryn.</p>
<p><em>Meals: Breakfast, Lunch, Dinner</em><br>
<em>Accommodation: Guesthouse in Karakol</em><br>
<em>Driving + Trekking: 6-7 hours total</em></p>',
            'duration' => '8 hours',
            'order' => 6,
        ]);

        // Day 7
        ItineraryItem::create([
            'tour_id' => $tour->id,
            'day_number' => 7,
            'type' => 'day',
            'title' => 'Karakol - Cholpon-Ata Petroglyphs - Bishkek - Departure',
            'description' => '<p>Final day of your nomadic odyssey, returning to Bishkek via the northern lakeshore.</p>
<p><strong>Scenic Issyk-Kul Drive</strong> - Follow the northern shore with mountain panoramas across the blue waters.</p>
<p><strong>Cholpon-Ata Petroglyphs (optional stop)</strong> - Open-air museum with 2,000+ rock carvings from Bronze Age to early Middle Ages. See ancient depictions of ibex, snow leopards, hunters, and shamanic symbols carved by nomadic ancestors.</p>
<p><strong>Lunch stop</strong> - Lakeside café for fresh fish or traditional Kyrgyz dishes.</p>
<p><strong>Return to Bishkek</strong> - Afternoon arrival in the capital. Depending on your flight time:</p>
<ul>
<li>Visit Osh Bazaar for last-minute souvenir shopping (felt products, honey, dried fruits)</li>
<li>Direct airport transfer for evening flights</li>
<li>Hotel check-in if staying extra nights</li>
</ul>
<p><strong>Farewell</strong> - Say goodbye to your guide and group, carrying memories of yurt stays, horseback rides across endless meadows, the taste of fresh kumys, and the kindness of nomadic families who shared their ancient way of life.</p>
<p><em>Meals: Breakfast, Lunch</em><br>
<em>Accommodation: Not included (or optional night in Bishkek)</em><br>
<em>Driving: ~5 hours (420 km)</em></p>',
            'duration' => '7 hours',
            'order' => 7,
        ]);

        $this->info("✅ Tour created successfully: {$tour->title}");
        $this->info("Tour ID: {$tour->id}");
        $this->info("Slug: {$tour->slug}");

        return Command::SUCCESS;
    }
}
