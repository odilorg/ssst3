<?php

namespace App\Console\Commands;

use App\Models\BlogPost;
use App\Models\BlogCategory;
use App\Models\BlogTag;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class CreateSilkRoadBlogPost extends Command
{
    protected $signature = 'create:silk-road-blog-post';
    protected $description = 'Create comprehensive blog post about the Great Silk Road based on authentic historical research';

    public function handle()
    {
        $this->info('Creating Great Silk Road blog post...');

        // Get category
        $cultureHistory = BlogCategory::where('slug', 'culture-history')->first();

        // Create the blog post
        $post = BlogPost::create([
            'category_id' => $cultureHistory->id,
            'title' => 'The Great Silk Road: How Central Asia Connected Two Worlds',
            'slug' => 'great-silk-road-history-central-asia-trade',
            'excerpt' => 'It wasn\'t just about silk. For 1,500 years, the Silk Road was the internet of the ancient world—moving goods, ideas, religions, and technologies across Eurasia. And Central Asia wasn\'t just a transit corridor. It was the heart of it all.',
            'content' => $this->getSilkRoadContent(),
            'featured_image' => 'blog/silk-road-history.jpg',
            'author_name' => 'Rustam Karimov',
            'author_image' => 'authors/rustam.jpg',
            'reading_time' => 16,
            'view_count' => 0,
            'is_featured' => true,
            'is_published' => true,
            'published_at' => now()->subHours(12),
            'meta_title' => 'The Great Silk Road: Complete History of the Ancient Trade Route',
            'meta_description' => 'Discover the true history of the Great Silk Road: how Sogdian merchants created it, why Central Asia was its heart, what was really traded, and how it shaped world civilization for 1,500 years.',
        ]);

        // Attach tags
        $tags = ['silk-road', 'history', 'uzbekistan', 'travel-guide'];
        $tagIds = BlogTag::whereIn('slug', $tags)->pluck('id');
        $post->tags()->attach($tagIds);

        $this->info("Created: {$post->title}");
        $this->info("\n✅ Silk Road blog post created successfully!");
    }

    private function getSilkRoadContent()
    {
        return "The German geographer Ferdinand von Richthofen coined the term \"Seidenstraße\" (Silk Road) in 1877. The name stuck. But calling it the \"Silk Road\" is like calling the internet the \"cat video highway\"—technically accurate but missing the bigger picture.

Yes, Chinese silk moved west along these routes. But so did Persian carpets, Indian spices, Roman glassware, Arabian horses, Buddhist scriptures, Islamic astronomy, Greek philosophy, gunpowder, paper, printing, mathematical concepts, artistic techniques, diseases, and religious movements that changed civilizations.

The Silk Road wasn't a road. It was a network—actually three major networks—of trade routes, cultural exchanges, and human migrations that connected the ancient world for over 1,500 years.

And Central Asia wasn't just a corridor between East and West. It was the beating heart of the system.

Let me explain how it actually worked.

BEFORE THE SILK ROAD: THE ANCIENT PATHS

Long before Chinese silk reached Roman markets, ancient peoples were already moving across Central Asia. These weren't aimless wanderings—they were purposeful migrations following rivers, seeking resources, and establishing trade connections.

As early as the 4th-3rd millennium BCE, agricultural tribes were migrating from the Helmand and Indus river valleys into Central Asia, crossing the towering Hindu Kush mountains. This required sophisticated route-finding and mountain-crossing techniques.

By the 2nd millennium BCE, specific commodities were being traded over long distances:

LAPIS LAZULI from Badakhshan (in modern Afghanistan) reached Mesopotamia, Iran, and Egypt. This deep blue stone was so valuable that ancient Egyptians used it for pharaonic jewelry and burial masks.

CARNELIAN from Sogdiana moved to Persia and beyond.

TIN and COPPER—essential for bronze—were traded from Central Asian deposits to regions lacking these metals.

These early \"proto-Silk Road\" paths followed logical routes:
- Along the Amu Darya (Oxus River)
- Through the Zeravshan valley
- Across mountain passes connecting river basins
- Along foothill corridors avoiding both desert and high mountains

Ancient Persian inscriptions from the Achaemenid period (6th-4th centuries BCE) specifically mention lapis lazuli and carnelian coming from Sogdiana and Bactria. The Greek historian Ctesias wrote about silver and gold mines in Bactria.

By the time Alexander the Great invaded Central Asia in 329 BCE, he wasn't crossing unknown territory—he was following routes that had been used for trade and migration for thousands of years.

THE ROYAL ROAD: THE ACHAEMENID PROTOTYPE

The Achaemenid Persian Empire (550-330 BCE) created what might be called the first \"Silk Road\"—though it predated silk trade by centuries.

Herodotus and Ctesias described the famous \"Royal Road\" connecting the western and eastern provinces of the Persian Empire. This wasn't a single paved highway—it was a system of routes with:

- Regularly spaced stations every 5 parasangs (about 27 km)
- Rest houses and changing posts for messengers
- Security patrols
- Water supplies

This infrastructure enabled rapid communication and safe trade across the empire. A message could travel from Sardis (in western Turkey) to Susa (in Persia) in just days—a journey that would take ordinary travelers months.

The concept was brilliant: control the routes, provide security and infrastructure, tax the merchants, profit from trade while maintaining political communication.

This Persian model would be copied by every subsequent empire that controlled Central Asian trade routes.

THE BIRTH OF THE SILK ROAD: THE ZHANG QIAN EXPEDITION

The traditional starting date for the Silk Road is 138 BCE, when the Chinese diplomat and explorer Zhang Qian set out on an epic journey.

THE MISSION
Emperor Wu of the Han Dynasty had a problem: the Xiongnu (nomadic peoples to the north) were constantly raiding Chinese territory. Wu needed allies. He'd heard rumors of a people called the Yuezhi who had been driven west by the Xiongnu and might want revenge.

Zhang Qian volunteered to find them. He left China with about 100 men.

THE JOURNEY
Almost immediately, Zhang Qian was captured by the Xiongnu. He spent ten years as a prisoner, married a Xiongnu woman, and had children. Then he escaped.

He continued west, finally reaching the Yuezhi (who had settled in Bactria, in modern northern Afghanistan). They weren't interested in an alliance—they were quite happy in their new, fertile homeland.

But Zhang Qian didn't return empty-handed. He spent years exploring Central Asia, visiting Fergana, Sogdiana, and Bactria. He observed their economies, agriculture, cities, and trade networks. When he finally returned to China in 126 BCE (after being captured again by the Xiongnu and escaping again), he brought invaluable intelligence.

THE REVELATION
Zhang Qian reported something that shocked the Han court: Chinese goods—especially silk and bamboo—were already being sold in Bactrian markets, having arrived via India. There were established trade routes the Chinese knew nothing about.

More importantly, he described the magnificent horses of Fergana—the \"heavenly\" or \"blood-sweating\" horses that were far superior to Chinese breeds. He described prosperous cities, sophisticated irrigation, grape wine, alfalfa, and walnuts unknown in China.

Zhang Qian's reports ignited Chinese interest in the \"Western Regions.\" Within decades, Han China established military and trade outposts across Central Asia, creating the eastern anchor of what would become the Silk Road.

THE SOGDIAN MIRACLE: THE MERCHANTS WHO MADE IT WORK

Here's what most histories miss: the Silk Road wasn't created by Chinese or Romans. It was created by Sogdian merchants.

The Sogdians were an Iranian people based in Sogdiana (centered on modern Samarkand and Bukhara). And they were arguably the most successful merchant culture in human history.

SOGDIAN TRADE COLONIES
By the 4th century CE, Sogdian merchant colonies existed along the entire length of the Silk Road:

In Central Asia: Samarkand, Bukhara, Panjikent
In Xinjiang: Kashgar, Khotan, Turfan, Dunhuang
In China: Chang'an (Xi'an), Luoyang
In the West: Sogdian communities in Crimea, possibly reaching Constantinople

These weren't just trading posts—they were self-governing communities with their own temples, laws, and quarter of cities. A Sogdian merchant from Samarkand could travel to Chang'an and find familiar faces, his language spoken, his gods worshipped.

THE SOGDIAN ADVANTAGE
What made Sogdians so successful?

1. LANGUAGE SKILLS: Sogdians were polyglots. They learned Chinese, Turkic, Arabic, Greek—whatever was necessary for trade.

2. NETWORKS: Family-based trading firms maintained branches across Eurasia. Trust was ensured through kinship.

3. RELIGIOUS TOLERANCE: Sogdians practiced Zoroastrianism, Buddhism, Manichaeism, and later Islam—whatever worked in local markets.

4. FINANCIAL INNOVATION: They developed credit systems, letters of credit, and partnership contracts that enabled long-distance trade without moving massive amounts of gold.

5. CULTURAL ADAPTABILITY: Sogdians adopted local customs while maintaining their commercial identity.

THE FIRST MARITIME CONNECTION
Around 568 CE, a Sogdian merchant accomplished something extraordinary: he sailed from the eastern Mediterranean to Constantinople, opening the first \"Caucasian Silk Road\" sea route.

This same merchant culture was trading on Indian Ocean routes, moving silk from China to India and beyond.

Sogdians didn't just participate in the Silk Road—they essentially invented it as a functioning commercial system.

THE THREE GREAT ROUTES

By the early centuries CE, the Silk Road had crystallized into three major transcontinental routes:

ROUTE 1: THE SOUTHERN OVERLAND ROUTE
Chang'an (China) → Dunhuang → Kashgar → Samarkand → Merv → Persian Empire → Syria → Mediterranean ports

This was the classic Silk Road, passing through the oases of the Tarim Basin, over the Pamirs, through the Zeravshan valley, across Persia, and into Roman Syria or Egypt.

Journey time: 6-8 months from Chang'an to Antioch
Key products: Chinese silk moving west; Roman gold, glassware, and wine moving east

ROUTE 2: THE MARITIME ROUTE
Alexandria (Egypt) → Red Sea → Indian Ocean → Arabian Sea → ports of western India → Bay of Bengal → Strait of Malacca → South China Sea → Chinese ports

This route became increasingly important after Rome conquered Egypt in 30 BCE. Ships could carry far more cargo than caravans and avoid the dangers of overland travel.

Journey time: 2-3 months from Egypt to India with monsoon winds; another 2-3 months to China
Key advantage: Bulk goods like spices, incense, gems could be shipped efficiently

ROUTE 3: THE STEPPE ROUTE
Chang'an → north of the Tianshan Mountains → Kazakhstan steppes → north of the Caspian Sea → north of the Black Sea → Crimea → Europe

Or alternatively: branching south through the Caucasus Mountains → Anatolia → Constantinople

This northern route was controlled by steppe nomads—Scythians, Sarmatians, later Turks and Mongols. It was dangerous but fast.

Journey time: Variable, but potentially faster than the southern route
Key products: Furs, amber, slaves, horses

WHAT WAS REALLY TRADED?

Let's be specific about what moved along these routes:

FROM CHINA (MOVING WEST):
- Silk (obviously)—raw silk thread, woven fabrics, embroidered goods
- Porcelain and ceramics
- Tea (later periods)
- Paper (especially after 751 CE when Arabs captured Chinese papermakers)
- Gunpowder and fireworks (later periods)
- Cast iron and steel
- Peaches, apricots, certain citrus

FROM THE WEST (MOVING EAST):
- Roman/Byzantine glassware (highly prized in China)
- Gold and silver coins
- Woolen textiles and carpets
- Wine and grapes
- Precious stones (emeralds, pearls, coral)
- Frankincense and myrrh
- Asbestos cloth (a Roman specialty)
- Horses (especially from Fergana)

FROM CENTRAL ASIA:
- Horses (Fergana's \"blood-sweating\" horses were legendary)
- Cotton textiles
- Dried fruits (Bukhara's plums were famous across Asia)
- Melons (Khorezmian melons reached distant markets)
- Carpets and rugs
- Metalwork—bronze, copper, silver vessels

FROM INDIA:
- Spices (pepper, cinnamon, cardamom)
- Cotton textiles (Indian muslins were prized everywhere)
- Gems (diamonds, rubies, sapphires)
- Ivory
- Indigo dye
- Sugar (later periods)

THE ECONOMICS OF SILK

Let's focus on silk specifically, since it gave the route its name.

Chinese silk was a monopoly product. For over 2,000 years, China guarded the secrets of sericulture (silkworm cultivation) and silk production. Exporting silkworm eggs or divulging production methods was punishable by death.

The markup was extraordinary. Silk that cost X in China might sell for 100X or more in Rome. Here's why:

WEIGHT AND VALUE: Silk was the medieval equivalent of microchips—immensely valuable relative to weight. A horse could carry silk worth a fortune.

DISTANCE MARKUP: Every merchant along the route added their profit margin. Sogdian merchant buys from Chinese producer, sells to Persian merchant, who sells to Syrian merchant, who sells to Roman buyer. Each transaction adds 30-50% markup.

TAXES AND TOLLS: Every political entity along the route taxed caravans. Crossing from China to Rome meant paying tolls to dozens of authorities.

RISK PREMIUM: Bandits, weather, accidents meant high losses. Surviving merchants charged accordingly.

Roman historians complained that silk was draining Rome's gold reserves. Pliny the Elder grumbled that \"our ladies and their baubles transfer our money to foreigners.\"

THE TECHNOLOGY TRANSFERS

The Silk Road's greatest legacy wasn't goods—it was ideas and technologies.

WESTWARD TRANSFERS (FROM CHINA):
- Papermaking (8th century) revolutionized Islamic and European record-keeping
- Printing technology (centuries later)
- Gunpowder (changed warfare forever)
- Compass for navigation
- Porcelain production techniques
- Sericulture (eventually leaked to Byzantium in 552 CE, supposedly hidden in hollow staffs by monks)

EASTWARD TRANSFERS (TO CHINA):
- Buddhism (entered via Central Asia in 1st-2nd centuries CE)
- Glassmaking techniques (5th century CE—Chinese glass immediately became cheaper and more common)
- Grape cultivation and wine-making
- Alfalfa and better horse breeds
- Mathematical concepts from India and Persia
- Astronomical knowledge
- Musical instruments
- New crops: sesame, walnuts, pomegranates, cucumbers

CENTRAL ASIAN INNOVATIONS:
- Paper production reached its zenith in Samarkand (Samarkand paper was exported worldwide)
- Astronomical advances (Ulugbek's observatory)
- Mathematical developments (algebra gets its name from al-Khwarizmi from Khiva)
- Agricultural techniques—sophisticated irrigation systems
- Architectural innovations—the pointed arch, double dome, elaborate tilework

THE RELIGIOUS HIGHWAY

The Silk Road was also a spiritual conduit.

BUDDHISM'S JOURNEY WEST
Buddhism originated in northern India around 500 BCE. By the 1st century CE, it was spreading along the Silk Road through Central Asia into China.

Sogdiana, Bactria, and the Tarim Basin cities became Buddhist centers. Massive monasteries, stupas, and caves filled with Buddhist art stretched across Central Asia.

The famous Bamiyan Buddhas in Afghanistan (destroyed by the Taliban in 2001) were Silk Road monuments. The Mogao Caves near Dunhuang in China contain thousands of Buddhist artworks created over a millennium.

Buddhist monks were the Silk Road's most dedicated travelers. They walked from China to India to retrieve sacred texts, established monasteries along the routes, and translated scriptures.

ZOROASTRIANISM, MANICHAEISM, NESTORIAN CHRISTIANITY
Sogdian merchants spread these religions along their trade routes. In Tang Dynasty China (7th-9th centuries), Chang'an had Zoroastrian fire temples, Manichaean churches, and Nestorian Christian communities—all courtesy of Sogdian merchants.

ISLAM'S EXPANSION
In the 7th-8th centuries, Arab Islamic conquests brought Islam to Central Asia. Unlike Buddhism, which spread through missionaries and merchants, Islam came with armies—though trade helped consolidate it.

By the 9th-10th centuries, Central Asia had become the Islamic world's intellectual heart. Cities like Bukhara, Samarkand, Merv, and Balkh produced scholars who revolutionized mathematics, astronomy, medicine, and philosophy.

THE GOLDEN AGE: WHEN CENTRAL ASIA RULED THE WORLD

The 9th-11th centuries were Central Asia's zenith. This is when Samarkand, Bukhara, Merv, and Balkh weren't peripheral—they were central.

While Europe struggled through the Dark Ages, Central Asian cities were:

ECONOMIC POWERHOUSES: Controlling trade between China, India, Persia, and the Mediterranean
INTELLECTUAL CENTERS: Producing scholars like Al-Khwarizmi (algebra, algorithms), Ibn Sina (Avicenna—medicine), Al-Biruni (astronomy, mathematics, anthropology)
CULTURAL CAPITALS: Creating poetry, art, architecture that influenced civilizations from Spain to India
TECHNOLOGICAL LEADERS: Samarkand paper was the world's best; astronomical instruments were the most advanced; mathematical knowledge was cutting-edge

This wasn't accident—this was the natural result of being the Silk Road's heart. Ideas flowed through Central Asia just like goods. Scholars could access Chinese, Indian, Greek, and Persian knowledge—synthesize it, improve it, transmit it.

THE MONGOL DISRUPTION AND REVIVAL

In the 13th century, Mongol conquests devastated Central Asia. Samarkand, Bukhara, Merv—all suffered catastrophic destruction.

But paradoxically, Mongol rule eventually revived the Silk Road.

THE PAX MONGOLICA
Once the conquests ended, the Mongol Empire stretched from Korea to Hungary—the largest contiguous land empire in history. Under Mongol rule:

- Trade routes were secured by military power
- A single authority controlled the entire route (no competing toll collectors)
- Passport systems allowed safe travel
- Way stations provided rest and supplies
- Merchants received protection

European travelers like Marco Polo and William of Rubruck could journey to China with relative safety. Trade boomed.

This is when the Silk Road reached its medieval peak—ironically under the empire that had devastated it.

TIMUR'S RECONSTRUCTION
In the late 14th century, Timur (Tamerlane) rebuilt Samarkand into a wonder of the world. He forced artisans, architects, and scholars from conquered cities to work in Samarkand, creating architectural masterpieces and reviving trade.

Timur's road network—with caravanserais every 30 kilometers—made Silk Road trade safer and faster than it had been in centuries.

THE DECLINE: THREE FATAL BLOWS

The Silk Road's decline resulted from three simultaneous developments:

1. THE MARITIME REVOLUTION (15th-16th CENTURIES)
Portuguese navigators found sea routes to India (Vasco da Gama, 1498) and China. Suddenly merchants could ship goods by sea, avoiding Central Asian middlemen.

Maritime trade was:
- Faster (monsoon winds were predictable)
- Cheaper (ships carry more than camels)
- Safer (no bandits, hostile kingdoms, or mountain passes)

Why pay Silk Road markups when you can ship directly?

2. THE FALL OF CONSTANTINOPLE (1453)
Ottoman Turkish conquest of Constantinople disrupted western Silk Road terminals. While Ottomans continued trading, the political disruption shifted trade patterns.

3. POLITICAL FRAGMENTATION IN CENTRAL ASIA
After Timur's death (1405), Central Asia fragmented into competing khanates. Security deteriorated. Merchants faced multiple toll collectors, inconsistent rules, and political instability.

Meanwhile, Ming China (1368-1644) turned inward, closing borders and restricting trade. The Haijin sea ban policies reduced Chinese participation in international trade.

By the 16th century, the overland Silk Road was dying. Caravans still traveled, but the glory days were over.

THE LEGACY: HOW THE SILK ROAD SHAPED CIVILIZATION

The Silk Road's impact on world history cannot be overstated.

ECONOMIC INTEGRATION: For the first time, Eurasia functioned as an integrated economic zone. Events in China affected Rome; wars in Persia disrupted Chinese trade.

TECHNOLOGICAL DIFFUSION: Paper, printing, gunpowder, compass, agricultural techniques, metallurgy—all spread via Silk Road contacts.

CULTURAL SYNTHESIS: Islamic Golden Age science combined Greek, Indian, Persian, and Chinese knowledge—possible only because scholars in Baghdad, Samarkand, and Bukhara had access to all these traditions.

RELIGIOUS TRANSFORMATION: Buddhism reached China; Islam reached Central Asia and eventually Indonesia; Christianity (Nestorian) briefly flourished in Central Asia and China.

DISEASE TRANSFER: The Black Death (bubonic plague) traveled the Silk Road from China to Europe in the 14th century, killing a third of Europe's population.

CULINARY EXCHANGE: Asian spices reached Europe; Chinese cooking gained new ingredients; Central Asian cuisines blended influences from multiple civilizations.

THE SILK ROAD TODAY: UNESCO AND REVIVAL

In 1988, UNESCO launched the \"Silk Road Programme\" to study and preserve this heritage. International expeditions traced both land and sea routes.

The goals:
- Document and preserve Silk Road heritage sites
- Promote cultural understanding
- Develop sustainable tourism
- Restore caravanserais as historical hotels
- Create international cooperation among Silk Road nations

In 2013, China launched the \"Belt and Road Initiative\"—a modern infrastructure project explicitly invoking Silk Road imagery to build trade connections across Eurasia.

Cities like Samarkand and Bukhara—dormant for centuries—are once again tourism destinations, celebrated for their Silk Road heritage.

WALKING THE SILK ROAD IN UZBEKISTAN

Uzbekistan sits at the Silk Road's heart. Here's where you can experience it:

SAMARKAND - THE SILK ROAD CAPITAL
Registan Square was the commercial and administrative center. Caravans arrived here from China, Persia, India.

Shah-i-Zinda necropolis shows the architectural synthesis—Islamic design with influences from across Eurasia.

Afrosiyab Museum displays Sogdian murals showing merchants, envoys, and goods from multiple civilizations.

BUKHARA - THE TRADING CITY
The trade domes (tok) still stand—Tok-i-Zargaron (jewelers), Tok-i-Sarrafon (money changers). These covered bazaars are where Silk Road commerce happened.

Lyab-i-Hauz was a gathering place for merchants and travelers.

KHIVA - THE LAST CARAVANSERAI CITY
Khiva's Ichan-Kala fortress looks much as it did when caravans arrived from the Kyzylkum Desert. The walled city preserved its medieval character better than anywhere else.

TASHKENT - THE MODERN CROSSROADS
Chorsu Bazaar continues centuries of market tradition. The layout, the goods, the commercial energy—all echo Silk Road trading.

FERGANA VALLEY - THE CRAFTSMEN'S HOMELAND
Margilan (silk production) and Rishtan (ceramics) continue crafts perfected during Silk Road times. You can watch silk being made using techniques 2,000 years old.

THE VERDICT

The Silk Road wasn't just about commerce. It was about connection.

For 1,500 years, it linked civilizations that barely knew each other existed. It moved not just silk and spices, but ideas, technologies, religions, and dreams.

Central Asia wasn't a corridor—it was the hub. Sogdian merchants didn't just carry goods; they built the network. Cities like Samarkand weren't transit points; they were destinations where East and West met, mixed, and created something new.

The Silk Road proved that human civilization is at its best when boundaries are permeable, when merchants and monks and scholars and artists can move freely, when different cultures trade not just products but knowledge.

Walking through Samarkand's Registan or Bukhara's trade domes, you're not just seeing historical monuments. You're standing at the crossroads where Chinese mathematics met Greek philosophy, where Persian art influenced Indian textiles, where Buddhist monks encountered Zoroastrian merchants, where the ancient world's internet physically manifested.

The Silk Road is gone. But its legacy—cultural exchange, international trade, technological transfer—defines our globalized world today.

Every time you drink tea, eat an apricot, use paper, benefit from algebra, or see architectural pointed arches, you're experiencing Silk Road legacy.

The route may be silent. But its echo still shapes civilization.";
    }
}
