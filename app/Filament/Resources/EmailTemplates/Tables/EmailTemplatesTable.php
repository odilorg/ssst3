<?php

namespace App\Filament\Resources\EmailTemplates\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

class EmailTemplatesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Template Name')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold),

                TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'initial_contact' => 'success',
                        'follow_up_1' => 'warning',
                        'follow_up_2' => 'warning',
                        'follow_up_3' => 'danger',
                        'proposal' => 'primary',
                        'custom' => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'initial_contact' => 'Initial Contact',
                        'follow_up_1' => 'Follow-up #1',
                        'follow_up_2' => 'Follow-up #2',
                        'follow_up_3' => 'Follow-up #3',
                        'proposal' => 'Proposal',
                        'custom' => 'Custom',
                    })
                    ->sortable(),

                TextColumn::make('subject')
                    ->label('Subject')
                    ->searchable()
                    ->limit(50)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        return strlen($state) > 50 ? $state : null;
                    }),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->sortable(),

                TextColumn::make('times_used')
                    ->label('Times Used')
                    ->numeric()
                    ->sortable()
                    ->alignCenter()
                    ->color(fn ($state) => $state > 0 ? 'success' : 'gray'),

                TextColumn::make('last_used_at')
                    ->label('Last Used')
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->placeholder('Never')
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('Type')
                    ->options([
                        'initial_contact' => 'Initial Contact',
                        'follow_up_1' => 'Follow-up #1',
                        'follow_up_2' => 'Follow-up #2',
                        'follow_up_3' => 'Follow-up #3',
                        'proposal' => 'Proposal',
                        'custom' => 'Custom',
                    ]),

                TernaryFilter::make('is_active')
                    ->label('Active Status')
                    ->placeholder('All templates')
                    ->trueLabel('Active only')
                    ->falseLabel('Inactive only'),
            ])
            ->recordActions([
                EditAction::make(),

                Action::make('preview')
                    ->label('Preview')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->modalHeading(fn ($record) => 'Preview: ' . $record->name)
                    ->modalContent(fn ($record) => view('filament.email-preview', [
                        'template' => $record,
                        'sampleData' => [
                            'company_name' => 'Sample Tour Company Ltd',
                            'contact_name' => 'John Doe',
                            'country' => 'USA',
                            'website' => 'https://example.com',
                            'sender_name' => auth()->user()->name ?? 'Your Name',
                            'sender_email' => config('mail.from.address', 'your@email.com'),
                            'sender_company' => config('mail.from.name', 'Your Company'),
                        ]
                    ]))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close'),

                Action::make('duplicate')
                    ->label('Duplicate')
                    ->icon('heroicon-o-document-duplicate')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->replicate()->save();
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),

                    Action::make('bulk_activate')
                        ->label('Activate')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(function ($records) {
                            foreach ($records as $record) {
                                $record->update(['is_active' => true]);
                            }
                        }),

                    Action::make('bulk_deactivate')
                        ->label('Deactivate')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->action(function ($records) {
                            foreach ($records as $record) {
                                $record->update(['is_active' => false]);
                            }
                        }),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
