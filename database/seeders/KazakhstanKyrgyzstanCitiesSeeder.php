<?php

namespace Database\Seeders;

use App\Models\City;
use Illuminate\Database\Seeder;

class KazakhstanKyrgyzstanCitiesSeeder extends Seeder
{
    /**
     * Seed Kazakhstan and additional Kyrgyzstan cities.
     */
    public function run(): void
    {
        $cities = [
            [
                'name' => 'Zharkent',
                'slug' => 'zharkent',
                'description' => 'Historic town near the Kazakhstan-China border',
                'short_description' => 'Border town with unique Chinese-influenced architecture',
                'long_description' => 'Zharkent is a historic town in southeastern Kazakhstan, near the Chinese border. Founded in 1882, it features unique architecture influenced by both Russian and Chinese styles. The town is famous for its colorful wooden mosque built by Chinese craftsmen without using a single nail. It serves as a gateway between Central Asia and China.',
                'latitude' => 44.1667,
                'longitude' => 80.0000,
                'is_featured' => false,
                'is_active' => true,
                'display_order' => 20,
                'tagline' => 'Gateway to the East',
            ],
            [
                'name' => 'Charyn Canyon',
                'slug' => 'charyn',
                'description' => 'Spectacular canyon with colorful rock formations',
                'short_description' => 'Kazakhstan\'s Grand Canyon with 80km of dramatic landscapes',
                'long_description' => 'Charyn Canyon is a breathtaking natural wonder located 200km east of Almaty. Stretching over 80 kilometers, the canyon features dramatic rock formations sculpted by wind and water over millions of years. The most famous section, the Valley of Castles, showcases towering rock pillars that resemble ancient fortresses. The canyon\'s walls display stunning layers of red, orange, and yellow sedimentary rock.',
                'latitude' => 43.3539,
                'longitude' => 79.0958,
                'is_featured' => true,
                'is_active' => true,
                'display_order' => 21,
                'tagline' => 'Kazakhstan\'s Grand Canyon',
            ],
            [
                'name' => 'Altyn Emel',
                'slug' => 'altyn-emel',
                'description' => 'National park with singing dunes and unique landscapes',
                'short_description' => 'Vast national park featuring the famous Singing Dune',
                'long_description' => 'Altyn Emel National Park is a vast protected area in southeastern Kazakhstan, covering 4,600 square kilometers. The park is famous for its Singing Dune (Singing Barkhan), a 150-meter-high sand dune that produces a distinctive humming sound in dry weather. The park also features the Aktau and Katutau mountains with their otherworldly colored landscapes, ancient petroglyphs, and diverse wildlife including the endangered Przhevalsky\'s horse.',
                'latitude' => 43.7167,
                'longitude' => 78.6000,
                'is_featured' => true,
                'is_active' => true,
                'display_order' => 22,
                'tagline' => 'Land of Singing Sands',
            ],
            [
                'name' => 'Skazka Canyon',
                'slug' => 'skazka',
                'description' => 'Fairy tale canyon with colorful clay formations',
                'short_description' => 'Colorful canyon resembling a fairy tale landscape',
                'long_description' => 'Skazka Canyon (meaning Fairy Tale in Russian) is a stunning geological formation located on the southern shore of Lake Issyk-Kul in Kyrgyzstan. The canyon features surreal rock formations sculpted by erosion, with vibrant red, orange, and yellow hues. The rocks form shapes that resemble castles, animals, and mythical creatures, creating a landscape straight out of a fairy tale. It\'s a popular stop between Bokonbaevo and Karakol.',
                'latitude' => 42.1758,
                'longitude' => 77.2506,
                'is_featured' => true,
                'is_active' => true,
                'display_order' => 23,
                'tagline' => 'Fairy Tale Rock Garden',
            ],
            [
                'name' => 'Tokmok',
                'slug' => 'tokmok',
                'description' => 'Historic town near Burana Tower',
                'short_description' => 'Ancient Silk Road town with Burana Tower ruins',
                'long_description' => 'Tokmok is a town in northern Kyrgyzstan, located in the Chuy Valley. It is situated near the ruins of the ancient Sogdian city of Balasagun, one of the major cities along the Silk Road. The town is famous for the nearby Burana Tower, an 11th-century minaret that stands as one of the oldest architectural monuments in Central Asia. The area also features stone balbals (ancient Turkic grave markers) and a small museum.',
                'latitude' => 42.8333,
                'longitude' => 75.3000,
                'is_featured' => false,
                'is_active' => true,
                'display_order' => 24,
                'tagline' => 'Silk Road Heritage',
            ],
            [
                'name' => 'Ala-Archa',
                'slug' => 'ala-archa',
                'description' => 'Alpine national park near Bishkek',
                'short_description' => 'Scenic gorge and national park for hiking and mountaineering',
                'long_description' => 'Ala-Archa National Park is a stunning alpine wilderness located just 40 kilometers south of Bishkek. The park encompasses the Ala-Archa River gorge and surrounding mountains, with peaks reaching over 4,800 meters. It offers excellent opportunities for hiking, rock climbing, and mountaineering. The park features glaciers, waterfalls, and diverse flora and fauna. Popular trails range from easy day hikes to challenging multi-day treks.',
                'latitude' => 42.5667,
                'longitude' => 74.5000,
                'is_featured' => true,
                'is_active' => true,
                'display_order' => 25,
                'tagline' => 'Alpine Paradise Near the Capital',
            ],
        ];

        foreach ($cities as $city) {
            City::updateOrCreate(
                ['slug' => $city['slug']],
                $city
            );
        }

        $this->command->info('Kazakhstan and additional Kyrgyzstan cities seeded successfully!');
    }
}
