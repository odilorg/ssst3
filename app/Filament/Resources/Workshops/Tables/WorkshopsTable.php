<?php

namespace App\Filament\Resources\Workshops\Tables;

use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class WorkshopsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('hero_image')
                    ->label('Image')
                    ->circular()
                    ->size(50),
                
                TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record) => $record->subtitle),
                
                TextColumn::make('city.name')
                    ->label('City')
                    ->sortable()
                    ->badge(),
                
                TextColumn::make('craft_type')
                    ->label('Craft')
                    ->badge()
                    ->color('success'),
                
                TextColumn::make('master_name')
                    ->label('Master')
                    ->searchable()
                    ->toggleable(),
                
                TextColumn::make('duration_display')
                    ->label('Duration')
                    ->toggleable(),
                
                TextColumn::make('price_from')
                    ->label('Price')
                    ->money('USD')
                    ->sortable(),
                
                TextColumn::make('rating')
                    ->label('Rating')
                    ->formatStateUsing(fn ($state) => $state ? number_format($state, 1) . ' ⭐' : '-')
                    ->sortable(),
                
                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
                
                IconColumn::make('is_featured')
                    ->label('Featured')
                    ->boolean(),
                
                TextColumn::make('sort_order')
                    ->label('Order')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime('M j, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('sort_order')
            ->filters([
                SelectFilter::make('city')
                    ->relationship('city', 'name'),
                
                TernaryFilter::make('is_active')
                    ->label('Active'),
                
                TernaryFilter::make('is_featured')
                    ->label('Featured'),
            ])
            ->actions([
                ViewAction::make()
                    ->url(fn ($record) => 'https://staging.jahongir-travel.uz/workshops/' . $record->slug)
                    ->openUrlInNewTab()
                    ->icon('heroicon-o-eye')
                    ->label('View Page'),
                EditAction::make(),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ]);
    }
}
