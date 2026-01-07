<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Pages\Page;

class BookingCalendar extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationLabel = 'Calendar';

    protected static ?int $navigationSort = 2;

    protected static ?string $title = 'Booking Calendar';

    protected string $view = 'filament.pages.booking-calendar';

    public static function getNavigationGroup(): ?string
    {
        return 'Bookings';
    }

    public function getHeading(): string
    {
        return 'Booking Calendar';
    }

    public function getSubheading(): ?string
    {
        return 'View and manage all bookings in calendar view';
    }
}
