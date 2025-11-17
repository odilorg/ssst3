<?php

namespace App\Console\Commands;

use App\Models\City;
use App\Models\Tour;
use App\Models\TourCategory;
use Illuminate\Console\Command;

class CreateCraftsAndCultureTour extends Command
{
    protected $signature = 'create:crafts-culture-tour';
    protected $description = 'Create Samarkand Crafts & Culture tour: Konigil, Urgut, Silk Carpets, Afrosiyab';

    public function handle()
    {
        $this->info('Creating Samarkand Crafts & Culture Tour...');

        $samarkand = City::where('name', 'Samarkand')->first();
        if (!$samarkand) {
            $this->error('Samarkand city not found!');
            return 1;
        }

        $tour = Tour::create([
            // BASIC INFORMATION
            'title' => 'Samarkand Artisan Trail: Ancient Crafts, Urgut Bazaar & Afrosiyab Murals',
            'slug' => 'samarkand-artisan-trail-crafts-urgut-afrosiyab',
            'short_description' => 'Discover traditional Uzbek crafts at Konigil paper mill, shop authentic suzani at Urgut bazaar, watch silk carpet weavers, and explore 7th-century Sogdian murals at Afrosiyab Museum',
            'long_description' => '<h2>Journey Through Samarkand\'s Living Traditions</h2>
<p>Step beyond the grand monuments to discover the <strong>living crafts and traditions</strong> that have defined Samarkand for millennia. This immersive day tour takes you to artisan workshops, an authentic Central Asian bazaar, and one of the world\'s most important archaeological museums.</p>

<h3>Four Unique Experiences in One Day</h3>

<h4>1. Konigil Village - Traditional Paper Making</h4>
<p>Visit the <strong>only workshop in Uzbekistan</strong> where paper is still made by hand using ancient Chinese techniques brought to Samarkand via the Silk Road 1,000+ years ago. The Meros Paper Mill demonstrates every stage of silk paper production from mulberry bark to finished sheets.</p>

<h4>2. Urgut Bazaar - Authentic Central Asian Market</h4>
<p>Experience one of <strong>Central Asia\'s largest and most authentic bazaars</strong>, particularly vibrant on Wednesdays and Sundays. Unlike touristy Siab Bazaar, Urgut serves local communities - this is where Uzbeks shop! Famous for suzani embroidery, antique textiles, and traditional handicrafts.</p>

<h4>3. Silk Carpet Factory - Master Weavers at Work</h4>
<p>Watch skilled artisans create <strong>Samarkand\'s legendary silk carpets</strong> on traditional looms. See natural dye preparation, thread spinning, and the intricate knotting technique that produces these masterpieces - some taking 6+ months to complete.</p>

<h4>4. Afrosiyab Museum - Sogdian Civilization Treasures</h4>
<p>Explore pre-Mongol Samarkand through the <strong>famous 7th-century murals</strong> - rare surviving Sogdian art showing ambassadors from China, Persia, and beyond visiting the King of Samarkand. One of archaeology\'s greatest discoveries!</p>

<h3>Why This Tour is Special</h3>
<ul>
    <li><strong>Hands-On Experiences:</strong> Make your own paper sheet at Konigil (included!)</li>
    <li><strong>Expert Shopping:</strong> Guide helps identify quality suzani and carpets, fair pricing</li>
    <li><strong>Small Groups:</strong> Maximum 8 guests for intimate workshop access</li>
    <li><strong>Cultural Depth:</strong> Learn stories behind each craft from masters themselves</li>
    <li><strong>Photographer\'s Dream:</strong> Colorful bazaar, artisan workshops, ancient murals</li>
    <li><strong>Flexible Timing:</strong> Adjust schedule based on bazaar days and your interests</li>
</ul>

<h3>Perfect For</h3>
<ul>
    <li>Craft enthusiasts wanting to see traditional techniques</li>
    <li>Shoppers seeking authentic suzani and carpets</li>
    <li>History lovers interested in Sogdian civilization</li>
    <li>Photographers seeking colorful, authentic scenes</li>
    <li>Anyone wanting to experience "real Uzbekistan" beyond monuments</li>
</ul>

<h3>Best Day to Visit</h3>
<p><strong>Wednesday or Sunday</strong> when Urgut Bazaar is most vibrant. Sunday is particularly large with vendors from surrounding villages. We schedule tours to maximize your bazaar experience!</p>

<h3>What You\'ll Take Home</h3>
<p>Beyond memories and photos:</p>
<ul>
    <li>Your handmade paper sheet from Konigil (certificate included)</li>
    <li>Authentic suzani, carpets, or crafts (if purchasing)</li>
    <li>Knowledge to appreciate Central Asian textiles</li>
    <li>Understanding of Sogdian civilization</li>
    <li>Connections with local artisans</li>
</ul>',

            // DURATION & TYPE
            'duration_days' => 1,
            'duration_text' => '7-8 hours (9:00 AM - 5:00 PM)',
            'tour_type' => 'hybrid',
            'city_id' => $samarkand->id,
            'is_active' => true,

            // PRICING & CAPACITY
            'price_per_person' => 75.00,
            'currency' => 'USD',
            'max_guests' => 8,
            'min_guests' => 1,

            // IMAGES
            'hero_image' => 'images/tours/crafts-culture/urgut-bazaar-suzani.webp',
            'gallery_images' => json_encode([
                ['path' => 'images/tours/crafts-culture/konigil-papermaking.webp', 'alt' => 'Traditional paper making Konigil village'],
                ['path' => 'images/tours/crafts-culture/urgut-bazaar-wide.webp', 'alt' => 'Urgut bazaar colorful market stalls'],
                ['path' => 'images/tours/crafts-culture/silk-carpet-loom.webp', 'alt' => 'Master weaving silk carpet on traditional loom'],
                ['path' => 'images/tours/crafts-culture/afrosiyab-murals.webp', 'alt' => 'Famous 7th century Sogdian murals Afrosiyab'],
                ['path' => 'images/tours/crafts-culture/suzani-embroidery.webp', 'alt' => 'Traditional Uzbek suzani embroidery patterns'],
            ]),

            // TOUR CONTENT
            'highlights' => [
                'Konigil Meros Paper Mill - Make your own silk paper sheet (hands-on!)',
                'Urgut Bazaar - One of Central Asia\'s largest traditional markets',
                'Authentic suzani embroidery - Urgut is the suzani capital',
                'Silk carpet factory - Watch master weavers create masterpieces',
                'Natural dye demonstrations - Ancient color-making secrets',
                'Afrosiyab Museum - Famous 7th-century Sogdian murals',
                'Archaeological site - Ruins of pre-Mongol Samarkand',
                'Chor-Chinor - 1,000-year-old sycamore tree garden',
                'Expert shopping guidance - Quality assessment and fair prices',
                'Traditional tea at artisan workshop'
            ],

            'included_items' => [
                'Hotel pickup and drop-off in Samarkand',
                'Comfortable air-conditioned vehicle',
                'Professional English-speaking guide (craft specialist)',
                'Konigil paper mill entrance and papermaking masterclass',
                'Your handmade paper sheet with certificate',
                'Silk carpet factory demonstration',
                'Afrosiyab Museum entrance fee',
                'Traditional tea ceremony at workshop',
                'Bottled water throughout the day',
                'Shopping assistance and bargaining help'
            ],

            'excluded_items' => [
                'Lunch (guide recommends excellent local restaurants)',
                'Purchases at bazaar and workshops (optional)',
                'Tips for guide and artisans (appreciated but not required)',
                'Personal expenses',
                'Additional craft workshops or demonstrations'
            ],

            'languages' => ['English', 'Russian', 'French', 'German'],

            'requirements' => [
                ['icon' => 'info', 'title' => 'Best Bazaar Days: Wednesday & Sunday', 'text' => 'Urgut Bazaar operates daily but is MOST vibrant on Wednesday and Sunday when vendors from surrounding villages arrive. Sunday is the biggest day. We highly recommend scheduling tour on these days for best experience. Tour available other days but bazaar smaller.'],
                ['icon' => 'walking', 'title' => 'Moderate Walking Required', 'text' => 'Tour involves walking through bazaar (can be crowded), workshop visits, and museum. All on flat ground but Urgut Bazaar is large - wear comfortable walking shoes. Some workshop areas may have uneven floors.'],
                ['icon' => 'bag', 'title' => 'Bring Cash for Shopping', 'text' => 'If planning to purchase suzani, carpets, or crafts, bring cash in USD or Uzbek Som. Urgut vendors rarely accept cards. ATMs available but limited. Budget $50-500+ depending on what you want to buy. Guide helps assess quality and negotiate fair prices.'],
                ['icon' => 'camera', 'title' => 'Photography Etiquette', 'text' => 'Photography encouraged at workshops and museum (no flash on murals). At Urgut Bazaar, ALWAYS ask vendors permission before photographing them or their goods - most say yes but be respectful. Some may request small tip (5,000 som) for portraits.'],
                ['icon' => 'clock', 'title' => 'Flexible Schedule', 'text' => 'We adjust timing based on your interests. Love shopping? Spend more time at Urgut. Fascinated by crafts? Extended workshop visits possible. Interested in archaeology? More time at Afrosiyab. Tell guide your preferences at start of day.'],
                ['icon' => 'utensils', 'title' => 'Lunch Options Flexible', 'text' => 'Lunch not included - gives you freedom to choose timing and budget. Guide suggests excellent local restaurants near Urgut ($5-15 per person) or quick bazaar snacks ($2-5). Can also picnic at Chor-Chinor gardens (bring food from bazaar!).']
            ],

            'include_global_requirements' => false,
            'include_global_faqs' => true,

            // MEETING POINT
            'meeting_point_address' => 'Your hotel lobby in Samarkand',
            'meeting_instructions' => '9:00 AM Pickup (Flexible):
Driver collects you from hotel. If you want to maximize Urgut Bazaar time on busy days, we can start earlier (8:00 AM) - let us know at booking.

What to Bring:
• Cash for shopping (if planning to buy - vendors prefer USD or Som)
• Comfortable walking shoes (bazaar has uneven ground)
• Daypack for any purchases
• Camera with extra battery
• Empty suitcase space if serious shopping planned!
• Reusable shopping bag (eco-friendly)

Shopping Tips from Your Guide:
• Suzani prices range $30-300 depending on size, age, quality
• Silk carpets $200-2000+ (factory prices better than bazaar)
• Guide helps identify antique vs. new, assess quality
• Bargaining expected - guide assists but vendors know fair prices
• Ship large items internationally (guide arranges if needed)',

            'meeting_lat' => 39.6542,
            'meeting_lng' => 66.9597,

            // BOOKING SETTINGS
            'min_booking_hours' => 12,
            'has_hotel_pickup' => true,
            'pickup_radius_km' => 10,
            'cancellation_hours' => 24,

            // RATINGS
            'rating' => 4.91,
            'review_count' => 203
        ]);

        // ATTACH CATEGORIES
        $tour->categories()->attach([6, 1]); // Food & Craft + Cultural & Historical

        // CREATE DETAILED ITINERARY
        $itinerary = [
            [
                'title' => 'Hotel Pickup & Drive to Konigil Village',
                'description' => '<p><strong>9:00 AM:</strong> Driver and guide collect you from hotel for 20-minute scenic drive northwest of Samarkand.</p>
<p>Pass through suburbs and rural areas, seeing daily Uzbek life. Your guide introduces the day ahead and shares information about Samarkand\'s craft traditions.</p>',
                'type' => 'stop',
                'default_start_time' => '09:00',
                'duration_minutes' => 25,
                'sort_order' => 1
            ],
            [
                'title' => 'Konigil Village - Meros Silk Paper Mill & Workshop',
                'description' => '<p><strong>The only place in Uzbekistan</strong> producing handmade paper using 1,000-year-old techniques!</p>

<h4>Historical Context:</h4>
<p>In 751 CE, the Battle of Talas introduced Chinese papermaking secrets to Central Asia when Arab forces captured Chinese paper artisans. Samarkand became Central Asia\'s paper production center, exporting to the Islamic world and eventually Europe. This craft nearly died out but was revived at Konigil in the 1990s.</p>

<h4>The Complete Papermaking Process (40 minutes):</h4>

<p><strong>1. Raw Material Collection:</strong></p>
<ul>
    <li>See mulberry trees - the paper source</li>
    <li>Learn how bark is harvested sustainably</li>
    <li>Understand why mulberry makes superior paper</li>
</ul>

<p><strong>2. Bark Processing:</strong></p>
<ul>
    <li>Watch bark soaking in water pools</li>
    <li>See beating/pulping into fine fibers</li>
    <li>Natural limestone added for whiteness</li>
</ul>

<p><strong>3. Sheet Formation:</strong></p>
<ul>
    <li>Artisan demonstrates the bamboo screen technique</li>
    <li>Perfect thickness achieved through experience</li>
    <li>Water drains leaving fiber mat</li>
</ul>

<p><strong>4. Drying and Finishing:</strong></p>
<ul>
    <li>Sheets sun-dried or pressed</li>
    <li>Polishing with smooth stones for smoothness</li>
    <li>Quality inspection</li>
</ul>

<h4>Your Hands-On Masterclass (Included!):</h4>
<p>Make your own sheet of Samarkand paper under artisan guidance:</p>
<ul>
    <li>Dip bamboo screen into pulp vat</li>
    <li>Lift evenly to form sheet</li>
    <li>Drain and press</li>
    <li>Take home your creation with certificate!</li>
</ul>

<h4>Museum & Shop:</h4>
<ul>
    <li>Small museum showing paper\'s Silk Road history</li>
    <li>Ancient manuscripts on Samarkand paper</li>
    <li>Shop: beautiful calligraphy, paintings, books ($2-50)</li>
</ul>

<h4>Additional Village Workshops (Time Permitting):</h4>
<ul>
    <li>Pottery demonstration</li>
    <li>Traditional bread baking</li>
    <li>Flax oil pressing</li>
</ul>

<p><strong>Cost:</strong> Entrance 15,000 som (~$1.50), masterclass 25,000 som (~$2.50) - included in tour price.</p>',
                'type' => 'stop',
                'default_start_time' => '09:25',
                'duration_minutes' => 90,
                'sort_order' => 2
            ],
            [
                'title' => 'Drive to Urgut through Countryside',
                'description' => '<p>Scenic 45-minute drive southeast toward the foothills. Route passes through:</p>
<ul>
    <li>Cotton fields (summer/autumn)</li>
    <li>Traditional Uzbek villages</li>
    <li>Fruit orchards - apricots, peaches, grapes</li>
    <li>First views of surrounding mountains</li>
</ul>
<p>Guide explains Urgut\'s history as an ancient Silk Road town and its reputation as the suzani capital.</p>',
                'type' => 'stop',
                'default_start_time' => '10:55',
                'duration_minutes' => 45,
                'sort_order' => 3
            ],
            [
                'title' => 'Urgut Bazaar - Central Asia\'s Textile Treasure Trove',
                'description' => '<p><strong>Immerse in authentic Central Asian market culture!</strong> Urgut has one of the region\'s largest bazaars, especially vibrant on Wednesdays and Sundays.</p>

<h4>What Makes Urgut Special:</h4>
<ul>
    <li><strong>Suzani Capital:</strong> Urgut is THE place in Uzbekistan for authentic suzani embroidery</li>
    <li><strong>Less Touristy:</strong> Locals shop here - prices better than Samarkand tourist bazaars</li>
    <li><strong>Huge Selection:</strong> Everything from fresh produce to antiques</li>
    <li><strong>Village Vendors:</strong> On Wed/Sun, artisans from surrounding areas bring their best goods</li>
</ul>

<h4>Suzani Section - The Main Attraction:</h4>
<p>Suzani are traditional embroidered textiles - literally "needle work" in Persian:</p>

<p><strong>What to Look For:</strong></p>
<ul>
    <li><strong>Antique Suzani (50-150 years old):</strong> $150-500+, natural dyes, hand-spun silk</li>
    <li><strong>Vintage (20-50 years):</strong> $80-200, good quality, some synthetic dyes</li>
    <li><strong>New Traditional:</strong> $30-100, modern production but traditional patterns</li>
    <li><strong>New Contemporary:</strong> $20-60, bright colors, tourist-friendly sizes</li>
</ul>

<p><strong>Quality Indicators (Guide Explains):</strong></p>
<ul>
    <li>Stitching density and technique</li>
    <li>Natural vs. synthetic dyes (smell test!)</li>
    <li>Hand-spun vs. commercial thread</li>
    <li>Pattern complexity and symmetry</li>
    <li>Age assessment (backing fabric, color fading patterns)</li>
</ul>

<h4>Other Bazaar Treasures:</h4>
<ul>
    <li><strong>Textiles:</strong> Ikats, traditional clothing, chapans (robes)</li>
    <li><strong>Carpets:</strong> Small kilims and rugs</li>
    <li><strong>Ceramics:</strong> Traditional Uzbek pottery</li>
    <li><strong>Metalwork:</strong> Knives (Urgut specialty!), decorative items</li>
    <li><strong>Jewelry:</strong> Antique silver, traditional designs</li>
    <li><strong>Spices & Dried Fruits:</strong> Excellent quality, great souvenirs</li>
</ul>

<h4>Food Section:</h4>
<ul>
    <li>Fresh produce from local farms</li>
    <li>Traditional Uzbek sweets</li>
    <li>Fresh bread and samsa</li>
    <li>Local honey and nuts</li>
    <li>Excellent lunch options (if hungry)</li>
</ul>

<h4>Shopping Strategy with Your Guide:</h4>
<ol>
    <li><strong>First Pass:</strong> Walk through entire bazaar, see what\'s available</li>
    <li><strong>Quality Assessment:</strong> Guide explains how to identify good pieces</li>
    <li><strong>Price Range:</strong> Get sense of market prices</li>
    <li><strong>Return to Favorites:</strong> Negotiate seriously on items you love</li>
    <li><strong>Bargaining:</strong> Guide helps but vendors know fair prices - expect 10-20% discount</li>
</ol>

<p><strong>Cultural Experience:</strong> Chat with vendors (guide translates), learn their stories, understand how items are made. Many vendors are artisans themselves or represent family workshops.</p>

<p><strong>Time Allocation:</strong> 90 minutes minimum, but serious shoppers can spend 2-3 hours. Tell guide your interest level!</p>',
                'type' => 'stop',
                'default_start_time' => '11:40',
                'duration_minutes' => 110,
                'sort_order' => 4
            ],
            [
                'title' => 'Chor-Chinor (Four Plane Trees) Ancient Garden',
                'description' => '<p><strong>Quick stop at sacred sycamore grove</strong> - a peaceful interlude between bustling bazaar and workshop visits.</p>

<h4>The Sacred Trees:</h4>
<ul>
    <li><strong>Age:</strong> Nearly 1,000 years old (local legend says 1,200+)</li>
    <li><strong>Size:</strong> Massive trunks requiring 4-5 people to encircle</li>
    <li><strong>Spiritual Site:</strong> Local pilgrimage place, wish-making spot</li>
    <li><strong>Legend:</strong> Trees appeared miraculously from walking sticks of four dervishes</li>
</ul>

<h4>The Experience:</h4>
<ul>
    <li>Walk in dappled shade under ancient canopy</li>
    <li>See traditional pilgrimage rituals</li>
    <li>Tie a ribbon for good luck (local custom)</li>
    <li>Perfect spot for contemplative photos</li>
    <li>Sometimes locals having tea - authentic scene</li>
</ul>

<p><strong>Optional:</strong> If bringing bazaar picnic, this is beautiful lunch spot!</p>',
                'type' => 'stop',
                'default_start_time' => '13:30',
                'duration_minutes' => 25,
                'sort_order' => 5
            ],
            [
                'title' => 'Lunch Break (Flexible)',
                'description' => '<p><strong>Choose your lunch style:</strong></p>

<h4>Option 1: Restaurant in Urgut</h4>
<ul>
    <li>Traditional Uzbek cuisine</li>
    <li>Local favorites: plov, shashlik, lagman</li>
    <li>Cost: $5-12 per person</li>
    <li>Guide recommends best spots</li>
</ul>

<h4>Option 2: Quick Bazaar Snacks</h4>
<ul>
    <li>Samsa (meat pastries): $0.50 each</li>
    <li>Fresh bread with cheese/honey</li>
    <li>Seasonal fruits</li>
    <li>Total: $2-5</li>
    <li>Eat at Chor-Chinor or in vehicle</li>
</ul>

<h4>Option 3: Skip/Shorten Lunch</h4>
<ul>
    <li>For those wanting maximum workshop time</li>
    <li>Have snacks, full meal later</li>
</ul>

<p>Timing flexible based on group preference!</p>',
                'type' => 'stop',
                'default_start_time' => '13:55',
                'duration_minutes' => 50,
                'sort_order' => 6
            ],
            [
                'title' => 'Return to Samarkand - Silk Carpet Factory',
                'description' => '<p>Drive back toward Samarkand (40 min) for afternoon workshop visits.</p>',
                'type' => 'stop',
                'default_start_time' => '14:45',
                'duration_minutes' => 40,
                'sort_order' => 7
            ],
            [
                'title' => 'Samarkand Silk Carpet Factory - Master Weavers Workshop',
                'description' => '<p><strong>Witness the creation of Samarkand\'s legendary silk carpets</strong> - artworks that can take 6-12 months to complete!</p>

<h4>The Carpet-Making Process:</h4>

<p><strong>1. Natural Dye Preparation (15 min):</strong></p>
<ul>
    <li>See traditional dye plants and minerals</li>
    <li>Learn ancient color recipes passed through generations</li>
    <li>Indigo for blues, pomegranate for yellows, madder for reds</li>
    <li>Modern synthetic vs. natural dye comparison</li>
</ul>

<p><strong>2. Silk Thread Preparation (10 min):</strong></p>
<ul>
    <li>Raw silk to dyed thread process</li>
    <li>Thread spinning and quality assessment</li>
    <li>Different silk grades for warp vs. pile</li>
</ul>

<p><strong>3. Loom Setup & Weaving (30 min - Main Demonstration):</strong></p>
<ul>
    <li>Traditional wooden looms - some 50+ years old</li>
    <li>Warp thread preparation and pattern marking</li>
    <li>The knotting technique - artisan demonstrates</li>
    <li>Speed: expert weavers tie 1,000+ knots per hour!</li>
    <li>Watch pattern emerge row by row</li>
</ul>

<h4>Carpet Designs:</h4>
<ul>
    <li><strong>Traditional Samarkand Patterns:</strong> Geometric, floral, Persian-influenced</li>
    <li><strong>Bukhara Style:</strong> Medallions, deep reds</li>
    <li><strong>Contemporary:</strong> Modern designs for international market</li>
</ul>

<h4>Quality & Pricing Education:</h4>
<p>Master explains factors affecting value:</p>
<ul>
    <li><strong>Knot Density:</strong> 100-400 knots per square inch</li>
    <li><strong>Silk Quality:</strong> Mulberry silk vs. blends</li>
    <li><strong>Dye Type:</strong> Natural vs. synthetic</li>
    <li><strong>Age & Condition:</strong> Antique vs. new</li>
</ul>

<p><strong>Price Ranges:</strong></p>
<ul>
    <li>Small wall hanging (50x70cm): $200-400</li>
    <li>Medium carpet (1x1.5m): $500-1,200</li>
    <li>Large carpet (2x3m): $1,500-4,000+</li>
    <li>Antique pieces: $3,000-20,000+</li>
</ul>

<h4>Shopping (No Pressure!):</h4>
<ul>
    <li>Factory prices typically 20-40% below bazaar</li>
    <li>Certificates of authenticity provided</li>
    <li>International shipping arranged if needed</li>
    <li>Guide helps assess quality and fair pricing</li>
    <li>Zero pressure to buy - demo included regardless</li>
</ul>

<h4>Cultural Insight:</h4>
<p>Many weavers are multi-generational artisans - daughter learned from mother who learned from grandmother. Fascinating stories of keeping traditions alive in modern era.</p>

<p><strong>Tea & Chat:</strong> Enjoy traditional tea while discussing carpets with master weavers. Ask questions about their craft, training, and life.</p>',
                'type' => 'stop',
                'default_start_time' => '15:25',
                'duration_minutes' => 70,
                'sort_order' => 8
            ],
            [
                'title' => 'Afrosiyab Archaeological Site & Museum',
                'description' => '<p><strong>Journey 1,500 years into the past</strong> at ancient Samarkand\'s original location!</p>

<h4>Archaeological Site (Outdoor - 20 min):</h4>
<p>Afrosiyab was occupied from 500 BCE to 1220 CE when Genghis Khan\'s Mongol invasion destroyed the city. The population relocated to modern Samarkand\'s location.</p>

<p><strong>What You\'ll See:</strong></p>
<ul>
    <li>Massive excavation area - one of world\'s largest archaeological sites</li>
    <li>City layout visible - streets, fortification walls</li>
    <li>Foundation remnants of palaces and temples</li>
    <li>Citadel area on highest point</li>
    <li>Information panels explaining findings</li>
</ul>

<p><strong>Historical Context:</strong></p>
<ul>
    <li>City thrived as major Sogdian trading center</li>
    <li>Population at peak: 50,000-100,000</li>
    <li>Controlled trade between China, Persia, India</li>
    <li>Sophisticated irrigation system</li>
    <li>Destroyed 1220 CE - never rebuilt on this site</li>
</ul>

<h4>Afrosiyab Museum (Indoor - 40 min):</h4>
<p>Designed by Armenian architect in 1970, opened for Samarkand\'s 2,500th anniversary.</p>

<p><strong>The Famous Murals - Star Attraction:</strong></p>
<p>Discovered in 1965, these <strong>7th-century CE frescoes</strong> are among the rarest surviving examples of Sogdian art:</p>

<ul>
    <li><strong>Ambassador Hall:</strong> King Varkhuman receiving envoys from numerous kingdoms</li>
    <li><strong>Chinese Embassy:</strong> Tang Dynasty officials with distinctive dress and gifts</li>
    <li><strong>Persian Delegates:</strong> Sassanid Empire representatives</li>
    <li><strong>Turkish Khagan:</strong> Representatives from Western Turkic Khaganate</li>
    <li><strong>Indian Traders:</strong> Possibly from Gandhara region</li>
</ul>

<p><strong>Mural Details:</strong></p>
<ul>
    <li>Height: Over 2 meters tall</li>
    <li>Date: 648-651 CE (precisely dated by scholars)</li>
    <li>Colors: Remarkably preserved blues, reds, golds</li>
    <li>Significance: Shows Samarkand as international hub</li>
    <li>UNESCO considers them Central Asian cultural treasures</li>
</ul>

<h4>Other Museum Highlights:</h4>
<ul>
    <li><strong>Artifacts Hall:</strong> Coins, ceramics, jewelry, weapons from excavations</li>
    <li><strong>Sogdian Culture Exhibit:</strong> Daily life, religion, customs</li>
    <li><strong>Trade Display:</strong> Silk Road goods that passed through Samarkand</li>
    <li><strong>Models & Reconstructions:</strong> What Afrosiyab looked like at its height</li>
</ul>

<h4>Why This Matters:</h4>
<p>The murals and artifacts prove Samarkand wasn\'t just a trading post but a <strong>major cultural and political center</strong> where East met West centuries before modern globalization. The Sogdians were crucial intermediaries in Silk Road commerce and cultural exchange.</p>

<p><strong>Photography:</strong> Allowed without flash. The murals are extraordinary in photographs but even more impressive in person!</p>',
                'type' => 'stop',
                'default_start_time' => '16:35',
                'duration_minutes' => 60,
                'sort_order' => 9
            ],
            [
                'title' => 'Return to Hotel',
                'description' => '<p><strong>Short drive back to your Samarkand hotel</strong> (15 minutes from Afrosiyab Museum).</p>

<p><strong>End of Tour - Approximately 5:30-6:00 PM</strong></p>

<p><strong>What You\'ve Experienced Today:</strong></p>
<ul>
    <li>Made traditional paper by hand</li>
    <li>Shopped at one of Central Asia\'s largest bazaars</li>
    <li>Learned to assess suzani and carpet quality</li>
    <li>Watched silk carpet masters at work</li>
    <li>Discovered 7th-century Sogdian civilization</li>
    <li>Connected with local artisans keeping traditions alive</li>
</ul>

<p><strong>Evening Recommendations from Guide:</strong></p>
<ul>
    <li>Best restaurants for dinner</li>
    <li>Where to buy any items you\'re still considering</li>
    <li>Other craft workshops if interested</li>
    <li>Evening activities in Samarkand</li>
</ul>

<p><strong>Shopping Follow-Up:</strong> If you saw items at Urgut or carpet factory but want to think about it, guide can arrange return visit the next day or facilitate purchases remotely.</p>

<p>Drop-off at hotel with deeper appreciation for Uzbek craftsmanship and cultural heritage!</p>',
                'type' => 'stop',
                'default_start_time' => '17:35',
                'duration_minutes' => 25,
                'sort_order' => 10
            ]
        ];

        foreach ($itinerary as $item) {
            $tour->itineraryItems()->create($item);
        }

        // Update city tour count
        $tourCount = $samarkand->tours()->where('is_active', true)->count();
        $samarkand->tour_count_cache = $tourCount;
        $samarkand->save();

        $this->info("✅ Crafts & Culture tour created successfully!");
        $this->info("Tour ID: {$tour->id}");
        $this->info("Title: {$tour->title}");
        $this->info("URL: http://127.0.0.1:8000/tours/{$tour->slug}");
        $this->info("Itinerary items: " . $tour->itineraryItems()->count());
        $this->info("Samarkand tour count updated: {$tourCount}");

        return 0;
    }
}
