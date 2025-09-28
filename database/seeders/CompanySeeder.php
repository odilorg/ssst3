<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companies = [
            [
                'name' => 'UzTour Travel',
                'address_street' => 'ул. Навои, 15',
                'address_city' => 'Ташкент',
                'phone' => '+998 71 123-45-67',
                'email' => 'info@uztour.uz',
                'inn' => 123456789,
                'account_number' => 20208000123456789012,
                'bank_name' => 'АО "Асака банк"',
                'bank_mfo' => 14,
                'director_name' => 'Алиев Алишер Алиевич',
                'is_operator' => true,
                'license_number' => 'TO-001-2024',
            ],
            [
                'name' => 'Silk Road Tours',
                'address_street' => 'пр. Регистан, 25',
                'address_city' => 'Самарканд',
                'phone' => '+998 66 234-56-78',
                'email' => 'contact@silkroutetours.uz',
                'inn' => 234567890,
                'account_number' => 20208000234567890123,
                'bank_name' => 'АО "Хамкор банк"',
                'bank_mfo' => 15,
                'director_name' => 'Каримова Мадина Рахимовна',
                'is_operator' => false,
                'license_number' => 'TO-002-2024',
            ],
            [
                'name' => 'Bukhara Heritage',
                'address_street' => 'ул. Бахауддина Накшбанди, 8',
                'address_city' => 'Бухара',
                'phone' => '+998 65 345-67-89',
                'email' => 'heritage@bukhara.uz',
                'inn' => 345678901,
                'account_number' => 20208000345678901234,
                'bank_name' => 'АО "Ипотека банк"',
                'bank_mfo' => 16,
                'director_name' => 'Нуриддинов Шухрат Абдуллаевич',
                'is_operator' => false,
                'license_number' => 'TO-003-2024',
            ],
            [
                'name' => 'Khiva Adventures',
                'address_street' => 'ул. Пахлаван Махмуд, 12',
                'address_city' => 'Хива',
                'phone' => '+998 62 456-78-90',
                'email' => 'adventures@khiva.uz',
                'inn' => 456789012,
                'account_number' => 20208000456789012345,
                'bank_name' => 'АО "Агробанк"',
                'bank_mfo' => 17,
                'director_name' => 'Турсунова Гулнора Абдурахимовна',
                'is_operator' => false,
                'license_number' => 'TO-004-2024',
            ],
            [
                'name' => 'Fergana Valley Tours',
                'address_street' => 'ул. Мустакиллик, 45',
                'address_city' => 'Фергана',
                'phone' => '+998 73 567-89-01',
                'email' => 'valley@fergana.uz',
                'inn' => 567890123,
                'account_number' => 20208000567890123456,
                'bank_name' => 'АО "Узбекский национальный банк"',
                'bank_mfo' => 18,
                'director_name' => 'Ахмедов Рустам Каримович',
                'is_operator' => false,
                'license_number' => 'TO-005-2024',
            ],
            [
                'name' => 'Namangan Travel',
                'address_street' => 'ул. Чорсу, 33',
                'address_city' => 'Наманган',
                'phone' => '+998 69 678-90-12',
                'email' => 'travel@namangan.uz',
                'inn' => 678901234,
                'account_number' => 20208000678901234567,
                'bank_name' => 'АО "Капитал банк"',
                'bank_mfo' => 19,
                'director_name' => 'Юлдашева Дилафруз Абдуллаевна',
                'is_operator' => false,
                'license_number' => 'TO-006-2024',
            ],
            [
                'name' => 'Andijan Explorer',
                'address_street' => 'ул. Бабура, 67',
                'address_city' => 'Андижан',
                'phone' => '+998 74 789-01-23',
                'email' => 'explorer@andijan.uz',
                'inn' => 789012345,
                'account_number' => 20208000789012345678,
                'bank_name' => 'АО "Траст банк"',
                'bank_mfo' => 20,
                'director_name' => 'Махмудов Илхом Рахимович',
                'is_operator' => false,
                'license_number' => 'TO-007-2024',
            ],
            [
                'name' => 'Nukus Desert Tours',
                'address_street' => 'ул. Каракалпакстан, 89',
                'address_city' => 'Нукус',
                'phone' => '+998 61 890-12-34',
                'email' => 'desert@nukus.uz',
                'inn' => 890123456,
                'account_number' => 20208000890123456789,
                'bank_name' => 'АО "Микрокредитбанк"',
                'bank_mfo' => 21,
                'director_name' => 'Атамуратова Айгуль Базарбаевна',
                'is_operator' => false,
                'license_number' => 'TO-008-2024',
            ],
            [
                'name' => 'Termez Border Tours',
                'address_street' => 'ул. Афганистан, 11',
                'address_city' => 'Термез',
                'phone' => '+998 76 901-23-45',
                'email' => 'border@termez.uz',
                'inn' => 901234567,
                'account_number' => 20208000901234567890,
                'bank_name' => 'АО "Давр банк"',
                'bank_mfo' => 22,
                'director_name' => 'Хакимов Бахтиёр Абдуллаевич',
                'is_operator' => false,
                'license_number' => 'TO-009-2024',
            ],
            [
                'name' => 'Gulistan City Tours',
                'address_street' => 'ул. Сырдарья, 55',
                'address_city' => 'Гулистан',
                'phone' => '+998 67 012-34-56',
                'email' => 'city@gulistan.uz',
                'inn' => 123456780,
                'account_number' => 20208000012345678901,
                'bank_name' => 'АО "Универсал банк"',
                'bank_mfo' => 23,
                'director_name' => 'Рахимова Зульфия Абдурахимовна',
                'is_operator' => false,
                'license_number' => 'TO-010-2024',
            ],
        ];

        foreach ($companies as $company) {
            Company::create($company);
        }
    }
}
