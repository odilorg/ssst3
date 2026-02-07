<?php

namespace App\Filament\Resources\Reviews\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ReviewForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Информация о рецензенте')
                    ->schema([
                        Select::make('tour_id')
                            ->label('Тур')
                            ->relationship('tour', 'title', fn ($query) => $query->whereNotNull('title')->where('title', '!=', ''))
                            ->searchable()
                            ->preload()
                            ->required()
                            ->columnSpanFull(),

                        TextInput::make('reviewer_name')
                            ->label('Имя рецензента')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('reviewer_email')
                            ->label('Email')
                            ->email()
                            ->maxLength(255),

                        TextInput::make('reviewer_location')
                            ->label('Местоположение')
                            ->maxLength(255)
                            ->helperText('Например: United States, France'),

                        Select::make('booking_id')
                            ->label('Связанное бронирование')
                            ->relationship('booking', 'reference')
                            ->searchable()
                            ->preload()
                            ->helperText('Необязательно: привяжите к существующему бронированию'),
                    ])
                    ->columns(2),

                Section::make('Содержание отзыва')
                    ->schema([
                        Select::make('rating')
                            ->label('Рейтинг')
                            ->options([
                                1 => '⭐ 1 - Плохо',
                                2 => '⭐⭐ 2 - Удовлетворительно',
                                3 => '⭐⭐⭐ 3 - Хорошо',
                                4 => '⭐⭐⭐⭐ 4 - Очень хорошо',
                                5 => '⭐⭐⭐⭐⭐ 5 - Отлично',
                            ])
                            ->required()
                            ->native(false)
                            ->columnSpanFull(),

                        TextInput::make('title')
                            ->label('Заголовок')
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Textarea::make('content')
                            ->label('Содержание отзыва')
                            ->required()
                            ->rows(6)
                            ->columnSpanFull(),
                    ]),

                Section::make('Метаданные')
                    ->schema([
                        Select::make('source')
                            ->label('Источник')
                            ->options([
                                'website' => 'Website',
                                'tripadvisor' => 'TripAdvisor',
                                'google' => 'Google Reviews',
                                'booking' => 'Booking.com',
                            ])
                            ->default('website')
                            ->required(),

                        Toggle::make('is_verified')
                            ->label('Верифицирован')
                            ->helperText('Отметьте, если рецензент действительно купил тур')
                            ->default(false),

                        Toggle::make('is_approved')
                            ->label('Одобрен для публикации')
                            ->helperText('Только одобренные отзывы видны на сайте')
                            ->default(false),
                    ])
                    ->columns(3),
            ]);
    }
}
