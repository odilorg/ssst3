<?php

namespace Database\Seeders;

use App\Models\City;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class KyrgyzstanCitiesSeeder extends Seeder
{
    /**
     * Run the database seeder for Kyrgyzstan cities.
     */
    public function run(): void
    {
        $cities = [
            [
                'name' => 'Bishkek',
                'slug' => 'bishkek',
                'description' => 'Capital and largest city of Kyrgyzstan, gateway to the Tian Shan mountains',
                'short_description' => 'Capital city of Kyrgyzstan with Soviet architecture and mountain views',
                'long_description' => 'Bishkek is the capital and largest city of Kyrgyzstan. Located in the Chuy Valley at the northern edge of the Kyrgyz Ala-Too range, it serves as the country\'s political, economic, and cultural center. The city features wide boulevards lined with Soviet-era buildings, parks, and monuments.',
                'latitude' => 42.8746,
                'longitude' => 74.5698,
                'is_featured' => true,
                'is_active' => true,
                'display_order' => 1,
                'tagline' => 'Gateway to the Mountains',
            ],
            [
                'name' => 'Karakol',
                'slug' => 'karakol',
                'description' => 'Historic town near Lake Issyk-Kul, base for mountain trekking',
                'short_description' => 'Charming town near Issyk-Kul Lake with Russian and Dungan heritage',
                'long_description' => 'Karakol is the fourth largest city in Kyrgyzstan, located near the eastern tip of Lake Issyk-Kul. Founded as a Russian military fortification in 1869, it has evolved into a popular base for trekking, skiing, and exploring the surrounding Terskey Alatau mountains. The city features unique Russian Orthodox churches and Dungan mosques.',
                'latitude' => 42.4906,
                'longitude' => 78.3936,
                'is_featured' => true,
                'is_active' => true,
                'display_order' => 2,
                'tagline' => 'Adventure Capital of the East',
            ],
            [
                'name' => 'Kochkor',
                'slug' => 'kochkor',
                'description' => 'Traditional village, gateway to Son-Kul Lake',
                'short_description' => 'Traditional Kyrgyz village known for handicrafts and felt-making',
                'long_description' => 'Kochkor is a small town in northern Kyrgyzstan, situated between Bishkek and Lake Issyk-Kul. It serves as an important stop for travelers heading to Song-Kul Lake. The town is famous for its felt-making cooperatives where local women produce traditional Kyrgyz felt carpets and crafts. It offers an authentic glimpse into rural Kyrgyz life.',
                'latitude' => 42.1950,
                'longitude' => 75.7886,
                'is_featured' => false,
                'is_active' => true,
                'display_order' => 3,
                'tagline' => 'Gateway to Nomadic Traditions',
            ],
            [
                'name' => 'Song-Kul',
                'slug' => 'song-kul',
                'description' => 'High-altitude alpine lake surrounded by summer pastures',
                'short_description' => 'Pristine high-altitude lake with traditional nomadic yurt camps',
                'long_description' => 'Song-Kul (Son-Kul) is a stunning alpine lake located at 3,016 meters above sea level in the Naryn Province. During summer months, the lake is surrounded by jailoo (summer pastures) where Kyrgyz herders graze their livestock and live in traditional yurts. The lake offers breathtaking views, horseback riding, and an authentic nomadic experience.',
                'latitude' => 41.8356,
                'longitude' => 75.1350,
                'is_featured' => true,
                'is_active' => true,
                'display_order' => 4,
                'tagline' => 'Highland Paradise',
            ],
            [
                'name' => 'Bokonbaevo',
                'slug' => 'bokonbaevo',
                'description' => 'Village on the southern shore of Issyk-Kul Lake',
                'short_description' => 'Southern Issyk-Kul village known for eagle hunting traditions',
                'long_description' => 'Bokonbaevo is a village located on the southern shore of Lake Issyk-Kul. It is known for preserving the ancient tradition of eagle hunting (berkutchi). Visitors can witness demonstrations of this centuries-old practice and interact with local eagle hunters. The village also offers beautiful views of the lake and surrounding mountains.',
                'latitude' => 42.1265,
                'longitude' => 77.1333,
                'is_featured' => false,
                'is_active' => true,
                'display_order' => 5,
                'tagline' => 'Home of Eagle Hunters',
            ],
            [
                'name' => 'Jeti-Oguz',
                'slug' => 'jeti-oguz',
                'description' => 'Scenic gorge with famous red rock formations',
                'short_description' => 'Picturesque gorge with Seven Bulls red rock formations',
                'long_description' => 'Jeti-Oguz (meaning Seven Bulls) is a stunning gorge located south of Karakol, famous for its distinctive red sandstone rock formations. The most famous formation resembles seven bulls lined up, giving the gorge its name. Another notable formation is called Broken Heart. The area offers excellent hiking, hot springs, and spectacular mountain scenery.',
                'latitude' => 42.3575,
                'longitude' => 78.2358,
                'is_featured' => true,
                'is_active' => true,
                'display_order' => 6,
                'tagline' => 'Red Rock Wonder',
            ],
            [
                'name' => 'Altyn-Arashan',
                'slug' => 'altyn-arashan',
                'description' => 'Mountain valley with natural hot springs',
                'short_description' => 'Remote valley featuring natural hot springs and alpine beauty',
                'long_description' => 'Altyn-Arashan (meaning Golden Spa) is a picturesque valley located in the Terskey Alatau mountain range, accessible only by hiking or horseback from Karakol. The valley is famous for its natural hot springs with therapeutic properties. Surrounded by snow-capped peaks, alpine meadows, and forests, it offers excellent trekking opportunities.',
                'latitude' => 42.5183,
                'longitude' => 78.5667,
                'is_featured' => true,
                'is_active' => true,
                'display_order' => 7,
                'tagline' => 'Natural Hot Springs Paradise',
            ],
            [
                'name' => 'Cholpon-Ata',
                'slug' => 'cholpon-ata',
                'description' => 'Resort town with ancient petroglyphs and beaches',
                'short_description' => 'Popular Issyk-Kul resort town with ancient stone carvings',
                'long_description' => 'Cholpon-Ata is a resort town on the northern shore of Lake Issyk-Kul, known for its sandy beaches and the famous open-air petroglyph museum. The petroglyphs, dating back over 2,000 years, depict ancient hunting scenes, animals, and solar symbols. The town is a popular summer destination for both locals and tourists.',
                'latitude' => 42.6488,
                'longitude' => 77.0822,
                'is_featured' => true,
                'is_active' => true,
                'display_order' => 8,
                'tagline' => 'Ancient Stones by the Lake',
            ],
        ];

        foreach ($cities as $city) {
            City::updateOrCreate(
                ['slug' => $city['slug']],
                $city
            );
        }

        $this->command->info('Kyrgyzstan cities seeded successfully!');
    }
}
