<?php

namespace Database\Seeders;

use App\Models\Driver;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DriverSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $drivers = [
            [
                'name' => 'SHODMONOV JAMSHID',
                'email' => 'jamshid.shodmonov@example.com',
                'phone' => '+998993120741',
                'address' => 'ул. Навои, 15, Ташкент',
                'license_number' => 'AD 6301731',
                'license_expiry_date' => '27.02.2034',
            ],
            [
                'name' => 'KARIMOV ALISHER',
                'email' => 'alisher.karimov@example.com',
                'phone' => '+998901234567',
                'address' => 'пр. Регистан, 25, Самарканд',
                'license_number' => 'AD 1234567',
                'license_expiry_date' => '15.06.2032',
            ],
            [
                'name' => 'TURSUNOV RUSTAM',
                'email' => 'rustam.tursunov@example.com',
                'phone' => '+998902345678',
                'address' => 'ул. Бахауддина Накшбанди, 8, Бухара',
                'license_number' => 'AD 2345678',
                'license_expiry_date' => '20.08.2033',
            ],
            [
                'name' => 'AKHMEDOV BAKHTIYOR',
                'email' => 'bakhtiyor.akhamedov@example.com',
                'phone' => '+998903456789',
                'address' => 'ул. Пахлаван Махмуд, 12, Хива',
                'license_number' => 'AD 3456789',
                'license_expiry_date' => '10.12.2031',
            ],
            [
                'name' => 'YULDASHEV DILAFRUZ',
                'email' => 'dilafruz.yuldashev@example.com',
                'phone' => '+998904567890',
                'address' => 'ул. Мустакиллик, 45, Фергана',
                'license_number' => 'AD 4567890',
                'license_expiry_date' => '05.03.2034',
            ],
            [
                'name' => 'MAHMUDOV ILHOM',
                'email' => 'ilhom.mahmudov@example.com',
                'phone' => '+998905678901',
                'address' => 'ул. Чорсу, 33, Наманган',
                'license_number' => 'AD 5678901',
                'license_expiry_date' => '18.09.2032',
            ],
            [
                'name' => 'ATAMURATOVA AYGUL',
                'email' => 'aygul.atamuratova@example.com',
                'phone' => '+998906789012',
                'address' => 'ул. Бабура, 67, Андижан',
                'license_number' => 'AD 6789012',
                'license_expiry_date' => '25.11.2033',
            ],
            [
                'name' => 'HAKIMOV BAKHTIYOR',
                'email' => 'bakhtiyor.hakimov@example.com',
                'phone' => '+998907890123',
                'address' => 'ул. Каракалпакстан, 89, Нукус',
                'license_number' => 'AD 7890123',
                'license_expiry_date' => '12.07.2031',
            ],
            [
                'name' => 'RAHIMOVA ZULFIYA',
                'email' => 'zulfiya.rahimova@example.com',
                'phone' => '+998908901234',
                'address' => 'ул. Афганистан, 11, Термез',
                'license_number' => 'AD 8901234',
                'license_expiry_date' => '30.04.2034',
            ],
            [
                'name' => 'NURIDDINOV SHUKHRAT',
                'email' => 'shukhrat.nuriddinov@example.com',
                'phone' => '+998909012345',
                'address' => 'ул. Сырдарья, 55, Гулистан',
                'license_number' => 'AD 9012345',
                'license_expiry_date' => '08.01.2032',
            ],
        ];

        foreach ($drivers as $driverData) {
            Driver::create($driverData);
        }
    }
}
