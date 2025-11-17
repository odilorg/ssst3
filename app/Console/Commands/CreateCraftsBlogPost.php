<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\BlogPost;
use App\Models\BlogCategory;
use App\Models\BlogTag;
use Carbon\Carbon;

class CreateCraftsBlogPost extends Command
{
    protected $signature = 'create:crafts-blog-post';
    protected $description = 'Create comprehensive blog post about traditional crafts and cultural practices of Uzbekistan';

    public function handle()
    {
        $this->info('Creating traditional crafts blog post...');

        // Get or create category
        $category = BlogCategory::firstOrCreate(
            ['name' => 'Culture & Heritage'],
            [
                'slug' => 'culture-heritage',
                'description' => 'Explore the rich cultural heritage and traditions of Uzbekistan'
            ]
        );

        // Create the blog post
        $post = BlogPost::create([
            'category_id' => $category->id,
            'title' => 'Living Heritage: The Traditional Crafts That Made Uzbekistan Legendary',
            'slug' => 'traditional-crafts-cultural-practices-uzbekistan',
            'excerpt' => 'For centuries, Uzbek master craftsmen have transformed silk, clay, wood, and wool into objects of breathtaking beauty. Discover the ancient techniques and cultural traditions behind the legendary crafts that once adorned palaces from Beijing to Constantinople.',
            'content' => $this->getCraftsContent(),
            'featured_image' => '/images/blog/uzbek-traditional-crafts.jpg',
            'author_name' => 'Cultural Heritage Team',
            'author_image' => '/images/authors/heritage-team.jpg',
            'reading_time' => 18,
            'view_count' => 0,
            'is_featured' => true,
            'is_published' => true,
            'published_at' => Carbon::now(),
            'meta_title' => 'Traditional Crafts of Uzbekistan: Living Heritage & Ancient Techniques',
            'meta_description' => 'Discover Uzbekistan\'s legendary crafts: carpet weaving, miniature painting, glazed ceramics, wood carving, and silk textiles. Learn the ancient techniques that made Central Asian crafts famous along the Silk Road.'
        ]);

        // Attach tags
        $tags = [
            'Traditional Crafts',
            'Cultural Heritage',
            'Uzbek Art',
            'Handmade',
            'Carpet Weaving',
            'Ceramics',
            'Woodcarving',
            'Silk Road',
            'Artisan Workshops'
        ];

        foreach ($tags as $tagName) {
            $tag = BlogTag::firstOrCreate(
                ['name' => $tagName],
                ['slug' => \Str::slug($tagName)]
            );
            $post->tags()->attach($tag->id);
        }

        $this->info('Created: ' . $post->title);
        $this->info('');
        $this->info('✅ Traditional crafts blog post created successfully!');

        return 0;
    }

