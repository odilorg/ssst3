<?php

namespace App\Filament\Resources\Contacts\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;

class ContactInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Contact Details')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('reference')
                                    ->label('Reference Number')
                                    ->badge()
                                    ->color('primary')
                                    ->copyable(),

                                TextEntry::make('status')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'new' => 'warning',
                                        'replied' => 'success',
                                        'closed' => 'gray',
                                        default => 'gray',
                                    }),
                            ]),

                        Grid::make(2)
                            ->schema([
                                TextEntry::make('name')
                                    ->icon('heroicon-m-user'),

                                TextEntry::make('email')
                                    ->icon('heroicon-m-envelope')
                                    ->copyable()
                                    ->url(fn ($record) => 'mailto:' . $record->email),
                            ]),

                        Grid::make(2)
                            ->schema([
                                TextEntry::make('phone')
                                    ->icon('heroicon-m-phone')
                                    ->url(fn ($record) => $record->phone ? 'tel:' . $record->phone : null)
                                    ->placeholder('Not provided'),

                                TextEntry::make('created_at')
                                    ->label('Submitted At')
                                    ->dateTime('F j, Y \a\t g:i A')
                                    ->icon('heroicon-m-clock'),
                            ]),

                        TextEntry::make('message')
                            ->columnSpanFull(),
                    ]),

                Section::make('Reply Information')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('replied_at')
                                    ->label('Replied At')
                                    ->dateTime('F j, Y \a\t g:i A')
                                    ->placeholder('Not yet replied'),

                                TextEntry::make('repliedBy.name')
                                    ->label('Replied By')
                                    ->placeholder('Not yet replied'),
                            ]),
                    ])
                    ->visible(fn ($record) => $record->replied_at !== null),

                Section::make('Technical Information')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('ip_address')
                                    ->label('IP Address')
                                    ->icon('heroicon-m-globe-alt'),

                                TextEntry::make('user_agent')
                                    ->label('Browser / Device')
                                    ->limit(50),
                            ]),
                    ])
                    ->collapsed(),
            ]);
    }
}
