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
                Action::make('translate')
                    ->label('Translate')
                    ->icon('heroicon-o-language')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Translate Tour with AI')
                    ->modalDescription(fn ($record) => 'Translate "' . $record->title . '" to all active languages using AI')
                    ->form([
                        Select::make('languages')
                            ->label('Target Languages')
                            ->options(fn () => \App\Models\Language::where('is_active', true)
                                ->where('code', '!=', 'en')
                                ->pluck('native_name', 'code'))
                            ->multiple()
                            ->default(fn () => \App\Models\Language::where('is_active', true)
                                ->where('code', '!=', 'en')
                                ->pluck('code')->toArray())
                            ->required(),
                        \Filament\Forms\Components\Toggle::make('force')
                            ->label('Force Re-translate')
                            ->helperText('Re-translate even if translations already exist')
                            ->default(false),
                    ])
                    ->action(function ($record, array $data): void {
                        $translationService = app(\App\Services\TourTranslationService::class);

                        try {
                            $results = $translationService->translateTour($record, $data['languages'], $data['force']);

                            $successCount = 0;
                            $failCount = 0;

                            foreach ($results as $lang => $result) {
                                if ($result['success']) {
                                    $successCount++;
                                } else {
                                    $failCount++;
                                }
                            }

                            if ($successCount > 0) {
                                Notification::make()
                                    ->title('Translation completed!')
                                    ->body("Successfully translated to {$successCount} language(s).")
                                    ->success()
                                    ->send();
                            }

                            if ($failCount > 0) {
                                Notification::make()
                                    ->title('Some translations failed')
                                    ->body("{$failCount} language(s) failed to translate. Check logs for details.")
                                    ->warning()
                                    ->send();
                            }
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Translation failed')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
                Action::make('view_frontend')
                    ->label('View Frontend')
                    ->icon(Heroicon::OutlinedEye)
                    ->color('info')
                    ->url(fn ($record) => '/tours/' . $record->slug)
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

                    BulkAction::make('translate_bulk')
                        ->label('Translate Selected')
                        ->icon('heroicon-o-language')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->modalHeading('Bulk Translate Tours with AI')
                        ->modalDescription(fn (Collection $records) => 'Translate ' . $records->count() . ' selected tour(s) using AI')
                        ->form([
                            Select::make('languages')
                                ->label('Target Languages')
                                ->options(fn () => \App\Models\Language::where('is_active', true)
                                    ->where('code', '!=', 'en')
                                    ->pluck('native_name', 'code'))
                                ->multiple()
                                ->default(fn () => \App\Models\Language::where('is_active', true)
                                    ->where('code', '!=', 'en')
                                    ->pluck('code')->toArray())
                                ->required(),
                            \Filament\Forms\Components\Toggle::make('force')
                                ->label('Force Re-translate')
                                ->helperText('Re-translate even if translations already exist')
                                ->default(false),
                        ])
                        ->action(function (Collection $records, array $data): void {
                            $translationService = app(\App\Services\TourTranslationService::class);
                            $totalSuccess = 0;
                            $totalFail = 0;

                            foreach ($records as $record) {
                                try {
                                    $results = $translationService->translateTour($record, $data['languages'], $data['force']);

                                    foreach ($results as $lang => $result) {
                                        if ($result['success']) {
                                            $totalSuccess++;
                                        } else {
                                            $totalFail++;
                                        }
                                    }
                                } catch (\Exception $e) {
                                    $totalFail += count($data['languages']);
                                }
                            }

                            if ($totalSuccess > 0) {
                                Notification::make()
                                    ->title('Bulk translation completed!')
                                    ->body("Successfully completed {$totalSuccess} translation(s) for " . $records->count() . " tour(s).")
                                    ->success()
                                    ->send();
                            }

                            if ($totalFail > 0) {
                                Notification::make()
                                    ->title('Some translations failed')
                                    ->body("{$totalFail} translation(s) failed. Check logs for details.")
                                    ->warning()
                                    ->send();
                            }
                        })
                        ->deselectRecordsAfterCompletion(),

                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
