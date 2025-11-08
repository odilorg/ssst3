<?php

namespace App\Filament\Resources\Contacts\Tables;

use App\Models\Contact;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Actions\DeleteAction;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ContactsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('reference')
                    ->label('Reference')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('Reference copied')
                    ->weight(FontWeight::Bold)
                    ->color('primary'),

                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::SemiBold),

                TextColumn::make('email')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->icon('heroicon-m-envelope'),

                TextColumn::make('phone')
                    ->searchable()
                    ->toggleable()
                    ->icon('heroicon-m-phone'),

                TextColumn::make('message')
                    ->limit(50)
                    ->searchable()
                    ->toggleable()
                    ->wrap(),

                BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'new',
                        'success' => 'replied',
                        'gray' => 'closed',
                    ])
                    ->sortable(),

                TextColumn::make('created_at')
                    ->dateTime('M j, Y H:i')
                    ->sortable()
                    ->toggleable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'new' => 'New',
                        'replied' => 'Replied',
                        'closed' => 'Closed',
                    ]),
            ])
            ->actions([
                ViewAction::make(),

                Action::make('markAsReplied')
                    ->label('Mark as Replied')
                    ->icon('heroicon-m-check-circle')
                    ->color('success')
                    ->visible(fn (Contact $record): bool => $record->status !== 'replied')
                    ->requiresConfirmation()
                    ->action(fn (Contact $record) => $record->markAsReplied(auth()->user())),

                Action::make('close')
                    ->label('Close')
                    ->icon('heroicon-m-x-circle')
                    ->color('gray')
                    ->visible(fn (Contact $record): bool => $record->status !== 'closed')
                    ->requiresConfirmation()
                    ->action(function (Contact $record) {
                        $record->update(['status' => 'closed']);
                    }),

                DeleteAction::make(),
            ])
            ->bulkActions([]);
    }
}
