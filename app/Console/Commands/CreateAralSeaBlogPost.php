<?php

namespace App\Console\Commands;

use App\Models\BlogPost;
use App\Models\BlogCategory;
use Illuminate\Console\Command;

class CreateAralSeaBlogPost extends Command
{
    protected $signature = 'create:aral-sea-blog';
    protected $description = 'Create blog post about Aral Sea disaster';

    public function handle()
    {
        $this->info('Creating Aral Sea blog post...');

        // Get Destinations category
        $category = BlogCategory::where('slug', 'destinations')->first();

        $post = BlogPost::create([
            'category_id' => $category->id,
            'title' => 'The Aral Sea Disaster: Visiting One of the World\'s Greatest Environmental Catastrophes',
            'slug' => 'aral-sea-disaster-environmental-catastrophe-travel-guide',
            'excerpt' => 'Once the world\'s fourth-largest lake, the Aral Sea has shrunk to 10% of its original size. This is the haunting story of an environmental disaster—and why you should witness it firsthand.',
            'content' => '<p class="lead"><strong>Imagine a fishing port city of 40,000 people where boats now sit marooned in the desert, 185 kilometers from water.</strong> This is Muynak, Uzbekistan—ground zero for one of the planet\'s worst environmental disasters. What was once the Aral Sea, a massive 68,000 km² lake teeming with life, is now largely the Aralkum Desert, a wasteland of salt, toxic dust, and rusted ship carcasses.</p>

<p>This isn\'t just an environmental tragedy—it\'s a cautionary tale about water mismanagement, a testament to human impact on ecosystems, and increasingly, a destination for travelers seeking to understand climate change through direct witness.</p>

<h2>What Happened to the Aral Sea?</h2>

<h3>The Fourth-Largest Lake in the World</h3>

<p>In 1960, the Aral Sea was a thriving inland sea shared between Kazakhstan and Uzbekistan. It supported:</p>

<ul>
    <li><strong>A massive fishing industry</strong> employing over 40,000 people in towns like Muynak and Aralsk</li>
    <li><strong>24 species of fish</strong> including commercially valuable sturgeon, bream, and carp</li>
    <li><strong>Coastal communities</strong> that had lived off the sea for millennia</li>
    <li><strong>A moderating climate effect</strong> that kept the region more temperate</li>
</ul>

<p>The sea was fed by two major rivers: the <strong>Amu Darya</strong> from the south and the <strong>Syr Darya</strong> from the northeast. These rivers carried snowmelt from the Pamir and Tian Shan mountains, replenishing the landlocked sea.</p>

<h3>Soviet "White Gold" Ambitions</h3>

<p>In the 1960s, Soviet planners decided to transform the arid steppes of Central Asia into cotton plantations. Cotton was "white gold"—a valuable export commodity. The plan required massive irrigation, so they diverted the Amu Darya and Syr Darya rivers into an expanding network of canals feeding cotton fields across Uzbekistan, Turkmenistan, and Kazakhstan.</p>

<p><em>"It doesn\'t matter if the Aral Sea dries up. We can always grow more cotton."</em> This attitude, prevalent among Soviet water managers, sealed the sea\'s fate.</p>

<h3>The Catastrophic Decline</h3>

<p>With its two main water sources diverted, the Aral Sea began to shrink:</p>

<ul>
    <li><strong>1960:</strong> The sea covered 68,000 km² at a depth of 53 meters</li>
    <li><strong>1987:</strong> Water level dropped nearly 13 meters; sea area decreased 40%</li>
    <li><strong>1989:</strong> The sea split into two separate bodies—North (Small) Aral and South (Large) Aral</li>
    <li><strong>2007:</strong> The sea had shrunk to just 10% of its original size</li>
    <li><strong>2014:</strong> The eastern basin of the South Aral Sea completely dried for the first time in 600 years</li>
</ul>

<p>Today, what remains is a fraction of a fraction—a few scattered pools of water up to 10 times saltier than the ocean.</p>

<h2>The Human and Environmental Cost</h2>

<h3>Ecological Collapse</h3>

<ul>
    <li><strong>20 of 24 fish species extinct</strong> in the region</li>
    <li><strong>Salinity increased from 10 g/L to over 100 g/L</strong> in remaining water</li>
    <li><strong>New desert created:</strong> The 40,000 km² Aralkum Desert now occupies the former seabed</li>
    <li><strong>Climate change:</strong> Winters became colder, summers hotter without the sea\'s moderating effect</li>
</ul>

<h3>Toxic Legacy</h3>

<p>The dried seabed isn\'t just salt—it\'s contaminated with:</p>

<ul>
    <li><strong>Pesticides</strong> from decades of cotton farming runoff</li>
    <li><strong>Fertilizers</strong> containing heavy metals</li>
    <li><strong>Industrial chemicals</strong> from Soviet-era weapons testing</li>
</ul>

<p>These toxins now blow in dust storms, traveling hundreds of kilometers and causing:</p>

<ul>
    <li>Respiratory illnesses (asthma, bronchitis, tuberculosis rates soared)</li>
    <li>Kidney diseases</li>
    <li>Cancer</li>
    <li>Infant mortality increases</li>
</ul>

<h3>Economic Devastation</h3>

<p>Muynak, once a bustling port, lost its entire fishing industry virtually overnight. By the 1980s:</p>

<ul>
    <li>The canning factories closed</li>
    <li>The fishing fleet was abandoned</li>
    <li>Population plummeted as workers migrated seeking employment</li>
    <li>Local economy collapsed</li>
</ul>

<p>The town that processed millions of tons of fish annually became a dusty backwater marooned in a new desert.</p>

<h2>Why Visit the Aral Sea?</h2>

<p>This might seem like disaster tourism—and in some ways, it is. But there are compelling reasons to make this journey:</p>

<h3>1. Educational Witness</h3>

<p>Reading about environmental disasters is one thing. Standing on the dried seabed, touching rusted ships surrounded by desert, collecting seashells from a landlocked wasteland—this visceral experience creates understanding no documentary can match. It\'s a geography lesson in real-time catastrophe.</p>

<h3>2. Supporting Local Communities</h3>

<p>Tourism has become one of the few economic opportunities for Muynak. Your visit:</p>

<ul>
    <li>Employs local guides</li>
    <li>Supports guesthouses and restaurants</li>
    <li>Provides income to families who lost fishing livelihoods</li>
    <li>Raises awareness that puts pressure on water policy reforms</li>
</ul>

<h3>3. Unparalleled Photography</h3>

<p>The Aral Sea region offers landscapes found nowhere else:</p>

<ul>
    <li><strong>The ship graveyard:</strong> Rusted fishing vessels frozen mid-voyage in sand</li>
    <li><strong>Drilling towers:</strong> Oil and gas infrastructure dotting the former seabed (a dark irony)</li>
    <li><strong>Ustyurt Plateau:</strong> Dramatic 100-meter limestone cliffs in the desert</li>
    <li><strong>Night skies:</strong> Zero light pollution = Milky Way stretching horizon to horizon</li>
    <li><strong>Surreal juxtapositions:</strong> Seashells in landlocked dunes, lighthouses overlooking desert, port infrastructure hundreds of kilometers from water</li>
</ul>

<h3>4. Understanding Water Security</h3>

<p>The Aral Sea disaster is the world\'s most visible lesson in water resource mismanagement. As climate change stresses water supplies globally, this serves as a warning about:</p>

<ul>
    <li>The dangers of short-term economic thinking</li>
    <li>Inter-regional water conflicts</li>
    <li>Ecosystem fragility</li>
    <li>The impossibility of "undoing" environmental damage</li>
</ul>

<h2>What to Expect: A Visit to the Aral Sea</h2>

<h3>Getting There</h3>

<p>The Aral Sea region is remote—very remote. Most journeys start in <strong>Nukus</strong>, the capital of Karakalpakstan (an autonomous republic within Uzbekistan). From Nukus:</p>

<ul>
    <li><strong>200+ km drive</strong> across desert and former seabed (4-6 hours)</li>
    <li><strong>4WD vehicle essential</strong> for rough tracks</li>
    <li><strong>Limited infrastructure</strong> once you leave town</li>
</ul>

<h3>Muynak Ship Cemetery</h3>

<p>The town\'s main attraction is the <strong>ship graveyard</strong>—a collection of fishing vessels marooned in the desert. The ships were dragged from where they were scattered across the dried seabed and arranged for easier viewing. You can:</p>

<ul>
    <li>Climb aboard the rusted hulks</li>
    <li>Photograph the surreal scene</li>
    <li>Visit the <strong>Muynak Regional Museum</strong> which shows documentary footage of Muynak in its 1960s heyday—workers unloading fish, children swimming, boats sailing. The contrast with today is devastating.</li>
</ul>

<h3>Crossing the Aralkum Desert</h3>

<p>To reach the remaining water, you drive across the dried seabed itself—now called the Aralkum Desert. Along the way:</p>

<ul>
    <li><strong>Seashells everywhere:</strong> Millions scattered across the sand, evidence of the vanished sea</li>
    <li><strong>Drilling towers:</strong> Like mechanical trees extracting oil and gas from beneath the former lakebed (the disaster that killed fishing created hydrocarbons)</li>
    <li><strong>Salt flats:</strong> White expanses where salt concentration is so high nothing grows</li>
    <li><strong>Ustyurt Plateau:</strong> Dramatic geological formations—100-meter cliffs displaying millions of years of ancient ocean sediments</li>
</ul>

<h3>The Remaining Aral Sea</h3>

<p>What\'s left of the sea is more saline than the ocean (salt concentration tripled as it shrank). But you can:</p>

<ul>
    <li><strong>Wade or swim</strong> in the ultra-salty water</li>
    <li><strong>Watch sunset</strong> over these "forgotten shores"</li>
    <li><strong>Collect shells</strong> from the beach</li>
    <li><strong>Reflect</strong> on the impermanence of ecosystems</li>
</ul>

<h3>Yurt Camp Experience</h3>

<p>Most tours include overnight stays in traditional yurts at camps like <strong>Besqala</strong>. These felt tents offer:</p>

<ul>
    <li>Authentic nomadic sleeping experience</li>
    <li>Traditional Karakalpak meals</li>
    <li>Exceptional stargazing (no light pollution)</li>
    <li>Silence—profound desert quiet broken only by wind</li>
</ul>

<p>Facilities are basic (shared outdoor toilets, limited water), but this is part of the experience—understanding how difficult life is in this harsh environment.</p>

<h2>Is There Any Hope?</h2>

<h3>The North Aral Success Story</h3>

<p>Kazakhstan has achieved partial recovery of the <strong>North (Small) Aral Sea</strong> through the <strong>Kok-Aral Dam</strong> completed in 2005. This dike separated the northern portion and trapped water from the Syr Darya. Results:</p>

<ul>
    <li>Water level rose 12 meters</li>
    <li>Salinity decreased by half</li>
    <li>Fish returned (14 species now present)</li>
    <li>Commercial fishing resumed at small scale</li>
    <li>Former port town of Aralsk can now access water again</li>
</ul>

<p>However, this success came at the expense of the South Aral, which was essentially abandoned.</p>

<h3>The South Aral: Beyond Saving?</h3>

<p>The Uzbekistan/southern portion of the sea is considered irreversibly lost by most scientists. Even if all irrigation stopped (economically impossible—millions depend on agriculture), the rivers no longer carry enough water to refill it.</p>

<h3>Adaptation, Not Restoration</h3>

<p>Current efforts focus on adaptation:</p>

<ul>
    <li><strong>Planting vegetation</strong> on the dried seabed to reduce toxic dust storms</li>
    <li><strong>Improving irrigation efficiency</strong> to reduce water waste</li>
    <li><strong>Economic diversification</strong>—fishing is gone, but oil/gas extraction and tourism provide new livelihoods</li>
    <li><strong>Health programs</strong> addressing respiratory and kidney diseases</li>
</ul>

<h2>Practical Information for Travelers</h2>

<h3>Best Time to Visit</h3>

<ul>
    <li><strong>Spring (April-May):</strong> Moderate temperatures (15-25°C), fewer dust storms</li>
    <li><strong>Fall (September-October):</strong> Comfortable weather, clear skies for photography</li>
    <li><strong>Avoid summer:</strong> Extreme heat (40°C+) and dust storms</li>
    <li><strong>Avoid winter:</strong> Freezing temperatures, harsh winds</li>
</ul>

<h3>What to Bring</h3>

<ul>
    <li><strong>Sun protection:</strong> Hat, sunglasses, SPF 50+ sunscreen</li>
    <li><strong>Layers:</strong> Desert temperatures swing 20°C between day and night</li>
    <li><strong>Dust protection:</strong> Scarf/bandana for dust storms, camera protection</li>
    <li><strong>Medications:</strong> Stomach remedies, respiratory inhalers if needed</li>
    <li><strong>Cash:</strong> No ATMs in Muynak or beyond</li>
    <li><strong>Water:</strong> Bring extra bottled water</li>
</ul>

<h3>Health Precautions</h3>

<ul>
    <li><strong>Travel insurance mandatory:</strong> Medical facilities are basic; evacuation insurance essential</li>
    <li><strong>Respiratory concerns:</strong> Asthmatics should consult doctors first; salt/dust can trigger attacks</li>
    <li><strong>Water safety:</strong> Only drink bottled or boiled water</li>
    <li><strong>Food safety:</strong> Stick to freshly cooked meals</li>
</ul>

<h3>Costs</h3>

<ul>
    <li><strong>2-day tours:</strong> $350-500 per person (includes transport, guide, meals, yurt stay)</li>
    <li><strong>Private tours:</strong> More expensive but flexible timing</li>
    <li><strong>Group tours:</strong> More economical; requires minimum participants</li>
</ul>

<h2>Combining with Other Attractions</h2>

<p>While in Nukus and Karakalpakstan, don\'t miss:</p>

<h3>Savitsky Museum</h3>

<p>The <strong>Igor Savitsky Karakalpakstan State Museum of Art</strong> holds the world\'s second-largest collection of Russian avant-garde art—masterpieces saved from Stalin\'s purges. This pairs perfectly with the Aral Sea disaster: both are Soviet-era catastrophes, one cultural, one environmental.</p>

<h3>Mizdakhan Necropolis</h3>

<p>An ancient cemetery spanning 2,400 years near Nukus. Legend says Adam is buried here. The site blends Zoroastrian and Islamic traditions.</p>

<h3>Ustyurt Plateau</h3>

<p>Explore dramatic limestone cliffs and canyons formed 20-60 million years ago when this area was beneath an ancient ocean. Spectacular geology and photography.</p>

<h2>Final Thoughts: Should You Go?</h2>

<p>Visiting the Aral Sea isn\'t a typical vacation. It\'s not comfortable. It\'s not convenient. The accommodations are basic, the journey is long, and what you see is heartbreaking.</p>

<p>But it\'s also one of the most important journeys you can make.</p>

<p>In an era of climate change, water scarcity, and environmental degradation, the Aral Sea is the parable we need to see. It\'s proof that ecosystems can collapse within a single human lifetime. It\'s evidence that short-term economic decisions can create permanent damage. It\'s a reminder that nature\'s patience has limits.</p>

<p>When you stand on that dried seabed, collecting seashells from a desert that was underwater just decades ago, touching rusted ships that once sailed where you now stand in sand—it changes you. It makes abstract environmental concerns visceral and real.</p>

<p>The Aral Sea disaster happened. It can\'t be undone. But it can be witnessed, understood, and learned from. That\'s why you should go.</p>

<hr>

<p><em><strong>Ready to witness the Aral Sea?</strong> Our <a href="/tours/forgotten-shores-2-day-aral-sea-journey">2-Day Aral Sea Ecological Journey</a> includes the ship cemetery, crossing the dried seabed, visiting the remaining waters, yurt camping, and exploration of the Ustyurt Plateau with expert environmental guides.</em></p>',

            'featured_image' => 'images/blog/aral-sea-ship-cemetery.jpg',
            'author_name' => 'Jahongir Travel Team',
            'reading_time' => 12,
            'view_count' => 145,
            'is_featured' => true,
            'is_published' => true,
            'published_at' => now()->subDays(3),
            'meta_title' => 'Aral Sea Disaster: Complete Travel Guide to Central Asia\'s Environmental Catastrophe',
            'meta_description' => 'Visit the Aral Sea, once the world\'s 4th largest lake, now 10% of its size. Complete guide to the ship cemetery, environmental disaster, and responsible tourism in Muynak, Uzbekistan.',
        ]);

        // Attach tags
        $post->tags()->attach([1, 14, 12]); // Uzbekistan, Adventure, Travel Guide

        $this->info("✅ Aral Sea blog post created!");
        $this->info("Post ID: {$post->id}");
        $this->info("Title: {$post->title}");
        $this->info("URL: http://127.0.0.1:8000/blog/{$post->slug}");

        return 0;
    }
}
