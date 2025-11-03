<?php

namespace App\Filament\Resources\Reviews\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables;
use Filament\Tables\Table;

class ReviewsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tour.title')
                    ->label('Тур')
                    ->searchable()
                    ->sortable()
                    ->limit(30)
                    ->url(fn ($record) => $record->tour ? route('filament.admin.resources.tours.tours.edit', ['record' => $record->tour_id]) : null)
                    ->color('primary'),

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
                    ->formatStateUsing(fn ($state) => str_repeat('⭐', $state) . " ({$state})")
                    ->sortable(),

                Tables\Columns\TextColumn::make('title')
                    ->label('Заголовок')
                    ->limit(40)
                    ->searchable()
                    ->wrap(),

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

                Tables\Columns\TextColumn::make('spam_score')
                    ->label('Спам-балл')
                    ->sortable()
                    ->badge()
                    ->color(fn ($state) => match (true) {
                        $state >= 70 => 'danger',
                        $state >= 50 => 'warning',
                        $state >= 30 => 'info',
                        default => 'success',
                    })
                    ->formatStateUsing(fn ($state) => $state . '/100')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('review_ip')
                    ->label('IP адрес')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('review_user_agent')
                    ->label('User Agent')
                    ->limit(50)
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Дата')
                    ->date()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_approved')
                    ->label('Статус одобрения')
                    ->placeholder('Все отзывы')
                    ->trueLabel('Только одобренные')
                    ->falseLabel('Только не одобренные')
                    ->native(false),

                Tables\Filters\TernaryFilter::make('is_verified')
                    ->label('Верификация')
                    ->placeholder('Все')
                    ->trueLabel('Только верифицированные')
                    ->falseLabel('Только не верифицированные')
                    ->native(false),

                Tables\Filters\SelectFilter::make('rating')
                    ->label('Рейтинг')
                    ->options([
                        5 => '⭐⭐⭐⭐⭐ 5 звёзд',
                        4 => '⭐⭐⭐⭐ 4 звезды',
                        3 => '⭐⭐⭐ 3 звезды',
                        2 => '⭐⭐ 2 звезды',
                        1 => '⭐ 1 звезда',
                    ]),

                Tables\Filters\SelectFilter::make('source')
                    ->label('Источник')
                    ->options([
                        'website' => 'Website',
                        'tripadvisor' => 'TripAdvisor',
                        'google' => 'Google Reviews',
                        'booking' => 'Booking.com',
                    ]),

                Tables\Filters\SelectFilter::make('tour_id')
                    ->label('Тур')
                    ->relationship('tour', 'title')
                    ->searchable()
                    ->preload(),

                Tables\Filters\Filter::make('spam_score')
                    ->label('Спам-балл')
                    ->form([
                        \Filament\Forms\Components\Select::make('spam_range')
                            ->label('Диапазон спам-балла')
                            ->options([
                                'low' => 'Низкий (0-29)',
                                'medium' => 'Средний (30-69)',
                                'high' => 'Высокий (70-100)',
                            ])
                            ->placeholder('Все баллы'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when(
                                $data['spam_range'] === 'low',
                                fn ($query) => $query->where('spam_score', '<', 30)
                            )
                            ->when(
                                $data['spam_range'] === 'medium',
                                fn ($query) => $query->whereBetween('spam_score', [30, 69])
                            )
                            ->when(
                                $data['spam_range'] === 'high',
                                fn ($query) => $query->where('spam_score', '>=', 70)
                            );
                    }),
            ])
            ->actions([
                Action::make('approve')
                    ->label('Одобрить')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(fn ($record) => $record->update(['is_approved' => true]))
                    ->visible(fn ($record) => !$record->is_approved)
                    ->successNotificationTitle('Отзыв одобрен'),

                Action::make('unapprove')
                    ->label('Отменить одобрение')
                    ->icon('heroicon-o-x-circle')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->action(fn ($record) => $record->update(['is_approved' => false]))
                    ->visible(fn ($record) => $record->is_approved)
                    ->successNotificationTitle('Одобрение отменено'),

                Action::make('mark_spam')
                    ->label('Пометить как спам')
                    ->icon('heroicon-o-exclamation-triangle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(fn ($record) => $record->markAsSpam())
                    ->successNotificationTitle('Отзыв помечен как спам'),

                EditAction::make(),

                Action::make('view_tour')
                    ->label('Открыть тур')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->color('primary')
                    ->url(fn ($record) => $record->tour ? route('filament.admin.resources.tours.tours.edit', ['record' => $record->tour_id]) : null)
                    ->openUrlInNewTab(),

                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    BulkAction::make('approve')
                        ->label('Одобрить выбранные')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(fn ($records) => $records->each->update(['is_approved' => true]))
                        ->deselectRecordsAfterCompletion()
                        ->successNotificationTitle('Отзывы одобрены'),

                    BulkAction::make('unapprove')
                        ->label('Отменить одобрение')
                        ->icon('heroicon-o-x-circle')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->action(fn ($records) => $records->each->update(['is_approved' => false]))
                        ->deselectRecordsAfterCompletion()
                        ->successNotificationTitle('Одобрение отменено'),

                    BulkAction::make('mark_spam')
                        ->label('Пометить как спам')
                        ->icon('heroicon-o-exclamation-triangle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(fn ($records) => $records->each->markAsSpam())
                        ->deselectRecordsAfterCompletion()
                        ->successNotificationTitle('Отзывы помечены как спам'),

                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
