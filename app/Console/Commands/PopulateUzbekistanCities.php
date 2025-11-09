<?php

namespace App\Console\Commands;

use App\Models\City;
use Illuminate\Console\Command;

class PopulateUzbekistanCities extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'populate:cities';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Populate database with major cities of Uzbekistan';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting to populate Uzbekistan cities...');

        // Clear existing cities with foreign key handling
        \DB::statement('SET FOREIGN_KEY_CHECKS=0');
        City::truncate();
        \DB::statement('SET FOREIGN_KEY_CHECKS=1');
        $this->info('Cleared existing cities');

        $cities = $this->getCitiesData();

        foreach ($cities as $cityData) {
            $city = City::create($cityData);
            $this->line("  ✓ Created: {$city->name}");
        }

        $this->info('All cities have been created successfully!');
        $this->info('Total cities: ' . City::count());
        $this->info('Featured cities: ' . City::featured()->count());

        return 0;
    }

    private function getCitiesData()
    {
        return [
            // Major Tourist Destinations (Featured)
            [
                'name' => 'Tashkent',
                'slug' => 'tashkent',
                'tagline' => 'Modern Capital of Uzbekistan',
                'description' => 'The vibrant capital and largest city of Uzbekistan, blending Soviet-era architecture with modern skyscrapers.',
                'short_description' => 'Modern capital city with beautiful metro, museums, and contemporary architecture.',
                'long_description' => '<p>Tashkent, the capital of Uzbekistan, is Central Asia\'s most populous city and a fascinating blend of ancient heritage and modern development. As the political, economic, and cultural heart of the country, Tashkent offers visitors a unique perspective on Uzbekistan\'s journey from Silk Road crossroads to independent nation.</p>

<p>The city is renowned for its impressive metro system, considered one of the most beautiful in the world, with stations featuring marble, mosaics, and chandeliers. Beyond its modern face, Tashkent also preserves important Islamic sites like the Hazrat Imam Complex, which houses the world\'s oldest Quran.</p>

<p>Visitors can explore bustling bazaars, world-class museums, contemporary art galleries, and enjoy a vibrant culinary scene. The city serves as the main gateway for most international travelers and an excellent starting point for exploring the rest of the country.</p>',
                'images' => [
                    ['path' => 'images/cities/tashkent/metro.jpg', 'alt' => 'Tashkent Metro Station'],
                    ['path' => 'images/cities/tashkent/independence-square.jpg', 'alt' => 'Independence Square'],
                    ['path' => 'images/cities/tashkent/hazrat-imam.jpg', 'alt' => 'Hazrat Imam Complex'],
                ],
                'featured_image' => 'images/cities/tashkent/hero.jpg',
                'hero_image' => 'images/cities/tashkent/hero-wide.jpg',
                'latitude' => 41.2995,
                'longitude' => 69.2401,
                'display_order' => 1,
                'is_featured' => true,
                'is_active' => true,
                'meta_title' => 'Tashkent - Modern Capital of Uzbekistan | Travel Guide',
                'meta_description' => 'Discover Tashkent, the modern capital of Uzbekistan. Explore beautiful metro stations, museums, Islamic heritage, and contemporary attractions.',
            ],
            [
                'name' => 'Samarkand',
                'slug' => 'samarkand',
                'tagline' => 'Pearl of the Islamic World',
                'description' => 'The legendary Silk Road city famous for Registan Square and Timurid architecture.',
                'short_description' => 'Ancient Silk Road city with stunning Islamic architecture and UNESCO World Heritage sites.',
                'long_description' => '<p>Samarkand, known as the "Pearl of the Islamic World," is one of Central Asia\'s most captivating cities. This ancient metropolis, with a history spanning over 2,500 years, was the capital of Timur (Tamerlane)\'s vast empire and a major stop on the legendary Silk Road.</p>

<p>The city\'s crowning jewel is Registan Square, featuring three magnificent madrasahs from the 15th-17th centuries with stunning tilework and architecture. Other must-see attractions include the Gur-e-Amir Mausoleum (Timur\'s tomb), the Shah-i-Zinda necropolis with its avenue of blue-domed mausoleums, and the massive Bibi-Khanym Mosque.</p>

<p>Today, Samarkand is a UNESCO World Heritage site and remains one of Uzbekistan\'s most popular destinations, offering visitors a journey through centuries of Central Asian history, culture, and architectural brilliance.</p>',
                'images' => [
                    ['path' => 'images/cities/samarkand/registan.jpg', 'alt' => 'Registan Square at sunset'],
                    ['path' => 'images/cities/samarkand/gur-emir.jpg', 'alt' => 'Gur-e-Amir Mausoleum'],
                    ['path' => 'images/cities/samarkand/shah-i-zinda.jpg', 'alt' => 'Shah-i-Zinda Necropolis'],
                ],
                'featured_image' => 'images/cities/samarkand/hero.jpg',
                'hero_image' => 'images/cities/samarkand/hero-wide.jpg',
                'latitude' => 39.6542,
                'longitude' => 66.9597,
                'display_order' => 2,
                'is_featured' => true,
                'is_active' => true,
                'meta_title' => 'Samarkand - Pearl of the Islamic World | UNESCO City',
                'meta_description' => 'Explore Samarkand, the legendary Silk Road city. Visit Registan Square, Gur-e-Amir, and Shah-i-Zinda. UNESCO World Heritage site.',
            ],
            [
                'name' => 'Bukhara',
                'slug' => 'bukhara',
                'tagline' => 'Living Museum of the Silk Road',
                'description' => 'A perfectly preserved medieval city with over 140 architectural monuments.',
                'short_description' => 'Medieval Silk Road city with authentic Islamic architecture and living traditions.',
                'long_description' => '<p>Bukhara is a captivating city where the past comes alive. This UNESCO World Heritage site is one of the best-preserved examples of Islamic architecture in Central Asia, with over 140 architectural monuments dating from the 9th century onwards.</p>

<p>The city\'s Old Town remains a living, breathing community where traditional crafts are still practiced and ancient trading traditions continue. Key attractions include the imposing Ark Fortress, the towering Kalyan Minaret, the beautiful Samanid Mausoleum, and the peaceful Lab-i Hauz complex around a historic pond.</p>

<p>Bukhara was once one of the most important trading centers on the Silk Road and a center of Islamic learning. Today, it offers visitors an authentic glimpse into Central Asian culture, with traditional craft workshops, bustling bazaars, and spiritual sites that have been venerated for centuries.</p>',
                'images' => [
                    ['path' => 'images/cities/bukhara/ark-fortress.jpg', 'alt' => 'Bukhara Ark Fortress'],
                    ['path' => 'images/cities/bukhara/kalyan-minaret.jpg', 'alt' => 'Kalyan Minaret'],
                    ['path' => 'images/cities/bukhara/lab-i-hauz.jpg', 'alt' => 'Lab-i Hauz Complex'],
                ],
                'featured_image' => 'images/cities/bukhara/hero.jpg',
                'hero_image' => 'images/cities/bukhara/hero-wide.jpg',
                'latitude' => 39.7670,
                'longitude' => 64.4231,
                'display_order' => 3,
                'is_featured' => true,
                'is_active' => true,
                'meta_title' => 'Bukhara - Living Museum of the Silk Road | UNESCO Heritage',
                'meta_description' => 'Visit Bukhara, a medieval Silk Road city with 140+ monuments. Explore Ark Fortress, Kalyan Minaret, and traditional craft workshops.',
            ],
            [
                'name' => 'Khiva',
                'slug' => 'khiva',
                'tagline' => 'Walled City of Legends',
                'description' => 'A perfectly preserved medieval city within ancient walls, open-air museum.',
                'short_description' => 'Walled inner city (Ichan-Kala) with intact medieval Islamic architecture.',
                'long_description' => '<p>Khiva is a true open-air museum, with its entire inner city (Ichan-Kala) enclosed by impressive mud-brick walls. This UNESCO World Heritage site is one of the best-preserved examples of Central Asian Islamic architecture.</p>

<p>Stepping through the gates of Ichan-Kala is like traveling back in time. The walled city contains numerous madrasahs, mosques, mausoleums, palaces, and baths, all restored to their medieval glory. The city\'s attraction lies in its completeness - every corner reveals another architectural gem, from the towering Islam Khoja Minaret to the ornate Tash Khaorov Palace.</p>

<p>Once a major stop on the Silk Road and an important center of the Khanate of Khiva, Khiva today offers visitors a rare opportunity to experience a living medieval Islamic city, with traditional crafts, local workshops, and authentic cultural experiences.</p>',
                'images' => [
                    ['path' => 'images/cities/khiva/ichan-kala.jpg', 'alt' => 'Ichan-Kala walled city'],
                    ['path' => 'images/cities/khiva/kunya-ark.jpg', 'alt' => 'Kunya Ark Fortress'],
                    ['path' => 'images/cities/khiva/islam-khoja.jpg', 'alt' => 'Islam Khoja Minaret'],
                ],
                'featured_image' => 'images/cities/khiva/hero.jpg',
                'hero_image' => 'images/cities/khiva/hero-wide.jpg',
                'latitude' => 41.3775,
                'longitude' => 60.3614,
                'display_order' => 4,
                'is_featured' => true,
                'is_active' => true,
                'meta_title' => 'Khiva - Walled City of Legends | UNESCO World Heritage',
                'meta_description' => 'Discover Khiva, a perfectly preserved medieval city. Explore Ichan-Kala walled city, mosques, madrasahs, and Islamic architecture.',
            ],

            // Other Major Cities
            [
                'name' => 'Fergana',
                'slug' => 'fergana',
                'tagline' => 'Silk and Craft Heritage',
                'description' => 'Commercial hub of the Fergana Valley, famous for silk production and traditional crafts.',
                'short_description' => 'Valley city known for silk weaving, traditional crafts, and vibrant markets.',
                'long_description' => '<p>Fergana is the principal city of the Fergana Valley, Uzbekistan\'s most fertile region. This bustling commercial center is renowned for its silk industry, which dates back to ancient times along the Silk Road. The city\'s workshops still produce some of the world\'s finest silk fabrics using traditional methods passed down through generations.</p>

<p>Beyond silk, Fergana is famous for its pottery, metalwork, and other traditional crafts. The city serves as a gateway to the beautiful Fergana Valley, with its fertile landscapes, traditional villages, and warm hospitality. Modern Fergana combines industrial development with cultural preservation, making it an important stop for understanding contemporary Uzbekistan.</p>',
                'images' => [
                    ['path' => 'images/cities/fergana/registan.jpg', 'alt' => 'Fergana Registan'],
                    ['path' => 'images/cities/fergana/silk-workshop.jpg', 'alt' => 'Traditional silk workshop'],
                ],
                'featured_image' => 'images/cities/fergana/hero.jpg',
                'latitude' => 40.3843,
                'longitude' => 71.7843,
                'display_order' => 10,
                'is_featured' => false,
                'is_active' => true,
                'meta_title' => 'Fergana - Silk and Craft Heritage of Uzbekistan',
                'meta_description' => 'Visit Fergana, the heart of Fergana Valley. Discover silk production, traditional crafts, and vibrant markets.',
            ],
            [
                'name' => 'Namangan',
                'slug' => 'namangan',
                'tagline' => 'Garden City',
                'description' => 'Third-largest city in Uzbekistan, known for its orchards and agricultural production.',
                'short_description' => 'Major industrial and agricultural center known as the "Garden City".',
                'long_description' => '<p>Namangan, Uzbekistan\'s third-largest city, is affectionately known as the "Garden City" due to its abundance of orchards and green spaces. Located in the eastern part of the Fergana Valley, the city has a long history as an agricultural and industrial center.</p>

<p>The city is particularly famous for its fruit production, especially apricots and apples, which are exported throughout Central Asia. Namangan has a rich cultural heritage and is home to several important Islamic sites and historical monuments. The city\'s modern development balances industrial growth with the preservation of its natural beauty and cultural traditions.</p>',
                'images' => [
                    ['path' => 'images/cities/namangan/city-view.jpg', 'alt' => 'Namangan city view'],
                ],
                'featured_image' => 'images/cities/namangan/hero.jpg',
                'latitude' => 41.0015,
                'longitude' => 71.6726,
                'display_order' => 11,
                'is_featured' => false,
                'is_active' => true,
                'meta_title' => 'Namangan - Garden City of Uzbekistan',
                'meta_description' => 'Explore Namangan, the Garden City of Uzbekistan. Known for orchards, agriculture, and industrial development.',
            ],
            [
                'name' => 'Andijan',
                'slug' => 'andijan',
                'tagline' => 'Birthplace of the Moguls',
                'description' => 'Ancient city, birthplace of Babur and center of Islamic scholarship.',
                'short_description' => 'Birthplace of Emperor Babur and important center of Islamic culture.',
                'long_description' => '<p>Andijan is one of Uzbekistan\'s oldest cities and holds a special place in world history as the birthplace of Zahir-ud-din Muhammad Babur, who founded the Mughal Empire in India in the 16th century. This ancient city in the Fergana Valley has been an important center of Islamic culture, trade, and scholarship for over 1,500 years.</p>

<p>Today, Andijan is a modern industrial city that balances its rich historical heritage with contemporary development. The city serves as a major commercial and transportation hub in the Fergana Valley, with connections to neighboring countries. Visitors can explore historical sites, traditional bazaars, and learn about Babur\'s remarkable journey from Andijan to becoming the founder of one of India\'s greatest empires.</p>',
                'images' => [
                    ['path' => 'images/cities/andijan/babur-museum.jpg', 'alt' => 'Babur Museum'],
                ],
                'featured_image' => 'images/cities/andijan/hero.jpg',
                'latitude' => 40.7821,
                'longitude' => 72.3442,
                'display_order' => 12,
                'is_featured' => false,
                'is_active' => true,
                'meta_title' => 'Andijan - Birthplace of the Mughal Emperor Babur',
                'meta_description' => 'Discover Andijan, birthplace of Emperor Babur. Explore Islamic culture, history, and the heritage of the Mughal Empire.',
            ],
            [
                'name' => 'Nukus',
                'slug' => 'nukus',
                'tagline' => 'Gateway to the Aral Sea',
                'description' => 'Capital of Karakalpakstan, home to the world-renowned Nukus Museum of Art.',
                'short_description' => 'Capital of Karakalpakstan, famous for avant-garde art museum and desert landscapes.',
                'long_description' => '<p>Nukus is the capital of Karakalpakstan, an autonomous republic within Uzbekistan. This remote desert city is famous worldwide for the Nukus Museum of Art, which houses one of the most important collections of Russian avant-garde art, saved by the visionary curator Igor Savitsky from destruction during the Soviet era.</p>

<p>Located near the Aral Sea, Nukus serves as a gateway to one of the world\'s most dramatic environmental stories. The city also provides access to the vast Kyzylkum Desert and the geological wonders of the Ustyurt Plateau. Despite its remote location, Nukus has a unique cultural identity influenced by Karakal traditions and the area\'s complex history.</p>',
                'images' => [
                    ['path' => 'images/cities/nukus/savitsky-museum.jpg', 'alt' => 'Nukus Museum'],
                ],
                'featured_image' => 'images/cities/nukus/hero.jpg',
                'latitude' => 42.4531,
                'longitude' => 59.6103,
                'display_order' => 13,
                'is_featured' => false,
                'is_active' => true,
                'meta_title' => 'Nukus - Art and Desert Heart of Karakalpakstan',
                'meta_description' => 'Visit Nukus, capital of Karakalpakstan. Discover the Savitsky Museum and explore desert landscapes near the Aral Sea.',
            ],
            [
                'name' => 'Termez',
                'slug' => 'termez',
                'tagline' => 'Frontier City of the South',
                'description' => 'Ancient city on the border with Afghanistan, rich in Buddhist heritage.',
                'short_description' => 'Southern border city with ancient Buddhist sites and strategic importance.',
                'long_description' => '<p>Termez is one of Uzbekistan\'s southernmost cities, located on the border with Afghanistan. This ancient city has been a strategic crossroads for millennia, serving as a key point on the Silk Road and as a gateway between Central Asia and the Indian subcontinent.</p>

<p>The region is rich in Buddhist heritage, with numerous ancient monasteries and stupas dating from the 1st-3rd centuries CE, when Buddhism flourished in Central Asia. Modern Termez is an important commercial border crossing and military garrison city, serving as a bridge between cultures. The city\'s location makes it a unique place to understand the complex history of Central Asian trade and cultural exchange.</p>',
                'images' => [
                    ['path' => 'images/cities/termez/fortress.jpg', 'alt' => 'Termez Fortress'],
                ],
                'featured_image' => 'images/cities/termez/hero.jpg',
                'latitude' => 37.2242,
                'longitude' => 67.2783,
                'display_order' => 14,
                'is_featured' => false,
                'is_active' => true,
                'meta_title' => 'Termez - Ancient Gateway to the South',
                'meta_description' => 'Explore Termez, southern border city of Uzbekistan. Discover ancient Buddhist heritage and strategic Silk Road location.',
            ],
            [
                'name' => 'Gulistan',
                'slug' => 'gulistan',
                'tagline' => 'Modern Administrative Center',
                'description' => 'Capital of Syrdarya Region, modern city in the Syr Darya river valley.',
                'short_description' => 'Regional capital with modern administration and agricultural economy.',
                'long_description' => '<p>Gulistan is the administrative center of Syrdarya Region in northern Uzbekistan. The city\'s name means "flower garden" in Persian, reflecting the fertile lands of the Syr Darya river valley. As a relatively modern city, Gulistan represents contemporary Uzbekistan\'s regional development and administrative structure.</p>

<p>The city serves as an important transportation and agricultural center, with cotton and grain production forming the backbone of the local economy. Gulistan is home to several educational institutions and government facilities, making it an important regional hub. Its location along the Syr Darya River provides fertile agricultural land and strategic importance for water management in the region.</p>',
                'images' => [
                    ['path' => 'images/cities/gulistan/center.jpg', 'alt' => 'Gulistan city center'],
                ],
                'featured_image' => 'images/cities/gulistan/hero.jpg',
                'latitude' => 40.4883,
                'longitude' => 68.7842,
                'display_order' => 15,
                'is_featured' => false,
                'is_active' => true,
                'meta_title' => 'Gulistan - Regional Center of Syrdarya',
                'meta_description' => 'Visit Gulistan, capital of Syrdarya Region. Modern administrative center in the fertile river valley.',
            ],
            [
                'name' => 'Jizzakh',
                'slug' => 'jizzakh',
                'tagline' => 'Gateway to the Mountains',
                'description' => 'City serving as a gateway to the Zeravshan mountains and natural attractions.',
                'short_description' => 'Mountain gateway city with access to scenic valleys and outdoor activities.',
                'long_description' => '<p>Jizzakh is a strategic city in central Uzbekistan, serving as an important gateway to the Zeravshan mountain range. The city has historical significance as a staging point for travelers and traders heading north-south through Central Asia. Its location at the edge of the mountains makes it a natural starting point for exploring Uzbekistan\'s natural attractions.</p>

<p>The surrounding region offers beautiful mountain landscapes, hiking opportunities, and the chance to experience rural Uzbek life. Jizzakh combines modern urban development with proximity to natural wonders, making it an attractive destination for adventure travelers and those seeking to explore beyond the major historical cities. The city also serves as an important agricultural center for the region.</p>',
                'images' => [
                    ['path' => 'images/cities/jizzakh/mountains.jpg', 'alt' => 'Mountains near Jizzakh'],
                ],
                'featured_image' => 'images/cities/jizzakh/hero.jpg',
                'latitude' => 40.1158,
                'longitude' => 68.7842,
                'display_order' => 16,
                'is_featured' => false,
                'is_active' => true,
                'meta_title' => 'Jizzakh - Gateway to Zeravshan Mountains',
                'meta_description' => 'Discover Jizzakh, gateway to Uzbekistan\'s mountains. Explore scenic valleys, hiking, and natural attractions.',
            ],
            [
                'name' => 'Kokand',
                'slug' => 'kokand',
                'tagline' => 'Former Khanate Capital',
                'description' => 'Historical capital of the Fergana Valley, with beautiful palaces and mosques.',
                'short_description' => 'Historical capital with impressive Islamic architecture and royal palaces.',
                'long_description' => '<p>Kokand was once the capital of the powerful Kokand Khanate, which controlled the Fergana Valley in the 18th-19th centuries. The city is a testament to Central Asian royal architecture, with several impressive palaces and mosques that reflect the wealth and power of the former khanate.</p>

<p>The city\'s main attraction is the Khudoyar Khan Palace, a magnificent complex that showcases the architectural style of the Kokand rulers. The city also features numerous madrasahs, mosques, and the colorful Kokand Central Bazaar, one of the largest markets in Central Asia. Today, Kokand remains an important commercial and cultural center in the Fergana Valley, preserving its rich historical heritage while embracing modern development.</p>',
                'images' => [
                    ['path' => 'images/cities/kokand/palace.jpg', 'alt' => 'Khudoyar Khan Palace'],
                ],
                'featured_image' => 'images/cities/kokand/hero.jpg',
                'latitude' => 40.5033,
                'longitude' => 70.2783,
                'display_order' => 17,
                'is_featured' => false,
                'is_active' => true,
                'meta_title' => 'Kokand - Former Capital of the Kokand Khanate',
                'meta_description' => 'Visit Kokand, former Khanate capital. Explore Khudoyar Khan Palace, historic mosques, and traditional bazaars.',
            ],
            [
                'name' => 'Navoi',
                'slug' => 'navoi',
                'tagline' => 'Industrial Heart of Uzbekistan',
                'description' => 'Modern industrial city and major center of uranium and gold mining.',
                'short_description' => 'Industrial city in the Kyzylkum Desert, center of mining and metallurgy.',
                'long_description' => '<p>Navoi is one of Uzbekistan\'s most important industrial cities, located in the heart of the Kyzylkum Desert. Named after the great poet and statesman Alisher Navoi, the city is the administrative center of Navoi Region and a key industrial hub. The city\'s economy is based on mining, metallurgy, and chemical production.</p>

<p>Despite its industrial focus, Navoi has a well-planned urban structure with wide boulevards, parks, and cultural facilities. The city serves as a transportation hub for the region and is an important stop for travelers heading to the Kyzylkum Desert and surrounding areas. Navoi represents modern Uzbekistan\'s industrial development and economic diversification, with plans for further growth in metallurgy, chemical production, and renewable energy.</p>',
                'images' => [
                    ['path' => 'images/cities/navoi/industrial.jpg', 'alt' => 'Navoi industrial area'],
                ],
                'featured_image' => 'images/cities/navoi/hero.jpg',
                'latitude' => 41.0015,
                'longitude' => 64.2081,
                'display_order' => 18,
                'is_featured' => false,
                'is_active' => true,
                'meta_title' => 'Navoi - Industrial Heart of Uzbekistan',
                'meta_description' => 'Discover Navoi, Uzbekistan\'s industrial center. Located in Kyzylkum Desert, major mining and metallurgy hub.',
            ],
        ];
    }
}

