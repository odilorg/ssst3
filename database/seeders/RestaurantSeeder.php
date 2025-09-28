<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Company;
use App\Models\MealType;
use App\Models\Restaurant;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RestaurantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $restaurants = [
            [
                'name' => 'Ресторан "Регистан"',
                'address' => 'ул. Регистан, 15',
                'phone' => '+998 66 123-45-67',
                'website' => 'https://registan-restaurant.uz',
                'email' => 'info@registan-restaurant.uz',
                'meal_types' => [
                    ['name' => 'breakfast', 'description' => 'Традиционный узбекский завтрак', 'price' => 12.00],
                    ['name' => 'lunch', 'description' => 'Обед с пловом и салатами', 'price' => 25.00],
                    ['name' => 'dinner', 'description' => 'Ужин с шашлыком и вином', 'price' => 35.00],
                ],
            ],
            [
                'name' => 'Кафе "Бухара"',
                'address' => 'пр. Навои, 25',
                'phone' => '+998 65 234-56-78',
                'website' => 'https://bukhara-cafe.uz',
                'email' => 'contact@bukhara-cafe.uz',
                'meal_types' => [
                    ['name' => 'breakfast', 'description' => 'Легкий завтрак с чаем', 'price' => 8.00],
                    ['name' => 'lunch', 'description' => 'Бизнес-ланч', 'price' => 18.00],
                    ['name' => 'coffee_break', 'description' => 'Кофе и десерты', 'price' => 6.00],
                ],
            ],
            [
                'name' => 'Ресторан "Хива"',
                'address' => 'ул. Пахлаван Махмуд, 8',
                'phone' => '+998 62 345-67-89',
                'website' => 'https://khiva-restaurant.uz',
                'email' => 'reservations@khiva-restaurant.uz',
                'meal_types' => [
                    ['name' => 'lunch', 'description' => 'Традиционные блюда Хивы', 'price' => 22.00],
                    ['name' => 'dinner', 'description' => 'Романтический ужин', 'price' => 40.00],
                ],
            ],
            [
                'name' => 'Кафе "Ташкент"',
                'address' => 'ул. Навои, 45',
                'phone' => '+998 71 456-78-90',
                'website' => 'https://tashkent-cafe.uz',
                'email' => 'info@tashkent-cafe.uz',
                'meal_types' => [
                    ['name' => 'breakfast', 'description' => 'Континентальный завтрак', 'price' => 10.00],
                    ['name' => 'lunch', 'description' => 'Современная кухня', 'price' => 20.00],
                    ['name' => 'coffee_break', 'description' => 'Эспрессо и выпечка', 'price' => 5.00],
                ],
            ],
            [
                'name' => 'Ресторан "Фергана"',
                'address' => 'ул. Мустакиллик, 33',
                'phone' => '+998 73 567-89-01',
                'website' => 'https://fergana-restaurant.uz',
                'email' => 'hello@fergana-restaurant.uz',
                'meal_types' => [
                    ['name' => 'breakfast', 'description' => 'Домашний завтрак', 'price' => 9.00],
                    ['name' => 'lunch', 'description' => 'Ферганская кухня', 'price' => 24.00],
                    ['name' => 'dinner', 'description' => 'Семейный ужин', 'price' => 30.00],
                ],
            ],
            [
                'name' => 'Кафе "Наманган"',
                'address' => 'ул. Чорсу, 12',
                'phone' => '+998 69 678-90-12',
                'website' => 'https://namangan-cafe.uz',
                'email' => 'cafe@namangan-cafe.uz',
                'meal_types' => [
                    ['name' => 'breakfast', 'description' => 'Быстрый завтрак', 'price' => 7.00],
                    ['name' => 'lunch', 'description' => 'Наманганские блюда', 'price' => 16.00],
                    ['name' => 'coffee_break', 'description' => 'Чай и сладости', 'price' => 4.00],
                ],
            ],
            [
                'name' => 'Ресторан "Андижан"',
                'address' => 'ул. Бабура, 67',
                'phone' => '+998 74 789-01-23',
                'website' => 'https://andijan-restaurant.uz',
                'email' => 'restaurant@andijan-restaurant.uz',
                'meal_types' => [
                    ['name' => 'lunch', 'description' => 'Андижанская кухня', 'price' => 21.00],
                    ['name' => 'dinner', 'description' => 'Торжественный ужин', 'price' => 38.00],
                ],
            ],
            [
                'name' => 'Кафе "Нукус"',
                'address' => 'ул. Каракалпакстан, 89',
                'phone' => '+998 61 890-12-34',
                'website' => 'https://nukus-cafe.uz',
                'email' => 'info@nukus-cafe.uz',
                'meal_types' => [
                    ['name' => 'breakfast', 'description' => 'Простой завтрак', 'price' => 6.00],
                    ['name' => 'lunch', 'description' => 'Каракалпакская кухня', 'price' => 15.00],
                    ['name' => 'coffee_break', 'description' => 'Кофе и печенье', 'price' => 3.00],
                ],
            ],
            [
                'name' => 'Ресторан "Термез"',
                'address' => 'ул. Афганистан, 11',
                'phone' => '+998 76 901-23-45',
                'website' => 'https://termez-restaurant.uz',
                'email' => 'contact@termez-restaurant.uz',
                'meal_types' => [
                    ['name' => 'breakfast', 'description' => 'Южный завтрак', 'price' => 8.00],
                    ['name' => 'lunch', 'description' => 'Термезская кухня', 'price' => 19.00],
                    ['name' => 'dinner', 'description' => 'Вечерний ужин', 'price' => 28.00],
                ],
            ],
            [
                'name' => 'Кафе "Гулистан"',
                'address' => 'ул. Сырдарья, 55',
                'phone' => '+998 67 012-34-56',
                'website' => 'https://gulistan-cafe.uz',
                'email' => 'cafe@gulistan-cafe.uz',
                'meal_types' => [
                    ['name' => 'breakfast', 'description' => 'Сырдарьинский завтрак', 'price' => 7.50],
                    ['name' => 'lunch', 'description' => 'Региональная кухня', 'price' => 17.00],
                    ['name' => 'coffee_break', 'description' => 'Чай и орехи', 'price' => 4.50],
                ],
            ],
        ];

        // Get first city and company for relationships
        $firstCity = City::first();
        $firstCompany = Company::first();

        if (!$firstCity || !$firstCompany) {
            $this->command->error('Please run CitySeeder and CompanySeeder first!');
            return;
        }

        foreach ($restaurants as $restaurantData) {
            $mealTypes = $restaurantData['meal_types'];
            unset($restaurantData['meal_types']);
            
            $restaurant = Restaurant::create([
                'name' => $restaurantData['name'],
                'address' => $restaurantData['address'],
                'phone' => $restaurantData['phone'],
                'website' => $restaurantData['website'],
                'email' => $restaurantData['email'],
                'city_id' => $firstCity->id,
                'company_id' => $firstCompany->id,
                'menu_images' => [],
            ]);
            
            // Create meal types for this restaurant
            foreach ($mealTypes as $mealTypeData) {
                MealType::create([
                    'name' => $mealTypeData['name'],
                    'description' => $mealTypeData['description'],
                    'price' => $mealTypeData['price'],
                    'restaurant_id' => $restaurant->id,
                ]);
            }
        }
    }
}
