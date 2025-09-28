<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customers = [
            [
                'name' => 'Анна Петрова',
                'email' => 'anna.petrova@email.com',
                'phone' => '+7 912 345-67-89',
                'address' => 'ул. Ленина, 15, кв. 23, Москва, Россия',
            ],
            [
                'name' => 'Михаил Иванов',
                'email' => 'mikhail.ivanov@gmail.com',
                'phone' => '+7 903 456-78-90',
                'address' => 'пр. Мира, 45, кв. 12, Санкт-Петербург, Россия',
            ],
            [
                'name' => 'Елена Смирнова',
                'email' => 'elena.smirnova@yandex.ru',
                'phone' => '+7 914 567-89-01',
                'address' => 'ул. Пушкина, 78, кв. 5, Казань, Россия',
            ],
            [
                'name' => 'Дмитрий Козлов',
                'email' => 'dmitry.kozlov@mail.ru',
                'phone' => '+7 905 678-90-12',
                'address' => 'ул. Гагарина, 23, кв. 8, Екатеринбург, Россия',
            ],
            [
                'name' => 'Ольга Морозова',
                'email' => 'olga.morozova@outlook.com',
                'phone' => '+7 916 789-01-23',
                'address' => 'пр. Победы, 67, кв. 15, Новосибирск, Россия',
            ],
            [
                'name' => 'Александр Волков',
                'email' => 'alexander.volkov@yahoo.com',
                'phone' => '+7 907 890-12-34',
                'address' => 'ул. Советская, 34, кв. 22, Нижний Новгород, Россия',
            ],
            [
                'name' => 'Татьяна Лебедева',
                'email' => 'tatyana.lebedeva@rambler.ru',
                'phone' => '+7 918 901-23-45',
                'address' => 'ул. Центральная, 56, кв. 7, Самара, Россия',
            ],
            [
                'name' => 'Сергей Новиков',
                'email' => 'sergey.novikov@bk.ru',
                'phone' => '+7 909 012-34-56',
                'address' => 'пр. Ленина, 89, кв. 18, Омск, Россия',
            ],
            [
                'name' => 'Наталья Федорова',
                'email' => 'natalya.fedorova@list.ru',
                'phone' => '+7 920 123-45-67',
                'address' => 'ул. Мира, 12, кв. 3, Казань, Россия',
            ],
            [
                'name' => 'Андрей Соколов',
                'email' => 'andrey.sokolov@inbox.ru',
                'phone' => '+7 911 234-56-78',
                'address' => 'ул. Парковая, 45, кв. 11, Ростов-на-Дону, Россия',
            ],
        ];

        foreach ($customers as $customer) {
            Customer::create($customer);
        }
    }
}
