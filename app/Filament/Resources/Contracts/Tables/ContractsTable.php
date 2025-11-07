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
                    ->label('Номер договора')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->icon('heroicon-o-document-text')
                    ->copyable()
                    ->copyMessage('Номер скопирован')
                    ->copyMessageDuration(1500),

                TextColumn::make('title')
                    ->label('Название')
                    ->searchable()
                    ->icon('heroicon-o-pencil-square')
                    ->limit(30),

                TextColumn::make('supplier.name')
                    ->label('Поставщик')
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
                            'App\Models\Company' => 'Компания',
                            'App\Models\Guide' => 'Гид',
                            'App\Models\Driver' => 'Водитель',
                            default => 'Неизвестно'
                        } . ')'
                    ),

                TextColumn::make('status')
                    ->label('Статус')
                    ->badge()
                    ->sortable()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'active' => 'success',
                        'expired' => 'warning',
                        'terminated' => 'danger',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'draft' => 'Черновик',
                        'active' => 'Активный',
                        'expired' => 'Истёк',
                        'terminated' => 'Расторгнут',
                        default => $state,
                    }),

                TextColumn::make('start_date')
                    ->label('Дата начала')
                    ->date('d.m.Y')
                    ->sortable()
                    ->icon('heroicon-o-calendar'),

                TextColumn::make('end_date')
                    ->label('Дата окончания')
                    ->date('d.m.Y')
                    ->sortable()
                    ->icon('heroicon-o-calendar')
                    ->color(fn ($record) => $record->end_date && $record->end_date->isPast() ? 'danger' : null),

                TextColumn::make('days_remaining')
                    ->label('Осталось дней')
                    ->getStateUsing(function ($record) {
                        if ($record->status === 'active' && $record->end_date >= now()) {
                            $days = $record->end_date->diffInDays(now());
                            return $days . ' дн.';
                        }
                        return '—';
                    })
                    ->badge()
                    ->color(fn ($state) => {
                        if ($state === '—') return 'gray';
                        $days = (int) $state;
                        if ($days < 30) return 'danger';
                        if ($days < 90) return 'warning';
                        return 'success';
                    }),

                TextColumn::make('contractServices_count')
                    ->counts('contractServices')
                    ->label('Услуги')
                    ->badge()
                    ->icon('heroicon-o-squares-2x2')
                    ->color('info')
                    ->sortable()
                    ->formatStateUsing(fn ($state) => $state > 0 ? $state : '—'),

                TextColumn::make('signed_by')
                    ->label('Подписант')
                    ->searchable()
                    ->icon('heroicon-o-user-circle')
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Создано')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Статус')
                    ->options([
                        'draft' => 'Черновик',
                        'active' => 'Активный',
                        'expired' => 'Истёк',
                        'terminated' => 'Расторгнут',
                    ])
                    ->multiple()
                    ->indicator('Статус'),

                SelectFilter::make('supplier_type')
                    ->label('Тип поставщика')
                    ->options([
                        'App\Models\Company' => 'Компания',
                        'App\Models\Guide' => 'Гид',
                        'App\Models\Driver' => 'Водитель',
                    ])
                    ->multiple()
                    ->indicator('Тип поставщика'),
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
            ->defaultSort('created_at', 'desc')
            ->striped();
    }
}
