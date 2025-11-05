<?php

namespace App\Filament\Resources\TourDepartures\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TourDepartureForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Детали отправления')
                    ->description('Основная информация о дате отправления')
                    ->schema([
                        Select::make('tour_id')
                            ->label('Тур')
                            ->relationship('tour', 'title')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->helperText('Выберите тур для этого отправления'),

                        DatePicker::make('start_date')
                            ->label('Дата начала')
                            ->required()
                            ->native(false)
                            ->displayFormat('d M Y')
                            ->minDate(now())
                            ->live()
                            ->helperText('Дата отправления тура'),

                        DatePicker::make('end_date')
                            ->label('Дата окончания')
                            ->required()
                            ->native(false)
                            ->displayFormat('d M Y')
                            ->minDate(fn ($get) => $get('start_date') ?: now())
                            ->helperText('Дата завершения тура')
                            ->rules([
                                fn ($get) => function ($attribute, $value, $fail) use ($get) {
                                    $startDate = $get('start_date');
                                    if ($startDate && $value < $startDate) {
                                        $fail('Дата окончания должна быть после даты начала.');
                                    }
                                },
                            ]),

                        Select::make('departure_type')
                            ->label('Тип отправления')
                            ->options([
                                'group' => 'Группа',
                                'private' => 'Приватный',
                            ])
                            ->required()
                            ->default('group')
                            ->helperText('Тип отправления: группа или приватный'),

                        Select::make('status')
                            ->label('Статус')
                            ->options([
                                'open' => 'Открыт',
                                'guaranteed' => 'Гарантирован',
                                'full' => 'Полный',
                                'cancelled' => 'Отменен',
                                'completed' => 'Завершен',
                            ])
                            ->required()
                            ->default('open')
                            ->helperText('Текущий статус отправления'),
                    ])
                    ->columns(2),

                Section::make('Вместимость')
                    ->description('Настройки вместимости и бронирования')
                    ->schema([
                        TextInput::make('max_pax')
                            ->label('Максимум пассажиров')
                            ->numeric()
                            ->required()
                            ->minValue(1)
                            ->default(15)
                            ->helperText('Максимальное количество пассажиров')
                            ->rules([
                                fn ($get) => function ($attribute, $value, $fail) use ($get) {
                                    $minPax = $get('min_pax');
                                    if ($minPax && $value < $minPax) {
                                        $fail('Максимум должен быть больше или равен минимуму.');
                                    }
                                },
                            ]),

                        TextInput::make('min_pax')
                            ->label('Минимум для гарантии')
                            ->numeric()
                            ->minValue(1)
                            ->helperText('Минимум пассажиров для подтверждения отправления'),

                        TextInput::make('booked_pax')
                            ->label('Забронировано')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->disabled()
                            ->dehydrated(false)
                            ->helperText('Обновляется автоматически'),

                        TextInput::make('price_per_person')
                            ->label('Цена за человека')
                            ->numeric()
                            ->prefix('$')
                            ->step(0.01)
                            ->helperText('Оставьте пустым для использования цены тура'),
                    ])
                    ->columns(2),

                Section::make('Дополнительная информация')
                    ->schema([
                        Textarea::make('notes')
                            ->label('Примечания')
                            ->maxLength(1000)
                            ->rows(3)
                            ->helperText('Внутренние заметки об этом отправлении')
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }
}
