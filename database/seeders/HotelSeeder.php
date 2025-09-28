<?php

namespace Database\Seeders;

use App\Models\Amenity;
use App\Models\City;
use App\Models\Company;
use App\Models\Hotel;
use App\Models\Room;
use App\Models\RoomType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class HotelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create room types
        $roomTypes = [
            'Standard',
            'Deluxe',
            'Suite',
            'Family',
            'Executive',
            'Presidential',
        ];

        foreach ($roomTypes as $type) {
            RoomType::create(['type' => $type]);
        }

        // Create amenities
        $amenities = [
            'Wi-Fi',
            'Air Conditioning',
            'TV',
            'Mini Bar',
            'Safe',
            'Balcony',
            'Sea View',
            'Mountain View',
            'City View',
            'Pool Access',
            'Spa Access',
            'Gym Access',
            'Room Service',
            'Breakfast Included',
            'Parking',
            'Pet Friendly',
        ];

        foreach ($amenities as $amenity) {
            Amenity::create(['name' => $amenity]);
        }

        $hotels = [
            [
                'name' => 'Гостиница "Регистан"',
                'address' => 'ул. Регистан, 15',
                'type' => '4_star',
                'description' => 'Роскошная гостиница в центре Самарканда с видом на знаменитый Регистан.',
                'phone' => '+998 66 123-45-67',
                'email' => 'info@registan-hotel.uz',
                'rooms' => [
                    [
                        'name' => 'Standard Room',
                        'description' => 'Уютный стандартный номер с современными удобствами',
                        'room_type' => 'Standard',
                        'cost_per_night' => 80.00,
                        'room_size' => 25.0,
                        'amenities' => ['Wi-Fi', 'Air Conditioning', 'TV', 'Safe'],
                    ],
                    [
                        'name' => 'Deluxe Room',
                        'description' => 'Просторный номер с видом на Регистан',
                        'room_type' => 'Deluxe',
                        'cost_per_night' => 120.00,
                        'room_size' => 35.0,
                        'amenities' => ['Wi-Fi', 'Air Conditioning', 'TV', 'Safe', 'Balcony', 'City View'],
                    ],
                ],
            ],
            [
                'name' => 'Отель "Бухара Плаза"',
                'address' => 'пр. Навои, 25',
                'type' => '5_star',
                'description' => 'Пятизвездочный отель в историческом центре Бухары.',
                'phone' => '+998 65 234-56-78',
                'email' => 'reservations@bukhara-plaza.uz',
                'rooms' => [
                    [
                        'name' => 'Executive Suite',
                        'description' => 'Эксклюзивный номер с отдельной гостиной',
                        'room_type' => 'Executive',
                        'cost_per_night' => 200.00,
                        'room_size' => 60.0,
                        'amenities' => ['Wi-Fi', 'Air Conditioning', 'TV', 'Mini Bar', 'Safe', 'Balcony', 'City View', 'Room Service'],
                    ],
                    [
                        'name' => 'Presidential Suite',
                        'description' => 'Президентский номер с панорамным видом',
                        'room_type' => 'Presidential',
                        'cost_per_night' => 350.00,
                        'room_size' => 100.0,
                        'amenities' => ['Wi-Fi', 'Air Conditioning', 'TV', 'Mini Bar', 'Safe', 'Balcony', 'City View', 'Room Service', 'Spa Access'],
                    ],
                ],
            ],
            [
                'name' => 'Гостиница "Хива"',
                'address' => 'ул. Пахлаван Махмуд, 8',
                'type' => '3_star',
                'description' => 'Уютная гостиница в сердце древней Хивы.',
                'phone' => '+998 62 345-67-89',
                'email' => 'hotel@khiva.uz',
                'rooms' => [
                    [
                        'name' => 'Family Room',
                        'description' => 'Семейный номер для 4 человек',
                        'room_type' => 'Family',
                        'cost_per_night' => 90.00,
                        'room_size' => 40.0,
                        'amenities' => ['Wi-Fi', 'Air Conditioning', 'TV', 'Safe', 'Breakfast Included'],
                    ],
                ],
            ],
            [
                'name' => 'Отель "Ташкент Центр"',
                'address' => 'ул. Навои, 45',
                'type' => '4_star',
                'description' => 'Современный отель в деловом центре Ташкента.',
                'phone' => '+998 71 456-78-90',
                'email' => 'info@tashkent-center.uz',
                'rooms' => [
                    [
                        'name' => 'Business Room',
                        'description' => 'Номер для деловых поездок',
                        'room_type' => 'Executive',
                        'cost_per_night' => 110.00,
                        'room_size' => 30.0,
                        'amenities' => ['Wi-Fi', 'Air Conditioning', 'TV', 'Safe', 'Gym Access'],
                    ],
                ],
            ],
            [
                'name' => 'Гостиница "Фергана"',
                'address' => 'ул. Мустакиллик, 33',
                'type' => '3_star',
                'description' => 'Комфортабельная гостиница в Фергане.',
                'phone' => '+998 73 567-89-01',
                'email' => 'hotel@fergana.uz',
                'rooms' => [
                    [
                        'name' => 'Standard Room',
                        'description' => 'Стандартный номер с базовыми удобствами',
                        'room_type' => 'Standard',
                        'cost_per_night' => 60.00,
                        'room_size' => 20.0,
                        'amenities' => ['Wi-Fi', 'Air Conditioning', 'TV'],
                    ],
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

        foreach ($hotels as $hotelData) {
            $rooms = $hotelData['rooms'];
            unset($hotelData['rooms']);
            
            $hotel = Hotel::create([
                'name' => $hotelData['name'],
                'address' => $hotelData['address'],
                'type' => $hotelData['type'],
                'category' => $hotelData['type'], // Same as type
                'description' => $hotelData['description'],
                'phone' => $hotelData['phone'],
                'email' => $hotelData['email'],
                'city_id' => $firstCity->id,
                'company_id' => $firstCompany->id,
                'images' => [],
            ]);
            
            // Create rooms for this hotel
            foreach ($rooms as $roomData) {
                $amenities = $roomData['amenities'];
                unset($roomData['amenities']);
                
                $roomType = RoomType::where('type', $roomData['room_type'])->first();
                
                $room = Room::create([
                    'name' => $roomData['name'],
                    'description' => $roomData['description'],
                    'room_type_id' => $roomType->id,
                    'cost_per_night' => $roomData['cost_per_night'],
                    'hotel_id' => $hotel->id,
                    'room_size' => $roomData['room_size'],
                    'images' => [],
                ]);
                
                // Attach amenities to room
                $amenityIds = Amenity::whereIn('name', $amenities)->pluck('id');
                $room->amenities()->attach($amenityIds);
            }
        }
    }
}
