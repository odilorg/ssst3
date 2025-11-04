<?php

namespace App\Filament\Resources\Cities\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class CitiesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('City Name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('tagline')
                    ->label('Tagline')
                    ->limit(40)
                    ->searchable(),

                ImageColumn::make('featured_image')
                    ->label('Image')
                    ->square()
                    ->size(60),

                TextColumn::make('tour_count_cache')
                    ->label('Tours')
                    ->badge()
                    ->color('primary')
                    ->alignCenter()
                    ->sortable(),

                IconColumn::make('is_featured')
                    ->label('Featured')
                    ->boolean()
                    ->alignCenter()
                    ->sortable(),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->alignCenter()
                    ->sortable(),

                TextColumn::make('display_order')
                    ->label('Order')
                    ->sortable()
                    ->alignCenter(),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('display_order', 'asc')
            ->filters([
                TernaryFilter::make('is_featured')
                    ->label('Featured')
                    ->placeholder('All cities')
                    ->trueLabel('Featured only')
                    ->falseLabel('Not featured'),

                TernaryFilter::make('is_active')
                    ->label('Status')
                    ->placeholder('All cities')
                    ->trueLabel('Active only')
                    ->falseLabel('Inactive only'),
            ])
            ->recordActions([
                Action::make('update_tour_count')
                    ->label('Update Tour Count')
                    ->icon('heroicon-o-arrow-path')
                    ->color('info')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->updateTourCount();
                    })
                    ->successNotificationTitle('Tour count updated'),

                EditAction::make(),

                Action::make('view_tours')
                    ->label('View Tours')
                    ->icon('heroicon-o-map')
                    ->color('primary')
                    ->url(fn ($record) => route('filament.admin.resources.tours.index', [
                        'tableFilters[city_id][value]' => $record->id,
                    ]))
                    ->openUrlInNewTab()
                    ->visible(fn ($record) => $record->tour_count > 0),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
