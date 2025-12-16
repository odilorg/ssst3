<?php

namespace Database\Seeders;

use App\Models\City;
use Illuminate\Database\Seeder;

class TajikistanCitiesSeeder extends Seeder
{
    /**
     * Seed Tajikistan and Pamir Highway cities.
     */
    public function run(): void
    {
        $cities = [
            [
                'name' => 'Dushanbe',
                'slug' => 'dushanbe',
                'description' => 'Capital and largest city of Tajikistan',
                'short_description' => 'Modern capital city at the foot of the Pamir Mountains',
                'long_description' => 'Dushanbe is the capital and largest city of Tajikistan, located in the Hissar Valley at the confluence of two rivers. The city name means "Monday" in Persian, as it grew from a Monday marketplace village. Today, Dushanbe is a modern city featuring wide avenues, parks, Soviet-era monuments, and the impressive Flagpole Park with one of the world\'s tallest flagpoles. It serves as the gateway to the legendary Pamir Highway.',
                'latitude' => 38.5598,
                'longitude' => 68.7738,
                'is_featured' => true,
                'is_active' => true,
                'display_order' => 30,
                'tagline' => 'Gateway to the Pamirs',
            ],
            [
                'name' => 'Iskanderkul',
                'slug' => 'iskanderkul',
                'description' => 'Stunning mountain lake named after Alexander the Great',
                'short_description' => 'Turquoise alpine lake in the Fann Mountains',
                'long_description' => 'Iskanderkul (Alexander\'s Lake) is a breathtaking mountain lake in the Fann Mountains of Tajikistan, located at 2,195 meters above sea level. Legend says Alexander the Great stopped here during his campaigns. The lake is famous for its turquoise waters, dramatic mountain backdrop, and the nearby 38-meter Fann Niagara waterfall. It\'s a popular destination for hiking, camping, and experiencing pristine mountain nature.',
                'latitude' => 39.0833,
                'longitude' => 68.3667,
                'is_featured' => true,
                'is_active' => true,
                'display_order' => 31,
                'tagline' => 'Alexander\'s Mountain Jewel',
            ],
            [
                'name' => 'Kalaikhum',
                'slug' => 'kalaikhum',
                'description' => 'Remote town on the Pamir Highway along the Afghan border',
                'short_description' => 'Scenic riverside town on the legendary Pamir Highway',
                'long_description' => 'Kalaikhum is a small town in southeastern Tajikistan, located along the Panj River which forms the border with Afghanistan. It\'s a key stop on the famous Pamir Highway. The town offers stunning views of the river valley and the Hindu Kush mountains across the border. Visitors can experience traditional Pamiri hospitality and witness the daily life of communities living in one of the world\'s most remote regions.',
                'latitude' => 37.8333,
                'longitude' => 70.8167,
                'is_featured' => false,
                'is_active' => true,
                'display_order' => 32,
                'tagline' => 'Gateway to the Wakhan',
            ],
            [
                'name' => 'Wakhan Valley',
                'slug' => 'wakhan',
                'description' => 'Remote valley in the Pamir Mountains',
                'short_description' => 'Spectacular mountain valley along the Afghan border',
                'long_description' => 'The Wakhan Valley is a narrow corridor of land in the Pamir Mountains, running along the Panj River which separates Tajikistan from Afghanistan. This remote and spectacular valley is part of the ancient Silk Road and offers unparalleled mountain scenery with peaks over 6,000 meters. The valley is home to Pamiri people who maintain their unique cultural traditions, ancient fortresses, and hot springs.',
                'latitude' => 37.3000,
                'longitude' => 71.7000,
                'is_featured' => true,
                'is_active' => true,
                'display_order' => 33,
                'tagline' => 'Ancient Silk Road Corridor',
            ],
            [
                'name' => 'Khorog',
                'slug' => 'khorog',
                'description' => 'Capital of Gorno-Badakhshan Autonomous Region',
                'short_description' => 'High-altitude capital on the Pamir Highway',
                'long_description' => 'Khorog is the capital of Gorno-Badakhshan Autonomous Region in Tajikistan, located at 2,200 meters above sea level along the Gunt River. It\'s the largest settlement on the Pamir Highway and serves as a crucial hub for travelers. The city features the second-highest botanical garden in the world, colorful bazaars, and stunning mountain views. It\'s the heart of Pamiri culture and hospitality.',
                'latitude' => 37.4833,
                'longitude' => 71.5500,
                'is_featured' => true,
                'is_active' => true,
                'display_order' => 34,
                'tagline' => 'Heart of the Pamirs',
            ],
            [
                'name' => 'Murghab',
                'slug' => 'murghab',
                'description' => 'Highest town on the Pamir Highway',
                'short_description' => 'Remote high-altitude settlement at 3,650 meters',
                'long_description' => 'Murghab is one of the highest settlements in the former Soviet Union, located at 3,650 meters above sea level on the Pamir Plateau. This remote town experiences extreme temperatures and offers a unique glimpse into life in one of the world\'s harshest environments. Surrounded by vast high-altitude desert and snow-capped peaks, Murghab is home to Kyrgyz herders and serves as a base for exploring the eastern Pamirs.',
                'latitude' => 38.1667,
                'longitude' => 73.9500,
                'is_featured' => true,
                'is_active' => true,
                'display_order' => 35,
                'tagline' => 'Roof of the Pamirs',
            ],
            [
                'name' => 'Karakul',
                'slug' => 'karakul',
                'description' => 'High-altitude lake in the Eastern Pamirs',
                'short_description' => 'Stunning lake at 3,900 meters with stark beauty',
                'long_description' => 'Karakul (meaning Black Lake) is a spectacular high-altitude lake in the Pamir Mountains of Tajikistan, located at 3,900 meters above sea level. The lake was formed by a meteorite impact about 25 million years ago and is one of the highest lakes in the world. Its deep blue-black waters contrast dramatically with the surrounding snowy peaks and barren landscape, creating an otherworldly scene along the Pamir Highway.',
                'latitude' => 39.0167,
                'longitude' => 73.4667,
                'is_featured' => true,
                'is_active' => true,
                'display_order' => 36,
                'tagline' => 'Black Lake of the Pamirs',
            ],
            [
                'name' => 'Sary-Tash',
                'slug' => 'sary-tash',
                'description' => 'High-altitude junction town in southern Kyrgyzstan',
                'short_description' => 'Strategic crossroads on the Pamir Highway',
                'long_description' => 'Sary-Tash is a small settlement in southern Kyrgyzstan at 3,170 meters above sea level, serving as a crucial junction where the Pamir Highway meets the road to Osh. This remote town sits in a vast high-altitude valley surrounded by dramatic peaks. Despite its small size and harsh climate, it\'s an important stop for travelers crossing between Tajikistan, Kyrgyzstan, and the route to the Chinese border at Irkeshtam Pass.',
                'latitude' => 39.5167,
                'longitude' => 73.2833,
                'is_featured' => false,
                'is_active' => true,
                'display_order' => 37,
                'tagline' => 'Crossroads of the Mountains',
            ],
            [
                'name' => 'Khujand',
                'slug' => 'khujand',
                'description' => 'Second largest city in Tajikistan, ancient Silk Road hub',
                'short_description' => 'Historic city founded by Alexander the Great',
                'long_description' => 'Khujand is the second-largest city in Tajikistan and one of the oldest cities in Central Asia, founded over 2,500 years ago by Alexander the Great as Alexandria Eschate. Located in the fertile Fergana Valley, it was an important trading center on the Silk Road. Today, Khujand features the impressive Panjshanbe Bazaar, the historical museum dedicated to archaeologist and historian Penjikent, ancient fortresses, and the stunning Arbob Palace.',
                'latitude' => 40.2833,
                'longitude' => 69.6167,
                'is_featured' => true,
                'is_active' => true,
                'display_order' => 38,
                'tagline' => 'Ancient Gateway to Fergana',
            ],
        ];

        foreach ($cities as $city) {
            City::updateOrCreate(
                ['slug' => $city['slug']],
                $city
            );
        }

        $this->command->info('Tajikistan and Pamir Highway cities seeded successfully!');
    }
}
