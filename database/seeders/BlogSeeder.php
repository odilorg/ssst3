<?php

namespace Database\Seeders;

use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\BlogTag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BlogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Categories
        $categories = [
            [
                'name' => 'Travel Tips',
                'slug' => 'travel-tips',
                'description' => 'Essential tips and advice for traveling in Uzbekistan and Central Asia',
                'is_active' => true,
                'display_order' => 1,
            ],
            [
                'name' => 'Destinations',
                'slug' => 'destinations',
                'description' => 'Discover amazing destinations across Uzbekistan',
                'is_active' => true,
                'display_order' => 2,
            ],
            [
                'name' => 'Culture & History',
                'slug' => 'culture-history',
                'description' => 'Explore the rich cultural heritage and history of the Silk Road',
                'is_active' => true,
                'display_order' => 3,
            ],
            [
                'name' => 'Food & Cuisine',
                'slug' => 'food-cuisine',
                'description' => 'Discover the flavors of Uzbek cuisine',
                'is_active' => true,
                'display_order' => 4,
            ],
        ];

        foreach ($categories as $category) {
            BlogCategory::create($category);
        }

        // Create Tags
        $tagNames = [
            'Uzbekistan', 'Samarkand', 'Bukhara', 'Khiva', 'Tashkent',
            'Silk Road', 'Culture', 'History', 'Food', 'Architecture',
            'UNESCO', 'Travel Guide', 'Photography', 'Adventure', 'Backpacking'
        ];

        foreach ($tagNames as $tagName) {
            BlogTag::create([
                'name' => $tagName,
                'slug' => Str::slug($tagName),
            ]);
        }

        // Create Blog Posts
        $posts = [
            [
                'category_id' => 1, // Travel Tips
                'title' => 'Top 10 Must-Visit Places in Uzbekistan',
                'slug' => 'top-10-must-visit-places-uzbekistan',
                'excerpt' => 'Discover the most breathtaking destinations in Uzbekistan, from ancient Silk Road cities to stunning natural landscapes.',
                'content' => '<p>Uzbekistan, the heart of Central Asia, is a treasure trove of historical wonders, stunning architecture, and vibrant culture. From the turquoise-domed mosques of Samarkand to the ancient fortress cities of Khiva and Bukhara, this country offers an unforgettable journey through time.</p>

<h2>1. Registan Square, Samarkand</h2>
<p>The crown jewel of Uzbekistan and one of the most magnificent public squares in the world. Three stunning madrasahs from the 15th-17th centuries stand as a testament to the golden age of the Timurid Empire.</p>

<h2>2. Bukhara Old City</h2>
<p>A UNESCO World Heritage site with over 140 architectural monuments. Walking through Bukhara feels like stepping back in time to the days of the Silk Road traders.</p>

<h2>3. Ichan-Kala, Khiva</h2>
<p>The walled inner town of Khiva is an open-air museum perfectly preserved from medieval times. Its mud-brick walls and towering minarets create an otherworldly atmosphere.</p>

<h2>4. Shah-i-Zinda Necropolis</h2>
<p>A stunning avenue of mausoleums in Samarkand, featuring some of the finest tile work in the Islamic world. The azure domes and intricate mosaics are breathtaking.</p>

<h2>5. Gur-e-Amir Mausoleum</h2>
<p>The final resting place of the great conqueror Timur (Tamerlane). The ribbed azure dome and golden interior make this one of Samarkand\'s most beautiful monuments.</p>

<p>These destinations represent just the beginning of what Uzbekistan has to offer. Each city tells its own story of conquest, culture, and architectural brilliance.</p>',
                'featured_image' => '/images/blog/registan-square.jpg',
                'author_name' => 'Jahongir Travel Team',
                'reading_time' => 8,
                'view_count' => 245,
                'is_featured' => true,
                'is_published' => true,
                'published_at' => now()->subDays(5),
                'meta_title' => 'Top 10 Must-Visit Places in Uzbekistan | Travel Guide',
                'meta_description' => 'Discover the 10 most amazing places to visit in Uzbekistan, from Registan Square to ancient Silk Road cities.',
            ],
            [
                'category_id' => 4, // Food & Cuisine
                'title' => 'A Culinary Journey Through Uzbekistan: Must-Try Dishes',
                'slug' => 'uzbekistan-cuisine-must-try-dishes',
                'excerpt' => 'From aromatic plov to succulent shashlik, explore the rich flavors and traditions of Uzbek cuisine.',
                'content' => '<p>Uzbek cuisine is a delightful fusion of flavors influenced by centuries of Silk Road trade. The food here is hearty, flavorful, and deeply rooted in tradition.</p>

<h2>Plov (Osh) - The National Dish</h2>
<p>No visit to Uzbekistan is complete without trying plov, the country\'s national dish. This aromatic rice pilaf is cooked with meat (usually lamb or beef), carrots, onions, and a special blend of spices. Each region has its own variation, making it a dish worth trying multiple times.</p>

<h2>Shashlik - Grilled Perfection</h2>
<p>These skewered and grilled meat kebabs are a staple across Central Asia. In Uzbekistan, they\'re typically made with lamb or beef, marinated in a mixture of onions, vinegar, and spices, then grilled over an open flame.</p>

<h2>Samsa - Savory Pastries</h2>
<p>These triangular pastries are filled with minced meat, onions, and spices, then baked in a traditional tandoor oven. The golden, flaky exterior gives way to a juicy, flavorful filling.</p>

<h2>Lagman - Noodle Soup</h2>
<p>This hearty noodle soup features hand-pulled noodles in a rich broth with meat and vegetables. It\'s perfect comfort food, especially in the cooler months.</p>

<h2>Non - Traditional Bread</h2>
<p>Uzbek bread (non) is baked in a clay oven called a tandoor. Each region has its own style, decorated with beautiful stamped patterns. It\'s often the centerpiece of the table.</p>

<p>Food in Uzbekistan is more than sustenance—it\'s a celebration of hospitality and tradition. Don\'t miss the opportunity to dine with a local family for an authentic experience.</p>',
                'featured_image' => '/images/blog/uzbek-plov.jpg',
                'author_name' => 'Chef Alisher',
                'reading_time' => 6,
                'view_count' => 187,
                'is_featured' => true,
                'is_published' => true,
                'published_at' => now()->subDays(12),
                'meta_title' => 'Uzbekistan Cuisine Guide: Traditional Dishes You Must Try',
                'meta_description' => 'Discover authentic Uzbek food culture - from plov to shashlik. Your complete guide to Uzbekistan\'s most delicious dishes.',
            ],
            [
                'category_id' => 3, // Culture & History
                'title' => 'The Silk Road: Uzbekistan\'s Golden Age of Trade and Culture',
                'slug' => 'silk-road-uzbekistan-history',
                'excerpt' => 'Explore how the ancient Silk Road shaped Uzbekistan into the crossroads of civilizations and cultures.',
                'content' => '<p>For over 1,500 years, Uzbekistan stood at the heart of the Silk Road, the network of trade routes connecting East and West. This strategic position transformed the cities of Samarkand, Bukhara, and Khiva into thriving centers of commerce, culture, and learning.</p>

<h2>The Routes Through Central Asia</h2>
<p>Multiple branches of the Silk Road passed through Uzbekistan, making it one of the most important regions along the entire network. Merchants traveling from China to Europe would rest, trade, and resupply in the caravanserais of Uzbek cities.</p>

<h2>Samarkand: The Jewel of the Silk Road</h2>
<p>Known as "The Pearl of the Islamic World," Samarkand reached its zenith under Timur (Tamerlane) in the 14th century. The city became famous for its markets, madrasahs, and magnificent architecture that blended influences from Persia, Mongolia, and India.</p>

<h2>Cultural Exchange and Innovation</h2>
<p>The Silk Road was about more than just trade in silk, spices, and precious goods. It facilitated the exchange of ideas, technologies, and religions. Buddhism, Islam, and Christianity all traveled these routes, while innovations in mathematics, astronomy, and medicine spread in both directions.</p>

<h2>The Timurid Renaissance</h2>
<p>The 15th century saw a cultural renaissance in Central Asia under Timur\'s descendants. His grandson Ulugh Beg built a famous observatory in Samarkand and advanced the study of astronomy to new heights.</p>

<h2>Legacy Today</h2>
<p>Today, visitors to Uzbekistan can still walk in the footsteps of ancient traders. The spectacular architectural monuments, bustling bazaars, and warm hospitality are living reminders of this golden age.</p>',
                'featured_image' => '/images/blog/silk-road-caravan.jpg',
                'author_name' => 'Dr. Sanjar Alimov',
                'author_image' => '/images/authors/sanjar.jpg',
                'reading_time' => 10,
                'view_count' => 312,
                'is_featured' => false,
                'is_published' => true,
                'published_at' => now()->subDays(20),
                'meta_title' => 'Silk Road History: Uzbekistan\'s Role in Ancient Trade',
                'meta_description' => 'Discover how the Silk Road shaped Uzbekistan into a crossroads of culture, trade, and civilization for over 1,500 years.',
            ],
            [
                'category_id' => 1, // Travel Tips
                'title' => 'First-Time Visitor\'s Guide to Uzbekistan: Everything You Need to Know',
                'slug' => 'first-time-visitor-guide-uzbekistan',
                'excerpt' => 'Planning your first trip to Uzbekistan? Here\'s everything you need to know about visas, currency, transportation, and cultural etiquette.',
                'content' => '<p>Uzbekistan is becoming increasingly popular among travelers seeking authentic cultural experiences off the beaten path. If you\'re planning your first visit, here\'s what you need to know.</p>

<h2>Visa Information</h2>
<p>Good news! Citizens of many countries can now visit Uzbekistan visa-free for up to 30 days. For others, e-visas are available and easy to obtain online. Check the official website before your trip.</p>

<h2>Best Time to Visit</h2>
<p>The ideal time to visit is during spring (April-June) or autumn (September-November). Summers can be extremely hot, especially in desert areas, while winters are cold. Spring brings blooming flowers and pleasant temperatures.</p>

<h2>Currency and Money</h2>
<p>The local currency is the Uzbek Som (UZS). While ATMs are available in major cities, it\'s wise to carry some cash, especially when visiting smaller towns. Credit cards are increasingly accepted in hotels and restaurants.</p>

<h2>Language</h2>
<p>Uzbek is the official language, but Russian is widely spoken. English is becoming more common in tourist areas, but learning a few basic phrases in Uzbek will be greatly appreciated by locals.</p>

<h2>Transportation</h2>
<p>High-speed trains connect major cities like Tashkent, Samarkand, and Bukhara, making travel between them comfortable and efficient. For shorter distances, shared taxis (marshrutkas) are common and affordable.</p>

<h2>Cultural Etiquette</h2>
<ul>
<li>Remove shoes when entering someone\'s home or a mosque</li>
<li>Dress modestly, especially when visiting religious sites</li>
<li>Accept tea when offered—it\'s a sign of hospitality</li>
<li>Avoid public displays of affection</li>
<li>Ask permission before photographing people</li>
</ul>

<h2>Safety</h2>
<p>Uzbekistan is considered very safe for tourists. Petty crime is rare, and locals are generally friendly and helpful toward visitors.</p>

<h2>What to Pack</h2>
<p>Bring comfortable walking shoes, sunscreen, a hat, and modest clothing. A light scarf is useful for women visiting mosques. Don\'t forget a good camera—you\'ll want to capture the stunning architecture!</p>',
                'featured_image' => '/images/blog/uzbekistan-guide.jpg',
                'author_name' => 'Jahongir Travel Team',
                'reading_time' => 7,
                'view_count' => 156,
                'is_featured' => false,
                'is_published' => true,
                'published_at' => now()->subDays(8),
                'meta_title' => 'First-Time Visitor\'s Guide to Uzbekistan | Travel Tips 2025',
                'meta_description' => 'Everything first-time visitors need to know about traveling to Uzbekistan - visas, money, transportation, and cultural tips.',
            ],
        ];

        foreach ($posts as $postData) {
            $post = BlogPost::create($postData);

            // Attach tags based on content
            $tags = [];
            if (str_contains($postData['title'], 'Uzbekistan')) $tags[] = 1; // Uzbekistan
            if (str_contains($postData['content'], 'Samarkand')) $tags[] = 2; // Samarkand
            if (str_contains($postData['content'], 'Bukhara')) $tags[] = 3; // Bukhara
            if (str_contains($postData['content'], 'Khiva')) $tags[] = 4; // Khiva
            if (str_contains($postData['content'], 'Silk Road')) $tags[] = 6; // Silk Road
            if ($postData['category_id'] == 3) $tags[] = 7; // Culture
            if ($postData['category_id'] == 3) $tags[] = 8; // History
            if ($postData['category_id'] == 4) $tags[] = 9; // Food
            if (str_contains($postData['content'], 'architecture')) $tags[] = 10; // Architecture
            if (str_contains($postData['content'], 'UNESCO')) $tags[] = 11; // UNESCO
            if ($postData['category_id'] == 1) $tags[] = 12; // Travel Guide

            $post->tags()->attach($tags);
        }

        $this->command->info('Blog seeder completed successfully!');
        $this->command->info('Created:');
        $this->command->info('- 4 categories');
        $this->command->info('- 15 tags');
        $this->command->info('- 4 blog posts');
    }
}
