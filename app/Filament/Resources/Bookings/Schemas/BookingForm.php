<?php

namespace App\Filament\Resources\Bookings\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Radio;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class BookingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Детали бронирования')
                    ->description('Основная информация о бронировании')
                    ->schema([
                        Select::make('tour_id')
                            ->label('Тур')
                            ->relationship('tour', 'title')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->live()
                            ->afterStateUpdated(fn (callable $set) => $set('departure_id', null)),

                        Select::make('departure_id')
                            ->label('Отправление')
                            ->relationship(
                                'departure',
                                'start_date',
                                fn ($query, callable $get) => $query
                                    ->when($get('tour_id'), fn ($q, $tourId) => $q->where('tour_id', $tourId))
                                    ->where('start_date', '>=', now())
                                    ->whereIn('status', ['open', 'guaranteed'])
                                    ->orderBy('start_date')
                            )
                            ->required()
                            ->searchable()
                            ->preload()
                            ->live()
                            ->getOptionLabelFromRecordUsing(fn ($record) =>
                                $record->start_date->format('d M Y') .
                                ' (' . $record->spotsRemaining() . ' мест)'
                            )
                            ->helperText('Выберите дату отправления'),

                        Radio::make('booking_type')
                            ->label('Тип бронирования')
                            ->options([
                                'group' => 'Группа',
                                'private' => 'Приватный',
                            ])
                            ->required()
                            ->default('group')
                            ->live()
                            ->helperText('Тип тура'),

                        TextInput::make('pax_total')
                            ->label('Количество пассажиров')
                            ->numeric()
                            ->required()
                            ->minValue(1)
                            ->default(1)
                            ->live()
                            ->helperText('Общее количество путешественников'),

                        Select::make('status')
                            ->label('Статус')
                            ->options([
                                'draft' => 'Черновик',
                                'inquiry' => 'Запрос',
                                'pending_payment' => 'Ожидание оплаты',
                                'confirmed' => 'Подтверждено',
                                'in_progress' => 'В процессе',
                                'completed' => 'Завершено',
                                'cancelled' => 'Отменено',
                                'declined' => 'Отклонено',
                            ])
                            ->default('draft')
                            ->required(),

                        TextInput::make('reference')
                            ->label('Номер бронирования')
                            ->disabled()
                            ->dehydrated(false)
                            ->helperText('Генерируется автоматически'),
                    ])
                    ->columns(3),

                Section::make('Информация о клиенте')
                    ->description('Контактная информация клиента')
                    ->schema([
                        TextInput::make('customer_name')
                            ->label('Имя')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('customer_email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->maxLength(255),

                        TextInput::make('customer_phone')
                            ->label('Телефон')
                            ->tel()
                            ->required()
                            ->maxLength(50),

                        TextInput::make('customer_country')
                            ->label('Страна')
                            ->maxLength(100),

                        Textarea::make('special_requests')
                            ->label('Особые запросы')
                            ->maxLength(1000)
                            ->rows(3)
                            ->columnSpanFull()
                            ->helperText('Любые особые требования или запросы клиента'),
                    ])
                    ->columns(2),

                Section::make('Информация об оплате')
                    ->description('Детали оплаты и статус')
                    ->schema([
                        Radio::make('payment_method')
                            ->label('Метод оплаты')
                            ->options([
                                'deposit' => 'Депозит (30%)',
                                'full_payment' => 'Полная оплата (скидка 10%)',
                                'request' => 'Запрос на бронирование',
                            ])
                            ->required()
                            ->default('deposit')
                            ->live()
                            ->helperText('Выберите метод оплаты'),

                        Select::make('payment_status')
                            ->label('Статус оплаты')
                            ->options([
                                'unpaid' => 'Не оплачено',
                                'payment_pending' => 'Ожидание оплаты',
                                'deposit_paid' => 'Депозит оплачен',
                                'fully_paid' => 'Полностью оплачено',
                            ])
                            ->default('unpaid')
                            ->required(),

                        TextInput::make('currency')
                            ->label('Валюта')
                            ->default('USD')
                            ->required()
                            ->maxLength(3),

                        TextInput::make('total_price')
                            ->label('Общая стоимость')
                            ->numeric()
                            ->prefix('$')
                            ->required()
                            ->minValue(0),

                        Placeholder::make('deposit_calculation')
                            ->label('Сумма депозита (30%)')
                            ->content(fn ($get) => '$' . number_format(($get('total_price') ?? 0) * 0.30, 2))
                            ->visible(fn ($get) => $get('payment_method') === 'deposit'),

                        Placeholder::make('full_payment_calculation')
                            ->label('Полная оплата со скидкой (90%)')
                            ->content(fn ($get) => '$' . number_format(($get('total_price') ?? 0) * 0.90, 2))
                            ->visible(fn ($get) => $get('payment_method') === 'full_payment'),

                        TextInput::make('amount_paid')
                            ->label('Сумма оплачена')
                            ->numeric()
                            ->prefix('$')
                            ->default(0)
                            ->disabled()
                            ->dehydrated(false)
                            ->helperText('Обновляется автоматически'),

                        TextInput::make('amount_remaining')
                            ->label('Остаток к оплате')
                            ->numeric()
                            ->prefix('$')
                            ->default(0)
                            ->disabled()
                            ->dehydrated(false)
                            ->helperText('Обновляется автоматически'),

                        TextInput::make('discount_applied')
                            ->label('Применена скидка')
                            ->numeric()
                            ->prefix('$')
                            ->default(0)
                            ->helperText('Скидка 10% за полную оплату'),

                        DatePicker::make('balance_due_date')
                            ->label('Срок оплаты баланса')
                            ->native(false)
                            ->displayFormat('d M Y')
                            ->disabled()
                            ->dehydrated(false)
                            ->helperText('Рассчитывается автоматически'),

                        TextInput::make('payment_uuid')
                            ->label('UUID платежа OCTO')
                            ->maxLength(255)
                            ->disabled()
                            ->dehydrated(false)
                            ->helperText('ID транзакции платежного шлюза')
                            ->columnSpanFull(),

                        Textarea::make('inquiry_notes')
                            ->label('Примечания к запросу')
                            ->visible(fn ($get) => $get('payment_method') === 'request')
                            ->rows(3)
                            ->columnSpanFull()
                            ->helperText('Примечания для запросов на бронирование'),
                    ])
                    ->columns(3),

                Section::make('Старая система (для совместимости)')
                    ->description('Поля из старой системы бронирования')
                    ->schema([
                        Select::make('customer_id')
                            ->label('Клиент (старая система)')
                            ->relationship('customer', 'name')
                            ->searchable()
                            ->preload()
                            ->helperText('Используется для обратной совместимости'),

                        DatePicker::make('start_date')
                            ->label('Дата начала (устаревшая)')
                            ->native(false)
                            ->helperText('Используйте поле "Отправление"'),

                        DatePicker::make('end_date')
                            ->label('Дата окончания (устаревшая)')
                            ->native(false)
                            ->disabled(),

                        Textarea::make('notes')
                            ->label('Заметки')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->collapsible()
                    ->collapsed(),
            ]);
    }
}
