<?php

namespace App\Filament\Resources\Tours\Pages;

use App\Filament\Resources\Tours\TourResource;
use App\Models\CompanySetting;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Resources\Pages\Page;
use Filament\Actions\Action;

class ViewTour extends Page
{
    protected static string $resource = TourResource::class;
    protected string $view = 'filament.resources.tours.pages.view-tour';

    public $record;
    public $tour;
    public $companySettings;

    public function mount(int | string $record): void
    {
        $this->record = TourResource::resolveRecordRouteBinding($record);
        $this->tour = $this->record;
        $this->companySettings = CompanySetting::current();
    }

    public function getTitle(): string
    {
        return 'Tour Details: ' . $this->tour->title;
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('print')
                ->label('Print')
                ->icon('heroicon-o-printer')
                ->color('primary')
                ->action('printTour'),
            Action::make('export_pdf')
                ->label('Export PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->color('success')
                ->action('exportPdf'),
            Action::make('back')
                ->label('Back to List')
                ->icon('heroicon-o-arrow-left')
                ->color('gray')
                ->url(fn (): string => TourResource::getUrl('index')),
        ];
    }

    public function printTour(): void
    {
        $this->js('window.print()');
    }

    public function exportPdf()
    {
        $pdf = Pdf::loadView('pdf.tour', [
            'tour' => $this->tour,
            'companySettings' => $this->companySettings,
        ]);

        $filename = str_replace(' ', '_', $this->tour->title) . '_Tour_Details.pdf';

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, $filename);
    }
}
