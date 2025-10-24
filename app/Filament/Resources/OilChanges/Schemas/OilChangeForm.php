<?php

namespace App\Filament\Resources\OilChanges\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use App\Models\Transport;

class OilChangeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Информация о замене масла')
                    ->schema([
                        Select::make('transport_id')
                            ->label('Транспорт')
                            ->relationship('transport', 'plate_number')
                            ->getOptionLabelFromRecordUsing(function ($record) {
                                $plate = $record->plate_number ?? '—';
                                $model = $record->model ?? '—';
                                return $plate . ' - ' . $model;
                            })
                            ->preload()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state) {
                                    $transport = Transport::find($state);
                                    if ($transport) {
                                        $set('next_change_date', now()->addMonths($transport->oil_change_interval_months)->format('Y-m-d'));
                                        $set('next_change_mileage', $transport->oil_change_interval_km);
                                    }
                                }
                            }),
                        DatePicker::make('oil_change_date')
                            ->label('Дата замены масла')
                            ->required()
                            ->native(false)
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set, $get) {
                                $transport = Transport::find($get('transport_id'));
                                if ($transport && $state) {
                                    $nextChangeDate = \Carbon\Carbon::parse($state)->addMonths($transport->oil_change_interval_months);
                                    $set('next_change_date', $nextChangeDate->format('Y-m-d'));
                                }
                            }),
                        TextInput::make('mileage_at_change')
                            ->label('Пробег при замене')
                            ->numeric()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set, $get) {
                                $transport = Transport::find($get('transport_id'));
                                if ($transport) {
                                    $nextChangeMileage = $state + $transport->oil_change_interval_km;
                                    $set('next_change_mileage', $nextChangeMileage);
                                }
                            }),
                        TextInput::make('cost')
                            ->label('Стоимость')
                            ->numeric()
                            ->prefix('UZS')
                            ->nullable(),
                        TextInput::make('oil_type')
                            ->label('Тип масла')
                            ->maxLength(255)
                            ->nullable(),
                        TextInput::make('service_center')
                            ->label('Сервисный центр')
                            ->maxLength(255)
                            ->nullable(),
                        Textarea::make('notes')
                            ->label('Примечания')
                            ->columnSpanFull()
                            ->nullable(),
                    ])
                    ->columns(2),
                
                Section::make('Автоматически рассчитанные поля')
                    ->schema([
                        DatePicker::make('next_change_date')
                            ->label('Дата следующей замены')
                            ->readOnly()
                            ->dehydrated(),
                        TextInput::make('next_change_mileage')
                            ->label('Пробег следующей замены')
                            ->numeric()
                            ->readOnly()
                            ->dehydrated(),
                    ])
                    ->columns(2),
                
                Section::make('Дополнительные услуги')
                    ->schema([
                        Repeater::make('other_services')
                            ->label('Другие услуги')
                            ->schema([
                                TextInput::make('service_name')
                                    ->label('Название услуги')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('service_cost')
                                    ->label('Стоимость услуги')
                                    ->numeric()
                                    ->prefix('UZS')
                                    ->required(),
                            ])
                            ->columns(2)
                            ->addActionLabel('Добавить услугу')
                            ->reorderable(true),
                    ]),
            ]);
    }
}
