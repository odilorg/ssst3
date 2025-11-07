<?php

namespace App\Filament\Resources\TourInquiries\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Grid;
use Filament\Schemas\Schema;

class TourInquiryInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Inquiry Information')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('reference')
                                    ->label('Reference')
                                    ->weight('bold')
                                    ->copyable(),

                                TextEntry::make('status')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'new' => 'primary',
                                        'replied' => 'success',
                                        'converted' => 'warning',
                                        'closed' => 'secondary',
                                        default => 'secondary',
                                    }),

                                TextEntry::make('created_at')
                                    ->label('Received')
                                    ->dateTime()
                                    ->since(),
                            ]),
                    ]),

                Section::make('Customer Information')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('customer_name')
                                    ->label('Name')
                                    ->icon('heroicon-o-user'),

                                TextEntry::make('customer_email')
                                    ->label('Email')
                                    ->icon('heroicon-o-envelope')
                                    ->copyable(),

                                TextEntry::make('customer_phone')
                                    ->label('Phone')
                                    ->icon('heroicon-o-phone')
                                    ->placeholder('Not provided')
                                    ->copyable(),

                                TextEntry::make('customer_country')
                                    ->label('Country')
                                    ->icon('heroicon-o-globe-alt')
                                    ->placeholder('Not specified'),
                            ]),
                    ]),

                Section::make('Tour Details')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('tour.title')
                                    ->label('Tour')
                                    ->columnSpan(3)
                                    ->url(fn ($record) => $record->tour ? route('filament.admin.resources.tours.edit', $record->tour) : null)
                                    ->openUrlInNewTab(),

                                TextEntry::make('preferred_date')
                                    ->label('Preferred Date')
                                    ->date()
                                    ->placeholder('Not specified'),

                                TextEntry::make('estimated_guests')
                                    ->label('Estimated Guests')
                                    ->suffix(' guests')
                                    ->placeholder('Not specified'),

                                TextEntry::make('tour.duration_days')
                                    ->label('Tour Duration')
                                    ->suffix(' days')
                                    ->placeholder('N/A'),
                            ]),
                    ]),

                Section::make('Message')
                    ->schema([
                        TextEntry::make('message')
                            ->label('')
                            ->columnSpanFull()
                            ->prose(),
                    ]),

                Section::make('Response Information')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('replied_at')
                                    ->label('Replied At')
                                    ->dateTime()
                                    ->placeholder('Not replied yet')
                                    ->since(),

                                TextEntry::make('repliedBy.name')
                                    ->label('Replied By')
                                    ->placeholder('N/A'),
                            ]),
                    ])
                    ->visible(fn ($record) => $record->status === 'replied' || $record->replied_at),

                Section::make('Conversion Information')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('booking.reference')
                                    ->label('Booking Reference')
                                    ->url(fn ($record) => $record->booking ? route('filament.admin.resources.bookings.bookings.edit', $record->booking) : null)
                                    ->openUrlInNewTab()
                                    ->placeholder('Not converted'),

                                TextEntry::make('converted_at')
                                    ->label('Converted At')
                                    ->dateTime()
                                    ->placeholder('Not converted')
                                    ->since(),
                            ]),
                    ])
                    ->visible(fn ($record) => $record->status === 'converted' || $record->booking_id),
            ]);
    }
}
