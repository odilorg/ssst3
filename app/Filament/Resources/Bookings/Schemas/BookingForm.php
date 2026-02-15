<?php

namespace App\Filament\Resources\Bookings\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;

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
                            ->relationship('customer', 'name', modifyQueryUsing: fn ($query) => $query->whereNotNull('name')->where('name', '!=', ''))
                            ->required()
                            ->searchable()
                            ->preload(),
                        Select::make('tour_id')
                            ->label('Тур')
                            ->relationship('tour', 'title', modifyQueryUsing: fn ($query) => $query->whereNotNull('title')->where('title', '!=', ''))
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
                Section::make('Детали поездки (от гостя)')
                    ->description('Информация, заполненная гостем через форму trip details')
                    ->schema([
                        Placeholder::make('trip_status')
                            ->label('Статус')
                            ->content(fn ($record) => $record?->tripDetail?->isCompleted()
                                ? new HtmlString('<span style="color: #16a34a; font-weight: 600;">Заполнено</span>')
                                : new HtmlString('<span style="color: #d97706; font-weight: 600;">Не заполнено</span>')
                            ),
                        Placeholder::make('trip_whatsapp')
                            ->label('WhatsApp')
                            ->content(fn ($record) => $record?->tripDetail?->whatsapp_number ?? '—'),
                        Placeholder::make('trip_hotel')
                            ->label('Отель')
                            ->content(fn ($record) => $record?->tripDetail?->hotel_name
                                ? ($record->tripDetail->hotel_name . ($record->tripDetail->hotel_address ? " ({$record->tripDetail->hotel_address})" : ''))
                                : '—'),
                        Placeholder::make('trip_arrival')
                            ->label('Прибытие')
                            ->content(fn ($record) => $record?->tripDetail?->arrival_date
                                ? ($record->tripDetail->arrival_date->format('d.m.Y') . ($record->tripDetail->arrival_flight ? " — {$record->tripDetail->arrival_flight}" : '') . ($record->tripDetail->arrival_time ? " в {$record->tripDetail->arrival_time}" : ''))
                                : '—')
                            ->visible(fn ($record) => $record?->needsFullTripDetails()),
                        Placeholder::make('trip_departure')
                            ->label('Отъезд')
                            ->content(fn ($record) => $record?->tripDetail?->departure_date
                                ? ($record->tripDetail->departure_date->format('d.m.Y') . ($record->tripDetail->departure_flight ? " — {$record->tripDetail->departure_flight}" : '') . ($record->tripDetail->departure_time ? " в {$record->tripDetail->departure_time}" : ''))
                                : '—')
                            ->visible(fn ($record) => $record?->needsFullTripDetails()),
                        Placeholder::make('trip_language')
                            ->label('Язык')
                            ->content(fn ($record) => $record?->tripDetail?->language_preference
                                ? ucfirst($record->tripDetail->language_preference)
                                : '—'),
                        Placeholder::make('trip_referral')
                            ->label('Источник')
                            ->content(fn ($record) => $record?->tripDetail?->referral_source
                                ? ucfirst($record->tripDetail->referral_source)
                                : '—'),
                        Placeholder::make('trip_notes')
                            ->label('Доп. заметки')
                            ->content(fn ($record) => $record?->tripDetail?->additional_notes ?? '—')
                            ->columnSpanFull(),
                        Placeholder::make('trip_details_link')
                            ->label('')
                            ->content(fn ($record) => $record?->passenger_details_url_token
                                ? new HtmlString('<a href="' . route('trip-details.show', ['token' => $record->passenger_details_url_token]) . '" target="_blank" style="color: #3b82f6; text-decoration: underline;">Открыть форму гостя →</a>')
                                : new HtmlString('<span style="color: #9ca3af;">Ссылка будет доступна после отправки email</span>'))
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->collapsible()
                    ->collapsed(fn ($record) => !$record?->tripDetail?->isCompleted())
                    ->hiddenOn('create'),

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