    private function getCraftsContent()
    {
        return <<<'CONTENT'
When you hold a piece of Uzbek silk in your hands, or run your fingers over the intricate carvings of a Khivan door, you're touching a living tradition that stretches back over a thousand years. These aren't museum pieces—they're the continuation of crafts that once made Central Asia the artistic center of the medieval world.

Let me take you into the workshops, the homes, and the creative minds of the master craftsmen who keep these traditions alive.


THE CARPETS THAT COVERED EMPIRES

Walk into any traditional Uzbek home, and you'll immediately understand why carpets weren't just floor coverings—they were the furniture itself. For centuries, the complete absence of chairs and tables in Central Asian homes meant that carpets, felts, and textiles defined the entire living space.

The Making of a Masterpiece

I watched master weaver Tuti Mustafaeva work at her narrow loom in Arab-khona village, her hands moving with practiced rhythm. "My grandmother taught me these patterns when I was seven," she told me. "Each design has a name, a story."

The process begins with raw wool from local sheep. Women spin it on simple wooden spindles called "urchuk"—a stick about 20-30 cm long with a weighted disc. The spinner holds the raw wool in her left hand, drawing out the fibers while rotating the spindle against her thigh with her right hand. It's hypnotic to watch, and extraordinarily efficient.

For flat-weave carpets (palas), they prepare two types of yarn: warp (the structural threads) and weft (the pattern threads). The magic happens in how these are combined.

The Secret Language of Patterns

Every motif on an Uzbek carpet tells a story:

"Kuchkorak" (Ram's Horn): The most ancient and beloved pattern, appearing as diamond shapes with curling spirals at the corners. You'll see countless variations across Central Asia—each region claims it as their own, but the basic form goes back millennia. It represents strength, prosperity, and the pastoral heritage of the steppe peoples.

"Taraq" (Comb): Based on the traditional wooden comb, this pattern features parallel lines radiating from a central point. It has dozens of variations and symbolizes order and craftsmanship.

"Gull" (Flower): Despite its geometric appearance, many designs represent stylized flowers. The degree of geometric abstraction is intentional—centuries of Islamic influence favored pattern over realistic representation.

The Arab Masters of Kamashi

In the Kashkadarya region, Arab communities developed carpet-making into a commercial art. In villages like Kamashi and Jeynau, carpets became the primary source of family income. Uniquely in Central Asia, men participated in carpet production here—though only in preparatory work like spinning. Weaving remained exclusively women's work.

Their "bazaar-gilam" (market carpets) are masterpieces of bold geometric design: massive rectangles, diamonds, triangles, and trapezoids in eye-popping color combinations. A characteristic feature is stepped contour lines—instead of straight edges, the borders step up and down like pixels, softening the harsh contrast of the bright colors.

Inside these large geometric forms, you'll find tiny motifs in contrasting colors: little combs (taraq), flowers (gull), and ancient symbols like the swastika (called "hearth"), which lost its original meaning centuries ago but persists as pure decoration.

The pile carpets (vorsy) require even more skill. After each pass of the weft thread, the weaver ties thousands of individual knots on pairs of warp threads, then cuts them to create the plush surface. The knots are tied entirely by hand, without tools—a technique that takes years to master.


MINIATURE PAINTING: BOOKS AS SACRED OBJECTS

In medieval Central Asia, a manuscript wasn't just a book—it was a work of art from start to finish. The finest paper came from Samarkand itself, made from silk waste, slightly cream-colored like old ivory, polished to a soft sheen. Italian Renaissance artists knew and prized Samarkand paper.

The Anatomy of a Masterpiece Manuscript

Let me walk you through the creation of a medieval book:

First, the paper is cut and the text frames are precisely measured according to mathematical principles of harmonic proportion—not eyeballed, but calculated.

Then comes the calligrapher. Calligraphy based on Arabic script was an art form unto itself, with multiple systems of letterforms, each governed by aesthetic rules. Master calligraphers like Mir-Ali (who moved from Herat to Bukhara in 1528) were celebrities. A biographer wrote that he "stole from everyone the sword of primacy and excellence in writing."

Outstanding calligraphers created "qit'a"—single sheets bearing a beautifully written aphorism, poem, or Quranic verse. These were highly valued gifts and collector's items.

After the text is complete, comes the decoration: margins covered with gold dust, delicate watercolor drawings, or gold patterns featuring lush plant ornaments or fantastic creatures—unicorns, dragons, phoenixes.

Title pages received elaborate frames (unwan) filled with complex patterns in blue and gold tones, reminiscent of architectural tile work. Or they might have a large cartouche (sarlavh) with intricate ornamentation.

And finally, the miniature paintings—illustrations that were complete artworks in themselves.

The Samarkand School of Miniature Painting

For years, scholars denied that Central Asia had its own school of miniature painting, dismissing everything as provincial copies of Persian work. But research over the past decades revealed a distinct Maverannahran (Transoxiana) school that flourished from the 15th to 17th centuries.

The style has roots going back to Timur himself. Chronicles mention that Timur's palaces in Samarkand were decorated with large paintings depicting the conqueror, his family, courtiers, battles, hunts, and celebrations. These monumental paintings are lost, but their style influenced the miniatures that survive.

What Makes Central Asian Miniatures Unique?

Larger figures and fewer people in each scene (unlike crowded Persian compositions)
Preference for natural landscapes over architectural backgrounds
Restrained color palettes despite the richness of available pigments
Attention to Central Asian details: facial hair styles, white felt hats with black trim, cavalry boots with pointed heels, specific weapons

The great Kamaleddin Behzad (late 15th century) worked in Herat and influenced all subsequent Central Asian painting. But local masters like Muhammad-Murad Samarqandi (16th-17th century) developed strikingly original styles.

Muhammad-Murad illustrated a Shahnameh manuscript with 115 miniatures of passionate intensity. He chose the most dramatic moments: the blacksmith Kaveh leading a popular uprising, Siyavush proving his innocence by riding through fire, countless battles rendered with such energy you can almost hear the clash of steel. His color sense is remarkable—golden skies, lilac heavens, pale blue hills, wine-red horses, light blue steeds—emphasizing the epic, legendary nature of the stories.

By the 17th century, Bukhara became the center of miniature painting under the patronage of the Ashtarkhanid khans. Masters like Muhammad-Mukim and Avaz-Muhammad created works of sophisticated elegance, with increasing attention to landscape and psychological expression of characters.


THE BLUE MAGIC OF SAMARKAND CERAMICS

Walk through the ruins of Afrasiyab (ancient Samarkand) and you'll see entire ceramic quarters—workshops from the 9th-13th centuries that produced both utilitarian and artistic glazed pottery.

Samarkand developed its own artistic school of ceramics, distinct from anything else in the Islamic world.

The Technical Revolution

The earliest glazed ceramics appeared in Samarkand around the mid-8th century. By the end of that century, local potters were experimenting with lead-tin glazes and colored painting.

By the 9th-10th centuries, they achieved technical perfection:

The clay came from carefully selected loess deposits along riverbanks—secondary deposits that were naturally washed and purified. This was formed into balls and thrown on fast foot-powered wheels.

Before glazing, ceramics received an "angob"—a liquid clay slip that created the base color. White-ground pottery was angob-coated twice. Red backgrounds used iron-rich clay called "juta." Black grounds had their own special mixture.

Then came the painting with metallic oxides: black (various iron oxides), red (iron-rich clay), green (copper oxide), blue-green (tin and copper), yellow, violet (manganese).

The secret that made these colors luminous? Tiny amounts of lead oxide and iron oxide added to the pigments, which created shine and depth after firing.

Finally, the glaze: transparent lead glaze, or lead-tin for opaque white, or lead-copper for transparent green.

The Glory Years: 10th Century Samarkand

The late 10th century saw Samarkand ceramics reach heights never surpassed. The potters achieved perfect harmony of form and decoration.

Shapes were logical, refined, with smooth profiles that might have one or two subtle curves but never broke the overall flow. The decorative elements enhanced rather than fought with the form. Color schemes were lively but harmonious. Everything felt aesthetically complete.

Popular motifs included:
- Calligraphy with blessings and proverbs
- Pomegranates (the "fruit of paradise")
- Birds—especially ducks, doves, eagles—each with symbolic meaning
- The "falling wave" pattern
- Geometric designs based on mathematical precision

Over subsequent centuries, the symbolic content gradually gave way to pure decoration. A rooster that once represented specific concepts became just a decorative motif. Arabic letters that once spelled wisdom became abstract linear patterns.

The Turquoise Revolution

By the 11th-12th centuries, turquoise and blue glazes began dominating Samarkand ceramics. This wasn't random—it reflected the artistic preferences of Turkic rulers who conquered Central Asia.

For Turkic and Mongol peoples, blue and white were sacred colors. The supreme deity was Tengri, the Eternal Blue Sky. To emphasize their divine right to rule, khans called themselves "sons of the eternal heaven" and began their decrees by invoking "the eternal blue sky."

Archaeological evidence is striking: at Mongol sites, 67% of ceramic sherds are blue, 21% black, and other colors appear only rarely.

Blue wasn't just pretty—it was a political and religious statement.

This love of blue-on-white ceramics ultimately evolved into the spectacular tilework that covers the monuments of Samarkand, Bukhara, and Khiva—but that's a story for another day.


KHIVAN WOOD CARVING: POETRY IN TIMBER

Khiva has something no other Central Asian city possesses: wooden columns from the 10th-14th centuries, preserved by being moved into later buildings. In the Juma Mosque alone, over 200 carved columns create a forest of wooden artistry.

Why Wood Dominated Khivan Architecture

Khiva's climate and lifestyle made carved wood essential. The kush-ayvan system—a tall north-facing ayvan (porch) to catch cool breezes, opposite a smaller south-facing one—created constant airflow. The main ayvan, supported by magnificent carved columns, became the primary living space in Khiva's scorching summers.

A bare, uncarved column on a large ayvan was considered temporary, something you'd replace when you saved enough money to commission proper carved work.

The Art of Elm

Khivan masters worked primarily in local elm (karagach) and poplar. Khorezm elms have a special quality—coarse-grained but even, quite dense and strong. Elms from other regions are harder but more porous, unsuitable for fine ornamental carving.

The peculiarities of elm dictated the artistic style. Khivan carvers exploited the wood's texture rather than fighting it. They didn't polish away the natural grain—they celebrated it. The slightly rough surface harmonizes beautifully with the monumental scale of the decoration.

That's why Khivan carving looks more "alive" than work from other regions—you can see every stroke of the chisel, every decisive cut. It looks effortless, but that apparent casualness masks profound skill.

Unlike carvers in Tashkent, Kokand, Bukhara, and Samarkand, Khivan masters never used punched backgrounds (gurzi)—the technique of covering the background with tiny hammered dots, borrowed from metalwork. They simply carved the background smooth and left it at that, letting the natural wood grain provide texture.

The Master: Ata Palvanov

I'm fortunate to have met master carver Ata Palvanov (1867-1960s), one of the last living links to the old tradition. Born into a family of hereditary carvers, he learned from his father Palvan and grandfather, working with them on countless doors and columns throughout Khiva.

"Karaman elm is best," he told me. "It doesn't crack. For fine doors with plant patterns (naqsh kapu islimi), I use karaman, sometimes mulberry or apricot wood. Two masters can complete such a door in a month."

His tool kit: 30 chisels of varying sizes and profiles—straight (togri qalam) and grooved (margula qalam), plus special curved blades for clearing backgrounds (zamin qalam) and refining relief with delicate grooves (tahrir qalam).

The design is transferred using "ulgi" (stencil)—a term used only in Khiva; elsewhere in Uzbekistan, they say "akhta." In his youth, Ata Palvanov created his own designs, but for the last 30 years of his career, he worked from patterns by ornamentalists Abdulla Boltaev and Ruzmet Masharipov.

After carving, the preservation treatment: the entire column or door is quickly swabbed with red-hot cottonseed oil. The oil, heated in a cauldron, is applied with a large cloth swab tied to a stick. A medium door requires about one kilogram of oil. The better the oil treatment, the more durable the carving—it fears neither sun nor moisture.

Pattern Vocabulary

"Aylanma islimi" (spiral vine): Curling shoots covered with flowers, leaves, and branches
"Kuchkorak" (ram's horn): The beloved spiral motif, in countless variations
"Madakhil" (peculiar trefoils): Elongated three-leaf patterns
"Gull" and "nimgull" (flower and half-flower): Rosettes of all sizes
"Margula/margulak" (little circles): Tiny circles scattered across open spaces, connecting to the main pattern
"Turundj" (large medallion): Occupying the entire central field of a single-plank door

The slowness of architectural evolution—forms refined over centuries—explains why traditional crafts change so gradually. What works perfectly needs no improvement.


SILK: THE FABRIC THAT MOVED EMPIRES

The story of Central Asian silk deserves its own epic. For 1,500 years, silk was currency, diplomacy, and art combined.

By the early medieval period (5th-8th centuries), Central Asia—especially Sogdiana—became one of the world's great silk producers. The Chinese monopoly on silk cocoons was broken, and Ferghana, Sogd, and Khorasan began raising their own silkworms.

This triggered an explosion of artistic silk production. Everywhere the silkworm appeared, fine patterned silks followed.

The "Zandana" Revelation

For years, magnificent silks preserved in European church treasuries puzzled scholars. Then came a breakthrough: on the reverse of a silk in the cathedral of Huy, Belgium, someone found a Sogdian inscription mentioning the village of "Zandana."

This silk shows typical Central Asian heraldic compositions—lions and rams flanking a tree, all within circles of pearls. The discovery allowed scholars to properly attribute dozens of silks previously labeled "Coptic" or "Byzantine" to their true origin: Sogdiana.

What makes Sogdian silks recognizable?
- Single motifs in circles, often mirrored, rather than complex multi-figure scenes
- Energetic, somewhat rough treatment of animals (retaining Hellenistic expressiveness)
- Less flattened figures than Byzantine or Egyptian work
- Specific compositional devices like double-row pearl circles with palmettes between them

The wall paintings of Panjikent, Samarkand's Afrasiyab, and Varakhsha preserve an unmatched record of medieval textile patterns: winged horses, griffins, senmurvs (dog-birds), phoenixes, ducks, pheasants, peacocks, along with complex geometric and floral designs.

Some of these were clearly heavy fabrics—brocades embroidered with gold and silver thread. Others were lighter silks with woven patterns.

The Technical Achievement

Sogdian weavers used multi-heddle looms that could reproduce virtually any design in colored thread. Unlike Chinese silks, Sogdian pieces were woven using techniques developed for cotton textiles—a Central Asian innovation.

They created:
- Polychrome figured silks with animal and human motifs
- Single-color damasks (kimkhab) with woven patterns
- Striped and checkered silks in brilliant colors
- Gold and silver brocades

The color palette was distinctive. In Sogdiana and northern areas, a wide range of colors prevailed. In the south (Turkmenistan, southern Uzbekistan), warm tones dominated: purples, reds, yellows, violets—the heritage of Parthian traditions.

The Legacy Lives

By the 14th-15th centuries, under Timur and the Timurids, Central Asian textile production reached new heights. Craftsmen brought from across the conquered territories found in Samarkand and Bukhara the resources and patronage to perfect their art.

Samarcand silks, gold brocade, atlas, damask, satin, taffeta, and the legendary Bukhara velvet gained worldwide fame.

Even today, in workshops in Margilan, Bukhara, and Samarkand, weavers continue the tradition. The techniques are ancient, but the creativity is eternal.


EXPERIENCING THE CRAFTS TODAY

This isn't just history—it's living culture. Throughout Uzbekistan, you can watch these crafts being practiced and even try your hand at some:

Samarkand: Visit the Koni-Ghil ceramics workshop where descendants of medieval potters still shape clay and mix glazes according to recipes that are family secrets.

Bukhara: The Bukhara Artisan Development Center offers workshops where you can meet carpet weavers, miniature painters, and textile artists at work.

Khiva: Wander the Ichan Kala (inner city) to find workshops where men carve wooden columns and doors using tools and techniques unchanged for centuries.

Margilan: The Yodgorlik Silk Factory demonstrates the entire silk production process, from cocoon to finished cloth. It's mesmerizing.

Tashkent: The Museum of Applied Arts occupies a traditional house and displays the finest examples of Uzbek crafts, with many pieces you can examine up close.

Best of all: attend a craft demonstration workshop. Many guesthouses and cultural centers arrange sessions where master craftspeople explain their work and let you try simple techniques. There's something profound about shaping clay on a wheel or drawing a brush across silk—you connect directly to traditions that stretch back a thousand years.


THE DEEPER MEANING

These crafts aren't quaint folk arts preserved for tourists. They're sophisticated artistic traditions that required—and still require—years of apprenticeship to master. They represent the accumulated wisdom of centuries of experimentation, failure, and refinement.

When a Khivan master carves a column, he's making decisions informed by a thousand years of experience: the angle of the cut to catch the light, the depth of relief that won't crack the wood, the scale of pattern that remains visible from ground level, the oil treatment that will preserve the work for generations.

When a Samarkand potter mixes glazes, she's following formulas developed through centuries of trial and error, balancing chemistry and aesthetics to achieve those legendary blues and golds.

These aren't museum pieces. They're living proof that beauty, craftsmanship, and tradition can survive war, conquest, modernization, and ideology—because they fulfill something fundamental in the human spirit.

And that's something worth traveling to Central Asia to witness.


PRACTICAL INFORMATION

Best Places to See Traditional Crafts:
- State Museum of Applied Arts, Tashkent
- Museum of Afrasiyab, Samarkand (ceramics)
- Khiva woodcarving workshops (throughout Ichan Kala)
- Margilan silk factories

Craft Workshops and Demonstrations:
- Koni-Ghil ceramics workshop, Samarkand
- Bukhara Artisan Development Center
- Various carpet-weaving cooperatives in Bukhara, Samarkand, and rural areas
- Yodgorlik Silk Factory, Margilan (tours daily)

Buying Authentic Crafts:
Look for items marked with the master's signature or workshop seal. Be aware that much of what's sold in tourist bazaars is mass-produced. For authentic pieces:
- Visit master workshops directly
- Ask local tour guides for recommendations
- Check with the Museum of Applied Arts—they maintain a list of certified masters
- Expect to pay appropriately—true handwork isn't cheap

If a carpet or silk seems like a bargain, it's probably not handmade. Authentic Uzbek carpets take weeks or months to create; genuine hand-dyed silk textiles require days of work. The prices reflect the time and skill invested.

But whether you buy anything or not, take the time to watch the craftspeople at work. It's a meditation on patience, skill, and beauty.
CONTENT;
    }
}
