<?php

namespace App\Filament\Resources\Bookings\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class BookingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Основная информация')
                    ->schema([
                        Select::make('customer_id')
                            ->label('Клиент')
                            ->relationship('customer', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Select::make('tour_id')
                            ->label('Тур')
                            ->relationship('tour', 'title')
                            ->required()
                            ->searchable()
                            ->preload(),
                        DatePicker::make('start_date')
                            ->label('Дата начала')
                            ->required(),
                        DatePicker::make('end_date')
                            ->label('Дата окончания')
                            ->disabled(),
                        TextInput::make('pax_total')
                            ->label('Количество участников')
                            ->numeric()
                            ->default(1)
                            ->required()
                            ->minValue(1),
                        Select::make('status')
                            ->label('Статус')
                            ->options([
                                'draft' => 'Черновик',
                                'pending' => 'В ожидании',
                                'confirmed' => 'Подтверждено',
                                'in_progress' => 'В процессе',
                                'completed' => 'Завершено',
                                'cancelled' => 'Отменено',
                            ])
                            ->default('draft')
                            ->required(),
                        Select::make('currency')
                            ->label('Валюта')
                            ->options([
                                'USD' => 'USD',
                                'EUR' => 'EUR',
                                'RUB' => 'RUB',
                            ])
                            ->default('USD')
                            ->required(),
                        TextInput::make('total_price')
                            ->label('Общая стоимость')
                            ->numeric()
                            ->prefix('$')
                            ->default(0),
                        TextInput::make('reference')
                            ->label('Номер бронирования')
                            ->disabled()
                            ->dehydrated(false),
                    ])
                    ->columns(3),

Section::make('Назначения')                    ->description('Водитель, гид и транспорт для этого бронирования')                    ->schema([                        TextInput::make('driver_name')                            ->label('Имя водителя')                            ->placeholder('Например: Азиз'),                        TextInput::make('driver_phone')                            ->label('Телефон водителя')                            ->tel()                            ->placeholder('+998 90 123 4567'),                        TextInput::make('guide_name')                            ->label('Имя гида')                            ->placeholder('Например: Малика'),                        TextInput::make('guide_phone')                            ->label('Телефон гида')                            ->tel()                            ->placeholder('+998 91 234 5678'),                        TextInput::make('vehicle_info')                            ->label('Транспорт')                            ->placeholder('Toyota Land Cruiser, номер UZ-123-AB')                            ->columnSpanFull(),                    ])                    ->columns(2)                    ->collapsible(),
                Section::make('Заметки')
                    ->schema([
                        Textarea::make('notes')                            ->label('Заметки клиента')                            ->rows(3)                            ->columnSpanFull(),                        Textarea::make('internal_notes')                            ->label('Внутренние заметки (не видны клиенту)')
                            ->label('Заметки')
                            ->rows(4)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
