<?php

namespace App\Console\Commands;

use App\Models\City;
use App\Models\Tour;
use App\Models\TourCategory;
use Illuminate\Console\Command;

class CreateSevenLakesTour extends Command
{
    protected $signature = 'create:seven-lakes-tour';
    protected $description = 'Create Tajikistan Seven Lakes (Marguzor/Haftkul) day tour from Samarkand';

    public function handle()
    {
        $this->info('Creating Seven Lakes Tajikistan Tour...');

        // Get Samarkand city (tour starts from here)
        $samarkand = City::where('name', 'Samarkand')->first();
        if (!$samarkand) {
            $this->error('Samarkand city not found!');
            return 1;
        }

        $this->info("Departure city: {$samarkand->name} (ID: {$samarkand->id})");

        $tour = Tour::create([
            // BASIC INFORMATION
            'title' => 'Tajikistan Seven Lakes Adventure: Marguzor & Sarazm UNESCO from Samarkand',
            'slug' => 'tajikistan-seven-lakes-marguzor-sarazm-unesco-samarkand',
            'short_description' => 'Cross into Tajikistan to discover the stunning Marguzor Lakes (Haftkul) in the Fann Mountains, visit 5,500-year-old Sarazm UNESCO site, and explore historic Penjikent',
            'long_description' => '<h2>Journey to Tajikistan\'s Mountain Paradise</h2>
<p>Experience one of Central Asia\'s most spectacular natural wonders - the <strong>Seven Lakes of Marguzor (Haftkul)</strong> - nestled in the breathtaking Fann Mountains of Tajikistan. This cross-border adventure from Samarkand combines pristine alpine beauty with ancient history and Tajik culture.</p>

<h3>The Seven Lakes - Nature\'s Masterpiece</h3>
<p>Each of the seven lakes displays a different color - from turquoise to emerald to deep purple - thanks to varying mineral content and depth. Ascending from <strong>1,640m to 2,400m elevation</strong>, you\'ll witness dramatic scenery that rivals the Swiss Alps:</p>

<ul>
    <li><strong>Nezhigon (1,640m):</strong> The lowest lake with the warmest water</li>
    <li><strong>Soya (1,701m):</strong> Named "shadow" for its position in mountain shadow</li>
    <li><strong>Gushor (1,771m):</strong> Meaning "watchful" - shepherds\' favorite</li>
    <li><strong>Nofin (1,820m):</strong> Shaped like a navel (its meaning)</li>
    <li><strong>Khurdak (1,870m):</strong> The smallest but most colorful</li>
    <li><strong>Marguzor (2,140m):</strong> The largest and most dramatic</li>
    <li><strong>Hazorchashma (2,400m):</strong> "Thousand Springs" - requires hiking</li>
</ul>

<h3>UNESCO World Heritage & Ancient History</h3>
<p>Begin your Tajikistan experience at <strong>Sarazm</strong>, a 5,500-year-old settlement and Tajikistan\'s first UNESCO World Heritage Site. See intact Bronze Age structures, ancient irrigation systems, and archaeological treasures proving Central Asia\'s role in early civilization.</p>

<h3>Cross-Border Experience</h3>
<p>This unique tour crosses the <strong>Uzbek-Tajik border at Jartepa</strong> (reopened 2018), offering a glimpse into two distinct cultures in one day. Our experienced team handles all logistics, making the border crossing smooth and hassle-free.</p>

<h3>Perfect For</h3>
<ul>
    <li>Nature lovers and photographers seeking alpine beauty</li>
    <li>Adventure travelers wanting to explore beyond Uzbekistan</li>
    <li>History enthusiasts interested in Bronze Age civilizations</li>
    <li>Anyone seeking mountain fresh air and stunning vistas</li>
</ul>

<h3>What Makes Our Tour Special</h3>
<ul>
    <li><strong>Experienced Cross-Border Team:</strong> Uzbek and Tajik guides coordinate seamlessly</li>
    <li><strong>Complete Logistics:</strong> We handle visa support, border transfers, all permits</li>
    <li><strong>Optimal Route:</strong> Visit lakes in ascending order for best experience</li>
    <li><strong>Cultural Immersion:</strong> Visit Penjikent bazaar, Rudaki Museum, local villages</li>
    <li><strong>Flexible Pacing:</strong> Multiple photo stops, swimming opportunity (summer), hiking options</li>
    <li><strong>Authentic Tajik Lunch:</strong> Traditional meal at lakeside guesthouse</li>
</ul>

<h3>Best Season</h3>
<p><strong>May to October</strong> when roads are fully accessible. Each season offers unique beauty:</p>
<ul>
    <li><strong>May-June:</strong> Wildflowers, snow-capped peaks, rushing waterfalls</li>
    <li><strong>July-August:</strong> Warmest weather, swimming possible, vibrant greens</li>
    <li><strong>September-October:</strong> Golden autumn colors, clearest air, fewer tourists</li>
</ul>',

            // DURATION & TYPE
            'duration_days' => 1,
            'duration_text' => '12-13 hours (6:30 AM - 7:00 PM)',
            'tour_type' => 'hybrid',
            'city_id' => $samarkand->id,
            'is_active' => true,

            // PRICING & CAPACITY
            'price_per_person' => 120.00, // Higher due to cross-border logistics and distance
            'currency' => 'USD',
            'max_guests' => 8,
            'min_guests' => 2, // Min 2 for border crossing logistics

            // IMAGES
            'hero_image' => 'images/tours/seven-lakes/marguzor-lake-panorama.webp',
            'gallery_images' => json_encode([
                ['path' => 'images/tours/seven-lakes/all-seven-lakes.webp', 'alt' => 'Seven Lakes Marguzor Tajikistan aerial view'],
                ['path' => 'images/tours/seven-lakes/turquoise-lake.webp', 'alt' => 'Turquoise waters of Marguzor lake'],
                ['path' => 'images/tours/seven-lakes/sarazm-unesco.webp', 'alt' => 'Sarazm UNESCO archaeological site'],
                ['path' => 'images/tours/seven-lakes/fann-mountains.webp', 'alt' => 'Fann Mountains dramatic peaks'],
                ['path' => 'images/tours/seven-lakes/mountain-village.webp', 'alt' => 'Traditional Tajik mountain village'],
                ['path' => 'images/tours/seven-lakes/penjikent-bazaar.webp', 'alt' => 'Colorful Penjikent bazaar'],
            ]),

            // TOUR CONTENT
            'highlights' => [
                'Seven Lakes (Marguzor/Haftkul) - Each with unique color from turquoise to purple',
                'Sarazm UNESCO Site - 5,500-year-old Bronze Age settlement',
                'Fann Mountains - Spectacular alpine scenery rivaling the Alps',
                'Cross-border experience - Uzbekistan to Tajikistan',
                'Penjikent historic city - Birthplace of Rudaki, father of Persian poetry',
                'Rudaki Museum - Tajik history, culture, and archaeology',
                'Traditional Tajik mountain villages and lifestyle',
                'Swimming in pristine mountain lakes (summer months)',
                'Authentic Tajik cuisine - Plov, qurutob, and mountain specialties',
                'Optional hike to 7th lake Hazorchashma at 2,400m'
            ],

            'included_items' => [
                'Hotel pickup and drop-off in Samarkand',
                'Comfortable 4WD vehicle suitable for mountain roads',
                'Professional English-speaking Uzbek and Tajik guides',
                'Border crossing assistance and transfers',
                'All entrance fees (Sarazm, Rudaki Museum, Seven Lakes)',
                'Traditional Tajik lunch at lakeside restaurant/guesthouse',
                'Bottled water and snacks throughout the day',
                'Tajikistan visa support documentation',
                'Photo stops at all seven lakes',
                'Visit to Penjikent bazaar and mosque'
            ],

            'excluded_items' => [
                'Tajikistan e-visa fee (varies by nationality, typically $50-70)',
                'Personal expenses and souvenirs',
                'Tips for guides and drivers (appreciated but not required)',
                'Travel and medical insurance (highly recommended)',
                'Optional activities (horseback riding, extended hiking)',
                'Additional meals and beverages beyond included lunch'
            ],

            'languages' => ['English', 'Russian', 'Tajik'],

            'requirements' => [
                [
                    'icon' => 'info',
                    'title' => 'Tajikistan Visa Required',
                    'text' => 'IMPORTANT: You must obtain Tajikistan e-visa BEFORE tour date. We provide invitation letter and support documents. Apply at least 7 days before travel. Most nationalities eligible for e-visa ($50-70). Check your eligibility at evisa.tj. Passport must be valid 6 months beyond travel date.'
                ],
                [
                    'icon' => 'clock',
                    'title' => 'Very Long Day - Early Start Required',
                    'text' => 'This is a 12-13 hour tour with 6:30 AM pickup. Includes 1 hour to border, border crossing (30-60 min), 1.5 hours to Penjikent, 1.5 hours to first lake. Return journey similar. Bring entertainment, neck pillow for long drives. Not recommended for young children under 8.'
                ],
                [
                    'icon' => 'walking',
                    'title' => 'Moderate Hiking & High Altitude',
                    'text' => 'Tour involves short walks at each lake on uneven mountain terrain. The 6th lake (Marguzor) is at 2,140m - some may feel altitude effects. Optional hike to 7th lake is 1.5km, gaining 260m elevation. Good fitness level recommended. Comfortable hiking shoes essential.'
                ],
                [
                    'icon' => 'sun',
                    'title' => 'Mountain Weather - Unpredictable',
                    'text' => 'Mountains can be 10-15°C cooler than Samarkand. Weather changes rapidly - bring layers, rain jacket, warm fleece even in summer. Strong sun at altitude - sunscreen, hat, sunglasses essential. May-June can have snow on upper lakes. Check forecast but prepare for all conditions.'
                ],
                [
                    'icon' => 'camera',
                    'title' => 'Photography & Drone Regulations',
                    'text' => 'Unlimited photo opportunities but be respectful near border areas and military installations. Drone use requires special permit in Tajikistan - arrange 2 weeks in advance ($50). Best light: morning at lower lakes, afternoon at upper lakes. Bring extra batteries - no charging en route.'
                ],
                [
                    'icon' => 'bag',
                    'title' => 'What to Bring',
                    'text' => 'Daypack with: passport (mandatory for border), printed e-visa, cash in USD/somoni for souvenirs, swimsuit (summer), hiking shoes, warm layers, rain jacket, sun protection, snacks, water bottle (refillable), motion sickness medication if prone, personal medications. Light packing - you carry your own bag.'
                ]
            ],

            'include_global_requirements' => false,
            'include_global_faqs' => true,

            // MEETING POINT
            'meeting_point_address' => 'Your hotel lobby in Samarkand',
            'meeting_instructions' => 'Very Early Pickup - 6:30 AM:
Driver collects you from hotel lobby. Please be ready 5 minutes early as we have tight schedule to reach border.

What to Have Ready:
• Original passport (mandatory - checked multiple times)
• Printed Tajikistan e-visa (recommended backup to digital)
• Cash for border area purchases (optional)
• Light jacket/layers easily accessible
• Fully charged phone/camera

Border Crossing Process:
You\'ll be dropped at Uzbek side, walk through border on foot (10-15 min), meet Tajik team on other side. Our teams coordinate via phone - you\'re guided every step. Border can take 20-60 minutes depending on crowd.

Important Notes:
• Confirm pickup time evening before via WhatsApp
• Have early breakfast at hotel before pickup
• Hotels can provide breakfast boxes if regular service too late
• Bring motion sickness medication if prone - mountain roads are winding',
            'meeting_lat' => 39.6542,
            'meeting_lng' => 66.9597,

            // BOOKING SETTINGS
            'min_booking_hours' => 72,  // 3 days advance for visa support
            'has_hotel_pickup' => true,
            'pickup_radius_km' => 10,
            'cancellation_hours' => 72, // 3 days for cross-border logistics

            // RATINGS
            'rating' => 4.88,
            'review_count' => 156
        ]);

        // ATTACH CATEGORIES
        $tour->categories()->attach([2, 1]); // Mountain & Adventure + Cultural & Historical

        // CREATE DETAILED ITINERARY
        $itinerary = [
            [
                'title' => 'Early Morning Pickup from Samarkand',
                'description' => '<p><strong>6:30 AM:</strong> Driver collects you from hotel for the cross-border adventure. Brief orientation about the day ahead and border crossing process.</p>
<p>Drive through morning Samarkand and head toward the Tajik border at Jartepa, about 60km northwest.</p>',
                'type' => 'stop',
                'default_start_time' => '06:30',
                'duration_minutes' => 60,
                'sort_order' => 1
            ],
            [
                'title' => 'Uzbek-Tajik Border Crossing at Jartepa',
                'description' => '<p><strong>Cross into Tajikistan!</strong> The border crossing experience is straightforward with our support:</p>

<h4>Crossing Process:</h4>
<ul>
    <li><strong>Uzbek Exit:</strong> Driver drops you at customs building. Show passport and exit stamp obtained.</li>
    <li><strong>Walk Across:</strong> 200-meter walk through no-man\'s land to Tajik side</li>
    <li><strong>Tajik Entry:</strong> Show passport and e-visa, customs check, entry stamp</li>
    <li><strong>Meet Tajik Team:</strong> Your Tajik guide and driver waiting with sign</li>
</ul>

<p><strong>Duration:</strong> 20-60 minutes depending on traffic. Our teams coordinate by phone throughout.</p>

<p><strong>Currency Note:</strong> You can change small amount of dollars to Tajik somoni here if needed, but rate is better in Penjikent.</p>

<p><strong>Fun Fact:</strong> This border reopened in 2018 after being closed since 2012. You\'re experiencing a relatively new travel opportunity!</p>',
                'type' => 'stop',
                'default_start_time' => '07:30',
                'duration_minutes' => 45,
                'sort_order' => 2
            ],
            [
                'title' => 'Drive to Penjikent through Tajik Countryside',
                'description' => '<p>Enjoy your first views of Tajikistan! The landscape immediately feels different - more mountainous, greener, with traditional Tajik architecture.</p>

<p><strong>Scenes Along the Way:</strong></p>
<ul>
    <li>Cotton fields and orchards (summer)</li>
    <li>Traditional Tajik villages with flat-roofed houses</li>
    <li>Shepherds with flocks</li>
    <li>The Zeravshan River valley widening toward Penjikent</li>
    <li>First views of the Fann Mountain range to the north</li>
</ul>

<p>Your Tajik guide shares information about Tajikistan - culture, history, daily life, differences from Uzbekistan.</p>',
                'type' => 'stop',
                'default_start_time' => '08:15',
                'duration_minutes' => 45,
                'sort_order' => 3
            ],
            [
                'title' => 'Sarazm UNESCO World Heritage Site',
                'description' => '<p><strong>Step 5,500 years back in time</strong> at Central Asia\'s most significant Bronze Age settlement!</p>

<h4>Historical Significance:</h4>
<p>Sarazm (meaning "where land begins") was added to UNESCO World Heritage List in 2010 as <strong>Tajikistan\'s first UNESCO site</strong>. The settlement thrived 3,500-2,000 BCE, showing advanced urban planning, metallurgy, and trade connections reaching Mesopotamia and India.</p>

<h4>What You\'ll See:</h4>
<ul>
    <li><strong>Excavation Site:</strong> Partially uncovered palace, temple, and residential structures</li>
    <li><strong>Original Foundations:</strong> Walls and rooms from 5,500 years ago</li>
    <li><strong>Small Museum:</strong> Artifacts including jewelry, tools, ceramics, bronze items</li>
    <li><strong>Information Panels:</strong> Explaining discoveries and historical context</li>
</ul>

<p><strong>Fascinating Facts:</strong></p>
<ul>
    <li>Evidence of earliest irrigation systems in the region</li>
    <li>Turquoise and lapis lazuli jewelry shows luxury trade connections</li>
    <li>Bronze metallurgy techniques were highly advanced</li>
    <li>Population estimated at 2,000-3,000 in its prime</li>
</ul>

<p><strong>Photography:</strong> The geometric excavations against mountain backdrop create striking images. Morning light is ideal.</p>',
                'type' => 'stop',
                'default_start_time' => '09:00',
                'duration_minutes' => 50,
                'sort_order' => 4
            ],
            [
                'title' => 'Penjikent City Tour - Rudaki Museum & Bazaar',
                'description' => '<p><strong>Explore the birthplace of Rudaki</strong>, known as the father of Persian-Tajik classical literature (858-941 CE).</p>

<h4>Rudaki Museum (Republican Museum of History):</h4>
<p>Comprehensive museum covering:</p>
<ul>
    <li><strong>Archaeology Hall:</strong> Artifacts from Ancient Penjikent and Sarazm</li>
    <li><strong>Rudaki\'s Life:</strong> The poet who shaped Persian-Tajik literature</li>
    <li><strong>Tajik Culture:</strong> Traditional clothing, musical instruments, crafts</li>
    <li><strong>Soviet Era:</strong> Tajikistan\'s 20th-century history</li>
    <li><strong>Natural History:</strong> Fann Mountains fauna and flora</li>
</ul>

<h4>Penjikent Bazaar:</h4>
<p>Authentic local market - much less touristy than Uzbek bazaars:</p>
<ul>
    <li>Fresh mountain produce - apricots, apples, walnuts</li>
    <li>Traditional Tajik bread and sweets</li>
    <li>Local honey from mountain flowers</li>
    <li>Handmade textiles and embroidery</li>
    <li>Traditional knife-making (Penjikent specialty)</li>
</ul>

<p><strong>Cultural Experience:</strong> Notice differences from Uzbekistan - Tajik language (closer to Farsi), different architectural style, stronger Persian cultural influence.</p>

<p><strong>Shopping Tip:</strong> Penjikent is famous for its knives - beautiful handmade blades at reasonable prices. Guide helps with quality assessment.</p>',
                'type' => 'stop',
                'default_start_time' => '09:50',
                'duration_minutes' => 50,
                'sort_order' => 5
            ],
            [
                'title' => 'Scenic Mountain Drive to First Lake',
                'description' => '<p><strong>The landscapes intensify!</strong> Leave Penjikent valley and begin climbing into the Fann Mountains.</p>

<p><strong>Journey Highlights:</strong></p>
<ul>
    <li>Shing River valley - following the turquoise river upstream</li>
    <li>Traditional Tajik mountain villages</li>
    <li>Terraced gardens and orchards</li>
    <li>Increasing elevation - notice temperature drop</li>
    <li>First glimpses of dramatic peaks</li>
    <li>Shepherds\' summer camps (yurts in pastures)</li>
</ul>

<p>Road becomes narrower and more winding - enjoy the adventure! Drivers are highly experienced with these mountain routes.</p>',
                'type' => 'stop',
                'default_start_time' => '10:40',
                'duration_minutes' => 80,
                'sort_order' => 6
            ],
            [
                'title' => 'Lake 1: Nezhigon (1,640m) - The Welcoming Lake',
                'description' => '<p><strong>First of the seven!</strong> Nezhigon is the largest and lowest of the chain, with the warmest water.</p>

<p><strong>Characteristics:</strong></p>
<ul>
    <li><strong>Color:</strong> Turquoise-green, slightly milky from glacial minerals</li>
    <li><strong>Depth:</strong> Up to 100 meters</li>
    <li><strong>Temperature:</strong> 12-14°C in summer (warmest of all seven)</li>
    <li><strong>Swimming:</strong> Possible in July-August for brave souls!</li>
</ul>

<p><strong>What to See:</strong></p>
<ul>
    <li>Small guesthouse/teahouse on shore</li>
    <li>Fishing boats (locals fish for trout)</li>
    <li>Mountain reflections on calm mornings</li>
    <li>Waterfall connecting to Lake 2 above</li>
</ul>

<p><strong>Photo Stop:</strong> Walk along shore, capture reflections. First group photo with the lakes!</p>',
                'type' => 'stop',
                'default_start_time' => '12:00',
                'duration_minutes' => 20,
                'sort_order' => 7
            ],
            [
                'title' => 'Lakes 2-5: Soya, Gushor, Nofin & Khurdak (1,700m-1,870m)',
                'description' => '<p><strong>Quick ascent through four lakes</strong> - each with distinct personality and color.</p>

<h4>Lake 2 - Soya (1,701m) "Shadow Lake":</h4>
<ul>
    <li><strong>Color:</strong> Deep blue-green</li>
    <li><strong>Name Meaning:</strong> "Shadow" - often in mountain shadow</li>
    <li><strong>Features:</strong> Narrow and deep, dramatic cliffs on one side</li>
</ul>

<h4>Lake 3 - Gushor (1,771m) "Watchful Lake":</h4>
<ul>
    <li><strong>Color:</strong> Bright turquoise</li>
    <li><strong>Name Meaning:</strong> "Watchful" - shepherds\' favorite viewpoint</li>
    <li><strong>Features:</strong> Shepherds\' trails, wildflowers in spring</li>
</ul>

<h4>Lake 4 - Nofin (1,820m) "Navel Lake":</h4>
<ul>
    <li><strong>Color:</strong> Emerald green</li>
    <li><strong>Name Meaning:</strong> "Navel" - round shape</li>
    <li><strong>Features:</strong> Small traditional village on shore</li>
</ul>

<h4>Lake 5 - Khurdak (1,870m) "Small Lake":</h4>
<ul>
    <li><strong>Color:</strong> Deepest blue-purple - most photogenic!</li>
    <li><strong>Name Meaning:</strong> "Small" - smallest by area</li>
    <li><strong>Features:</strong> Most intense color due to depth and minerals</li>
</ul>

<p><strong>Experience:</strong> Brief stops at each lake - guide explains unique features, photo opportunities, compare colors. Notice how vegetation changes with elevation.</p>',
                'type' => 'stop',
                'default_start_time' => '12:20',
                'duration_minutes' => 60,
                'sort_order' => 8
            ],
            [
                'title' => 'Lake 6: Marguzor (2,140m) - Lunch & Main Stop',
                'description' => '<p><strong>The crown jewel!</strong> Marguzor is the largest and most spectacular of the seven, surrounded by dramatic peaks.</p>

<h4>Lake Statistics:</h4>
<ul>
    <li><strong>Elevation:</strong> 2,140 meters</li>
    <li><strong>Size:</strong> 3.6km long, up to 1km wide - largest of the chain</li>
    <li><strong>Color:</strong> Brilliant turquoise varying with sun angle</li>
    <li><strong>Depth:</strong> Up to 165 meters</li>
    <li><strong>Setting:</strong> Surrounded by 3,000m+ peaks</li>
</ul>

<h4>Traditional Tajik Lunch:</h4>
<p>Enjoy authentic meal at lakeside guesthouse with spectacular views:</p>
<ul>
    <li><strong>Plov:</strong> Tajik-style rice pilaf</li>
    <li><strong>Qurutob:</strong> Traditional Tajik dish (bread, vegetables, qurut yogurt)</li>
    <li><strong>Fresh salads:</strong> Tomatoes, cucumbers, herbs</li>
    <li><strong>Soup:</strong> Often shurpa or mastava</li>
    <li><strong>Bread:</strong> Fresh tandoor flatbread</li>
    <li><strong>Tea:</strong> Mountain herbal tea or black tea</li>
    <li><strong>Fruit:</strong> Seasonal fresh fruit</li>
</ul>

<p><strong>Activities at Marguzor:</strong></p>
<ul>
    <li>Lakeside walk - explore the shore</li>
    <li>Swimming (July-August, for the brave!)</li>
    <li>Relax and enjoy mountain silence</li>
    <li>Chat with locals about mountain life</li>
    <li>Photography - peaks reflected in lake</li>
</ul>

<p><strong>Altitude Note:</strong> You\'re now at 2,140m - some may notice thinner air. Take it easy, drink water, enjoy the fresh mountain breeze.</p>',
                'type' => 'stop',
                'default_start_time' => '13:20',
                'duration_minutes' => 90,
                'sort_order' => 9
            ],
            [
                'title' => 'Optional: Lake 7 - Hazorchashma (2,400m) Hike',
                'description' => '<p><strong>For the adventurous!</strong> The seventh and highest lake requires a hike but rewards with pristine beauty.</p>

<h4>Hike Details:</h4>
<ul>
    <li><strong>Distance:</strong> 1.5km one way from road</li>
    <li><strong>Elevation Gain:</strong> 260 meters</li>
    <li><strong>Time:</strong> 45-60 minutes up, 30-40 minutes down</li>
    <li><strong>Difficulty:</strong> Moderate - rocky path, some steep sections</li>
    <li><strong>Final Elevation:</strong> 2,400 meters</li>
</ul>

<h4>What Makes It Special:</h4>
<ul>
    <li><strong>Name Meaning:</strong> "Thousand Springs" - fed by countless mountain springs</li>
    <li><strong>Color:</strong> Crystal clear turquoise</li>
    <li><strong>Setting:</strong> Completely surrounded by jagged peaks</li>
    <li><strong>Wildlife:</strong> Better chance of seeing marmots, eagles</li>
    <li><strong>Solitude:</strong> Fewer visitors reach this lake</li>
</ul>

<p><strong>Not Included in Standard Tour:</strong> Most groups don\'t have time for this hike. If interested, inform guide in advance so we can adjust timing. Adds 2-2.5 hours to day.</p>

<p><strong>Requirements:</strong> Good fitness, proper hiking shoes, warm layer for summit. Guide assesses conditions and your capability.</p>',
                'type' => 'stop',
                'default_start_time' => '14:50',
                'duration_minutes' => 120,
                'sort_order' => 10
            ],
            [
                'title' => 'Return Journey - Descent Through the Lakes',
                'description' => '<p><strong>Different perspective!</strong> Descending offers new views - afternoon light changes lake colors dramatically.</p>

<p><strong>Return Highlights:</strong></p>
<ul>
    <li>Golden hour light on mountains (late afternoon)</li>
    <li>Brief stops for last photos if desired</li>
    <li>Notice how lakes look different going down</li>
    <li>Possible shepherd encounters - chance to see traditional life</li>
    <li>Watch sunset colors on peaks (timing permitting)</li>
</ul>

<p>Many guests nap or reflect on the incredible day. Guide shares more stories about Tajikistan, answers questions about culture and life.</p>',
                'type' => 'stop',
                'default_start_time' => '14:50',
                'duration_minutes' => 90,
                'sort_order' => 11
            ],
            [
                'title' => 'Return to Penjikent',
                'description' => '<p>Descend from mountains back to Penjikent, watching the landscapes transition from alpine to valley.</p>
<p>Last views of the Fann Mountains as sun sets. If time permits, quick refreshment stop in Penjikent.</p>',
                'type' => 'stop',
                'default_start_time' => '16:20',
                'duration_minutes' => 50,
                'sort_order' => 12
            ],
            [
                'title' => 'Border Crossing Back to Uzbekistan',
                'description' => '<p><strong>Return through Jartepa border.</strong> Usually faster in evening with less traffic.</p>

<p>Say farewell to your Tajik guide and driver. Walk through border to Uzbek side where your original driver is waiting.</p>

<p>Passport checked, exit stamp from Tajikistan, entry back to Uzbekistan. Our Uzbek team coordinates pickup timing perfectly.</p>',
                'type' => 'stop',
                'default_start_time' => '17:10',
                'duration_minutes' => 40,
                'sort_order' => 13
            ],
            [
                'title' => 'Drive Back to Samarkand',
                'description' => '<p>Final leg of this epic journey - return to Samarkand in evening.</p>

<p><strong>Arrival around 6:30-7:00 PM</strong> depending on border wait time and traffic.</p>

<p><strong>Memories Made:</strong></p>
<ul>
    <li>Crossed an international border</li>
    <li>Visited a 5,500-year-old UNESCO site</li>
    <li>Saw seven different colored mountain lakes</li>
    <li>Experienced Tajik culture and cuisine</li>
    <li>Hiked in the Fann Mountains</li>
    <li>Crossed elevations from 800m to 2,400m+</li>
</ul>

<p>Drop-off at your hotel with plenty of incredible photos and stories to share!</p>',
                'type' => 'stop',
                'default_start_time' => '17:50',
                'duration_minutes' => 70,
                'sort_order' => 14
            ]
        ];

        foreach ($itinerary as $item) {
            $tour->itineraryItems()->create($item);
        }

        // Update city tour count
        $tourCount = $samarkand->tours()->where('is_active', true)->count();
        $samarkand->tour_count_cache = $tourCount;
        $samarkand->save();

        $this->info("✅ Seven Lakes Tajikistan tour created successfully!");
        $this->info("Tour ID: {$tour->id}");
        $this->info("Title: {$tour->title}");
        $this->info("URL: http://127.0.0.1:8000/tours/{$tour->slug}");
        $this->info("Itinerary items: " . $tour->itineraryItems()->count());
        $this->info("Samarkand tour count updated: {$tourCount}");

        return 0;
    }
}
