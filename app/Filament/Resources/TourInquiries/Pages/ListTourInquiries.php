<?php

namespace App\Filament\Resources\TourInquiries\Pages;

use App\Filament\Resources\TourInquiries\TourInquiryResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\Action;

class ListTourInquiries extends ListRecords
{
    protected static string $resource = TourInquiryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // No create action - inquiries come from the website
        ];
    }
}
