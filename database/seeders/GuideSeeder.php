<?php

namespace Database\Seeders;

use App\Models\Guide;
use App\Models\SpokenLanguage;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GuideSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $guides = [
            [
                'name' => 'Алишер Каримов',
                'daily_rate' => 150.00,
                'language' => 'Uzbek',
                'is_marketing' => true,
                'phone' => '+998 90 123-45-67',
                'email' => 'alisher@guide.uz',
                'address' => 'ул. Навои, 15',
                'city' => 'Ташкент',
                'price_types' => [
                    ['price_type_name' => 'pickup_dropoff', 'price' => 50],
                    ['price_type_name' => 'halfday', 'price' => 80],
                    ['price_type_name' => 'per_daily', 'price' => 150],
                ],
                'languages' => ['Uzbek', 'Russian', 'English'],
            ],
            [
                'name' => 'Мадина Турсунова',
                'daily_rate' => 120.00,
                'language' => 'Russian',
                'is_marketing' => false,
                'phone' => '+998 91 234-56-78',
                'email' => 'madina@guide.uz',
                'address' => 'пр. Регистан, 25',
                'city' => 'Самарканд',
                'price_types' => [
                    ['price_type_name' => 'pickup_dropoff', 'price' => 40],
                    ['price_type_name' => 'halfday', 'price' => 70],
                    ['price_type_name' => 'per_daily', 'price' => 120],
                ],
                'languages' => ['Russian', 'English', 'French'],
            ],
            [
                'name' => 'Шухрат Нуриддинов',
                'daily_rate' => 180.00,
                'language' => 'English',
                'is_marketing' => true,
                'phone' => '+998 92 345-67-89',
                'email' => 'shukhrat@guide.uz',
                'address' => 'ул. Бахауддина Накшбанди, 8',
                'city' => 'Бухара',
                'price_types' => [
                    ['price_type_name' => 'pickup_dropoff', 'price' => 60],
                    ['price_type_name' => 'halfday', 'price' => 100],
                    ['price_type_name' => 'per_daily', 'price' => 180],
                ],
                'languages' => ['English', 'German', 'Spanish'],
            ],
            [
                'name' => 'Гулнора Ахмедова',
                'daily_rate' => 100.00,
                'language' => 'Uzbek',
                'is_marketing' => false,
                'phone' => '+998 93 456-78-90',
                'email' => 'gulnora@guide.uz',
                'address' => 'ул. Пахлаван Махмуд, 12',
                'city' => 'Хива',
                'price_types' => [
                    ['price_type_name' => 'pickup_dropoff', 'price' => 35],
                    ['price_type_name' => 'halfday', 'price' => 60],
                    ['price_type_name' => 'per_daily', 'price' => 100],
                ],
                'languages' => ['Uzbek', 'Russian'],
            ],
            [
                'name' => 'Рустам Юлдашев',
                'daily_rate' => 200.00,
                'language' => 'English',
                'is_marketing' => true,
                'phone' => '+998 94 567-89-01',
                'email' => 'rustam@guide.uz',
                'address' => 'ул. Мустакиллик, 45',
                'city' => 'Фергана',
                'price_types' => [
                    ['price_type_name' => 'pickup_dropoff', 'price' => 70],
                    ['price_type_name' => 'halfday', 'price' => 120],
                    ['price_type_name' => 'per_daily', 'price' => 200],
                ],
                'languages' => ['English', 'Chinese', 'Japanese'],
            ],
            [
                'name' => 'Дилафруз Махмудова',
                'daily_rate' => 130.00,
                'language' => 'Russian',
                'is_marketing' => false,
                'phone' => '+998 95 678-90-12',
                'email' => 'dilafruz@guide.uz',
                'address' => 'ул. Чорсу, 33',
                'city' => 'Наманган',
                'price_types' => [
                    ['price_type_name' => 'pickup_dropoff', 'price' => 45],
                    ['price_type_name' => 'halfday', 'price' => 80],
                    ['price_type_name' => 'per_daily', 'price' => 130],
                ],
                'languages' => ['Russian', 'Uzbek', 'Turkish'],
            ],
            [
                'name' => 'Илхом Хакимов',
                'daily_rate' => 160.00,
                'language' => 'English',
                'is_marketing' => true,
                'phone' => '+998 96 789-01-23',
                'email' => 'ilhom@guide.uz',
                'address' => 'ул. Бабура, 67',
                'city' => 'Андижан',
                'price_types' => [
                    ['price_type_name' => 'pickup_dropoff', 'price' => 55],
                    ['price_type_name' => 'halfday', 'price' => 90],
                    ['price_type_name' => 'per_daily', 'price' => 160],
                ],
                'languages' => ['English', 'Arabic', 'Persian'],
            ],
            [
                'name' => 'Айгуль Атамуратова',
                'daily_rate' => 110.00,
                'language' => 'Uzbek',
                'is_marketing' => false,
                'phone' => '+998 97 890-12-34',
                'email' => 'aygul@guide.uz',
                'address' => 'ул. Каракалпакстан, 89',
                'city' => 'Нукус',
                'price_types' => [
                    ['price_type_name' => 'pickup_dropoff', 'price' => 40],
                    ['price_type_name' => 'halfday', 'price' => 70],
                    ['price_type_name' => 'per_daily', 'price' => 110],
                ],
                'languages' => ['Uzbek', 'Russian', 'Kazakh'],
            ],
            [
                'name' => 'Бахтиёр Рахимов',
                'daily_rate' => 140.00,
                'language' => 'English',
                'is_marketing' => false,
                'phone' => '+998 98 901-23-45',
                'email' => 'bakhtiyor@guide.uz',
                'address' => 'ул. Афганистан, 11',
                'city' => 'Термез',
                'price_types' => [
                    ['price_type_name' => 'pickup_dropoff', 'price' => 50],
                    ['price_type_name' => 'halfday', 'price' => 85],
                    ['price_type_name' => 'per_daily', 'price' => 140],
                ],
                'languages' => ['English', 'Dari', 'Pashto'],
            ],
            [
                'name' => 'Зульфия Каримова',
                'daily_rate' => 125.00,
                'language' => 'Russian',
                'is_marketing' => true,
                'phone' => '+998 99 012-34-56',
                'email' => 'zulfiya@guide.uz',
                'address' => 'ул. Сырдарья, 55',
                'city' => 'Гулистан',
                'price_types' => [
                    ['price_type_name' => 'pickup_dropoff', 'price' => 42],
                    ['price_type_name' => 'halfday', 'price' => 75],
                    ['price_type_name' => 'per_daily', 'price' => 125],
                ],
                'languages' => ['Russian', 'Uzbek', 'Kyrgyz'],
            ],
        ];

        foreach ($guides as $guideData) {
            $languages = $guideData['languages'];
            unset($guideData['languages']);
            
            $guide = Guide::create($guideData);
            
            // Attach languages
            $languageIds = SpokenLanguage::whereIn('name', $languages)->pluck('id');
            $guide->spokenLanguages()->attach($languageIds);
        }
    }
}
