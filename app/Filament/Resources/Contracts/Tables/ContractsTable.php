<?php

namespace App\Filament\Resources\Contracts\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ContractsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('contract_number')
                    ->label('Contract Number')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('title')
                    ->label('Title')
                    ->searchable()
                    ->limit(30),
                TextColumn::make('supplier.name')
                    ->label('Supplier')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color(fn ($record) => match($record->supplier_type) {
                        'App\Models\Company' => 'success',
                        'App\Models\Guide' => 'info',
                        'App\Models\Driver' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($record) =>
                        $record->supplier?->name . ' (' .
                        match($record->supplier_type) {
                            'App\Models\Company' => 'Company',
                            'App\Models\Guide' => 'Guide',
                            'App\Models\Driver' => 'Driver',
                            default => 'Unknown'
                        } . ')'
                    ),
                TextColumn::make('start_date')
                    ->label('Start Date')
                    ->date()
                    ->sortable(),
                TextColumn::make('end_date')
                    ->label('End Date')
                    ->date()
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'active' => 'success',
                        'expired' => 'warning',
                        'terminated' => 'danger',
                    }),
                TextColumn::make('days_remaining')
                    ->label('Days Remaining')
                    ->getStateUsing(function ($record) {
                        if ($record->status === 'active' && $record->end_date >= now()) {
                            return $record->end_date->diffInDays(now()) . ' days';
                        }
                        return '—';
                    })
                    ->color(fn ($state) => str_contains($state, 'days') && (int) $state < 30 ? 'warning' : null),
                TextColumn::make('signed_by')
                    ->label('Signed By')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('contractServices_count')
                    ->counts('contractServices')
                    ->label('Services')
                    ->badge()
                    ->color('info'),
                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'active' => 'Active',
                        'expired' => 'Expired',
                        'terminated' => 'Terminated',
                    ]),
                SelectFilter::make('supplier_type')
                    ->label('Supplier Type')
                    ->options([
                        'App\Models\Company' => 'Company',
                        'App\Models\Guide' => 'Individual Guide',
                        'App\Models\Driver' => 'Individual Driver',
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
