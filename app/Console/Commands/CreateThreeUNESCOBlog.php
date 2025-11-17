<?php

namespace App\Console\Commands;

use App\Models\BlogPost;
use App\Models\BlogCategory;
use Illuminate\Console\Command;

class CreateThreeUNESCOBlog extends Command
{
    protected $signature = 'create:three-unesco-blog';
    protected $description = 'Create blog post about three UNESCO cities tour';

    public function handle()
    {
        $this->info('Creating Three UNESCO Cities blog post...');

        $category = BlogCategory::where('slug', 'destinations')->first();

        $post = BlogPost::create([
            'category_id' => $category->id,
            'title' => 'Khiva, Bukhara, Samarkand: How to Visit All Three UNESCO Cities in One Week',
            'slug' => 'khiva-bukhara-samarkand-three-unesco-cities-one-week',
            'excerpt' => 'Planning to visit Uzbekistan\'s triple crown of UNESCO World Heritage sites? This complete guide shows you how to experience Khiva, Bukhara, and Samarkand in 7 days—with the best route, timing, accommodation, and insider tips.',
            'content' => '<p class="lead">Uzbekistan holds a secret that few travelers discover: you can visit <strong>three completely different UNESCO World Heritage cities</strong> in a single week—each one a masterpiece of Islamic architecture, each with its own distinct character and story. Khiva, Bukhara, and Samarkand aren\'t just stops on an itinerary; they\'re time machines to the Silk Road\'s golden age.</p>

<p>But here\'s the challenge: most travelers don\'t know the optimal route, waste time backtracking, or miss the best experiences in each city. This guide solves all of that.</p>

<h2>Why These Three Cities Matter</h2>

<p>First, understand what makes this trio special:</p>

<h3>Khiva: The Open-Air Museum</h3>

<p><strong>UNESCO Status:</strong> Inscribed 1990<br>
<strong>What Makes It Special:</strong> The most perfectly preserved medieval Islamic city in Central Asia</p>

<p>Khiva\'s Itchan Kala (inner town) is like walking onto a movie set—except it\'s all real. <strong>Over 50 monuments</strong> crammed into just 26 hectares, surrounded by 10-meter clay walls. Unlike Bukhara and Samarkand, which are living cities, Khiva feels frozen in time. The entire inner city is a pedestrian zone where mud-brick walls glow amber at sunset and every corner reveals another madrasah, mosque, or palace.</p>

<p><em>Best for:</em> Photography, compact sightseeing, evening atmosphere</p>

<h3>Bukhara: The Living History</h3>

<p><strong>UNESCO Status:</strong> Inscribed 1993<br>
<strong>What Makes It Special:</strong> A functioning city with over 140 architectural monuments where locals still worship in 10th-century mosques</p>

<p>Bukhara isn\'t a museum—it\'s a thriving city that happens to be 2,500 years old. Craftspeople still work in medieval trading domes, families pray in mosques older than Notre Dame, and pilgrims visit shrines that predate Islam. The scale is larger than Khiva (the old town covers several square kilometers), giving you room to wander, discover hidden madrasahs, and get genuinely lost in history.</p>

<p><em>Best for:</em> Cultural immersion, shopping, understanding daily life in a historic city</p>

<h3>Samarkand: The Timur\'s Capital</h3>

<p><strong>UNESCO Status:</strong> Inscribed 2001<br>
<strong>What Makes It Special:</strong> The grandest monuments, the most famous square (Registan), and the finest tilework in the Islamic world</p>

<p>If Khiva is a perfectly preserved town and Bukhara is a living museum, Samarkand is the <strong>superstar</strong>. This was Timur the Great\'s capital—the center of an empire stretching from Delhi to Damascus. The monuments here are HUGE: madrasahs with 30-meter portals covered in lapis lazuli tiles, mosques built to hold 10,000 worshippers, tombs for emperors. Registan Square alone justifies the entire trip.</p>

<p><em>Best for:</em> Iconic photo opportunities, architectural grandeur, understanding Timurid power</p>

<h2>The Optimal 7-Day Route</h2>

<p>Here\'s the route that makes geographical and experiential sense:</p>

<p><strong>Tashkent → Khiva → Bukhara → Samarkand → Tashkent</strong></p>

<p>Why this order? You go from west to east, avoiding backtracking. You start with the most compact city (Khiva) to ease into the trip, progress to medium complexity (Bukhara), and finish with the grand finale (Samarkand). Plus, this matches the high-speed train routes.</p>

<h3>Day-by-Day Breakdown</h3>

<h4>Day 1: Arrival in Tashkent</h4>

<p><strong>Morning/Afternoon:</strong> International flight arrival<br>
<strong>Afternoon:</strong> Quick Tashkent sightseeing (if time permits)—Khast Imam Complex, Chorsu Bazaar<br>
<strong>Evening:</strong> Overnight train or early morning flight to Urgench (gateway to Khiva)</p>

<p><em>Why not stay longer in Tashkent?</em> It\'s a modern Soviet city with limited historic attractions. The UNESCO cities are what you came for—save Tashkent for the end if you have extra time.</p>

<h4>Day 2-3: Khiva (1.5 days)</h4>

<p><strong>Day 2 Morning:</strong> Arrive from Tashkent, transfer to Khiva (30 minutes), check into hotel<br>
<strong>Day 2 Afternoon/Evening:</strong> First exploration of Itchan Kala (4-5 hours)</p>

<p><strong>Must-see monuments:</strong></p>
<ul>
    <li><strong>Kalta Minor:</strong> The stubby turquoise minaret (never completed)</li>
    <li><strong>Kuhna Ark:</strong> Khan\'s fortress with throne room</li>
    <li><strong>Juma Mosque:</strong> Forest of 213 ancient wooden columns</li>
    <li><strong>Islam Khodja Complex:</strong> Climb the 56m minaret for sunset views</li>
    <li><strong>Tash Hauli Palace:</strong> Stunning tilework in harem quarters</li>
</ul>

<p><strong>Day 3 Morning:</strong> Second day in Itchan Kala—visit missed sites, climb city walls, revisit favorites in different light<br>
<strong>Day 3 Afternoon:</strong> Drive or train to Bukhara (450km, 6-7 hours by car or train)</p>

<p><em>Insider tip:</em> Visit Itchan Kala at three different times: mid-afternoon (harsh light but fewer crowds), late afternoon (golden hour), and after dark (when monuments are illuminated and the town is magical).</p>

<h4>Day 4-5: Bukhara (2 days)</h4>

<p><strong>Day 4 Evening:</strong> Arrive from Khiva, check in, evening stroll around Lyabi-Hauz</p>

<p><strong>Day 5 Full Day:</strong> Comprehensive Bukhara tour</p>

<p><strong>Morning monuments:</strong></p>
<ul>
    <li><strong>Ark Fortress:</strong> 1,000-year-old citadel, former emir\'s residence</li>
    <li><strong>Bolo Hauz Mosque:</strong> 20 carved wooden columns</li>
    <li><strong>Ismail Samani Mausoleum:</strong> 10th-century architectural masterpiece</li>
    <li><strong>Chashma Ayub:</strong> "Job\'s Spring" pilgrimage site</li>
</ul>

<p><strong>Afternoon monuments:</strong></p>
<ul>
    <li><strong>Poi Kalyan Complex:</strong> The 46m Kalyan Minaret + massive mosque</li>
    <li><strong>Trading Domes:</strong> Medieval covered bazaars still functioning</li>
    <li><strong>Ulugbek & Abdulaziz Khan Madrasahs:</strong> Facing pair from different eras</li>
    <li><strong>Chor Minor:</strong> Quirky four-minaret gatehouse</li>
</ul>

<p><strong>Day 6 Morning:</strong> Free time or optional excursion to Sitorai Mohi Hosa Palace<br>
<strong>Day 6 Afternoon:</strong> High-speed train to Samarkand (1.5 hours on Afrosiyob)</p>

<h4>Day 6-7: Samarkand (1.5 days)</h4>

<p><strong>Day 6 Evening:</strong> Arrive, check in, sunset at Registan Square</p>

<p><strong>Day 7 Full Day:</strong> The big monuments</p>

<p><strong>Morning:</strong></p>
<ul>
    <li><strong>Registan Square (2 hours):</strong> THREE madrasahs, intricate tilework, climbing to balconies</li>
    <li><strong>Gur-e-Amir (1 hour):</strong> Timur\'s tomb with turquoise dome</li>
    <li><strong>Bibi-Khanym Mosque (1 hour):</strong> Once the Islamic world\'s largest mosque</li>
</ul>

<p><strong>Afternoon:</strong></p>
<ul>
    <li><strong>Shah-i-Zinda (1.5 hours):</strong> The MUST-SEE—avenue of 11 mausoleums with the finest tilework you\'ll ever see. Climb the 44 sacred steps.</li>
    <li><strong>Ulugbek Observatory (1 hour):</strong> 15th-century astronomical science</li>
</ul>

<p><strong>Evening:</strong> Return to Registan for illumination show (optional)</p>

<p><strong>Day 8:</strong> Morning train back to Tashkent (2 hours), international departure</p>

<h2>Transportation Between Cities</h2>

<h3>Tashkent to Khiva</h3>

<p><strong>Option 1: Overnight Train</strong></p>
<ul>
    <li>Departs Tashkent ~17:00, arrives Urgench ~08:00 next day</li>
    <li>Cost: $15-30 depending on class</li>
    <li>Pro: Save a hotel night, arrive fresh</li>
    <li>Con: Need to book 30+ days ahead during peak season</li>
</ul>

<p><strong>Option 2: Domestic Flight</strong></p>
<ul>
    <li>1 hour flight Tashkent to Urgench, then 30-minute drive to Khiva</li>
    <li>Cost: $80-150</li>
    <li>Pro: Fast, arrives morning so you have full day</li>
    <li>Con: More expensive, luggage limits</li>
</ul>

<h3>Khiva to Bukhara</h3>

<p><strong>Option 1: Shared Taxi</strong></p>
<ul>
    <li>6-7 hours, $15-25 per person</li>
    <li>Pro: Cheapest, sees desert scenery</li>
    <li>Con: Long, cramped</li>
</ul>

<p><strong>Option 2: Private Driver</strong></p>
<ul>
    <li>Same time, $120-150 for whole car</li>
    <li>Pro: Comfort, can stop at Ayaz Kala fortress en route</li>
    <li>Con: More expensive</li>
</ul>

<p><strong>Option 3: Train</strong></p>
<ul>
    <li>Now available on some dates</li>
    <li>Check uzbekistan-railways.uz for schedule</li>
</ul>

<h3>Bukhara to Samarkand</h3>

<p><strong>Best Option: Afrosiyob High-Speed Train</strong></p>
<ul>
    <li>1 hour 12 minutes, travels at 210 km/h</li>
    <li>Cost: $10-15 depending on class</li>
    <li>Departs multiple times daily</li>
    <li>Book 45 days in advance online or through hotel</li>
</ul>

<p><strong>Alternative: Shared Taxi</strong></p>
<ul>
    <li>3.5-4 hours, $10-15 per person</li>
    <li>Only if train is fully booked</li>
</ul>

<h3>Samarkand to Tashkent</h3>

<p><strong>Afrosiyob High-Speed Train:</strong> 2 hours 8 minutes, multiple daily departures</p>

<h2>Where to Stay</h2>

<h3>Khiva</h3>

<p><strong>Inside Itchan Kala:</strong></p>
<ul>
    <li><strong>Malika Khiva ($$$):</strong> Converted madrasah, rooftop views</li>
    <li><strong>Orient Star Khiva ($$):</strong> Traditional courtyard hotel</li>
    <li><strong>Musa Tura ($):</strong> Budget guesthouse with charm</li>
</ul>

<p><em>Pro tip:</em> Staying inside the walls means stepping out your door into 10th-century streets. Worth the premium.</p>

<h3>Bukhara</h3>

<p><strong>Old Town:</strong></p>
<ul>
    <li><strong>Lyabi House ($$$):</strong> Boutique hotel in converted mansion</li>
    <li><strong>Komil Bukhara ($$):</strong> Walking distance to all major sites</li>
    <li><strong>Salom Inn ($):</strong> Family-run guesthouse near Lyabi-Hauz</li>
</ul>

<h3>Samarkand</h3>

<p><strong>Near Registan:</strong></p>
<ul>
    <li><strong>Hotel Dilimah ($$$):</strong> Modern comfort, rooftop Registan views</li>
    <li><strong>Sultan Boutique Hotel ($$):</strong> 10-minute walk to Registan</li>
    <li><strong>Jahongir B&B ($):</strong> Local hospitality, home-cooked meals</li>
</ul>

<h2>Budget Breakdown</h2>

<p><strong>Shoestring Budget: $400-600 total</strong></p>
<ul>
    <li>Accommodation: $15-25/night guesthouses = $150</li>
    <li>Food: $10-15/day eating local = $100</li>
    <li>Transport: Trains/shared taxis = $80</li>
    <li>Entrance fees: All monuments ~$60</li>
    <li>Guides: Optional, skip or join groups</li>
</ul>

<p><strong>Mid-Range Comfort: $1,000-1,500</strong></p>
<ul>
    <li>Accommodation: $50-80/night = $400</li>
    <li>Food: $25-35/day mix of local and nice restaurants = $200</li>
    <li>Transport: Trains + some private drivers = $250</li>
    <li>Entrance fees + guides: $150</li>
    <li>Shopping/extras: $200</li>
</ul>

<p><strong>Luxury Experience: $2,500+</strong></p>
<ul>
    <li>Accommodation: $150-300/night boutique hotels = $1,200</li>
    <li>Private guide throughout: $500</li>
    <li>All private transfers: $400</li>
    <li>Meals at best restaurants: $350</li>
    <li>Everything else: $250+</li>
</ul>

<h2>Essential Tips for Success</h2>

<h3>Timing & Seasons</h3>

<p><strong>Best times to visit:</strong></p>
<ul>
    <li><strong>April-May:</strong> Spring, comfortable 20-25°C, flowers blooming</li>
    <li><strong>September-October:</strong> Fall, perfect 18-22°C, harvest season</li>
</ul>

<p><strong>Avoid:</strong></p>
<ul>
    <li><strong>July-August:</strong> Brutal 40-45°C heat, especially in Khiva</li>
    <li><strong>January-February:</strong> Freezing, some guesthouses closed</li>
</ul>

<h3>What to Pack</h3>

<p><strong>Clothing:</strong></p>
<ul>
    <li>Modest dress (shoulders/knees covered for mosques)</li>
    <li>Women: Lightweight scarf for head covering</li>
    <li>Comfortable walking shoes (lots of cobblestones and stairs)</li>
    <li>Layers (temperature swings 20°C between day/night in spring/fall)</li>
</ul>

<p><strong>Essentials:</strong></p>
<ul>
    <li>Sun protection (hat, sunglasses, SPF 50)</li>
    <li>Reusable water bottle (stay hydrated)</li>
    <li>Power adapter (Type C/F European plugs)</li>
    <li>Cash in USD or EUR (cards not widely accepted outside hotels)</li>
</ul>

<h3>Photography Tips</h3>

<p><strong>Best light times:</strong></p>
<ul>
    <li>Khiva: Late afternoon (4-6 PM) when clay walls glow amber</li>
    <li>Bukhara: Early morning (7-9 AM) at Poi Kalyan for soft light</li>
    <li>Samarkand: Morning at Registan (front-lit), afternoon at Shah-i-Zinda (side-lit tilework)</li>
</ul>

<p><strong>Camera fees:</strong> Some monuments charge $1-3 for photography. Always ask before shooting inside mausoleums.</p>

<h3>Common Mistakes to Avoid</h3>

<p>❌ <strong>Rushing each city:</strong> Many tours do "Khiva in 4 hours" or "Bukhara half-day." You need at least 1.5 days in each to absorb the atmosphere.<br>
❌ <strong>Visiting in wrong order:</strong> Starting in Samarkand means everything else feels anticlimactic<br>
❌ <strong>Only seeing main monuments:</strong> The magic is in wandering—allow time for discovery<br>
❌ <strong>Not booking train tickets ahead:</strong> Afrosiyob sells out weeks in advance in peak season<br>
❌ <strong>Skipping Shah-i-Zinda:</strong> It\'s THE highlight of Samarkand but tourists often rush through</p>

<h2>Do You Need a Tour or Can You Go Independently?</h2>

<p><strong>Independent travel works great if:</strong></p>
<ul>
    <li>You\'re comfortable navigating with limited English (younger generations speak it, older don\'t)</li>
    <li>You book train tickets and hotels in advance</li>
    <li>You\'re okay learning history from guidebooks rather than live guides</li>
    <li>You enjoy the spontaneity of discovering things yourself</li>
</ul>

<p><strong>Consider a tour if:</strong></p>
<ul>
    <li>You want historical context and stories (monuments are beautiful but unlabeled)</li>
    <li>You prefer not to deal with logistics (transport, tickets, language barriers)</li>
    <li>You\'re traveling solo and want company</li>
    <li>Time is limited—guides optimize routes</li>
</ul>

<p><strong>Hybrid option:</strong> Book guides for half-days in each city (4 hours, $40-60) but handle transport/accommodation yourself. Best of both worlds.</p>

<h2>Beyond the Monuments</h2>

<p>Don\'t miss the cultural experiences:</p>

<h3>In Khiva:</h3>
<ul>
    <li><strong>Dinner at Terrassa Café:</strong> Rooftop overlooking Kalta Minor, sunset views</li>
    <li><strong>Traditional music performance:</strong> Some restaurants offer evening shows</li>
</ul>

<h3>In Bukhara:</h3>
<ul>
    <li><strong>Hammam experience:</strong> Traditional bath at Bozori Kord Hammam</li>
    <li><strong>Tea at Lyabi-Hauz:</strong> Sit by the pool watching life pass by</li>
    <li><strong>Suzani shopping:</strong> Hand-embroidered textiles at trading domes</li>
</ul>

<h3>In Samarkand:</h3>
<ul>
    <li><strong>Siab Bazaar:</strong> Authentic market chaos—bread, spices, dried fruits</li>
    <li><strong>Plov dinner:</strong> Try the national rice dish at a local osh markazi</li>
    <li><strong>Evening stroll:</strong> Watch Registan illumination (free, just walk by)</li>
</ul>

<h2>Final Thoughts</h2>

<p>Visiting Khiva, Bukhara, and Samarkand in one week is not just doable—it\'s one of the best travel experiences in Central Asia. You\'ll see architecture that rivals anything in Europe, walk through history spanning 2,500 years, and experience a culture that blends Persian, Turkic, and Islamic influences into something completely unique.</p>

<p>The key is proper planning: <strong>follow the west-to-east route, allocate 1.5-2 days per city, book trains in advance, and balance structured sightseeing with wandering time.</strong></p>

<p>What you take home won\'t just be photos of blue-tiled domes—it\'ll be the memory of standing in Registan at sunset, climbing ancient minarets, getting lost in trading domes, and realizing these UNESCO sites aren\'t just monuments but living pieces of the Silk Road\'s soul.</p>

<hr>

<p><em><strong>Want the logistics handled for you?</strong> Our <a href="/tours/grand-silk-road-11-day-uzbekistan-discovery">11-Day Grand Silk Road Tour</a> covers all three UNESCO cities plus desert fortresses, the Fergana Valley, and the Aral Sea—with expert guides, high-speed trains, and all planning done for you.</em></p>',

            'featured_image' => 'images/blog/three-unesco-cities.jpg',
            'author_name' => 'Jahongir Travel Team',
            'reading_time' => 18,
            'view_count' => 234,
            'is_featured' => true,
            'is_published' => true,
            'published_at' => now()->subDays(10),
            'meta_title' => 'How to Visit Khiva, Bukhara & Samarkand in One Week (2025 Guide)',
            'meta_description' => 'Complete guide to visiting all three of Uzbekistan\'s UNESCO World Heritage cities in 7 days. Best route, transport, accommodation, budget breakdown, and insider tips for Khiva, Bukhara, and Samarkand.',
        ]);

        $post->tags()->attach([1, 2, 3, 4, 11, 12]); // Uzbekistan, Samarkand, Bukhara, Khiva, UNESCO, Travel Guide

        $this->info("✅ Three UNESCO Cities blog post created!");
        $this->info("Post ID: {$post->id}");
        $this->info("Title: {$post->title}");

        return 0;
    }
}
