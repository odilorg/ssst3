<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Company;
use App\Models\Monument;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MonumentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $monuments = [
            [
                'name' => 'Регистан',
                'city' => 'Самарканд',
                'ticket_price' => 25.00,
                'description' => 'Знаменитый архитектурный ансамбль в центре Самарканда, состоящий из трех медресе: Улугбека, Шер-Дор и Тилля-Кари.',
                'voucher' => true,
            ],
            [
                'name' => 'Мавзолей Гур-Эмир',
                'city' => 'Самарканд',
                'ticket_price' => 15.00,
                'description' => 'Мавзолей Тимура и его потомков, шедевр средневековой архитектуры.',
                'voucher' => true,
            ],
            [
                'name' => 'Крепость Арк',
                'city' => 'Бухара',
                'ticket_price' => 20.00,
                'description' => 'Древняя цитадель Бухары, резиденция правителей на протяжении веков.',
                'voucher' => true,
            ],
            [
                'name' => 'Минарет Калян',
                'city' => 'Бухара',
                'ticket_price' => 12.00,
                'description' => 'Символ Бухары, один из самых высоких минаретов в Центральной Азии.',
                'voucher' => false,
            ],
            [
                'name' => 'Ичан-Кала',
                'city' => 'Хива',
                'ticket_price' => 30.00,
                'description' => 'Внутренний город Хивы, музей под открытым небом с множеством исторических памятников.',
                'voucher' => true,
            ],
            [
                'name' => 'Минарет Ислам-Ходжа',
                'city' => 'Хива',
                'ticket_price' => 8.00,
                'description' => 'Самый высокий минарет в Хиве, символ города.',
                'voucher' => false,
            ],
            [
                'name' => 'Площадь Независимости',
                'city' => 'Ташкент',
                'ticket_price' => 0.00,
                'description' => 'Главная площадь столицы Узбекистана, место проведения государственных мероприятий.',
                'voucher' => false,
            ],
            [
                'name' => 'Медресе Кукельдаш',
                'city' => 'Ташкент',
                'ticket_price' => 10.00,
                'description' => 'Крупнейшее медресе в Ташкенте, памятник архитектуры XVI века.',
                'voucher' => true,
            ],
            [
                'name' => 'Дворец Худояр-хана',
                'city' => 'Коканд',
                'ticket_price' => 18.00,
                'description' => 'Резиденция последнего правителя Кокандского ханства, памятник архитектуры XIX века.',
                'voucher' => true,
            ],
            [
                'name' => 'Мемориал Бабура',
                'city' => 'Андижан',
                'ticket_price' => 5.00,
                'description' => 'Мемориальный комплекс, посвященный основателю империи Великих Моголов.',
                'voucher' => false,
            ],
        ];

        // Get first city and company for relationships
        $firstCity = City::first();
        $firstCompany = Company::first();

        if (!$firstCity || !$firstCompany) {
            $this->command->error('Please run CitySeeder and CompanySeeder first!');
            return;
        }

        foreach ($monuments as $monumentData) {
            Monument::create([
                'name' => $monumentData['name'],
                'city' => $monumentData['city'],
                'ticket_price' => $monumentData['ticket_price'],
                'description' => $monumentData['description'],
                'city_id' => $firstCity->id,
                'company_id' => $firstCompany->id,
                'voucher' => $monumentData['voucher'],
                'images' => [],
            ]);
        }
    }
}
