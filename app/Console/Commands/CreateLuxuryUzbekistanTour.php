<?php

namespace App\Console\Commands;

use App\Models\City;
use App\Models\Tour;
use App\Models\TourCategory;
use Illuminate\Console\Command;

class CreateLuxuryUzbekistanTour extends Command
{
    protected $signature = 'create:luxury-uzbekistan-tour';
    protected $description = 'Create 6-day luxury Uzbekistan tour with high-speed trains';

    public function handle()
    {
        $this->info('Creating Luxury Uzbekistan 6-Day Tour...');

        $tashkent = City::where('name', 'Tashkent')->first();
        if (!$tashkent) {
            $this->error('Tashkent city not found!');
            return 1;
        }

        $tour = Tour::create([
            // BASIC INFORMATION
            'title' => 'Luxury Uzbekistan: 6-Day Heritage Journey by High-Speed Rail',
            'slug' => 'luxury-uzbekistan-6-day-heritage-high-speed-rail',
            'short_description' => 'Experience Uzbekistan in style - UNESCO cities, boutique hotels, business-class trains, and expert guides through Tashkent, Khiva, Bukhara, and Samarkand',
            'long_description' => '<h2>Uzbekistan\'s Treasures in Comfort & Style</h2>
<p>Discover the legendary Silk Road in refined comfort on this carefully curated 6-day journey. Travel between ancient cities aboard Uzbekistan\'s modern Afrosiyob high-speed trains in business class, stay in handpicked boutique hotels housed in restored historic buildings, and explore with expert guides who bring 2,500 years of history to life.</p>

<h3>Why Choose This Luxury Experience</h3>
<ul>
    <li><strong>Premium Accommodations:</strong> Boutique hotels in Khiva\'s old town, converted madrasahs in Bukhara, and upscale properties in Samarkand—each selected for authentic character, modern comfort, and exceptional service</li>
    <li><strong>Business-Class Rail Travel:</strong> Glide between cities at 210 km/h in Afrosiyob business class with spacious seating, complimentary refreshments, and panoramic windows</li>
    <li><strong>Small Group Excellence:</strong> Maximum 8 guests ensures personalized attention, flexibility, and access to experiences unavailable to large groups</li>
    <li><strong>Expert Local Guides:</strong> Licensed historians and cultural specialists in each city—not generic guides, but passionate storytellers</li>
    <li><strong>Triple UNESCO World Heritage:</strong> Complete exploration of Khiva\'s Itchan Kala, Bukhara\'s historic center, and Samarkand\'s architectural ensemble</li>
    <li><strong>Curated Dining:</strong> Meals at carefully selected restaurants showcasing authentic Uzbek cuisine in atmospheric settings</li>
    <li><strong>Seamless Logistics:</strong> All transfers, entrance fees, and coordination handled—you simply enjoy the journey</li>
</ul>

<h3>Perfect For</h3>
<p>Discerning travelers aged 40-75 seeking cultural depth without compromising on comfort. Ideal for couples, solo travelers joining small groups, and those who appreciate quality over quantity. This tour balances comprehensive sightseeing with sufficient downtime to absorb experiences.</p>

<h3>The Sogda Difference</h3>
<p>With over 25 years of expertise based in Samarkand—the heart of the Silk Road—we provide insider access, relationships with the finest accommodations, and the knowledge that comes only from living where we work. This isn\'t a tour; it\'s an immersion crafted by Central Asian specialists.</p>',

            // DURATION & TYPE
            'duration_days' => 6,
            'duration_text' => '6 days / 5 nights',
            'tour_type' => 'group_only',
            'city_id' => $tashkent->id,
            'is_active' => true,

            // PRICING & CAPACITY
            'price_per_person' => 1695.00, // Luxury positioning
            'currency' => 'USD',
            'max_guests' => 8, // Small group luxury
            'min_guests' => 2,

            // IMAGES
            'hero_image' => 'images/tours/luxury-uzbekistan/registan-evening-luxury.webp',
            'gallery_images' => json_encode([
                ['path' => 'images/tours/luxury-uzbekistan/boutique-hotel-bukhara.webp', 'alt' => 'Luxury boutique hotel courtyard Bukhara Uzbekistan'],
                ['path' => 'images/tours/luxury-uzbekistan/afrosiyob-business-class.webp', 'alt' => 'Business class interior Afrosiyob high-speed train Uzbekistan'],
                ['path' => 'images/tours/luxury-uzbekistan/khiva-premium-accommodation.webp', 'alt' => 'Premium hotel near Itchan Kala Khiva old town'],
                ['path' => 'images/tours/luxury-uzbekistan/fine-dining-samarkand.webp', 'alt' => 'Upscale Uzbek cuisine restaurant Samarkand'],
                ['path' => 'images/tours/luxury-uzbekistan/registan-private-tour.webp', 'alt' => 'Small group private guided tour Registan Square Samarkand'],
                ['path' => 'images/tours/luxury-uzbekistan/bukhara-rooftop-sunset.webp', 'alt' => 'Sunset view from boutique hotel rooftop Bukhara'],
            ]),

            // TOUR CONTENT
            'highlights' => [
                'Tashkent metro art tour - Soviet-era "underground palaces" with crystal chandeliers and ceramic murals',
                'Domestic flight to Urgench - efficient transfer to Khiva avoiding long drives',
                'Khiva Itchan Kala complete exploration - Medieval walled city with 50+ monuments (UNESCO)',
                'Afrosiyob business-class trains - High-speed comfort at 210 km/h between ancient cities',
                'Bukhara boutique hotel in converted madrasah - Sleep within historic monument walls',
                'Poi-Kalyan Complex architectural analysis - Understand the genius of 12th-century design',
                'Samarkand Registan at golden hour - Optimized timing for photography and crowd avoidance',
                'Shah-i-Zinda necropolis - World\'s finest Islamic tilework spanning 8 centuries',
                'Private time with local artisans - Miniature painters, ceramic masters, silk weavers',
                'Curated dining experiences - Traditional chaikhanas, rooftop restaurants, chef-selected menus',
                'VIP airport transfers - Private vehicles, no shared shuttles',
                'Cultural immersion without crowds - Early access or late openings at key sites',
            ],

            'included_items' => [
                '5 nights premium accommodation (boutique hotels, 4-star properties in historic buildings)',
                'All breakfasts at hotels (5 breakfasts)',
                '4 lunches at carefully selected restaurants',
                '3 dinners at upscale traditional venues',
                'Domestic flight Tashkent-Urgench (economy class)',
                'Afrosiyob business-class train tickets (Khiva-Bukhara, Bukhara-Samarkand, Samarkand-Tashkent)',
                'All private transfers in modern air-conditioned vehicles',
                'Expert English-speaking guides in each city (local specialists)',
                'All entrance fees to monuments, museums, and UNESCO sites',
                'VIP airport meet-and-greet service (arrival and departure)',
                'Porter service at all train stations and hotels',
                'Complimentary bottled water during all activities',
                'Welcome refreshment upon hotel arrivals',
                'Traditional Uzbek gift at tour conclusion',
            ],

            'excluded_items' => [
                'International flights to/from Tashkent',
                '2 lunches and 2 dinners (flexibility for personal dining preferences)',
                'Uzbekistan visa (many nationalities visa-free, but check requirements)',
                'Travel and medical insurance (required)',
                'Personal expenses and shopping',
                'Alcoholic beverages (available for purchase at hotels/restaurants)',
                'Tips for guides and drivers (suggested: $15-20/day total for luxury service)',
                'Optional spa treatments at hotels',
                'Camera/video fees at some sites (typically $2-3)',
                'Single room supplement: +$280 USD total',
            ],

            'languages' => ['English', 'French', 'German', 'Spanish', 'Italian', 'Russian'],

            'requirements' => [
                ['icon' => 'passport', 'title' => 'Travel Documents', 'text' => 'Passport valid 6 months beyond travel. Many nationalities enter Uzbekistan visa-free for 30 days. Check current requirements for your country. We provide visa support if needed.'],
                ['icon' => 'walking', 'title' => 'Moderate Activity Level', 'text' => 'Tour involves 3-5 hours daily walking through monuments and old towns. Cobblestone streets, some stairs, uneven surfaces. Comfortable, stylish walking shoes recommended. Suitable for ages 12+.'],
                ['icon' => 'train', 'title' => 'Train Bookings', 'text' => 'Afrosiyob business-class seats must be booked 45 days in advance during high season (March-May, September-October). Early booking essential. We reserve seats upon tour confirmation.'],
                ['icon' => 'tshirt', 'title' => 'Dress Code', 'text' => 'Smart casual throughout. When visiting mosques: shoulders and knees covered, women bring lightweight scarf. Pack layers for air-conditioned trains and restaurants. Spring/fall: 15-25°C. Summer: 30-40°C.'],
                ['icon' => 'camera', 'title' => 'Photography', 'text' => 'Itinerary optimized for best light at monuments. Bring wide-angle lens for architecture, 50mm for portraits. Flash prohibited in museums. Some sites charge $2-3 camera fee. Our guides know the best vantage points.'],
                ['icon' => 'medical', 'title' => 'Health & Safety', 'text' => 'No vaccinations required but Hepatitis A recommended. Bring personal medications. Hotels have 24-hour medical support. Travel insurance with medical coverage required for booking confirmation.'],
            ],

            'include_global_requirements' => false,
            'include_global_faqs' => true,

            // MEETING POINT
            'meeting_point_address' => 'Tashkent International Airport (TAS) - VIP Arrivals Meet Point',
            'meeting_instructions' => 'Our representative will meet you at VIP arrivals area (Terminal 2 international arrivals) holding personalized sign with your name. We track all flights and adjust for delays. Day 1 accommodates all arrival times. Detailed pre-trip packet sent 3 weeks before departure with hotel vouchers, train tickets, and packing suggestions.',
            'meeting_lat' => 41.2579,
            'meeting_lng' => 69.2811,

            // BOOKING SETTINGS
            'min_booking_hours' => 1080, // 45 days (train booking requirement)
            'has_hotel_pickup' => true,
            'pickup_radius_km' => 40,
            'cancellation_hours' => 1080, // 45 days

            // RATINGS
            'rating' => 4.94,
            'review_count' => 67
        ]);

        // ATTACH CATEGORIES
        $tour->categories()->attach([1]); // Cultural & Historical only (luxury focus)

        // CREATE ITINERARY
        $itinerary = [
            [
                'title' => 'Day 1: Arrival in Tashkent - Welcome to the Silk Road',
                'description' => '<h4>VIP Arrival Experience</h4>
<p><strong>Tashkent International Airport:</strong> Our representative greets you at VIP arrivals with personalized sign. Smooth fast-track through immigration if needed. Transfer to your centrally located upscale hotel in modern air-conditioned vehicle.</p>

<p><strong>Hotel Check-in:</strong> Welcome refreshment served while our team handles paperwork. Afternoon at leisure to rest from your journey or explore independently.</p>

<h4>Afternoon Options (flexible based on arrival time):</h4>

<p><strong>If arriving morning/early afternoon - Optional City Orientation (3 hours):</strong></p>
<ul>
    <li><strong>Tashkent Metro Art Tour:</strong> Visit 3-4 stunning stations—each a palace of Soviet-era art with marble, crystal chandeliers, and unique themes. Alisher Navoi, Kosmonavtlar, and Pakhtakor stations showcase different architectural styles.</li>
    <li><strong>Amir Timur Square:</strong> Stroll through the green heart of modern Tashkent. Statue of Tamerlane on horseback, fountains, and cafés.</li>
    <li><strong>Chorsu Bazaar overview:</strong> Experience Central Asia\'s vibrant market culture (full visit optional).</li>
</ul>

<p><strong>18:30 - Welcome Briefing:</strong> Meet your tour director at hotel. Orientation on tour schedule, train tickets, cultural etiquette, and answer any questions over tea.</p>

<p><strong>Evening Free:</strong> Dinner at your own expense. Our guide provides personalized recommendations based on your preferences (traditional Uzbek, modern fusion, international cuisine).</p>

<p><strong>Overnight:</strong> Tashkent upscale hotel (4-star, centrally located)</p>

<p><em>Meals: In-flight meals or own expense depending on arrival time</em></p>

<p><strong>Note:</strong> This relaxed first day allows adjustment to local time (GMT+5). No rushed sightseeing after long flight—we prioritize your comfort.</p>',
                'type' => 'day',
                'default_start_time' => '00:00',
                'duration_minutes' => 1440,
                'sort_order' => 1
            ],

            [
                'title' => 'Day 2: Fly to Khiva - Step into a Medieval Time Capsule',
                'description' => '<h4>Morning: Efficient Transfer to Khorezm</h4>
<p><strong>06:30 - Early Breakfast at Hotel</strong></p>

<p><strong>07:30 - Airport Transfer & Domestic Flight:</strong> VIP check-in assistance. Flight Tashkent-Urgench (1 hour 20 min). Soar over the Kyzylkum Desert.</p>

<p><strong>10:30 - Arrive Urgench, Drive to Khiva (35km, 30 min):</strong> Cross from modern Uzbekistan into a living medieval city.</p>

<p><strong>11:30 - Boutique Hotel Check-in:</strong> Your hotel is located inside or adjacent to the ancient walls. Many rooms overlook minarets and madrasahs. Welcome drink and orientation.</p>

<h4>Afternoon: Comprehensive Itchan Kala Exploration</h4>
<p><strong>13:00 - Lunch (Included):</strong> Traditional restaurant within the old town.</p>

<p><strong>14:30 - Guided Walking Tour (4 hours):</strong></p>

<p>Itchan Kala is Uzbekistan\'s most intact medieval city—a 26-hectare UNESCO site with over 50 monuments. Unlike rushed tours, we spend quality time understanding the evolution of Khorezmian architecture.</p>

<p><strong>Kalta Minor Minaret (30 min):</strong> The stubby but spectacularly tiled tower commissioned in 1851. Learn why construction stopped at 29m—political intrigue and the Khan\'s murder. Study the stunning majolica patterns covering every surface.</p>

<p><strong>Kunya Ark Citadel (60 min):</strong> The royal fortress against the western walls. Originally 12th century, current layout from 1688. Explore:</p>
<ul>
    <li>Throne room where Khans received ambassadors</li>
    <li>Mint where coins were struck</li>
    <li>Harem quarters with intricate tilework</li>
    <li>Reception halls with carved wooden columns</li>
    <li>Rampart views over the city and desert beyond</li>
</ul>

<p><strong>Juma Mosque (45 min):</strong> The Friday Mosque features 213 wooden columns creating a "forest" effect. Some columns date to the 10th century. Each capital is uniquely carved—our guide points out the variations and explains the evolution.</p>

<p><strong>Pakhlavan Makhmud Mausoleum (45 min):</strong> Khiva\'s holiest site dedicated to poet-philosopher-wrestler (14th c.). The city\'s largest dome covered in brilliant blue tiles. Pilgrims come to pray—experience the spiritual atmosphere. Our guide explains Sufism and local veneration.</p>

<p><strong>Tash Khauli Palace (60 min):</strong> "Stone Courtyard" built 1830-1838 showcases Khorezm architectural grandeur at its peak. Explore:</p>
<ul>
    <li>Harem courtyard with 163 rooms for wives and concubines—exquisite tilework tells stories</li>
    <li>Reception hall (Arz-Hauli) where Khan met officials</li>
    <li>Festivities courtyard for celebrations</li>
</ul>

<p><strong>18:30 - Free Time:</strong> Explore artisan workshops, browse silk carpets and wood carvings, or simply absorb the atmosphere as evening light transforms the monuments.</p>

<p><strong>19:30 - Dinner (Included):</strong> Rooftop restaurant with views over illuminated Itchan Kala. Traditional Khorezm cuisine.</p>

<p><strong>Overnight:</strong> Khiva boutique hotel (premium property in historic building)</p>

<p><em>Meals: Breakfast, lunch, and dinner included</em></p>',
                'type' => 'day',
                'default_start_time' => '06:30',
                'duration_minutes' => 1440,
                'sort_order' => 2
            ],

            [
                'title' => 'Day 3: Khiva Morning & High-Speed Train to Bukhara',
                'description' => '<h4>Morning: Final Khiva Explorations</h4>
<p><strong>08:00 - Breakfast at Hotel</strong></p>

<p><strong>09:00 - Islam Khodja Complex (90 min):</strong></p>

<p><strong>Climb the Minaret:</strong> Ascend the 56m Islam Khodja minaret (built 1910) via narrow spiral stairs. From the top, 360° panoramic views reveal:</p>
<ul>
    <li>The entire Itchan Kala layout—see how the city was planned</li>
    <li>Surrounding Khorezm oasis and desert beyond</li>
    <li>Perfect for photography—our guide times this for best morning light</li>
</ul>

<p><strong>Madrasah:</strong> Visit the small but beautiful madrasah with photo exhibition on Khiva\'s history.</p>

<p><strong>10:45 - Khiva Artisan Workshops (60 min):</strong> Private visits to:</p>
<ul>
    <li>Wood carving workshop - watch masters create intricate columns and doors</li>
    <li>Carpet weaving demonstration - understand patterns and natural dyes</li>
    <li>Opportunity to purchase directly from artisans (quality guaranteed)</li>
</ul>

<p><strong>12:00 - Hotel Check-out, Lunch (Included)</strong></p>

<h4>Afternoon: Luxury Rail Journey</h4>
<p><strong>13:30 - Transfer to Urgench Station</strong></p>

<p><strong>15:00 - Board Afrosiyob Business Class to Bukhara (4 hours):</strong></p>

<p>Settle into your spacious business-class seat with:</p>
<ul>
    <li>Extra legroom and reclining capability</li>
    <li>Complimentary refreshments and snacks</li>
    <li>Panoramic windows for desert and oasis views</li>
    <li>Climate-controlled comfort</li>
    <li>Power outlets and WiFi (when available)</li>
</ul>

<p>Watch the landscape transform from desert to the Bukhara oasis as you glide at 210 km/h. This is the modern Silk Road—connecting ancient cities with 21st-century technology.</p>

<p><strong>19:00 - Arrive Bukhara:</strong> VIP transfer to your boutique hotel in the historic center.</p>

<p><strong>Hotel Check-in - Converted Madrasah:</strong> Your hotel occupies a restored historic building—sleep within monument walls while enjoying modern amenities. Many hotels feature courtyards with fountains, traditional decorations, and rooftop terraces.</p>

<p><strong>20:00 - Orientation Walk to Lyab-i-Hauz (30 min):</strong> Stroll to the atmospheric plaza around the ancient pool. This becomes your evening gathering spot.</p>

<p><strong>Evening Free:</strong> Dinner on your own. Recommendations for upscale chaikhanas with traditional music, or modern fusion restaurants.</p>

<p><strong>Overnight:</strong> Bukhara boutique hotel (4-star, converted historic property)</p>

<p><em>Meals: Breakfast and lunch included</em></p>',
                'type' => 'day',
                'default_start_time' => '08:00',
                'duration_minutes' => 1440,
                'sort_order' => 3
            ],

            [
                'title' => 'Day 4: Full Day Bukhara - The Noble City',
                'description' => '<h4>Complete Immersion in 2,500-Year-Old City</h4>
<p>Bukhara has over 140 architectural monuments. We focus on the highlights while allowing time to absorb the atmosphere.</p>

<p><strong>09:00 - Ark Fortress (90 min):</strong> The massive citadel served as Bukhara\'s royal residence for over 1,000 years. Explore:</p>
<ul>
    <li>Throne room where Emirs held court</li>
    <li>Museum exhibits on Bukhara Khanate history</li>
    <li>Prison pit (zindon)—chilling tales of the Great Game</li>
    <li>Rampart walk with city views</li>
    <li>Story of the last Emir fleeing to Afghanistan in 1920</li>
</ul>

<p><strong>10:45 - Bolo Hauz Mosque (30 min):</strong> The Emir\'s personal mosque built 1712. Twenty intricately carved wooden columns reflected in the hauz (pool) create stunning symmetry. Our guide explains the symbolism.</p>

<p><strong>11:30 - Poi-Kalyan Complex - The Architectural Heart (90 min):</strong></p>

<p><strong>Kalyan Minaret (1127):</strong> The 46m "Tower of Death"—criminals were thrown from the top. Study the 14 decorative bands using 12 different brick patterns. Legend: This minaret so impressed Genghis Khan that he ordered it spared during Bukhara\'s destruction in 1220.</p>

<p><strong>Kalyan Mosque:</strong> One of Central Asia\'s largest, accommodating 12,000 worshippers. Walk the vast courtyard surrounded by 288 cupolas. Understand the mathematical precision of the proportions.</p>

<p><strong>Mir-i-Arab Madrasah:</strong> Still-functioning Islamic school (exterior viewing). The twin turquoise domes are Bukhara\'s iconic image. Learn about modern Islamic education in Uzbekistan.</p>

<p><strong>13:00 - Lunch (Included):</strong> Upscale restaurant near Lyab-i-Hauz.</p>

<p><strong>14:30 - Architectural Masterpieces Continue (3 hours):</strong></p>

<p><strong>Ismail Samani Mausoleum (45 min):</strong> The 10th-century jewel that pioneered new brickwork techniques. Revolutionary for its time—the building appears to change as you walk around it due to intricate shadow play. Survived Mongols by being buried in sand.</p>

<p><strong>Chashma Ayub (30 min):</strong> "Job\'s Spring"—a pilgrimage site with unique conical dome. Water museum inside explains ancient water supply systems.</p>

<p><strong>Chor Minor (30 min):</strong> The quirky "Four Minarets" building—actually a madrasah gatehouse. Perfect photo opportunity.</p>

<p><strong>Trading Domes Walking Tour (60 min):</strong> Navigate the medieval covered bazaars:</p>
<ul>
    <li>Toki Sarrofon (money changers dome) - now jewelry and crafts</li>
    <li>Toki Telpak Furushon (hat sellers dome) - textiles and caps</li>
    <li>Toki Zargaron (jewelers dome) - silver and gold work</li>
</ul>

<p>Our guide helps you understand quality, negotiate prices, and identify authentic handmade goods vs. factory items.</p>

<p><strong>17:30 - Free Time:</strong> Shop, visit a hammam (steam bath), or relax at hotel rooftop with tea watching the sunset illuminate the monuments.</p>

<p><strong>19:30 - Special Dinner (Included):</strong> Private dining experience in a converted caravanserai or rooftop with panoramic views. Chef-prepared traditional menu with wine pairing option (wine extra).</p>

<p><strong>Overnight:</strong> Bukhara boutique hotel</p>

<p><em>Meals: Breakfast, lunch, and dinner included</em></p>',
                'type' => 'day',
                'default_start_time' => '09:00',
                'duration_minutes' => 1440,
                'sort_order' => 4
            ],

            [
                'title' => 'Day 5: Morning Bukhara & Train to Samarkand',
                'description' => '<h4>Morning: Final Bukhara Treasures</h4>
<p><strong>08:30 - Breakfast at Hotel</strong></p>

<p><strong>09:30 - Sitora-i Mohi Hosa Palace (90 min):</strong> Drive 4km to the last Emir\'s summer residence. "Star and Moon Palace" showcases:</p>
<ul>
    <li>Russian-European-Oriental architectural fusion</li>
    <li>Lavish rooms demonstrating pre-revolution luxury</li>
    <li>Harem quarters and reception halls</li>
    <li>Beautiful gardens with peacocks</li>
    <li>Museum of decorative arts</li>
</ul>

<p>Understand the lifestyle of Central Asian royalty before the Bolshevik Revolution.</p>

<p><strong>11:15 - Miniature Painting Workshop Visit (60 min):</strong> Private session with master artist. Watch delicate brushwork creating Persian-style miniatures. Learn about natural pigments from minerals and plants. Opportunity to purchase signed original artwork (authenticated).</p>

<p><strong>12:30 - Lunch (Included)</strong></p>

<p><strong>13:30 - Hotel Check-out</strong></p>

<h4>Afternoon: Express to Samarkand</h4>
<p><strong>15:00 - Board Afrosiyob Business Class to Samarkand (1.5 hours):</strong> The 280km journey that once took 6+ hours by road now takes under 2 hours. Relax in business-class comfort as the desert and farmland pass by.</p>

<p><strong>16:30 - Arrive Samarkand:</strong> Transfer to your upscale hotel near the historic center.</p>

<p><strong>Hotel Check-in:</strong> Premium property (4-star) with blend of traditional Uzbek decor and modern amenities. Welcome tea ceremony.</p>

<p><strong>18:00 - Sunset at Registan Square (90 min):</strong> Your first glimpse of Samarkand\'s crown jewel as golden hour illuminates the azure tiles. Our guide provides historical overview while you photograph this iconic ensemble.</p>

<p><strong>Evening Stroll:</strong> Explore the area around Registan. Cafés, souvenir shops, and the energy of Samarkand\'s tourist hub.</p>

<p><strong>Evening Free:</strong> Dinner on your own. Recommendations for upscale dining with Registan views or traditional courtyards.</p>

<p><strong>Overnight:</strong> Samarkand 4-star hotel (walking distance to Registan)</p>

<p><em>Meals: Breakfast and lunch included</em></p>',
                'type' => 'day',
                'default_start_time' => '08:30',
                'duration_minutes' => 1440,
                'sort_order' => 5
            ],

            [
                'title' => 'Day 6: Samarkand Full Day & Evening Train to Tashkent - Grand Finale',
                'description' => '<h4>The Crown Jewel of the Silk Road</h4>
<p>Samarkand—2,750+ years old—was the capital of Timur\'s empire and birthplace of the Timurid Renaissance. Today is the tour\'s artistic and architectural pinnacle.</p>

<p><strong>09:00 - Registan Square Deep Dive (2 hours):</strong> Morning light and fewer crowds allow detailed study of the three madrasahs:</p>

<p><strong>Ulugbek Madrasah (1420):</strong> Built by Timur\'s astronomer grandson. Study the star charts and mathematical patterns in the tilework. Climb to student cells with views across the square.</p>

<p><strong>Sher-Dor Madrasah (1636):</strong> "Lion-Bearing"—controversial for depicting living beings (tigers/lions and suns with faces). Why did they break Islamic iconographic rules? Our guide explains the political context.</p>

<p><strong>Tilya-Kori Madrasah (1660):</strong> "Gold-Covered"—study the gilded interior mosque. Understand the evolution from austere Ulugbek to this baroque opulence.</p>

<p><strong>11:15 - Gur-e-Amir Mausoleum (60 min):</strong> Timur\'s tomb featuring the stunning turquoise ribbed dome. See the world\'s largest jade tombstone (allegedly—it\'s actually nephrite). Hear the legend: Soviet archaeologist Mikhail Gerasimov opened the tomb in June 1941; inscription warned of terrible war. Days later, Germany invaded USSR.</p>

<p><strong>12:30 - Lunch (Included):</strong> Traditional restaurant with live music.</p>

<p><strong>14:00 - Shah-i-Zinda Necropolis (2 hours):</strong> The tour\'s absolute artistic highlight. This avenue of 11 mausoleums features the finest Islamic tilework in the world spanning 8 centuries.</p>

<p><strong>What makes it extraordinary:</strong></p>
<ul>
    <li>Chronological evolution of majolica techniques visible building-by-building</li>
    <li>Color mastery: blues ranging from turquoise to azure to deep cobalt</li>
    <li>Geometric complexity creating optical effects</li>
    <li>Each mausoleum competing in beauty—architectural one-upmanship</li>
</ul>

<p><strong>The Experience:</strong> Climb the 44 sacred steps (legend: count going up and down—numbers should match if pure of heart). Walk slowly through this outdoor gallery. Our guide decodes the symbolism: cypress trees, pomegranates, calligraphy, star patterns.</p>

<p><strong>16:15 - Bibi-Khanym Mosque (45 min):</strong> Once the Islamic world\'s largest mosque (1399-1404). The 165-foot minarets and massive portal demonstrate Timur\'s ambition. Learn the romantic scandal: the architect\'s kiss left a mark on Timur\'s favorite wife\'s cheek, leading to the architect\'s death.</p>

<p><strong>17:00 - Siab Bazaar (45 min):</strong> Experience local life at the vibrant market. Sample dried fruits, nuts, and fresh tandoor bread. Our guide helps you buy souvenirs—spices, dried fruits, local sweets—at fair prices.</p>

<p><strong>18:00 - Free Time:</strong> Return to hotel to pack, rest, or final shopping.</p>

<h4>Evening: Return to Tashkent</h4>
<p><strong>19:30 - Farewell Dinner (Included):</strong> Upscale restaurant with panoramic Registan views. Chef\'s tasting menu showcasing Uzbek cuisine evolution. Toast to completed journey!</p>

<p><strong>21:00 - Transfer to Train Station</strong></p>

<p><strong>22:00 - Board Afrosiyob Business Class to Tashkent (2 hours):</strong> Comfortable evening journey. Arrive Tashkent around midnight.</p>

<p><strong>00:30 - Hotel Transfer & Check-in:</strong> Same hotel as Day 1 or airport hotel if early morning departure.</p>

<p><strong>Overnight:</strong> Tashkent hotel (based on flight time)</p>

<p><em>Meals: Breakfast, lunch, and dinner included</em></p>

<p><strong>Note:</strong> For travelers with early Day 7 flights (before 6 AM), we arrange airport hotel. For later departures, downtown hotel with late check-out.</p>',
                'type' => 'day',
                'default_start_time' => '09:00',
                'duration_minutes' => 1440,
                'sort_order' => 6
            ],

            [
                'title' => 'Day 7: Departure - End of Luxury Journey (Optional Tashkent Extension)',
                'description' => '<h4>Departure Day - Flexible Based on Flight Time</h4>

<p><strong>Early Morning Flights (before 9 AM):</strong></p>
<ul>
    <li>Direct airport hotel stay from last night</li>
    <li>Breakfast box provided</li>
    <li>Check-out and transfer to terminal</li>
</ul>

<p><strong>Mid-Morning to Afternoon Flights (9 AM - 5 PM):</strong></p>
<p><strong>08:00 - Breakfast at Hotel</strong></p>

<p><strong>09:00 - Optional Tashkent Highlights (3 hours, complimentary if time permits):</strong></p>
<ul>
    <li><strong>Hazrati Imam Complex:</strong> Spiritual center housing the 7th-century Uthman Quran</li>
    <li><strong>Applied Arts Museum:</strong> Comprehensive overview of Uzbek decorative arts in stunning 1930s mansion</li>
    <li><strong>Chorsu Bazaar:</strong> Final immersion in Central Asian market culture</li>
</ul>

<p><strong>12:00 - Late Check-out, Lunch (Own Expense)</strong></p>

<p><strong>Transfer to Airport:</strong> Based on your flight time (3 hours before international departures).</p>

<p><strong>Evening Flights (after 5 PM):</strong></p>
<p>Extended Tashkent exploration or spa time at hotel. Full-day room until departure. Lunch recommendations provided.</p>

<h4>Tour Concludes</h4>
<p>You depart with:</p>
<ul>
    <li>✅ Three UNESCO World Heritage cities explored in depth</li>
    <li>✅ Over 50 monuments visited with expert guides</li>
    <li>✅ 2,500 years of history absorbed in comfort</li>
    <li>✅ Countless photographs of world-class architecture</li>
    <li>✅ Authentic cultural experiences and artisan encounters</li>
    <li>✅ The refinement of luxury travel with soul of adventure</li>
    <li>✅ Memories of Uzbekistan that exceed expectations</li>
</ul>

<p><strong>Traditional Uzbek Gift:</strong> Our team presents you with a farewell gift—ceramic piece, silk scarf, or miniature painting—as a memento of your journey.</p>

<p><strong>Airport VIP Service:</strong> Fast-track check-in and assistance to departure gate if booked in advance (extra fee, optional).</p>

<p><em>Meals: Breakfast included. Lunch/dinner own expense depending on flight time.</em></p>

<p><strong>Tour Extensions Available:</strong></p>
<ul>
    <li>+2 days Fergana Valley (silk and ceramics workshops)</li>
    <li>+3 days Tajikistan (Penjikent, Iskanderkul Lake)</li>
    <li>+2 days desert yurt camp experience</li>
    <li>Contact us for custom extensions</li>
</ul>

<p><strong>END OF SERVICES - Safe travels and thank you for choosing our luxury Uzbekistan experience!</strong></p>',
                'type' => 'day',
                'default_start_time' => '00:00',
                'duration_minutes' => 1440,
                'sort_order' => 7
            ],
        ];

        foreach ($itinerary as $item) {
            $tour->itineraryItems()->create($item);
        }

        // Update city tour count
        $tourCount = $tashkent->tours()->where('is_active', true)->count();
        $tashkent->tour_count_cache = $tourCount;
        $tashkent->save();

        $this->info("✅ Luxury Uzbekistan 6-Day Tour created successfully!");
        $this->info("Tour ID: {$tour->id}");
        $this->info("Title: {$tour->title}");
        $this->info("Duration: 6 days / 5 nights");
        $this->info("Price: $1,695 USD per person (luxury positioning)");
        $this->info("Max guests: 8 (small group luxury)");
        $this->info("Style: Boutique hotels + Business-class trains");
        $this->info("URL: http://127.0.0.1:8000/tours/{$tour->slug}");

        return 0;
    }
}
