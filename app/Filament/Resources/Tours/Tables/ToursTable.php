<?php

namespace App\Filament\Resources\Tours\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class ToursTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Название тура')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('duration_days')
                    ->label('Продолжительность')
                    ->suffix(' дн.')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('short_description')
                    ->label('Краткое описание')
                    ->searchable()
                    ->limit(50),
                IconColumn::make('is_active')
                    ->label('Активный')
                    ->boolean()
                    ->sortable(),
                TextColumn::make('bookings_count')
                    ->label('Количество бронирований')
                    ->counts('bookings')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Создано')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Обновлено')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                Action::make('view_frontend')
                    ->label('View Frontend')
                    ->icon(Heroicon::OutlinedEye)
                    ->color('info')
                    ->url(fn ($record) => '/tour-details.html?slug=' . $record->slug)
                    ->openUrlInNewTab(),
                ViewAction::make()
                    ->label('View Formatted')
                    ->icon('heroicon-o-document-text'),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('assign_categories')
                        ->label('Assign Categories')
                        ->icon(Heroicon::OutlinedTag)
                        ->color('success')
                        ->form([
                            Select::make('categories')
                                ->label('Select Categories')
                                ->options(fn () => \App\Models\TourCategory::where('is_active', true)
                                    ->orderBy('display_order')
                                    ->pluck('name', 'id'))
                                ->multiple()
                                ->searchable()
                                ->required()
                                ->helperText('Select one or more categories to assign to the selected tours'),

                            Select::make('assignment_mode')
                                ->label('Assignment Mode')
                                ->options([
                                    'replace' => 'Replace existing categories (remove old, add new)',
                                    'add' => 'Add to existing categories (keep old, add new)',
                                ])
                                ->default('add')
                                ->required()
                                ->helperText('Choose how to handle existing category assignments'),
                        ])
                        ->action(function (Collection $records, array $data): void {
                            $categoryIds = $data['categories'];
                            $mode = $data['assignment_mode'];

                            foreach ($records as $tour) {
                                if ($mode === 'replace') {
                                    // Replace: sync will remove old and add new
                                    $tour->categories()->sync($categoryIds);
                                } else {
                                    // Add: attach without detaching existing
                                    $tour->categories()->syncWithoutDetaching($categoryIds);
                                }
                            }

                            Notification::make()
                                ->title('Categories assigned successfully')
                                ->body(count($records) . ' tour(s) updated with selected categories.')
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),

                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
