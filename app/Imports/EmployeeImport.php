<?php

namespace App\Imports;


use App\Models\PAMO\MasterList;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class EmployeeImport implements ToModel, WithHeadingRow, SkipsOnError, SkipsOnFailure
{
    use Importable, SkipsErrors, SkipsFailures;

    public $imported = 0;
    public $failed = 0;

    public function model(array $row)
    {
        // Debug: Log the actual row structure
        Log::info('Row data:', $row);

        $data = [
            'employee_number' => $this->cleanString($row['employee_number'] ?? $row['employee number'] ?? ''),
            'full_name' => $this->cleanString($row['full_name'] ?? $row['full name'] ?? ''),
            'department' => $this->cleanString($row['department'] ?? ''),
            'position' => $this->cleanString($row['position'] ?? null),
            'email' => $this->cleanString($row['email'] ?? null),
            'phone' => $this->cleanString($row['phone'] ?? null),
            'status' => 'active'
        ];

        // Skip if required fields are empty
        if (empty($data['employee_number']) || empty($data['full_name']) || empty($data['department'])) {
            $this->failed++;
            return null;
        }

        // Validate the data manually
        $validator = Validator::make($data, [
            'employee_number' => ['required', 'string', 'max:50', Rule::unique('master_lists', 'employee_number')],
            'full_name' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'position' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20'
        ]);

        if ($validator->fails()) {
            $this->failed++;
            Log::error('Validation failed for row:', ['data' => $data, 'errors' => $validator->errors()]);
            return null;
        }

        try {
            $this->imported++;
            return new MasterList($data);
        } catch (\Exception $e) {
            $this->failed++;
            Log::error('Database error for row:', ['data' => $data, 'error' => $e->getMessage()]);
            return null;
        }
    }

    private function cleanString($value)
    {
        if (empty($value)) return null;

        $value = trim((string) $value);
        $value = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $value);

        // Simplified character replacements to avoid syntax issues
        $replacements = [
            '–' => '-',     // En dash
            '—' => '-',     // Em dash
            '…' => '...'    // Horizontal ellipsis
        ];

        $value = str_replace(array_keys($replacements), array_values($replacements), $value);

        if (!mb_check_encoding($value, 'UTF-8')) {
            $value = mb_convert_encoding($value, 'UTF-8', 'auto');
        }

        return $value;
    }

    public function getImportStats()
    {
        return [
            'imported' => $this->imported,
            'errors' => $this->failed,
            'failures' => []
        ];
    }
}
