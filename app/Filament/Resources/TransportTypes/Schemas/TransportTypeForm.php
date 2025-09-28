<?php

namespace App\Filament\Resources\TransportTypes\Schemas;

use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TransportTypeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Информация о типе транспорта')
                    ->schema([
                        TextInput::make('type')
                            ->label('Тип транспорта')
                            ->required()
                            ->maxLength(255),
                        Select::make('category')
                            ->label('Категория')
                            ->options([
                                'bus' => 'Автобус',
                                'car' => 'Автомобиль',
                                'mikro_bus' => 'Микроавтобус',
                                'mini_van' => 'Минивэн',
                                'air' => 'Авиа',
                                'rail' => 'Железная дорога',
                            ])
                            ->required(),
                        CheckboxList::make('running_days')
                            ->label('Дни работы')
                            ->options([
                                'monday' => 'Понедельник',
                                'tuesday' => 'Вторник',
                                'wednesday' => 'Среда',
                                'thursday' => 'Четверг',
                                'friday' => 'Пятница',
                                'saturday' => 'Суббота',
                                'sunday' => 'Воскресенье',
                            ])
                            ->columns(4),
                    ])
                    ->columns(2),
            ]);
    }
}
