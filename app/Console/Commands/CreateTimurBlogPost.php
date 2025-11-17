<?php

namespace App\Console\Commands;

use App\Models\BlogPost;
use App\Models\BlogCategory;
use App\Models\BlogTag;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class CreateTimurBlogPost extends Command
{
    protected $signature = 'create:timur-blog-post';
    protected $description = 'Create comprehensive blog post about Timur (Tamerlane) based on authentic historical research';

    public function handle()
    {
        $this->info('Creating Timur/Tamerlane blog post...');

        // Get category
        $cultureHistory = BlogCategory::where('slug', 'culture-history')->first();

        // Create the blog post
        $post = BlogPost::create([
            'category_id' => $cultureHistory->id,
            'title' => 'Timur the Great: The Truth Behind Tamerlane, Conqueror of Eurasia',
            'slug' => 'timur-tamerlane-conqueror-history-uzbekistan',
            'excerpt' => 'History remembers him as "Tamerlane the Terrible"—but who was the man who built an empire stretching from Delhi to Moscow, yet never claimed the title of Khan? The true story of Timur is far more complex than the legends.',
            'content' => $this->getTimurContent(),
            'featured_image' => 'blog/timur-history.jpg',
            'author_name' => 'Rustam Karimov',
            'author_image' => 'authors/rustam.jpg',
            'reading_time' => 15,
            'view_count' => 0,
            'is_featured' => true,
            'is_published' => true,
            'published_at' => now()->subDays(1),
            'meta_title' => 'Timur (Tamerlane): The Untold Story of Central Asia\'s Greatest Conqueror',
            'meta_description' => 'Discover the true history of Timur (Tamerlane), who conquered an empire from India to Turkey but never became Khan. His military genius, the building of Samarkand, and his complex legacy.',
        ]);

        // Attach tags
        $tags = ['history', 'silk-road', 'uzbekistan', 'samarkand'];
        $tagIds = BlogTag::whereIn('slug', $tags)->pluck('id');
        $post->tags()->attach($tagIds);

        $this->info("Created: {$post->title}");
        $this->info("\n✅ Timur blog post created successfully!");
    }

    private function getTimurContent()
    {
        return "His enemies called him Timur the Lame—Timur-i-Lang in Persian, corrupted to \"Tamerlane\" in European tongues. History books portray him as a bloodthirsty tyrant who built pyramids from human skulls. Yet this \"barbarian\" spoke multiple languages, debated theology with Islamic scholars, played chess with court philosophers, and transformed Samarkand into the most magnificent city on Earth.

The truth about Timur—Amir Timur, Timur the Great, Sahib-Qiran (Lord of the Fortunate Conjunction)—is far more complex than the legend. After spending weeks researching his life through Central Asian historical sources, I discovered a man who was simultaneously brilliant strategist, ruthless warrior, devout Muslim, patron of arts, and builder of architectural wonders.

Let me tell you the real story.

THE NAME THAT BECAME A CURSE

His name was Timur, meaning \"iron\" in Turkic. He was born in 1336 in Kesh (modern Shahrisabz) in the Kashkadarya valley, just south of Samarkand. He came from the Barlas tribe—one of the most influential Turkic-Mongol clans that had followed Genghis Khan's conquests a century earlier.

But \"Timur\" isn't how history remembers him.

In his youth, Timur was wounded—accounts vary on how, some say in battle, others claim he was injured while stealing sheep. Whatever the cause, he was left with a lame right leg and a withered right arm. His enemies seized on this disability, mockingly calling him \"Aksak Timur\" (Lame Timur) in Turkic, \"Timur-i-Lang\" in Persian.

Europeans corrupted this to \"Tamerlane,\" and that's the name that stuck. The man who would conquer half the known world entered history books under a nickname invented to ridicule him.

THE PROBLEM OF LEGITIMACY

Here's what Western histories rarely explain: Timur faced an insurmountable political obstacle. He could conquer the world, but he could never legally rule it.

Why? Because of the Yasa—the legal code established by Genghis Khan—which stated that only direct descendants of Genghis Khan (Chingizids) could claim the title of Khan and rule legitimately.

Timur was not a Chingizid. His Barlas ancestors had been close allies of Genghis Khan—his great-great-grandfather served as one of Genghis's trusted commanders. According to some genealogies, both Timur and Genghis Khan descended from a common ancestor named Tumeneh Khan. But this distant connection wasn't enough.

The law was clear: he could not be Khan.

So throughout his life, while he conquered empires and defeated khans, Timur held only the title of \"Amir\" (Emir)—essentially \"commander\" or \"prince.\" He ruled through puppet khans from Genghis Khan's lineage: first Suyurghatmish (1370-1388), then Mahmud (1388-1402). These Chingizid khans had the bloodline and the title, but Timur held the power.

This wasn't just ceremonial humility—this was Timur's greatest vulnerability and the source of his endless wars.

THE CONTINENTAL CONSPIRACY AGAINST HIM

When Timur seized power in Mawarannahr (the land between the Amu Darya and Syr Darya rivers) in 1370, he violated the established order. The legitimate rulers of Eurasia—all Chingizid descendants or their recognized vassals—saw him as a usurper, an upstart who needed to be crushed.

A grand continental alliance formed against him:

TOKTAMISH, Khan of the Golden Horde (whom Timur had actually helped gain power—classic betrayal story)
BAYEZID I, Sultan of the Ottoman Empire
BARQUK, Mamluk Sultan of Egypt
AHMAD JALAYIR, ruler of Baghdad
The Jelairids, Kara Koyunlu Turkmens, and various smaller powers

This wasn't just regional politics. This was the equivalent of a medieval world war coalition, spanning from the Mediterranean to the Volga, all coordinated to destroy Timur and restore the \"proper\" order.

Documents show constant diplomatic exchanges between these powers. Toktamish sent envoys to the Mamluk Sultan. The Ottomans coordinated with Baghdad. They planned simultaneous attacks from multiple directions.

Timur discovered the conspiracy through his extensive intelligence network—a sophisticated system of spies and informants across Eurasia. Realizing he couldn't fight them all at once, he made a strategic decision that would define his life: attack them separately, one by one, before they could unite.

This is why Timur never stopped fighting. It wasn't bloodlust or megalomania. It was survival. If he stopped, if he withdrew to Samarkand to enjoy his palaces, the coalition would destroy him and devastate Mawarannahr.

THE MILITARY GENIUS

Timur's military tactics are still studied in war colleges today. He didn't just fight—he revolutionized warfare.

THE OBLIQUE FLANK ATTACK
Historians credit Prussian King Frederick II with inventing the oblique flank attack in the 18th century. Frederick used it brilliantly. But Timur was using this tactic 350 years earlier.

Timur would mass his elite cavalry on the right flank in two echelons—vanguard and reserve. He'd strike the enemy's left flank with overwhelming concentrated force, break through, and wheel into their rear while the enemy's right flank remained unengaged.

Instead of pushing forward across the entire line (which causes maximum casualties), Timur concentrated power at a single point, achieved local superiority, broke the enemy formation, and rolled up their line from behind.

THE HEDGEHOG FORMATION
In 1395, facing Toktamish's forces at the Terek River, Timur faced a tactical problem. Toktamish's troops used encirclement tactics—they'd sweep around both flanks in a crescent formation and surround the enemy.

Timur anticipated this. He deployed in a defensive hedgehog formation with massively reinforced rear guard. When Toktamish's forces swept around him in their trademark crescent, Timur held the defensive position. Then, once Toktamish's forces were extended in their encirclement, Timur struck with his reserves directly at the enemy center, splitting Toktamish's army in half.

While his rear guard protected his back, Timur's forces systematically destroyed the divided halves of the Golden Horde army. He surrounded the army that had surrounded him.

Toktamish, the great khan who had unified the Golden Horde and terrorized Russia, fled the field. His power was broken forever.

DECEPTION AND INTELLIGENCE
Timur was a master of strategic deception. He never revealed his true objectives. He'd march his army in one direction, convincing everyone he was attacking Target A, then at the last moment pivot and strike Target B, which was unprepared.

His intelligence network rivaled any modern spy service. Agents reported from across Eurasia about enemy movements, supply situations, and political intrigues. He used this information to strike when and where enemies were weakest.

He also pioneered information warfare. His agents spread rumors, bribed officials, and psychologically prepared target cities for conquest—sometimes cities would surrender before Timur arrived because his fearsome reputation (carefully cultivated and exaggerated) preceded him.

THE PARADOX OF TIMUR

Here's what makes Timur so difficult to understand from a modern perspective: he was simultaneously destroyer and builder, barbarian and intellectual, tyrant and patron.

THE DESTROYER
The accounts of Timur's conquests are horrifying by any standard. Cities that resisted were utterly destroyed. Isfahan rebelled after initially surrendering—Timur's troops killed an estimated 70,000 people and built towers from their skulls. Delhi was sacked so thoroughly that it didn't recover for a century. Baghdad, once the jewel of Islamic civilization, was devastated.

These weren't random acts of cruelty—they were calculated terror tactics. Timur would spare cities that surrendered immediately but absolutely annihilate those that resisted. The message was clear: submit and live, resist and face complete destruction.

Was this unusually brutal for the era? Not particularly. Medieval warfare was savage everywhere. But Timur operated on a scale that was extraordinary—his campaigns stretched across continents, and his armies were large enough to destroy major cities.

THE BUILDER
Yet this same man transformed Samarkand into a wonder of the world. He scoured conquered territories for the finest architects, craftsmen, artists, and scholars, bringing them to Samarkand. His orders to his commanders were specific: find the skilled artisans and bring them unharmed.

Persian architects, Damascus craftsmen, Indian engineers, scholars from Baghdad—all were transported to Samarkand. The city exploded in construction: the Bibi-Khanym Mosque (celebrating his Indian campaign), the Gur-e-Amir Mausoleum (built for his beloved grandson), magnificent palaces, gardens, and madrasas.

Timur built a road network across his empire with caravanserais every 30 kilometers where travelers could rest, change horses, and find protection. Trade flourished under his rule—merchants could travel from China to the Mediterranean under his protection.

He reduced tax burdens in newly conquered territories, understanding that economic relief would stimulate growth and eventually yield higher revenues. Under Timur, the Silk Road reached its medieval peak.

THE INTELLECTUAL
Timur was illiterate—he never learned to read or write. Yet he was highly educated in his own way. He surrounded himself with scholars, historians, theologians, and philosophers. He absorbed knowledge through conversation and debate.

He knew history intimately—especially the campaigns of Alexander the Great (whose path he deliberately followed) and Genghis Khan (whose legal code he revered). He could debate Islamic theology with ulama (scholars). He was a master chess player who used the game to teach military strategy.

When envoys arrived from foreign courts, Timur interrogated them about their lands—geography, customs, governance, resources. His knowledge of Eurasia was encyclopedic.

He spoke Chagatai Turkic (his native tongue), Persian (the language of administration and culture), and understood Mongolian and some Arabic.

THE RELIGIOUS DIMENSION

Timur's relationship with Islam is complex and often misunderstood.

He was a devout Muslim who built mosques, supported scholars, went on pilgrimage, and styled his conquests as jihad against infidels and heretics. Every victory was marked by construction of a commemorative religious building.

Yet he also followed the Yasa, the Mongol legal code of Genghis Khan, which sometimes contradicted Islamic sharia. The Yasa governed his army and the nobility; sharia governed the settled civilian population.

This dual legal system—secular Mongol military law and Islamic civilian law—allowed Timur to unite Turkic-Mongol warriors with settled Muslim populations. It was a pragmatic synthesis that worked.

His religious justifications for war were often cynical. When attacking the Muslim Ottoman Sultan Bayezid, Timur accused him of insufficient devotion. When attacking Muslim Delhi, he claimed the sultans were too lenient toward Hindus. When attacking Christian Georgia, it was jihad against infidels.

Was Timur genuinely pious or merely using religion politically? Probably both. Medieval rulers saw no contradiction between sincere faith and political manipulation of religion.

THE BATTLE OF ANKARA: TIMUR VS THE OTTOMANS

July 28, 1402. Near Ankara, Anatolia.

This was Timur's most famous battle, where he faced Sultan Bayezid I, called \"the Thunderbolt\" for his lightning military campaigns. Bayezid had crushed the last crusade at Nicopolis in 1396 and was besieging Constantinople, seemingly unstoppable.

Timur marched into Anatolia with a massive army—estimates range from 140,000 to 200,000 men. Bayezid lifted the siege of Constantinople and rushed to meet him with about 85,000-120,000 troops.

Timur's intelligence network and psychological warfare paid off. Many of Bayezid's Turkmen vassals defected to Timur before the battle, recognizing a fellow Turkic conqueror. Bayezid's army was already weakened by thirst—Timur had diverted water sources away from the Ottoman camp.

The battle was decisive. Timur's forces shattered the Ottoman army. Bayezid himself was captured, becoming the only Ottoman sultan ever taken prisoner in battle.

What happened to Bayezid afterward is disputed. Some sources say Timur treated him honorably; others claim he was humiliated and caged. Most historians now believe the \"iron cage\" story is propaganda—Bayezid was likely held under guard but not cruelly treated. He died in captivity eight months later, possibly by suicide.

For Europe, this was salvation. Constantinople, which seemed doomed to fall, gained fifty more years of life. The Ottoman advance into Europe was halted for a generation.

THE LAST CAMPAIGN: CHINA

In late 1404, at age 68, crippled and ill, Timur announced his final campaign: the conquest of Ming China.

This sounds insane. China was the most powerful, populous, and advanced empire on Earth. The Ming Dynasty had 60 million people, a sophisticated bureaucracy, advanced technology, and formidable armies.

But Timur had his reasons. China represented the ultimate prize—immense wealth, ancient prestige, and control of the eastern end of the Silk Road. More importantly, China had sent envoys that Timur considered insufficiently respectful. In the diplomatic language of the era, China treated Timur as an inferior.

This he could not accept.

Timur assembled an army of 200,000 and began the march east in December 1404, in the depths of winter. His health was failing, but he pushed forward.

He never reached China. On February 17, 1405, in the city of Otrar (in modern Kazakhstan), Timur fell ill and died. He was 68 years old.

His body was returned to Samarkand and buried in the Gur-e-Amir mausoleum he had built for his grandson.

THE LEGACY

Timur's death triggered immediate civil war among his descendants. His empire fragmented. Yet his dynasty, the Timurids, ruled parts of Central Asia and Persia for another century, presiding over a cultural renaissance.

His grandson Ulugbek became a brilliant astronomer, building an observatory in Samarkand and producing astronomical tables used for centuries.

The Timurid artistic style—especially miniature painting—reached extraordinary heights under artists like Behzad in Herat.

And in 1526, Timur's great-great-great-grandson Babur, driven from Central Asia, conquered northern India and founded the Mughal Empire, which ruled India for three centuries. The Taj Mahal, built by Mughal emperor Shah Jahan, is part of Timur's artistic legacy.

TIMUR TODAY

In Uzbekistan, Timur has been rehabilitated from Soviet-era condemnation (when he was portrayed as a feudal oppressor) to national hero. His statue stands in Tashkent's Amir Timur Square. He's celebrated as the founder of the Uzbek state, a military genius, and a patron of culture.

Is this historical revisionism? Partly. But it's also recovery of a more balanced view. Soviet historiography emphasized only Timur's brutality. Modern Uzbek historiography emphasizes his achievements. The truth lies somewhere in between.

VISITING TIMUR'S LEGACY

You can walk through Timur's world in Uzbekistan:

GUR-E-AMIR MAUSOLEUM, SAMARKAND: Timur's tomb, where his body still rests in a jade sarcophagus. The blue ribbed dome is one of Central Asia's most iconic images. Inside, the chamber where Timur, his sons, grandsons including Ulugbek, and his spiritual advisor are buried.

When Soviet archaeologists opened Timur's tomb in 1941, they found a skeleton of a tall man (about 5'8\", tall for the era) with injuries consistent with historical accounts—lame right leg, damaged right arm. Two days after they opened the tomb, Hitler invaded the Soviet Union. Coincidence or curse? In 1942, Timur was reburied with Islamic rites, and shortly after, the tide turned at Stalingrad.

AK-SARAY PALACE, SHAHRISABZ: The ruins of Timur's summer palace in his birthplace. Only the entrance portal remains—a 38-meter-high arch that once held an inscription: \"If you doubt our power, look at our buildings.\" The palace was said to rival anything in the world.

REGISTAN, SAMARKAND: While the current buildings postdate Timur, he transformed Registan Square into the heart of his capital.

BIBI-KHANYM MOSQUE, SAMARKAND: Built after Timur's Indian campaign, once one of the Islamic world's largest mosques. The construction was so rushed (Timur wanted it finished before his return) that it began collapsing within decades. Still, its massive scale impresses.

THE VERDICT

Timur was not a good man by modern moral standards. He killed hundreds of thousands, destroyed cities, and spread terror across continents.

But judging medieval rulers by modern humanitarian standards is ahistorical. By the standards of his time, Timur was neither exceptionally cruel nor unusually benevolent—he was exceptionally successful.

His military genius is undeniable. His administrative skill in managing a vast empire while constantly on campaign was remarkable. His patronage transformed Samarkand into a cultural capital. His infrastructure investments boosted trade. His dynasty produced scholars, artists, and rulers who shaped Central Asian and Indian culture for centuries.

Most importantly, Timur was not a barbarian destroyer mindlessly wrecking civilization. He was a sophisticated leader pursuing strategic goals with every tool available—military conquest, diplomatic intrigue, economic policy, religious legitimization, and cultural patronage.

Understanding Timur requires moving beyond the \"Tamerlane the Terrible\" caricature to see the man in his historical context: a brilliant, ruthless, complex figure who shaped the medieval world and whose legacy still echoes in the streets of Samarkand today.

When you visit the Gur-e-Amir and stand before Timur's tomb, remember: beneath that jade stone lies not a cartoon villain, but a man who built an empire stretching from Delhi to Damascus, who debated theology and played chess, who built wonders and destroyed cities, who could never be Khan but conquered half the world anyway.

That's the real Timur. Iron by name, iron by nature.";
    }
}
