<?php

namespace App\Filament\Resources\Tours\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ReviewsRelationManager extends RelationManager
{
    protected static string $relationship = 'reviews';

    protected static ?string $title = 'Отзывы';

    protected static ?string $modelLabel = 'Отзыв';

    protected static ?string $pluralModelLabel = 'Отзывы';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\TextInput::make('reviewer_name')
                    ->label('Имя рецензента')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('reviewer_email')
                    ->label('Email')
                    ->email()
                    ->maxLength(255),

                Forms\Components\TextInput::make('reviewer_location')
                    ->label('Местоположение')
                    ->maxLength(255)
                    ->helperText('Например: United States, France'),

                Forms\Components\Select::make('rating')
                    ->label('Рейтинг')
                    ->options([
                        1 => '1 - Плохо',
                        2 => '2 - Удовлетворительно',
                        3 => '3 - Хорошо',
                        4 => '4 - Очень хорошо',
                        5 => '5 - Отлично',
                    ])
                    ->required()
                    ->native(false),

                Forms\Components\TextInput::make('title')
                    ->label('Заголовок')
                    ->maxLength(255)
                    ->columnSpanFull(),

                Forms\Components\Textarea::make('content')
                    ->label('Содержание отзыва')
                    ->required()
                    ->rows(5)
                    ->columnSpanFull(),

                Forms\Components\Select::make('source')
                    ->label('Источник')
                    ->options([
                        'website' => 'Website',
                        'tripadvisor' => 'TripAdvisor',
                        'google' => 'Google Reviews',
                        'booking' => 'Booking.com',
                    ])
                    ->default('website')
                    ->required(),

                Forms\Components\Select::make('booking_id')
                    ->label('Связанное бронирование')
                    ->relationship('booking', 'reference')
                    ->searchable()
                    ->preload()
                    ->helperText('Необязательно: привяжите к существующему бронированию'),

                Forms\Components\Toggle::make('is_verified')
                    ->label('Верифицирован')
                    ->helperText('Отметьте, если рецензент действительно купил тур')
                    ->default(false),

                Forms\Components\Toggle::make('is_approved')
                    ->label('Одобрен')
                    ->helperText('Только одобренные отзывы видны публично')
                    ->default(false),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                Tables\Columns\TextColumn::make('reviewer_name')
                    ->label('Рецензент')
                    ->searchable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('reviewer_location')
                    ->label('Откуда')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('rating')
                    ->label('Рейтинг')
                    ->badge()
                    ->color(fn ($state) => match(true) {
                        $state >= 4 => 'success',
                        $state >= 3 => 'warning',
                        default => 'danger',
                    })
                    ->formatStateUsing(fn ($state) => str_repeat('⭐', $state))
                    ->sortable(),

                Tables\Columns\TextColumn::make('title')
                    ->label('Заголовок')
                    ->limit(30)
                    ->searchable()
                    ->wrap(),

                Tables\Columns\TextColumn::make('content')
                    ->label('Содержание')
                    ->limit(40)
                    ->wrap()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('source')
                    ->label('Источник')
                    ->badge()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\IconColumn::make('is_verified')
                    ->label('Верифицирован')
                    ->boolean()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\IconColumn::make('is_approved')
                    ->label('Одобрен')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Дата')
                    ->date()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_approved')
                    ->label('Одобренные')
                    ->boolean()
                    ->trueLabel('Только одобренные')
                    ->falseLabel('Только не одобренные')
                    ->native(false),

                Tables\Filters\TernaryFilter::make('is_verified')
                    ->label('Верифицированные')
                    ->boolean()
                    ->native(false),

                Tables\Filters\SelectFilter::make('rating')
                    ->label('Рейтинг')
                    ->options([
                        5 => '5 звёзд',
                        4 => '4 звезды',
                        3 => '3 звезды',
                        2 => '2 звезды',
                        1 => '1 звезда',
                    ]),

                Tables\Filters\SelectFilter::make('source')
                    ->label('Источник')
                    ->options([
                        'website' => 'Website',
                        'tripadvisor' => 'TripAdvisor',
                        'google' => 'Google Reviews',
                        'booking' => 'Booking.com',
                    ]),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Добавить отзыв'),
            ])
            ->actions([
                ActionGroup::make([
                    Action::make('approve')
                        ->label('Одобрить')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(fn ($record) => $record->update(['is_approved' => true]))
                        ->visible(fn ($record) => !$record->is_approved),

                    Action::make('unapprove')
                        ->label('Отменить одобрение')
                        ->icon('heroicon-o-x-circle')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->action(fn ($record) => $record->update(['is_approved' => false]))
                        ->visible(fn ($record) => $record->is_approved),

                    EditAction::make(),
                    DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    BulkAction::make('approve')
                        ->label('Одобрить выбранные')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(fn ($records) => $records->each->update(['is_approved' => true])),

                    BulkAction::make('unapprove')
                        ->label('Отменить одобрение')
                        ->icon('heroicon-o-x-circle')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->action(fn ($records) => $records->each->update(['is_approved' => false])),

                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
