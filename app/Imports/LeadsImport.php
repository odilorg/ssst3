<?php

namespace App\Imports;

use App\Models\Lead;
use App\Models\LeadImport;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Validators\Failure;
use Throwable;

class LeadsImport implements
    ToCollection,
    WithHeadingRow,
    WithChunkReading,
    WithValidation,
    SkipsOnError,
    SkipsOnFailure
{
    private LeadImport $leadImport;
    private array $fieldMapping;
    private string $duplicateStrategy;
    private array $errors = [];

    public function __construct(LeadImport $leadImport, array $fieldMapping, string $duplicateStrategy = 'skip')
    {
        $this->leadImport = $leadImport;
        $this->fieldMapping = $fieldMapping;
        $this->duplicateStrategy = $duplicateStrategy;
    }

    public function collection(Collection $rows): void
    {
        foreach ($rows as $index => $row) {
            try {
                // Map CSV columns to Lead fields based on user's mapping
                $data = $this->mapFields($row->toArray());

                // Skip rows with no company name (required field)
                if (empty($data['company_name'])) {
                    $this->leadImport->increment('skipped_count');
                    $this->errors[] = "Row " . ($index + 2) . ": Company name is required";
                    continue;
                }

                // Check for duplicates
                $duplicate = $this->findDuplicate($data);

                if ($duplicate) {
                    $this->handleDuplicate($duplicate, $data, $index);
                } else {
                    // Create new lead
                    $this->createLead($data);
                }

                $this->leadImport->increment('processed_rows');
            } catch (\Exception $e) {
                $this->leadImport->increment('failed_count');
                $this->errors[] = "Row " . ($index + 2) . ": " . $e->getMessage();
                Log::error('Lead import error', [
                    'row' => $index + 2,
                    'error' => $e->getMessage(),
                    'data' => $data ?? []
                ]);
            }
        }

        // Update error log
        if (!empty($this->errors)) {
            $existingErrors = $this->leadImport->error_log ? json_decode($this->leadImport->error_log, true) : [];
            $allErrors = array_merge($existingErrors, $this->errors);
            $this->leadImport->update(['error_log' => json_encode($allErrors)]);
            $this->errors = []; // Reset for next chunk
        }
    }

    /**
     * Map CSV columns to Lead model fields based on user's field mapping.
     */
    private function mapFields(array $row): array
    {
        $mapped = [];

        foreach ($this->fieldMapping as $csvColumn => $leadField) {
            // Skip if no mapping for this column
            if (empty($leadField) || $leadField === 'ignore') {
                continue;
            }

            // Get value from CSV row (case-insensitive)
            $value = $row[strtolower($csvColumn)] ?? $row[$csvColumn] ?? null;

            // Handle special fields
            if (in_array($leadField, ['tour_types', 'target_markets', 'certifications'])) {
                // Convert comma-separated string to array
                $mapped[$leadField] = $value ? array_map('trim', explode(',', $value)) : null;
            } elseif ($leadField === 'has_uzbekistan_partner') {
                // Convert to boolean
                $mapped[$leadField] = in_array(strtolower($value), ['yes', 'true', '1', 'y']);
            } else {
                $mapped[$leadField] = $value;
            }
        }

        // Set defaults
        $mapped['source'] = $mapped['source'] ?? 'csv_import';
        $mapped['status'] = $mapped['status'] ?? 'new';
        $mapped['assigned_to'] = $mapped['assigned_to'] ?? Auth::id();
        $mapped['working_status'] = $mapped['working_status'] ?? 'active';

        return $mapped;
    }

    /**
     * Find duplicate lead by email or website.
     */
    private function findDuplicate(array $data): ?Lead
    {
        $query = Lead::query();

        // Check by email first (most reliable)
        if (!empty($data['email'])) {
            $lead = $query->where('email', $data['email'])->first();
            if ($lead) {
                return $lead;
            }
        }

        // Check by website as fallback
        if (!empty($data['website'])) {
            $lead = Lead::where('website', $data['website'])->first();
            if ($lead) {
                return $lead;
            }
        }

        return null;
    }

    /**
     * Handle duplicate lead based on strategy.
     */
    private function handleDuplicate(Lead $duplicate, array $data, int $index): void
    {
        switch ($this->duplicateStrategy) {
            case 'update':
                $duplicate->update($data);
                $this->leadImport->increment('updated_count');
                break;

            case 'create':
                $this->createLead($data);
                break;

            case 'skip':
            default:
                $this->leadImport->increment('skipped_count');
                $this->errors[] = "Row " . ($index + 2) . ": Skipped duplicate (email: {$duplicate->email})";
                break;
        }
    }

    /**
     * Create a new lead.
     */
    private function createLead(array $data): void
    {
        Lead::create($data);
        $this->leadImport->increment('created_count');
    }

    /**
     * Chunk size for processing.
     */
    public function chunkSize(): int
    {
        return 100;
    }

    /**
     * Validation rules.
     */
    public function rules(): array
    {
        return [
            '*.email' => ['nullable', 'email'],
            '*.website' => ['nullable', 'url'],
        ];
    }

    /**
     * Handle validation errors.
     */
    public function onError(Throwable $error): void
    {
        $this->errors[] = $error->getMessage();
        $this->leadImport->increment('failed_count');
    }

    /**
     * Handle validation failures.
     */
    public function onFailure(Failure ...$failures): void
    {
        foreach ($failures as $failure) {
            $error = "Row {$failure->row()}: " . implode(', ', $failure->errors());
            $this->errors[] = $error;
            $this->leadImport->increment('failed_count');
        }
    }
}
