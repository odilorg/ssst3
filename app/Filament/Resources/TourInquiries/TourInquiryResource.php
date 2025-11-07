<?php

namespace App\Filament\Resources\TourInquiries;

use App\Filament\Resources\TourInquiries\Pages\ListTourInquiries;
use App\Filament\Resources\TourInquiries\Pages\ViewTourInquiry;
use App\Filament\Resources\TourInquiries\Schemas\TourInquiryInfolist;
use App\Filament\Resources\TourInquiries\Tables\TourInquiriesTable;
use App\Models\TourInquiry;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class TourInquiryResource extends Resource
{
    protected static ?string $model = TourInquiry::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChatBubbleBottomCenterText;

    protected static ?string $recordTitleAttribute = 'reference';

    protected static ?int $navigationSort = 4;

    public static function infolist(Schema $schema): Schema
    {
        return TourInquiryInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TourInquiriesTable::configure($table);
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
            'index' => ListTourInquiries::route('/'),
            'view' => ViewTourInquiry::route('/{record}'),
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Tours';
    }

    public static function getNavigationLabel(): string
    {
        return 'Inquiries';
    }

    public static function getModelLabel(): string
    {
        return 'Tour Inquiry';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Tour Inquiries';
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'new')->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $count = static::getModel()::where('status', 'new')->count();

        return match (true) {
            $count === 0 => 'success',
            $count < 5 => 'warning',
            default => 'danger',
        };
    }
}
