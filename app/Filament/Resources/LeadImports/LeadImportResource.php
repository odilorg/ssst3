<?php

namespace App\Filament\Resources\LeadImports;

use App\Filament\Resources\LeadImports\Pages\ListLeadImports;
use App\Filament\Resources\LeadImports\Pages\ViewLeadImport;
use App\Models\LeadImport;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Components\DateTimePicker;
use Filament\Schemas\Components\Placeholder;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Select;
use Filament\Schemas\Components\Textarea;
use Filament\Schemas\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;

class LeadImportResource extends Resource
{
    protected static ?string $model = LeadImport::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    public static function getNavigationGroup(): ?string
    {
        return 'Lead Management';
    }

    public static function getNavigationSort(): ?int
    {
        return 3;
    }

    public static function getNavigationLabel(): string
    {
        return 'Import History';
    }

    public static function getModelLabel(): string
    {
        return 'Import';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Imports';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Import Details')
                    ->schema([
                        TextInput::make('filename')
                            ->label('Filename')
                            ->disabled(),

                        Select::make('user_id')
                            ->label('Imported By')
                            ->relationship('user', 'name')
                            ->disabled(),

                        TextInput::make('status')
                            ->disabled(),

                        DateTimePicker::make('started_at')
                            ->label('Started At')
                            ->disabled(),

                        DateTimePicker::make('completed_at')
                            ->label('Completed At')
                            ->disabled(),
                    ])
                    ->columns(2),

                Section::make('Statistics')
                    ->schema([
                        TextInput::make('total_rows')
                            ->label('Total Rows')
                            ->disabled(),

                        TextInput::make('created_count')
                            ->label('Created')
                            ->disabled(),

                        TextInput::make('updated_count')
                            ->label('Updated')
                            ->disabled(),

                        TextInput::make('skipped_count')
                            ->label('Skipped')
                            ->disabled(),

                        TextInput::make('failed_count')
                            ->label('Failed')
                            ->disabled(),

                        Placeholder::make('success_rate')
                            ->label('Success Rate')
                            ->content(fn (LeadImport $record) => round($record->success_rate, 1) . '%'),
                    ])
                    ->columns(3),

                Section::make('Configuration')
                    ->schema([
                        Textarea::make('field_mapping')
                            ->label('Field Mapping')
                            ->disabled()
                            ->formatStateUsing(fn ($state) => json_encode($state, JSON_PRETTY_PRINT)),

                        Textarea::make('error_log')
                            ->label('Error Log')
                            ->disabled()
                            ->formatStateUsing(fn ($state) => $state ? json_encode(json_decode($state), JSON_PRETTY_PRINT) : 'No errors'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('filename')
                    ->label('File')
                    ->searchable()
                    ->sortable()
                    ->description(fn (LeadImport $record) => "Imported by {$record->user->name}")
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->colors([
                        'warning' => 'pending',
                        'info' => 'processing',
                        'success' => 'completed',
                        'danger' => 'failed',
                    ])
                    ->sortable(),

                Tables\Columns\TextColumn::make('total_rows')
                    ->label('Total')
                    ->sortable()
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('created_count')
                    ->label('Created')
                    ->sortable()
                    ->alignCenter()
                    ->color('success'),

                Tables\Columns\TextColumn::make('updated_count')
                    ->label('Updated')
                    ->sortable()
                    ->alignCenter()
                    ->color('info'),

                Tables\Columns\TextColumn::make('skipped_count')
                    ->label('Skipped')
                    ->sortable()
                    ->alignCenter()
                    ->color('warning'),

                Tables\Columns\TextColumn::make('failed_count')
                    ->label('Failed')
                    ->sortable()
                    ->alignCenter()
                    ->color('danger'),

                Tables\Columns\TextColumn::make('success_rate')
                    ->label('Success')
                    ->suffix('%')
                    ->sortable()
                    ->alignCenter()
                    ->color(fn (LeadImport $record) => $record->success_rate >= 90 ? 'success' : ($record->success_rate >= 70 ? 'warning' : 'danger')),

                Tables\Columns\TextColumn::make('formatted_duration')
                    ->label('Duration')
                    ->sortable(query: function ($query, $direction) {
                        return $query->orderByRaw("TIMESTAMPDIFF(SECOND, started_at, completed_at) {$direction}");
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Imported At')
                    ->dateTime('M j, Y g:i A')
                    ->sortable()
                    ->toggleable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'processing' => 'Processing',
                        'completed' => 'Completed',
                        'failed' => 'Failed',
                    ]),

                Tables\Filters\SelectFilter::make('user_id')
                    ->label('Imported By')
                    ->relationship('user', 'name'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('No imports yet')
            ->emptyStateDescription('Import your first CSV file to see the history here.')
            ->emptyStateIcon('heroicon-o-clipboard-document-list');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLeadImports::route('/'),
            'view' => ViewLeadImport::route('/{record}'),
        ];
    }
}
