<?php

namespace App\Filament\Resources\TourDepartures\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class TourDeparturesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tour.title')
                    ->label('Tour')
                    ->searchable()
                    ->sortable()
                    ->limit(40)
                    ->tooltip(fn ($record) => $record->tour->title),

                TextColumn::make('date_range')
                    ->label('Dates')
                    ->sortable(['start_date'])
                    ->getStateUsing(fn ($record) =>
                        $record->start_date->format('M d') . ' - ' .
                        $record->end_date->format('M d, Y')
                    )
                    ->description(fn ($record) =>
                        $record->start_date->diffInDays($record->end_date) + 1 . ' days'
                    ),

                BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'primary' => 'open',
                        'success' => 'guaranteed',
                        'danger' => 'full',
                        'secondary' => 'completed',
                        'warning' => 'cancelled',
                    ])
                    ->icons([
                        'heroicon-o-calendar' => 'open',
                        'heroicon-o-check-circle' => 'guaranteed',
                        'heroicon-o-x-circle' => 'full',
                        'heroicon-o-check-badge' => 'completed',
                        'heroicon-o-ban' => 'cancelled',
                    ])
                    ->sortable(),

                TextColumn::make('capacity')
                    ->label('Capacity')
                    ->getStateUsing(fn ($record) =>
                        $record->booked_pax . ' / ' . $record->max_pax
                    )
                    ->description(fn ($record) =>
                        $record->spots_remaining . ' spots left'
                    )
                    ->color(fn ($record) => match(true) {
                        $record->is_sold_out => 'danger',
                        $record->is_filling_fast => 'warning',
                        default => 'success'
                    }),

                TextColumn::make('departure_type')
                    ->label('Type')
                    ->badge()
                    ->colors([
                        'info' => 'group',
                        'secondary' => 'private',
                    ])
                    ->sortable(),

                TextColumn::make('price_per_person')
                    ->label('Price')
                    ->money('USD')
                    ->sortable()
                    ->toggleable()
                    ->placeholder('Uses tour pricing'),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime('M d, Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'open' => 'Open',
                        'guaranteed' => 'Guaranteed',
                        'full' => 'Full',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ])
                    ->multiple(),

                SelectFilter::make('departure_type')
                    ->options([
                        'group' => 'Group',
                        'private' => 'Private',
                    ]),

                SelectFilter::make('tour')
                    ->relationship('tour', 'title')
                    ->searchable()
                    ->preload(),
            ])
            ->defaultSort('start_date', 'asc')
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
