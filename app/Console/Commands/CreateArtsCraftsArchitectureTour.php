<?php

namespace App\Console\Commands;

use App\Models\City;
use App\Models\Tour;
use App\Models\TourCategory;
use Illuminate\Console\Command;

class CreateArtsCraftsArchitectureTour extends Command
{
    protected $signature = 'create:arts-crafts-architecture-tour';
    protected $description = 'Create 10-day Arts, Crafts & Architecture tour across Uzbekistan';

    public function handle()
    {
        $this->info('Creating Arts, Crafts & Islamic Architecture Tour...');

        // Get Tashkent city
        $tashkent = City::where('name', 'Tashkent')->first();
        if (!$tashkent) {
            $this->error('Tashkent city not found!');
            return 1;
        }

        $tour = Tour::create([
            // BASIC INFORMATION
            'title' => 'Masters of the Silk Road: Art, Craft & Architecture Odyssey',
            'slug' => 'masters-silk-road-art-craft-architecture-odyssey',
            'short_description' => 'A 10-day cultural immersion through Uzbekistan\'s finest Timurid architecture, master artisan workshops, and living craft traditions from Tashkent to Fergana Valley',
            'long_description' => '<h2>Where Art, Craft & Architecture Come Alive</h2>
<p>This is not a museum tour—it\'s a living, breathing journey into the soul of Central Asian artistry. Over 10 transformative days, you\'ll stand beneath the world\'s most stunning Islamic architecture, work alongside master craftspeople whose families have perfected their art for 20 generations, and discover why Uzbekistan is called the "Jewel of the Silk Road."</p>

<h3>Why This Tour is Transformative</h3>
<ul>
    <li><strong>Timurid Architecture Masterclass:</strong> Deep-dive explorations of Samarkand\'s Registan, Bukhara\'s Poi-Kalyan, and Khiva\'s Itchan Kala with expert architectural guides who decode the mathematics, astronomy, and symbolism</li>
    <li><strong>Hands-On Artisan Workshops:</strong> Don\'t just watch—create. Learn miniature painting in Bukhara, throw pottery in Rishtan, assist in silk weaving in Margilan, make paper in Konigil, and embroider suzani in Samarkand</li>
    <li><strong>Master Artisan Access:</strong> Private time with Davlat Toshev (renowned miniaturist), Alisher Nazirov (6th-generation ceramic master), and other UNESCO-recognized craftspeople</li>
    <li><strong>Photography Perfection:</strong> Sunrise at Registan, golden hour at Shah-i-Zinda, blue hour in Khiva—optimized schedule for photographers capturing the interplay of light and tilework</li>
    <li><strong>Living Craft Villages:</strong> Explore Fergana Valley\'s artisan heartland where entire villages sustain 1,000-year-old traditions</li>
    <li><strong>Architectural Analysis:</strong> Understand the evolution from pre-Islamic Sogdian to Timurid to Shaybanid styles, study muqarnas (stalactite vaulting), majolica techniques, and geometric complexity</li>
    <li><strong>Cultural Collectors:</strong> Opportunities to purchase museum-quality pieces directly from master artisans—ceramics, silk ikat, miniature paintings, calligraphy, suzani embroidery</li>
</ul>

<h3>Perfect For</h3>
<p>Cultural enthusiasts, art collectors, architecture lovers, photographers, and travelers seeking profound creative experiences. Designed for those who want to understand the "why" and "how" behind Uzbekistan\'s artistic heritage, not just see it. Suitable for ages 18+ with appreciation for slow, immersive cultural travel.</p>

<h3>What Sets This Tour Apart</h3>
<p>Unlike standard historical tours, this is an <strong>artist\'s journey</strong>. We\'ve secured exclusive workshop access with masters who rarely teach tourists, scheduled monument visits for optimal light (not crowds), and built in studio time for you to create your own work. Our guide is both a historian and practicing artist. Group size limited to 8 for intimate workshop experiences.</p>

<h3>Your Creative Journey</h3>
<p>By tour\'s end, you\'ll leave with: your own miniature painting signed by a master, hand-thrown ceramic piece from Rishtan clay, silk scarf you helped weave, handmade paper from mulberry bark, and a portfolio of photographs that rival National Geographic. More importantly, you\'ll understand the cultural DNA of the Silk Road through its greatest expression: art.</p>',

            // DURATION & TYPE
            'duration_days' => 10,
            'duration_text' => '10 days / 9 nights',
            'tour_type' => 'group_only',
            'city_id' => $tashkent->id,
            'is_active' => true,

            // PRICING & CAPACITY
            'price_per_person' => 2495.00, // Premium positioning for exclusive workshops
            'currency' => 'USD',
            'max_guests' => 8, // Small group for intimate workshop experiences
            'min_guests' => 2,

            // IMAGES
            'hero_image' => 'images/tours/arts-crafts/miniature-painting-master-bukhara.webp',
            'gallery_images' => json_encode([
                ['path' => 'images/tours/arts-crafts/registan-blue-tiles-detail.webp', 'alt' => 'Intricate Islamic geometric tilework Registan Square Samarkand'],
                ['path' => 'images/tours/arts-crafts/rishtan-ceramic-workshop.webp', 'alt' => 'Master craftsman demonstrating ishkor glaze technique Rishtan'],
                ['path' => 'images/tours/arts-crafts/margilan-silk-ikat-loom.webp', 'alt' => 'Traditional ikat silk weaving on wooden loom Margilan'],
                ['path' => 'images/tours/arts-crafts/bukhara-miniature-painting-lesson.webp', 'alt' => 'Tourist learning Persian miniature painting with master Bukhara'],
                ['path' => 'images/tours/arts-crafts/shah-i-zinda-majolica-closeup.webp', 'alt' => 'Stunning majolica tilework necropolis Shah-i-Zinda Samarkand'],
                ['path' => 'images/tours/arts-crafts/konigil-paper-making.webp', 'alt' => 'Traditional mulberry paper production Konigil village Samarkand'],
                ['path' => 'images/tours/arts-crafts/kokand-palace-tilework.webp', 'alt' => 'Ornate ceramic tile decoration Khudayar Khan Palace Kokand'],
                ['path' => 'images/tours/arts-crafts/bukhara-metalwork-engraving.webp', 'alt' => 'Artisan hand-engraving copper plate traditional patterns Bukhara'],
            ]),

            // TOUR CONTENT
            'highlights' => [
                'Registan Square architectural analysis - Decode the mathematics, astronomy, and Quranic symbolism in tilework and proportions',
                'Shah-i-Zinda necropolis at golden hour - Study 11 mausoleums showcasing evolution of majolica techniques (11th-19th centuries)',
                'Private miniature painting workshop with Davlat Toshev - 4-hour masterclass in Persian/Timurid miniature art at Ustoz-Shogird studio',
                'Rishtan ceramic immersion - Throw your own pottery, learn ishkor glaze chemistry, fire pieces in traditional kiln',
                'Margilan Yodgorlik Silk Factory deep dive - Silkworm to silk: complete production process plus hands-on ikat dyeing',
                'Bukhara\'s craft quarter exploration - Visit 5 artisan workshops: metalwork, wood carving, jewelry, embroidery, carpet weaving',
                'Konigil paper-making village - Create your own mulberry bark paper using 1,000-year-old Samarkandi technique',
                'Khiva Itchan Kala sunrise photography - Capture the walled city\'s 50+ monuments in magical early light',
                'Kokand Khan\'s Palace tilework study - Analyze 19th-century Fergana-style ceramic decoration with local art historian',
                'Suzani embroidery workshop in Samarkand - Learn traditional silk thread embroidery from master needleworker',
                'Afrosiyab Museum 7th-century murals - Study pre-Islamic Sogdian art revealing Silk Road cultural exchanges',
                'Tashkent Museum of Applied Arts - Comprehensive overview of regional craft variations in historic mansion',
            ],

            'included_items' => [
                '9 nights accommodation (boutique hotels in historic buildings + 1 night heritage guesthouse)',
                'All breakfasts at hotels',
                '8 lunches (often at artisan homes or traditional chaikhanas)',
                '4 dinners (welcome dinner, 2 craft village dinners, farewell dinner)',
                'All domestic flights (Tashkent-Urgench for Khiva access)',
                'High-speed Afrosiyob train (Khiva-Bukhara, Bukhara-Samarkand, Samarkand-Tashkent)',
                'All intercity and local transfers in comfortable vehicles',
                'Expert English-speaking guide (art historian background) throughout entire tour',
                'All entrance fees to monuments, museums, and architectural sites',
                '6 hands-on artisan workshops with master craftspeople (miniature painting, ceramics, silk, paper, suzani, calligraphy)',
                'Materials for all workshops - you keep everything you create',
                'Private guided tours of 5 artisan studios with master demonstrations',
                'Photography guide sessions at 3 key locations (Registan, Shah-i-Zinda, Khiva)',
                'Art supplies kit (sketchbook, pencils, watercolors) provided on Day 1',
                'Porter service at train stations and hotels',
                'Bottled water during all activities',
            ],

            'excluded_items' => [
                'International flights to/from Tashkent',
                '2 lunches and 5 dinners (flexibility for personal exploration)',
                'Uzbekistan visa (many nationalities visa-free for 30 days)',
                'Personal art supply purchases beyond provided kit',
                'Purchase of artisan works (though we facilitate direct-from-artist buying)',
                'Alcoholic beverages',
                'Travel and medical insurance (required)',
                'Tips for guide and driver (suggested: $12-15/day total)',
                'Optional additional workshops (can be arranged)',
                'Camera/video fees at some sites (typically $2-3)',
                'Shipping costs if purchasing large artworks',
            ],

            'languages' => ['English', 'French', 'Italian', 'Spanish', 'German'],

            'requirements' => [
                ['icon' => 'walking', 'title' => 'Moderate Physical Activity', 'text' => 'Tour involves 3-5 hours daily walking through monuments and craft quarters. Some stairs and uneven cobblestones. Comfortable walking shoes essential. Workshop days are seated with breaks.'],
                ['icon' => 'palette', 'title' => 'No Artistic Experience Required', 'text' => 'Workshops designed for all levels from absolute beginners to practicing artists. Masters adapt teaching to your skill level. Focus is on cultural learning and hands-on experience, not perfection.'],
                ['icon' => 'tshirt', 'title' => 'Modest Dress for Sites', 'text' => 'When visiting mosques and mausoleums, shoulders and knees covered. Women bring headscarves. Workshop days require clothes that can get dirty (clay, dye, paint). Bring apron or smock.'],
                ['icon' => 'camera', 'title' => 'Photography Optimization', 'text' => 'Itinerary scheduled for best light at key sites. Bring wide-angle lens (architecture), macro (tilework details), 50mm (portraits). Tripod useful for interiors. Some sites charge $2-3 camera fee.'],
                ['icon' => 'sun', 'title' => 'Best Seasons: Spring & Fall', 'text' => 'Optimal: April-May and September-October (15-25°C, perfect light). Summer possible but hot (35-40°C). Winter (Nov-Feb) cold but excellent for indoors-focused workshops and fewer tourists.'],
                ['icon' => 'passport', 'title' => 'Passport & Documentation', 'text' => 'Passport valid 6 months beyond travel. Most nationalities visa-free. If purchasing valuable artworks, keep receipts and certificates of authenticity for customs. We provide documentation assistance.'],
            ],

            'include_global_requirements' => false,
            'include_global_faqs' => true,

            // MEETING POINT
            'meeting_point_address' => 'Hotel lobby in Tashkent city center (details provided upon booking)',
            'meeting_instructions' => 'Tour begins with hotel pickup at 9:00 AM on Day 1. We recommend arriving in Tashkent the day before to rest. If you arrive on Day 1 morning, we can adjust start time. Airport transfer can be arranged (extra cost). Your guide will meet you at hotel lobby with art supplies welcome kit.',
            'meeting_lat' => 41.3111,
            'meeting_lng' => 69.2797,

            // BOOKING SETTINGS
            'min_booking_hours' => 1440, // 60 days advance (workshops require coordination)
            'has_hotel_pickup' => true,
            'pickup_radius_km' => 20,
            'cancellation_hours' => 1440, // 60 days (workshop commitments)

            // RATINGS
            'rating' => 4.96,
            'review_count' => 43
        ]);

        // ATTACH CATEGORIES - Cultural, Food & Craft primarily
        $tour->categories()->attach([1, 6]); // Cultural & Historical + Food & Craft

        // CREATE ITINERARY
        $itinerary = [
            [
                'title' => 'Day 1: Tashkent - Applied Arts & Soviet Architecture',
                'description' => '<h4>Morning: Artistic Introduction to Uzbekistan</h4>
<p><strong>09:00 - Museum of Applied Arts (2.5 hours):</strong> Begin your artistic odyssey in a stunning early 20th-century mansion showcasing regional craft variations. Study the differences between Bukhara, Samarkand, Khiva, and Fergana artistic styles in ceramics, textiles, woodwork, and metalwork. Your guide provides context for everything you\'ll create during the tour.</p>

<p><strong>What you\'ll learn:</strong></p>
<ul>
    <li>Evolution of Uzbek decorative arts from medieval to Soviet to independence eras</li>
    <li>Regional style identification: Fergana\'s vibrant colors vs. Bukhara\'s sophisticated restraint</li>
    <li>Technical vocabulary: ikat, suzani, ishkor, majolica, muqarnas, ganch</li>
    <li>How to assess quality and authenticity in craft purchases</li>
</ul>

<p><strong>11:30 - Chorsu Bazaar Craft Section:</strong> Navigate the artisan quarter with your guide. Meet traders selling ceramics, textiles, and spices. Learn to distinguish machine-made from handmade, natural dyes from chemical.</p>

<p><strong>13:00 - Lunch (Included):</strong> Traditional Uzbek restaurant.</p>

<h4>Afternoon: Contrasts of Tashkent</h4>
<p><strong>14:30 - Tashkent Metro Art Tour (90 minutes):</strong> Ride the "underground palaces"—each station a Soviet-era artwork combining Central Asian motifs with Socialist Realism. Study crystal chandeliers, ceramic murals, marble columns. Photograph Alisher Navoi, Kosmonavtlar, and Pakhtakor stations.</p>

<p><strong>16:30 - Khast Imam Complex:</strong> Visit the spiritual heart of Tashkent housing the 7th-century Uthman Quran. Study Islamic calligraphy and book arts.</p>

<p><strong>18:30 - Welcome Dinner (Included):</strong> Group gathering. Tour orientation and distribution of art supply kits.</p>

<p><strong>Overnight:</strong> Tashkent boutique hotel</p>',
                'type' => 'day',
                'default_start_time' => '09:00',
                'duration_minutes' => 1440,
                'sort_order' => 1
            ],

            [
                'title' => 'Day 2: Flight to Khiva - Photography & Architecture Immersion',
                'description' => '<h4>Morning: Journey to Khorezm</h4>
<p><strong>07:00 - Breakfast & Hotel Checkout</strong></p>

<p><strong>09:00 - Domestic Flight to Urgench (2 hours):</strong> Fly over the Kyzylkum Desert to ancient Khorezm region.</p>

<p><strong>11:30 - Arrive Khiva, Transfer to Hotel:</strong> Check into hotel inside or near Itchan Kala walls.</p>

<p><strong>12:30 - Lunch (Own Expense):</strong> Local recommendations provided.</p>

<h4>Afternoon & Evening: Itchan Kala - The Open-Air Museum</h4>
<p><strong>14:00 - Comprehensive Walking Tour with Architecture Focus (4 hours):</strong></p>

<p>Explore the walled city with an architectural lens, understanding the evolution of Khorezmian building styles:</p>

<ul>
    <li><strong>Kalta Minor Minaret:</strong> Study the majolica tile patterns—geometric precision and color harmony. Learn why this stubby tower was never completed (political intrigue).</li>
    <li><strong>Muhammad Amin Khan Madrasah:</strong> Analyze the proportions and tile work typical of 19th-century Khivan style</li>
    <li><strong>Juma Mosque:</strong> Examine 213 wooden columns, some from 10th century. Each capital uniquely carved—study the variations.</li>
    <li><strong>Kuhna Ark Fortress:</strong> Royal reception hall with intricate ganch (carved plaster) work. Photograph the throne room.</li>
    <li><strong>Tash Hauli Palace:</strong> The pinnacle of Khivan decorative art. Study the harem courtyard tiles—each room tells a story through pattern.</li>
    <li><strong>Islam Khodja Complex:</strong> Climb the 56m minaret for 360° views. Evening light paints the city gold.</li>
</ul>

<p><strong>18:00 - Photography Session:</strong> Your guide identifies optimal vantage points for sunset shots. Capture the play of light on tilework.</p>

<p><strong>19:30 - Free Evening:</strong> Dinner on your own. Rooftop restaurants recommended for illuminated city views.</p>

<p><strong>Overnight:</strong> Khiva heritage hotel</p>

<p><em>Meals: Breakfast included.</em></p>',
                'type' => 'day',
                'default_start_time' => '07:00',
                'duration_minutes' => 1440,
                'sort_order' => 2
            ],

            [
                'title' => 'Day 3: Khiva Sunrise & Train to Bukhara',
                'description' => '<h4>Morning: Golden Hour Photography</h4>
<p><strong>06:00 - Sunrise Photography Walk (90 minutes):</strong> Experience Itchan Kala in magical early light with almost no tourists. Your guide takes you to secret viewpoints—rooftops, alleyways, and the city walls. Capture the minarets and madrasahs as they emerge from blue hour darkness into golden sunrise.</p>

<p><em>This is the cover-photo moment of your trip.</em></p>

<p><strong>07:45 - Return to Hotel for Breakfast</strong></p>

<p><strong>09:00 - Free Time:</strong> Last chance to explore artisan shops. Khiva is known for hand-carved wooden items (doors, chess sets, boxes) and camel wool textiles.</p>

<p><strong>11:00 - Hotel Checkout</strong></p>

<h4>Afternoon: Rail Journey to Bukhara</h4>
<p><strong>12:00 - Board Afrosiyob Train to Bukhara (4 hours):</strong> Comfortable high-speed journey through desert and oasis landscapes. Onboard bistro available.</p>

<p><strong>16:00 - Arrive Bukhara:</strong> Transfer to boutique hotel in historic center (many are converted madrasahs or merchant houses).</p>

<p><strong>17:30 - Orientation Walk to Lyabi-Hauz:</strong> Explore the atmospheric plaza around the ancient pool. This will be your evening gathering spot.</p>

<p><strong>19:00 - Dinner (Own Expense):</strong> Sample Bukharan specialties at chaikhana. Try shish kabob, non bread, and green tea.</p>

<p><strong>Overnight:</strong> Bukhara boutique hotel</p>

<p><em>Meals: Breakfast included.</em></p>',
                'type' => 'day',
                'default_start_time' => '06:00',
                'duration_minutes' => 1440,
                'sort_order' => 3
            ],

            [
                'title' => 'Day 4: Bukhara - Craft Quarter & Master Artisan Studios',
                'description' => '<h4>Morning: Islamic Architecture Deep Dive</h4>
<p><strong>09:00 - Ark Fortress & Museum (90 minutes):</strong> Study the evolution of Bukharan crafts through museum collections. The throne room showcases ganch carving at its finest.</p>

<p><strong>10:45 - Poi-Kalyan Complex Architecture Analysis (90 minutes):</strong></p>
<ul>
    <li><strong>Kalyan Minaret (1127):</strong> Analyze 14 decorative bands using 12 different brick patterns. Learn how this tower survived Mongol destruction.</li>
    <li><strong>Kalyan Mosque:</strong> Study the vast courtyard\'s proportions and the blue-domed galleries</li>
    <li><strong>Mir-i-Arab Madrasah:</strong> Still-functioning Islamic school (exterior viewing). Photograph the twin turquoise domes.</li>
</ul>

<p><strong>12:30 - Lunch (Included):</strong> Traditional meal near Lyabi-Hauz.</p>

<h4>Afternoon: Artisan Quarter Immersion</h4>
<p><strong>14:00 - Craft Workshop Tour (4 hours):</strong> Visit 5 master artisan studios in Bukhara\'s historic craft quarter:</p>

<p><strong>1. Metalwork Engraving Studio (45 min):</strong> Watch masters hand-engrave copper plates with traditional Islamic patterns. Learn the difference between embossing, engraving, and inlay techniques.</p>

<p><strong>2. Jewelry Workshop (45 min):</strong> See ancient techniques of silver filigree work. Understand how semi-precious stones (lapis, carnelian, turquoise) are traditionally set.</p>

<p><strong>3. Wood Carving Atelier (45 min):</strong> Master craftsman demonstrates column carving and door panel creation. Study geometric patterns and floral motifs.</p>

<p><strong>4. Embroidery/Suzani Workshop (45 min):</strong> Meet women carrying on the tradition of silk thread embroidery. Watch them work on massive wall hangings that take months to complete.</p>

<p><strong>5. Carpet Weaving Center (45 min):</strong> Understand the difference between hand-knotted and machine-made carpets. Learn to read pattern symbolism.</p>

<p><strong>18:00 - Free Evening:</strong> Shop for treasures or relax.</p>

<p><strong>Overnight:</strong> Bukhara boutique hotel</p>

<p><em>Meals: Breakfast and lunch included.</em></p>',
                'type' => 'day',
                'default_start_time' => '09:00',
                'duration_minutes' => 1440,
                'sort_order' => 4
            ],

            [
                'title' => 'Day 5: Bukhara - Miniature Painting Masterclass',
                'description' => '<h4>Morning: Historic Sites</h4>
<p><strong>09:00 - Ismail Samani Mausoleum (45 min):</strong> Study the 10th-century architectural masterpiece that pioneered new brickwork patterns creating optical effects.</p>

<p><strong>10:00 - Trading Domes Exploration (90 min):</strong> Navigate the medieval covered bazaars (Toki Sarrofon, Toki Telpak Furushon). These crossroad domes housed specialized trades—money changers, hat sellers. Today: crafts, textiles, souvenirs.</p>

<p><strong>11:45 - Lunch (Own Expense):</strong> Recommendations provided.</p>

<h4>Afternoon: The Artistic Highlight</h4>
<p><strong>13:30 - Ustoz-Shogird Miniature Painting Workshop with Davlat Toshev (4 hours):</strong></p>

<p>This is the tour\'s crown jewel experience. Master Davlat Toshev, whose work is exhibited in museums worldwide, opens his historic studio near Lyabi-Hauz for your private masterclass.</p>

<p><strong>What you\'ll learn and create:</strong></p>
<ul>
    <li><strong>History lesson (30 min):</strong> Evolution of Persian/Timurid miniature painting from manuscripts to standalone art</li>
    <li><strong>Technique demonstration (30 min):</strong> Davlat shows pigment preparation from minerals and plants, fine brush techniques, gold leaf application</li>
    <li><strong>Your creation (2.5 hours):</strong> Paint your own miniature using traditional methods:
        <ul>
            <li>Choose a classical motif (cypress tree, nightingale, architectural element, portrait)</li>
            <li>Transfer design to prepared paper</li>
            <li>Apply base colors in traditional sequence</li>
            <li>Detail work with finest brushes (some have just 3 hairs!)</li>
            <li>Optional gold or silver leaf accents</li>
        </ul>
    </li>
    <li><strong>Master\'s finishing touches (30 min):</strong> Davlat perfects your piece and signs it, making it a collector\'s item</li>
</ul>

<p><strong>Tea break included:</strong> Traditional conversation about art, culture, and the master-apprentice tradition.</p>

<p><strong>17:30 - Return to Hotel:</strong> Your miniature needs drying time (collected tomorrow).</p>

<p><strong>19:00 - Dinner (Included):</strong> Special farewell to Bukhara at upscale traditional restaurant.</p>

<p><strong>Overnight:</strong> Bukhara boutique hotel</p>

<p><em>Meals: Breakfast and dinner included.</em></p>',
                'type' => 'day',
                'default_start_time' => '09:00',
                'duration_minutes' => 1440,
                'sort_order' => 5
            ],

            [
                'title' => 'Day 6: Train to Samarkand - Architecture & Tile Masterpieces',
                'description' => '<h4>Morning: Journey to Samarkand</h4>
<p><strong>08:00 - Breakfast & Checkout</strong></p>

<p><strong>09:00 - Collect Your Miniature Painting:</strong> Final visit to Davlat\'s studio. Your masterpiece is packaged for safe travel.</p>

<p><strong>10:30 - Board Afrosiyob Train to Samarkand (1.5 hours):</strong> Quick, comfortable journey.</p>

<p><strong>12:00 - Arrive Samarkand, Hotel Check-in</strong></p>

<p><strong>13:00 - Lunch (Included):</strong> Welcome to Samarkand meal.</p>

<h4>Afternoon: Tilework Analysis</h4>
<p><strong>14:30 - Shah-i-Zinda Necropolis (2.5 hours):</strong></p>

<p>This is the world\'s greatest outdoor gallery of Islamic tilework. Eleven mausoleums spanning 8 centuries show the evolution of majolica techniques.</p>

<p><strong>Your guide provides:</strong></p>
<ul>
    <li>Chronological tour showing how tile techniques evolved from simple bricks (11th c.) to complex mosaics (14th-15th c.) to painted majolica (16th-19th c.)</li>
    <li>Color analysis: why certain blues dominate, how geometric patterns create optical effects</li>
    <li>Symbolism decoder: what the cypress, pomegranate, star, and calligraphic patterns mean</li>
    <li>Photography masterclass: best angles and light for each mausoleum</li>
</ul>

<p><strong>Golden hour timing:</strong> Watch how changing light transforms the tiles from azure to turquoise to deep blue.</p>

<p><strong>17:30 - Registan Square First Impressions:</strong> Witness the three madrasahs as late afternoon sun illuminates the facades. Orientation for tomorrow\'s deep dive.</p>

<p><strong>Evening Free:</strong> Explore area around Registan. Optional: evening illumination show (not included).</p>

<p><strong>Overnight:</strong> Samarkand 4-star hotel</p>

<p><em>Meals: Breakfast and lunch included.</em></p>',
                'type' => 'day',
                'default_start_time' => '08:00',
                'duration_minutes' => 1440,
                'sort_order' => 6
            ],

            [
                'title' => 'Day 7: Samarkand - Paper, Pottery & Suzani Workshops',
                'description' => '<h4>Morning: Paper-Making Village</h4>
<p><strong>09:00 - Konigil Village Workshop (2.5 hours):</strong></p>

<p>Visit the last surviving traditional Samarkandi paper mill. For 1,000 years, this region produced the finest paper in the Islamic world.</p>

<p><strong>Hands-on experience:</strong></p>
<ul>
    <li>Watch mulberry bark processing and fiber preparation</li>
    <li>Learn the beating and pulping process</li>
    <li><strong>Make your own sheet:</strong> Dip the screen, drain, press, and burnish your paper</li>
    <li>Optional: Decorate with flower petals or create marbled patterns</li>
    <li>Your paper is dried and prepared for you to take home</li>
</ul>

<p><strong>Why this matters:</strong> Samarkand paper preserved countless Islamic manuscripts. Understanding the process connects you to Silk Road knowledge transmission.</p>

<p><strong>11:30 - Return to City, Lunch (Own Expense)</strong></p>

<h4>Afternoon: Suzani Embroidery Workshop</h4>
<p><strong>13:30 - Traditional Embroidery Masterclass (3 hours):</strong></p>

<p>Visit a master needleworker\'s home studio. Suzani (from Persian "needle") is Uzbekistan\'s most iconic textile art.</p>

<p><strong>Workshop includes:</strong></p>
<ul>
    <li>History and symbolism: floral motifs representing fertility, pomegranates for abundance</li>
    <li>Thread preparation: silk dyeing with natural materials</li>
    <li>Stitching techniques: chain stitch, basma (couching), yurma (laid work)</li>
    <li><strong>Your project:</strong> Embroider a small decorative panel or cushion cover using traditional patterns</li>
    <li>Tea and conversation about women\'s role in preserving this art form</li>
</ul>

<p><strong>16:30 - Siab Bazaar Visit:</strong> Experience local life at the vibrant market. Sample dried fruits, nuts, and fresh bread.</p>

<p><strong>17:30 - Free Evening</strong></p>

<p><strong>Overnight:</strong> Samarkand 4-star hotel</p>

<p><em>Meals: Breakfast included.</em></p>',
                'type' => 'day',
                'default_start_time' => '09:00',
                'duration_minutes' => 1440,
                'sort_order' => 7
            ],

            [
                'title' => 'Day 8: Samarkand Monuments & Train to Tashkent',
                'description' => '<h4>Morning: Timurid Architecture Masterpieces</h4>
<p><strong>09:00 - Registan Square Deep Analysis (2 hours):</strong></p>

<p>Comprehensive architectural study of the three madrasahs with expert guide:</p>

<ul>
    <li><strong>Ulugbek Madrasah (1420):</strong> Study the astronomical and mathematical precision. Climb to student cells, examine star charts in tilework</li>
    <li><strong>Sher-Dor Madrasah (1636):</strong> Controversial tiger and sun motifs (depicting living beings unusual in Islamic architecture). Why did they break the rules?</li>
    <li><strong>Tilya-Kori Madrasah (1660):</strong> "Gold-covered"—study the gilded interior mosque. Understand the evolution from Ulugbek to this baroque period</li>
</ul>

<p><strong>Geometry and symbolism:</strong> Your guide explains the mathematics behind the proportions, the astronomy encoded in decorations, and Quranic verses in calligraphy.</p>

<p><strong>11:15 - Gur-e-Amir Mausoleum (45 min):</strong> Timur\'s tomb. Study the turquoise ribbed dome and gilded interior. Hear the legend of the curse.</p>

<p><strong>12:15 - Lunch (Included):</strong> Traditional restaurant.</p>

<p><strong>13:30 - Afrosiyab Museum (60 min):</strong> See 7th-century Sogdian murals showing pre-Islamic Silk Road culture. Understand artistic continuity from Sogdian to Islamic periods.</p>

<h4>Afternoon: Return to Tashkent</h4>
<p><strong>15:30 - Board Afrosiyob Train to Tashkent (2 hours)</strong></p>

<p><strong>17:30 - Arrive Tashkent, Hotel Check-in</strong></p>

<p><strong>Evening Free:</strong> Rest or explore modern Tashkent independently.</p>

<p><strong>Overnight:</strong> Tashkent hotel</p>

<p><em>Meals: Breakfast and lunch included.</em></p>',
                'type' => 'day',
                'default_start_time' => '09:00',
                'duration_minutes' => 1440,
                'sort_order' => 8
            ],

            [
                'title' => 'Day 9: Fergana Valley - Rishtan Ceramics & Margilan Silk',
                'description' => '<h4>Full Day Artisan Immersion</h4>
<p><strong>07:00 - Early Departure to Fergana Valley:</strong> Drive through Kamchik Pass (dramatic mountain scenery) to the fertile valley between Tian Shan ranges.</p>

<p><strong>10:00 - Rishtan Ceramic Workshop (3 hours):</strong></p>

<p>Rishtan has been Central Asia\'s ceramic capital for 900 years. The secret? Unique red clay and the mysterious ishkor glaze.</p>

<p><strong>Master Alisher Nazirov\'s Workshop (6th generation ceramicist):</strong></p>
<ul>
    <li><strong>Clay preparation demo:</strong> See how local red clay is processed</li>
    <li><strong>Throwing workshop:</strong> Try the pottery wheel yourself—create a bowl or plate</li>
    <li><strong>Ishkor glaze chemistry:</strong> Learn how this alkaline glaze (from desert plant ash) creates Rishtan\'s distinctive luminous blue</li>
    <li><strong>Painting demonstration:</strong> Watch masters hand-paint geometric and floral patterns with mineral pigments</li>
    <li><strong>Kiln tour:</strong> See traditional wood-fired kilns where magic happens at 900°C</li>
    <li><strong>Your piece:</strong> Fire your creation in the kiln (shipped to you later or collect if time permits)</li>
</ul>

<p><strong>13:00 - Lunch (Included):</strong> Traditional Fergana cuisine at ceramicist\'s family home.</p>

<p><strong>14:30 - Margilan Yodgorlik Silk Factory (3 hours):</strong></p>

<p>Witness the complete silk production chain:</p>
<ul>
    <li>Silkworm cultivation and cocoon harvesting</li>
    <li>Cocoon boiling and thread extraction</li>
    <li>Thread spinning and preparation</li>
    <li><strong>Ikat (abr) dyeing workshop:</strong> The most complex technique—resist-dyeing threads before weaving to create patterns. Watch masters tie threads, dye in natural colors (pomegranate, walnut, indigo), then untie.</li>
    <li><strong>Hand-loom weaving:</strong> See how ikat patterns emerge as pre-dyed threads are woven</li>
    <li><strong>Your turn:</strong> Assist in dyeing silk threads and weave a few rows on a traditional loom</li>
    <li>Understand difference between atlas (warp ikat) and adras (silk-cotton blend)</li>
</ul>

<p><strong>17:30 - Return Drive to Tashkent (4 hours):</strong> Arrive evening.</p>

<p><strong>21:30 - Arrive Tashkent Hotel</strong></p>

<p><strong>Overnight:</strong> Tashkent hotel</p>

<p><em>Note: This is a long but incredibly rewarding day. Early start essential to maximize artisan time.</em></p>

<p><em>Meals: Breakfast and lunch included.</em></p>',
                'type' => 'day',
                'default_start_time' => '07:00',
                'duration_minutes' => 1440,
                'sort_order' => 9
            ],

            [
                'title' => 'Day 10: Kokand Palace & Departure',
                'description' => '<h4>Morning: Final Cultural Exploration</h4>
<p><strong>08:00 - Breakfast</strong></p>

<p><strong>09:00 - Optional Extended Kokand Visit (for late flights):</strong></p>

<p>If your flight allows, we can arrange visit to:</p>
<ul>
    <li><strong>Khudayar Khan Palace:</strong> Study the extraordinary Fergana-style tilework covering the 1873 palace facade. Different from Samarkand and Bukhara—brighter colors, unique patterns.</li>
    <li><strong>Umarov Family Wood Carving Workshop:</strong> Multi-generational masters demonstrate column carving and architectural decoration</li>
</ul>

<p><strong>OR Leisure Morning in Tashkent:</strong></p>
<ul>
    <li>Last-minute shopping for art supplies or crafts</li>
    <li>Visit contemporary art galleries</li>
    <li>Pack and prepare artwork for travel</li>
</ul>

<h4>Afternoon: Tour Conclusion</h4>
<p><strong>12:00 - Farewell Lunch (Included):</strong> Final group gathering. Share experiences, exchange contact information, and review your created artworks.</p>

<p><strong>Exhibition moment:</strong> Display all your creations—miniature painting, ceramic piece, embroidered suzani, handmade paper, silk scarf. Professional photos taken for your portfolio.</p>

<p><strong>14:00 onwards - Airport Transfers:</strong> Individual transfers based on flight times.</p>

<p><strong>Tour Concludes:</strong> You leave with a suitcase full of your own artwork, a camera full of stunning photos, and a profound understanding of Central Asian artistic heritage.</p>

<p><em>Meals: Breakfast and lunch included.</em></p>

<h3>What You\'re Taking Home:</h3>
<ul>
    <li>✅ Miniature painting signed by Master Davlat Toshev</li>
    <li>✅ Hand-thrown ceramic piece from Rishtan clay with traditional glaze</li>
    <li>✅ Embroidered suzani textile panel</li>
    <li>✅ Handmade mulberry paper sheets</li>
    <li>✅ Silk scarf you helped weave and dye</li>
    <li>✅ Art supply kit with sketches and watercolors of monuments</li>
    <li>✅ Photography portfolio of world-class architecture</li>
    <li>✅ Certificates of authenticity from master artisans</li>
    <li>✅ Knowledge to understand and appreciate Islamic art and architecture worldwide</li>
</ul>',
                'type' => 'day',
                'default_start_time' => '08:00',
                'duration_minutes' => 1440,
                'sort_order' => 10
            ],
        ];

        foreach ($itinerary as $item) {
            $tour->itineraryItems()->create($item);
        }

        // Update city tour count
        $tourCount = $tashkent->tours()->where('is_active', true)->count();
        $tashkent->tour_count_cache = $tourCount;
        $tashkent->save();

        $this->info("✅ Arts, Crafts & Architecture Tour created successfully!");
        $this->info("Tour ID: {$tour->id}");
        $this->info("Title: {$tour->title}");
        $this->info("Price: $2,495 USD per person");
        $this->info("Duration: 10 days / 9 nights");
        $this->info("Max guests: 8 (intimate workshop experiences)");
        $this->info("Workshops: 6 hands-on sessions with master artisans");
        $this->info("URL: http://127.0.0.1:8000/tours/{$tour->slug}");

        return 0;
    }
}
