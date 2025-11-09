<?php

namespace App\Console\Commands;

use App\Models\Tour;
use App\Models\TourCategory;
use App\Models\City;
use App\Models\BlogPost;
use App\Models\BlogCategory;
use App\Models\BlogTag;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class GenerateToursAndBlogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:tours-blogs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate 8 tours with different categories and 8 blog posts';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting tour and blog generation...');

        // Generate Tours
        $this->info('Generating 8 tours...');
        $this->generateTours();
        $this->info('Tours generated successfully!');

        // Generate Blog Posts
        $this->info('Generating 8 blog posts...');
        $this->generateBlogPosts();
        $this->info('Blog posts generated successfully!');

        $this->info('All tours and blog posts have been created!');
        return 0;
    }

    private function generateTours()
    {
        // Get or create cities
        $samarkand = City::firstOrCreate(['name' => 'Samarkand'], ['description' => 'Ancient city on the Silk Road']);
        $bukhara = City::firstOrCreate(['name' => 'Bukhara'], ['description' => 'Historic trading center of the Silk Road']);
        $khiva = City::firstOrCreate(['name' => 'Khiva'], ['description' => 'Walled city with preserved medieval architecture']);
        $tashkent = City::firstOrCreate(['name' => 'Tashkent'], ['description' => 'Modern capital of Uzbekistan']);
        $tashkentRegion = City::firstOrCreate(['name' => 'Chimgan Mountains'], ['description' => 'Mountain range near Tashkent']);
        $navoi = City::firstOrCreate(['name' => 'Navoi'], ['description' => 'City in the Kyzylkum Desert']);

        // Get categories
        $cultural = TourCategory::where('slug', 'cultural-historical')->first();
        $mountain = TourCategory::where('slug', 'mountain-adventure')->first();
        $family = TourCategory::where('slug', 'family-educational')->first();
        $desert = TourCategory::where('slug', 'desert-nomadic')->first();
        $cityWalks = TourCategory::where('slug', 'city-walks')->first();
        $foodCraft = TourCategory::where('slug', 'food-craft')->first();

        $tours = [
            // 1. Cultural & Historical Tour
            [
                'title' => 'Golden Ring of Samarkand: A Historical Journey',
                'short_description' => 'Explore the architectural masterpieces of Samarkand from Timurid era to present day',
                'long_description' => '<p>Journey through time in the legendary city of Samarkand, where ancient history meets architectural brilliance. This comprehensive tour takes you through the most significant monuments of the Timurid period and beyond.</p><p>Walk in the footsteps of merchants, scholars, and conquerors who shaped the Silk Road. Our expert guides will bring to life the stories of Timur, Ulugh Beg, and the golden age of Central Asian civilization.</p>',
                'duration_days' => 1,
                'duration_text' => '6 hours',
                'price_per_person' => 65.00,
                'currency' => 'USD',
                'max_guests' => 8,
                'min_guests' => 1,
                'city_id' => $samarkand->id,
                'is_active' => true,
                'city' => $samarkand,
                'categories' => [$cultural],
                'hero_image' => 'images/tours/golden-ring/hero.webp',
                'highlights' => [
                    'Registan Square - Three madrasahs in perfect harmony',
                    'Gur-e-Amir - Tomb of the great conqueror Timur',
                    'Shah-i-Zinda - Avenue of mystical mausoleums',
                    'Bibi-Khanym Mosque - Monument to eternal love',
                    'Ulugh Beg Observatory - Medieval astronomical marvel'
                ],
                'included_items' => [
                    'Hotel pickup and drop-off',
                    'Expert English-speaking guide',
                    'Entrance fees to all monuments',
                    'Traditional tea ceremony',
                    'Bottled water'
                ],
                'excluded_items' => [
                    'Lunch',
                    'Tips',
                    'Personal expenses'
                ]
            ],

            // 2. Mountain & Adventure Tour
            [
                'title' => 'Chimgan Mountains: Hiking and Scenic Beauty',
                'short_description' => 'Trek through the stunning Chimgan mountain range with panoramic views and outdoor activities',
                'long_description' => '<p>Escape the city and immerse yourself in the natural beauty of the Chimgan Mountains. This adventure tour combines hiking, photography, and cultural experiences in one of Uzbekistan\'s most beautiful natural landscapes.</p><p>Perfect for nature lovers and adventure enthusiasts, this tour offers various difficulty levels and breathtaking vistas of snow-capped peaks and lush valleys.</p>',
                'duration_days' => 1,
                'duration_text' => '8 hours',
                'price_per_person' => 75.00,
                'currency' => 'USD',
                'max_guests' => 12,
                'min_guests' => 2,
                'city_id' => $tashkentRegion->id,
                'is_active' => true,
                'city' => $tashkentRegion,
                'categories' => [$mountain],
                'hero_image' => 'images/tours/chimgan/hero.webp',
                'highlights' => [
                    'Pichkak Valley - Perfect for hiking and photography',
                    'Big Chimgan Peak - Highest point in the range (3309m)',
                    'Crystal clear mountain streams',
                    'Traditional yurt stay option',
                    'Sunset views over the mountains'
                ],
                'included_items' => [
                    'Round-trip transportation from Tashkent',
                    'Professional mountain guide',
            'Hiking equipment (if needed)',
                    'Lunch at a mountain guesthouse',
                    'First aid kit and safety briefing'
                ],
                'excluded_items' => [
                    'Personal hiking gear',
                    'Additional snacks',
                    'Professional photography equipment'
                ]
            ],

            // 3. Family & Educational Tour
            [
                'title' => 'Bukhara for Families: Interactive Historical Adventure',
                'short_description' => 'A kid-friendly tour of Bukhara with interactive activities and educational games',
                'long_description' => '<p>Make history come alive for the whole family! This specially designed tour combines education with fun activities that will captivate both children and adults. Learn about the Silk Road through interactive storytelling, treasure hunts, and hands-on experiences.</p><p>Children will love the scavenger hunts through ancient monuments, while parents appreciate the rich historical content and beautiful architecture.</p>',
                'duration_days' => 1,
                'duration_text' => '5 hours',
                'price_per_person' => 55.00,
                'currency' => 'USD',
                'max_guests' => 15,
                'min_guests' => 2,
                'city_id' => $bukhara->id,
                'is_active' => true,
                'city' => $bukhara,
                'categories' => [$family],
                'hero_image' => 'images/tours/bukhara-families/hero.webp',
                'highlights' => [
                    'Interactive treasure hunt at Ark Fortress',
                    'Traditional craft workshop for kids',
                    'Poetry and storytelling session',
                    'Minaret climbing challenge (safe route)',
                    'Family photos in traditional costumes'
                ],
                'included_items' => [
                    'Family-friendly guide with kids experience',
                    'All entrance fees',
                    'Craft workshop materials',
                    'Traditional costume photos',
                    'Small gifts for children'
                ],
                'excluded_items' => [
                    'Lunch (child-friendly restaurants recommended)',
                    'Additional costume changes',
                    'Professional family photography'
                ]
            ],

            // 4. Desert & Nomadic Tour
            [
                'title' => 'Kyzlkum Desert: Nomadic Life Experience',
                'short_description' => 'Experience authentic nomadic lifestyle in the vast Kyzlkum Desert with camel trekking and traditional camps',
                'long_description' => '<p>Journey into the heart of the Kyzlkum Desert, one of the world\'s largest deserts, and experience the timeless traditions of nomadic life. This immersive tour offers a rare glimpse into the customs and culture of Central Asian nomads.</p><p>Sleep under the stars in traditional yurts, learn traditional crafts, and participate in daily nomadic activities. This is an authentic cultural exchange you\'ll never forget.</p>',
                'duration_days' => 2,
                'duration_text' => '2 Days / 1 Night',
                'price_per_person' => 180.00,
                'currency' => 'USD',
                'max_guests' => 10,
                'min_guests' => 2,
                'city_id' => $navoi->id,
                'is_active' => true,
                'city' => $navoi,
                'categories' => [$desert],
                'hero_image' => 'images/tours/desert/hero.webp',
                'highlights' => [
                    'Camel trekking at sunset',
                    'Traditional yurt camping',
                    'Nomadic family interaction',
                    'Traditional music and dance',
                    'Stargazing in pristine darkness',
                    'Desert sunrise and sunset'
                ],
                'included_items' => [
                    'All transportation including 4WD vehicles',
                    'Overnight accommodation in traditional yurt',
                    'All meals (traditional nomadic cuisine)',
                    'Camel riding experience',
                    'Cultural activities and workshops'
                ],
                'excluded_items' => [
                    'Personal camping gear',
                    'Additional beverages',
                    'Optional professional desert photography'
                ]
            ],

            // 5. City Walks Tour
            [
                'title' => 'Tashkent Modern: A City Walking Tour',
                'short_description' => 'Discover the modern face of Uzbekistan\'s capital with metro, malls, and contemporary architecture',
                'long_description' => '<p>Experience the contrast between old and new in Tashkent, Central Asia\'s most modern city. This walking tour showcases the capital\'s contemporary architecture, efficient metro system, and vibrant urban life.</p><p>From Soviet-era monuments to gleaming shopping centers, witness how Uzbekistan balances its rich past with an ambitious future.</p>',
                'duration_days' => 1,
                'duration_text' => '4 hours',
                'price_per_person' => 40.00,
                'currency' => 'USD',
                'max_guests' => 10,
                'min_guests' => 1,
                'city_id' => $tashkent->id,
                'is_active' => true,
                'city' => $tashkent,
                'categories' => [$cityWalks],
                'hero_image' => 'images/tours/tashkent-modern/hero.webp',
                'highlights' => [
                    'Tashkent Metro - Most beautiful in the world',
                    'Independence Square and government district',
                    'Modern shopping and business centers',
                    'Contemporary art galleries',
                    'Chorsu Bazaar - Traditional meets modern'
                ],
                'included_items' => [
                    'Metro tickets for the tour',
                    'English-speaking urban guide',
                    'Tips on using public transportation',
                    'Bottled water',
                    'City map and recommendations'
                ],
                'excluded_items' => [
                    'Meals',
                    'Additional metro rides',
                    'Shopping expenses'
                ]
            ],

            // 6. Food & Craft Tour
            [
                'title' => 'Samarkand Culinary & Craft Heritage',
                'short_description' => 'Learn to cook traditional dishes and master ancient crafts in the heart of the Silk Road',
                'long_description' => '<p>Combine culinary delights with traditional craftsmanship in this unique experiential tour. Learn the secrets of Uzbek cuisine from master chefs and discover the ancient art of paper-making and pottery that made Samarkand famous.</p><p>Take home not just memories, but new skills and handmade souvenirs created with your own hands.</p>',
                'duration_days' => 1,
                'duration_text' => '7 hours',
                'price_per_person' => 85.00,
                'currency' => 'USD',
                'max_guests' => 8,
                'min_guests' => 2,
                'city_id' => $samarkand->id,
                'is_active' => true,
                'city' => $samarkand,
                'categories' => [$foodCraft],
                'hero_image' => 'images/tours/culinary-craft/hero.webp',
                'highlights' => [
                    'Cooking class: Plov, samsa, and somsa',
                    'Samarkand paper-making workshop',
                    'Pottery masterclass',
                    'Visit to local spice bazaar',
                    'Traditional tea ceremony',
                    'Take home your own creations'
                ],
                'included_items' => [
                    'All cooking materials and ingredients',
                    'Craft workshop fees',
                    'Professional chef and craft instructor',
                    'Recipe book in English',
                    'All meals (what you cook!)',
                    'Packaging for your creations'
                ],
                'excluded_items' => [
                    'Additional drinks',
                    'Extra craft materials',
                    'Shipping of items home'
                ]
            ],

            // 7. Multi-day Cultural Tour
            [
                'title' => 'Complete Silk Road Heritage: 7-Day Comprehensive Tour',
                'short_description' => 'Experience all three UNESCO cities in one epic journey through Uzbekistan\'s cultural heartland',
                'long_description' => '<p>This comprehensive 7-day tour covers all major highlights of Uzbekistan in one unforgettable journey. From modern Tashkent to ancient Samarkand, Bukhara, and Khiva, experience the full spectrum of Central Asian culture and history.</p><p>Perfect for first-time visitors who want to see everything, this tour includes comfortable accommodations, expert guides, and carefully curated experiences at each destination.</p>',
                'duration_days' => 7,
                'duration_text' => '7 Days / 6 Nights',
                'price_per_person' => 1250.00,
                'currency' => 'USD',
                'max_guests' => 12,
                'min_guests' => 2,
                'city_id' => $tashkent->id,
                'is_active' => true,
                'city' => $tashkent,
                'categories' => [$cultural, $cityWalks],
                'hero_image' => 'images/tours/complete-silk-road/hero.webp',
                'highlights' => [
                    'Tashkent: Modern capital and metro system',
                    'Samarkand: Registan Square and Shah-i-Zinda',
                    'Bukhara: Historic trading center',
                    'Khiva: Medieval walled city',
                    'High-speed train travel between cities',
                    'Traditional homestay experience'
                ],
                'included_items' => [
                    '6 nights accommodation (4-star hotels)',
                    'All transportation including high-speed train',
                    'Daily breakfast and 4 dinners',
                    'Expert English-speaking guide throughout',
                    'All entrance fees',
                    'Airport transfers'
                ],
                'excluded_items' => [
                    'Flights to/from Uzbekistan',
                    'Lunches (flexible dining options)',
                    'Visa fees',
                    'Travel insurance',
                    'Tips and personal expenses'
                ]
            ],

            // 8. Adventure & Culture Mix
            [
                'title' => 'Khiva and Beyond: Fortresses and Legends',
                'short_description' => 'Explore the perfectly preserved medieval city of Khiva and surrounding archaeological sites',
                'long_description' => '<p>Step into a living museum in Khiva, where every stone tells a story. This tour takes you beyond the walled city to explore ancient fortresses, discover archaeological wonders, and unravel the legends that shroud this region.</p><p>From the Ichankala to Ayaz-Kala fortresses, experience the full scope of Khorezm\'s rich history and stunning desert landscapes.</p>',
                'duration_days' => 2,
                'duration_text' => '2 Days / 1 Night',
                'price_per_person' => 220.00,
                'currency' => 'USD',
                'max_guests' => 10,
                'min_guests' => 2,
                'city_id' => $khiva->id,
                'is_active' => true,
                'city' => $khiva,
                'categories' => [$cultural, $desert],
                'hero_image' => 'images/tours/khiva-fortresses/hero.webp',
                'highlights' => [
                    'Ichan-Kala: Walled inner city of Khiva',
                    'Ayaz-Kala: Ancient desert fortress',
                    'Toprak-Kala: Archaeological excavations',
                    'Traditional Khorezm cuisine',
                    'Sunset views from fortress walls',
                    'Night photography in Ichan-Kala'
                ],
                'included_items' => [
                    '1 night accommodation in Khiva',
                    'All transportation to fortresses',
                    'Expert archaeological guide',
                    'All entrance fees',
                    'Traditional dinner and breakfast',
                    'Evening photography session'
                ],
                'excluded_items' => [
                    'Lunch on day 1',
                    'Additional snacks and drinks',
                    'Professional photography services'
                ]
            ]
        ];

        foreach ($tours as $tourData) {
            $city = $tourData['city'];
            $categories = $tourData['categories'];
            unset($tourData['city'], $tourData['categories']);

            $tour = Tour::create($tourData);
            $categoryIds = array_map(function($cat) { return $cat->id; }, $categories);
            $tour->categories()->attach($categoryIds);

            $this->line("Created tour: {$tour->title}");
        }
    }

    private function generateBlogPosts()
    {
        $blogCategories = BlogCategory::all()->keyBy('slug');

        $posts = [
            [
                'category_id' => $blogCategories['destinations']->id,
                'title' => 'The Magnificent Registan Square: A Photographer\'s Paradise',
                'slug' => 'registan-square-photographers-guide',
                'excerpt' => 'Capture the perfect shots of Samarkand\'s crown jewel with our comprehensive photography guide to Registan Square.',
                'content' => '<p>Registan Square in Samarkand is arguably one of the most photogenic places on Earth. The trio of madrasahs - Ulugh Beg, Tilya-Kori, and Sher-Dor - creates a perfect backdrop that has captivated photographers for centuries.</p>

<h2>Best Time to Visit</h2>
<p>The golden hours of sunrise and sunset offer the most dramatic lighting. Arrive at 7 AM for sunrise to have the square almost to yourself. For sunset, position yourself to capture the warm light bouncing off the intricate tilework.</p>

<h2>Photography Tips</h2>
<ul>
<li>Use a wide-angle lens (16-24mm) to capture the full grandeur</li>
<li>Look up! The mosaics and tile patterns on the ceilings are incredible</li>
<li>Include visitors in your shots for scale</li>
<li>Try different angles - the square looks different from each corner</li>
</ul>

<h2>What to Look For</h2>
<p>Beyond the obvious grand facade, notice the subtle details: calligraphic inscriptions, geometric patterns, and the play of light and shadow through the iwans (arches).</p>

<p>Remember to respect the sacredness of this place - it is not just a tourist attraction, but an active place of worship and learning.</p>',
                'featured_image' => '/images/blog/registan-photography.jpg',
                'author_name' => 'Akmal Karimov',
                'reading_time' => 5,
                'view_count' => 89,
                'is_featured' => false,
                'is_published' => true,
                'published_at' => now()->subDays(3),
                'meta_title' => 'Registan Square Photography Guide | Samarkand',
                'meta_description' => 'Professional photography tips for capturing the best shots of Registan Square in Samarkand, Uzbekistan.'
            ],
            [
                'category_id' => $blogCategories['travel-tips']->id,
                'title' => 'Uzbekistan on a Budget: How to Travel Cheap Without Missing Out',
                'slug' => 'uzbekistan-budget-travel-guide',
                'excerpt' => 'Travel through Uzbekistan without breaking the bank. Our budget travel guide reveals money-saving tips and tricks.',
                'content' => '<p>Uzbekistan is one of the most affordable destinations in Central Asia, but you can make your money go even further with these insider tips.</p>

<h2>Money-Saving Tips</h2>
<p>Use shared taxis (marshrutkas) instead of private transfers - they cost 1/10th the price and are just as safe. Stay in guesthouses instead of hotels for a more authentic experience and better rates. Eat where locals eat - street food and small cafes offer delicious meals for just $2-3.</p>

<h2>Free and Cheap Attractions</h2>
<p>Many of Uzbekistan\'s most beautiful sites are free or very low-cost. Wander through the old cities of Samarkand, Bukhara, and Khiva without spending a dime. The metro in Tashkent is not only efficient but a work of art - and it only costs 30 cents per ride.</p>

<h2>Budget Breakdown</h2>
<p>You can comfortably travel in Uzbekistan for $30-50 per day including accommodation, food, and local transport. International flights will be your biggest expense.</p>

<p>Don\'t let budget constraints stop you from experiencing this incredible country!</p>',
                'featured_image' => '/images/blog/budget-uzbekistan.jpg',
                'author_name' => 'Travel Budget Team',
                'reading_time' => 6,
                'view_count' => 167,
                'is_featured' => true,
                'is_published' => true,
                'published_at' => now()->subDays(6),
                'meta_title' => 'Uzbekistan Budget Travel Guide | Save Money 2025',
                'meta_description' => 'Complete guide to traveling Uzbekistan on a budget. Tips for saving money on accommodation, food, and transport.'
            ],
            [
                'category_id' => $blogCategories['culture-history']->id,
                'title' => 'Timur the Conqueror: How One Man Shaped Central Asia',
                'slug' => 'timur-conqueror-central-asia-history',
                'excerpt' => 'Explore the life and legacy of Timur (Tamerlane), the great conqueror who built an empire and transformed Samarkand into a world capital.',
                'content' => '<p>Sayings "Timur the Lame" (Tamerlane) - one of history\'s most successful conquerors, built an empire that stretched from Turkey to India. But beyond the conquests, what was his real legacy?</p>

<h2>The Rise of an Empire</h2>
<p>Born in 1336 near Samarkand, Timur united the warring tribes of Central Asia and launched a series of campaigns that created the last great nomadic empire. His army never lost a battle in 35 years of warfare.</p>

<h2>Samarkand: The Capital of the World</h2>
<p>Timur transformed Samarkand into the most beautiful city in the world, bringing artisans, scholars, and treasures from across his empire. The city became a center of art, culture, and learning that rivaled any in the world.</p>

<h2>Architectural Legacy</h2>
<p>His legacy lives on in the stunning monuments of Samarkand. Gur-e-Amir, Shah-i-Zinda, and parts of Registan all date to his reign, showcasing the incredible artistic achievements of his era.</p>

<h2>A Complex Figure</h2>
<p>While ruthless in war, Timur also patronized the arts, astronomy, and philosophy. His grandson Ulugh Beg built one of the world\'s first observatories.</p>

<p>Understanding Timur helps us appreciate the monuments we see today and the sophisticated civilization he created.</p>',
                'featured_image' => '/images/blog/timur-history.jpg',
                'author_name' => 'Dr. Sanjar Alimov',
                'reading_time' => 8,
                'view_count' => 234,
                'is_featured' => false,
                'is_published' => true,
                'published_at' => now()->subDays(10),
                'meta_title' => 'Timur Tamerlane: The Conqueror Who Built Samarkand',
                'meta_description' => 'Discover the life and legacy of Timur (Tamerlane), the great conqueror who shaped Central Asia and built Samarkand.'
            ],
            [
                'category_id' => $blogCategories['food-cuisine']->id,
                'title' => 'Tea Culture in Uzbekistan: More Than Just a Drink',
                'slug' => 'tea-culture-uzbekistan-traditions',
                'excerpt' => 'Discover the rich tradition of tea culture in Uzbekistan and why accepting a cup of tea is more than just good manners.',
                'content' => '<p>In Uzbekistan, tea (choy) is far more than a beverage - it\'s a symbol of hospitality, respect, and friendship. Understanding tea culture is key to understanding Uzbek society.</p>

<h2>The Sacred Ritual</h2>
<p>When you visit someone\'s home, the first thing offered will always be tea. Refusing it is considered rude. The tea is poured into small bowls, never full - indicating the host\'s care and attention.</p>

<h2>Types of Tea</h2>
<p>Green tea (yashil choy) is the most traditional and is served with almost every meal. Black tea (qora choy) is also popular, often flavored with bergamot or other aromatics. In desert regions, you\'ll find salted tea, a unique regional variation.</p>

<h2>Tea Houses (Choyhana)</h2>
<p>Traditional tea houses are social centers where men gather to discuss business, politics, and life. They\'re excellent places to experience authentic local culture and enjoy tea with simple snacks.</p>

<h2>Etiquette</h2>
<p>Always accept tea when offered. Thank the host with "rahmat" (thank you). It\'s polite to pour tea for others before yourself. Never refuse a second cup - it shows appreciation for the hospitality.</p>

<p>Next time you\'re in Uzbekistan, slow down, accept the tea, and let the conversation flow. You\'ll discover the true heart of Uzbek hospitality.</p>',
                'featured_image' => '/images/blog/tea-culture.jpg',
                'author_name' => 'Gulbahor Ismailova',
                'reading_time' => 5,
                'view_count' => 145,
                'is_featured' => false,
                'is_published' => true,
                'published_at' => now()->subDays(12),
                'meta_title' => 'Tea Culture in Uzbekistan | Traditional Customs',
                'meta_description' => 'Learn about the rich tea culture and traditions in Uzbekistan, from tea houses to hospitality customs.'
            ],
            [
                'category_id' => $blogCategories['destinations']->id,
                'title' => 'Hidden Gems of Bukhara: Beyond the Tourist Trail',
                'slug' => 'hidden-gems-bukhara-secret-places',
                'excerpt' => 'Discover the lesser-known treasures of Bukhara that most tourists miss, from quiet courtyards to secret gardens.',
                'content' => '<p>While most visitors stick to the main monuments, Bukhara has countless hidden corners waiting to be discovered. These secret spots offer a more intimate experience of this ancient city.</p>

<h2>Quiet Courtyards of the Old City</h2>
<p>Wander away from the main pathways and you\'ll find peaceful courtyards where families have lived for generations. These residential areas are often decorated with beautiful wooden doors and offer authentic glimpses of local life.</p>

<h2>The Secret Garden Behind Kalyan Minaret</h2>
<p>Most people photograph the Kalyan Minaret from the front, but there\'s a small garden behind it where you can sit in quiet contemplation, away from the crowds. Local families bring their children here in the evenings.</p>

<h2>Underground Water Channels</h2>
<p>Ancient qanat systems still run beneath Bukhara. Some sections are accessible and offer a fascinating glimpse into the engineering prowess of medieval Central Asia.</p>

<h2>Sunset from the Ark Fortress Walls</h2>
<p>While popular during the day, the fortress walls at sunset are magical. Climb to the upper levels for panoramic views of the old city bathed in golden light.</p>

<h2>Local Artisan Workshops</h2>
<p>Seek out traditional craftspeople working in small workshops. You might find a woodcarver, a pottery studio, or a silk weaver creating beautiful works using centuries-old techniques.</p>

<p>These hidden gems make Bukhara a place that rewards exploration and curiosity.</p>',
                'featured_image' => '/images/blog/bukhara-secrets.jpg',
                'author_name' => 'Dilorom Rakhimova',
                'reading_time' => 7,
                'view_count' => 198,
                'is_featured' => true,
                'is_published' => true,
                'published_at' => now()->subDays(14),
                'meta_title' => 'Hidden Gems of Bukhara | Secret Places to Visit',
                'meta_description' => 'Discover the hidden treasures of Bukhara beyond the main tourist sites. Local secrets and quiet places to explore.'
            ],
            [
                'category_id' => $blogCategories['travel-tips']->id,
                'title' => 'Dress Code in Uzbekistan: What to Wear and Cultural Considerations',
                'slug' => 'uzbekistan-dress-code-cultural-guide',
                'excerpt' => 'Your complete guide to appropriate dress in Uzbekistan, including tips for visiting mosques and cultural sites.',
                'content' => '<p>Uzbekistan is a predominantly Muslim country with conservative values, so dressing modestly is important, especially when visiting religious sites and interacting with local communities.</p>

<h2>For Women</h2>
<p>Women should dress modestly with shoulders and knees covered. Loose-fitting clothing is preferred. When visiting mosques, bring a scarf to cover your hair (though it\'s not mandatory for tourists). Avoid tight, revealing, or transparent clothing. Maxi dresses, long skirts, and loose pants are all excellent choices.</p>

<h2>For Men</h2>
<p>Men should avoid going shirtless in public. Shorts should be knee-length or longer. Tank tops are acceptable in hot weather, but when visiting religious sites, wear a shirt with sleeves.</p>

<h2>Footwear</h2>
<p>Bring comfortable shoes that are easy to remove, as you\'ll need to take them off when entering mosques, madrasahs, and people\'s homes. Slip-on shoes or sandals work well.</p>

<h2>Seasonal Considerations</h2>
<p>In summer, light, breathable fabrics in light colors help reflect the sun. Long sleeves protect against sunburn. In winter, dress in layers and bring a warm coat, hat, and gloves.</p>

<h2>Beach and Resort Wear</h2>
<p>If staying at resorts with pools, swimwear is acceptable only at the pool area. Cover up with a robe or loose clothing when moving around the resort.</p>

<p>Remember, dressing respectfully shows appreciation for local culture and will be met with warmth and respect in return.</p>',
                'featured_image' => '/images/blog/dress-code.jpg',
                'author_name' => 'Cultural Guide Team',
                'reading_time' => 4,
                'view_count' => 123,
                'is_featured' => false,
                'is_published' => true,
                'published_at' => now()->subDays(16),
                'meta_title' => 'Uzbekistan Dress Code Guide | What to Wear',
                'meta_description' => 'Complete guide to appropriate dress in Uzbekistan. What to wear when visiting mosques, cities, and cultural sites.'
            ],
            [
                'category_id' => $blogCategories['culture-history']->id,
                'title' => 'The Art of Uzbek Carpets: Stories Woven in Wool',
                'slug' => 'uzbek-carpet-weaving-traditions',
                'excerpt' => 'Explore the ancient art of carpet weaving in Uzbekistan, from traditional patterns to modern artisans keeping the craft alive.',
                'content' => '<p>Uzbek carpets are more than just floor coverings - they\'re canvases that tell stories, preserve traditions, and showcase the incredible skill of local artisans. Each pattern has meaning, each color tells a tale.</p>

<h2>History and Heritage</h2>
<p>Carpet weaving in Central Asia dates back over 2,500 years. Silk Road traders carried these precious textiles across continents, making them among the most sought-after luxury items in the ancient world.</p>

<h2>Regional Styles</h2>
<p>Each region has its own distinct style. Bukhara carpets are known for their geometric patterns and deep reds. Samarkand carpets often feature more floral motifs. Khiva carpets are famous for their diamond patterns and central medallions.</p>

<h2>Symbolic Meanings</h2>
<p>Traditional patterns aren\'t just decorative - they carry deep meaning. The diamond shape represents a pomegranate blossom and fertility. Eight-pointed stars symbolize prosperity. Running water patterns represent life and abundance.</p>

<h2>Traditional Process</h2>
<p>Creating a single carpet can take months or even years. Artisans use natural dyes from plants, minerals, and insects. The wool comes from local sheep, and the silk for premium carpets is harvested from silkworms raised specifically for this purpose.</p>

<h2>Buying Authentic Carpets</h2>
<p>When buying a carpet, look for natural dyes, hand-spun wool, and tight knots. The back should be hand-stitched. Ask about the weaver\'s story - authentic carpets come with their own tale.</p>

<p>Taking home a handwoven carpet means preserving a piece of Uzbek heritage.</p>',
                'featured_image' => '/images/blog/carpet-weaving.jpg',
                'author_name' => 'Maksuda Akhmedova',
                'reading_time' => 9,
                'view_count' => 176,
                'is_featured' => false,
                'is_published' => true,
                'published_at' => now()->subDays(18),
                'meta_title' => 'Uzbek Carpet Weaving Traditions | History and Craft',
                'meta_description' => 'Discover the ancient art of carpet weaving in Uzbekistan. Learn about traditional patterns, symbols, and techniques.'
            ],
            [
                'category_id' => $blogCategories['destinations']->id,
                'title' => 'Khiva After Dark: Night Photography in the Ancient City',
                'slug' => 'khiva-night-photography-guide',
                'excerpt' => 'Capture the magic of Ichan-Kala at night with our comprehensive guide to night photography in Khiva.',
                'content' => '<p>When the sun sets and the day tourists leave, Khiva transforms into a photographer\'s dream. The ancient walls of Ichan-Kala are illuminated, creating a magical atmosphere perfect for night photography.</p>

<h2>Best Spots for Night Shots</h2>
<p>The Kalyan Minaret looks stunning when lit up at night. The square facing the Islam Khoja minaret offers a wide shot with multiple illuminated monuments. Don\'t miss the view from the city walls - climb the western gate for panoramic night vistas.</p>

<h2>Photography Equipment</h2>
<p>A tripod is essential for night photography. A wide-angle lens (14-24mm) captures the grand architecture. Use manual focus and long exposures (10-30 seconds) to capture the beautiful illumination. A remote shutter release prevents camera shake.</p>

<h2>Camera Settings</h2>
<p>Start with ISO 800-1600, aperture f/2.8-f/4, and long exposures. Play with different white balances to create mood - daylight gives a natural look, tungsten creates warmth, and custom white balance offers creative freedom.</p>

<h2>Safety and Etiquette</h2>
<p>The old city is safe at night, but bring a flashlight for navigation. Respect sleeping residents - keep noise down. Some areas may be closed after 10 PM, so check with your hotel about curfews.</p>

<h2>Best Time to Visit</h2>
<p>Arrive 30 minutes after sunset when the lights first come on. Stay until the last lights go off around midnight. The blue hour just after sunset and before the lights fully illuminate offers a unique balance of natural and artificial light.</p>

<p>Night photography in Khiva offers a completely different perspective on this ancient city. The empty streets and illuminated monuments create an otherworldly atmosphere you won\'t find anywhere else.</p>',
                'featured_image' => '/images/blog/khiva-night.jpg',
                'author_name' => 'Bekhzod Yuldoshev',
                'reading_time' => 6,
                'view_count' => 212,
                'is_featured' => true,
                'is_published' => true,
                'published_at' => now()->subDays(20),
                'meta_title' => 'Khiva Night Photography Guide | Best Spots & Tips',
                'meta_description' => 'Complete guide to night photography in Khiva\'s Ichan-Kala. Best spots, camera settings, and tips for stunning shots.'
            ]
        ];

        foreach ($posts as $postData) {
            $post = BlogPost::create($postData);

            // Attach tags
            $tags = [];
            if (str_contains($postData['title'], 'Uzbekistan') || str_contains($postData['content'], 'Uzbekistan')) $tags[] = 1; // Uzbekistan
            if (str_contains($postData['content'], 'Samarkand')) $tags[] = 2; // Samarkand
            if (str_contains($postData['content'], 'Bukhara')) $tags[] = 3; // Bukhara
            if (str_contains($postData['content'], 'Khiva')) $tags[] = 4; // Khiva
            if (str_contains($postData['content'], 'Silk Road')) $tags[] = 6; // Silk Road
            if (str_contains($postData['content'], 'culture') || str_contains($postData['content'], 'Culture')) $tags[] = 7; // Culture
            if (str_contains($postData['content'], 'history') || str_contains($postData['content'], 'History')) $tags[] = 8; // History
            if (str_contains($postData['content'], 'food') || str_contains($postData['content'], 'cuisine') || str_contains($postData['content'], 'tea')) $tags[] = 9; // Food
            if (str_contains($postData['content'], 'architecture') || str_contains($postData['content'], 'monument')) $tags[] = 10; // Architecture
            if (str_contains($postData['content'], 'UNESCO')) $tags[] = 11; // UNESCO
            if ($blogCategories['travel-tips']->id == $postData['category_id']) $tags[] = 12; // Travel Guide

            if (!empty($tags)) {
                $post->tags()->attach($tags);
            }

            $this->line("Created blog post: {$post->title}");
        }
    }
}
