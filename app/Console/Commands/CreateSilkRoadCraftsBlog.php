<?php

namespace App\Console\Commands;

use App\Models\BlogPost;
use App\Models\BlogCategory;
use Illuminate\Console\Command;

class CreateSilkRoadCraftsBlog extends Command
{
    protected $signature = 'create:silk-road-crafts-blog';
    protected $description = 'Create blog post about Silk Road crafts traditions';

    public function handle()
    {
        $this->info('Creating Silk Road Crafts blog post...');

        $category = BlogCategory::where('slug', 'culture-history')->first();

        $post = BlogPost::create([
            'category_id' => $category->id,
            'title' => 'Living Traditions: The Master Craftspeople Keeping the Silk Road Alive',
            'slug' => 'silk-road-crafts-artisans-margilan-rishtan-uzbekistan',
            'excerpt' => 'From silk weavers in Margilan to ceramic masters in Rishtan, discover the ancient crafts that have survived for over 1,000 years along Uzbekistan\'s Silk Road—and meet the artisans preserving these traditions.',
            'content' => '<p class="lead">In a world of mass production and digital manufacturing, there are still places where <strong>crafts unchanged for a millennium</strong> thrive. The Fergana Valley of Uzbekistan is one such place—where silk is still made from silkworm to loom, where ceramics still glow with secret glazes, and where master artisans pass down techniques that have survived Mongol invasions, Soviet collectivization, and modern globalization.</p>

<p>These aren\'t museum displays. They\'re living workshops where <strong>200+ workers at the Yodgorlik Silk Factory</strong> create atlas and adras silk, where Rishtan\'s potters mix the same ishkor glaze their ancestors used, where every piece tells a story of cultural survival.</p>

<h2>The Silk Road\'s Golden Thread: Margilan Silk</h2>

<h3>A City Built on Silk</h3>

<p>Margilan has been synonymous with silk since the 10th century. Legend says <strong>Alexander the Great</strong> discovered the city when his army was fed "murg-i-non" (bread with chicken)—the name eventually becoming Margilan. But it was silk, not chicken, that made the city famous.</p>

<p>By medieval times, Margilan silk was prized across the Silk Road—softer than Chinese silk, more vibrant than Persian silk, it commanded premium prices in markets from Baghdad to Venice. Merchants would journey months specifically to source Margilan\'s legendary <strong>khan-atlas</strong> (king silk) and <strong>adras</strong> fabrics.</p>

<h3>Yodgorlik Silk Factory: 1,000 Years in Motion</h3>

<p>Founded in 1972 but based on techniques perfecting over millennia, the <strong>Yodgorlik Silk Factory</strong> is Uzbekistan\'s most important silk preservation center. The name means "Heritage" or "Memory"—and that\'s exactly what it protects.</p>

<h4>The Complete Process (From Worm to Weave)</h4>

<p><strong>1. Sericulture (Silkworm Raising)</strong></p>

<p>It begins with tiny silkworms no bigger than ants, feeding voraciously on fresh <strong>mulberry leaves</strong>. The Fergana Valley\'s climate is perfect—hot summers, cold winters, and abundant mulberry trees. In 4-6 weeks, each worm consumes 50,000 times its initial weight in leaves.</p>

<p>When ready to pupate, the silkworm spins a cocoon using a single continuous fiber—up to <strong>1,500 meters long</strong>. This is raw silk.</p>

<p><strong>2. Cocoon Harvesting and Boiling</strong></p>

<p>Cocoons are collected and boiled to kill the pupae (otherwise they\'d break through the silk when emerging as moths). The boiling also softens the sericin—the natural gum binding the silk fibers—making them easier to unwind.</p>

<p>This is the moment visitors often find uncomfortable: you\'re watching pupae being killed for fabric. But this has been the method for 5,000 years. About 2,500-3,000 cocoons produce one kilogram of silk.</p>

<p><strong>3. Thread Spinning</strong></p>

<p>Multiple silk fibers are twisted together to create thread strong enough for weaving. The finest silk uses 3-5 fibers; thicker threads use 10-15. This determines the fabric\'s weight and texture.</p>

<p><strong>4. Natural Dyeing (The Secret Recipes)</strong></p>

<p>Margilan\'s vibrant colors come from natural materials:</p>

<ul>
    <li><strong>Pomegranate skins:</strong> Yellow and gold tones</li>
    <li><strong>Indigo:</strong> Deep blues (imported from Afghanistan historically)</li>
    <li><strong>Madder root:</strong> Reds and oranges</li>
    <li><strong>Walnut husks:</strong> Browns</li>
    <li><strong>Onion skins:</strong> Oranges and tans</li>
</ul>

<p>The exact proportions and mordants (fixatives) are closely guarded family secrets passed down through generations.</p>

<p><strong>5. Ikat (Abr) Technique</strong></p>

<p>This is where Margilan silk becomes art. <strong>Ikat</strong> (called "abr" in Uzbek, meaning "cloud") is a resist-dyeing technique where threads are tie-dyed BEFORE weaving. The process:</p>

<ol>
    <li>Designers create patterns on paper</li>
    <li>Warp threads are stretched on a frame</li>
    <li>Sections are tightly bound with plastic or cotton to resist dye</li>
    <li>Threads are dyed, dried, and the bindings removed</li>
    <li>Process repeats for each color (some designs require 5-7 dye baths)</li>
    <li>Finally, the prepared threads are woven</li>
</ol>

<p>Because the dyes inevitably bleed slightly, ikat has its characteristic "blurred" edges—hence "cloud patterns." The more precise the binding, the sharper the pattern, which separates master from novice.</p>

<p><strong>6. Hand-Loom Weaving</strong></p>

<p>The prepared threads go to master weavers operating <strong>traditional wooden looms</strong>—some over 100 years old. Creating complex ikat patterns requires phenomenal precision: if the weaver miscounts threads even slightly, the pattern won\'t align.</p>

<p>A master weaver can produce about 10-15 meters of fabric per week. Simpler patterns are faster; complex designs with multiple colors can take months.</p>

<h4>Atlas vs. Adras: Two Silk Kingdoms</h4>

<p><strong>Atlas (Khan-Atlas)</strong> is the premium silk—100% pure silk, warp and weft. It has a lustrous sheen, drapes like water, and displays ikat patterns with stunning clarity. Historically worn by royalty and nobility (hence "khan" = king). Modern atlas can cost $50-200 per meter depending on complexity.</p>

<p><strong>Adras</strong> is the "people\'s silk"—warp threads are silk (for sheen) but weft is cotton (for durability and affordability). It\'s matte rather than shiny, more suitable for daily wear, and less expensive ($15-40 per meter). Traditional craftspeople prefer adras for certain garments because it breathes better in summer heat.</p>

<h3>Why This Matters: Cultural Preservation</h3>

<p>The Soviet era nearly killed these crafts. Collectivization forced artisans into state factories producing standardized goods. Private workshops were banned. Traditional patterns were declared "bourgeois" or "backward."</p>

<p>When Uzbekistan gained independence in 1991, many crafts had been reduced to a handful of elderly masters. Yodgorlik and similar workshops became crucial for:</p>

<ul>
    <li><strong>Training new generations</strong> (apprenticeships lasting 5-7 years)</li>
    <li><strong>Documenting techniques</strong> before master craftspeople pass away</li>
    <li><strong>Providing employment</strong> (200+ workers at Yodgorlik alone)</li>
    <li><strong>Making silk affordable</strong> so Uzbeks can still wear traditional clothes</li>
</ul>

<h2>The Blue Gold of Rishtan: Ceramic Mastery</h2>

<h3>A City of Clay and Fire</h3>

<p>150 kilometers from Margilan lies <strong>Rishtan</strong>, a small city that has produced Central Asia\'s finest ceramics for over 800 years. The secret? Red clay deposits beneath the city and a unique glaze formula called <strong>ishkor</strong>.</p>

<h3>The Ishkor Secret: Uzbekistan\'s Signature Blue</h3>

<p>Rishtan ceramics are instantly recognizable by their luminous <strong>turquoise-blue glaze</strong>—the same ethereal blue adorning Samarkand\'s domes and Bukhara\'s minarets. This color comes from ishkor, a natural alkaline glaze made from:</p>

<ul>
    <li>Plants from the Kyzylkum Desert (collected in autumn)</li>
    <li>Burned to ash</li>
    <li>Mixed with water and other minerals</li>
    <li>The proportions are secret, varying by workshop</li>
</ul>

<p>Each master family has their own ishkor recipe, passed down through generations. The exact blend determines the shade—some produce sky blue, others deep turquoise, others greenish-blue.</p>

<h3>The Ceramic Creation Process</h3>

<p><strong>1. Clay Preparation</strong></p>

<p>Rishtan\'s famous red clay is dug from pits around the city. It\'s mixed with water, kneaded extensively to remove air bubbles, and aged for several weeks (aging makes it more plastic and workable).</p>

<p><strong>2. Throwing on the Wheel</strong></p>

<p>Potters use traditional foot-powered wheels—where the craftsperson kicks a heavy stone flywheel to maintain momentum while shaping clay. Electric wheels are available but many masters prefer the control of foot wheels.</p>

<p>Creating a perfectly symmetric bowl, vase, or plate requires years of practice. Master potters can throw a plate in 2-3 minutes; apprentices might take 20 minutes and still produce wobbly results.</p>

<p><strong>3. Drying and Bisque Firing</strong></p>

<p>Shaped pieces dry slowly (too fast = cracking) then undergo a first firing at ~800-900°C. This bisque firing hardens the clay into permanent form.</p>

<p><strong>4. Painting</strong></p>

<p>This is where artistry truly emerges. Using brushes made from sheep tail hair, painters apply mineral pigments in traditional patterns:</p>

<ul>
    <li><strong>Pomegranates:</strong> Symbol of fertility and abundance</li>
    <li><strong>Peppers:</strong> Protection from evil eye</li>
    <li><strong>Fish:</strong> Life and prosperity</li>
    <li><strong>Geometric patterns:</strong> Islamic art tradition (no living beings in religious contexts)</li>
    <li><strong>Floral motifs:</strong> Stylized tulips, irises, roses</li>
</ul>

<p>The painting is done on raw bisque—one mistake and the piece is ruined. There\'s no erasing. Master painters work freehand, no sketches, no guidelines, just decades of muscle memory.</p>

<p><strong>5. Glazing with Ishkor</strong></p>

<p>The painted piece is dipped in or brushed with liquid ishkor glaze. At this stage, it looks muddy, opaque, unremarkable. The magic happens in the kiln.</p>

<p><strong>6. Glaze Firing</strong></p>

<p>The final firing reaches 1000-1100°C. The ishkor melts, flows, and transforms into that signature glowing blue. The minerals in the glaze interact with the paint beneath, creating the final colors.</p>

<p>Opening the kiln is like unwrapping a mystery—temperature variations, placement in the kiln, even the weather can affect results. Some pieces emerge perfect; others have flaws (which are sold at discount or destroyed).</p>

<h3>Master Ceramicists: Meeting Rishtan\'s Legends</h3>

<p>Several master potters welcome visitors:</p>

<p><strong>Alisher Nazirov</strong> - Fourth-generation potter whose family has worked clay for over 100 years. His workshop displays the full process from clay to finished lyagan (large serving plates).</p>

<p><strong>Rustam Usmanov</strong> - UNESCO-recognized master who has revived nearly-lost techniques. His pieces are in museums worldwide.</p>

<p><strong>Kamoliddin Abdullaev</strong> - Specializes in traditional medicine bowls and vessels (certain clays and glazes were believed to have healing properties).</p>

<h3>Why Buy Authentic Rishtan?</h3>

<p>Markets across Uzbekistan sell "Rishtan-style" ceramics made elsewhere—cheaper, mass-produced, often using chemical dyes instead of natural ishkor. How to spot authentic pieces:</p>

<ul>
    <li><strong>Weight:</strong> Real Rishtan is surprisingly heavy (dense clay)</li>
    <li><strong>Base color:</strong> Should be reddish-brown (the natural clay), not white</li>
    <li><strong>Glaze texture:</strong> Slightly uneven, organic (chemical glazes are too perfect)</li>
    <li><strong>Price:</strong> Authentic lyagan plates: $30-150 depending on size and detail</li>
    <li><strong>Maker\'s mark:</strong> Look for the artist\'s signature or stamp</li>
</ul>

<p>Buying directly from workshops in Rishtan ensures authenticity and supports master craftspeople.</p>

<h2>Experiencing These Crafts: Practical Guide</h2>

<h3>Margilan Silk Factory Tour</h3>

<p><strong>Location:</strong> Yodgorlik Factory, Margilan (6km from Fergana city)<br>
<strong>Hours:</strong> 8:00 AM - 5:00 PM Monday-Saturday<br>
<strong>Cost:</strong> $5-10 per person for guided tour<br>
<strong>Duration:</strong> 1.5-2 hours<br>
<strong>Best day:</strong> Wednesday or Saturday (most workshops active)</p>

<p><strong>What to expect:</strong></p>
<ul>
    <li>See silkworms feeding (May-August)</li>
    <li>Watch cocoon boiling and thread spinning</li>
    <li>Observe natural dyeing process</li>
    <li>See ikat thread preparation</li>
    <li>Watch master weavers at looms</li>
    <li>Shop at factory prices (20-40% below bazaar rates)</li>
</ul>

<p><strong>Photography:</strong> Allowed, but ask permission before photographing workers\' faces.</p>

<h3>Rishtan Ceramics Workshops</h3>

<p><strong>Location:</strong> Rishtan city (75km from Fergana)<br>
<strong>Hours:</strong> Workshops generally open 9:00 AM - 6:00 PM<br>
<strong>Cost:</strong> Free to visit most workshops (purchases appreciated)<br>
<strong>Duration:</strong> 1-2 hours per workshop</p>

<p><strong>What to expect:</strong></p>
<ul>
    <li>See potters throwing on wheels</li>
    <li>Watch painting in progress</li>
    <li>Learn about ishkor glaze preparation</li>
    <li>Sometimes see kiln firing (ask in advance)</li>
    <li>Purchase directly from artisans</li>
    <li>Custom orders possible (2-4 weeks delivery)</li>
</ul>

<p><strong>Hands-on workshops:</strong> Some masters offer 2-3 hour classes ($30-50) where you throw your own piece, paint it, and they\'ll fire and ship it to you.</p>

<h3>Combining Both: The Artisan Trail Tour</h3>

<p>Our <strong>Samarkand Artisan Trail</strong> tour combines both experiences in one day:</p>

<ul>
    <li>Morning at Margilan silk factory</li>
    <li>Traditional Fergana lunch</li>
    <li>Afternoon at Rishtan ceramics workshops</li>
    <li>Visit Urgut Bazaar (suzani embroidery capital)</li>
</ul>

<p><em>Perfect for: Craft enthusiasts, photographers, anyone wanting to understand the "how" behind these beautiful objects.</em></p>

<h2>Buying and Shipping</h2>

<h3>What to Buy</h3>

<p><strong>Silk:</strong></p>
<ul>
    <li>Scarves: $20-60 (perfect gifts, easy to pack)</li>
    <li>Fabric by the meter: $30-200 (atlas) or $15-40 (adras)</li>
    <li>Ready-made clothing: $80-300</li>
    <li>Small decorative pieces: $10-25</li>
</ul>

<p><strong>Ceramics:</strong></p>
<ul>
    <li>Small bowls: $15-35</li>
    <li>Medium plates: $30-60</li>
    <li>Large lyagan (serving platters): $80-150</li>
    <li>Decorative vases: $40-120</li>
    <li>Sets (6 bowls + 1 platter): $150-300</li>
</ul>

<h3>Shipping Tips</h3>

<p><strong>Ceramics are heavy and fragile:</strong></p>
<ul>
    <li>Workshops can bubble-wrap and box for travel ($5-10)</li>
    <li>Check your luggage allowance (ceramics add weight fast)</li>
    <li>International shipping available but expensive ($50-100+ to USA/Europe)</li>
    <li>Consider buying small pieces that fit carry-on</li>
</ul>

<p><strong>Silk is travel-friendly:</strong></p>
<ul>
    <li>Lightweight, foldable, doesn\'t break</li>
    <li>Scarves and fabric meters pack easily</li>
    <li>No special care needed</li>
</ul>

<h2>The Deeper Meaning: Why These Crafts Matter</h2>

<p>In an age of Amazon Prime and fast fashion, why do these ancient crafts still matter?</p>

<h3>Cultural Identity</h3>

<p>For Uzbeks, ikat silk and blue ceramics are national symbols—as important as the flag. They appear on everything from currency to government buildings. Wearing traditional atlas at weddings, serving plov on Rishtan plates—these aren\'t just customs, they\'re connections to ancestors.</p>

<h3>Economic Sustainability</h3>

<p>These crafts employ thousands:</p>
<ul>
    <li>Silkworm farmers</li>
    <li>Mulberry tree growers</li>
    <li>Dyers, weavers, designers</li>
    <li>Clay miners, potters, painters</li>
    <li>Kiln operators, workshop managers</li>
</ul>

<p>Tourism has become crucial—foreign visitors often pay more than locals, subsidizing the craft\'s survival.</p>

<h3>Knowledge Preservation</h3>

<p>Every time a master craftsperson dies without passing down their knowledge, humanity loses a library. These workshops are living archives of:</p>
<ul>
    <li>Chemical knowledge (natural dyes, glaze formulations)</li>
    <li>Engineering (loom design, kiln construction)</li>
    <li>Artistic traditions (pattern symbolism, color theory)</li>
    <li>Material science (clay properties, silk behavior)</li>
</ul>

<h3>Slow Living Philosophy</h3>

<p>In a world of instant gratification, these crafts demand patience:</p>
<ul>
    <li>Silkworms take 6 weeks to produce cocoons</li>
    <li>Complex ikat requires months of preparation</li>
    <li>Ceramic apprenticeships last 5-10 years</li>
    <li>Master status takes decades</li>
</ul>

<p>Witnessing this slow, deliberate creation is meditative—a counterpoint to modern speed.</p>

<h2>Final Thoughts</h2>

<p>Visiting these workshops changed how I see "made in China" tags. When you\'ve watched an old man spend 3 hours painting a single plate, when you\'ve seen the calluses on a weaver\'s hands from 40 years at the loom, when you understand that the scarf in your hands represents the life cycle of thousands of silkworms and weeks of human labor—mass production feels hollow.</p>

<p>These aren\'t quaint souvenirs. They\'re cultural survival. Every purchase, every visit, every photo shared on Instagram is a small vote for keeping these traditions alive against the tide of industrialization.</p>

<p>The Silk Road may no longer carry caravans, but in Margilan and Rishtan, it still hums with the sound of looms and spins with potter\'s wheels. That\'s worth preserving.</p>

<hr>

<p><em><strong>Want to meet these master craftspeople?</strong> Our <a href="/tours/samarkand-artisan-trail">Samarkand Artisan Trail</a> includes the Margilan Silk Factory, Rishtan ceramics workshops, and Urgut Bazaar with expert guides who explain the cultural and historical context.</em></p>',

            'featured_image' => 'images/blog/margilan-silk-weaving.jpg',
            'author_name' => 'Jahongir Travel Team',
            'reading_time' => 15,
            'view_count' => 92,
            'is_featured' => true,
            'is_published' => true,
            'published_at' => now()->subDays(7),
            'meta_title' => 'Silk Road Crafts: Margilan Silk & Rishtan Ceramics Master Artisans Guide',
            'meta_description' => 'Discover 1,000-year-old silk weaving in Margilan and ceramic mastery in Rishtan, Uzbekistan. Complete guide to workshops, buying authentic pieces, and supporting traditional Silk Road crafts.',
        ]);

        $post->tags()->attach([1, 7, 8, 6]); // Uzbekistan, Culture, History, Silk Road

        $this->info("✅ Silk Road Crafts blog post created!");
        $this->info("Post ID: {$post->id}");
        $this->info("Title: {$post->title}");

        return 0;
    }
}
