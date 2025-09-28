<?php

namespace App\Filament\Resources\Transports\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TransportForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Основная информация')
                    ->schema([
                        Select::make('category')
                            ->label('Категория')
                            ->options([
                                'bus' => 'Bus',
                                'car' => 'Car',
                                'mikro_bus' => 'Mikro Bus',
                                'mini_van' => 'Mini Van',
                                'air' => 'Air',
                                'rail' => 'Rail',
                            ])
                            ->required()
                            ->live(),
                        Select::make('transport_type_id')
                            ->label('Тип транспорта')
                            ->relationship('transportType', 'type')
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->type)
                            ->preload()
                            ->required(),
                        Select::make('company_id')
                            ->label('Компания')
                            ->relationship('company', 'name')
                            ->preload()
                            ->searchable()
                            ->required()
                            ->visible(fn ($get) => !in_array($get('category'), ['air', 'rail'])),
                        Select::make('driver_id')
                            ->label('Водитель')
                            ->relationship('driver', 'name')
                            ->preload()
                            ->searchable()
                            ->required()
                            ->visible(fn ($get) => !in_array($get('category'), ['air', 'rail'])),
                        Select::make('city_id')
                            ->label('Город')
                            ->relationship('city', 'name')
                            ->preload()
                            ->searchable()
                            ->required(),
                    ])
                    ->columns(2),

                Section::make('Информация о транспортном средстве')
                    ->schema([
                        TextInput::make('plate_number')
                            ->label('Номерной знак')
                            ->required()
                            ->maxLength(255)
                            ->visible(fn ($get) => !in_array($get('category'), ['air', 'rail'])),
                        TextInput::make('model')
                            ->label('Модель')
                            ->required()
                            ->maxLength(255)
                            ->visible(fn ($get) => !in_array($get('category'), ['air', 'rail'])),
                        TextInput::make('number_of_seat')
                            ->label('Количество мест')
                            ->numeric()
                            ->required()
                            ->visible(fn ($get) => !in_array($get('category'), ['air', 'rail'])),
                        TimePicker::make('departure_time')
                            ->label('Время отправления')
                            ->required()
                            ->visible(fn ($get) => in_array($get('category'), ['air', 'rail'])),
                        TimePicker::make('arrival_time')
                            ->label('Время прибытия')
                            ->required()
                            ->visible(fn ($get) => in_array($get('category'), ['air', 'rail'])),
                    ])
                    ->columns(2),

                Section::make('Топливо и обслуживание')
                    ->schema([
                        Select::make('fuel_type')
                            ->label('Тип топлива')
                            ->options([
                                'diesel' => 'Дизель',
                                'benzin/propane' => 'Бензин/Пропан',
                                'natural_gaz' => 'Газ',
                            ])
                            ->required()
                            ->visible(fn ($get) => !in_array($get('category'), ['air', 'rail'])),
                        TextInput::make('fuel_consumption')
                            ->label('Расход топлива (л/100км)')
                            ->numeric()
                            ->required()
                            ->visible(fn ($get) => !in_array($get('category'), ['air', 'rail'])),
                        TextInput::make('oil_change_interval_months')
                            ->label('Интервал замены масла (месяцы)')
                            ->numeric()
                            ->required()
                            ->visible(fn ($get) => !in_array($get('category'), ['air', 'rail'])),
                        TextInput::make('oil_change_interval_km')
                            ->label('Интервал замены масла (км)')
                            ->numeric()
                            ->required()
                            ->visible(fn ($get) => !in_array($get('category'), ['air', 'rail'])),
                    ])
                    ->columns(2),

                Section::make('Удобства и изображения')
                    ->schema([
                        Select::make('amenities')
                            ->label('Удобства')
                            ->relationship('amenities', 'name')
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->createOptionForm([
                                TextInput::make('name')
                                    ->label('Название удобства')
                                    ->required()
                                    ->maxLength(255),
                            ]),
                        FileUpload::make('images')
                            ->label('Изображения')
                            ->multiple()
                            ->image()
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }
}
