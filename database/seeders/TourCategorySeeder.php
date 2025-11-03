<?php

namespace Database\Seeders;

use App\Models\TourCategory;
use Illuminate\Database\Seeder;

class TourCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => [
                    'en' => 'Cultural & Historical',
                    'ru' => 'Культурно-исторические',
                    'fr' => 'Culturel et Historique',
                ],
                'slug' => 'cultural-historical',
                'description' => [
                    'en' => 'Explore ancient cities, monuments, and rich cultural heritage of Uzbekistan',
                    'ru' => 'Исследуйте древние города, памятники и богатое культурное наследие Узбекистана',
                    'fr' => 'Explorez les villes anciennes, les monuments et le riche patrimoine culturel de l\'Ouzbékistan',
                ],
                'icon' => 'fas fa-landmark',
                'display_order' => 1,
                'is_active' => true,
                'show_on_homepage' => true,
            ],
            [
                'name' => [
                    'en' => 'Mountain & Adventure',
                    'ru' => 'Горы и приключения',
                    'fr' => 'Montagne et Aventure',
                ],
                'slug' => 'mountain-adventure',
                'description' => [
                    'en' => 'Trek through stunning mountain ranges and experience thrilling outdoor activities',
                    'ru' => 'Путешествуйте по потрясающим горным хребтам и испытайте захватывающие приключения на свежем воздухе',
                    'fr' => 'Parcourez de magnifiques chaînes de montagnes et vivez des activités de plein air palpitantes',
                ],
                'icon' => 'fas fa-mountain',
                'display_order' => 2,
                'is_active' => true,
                'show_on_homepage' => true,
            ],
            [
                'name' => [
                    'en' => 'Family & Educational',
                    'ru' => 'Семейные и образовательные',
                    'fr' => 'Famille et Éducatif',
                ],
                'slug' => 'family-educational',
                'description' => [
                    'en' => 'Perfect tours for families with children, combining fun and learning experiences',
                    'ru' => 'Идеальные туры для семей с детьми, сочетающие веселье и образовательный опыт',
                    'fr' => 'Tours parfaits pour les familles avec enfants, combinant plaisir et apprentissage',
                ],
                'icon' => 'fas fa-users',
                'display_order' => 3,
                'is_active' => true,
                'show_on_homepage' => true,
            ],
            [
                'name' => [
                    'en' => 'Desert & Nomadic',
                    'ru' => 'Пустыня и кочевая жизнь',
                    'fr' => 'Désert et Nomade',
                ],
                'slug' => 'desert-nomadic',
                'description' => [
                    'en' => 'Experience authentic nomadic lifestyle and explore vast desert landscapes',
                    'ru' => 'Испытайте подлинный кочевой образ жизни и исследуйте обширные пустынные ландшафты',
                    'fr' => 'Découvrez le mode de vie nomade authentique et explorez de vastes paysages désertiques',
                ],
                'icon' => 'fas fa-campground',
                'display_order' => 4,
                'is_active' => true,
                'show_on_homepage' => true,
            ],
            [
                'name' => [
                    'en' => 'City Walks',
                    'ru' => 'Городские прогулки',
                    'fr' => 'Promenades en Ville',
                ],
                'slug' => 'city-walks',
                'description' => [
                    'en' => 'Discover the modern and historical sides of Uzbekistan\'s vibrant cities',
                    'ru' => 'Откройте для себя современную и историческую стороны оживленных городов Узбекистана',
                    'fr' => 'Découvrez les côtés modernes et historiques des villes animées d\'Ouzbékistan',
                ],
                'icon' => 'fas fa-walking',
                'display_order' => 5,
                'is_active' => true,
                'show_on_homepage' => true,
            ],
            [
                'name' => [
                    'en' => 'Food & Craft',
                    'ru' => 'Еда и ремесла',
                    'fr' => 'Cuisine et Artisanat',
                ],
                'slug' => 'food-craft',
                'description' => [
                    'en' => 'Taste traditional cuisine and learn authentic crafts from local artisans',
                    'ru' => 'Попробуйте традиционную кухню и научитесь подлинным ремеслам у местных мастеров',
                    'fr' => 'Goûtez la cuisine traditionnelle et apprenez l\'artisanat authentique des artisans locaux',
                ],
                'icon' => 'fas fa-utensils',
                'display_order' => 6,
                'is_active' => true,
                'show_on_homepage' => true,
            ],
        ];

        foreach ($categories as $category) {
            TourCategory::create($category);
        }
    }
}
