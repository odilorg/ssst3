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
                Section::make('Хронология бронирования')
                    ->schema([
                        Placeholder::make('booking_timeline')
                            ->label('')
                            ->content(function ($record) {
                                if (!$record) return '';

                                $steps = [
                                    [
                                        'label' => 'Создано',
                                        'done' => true,
                                        'date' => $record->created_at?->format('d.m.Y'),
                                    ],
                                    [
                                        'label' => 'Подтверждено',
                                        'done' => in_array($record->status, ['confirmed', 'in_progress', 'completed']),
                                        'date' => in_array($record->status, ['confirmed', 'in_progress', 'completed']) ? '' : null,
                                    ],
                                    [
                                        'label' => 'Детали поездки',
                                        'done' => $record->hasTripDetails(),
                                        'date' => $record->tripDetail?->completed_at?->format('d.m.Y'),
                                    ],
                                    [
                                        'label' => 'Оплата',
                                        'done' => in_array($record->payment_status, ['paid', 'partial']),
                                        'date' => $record->deposit_paid_at?->format('d.m.Y'),
                                    ],
                                    [
                                        'label' => $record->status === 'completed' ? 'Завершено' : 'В туре',
                                        'done' => in_array($record->status, ['in_progress', 'completed']),
                                        'date' => $record->status === 'completed' ? $record->end_date?->format('d.m.Y') : null,
                                    ],
                                ];

                                $html = '<div style="display:flex;align-items:flex-start;flex-wrap:nowrap;">';
                                foreach ($steps as $i => $step) {
                                    $bg = $step['done'] ? '#059669' : '#d1d5db';
                                    $text = $step['done'] ? '#fff' : '#6b7280';
                                    $html .= '<div style="text-align:center;flex:1;min-width:0;">';
                                    $html .= '<div style="width:28px;height:28px;border-radius:50%;background:'.$bg.';color:'.$text.';display:inline-flex;align-items:center;justify-content:center;font-size:13px;font-weight:600;">'.($i+1).'</div>';
                                    $html .= '<div style="font-size:11px;margin-top:4px;color:'.($step['done'] ? '#059669' : '#6b7280').';font-weight:'.($step['done'] ? '600' : '400').';white-space:nowrap;">'.$step['label'].'</div>';
                                    if ($step['date']) {
                                        $html .= '<div style="font-size:10px;color:#9ca3af;">'.$step['date'].'</div>';
                                    }
                                    $html .= '</div>';
                                    if ($i < count($steps) - 1) {
                                        $lineColor = $steps[$i+1]['done'] ? '#059669' : '#d1d5db';
                                        $html .= '<div style="flex:0 0 30px;height:2px;background:'.$lineColor.';margin-top:14px;"></div>';
                                    }
                                }
                                $html .= '</div>';

                                if ($record->status === 'cancelled') {
                                    $html .= '<div style="margin-top:8px;padding:6px 12px;background:#fef2f2;border:1px solid #fecaca;border-radius:6px;color:#991b1b;font-size:12px;font-weight:500;">Бронирование отменено</div>';
                                }

                                return new HtmlString($html);
                            })
                            ->columnSpanFull(),
                    ])
                    ->hiddenOn('create'),

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
                        Placeholder::make('departure_time_display')
                            ->label('Время отправления')
                            ->content(fn ($record) => $record?->departure?->formatted_time ?? '—')
                            ->hiddenOn('create'),
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
