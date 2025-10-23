<?php

namespace App\Filament\Pages;

use App\Imports\LeadsImport;
use App\Models\Lead;
use App\Models\LeadImport;
use BackedEnum;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\HeadingRowImport;

class ImportLeads extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowUpTray;

    protected static string $view = 'filament.pages.import-leads';

    public static function getNavigationGroup(): ?string
    {
        return 'Lead Management';
    }

    public static function getNavigationSort(): ?int
    {
        return 2;
    }

    public static function getNavigationLabel(): string
    {
        return 'Import Leads';
    }

    public function getTitle(): string
    {
        return 'Import Leads';
    }

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make([
                    Wizard\Step::make('Upload CSV')
                        ->description('Upload your CSV file containing lead data')
                        ->schema([
                            Forms\Components\FileUpload::make('csv_file')
                                ->label('CSV File')
                                ->acceptedFileTypes(['text/csv', 'application/csv', 'text/plain'])
                                ->maxSize(5120) // 5MB
                                ->required()
                                ->helperText('Maximum file size: 5MB. File must be in CSV format.')
                                ->disk('local')
                                ->directory('lead-imports')
                                ->preserveFilenames()
                                ->live()
                                ->afterStateUpdated(function ($state, Forms\Set $set) {
                                    if ($state) {
                                        // Read CSV headers
                                        $filePath = Storage::disk('local')->path($state);
                                        $headers = $this->getCSVHeaders($filePath);
                                        $set('csv_headers', $headers);
                                        $set('field_mapping', $this->autoDetectMapping($headers));
                                    }
                                }),

                            Forms\Components\Placeholder::make('file_info')
                                ->label('')
                                ->content(function ($get) {
                                    if ($get('csv_file')) {
                                        $filePath = Storage::disk('local')->path($get('csv_file'));
                                        $rowCount = $this->getCSVRowCount($filePath);
                                        return "📊 File contains approximately **{$rowCount} rows** (excluding header).";
                                    }
                                    return '';
                                })
                                ->visible(fn ($get) => $get('csv_file') !== null),
                        ]),

                    Wizard\Step::make('Map Fields')
                        ->description('Map CSV columns to lead fields')
                        ->schema([
                            Forms\Components\Section::make('Field Mapping')
                                ->description('Match your CSV columns to the appropriate lead fields. Unmapped columns will be ignored.')
                                ->schema(function ($get) {
                                    $headers = $get('csv_headers') ?? [];
                                    $fields = [];

                                    $leadFields = $this->getLeadFields();

                                    foreach ($headers as $header) {
                                        $fields[] = Forms\Components\Select::make("field_mapping.{$header}")
                                            ->label("CSV Column: {$header}")
                                            ->options($leadFields)
                                            ->placeholder('-- Ignore this column --')
                                            ->searchable();
                                    }

                                    return $fields;
                                })
                                ->columns(2),

                            Forms\Components\Toggle::make('has_header_row')
                                ->label('First row contains headers')
                                ->default(true)
                                ->helperText('Enable if your CSV file has column names in the first row.'),
                        ]),

                    Wizard\Step::make('Preview & Import')
                        ->description('Review settings and start the import')
                        ->schema([
                            Forms\Components\Section::make('Preview')
                                ->description('Review the first few rows of your data')
                                ->schema([
                                    Forms\Components\Placeholder::make('preview')
                                        ->label('')
                                        ->content(function ($get) {
                                            $filePath = Storage::disk('local')->path($get('csv_file'));
                                            return $this->renderPreview($filePath, $get('field_mapping') ?? [], 5);
                                        }),
                                ]),

                            Forms\Components\Section::make('Duplicate Handling')
                                ->description('How should we handle duplicate leads?')
                                ->schema([
                                    Forms\Components\Radio::make('duplicate_strategy')
                                        ->label('If a duplicate is found')
                                        ->options([
                                            'skip' => 'Skip the duplicate (recommended) - Leave existing lead unchanged',
                                            'update' => 'Update existing lead - Overwrite with new data from CSV',
                                            'create' => 'Create anyway - Allow duplicates in database',
                                        ])
                                        ->default('skip')
                                        ->required()
                                        ->descriptions([
                                            'skip' => 'Safest option. Won\'t modify existing data.',
                                            'update' => 'Use this to refresh lead information.',
                                            'create' => 'Not recommended. May create duplicate records.',
                                        ]),

                                    Forms\Components\Placeholder::make('duplicate_info')
                                        ->label('')
                                        ->content('📧 Duplicates are detected by matching **email address** or **website URL**.'),
                                ]),

                            Forms\Components\Section::make('Summary')
                                ->schema([
                                    Forms\Components\Placeholder::make('import_summary')
                                        ->label('')
                                        ->content(function ($get) {
                                            $filePath = Storage::disk('local')->path($get('csv_file'));
                                            $rowCount = $this->getCSVRowCount($filePath);
                                            $mappedFields = count(array_filter($get('field_mapping') ?? []));

                                            return view('filament.pages.import-summary', [
                                                'rowCount' => $rowCount,
                                                'mappedFields' => $mappedFields,
                                                'filename' => basename($get('csv_file')),
                                            ])->render();
                                        }),
                                ]),
                        ]),
                ])
                    ->submitAction(view('filament.pages.import-submit-button')),
            ])
            ->statePath('data');
    }

    /**
     * Handle form submission - start the import.
     */
    public function submit(): void
    {
        $data = $this->form->getState();

        try {
            // Create LeadImport record
            $filePath = Storage::disk('local')->path($data['csv_file']);
            $rowCount = $this->getCSVRowCount($filePath);

            $leadImport = LeadImport::create([
                'user_id' => Auth::id(),
                'filename' => basename($data['csv_file']),
                'filepath' => $data['csv_file'],
                'status' => 'processing',
                'total_rows' => $rowCount,
                'field_mapping' => $data['field_mapping'],
                'duplicate_strategy' => $data['duplicate_strategy'],
                'started_at' => now(),
            ]);

            // Perform the import
            Excel::import(
                new LeadsImport($leadImport, $data['field_mapping'], $data['duplicate_strategy']),
                $filePath
            );

            // Update status
            $leadImport->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);

            // Show success notification
            Notification::make()
                ->title('Import completed successfully!')
                ->success()
                ->body("Imported {$leadImport->created_count} leads, updated {$leadImport->updated_count}, skipped {$leadImport->skipped_count} duplicates.")
                ->send();

            // Redirect to leads list
            $this->redirect(route('filament.admin.resources.leads.leads.index'));
        } catch (\Exception $e) {
            if (isset($leadImport)) {
                $leadImport->update([
                    'status' => 'failed',
                    'completed_at' => now(),
                ]);
            }

            Notification::make()
                ->title('Import failed')
                ->danger()
                ->body($e->getMessage())
                ->send();
        }
    }

    /**
     * Get CSV headers from file.
     */
    private function getCSVHeaders(string $filePath): array
    {
        $headings = (new HeadingRowImport)->toArray($filePath);
        return $headings[0][0] ?? [];
    }

    /**
     * Get approximate row count from CSV.
     */
    private function getCSVRowCount(string $filePath): int
    {
        $file = fopen($filePath, 'r');
        $count = 0;

        while (fgets($file) !== false) {
            $count++;
        }

        fclose($file);

        return max(0, $count - 1); // Subtract header row
    }

    /**
     * Auto-detect field mapping based on column names.
     */
    private function autoDetectMapping(array $headers): array
    {
        $mapping = [];
        $patterns = [
            'company_name' => ['company', 'company name', 'business', 'organization'],
            'email' => ['email', 'e-mail', 'email address'],
            'website' => ['website', 'web site', 'url', 'web', 'site'],
            'phone' => ['phone', 'telephone', 'tel', 'mobile', 'contact number'],
            'country' => ['country', 'nation'],
            'city' => ['city', 'town', 'location'],
            'contact_name' => ['contact name', 'contact person', 'contact', 'rep name'],
            'contact_email' => ['contact email', 'rep email', 'representative email'],
            'source' => ['source', 'lead source', 'origin'],
        ];

        foreach ($headers as $header) {
            $headerLower = strtolower(trim($header));

            foreach ($patterns as $field => $keywords) {
                foreach ($keywords as $keyword) {
                    if (str_contains($headerLower, $keyword)) {
                        $mapping[$header] = $field;
                        continue 3;
                    }
                }
            }

            $mapping[$header] = ''; // Default to ignore
        }

        return $mapping;
    }

    /**
     * Get available lead fields for mapping.
     */
    private function getLeadFields(): array
    {
        return [
            'company_name' => 'Company Name',
            'email' => 'Email',
            'website' => 'Website',
            'phone' => 'Phone',
            'description' => 'Description',
            'contact_name' => 'Contact Name',
            'contact_position' => 'Contact Position',
            'contact_email' => 'Contact Email',
            'contact_phone' => 'Contact Phone',
            'country' => 'Country',
            'city' => 'City',
            'source' => 'Source',
            'source_url' => 'Source URL',
            'source_notes' => 'Source Notes',
            'business_type' => 'Business Type',
            'tour_types' => 'Tour Types (comma-separated)',
            'target_markets' => 'Target Markets (comma-separated)',
            'annual_volume' => 'Annual Volume',
            'certifications' => 'Certifications (comma-separated)',
            'quality_score' => 'Quality Score (1-5)',
            'notes' => 'Notes',
        ];
    }

    /**
     * Render preview of CSV data.
     */
    private function renderPreview(string $filePath, array $fieldMapping, int $rows = 5): string
    {
        $file = fopen($filePath, 'r');
        $headers = fgetcsv($file);
        $preview = [];

        for ($i = 0; $i < $rows && ($row = fgetcsv($file)) !== false; $i++) {
            $preview[] = array_combine($headers, $row);
        }

        fclose($file);

        if (empty($preview)) {
            return '<p class="text-sm text-gray-500">No data to preview.</p>';
        }

        // Build HTML table
        $html = '<div class="overflow-x-auto"><table class="min-w-full divide-y divide-gray-200 text-sm">';
        $html .= '<thead class="bg-gray-50"><tr>';

        foreach ($headers as $header) {
            $mappedField = $fieldMapping[$header] ?? '';
            $mappedLabel = $mappedField ? "→ {$mappedField}" : '(ignored)';
            $html .= "<th class=\"px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase\">{$header}<br/><span class=\"text-xs text-blue-600\">{$mappedLabel}</span></th>";
        }

        $html .= '</tr></thead><tbody class="bg-white divide-y divide-gray-200">';

        foreach ($preview as $row) {
            $html .= '<tr>';
            foreach ($row as $cell) {
                $html .= '<td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">' . htmlspecialchars($cell) . '</td>';
            }
            $html .= '</tr>';
        }

        $html .= '</tbody></table></div>';

        return $html;
    }
}
