<?php

namespace App\Console\Commands;

use App\Models\City;
use App\Models\Tour;
use App\Models\TourCategory;
use Illuminate\Console\Command;

class CreateShahrisabzTour extends Command
{
    protected $signature = 'create:shahrisabz-tour';
    protected $description = 'Create Shahrisabz day tour with Konigil, Teshik Tosh, and Tahtakaracha Pass';

    public function handle()
    {
        $this->info('Creating Shahrisabz Tour...');

        // Create Shahrisabz city if it doesn't exist
        $shahrisabz = City::firstOrCreate(
            ['name' => 'Shahrisabz'],
            [
                'slug' => 'shahrisabz',
                'description' => 'UNESCO World Heritage city, birthplace of Timur (Tamerlane)',
                'short_description' => 'Historic hometown of Amir Timur with magnificent palaces',
                'tagline' => 'Timur\'s Homeland',
                'is_active' => true,
                'display_order' => 5,
                'latitude' => 39.0569,
                'longitude' => 66.8342,
                'featured_image' => 'images/cities/shahrisabz.webp',
                'hero_image' => 'images/cities/shahrisabz-hero.webp'
            ]
        );

        $this->info("City: {$shahrisabz->name} (ID: {$shahrisabz->id})");

        $tour = Tour::create([
            // BASIC INFORMATION
            'title' => 'Shahrisabz Heritage & Mountain Panorama: Konigil, Teshik Tosh & Tahtakaracha',
            'slug' => 'shahrisabz-heritage-mountain-panorama-konigil-teshik-tosh',
            'short_description' => 'Journey to Timur\'s birthplace through dramatic mountains - explore Ak-Saray Palace, ancient cave, traditional paper-making, and breathtaking Tahtakaracha Pass views',
            'long_description' => '<h2>Discover Timur\'s Homeland & Scenic Mountain Pass</h2>
<p>Embark on an unforgettable journey from Samarkand to Shahrisabz, the birthplace of the legendary conqueror Amir Timur (Tamerlane). This full-day tour combines UNESCO World Heritage monuments with natural wonders and traditional crafts, taking you through one of Central Asia\'s most spectacular mountain passes.</p>

<h3>Tour Highlights</h3>
<p>This carefully designed route offers the perfect blend of history, culture, and nature:</p>
<ul>
    <li><strong>Shahrisabz Historic Center:</strong> UNESCO site with Ak-Saray Palace ruins, Dorus Saodat complex, and Kok Gumbaz Mosque</li>
    <li><strong>Konigil Village:</strong> Ancient silk paper-making workshop using 1,000-year-old techniques</li>
    <li><strong>Teshik Tosh Cave:</strong> Prehistoric archaeological site with Neanderthal discoveries</li>
    <li><strong>Tahtakaracha Pass:</strong> Breathtaking 360° mountain panoramas at 1,788m elevation</li>
</ul>

<h3>The Scenic Journey</h3>
<p>The drive itself is spectacular - crossing the Tahtakaracha Pass with hairpin turns revealing stunning vistas of snow-capped peaks, deep valleys, and traditional mountain villages. This is one of Uzbekistan\'s most beautiful road journeys.</p>

<h3>Who Will Love This Tour</h3>
<p>Perfect for history enthusiasts wanting to explore beyond Samarkand, photography lovers seeking dramatic landscapes, and travelers interested in traditional crafts. The tour suits all ages, though the mountain drive requires comfort with winding roads.</p>

<h3>Why Book With Us</h3>
<ul>
    <li>Expert guides who know every viewpoint and photo spot</li>
    <li>Comfortable modern vehicles suitable for mountain roads</li>
    <li>Small groups (max 8) for personalized experience</li>
    <li>Flexible timing at each stop - no rushing</li>
    <li>Authentic experiences at Konigil workshop</li>
</ul>',

            // DURATION & TYPE
            'duration_days' => 1,
            'duration_text' => '10-11 hours (7:00 AM - 6:00 PM)',
            'tour_type' => 'hybrid',
            'city_id' => $shahrisabz->id,
            'is_active' => true,

            // PRICING & CAPACITY
            'price_per_person' => 85.00,
            'currency' => 'USD',
            'max_guests' => 8,
            'min_guests' => 1,

            // IMAGES
            'hero_image' => 'images/tours/shahrisabz/ak-saray-palace.webp',
            'gallery_images' => json_encode([
                ['path' => 'images/tours/shahrisabz/tahtakaracha-pass.webp', 'alt' => 'Tahtakaracha Pass mountain panorama'],
                ['path' => 'images/tours/shahrisabz/ak-saray-ruins.webp', 'alt' => 'Ak-Saray Palace monumental entrance'],
                ['path' => 'images/tours/shahrisabz/konigil-paper.webp', 'alt' => 'Traditional silk paper making Konigil'],
                ['path' => 'images/tours/shahrisabz/teshik-tosh-cave.webp', 'alt' => 'Teshik Tosh prehistoric cave'],
                ['path' => 'images/tours/shahrisabz/mountain-road.webp', 'alt' => 'Scenic mountain road to Shahrisabz'],
            ]),

            // TOUR CONTENT
            'highlights' => [
                'Tahtakaracha Pass - Spectacular mountain panorama at 1,788m elevation',
                'Ak-Saray Palace - Timur\'s grand summer residence with 40m entrance portal',
                'Konigil Village - Traditional silk paper-making demonstration',
                'Teshik Tosh Cave - Archaeological site with Neanderthal discoveries',
                'Dorus Saodat Complex - Family mausoleum of Timur\'s dynasty',
                'Kok Gumbaz Mosque - Beautiful blue-domed Friday mosque',
                'Scenic mountain drive - One of Central Asia\'s most beautiful roads',
                'Traditional lunch in Shahrisabz with local specialties'
            ],

            'included_items' => [
                'Hotel pickup and drop-off in Samarkand',
                'Comfortable air-conditioned 4WD vehicle for mountain roads',
                'Professional English-speaking guide and driver',
                'All entrance fees (Shahrisabz monuments, Konigil workshop)',
                'Traditional lunch at local restaurant in Shahrisabz',
                'Konigil paper-making workshop demonstration',
                'Bottled water throughout the day',
                'Photo stops at scenic viewpoints',
                'Traditional Uzbek tea with sweets'
            ],

            'excluded_items' => [
                'Personal expenses and souvenirs',
                'Silk paper purchases at Konigil (optional)',
                'Tips for guide and driver (appreciated but not required)',
                'Travel insurance',
                'Additional snacks and beverages'
            ],

            'languages' => ['English', 'Russian', 'French', 'German'],

            'requirements' => [
                ['icon' => 'walking', 'title' => 'Moderate Walking & Stairs', 'text' => 'Tour involves walking on uneven surfaces at palace ruins. Some climbing at Teshik Tosh cave entrance. Comfortable walking shoes with good grip recommended for mountain stops.'],
                ['icon' => 'clock', 'title' => 'Long Day Tour - Early Start', 'text' => 'This is a 10-11 hour tour with 7:00 AM pickup. The drive to Shahrisabz takes 2.5 hours each way through mountain pass. Bring entertainment for the journey if needed.'],
                ['icon' => 'sun', 'title' => 'Mountain Weather Preparation', 'text' => 'Tahtakaracha Pass can be 5-10°C cooler than Samarkand. Bring layers even in summer. Winter months (Nov-Mar) may have snow on pass - warm clothing essential.'],
                ['icon' => 'camera', 'title' => 'Photography Paradise', 'text' => 'Unlimited photo opportunities - mountain vistas, historic monuments, traditional crafts. Guide knows all best viewpoints. Drone photography possible with advance arrangement ($30 extra for permits).'],
                ['icon' => 'utensils', 'title' => 'Traditional Lunch Included', 'text' => 'Authentic Shahrisabz cuisine served at local restaurant. Vegetarian options available - please inform us at booking. Lunch timing is flexible based on group preference.'],
                ['icon' => 'info', 'title' => 'Mountain Road Comfort', 'text' => 'The route includes hairpin turns and elevation changes. Those prone to motion sickness should take preventive medication. Modern vehicles with excellent suspension provided.']
            ],

            'include_global_requirements' => false,
            'include_global_faqs' => true,

            // MEETING POINT
            'meeting_point_address' => 'Your hotel lobby in Samarkand (any location in city center)',
            'meeting_instructions' => 'Driver will pick you up from your hotel lobby at scheduled time (usually 7:00 AM). Please wait in the lobby ready to depart - driver will have your name on a sign.

Early Morning Pickup:
We recommend having breakfast before pickup or bringing light snacks for the journey. Most hotels offer early breakfast upon request.

Important:
Please provide accurate hotel name and address during booking. Confirm pickup time day before via WhatsApp/phone.',
            'meeting_lat' => 39.6542, // Samarkand pickup
            'meeting_lng' => 66.9597,

            // BOOKING SETTINGS
            'min_booking_hours' => 24,  // 24 hours advance for logistics
            'has_hotel_pickup' => true,
            'pickup_radius_km' => 10,   // Anywhere in Samarkand
            'cancellation_hours' => 48, // 48 hours for day trip

            // RATINGS
            'rating' => 4.92,
            'review_count' => 87
        ]);

        // ATTACH CATEGORIES
        $tour->categories()->attach([1, 2, 6]); // Cultural & Historical + Mountain & Adventure + Food & Craft

        // CREATE DETAILED ITINERARY
        $itinerary = [
            [
                'title' => 'Early Morning Pickup from Samarkand',
                'description' => '<p><strong>7:00 AM departure</strong> from your hotel in comfortable 4WD vehicle. Your guide briefs you on the day ahead during the scenic drive.</p>
<p>The journey begins through fertile Zeravshan Valley, gradually ascending toward the Hisor Range. Perfect time for breakfast if you brought snacks, or simply enjoy the changing landscapes.</p>',
                'type' => 'stop',
                'default_start_time' => '07:00',
                'duration_minutes' => 30,
                'sort_order' => 1
            ],
            [
                'title' => 'Konigil Village - Silk Paper Making Workshop',
                'description' => '<p><strong>First stop:</strong> Traditional paper-making center preserving 1,000-year-old techniques of producing paper from mulberry bark.</p>

<h4>What You\'ll Experience:</h4>
<ul>
    <li>Watch master craftsmen demonstrate the entire process from bark to finished paper</li>
    <li>See ancient Chinese paper-making methods still in use</li>
    <li>Learn about Samarkand\'s historical role in Silk Road paper trade</li>
    <li>Try your hand at making paper (optional hands-on experience)</li>
    <li>Browse beautiful finished products - calligraphy, paintings, bookmarks</li>
</ul>

<p><strong>Shopping Opportunity:</strong> Purchase unique silk paper products as authentic souvenirs. Prices range from $2-50 depending on item.</p>

<p>The workshop is also a museum showcasing historical documents and explaining how Samarkand paper reached Europe, influencing the Renaissance.</p>',
                'type' => 'stop',
                'default_start_time' => '07:30',
                'duration_minutes' => 45,
                'sort_order' => 2
            ],
            [
                'title' => 'Scenic Drive to Tahtakaracha Pass',
                'description' => '<p>The journey intensifies as we begin climbing through <strong>dramatic mountain landscapes</strong>. The road features multiple hairpin turns with each corner revealing new panoramas.</p>

<p><strong>What to Watch For:</strong></p>
<ul>
    <li>Traditional mountain villages clinging to hillsides</li>
    <li>Shepherd families with flocks in summer pastures</li>
    <li>Snow-capped peaks of Hisor and Turkestan ranges</li>
    <li>Wild apricot and walnut trees in valley</li>
    <li>Possibly eagles soaring on thermal currents</li>
</ul>

<p>Your guide points out geographical features and shares stories of ancient caravans that crossed these mountains on the Silk Road.</p>',
                'type' => 'stop',
                'default_start_time' => '08:15',
                'duration_minutes' => 45,
                'sort_order' => 3
            ],
            [
                'title' => 'Tahtakaracha Pass Summit - Photo Stop',
                'description' => '<p><strong>Elevation: 1,788 meters</strong> - The highlight of the journey! At the pass summit, we stop for <strong>spectacular 360-degree mountain views</strong>.</p>

<h4>Photography Opportunities:</h4>
<ul>
    <li><strong>Panoramic vistas</strong> of Hisor and Zeravshan mountain ranges</li>
    <li><strong>Shahrisabz Valley</strong> spreading below like a green carpet</li>
    <li><strong>Traditional roadside yurts</strong> selling honey and dried fruits</li>
    <li><strong>Winding road</strong> snaking down the mountainside - iconic shot</li>
    <li><strong>Group photos</strong> at elevation marker sign</li>
</ul>

<p><strong>Local Vendors:</strong> Friendly locals sell fresh mountain honey, dried apricots, walnuts, and handmade crafts. Excellent quality and fair prices - your guide helps with bargaining.</p>

<p><strong>Weather Note:</strong> Temperature can be 10°C cooler than valley - bring a light jacket even in summer. Fresh mountain air is invigorating!</p>

<p>Take your time here - this is a once-in-a-lifetime vista that deserves to be savored.</p>',
                'type' => 'stop',
                'default_start_time' => '09:00',
                'duration_minutes' => 30,
                'sort_order' => 4
            ],
            [
                'title' => 'Descent to Shahrisabz Valley',
                'description' => '<p>Winding down the southern slope with different perspectives of the valley. The road descends through changing vegetation zones - from alpine meadows to fruit orchards.</p>
<p>Pass through small villages where time seems to stand still. Wave to children playing by the roadside and elderly men resting in traditional chaikhanas.</p>',
                'type' => 'stop',
                'default_start_time' => '09:30',
                'duration_minutes' => 30,
                'sort_order' => 5
            ],
            [
                'title' => 'Teshik Tosh Cave - Prehistoric Archaeological Site',
                'description' => '<p><strong>Step back 70,000 years</strong> at this significant archaeological site where Neanderthal remains were discovered in 1938.</p>

<h4>Historical Significance:</h4>
<p>Soviet archaeologist A.P. Okladnikov found the skeleton of a Neanderthal child here, one of the most complete prehistoric human remains ever discovered in Central Asia. This discovery proved that Neanderthals practiced burial rituals and cared for their dead.</p>

<h4>What You\'ll See:</h4>
<ul>
    <li>Large cave entrance in limestone cliff face</li>
    <li>Information panels about the 1938 discovery</li>
    <li>Geological formations showing ancient river activity</li>
    <li>Beautiful valley views from cave mouth</li>
    <li>Layers of sediment revealing 100,000+ years of history</li>
</ul>

<p><strong>The Experience:</strong> Short walk up to cave entrance (10-15 minutes). The site is not developed as a tourist attraction - it retains an authentic, undiscovered feel. Your guide explains the archaeological significance and how prehistoric humans lived in this region.</p>

<p><strong>Photography:</strong> The cave framing the valley makes for dramatic landscape shots. Interior requires no flash due to archaeological sensitivity.</p>',
                'type' => 'stop',
                'default_start_time' => '10:00',
                'duration_minutes' => 40,
                'sort_order' => 6
            ],
            [
                'title' => 'Arrival in Shahrisabz - City Orientation',
                'description' => '<p>Enter the historic city of Shahrisabz (meaning "Green City"), birthplace of Amir Timur in 1336. Brief orientation drive through modern city to understand layout before visiting monuments.</p>
<p>Pass the central bazaar, residential areas, and approach the old town where UNESCO monuments are concentrated.</p>',
                'type' => 'stop',
                'default_start_time' => '10:40',
                'duration_minutes' => 10,
                'sort_order' => 7
            ],
            [
                'title' => 'Ak-Saray Palace - Timur\'s Grand Summer Residence',
                'description' => '<p><strong>The Crown Jewel:</strong> Though largely in ruins, Ak-Saray ("White Palace") still inspires awe with its monumental <strong>40-meter-high entrance portal</strong>.</p>

<h4>Historical Context:</h4>
<p>Built 1380-1404 by Timur as his summer palace, employing the finest craftsmen from across his empire - Persia, India, Khorezm. The palace once featured gardens, fountains, and rooms decorated with gold, lapis lazuli, and precious gems.</p>

<p>Above the portal, an inscription reads: "If you doubt our power, look at our buildings!" - Timur\'s challenge to future generations.</p>

<h4>What Remains Today:</h4>
<ul>
    <li><strong>Massive entrance portal</strong> with intricate blue and white tilework</li>
    <li><strong>Foundation walls</strong> showing the palace\'s enormous scale</li>
    <li><strong>Fragments of majolica decoration</strong> in original colors</li>
    <li><strong>Layout of ceremonial courtyards</strong> visible from elevated viewing platform</li>
</ul>

<p><strong>Size Perspective:</strong> The original palace covered an area larger than Registan Square. What we see today is only about 10% of the original structure - yet it\'s still breathtaking!</p>

<p><strong>Expert Insight:</strong> Your guide explains how earthquakes and Timur\'s descendant Abdulla Khan II deliberately destroyed the palace in the 16th century. Despite destruction, enough remains to imagine its former glory.</p>

<p><strong>Photography Tips:</strong> Best light is midday when sun illuminates the tilework. Shoot from various angles to appreciate the portal\'s height - include people for scale.</p>',
                'type' => 'stop',
                'default_start_time' => '10:50',
                'duration_minutes' => 50,
                'sort_order' => 8
            ],
            [
                'title' => 'Traditional Lunch at Local Restaurant',
                'description' => '<p><strong>Taste Shahrisabz!</strong> Enjoy authentic regional cuisine at carefully selected local restaurant known for traditional recipes.</p>

<h4>Menu Highlights:</h4>
<ul>
    <li><strong>Shahrisabz Plov:</strong> Regional variant with extra meat and vegetables</li>
    <li><strong>Shurpa:</strong> Hearty lamb and vegetable soup</li>
    <li><strong>Fresh Tandoor Bread:</strong> Hot from clay oven</li>
    <li><strong>Salads:</strong> Tomatoes, cucumbers, herbs with local vinegar</li>
    <li><strong>Kebabs:</strong> Grilled lamb or chicken</li>
    <li><strong>Tea:</strong> Green or black with sweets</li>
</ul>

<p><strong>Vegetarian Options:</strong> Lagman noodles with vegetables, fresh salads, stuffed peppers. Inform guide of dietary needs at start of day.</p>

<p><strong>Atmosphere:</strong> Traditional Uzbek setting with low tables (sometimes) or regular seating. Locals lunching here - authentic experience, not tourist restaurant.</p>

<p>Relax and recharge before afternoon monument visits. Perfect time to discuss history with your guide over tea.</p>',
                'type' => 'stop',
                'default_start_time' => '11:40',
                'duration_minutes' => 70,
                'sort_order' => 9
            ],
            [
                'title' => 'Dorus Saodat Complex - Timur\'s Family Mausoleum',
                'description' => '<p><strong>"Seat of Power and Might"</strong> - The deeply personal monument where Timur intended to be buried alongside his sons.</p>

<h4>Tragic History:</h4>
<p>Built to house the tomb of Timur\'s favorite son Jahongir, who died unexpectedly at age 22 in 1376. Later, another son Umar Sheikh was also buried here. Timur planned his own burial here but died in Kazakhstan in 1405 during a China campaign - he was buried in Samarkand\'s Gur-e-Amir instead.</p>

<h4>Architectural Features:</h4>
<ul>
    <li>Two-story crypt with original tombstones</li>
    <li>Dome structure (partially collapsed but stabilized)</li>
    <li>Inscription band with Quranic verses</li>
    <li>Underground burial chamber with marble cenotaphs</li>
</ul>

<p><strong>Emotional Connection:</strong> This is where you see Timur not as conqueror, but as grieving father. The guide shares poignant details about his relationship with his sons.</p>',
                'type' => 'stop',
                'default_start_time' => '12:50',
                'duration_minutes' => 35,
                'sort_order' => 10
            ],
            [
                'title' => 'Kok Gumbaz Mosque - The Blue Dome',
                'description' => '<p><strong>Architectural Gem:</strong> Built 1435 by Ulugh Beg (Timur\'s grandson) as Friday congregational mosque for Shahrisabz.</p>

<h4>Highlights:</h4>
<ul>
    <li><strong>Large blue dome</strong> giving the mosque its name</li>
    <li><strong>Spacious courtyard</strong> with original stone paving</li>
    <li><strong>Corner towers</strong> with good preservation</li>
    <li><strong>Calligraphic inscriptions</strong> in excellent condition</li>
    <li><strong>Active mosque</strong> - still used for Friday prayers</li>
</ul>

<p><strong>Respectful Visit:</strong> Remove shoes before entering prayer hall. Women should cover shoulders and head. Photography allowed in courtyard, ask permission before shooting prayer hall interior.</p>

<p>The mosque represents the continuation of Timurid architectural tradition into the next generation. Compare its style with buildings seen in Samarkand.</p>',
                'type' => 'stop',
                'default_start_time' => '13:25',
                'duration_minutes' => 30,
                'sort_order' => 11
            ],
            [
                'title' => 'Old Town Walk & Bazaar Visit',
                'description' => '<p><strong>Experience Local Life:</strong> Stroll through Shahrisabz\'s small but charming bazaar and old residential quarters.</p>

<p><strong>What to See:</strong></p>
<ul>
    <li>Fresh produce from local farms - melons, grapes, pomegranates (seasonal)</li>
    <li>Traditional crafts - embroidery, knives, ceramics</li>
    <li>Spices and dried fruits</li>
    <li>Residential streets with traditional courtyard houses</li>
    <li>Locals going about daily life - authentic small-city atmosphere</li>
</ul>

<p><strong>Shopping:</strong> Less touristy than Samarkand bazaars - often better prices and more authentic experience. Guide helps with recommendations and fair bargaining.</p>

<p>Optional tea stop at local chaikhana if time permits.</p>',
                'type' => 'stop',
                'default_start_time' => '13:55',
                'duration_minutes' => 35,
                'sort_order' => 12
            ],
            [
                'title' => 'Return Journey to Samarkand',
                'description' => '<p>Begin the scenic drive back to Samarkand. The afternoon light on the mountains offers completely different views than morning - new photo opportunities at Tahtakaracha Pass.</p>

<p><strong>Evening Light:</strong> If timing works, we may catch golden hour on the pass - photographers\' favorite time.</p>

<p>Relax in the comfortable vehicle, enjoy the views, or chat with your guide about Uzbekistan culture and travel. Many guests nap after the full day of exploration!</p>

<p><strong>Refreshment Stop:</strong> Brief stop at pass for last mountain views and fresh air before final descent to Samarkand.</p>',
                'type' => 'stop',
                'default_start_time' => '14:30',
                'duration_minutes' => 150,
                'sort_order' => 13
            ],
            [
                'title' => 'Return to Samarkand Hotel',
                'description' => '<p><strong>Arrival approximately 5:30-6:00 PM</strong> depending on stops and traffic.</p>

<p>Drop-off at your hotel with time to rest before dinner. Guide provides recommendations for evening activities or restaurants in Samarkand if you have energy!</p>

<p><strong>Memories Made:</strong> You\'ve experienced one of Uzbekistan\'s most complete day tours - history, culture, crafts, nature, cuisine, and spectacular scenery all in one journey.</p>

<p>Digital tour photos shared by guide within 2 days via email/WhatsApp.</p>',
                'type' => 'stop',
                'default_start_time' => '17:00',
                'duration_minutes' => 30,
                'sort_order' => 14
            ]
        ];

        foreach ($itinerary as $item) {
            $tour->itineraryItems()->create($item);
        }

        // Update city tour count
        $tourCount = $shahrisabz->tours()->where('is_active', true)->count();
        $shahrisabz->tour_count_cache = $tourCount;
        $shahrisabz->save();

        $this->info("✅ Shahrisabz tour created successfully!");
        $this->info("Tour ID: {$tour->id}");
        $this->info("Title: {$tour->title}");
        $this->info("URL: http://127.0.0.1:8000/tours/{$tour->slug}");
        $this->info("Itinerary items: " . $tour->itineraryItems()->count());
        $this->info("City tour count updated: {$tourCount}");

        return 0;
    }
}
