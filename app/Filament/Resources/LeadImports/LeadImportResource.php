<?php

namespace App\Filament\Resources\LeadImports;

use App\Filament\Resources\LeadImports\Pages\ListLeadImports;
use App\Filament\Resources\LeadImports\Pages\ViewLeadImport;
use App\Models\LeadImport;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class LeadImportResource extends Resource
{
    protected static ?string $model = LeadImport::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationGroup = 'Lead Management';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationLabel = 'Import History';

    protected static ?string $modelLabel = 'Import';

    protected static ?string $pluralModelLabel = 'Imports';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Import Details')
                    ->schema([
                        Forms\Components\TextInput::make('filename')
                            ->label('Filename')
                            ->disabled(),

                        Forms\Components\Select::make('user_id')
                            ->label('Imported By')
                            ->relationship('user', 'name')
                            ->disabled(),

                        Forms\Components\TextInput::make('status')
                            ->badge()
                            ->disabled(),

                        Forms\Components\DateTimePicker::make('started_at')
                            ->label('Started At')
                            ->disabled(),

                        Forms\Components\DateTimePicker::make('completed_at')
                            ->label('Completed At')
                            ->disabled(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Statistics')
                    ->schema([
                        Forms\Components\TextInput::make('total_rows')
                            ->label('Total Rows')
                            ->disabled(),

                        Forms\Components\TextInput::make('created_count')
                            ->label('Created')
                            ->disabled(),

                        Forms\Components\TextInput::make('updated_count')
                            ->label('Updated')
                            ->disabled(),

                        Forms\Components\TextInput::make('skipped_count')
                            ->label('Skipped')
                            ->disabled(),

                        Forms\Components\TextInput::make('failed_count')
                            ->label('Failed')
                            ->disabled(),

                        Forms\Components\Placeholder::make('success_rate')
                            ->label('Success Rate')
                            ->content(fn (LeadImport $record) => round($record->success_rate, 1) . '%'),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Configuration')
                    ->schema([
                        Forms\Components\Textarea::make('field_mapping')
                            ->label('Field Mapping')
                            ->disabled()
                            ->formatStateUsing(fn ($state) => json_encode($state, JSON_PRETTY_PRINT)),

                        Forms\Components\Textarea::make('error_log')
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
