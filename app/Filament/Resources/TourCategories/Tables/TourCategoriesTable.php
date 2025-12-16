<?php

namespace App\Filament\Resources\TourCategories\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class TourCategoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name.en')
                    ->label('Name')
                    ->searchable()
                    ->sortable()
                    ->getStateUsing(fn ($record) => $record->name['en'] ?? 'Untitled'),

                TextColumn::make('slug')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Slug copied!')
                    ->badge()
                    ->color('gray'),

                ImageColumn::make('image_path')
                    ->disk('public')
                    ->label('Card Image')
                    ->circular()
                    ->defaultImageUrl(fn () => asset('images/placeholder-category.png'))
                    ->tooltip('Homepage card background'),

                ImageColumn::make('hero_image')
                    ->disk('public')
                    ->label('Hero Image')
                    ->circular()
                    ->defaultImageUrl(fn () => asset('images/placeholder-hero.png'))
                    ->tooltip('Landing page hero'),

                TextColumn::make('icon')
                    ->label('Icon')
                    ->badge()
                    ->color('info')
                    ->formatStateUsing(fn ($state) => $state ?: 'No icon')
                    ->limit(20),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),

                IconColumn::make('show_on_homepage')
                    ->label('Homepage')
                    ->boolean()
                    ->tooltip(fn ($state) => $state ? 'Shown on homepage' : 'Not shown on homepage'),

                TextColumn::make('display_order')
                    ->label('Order')
                    ->numeric()
                    ->sortable()
                    ->alignCenter(),

                TextColumn::make('tours_count')
                    ->label('Tours')
                    ->counts('tours')
                    ->badge()
                    ->color('success'),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('display_order', 'asc');
    }
}
