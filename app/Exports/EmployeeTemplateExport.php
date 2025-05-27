<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Color;

class EmployeeTemplateExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths
{
    public function array(): array
    {
        return [
            [
                'EMP001',
                'Juan Dela Cruz',
                'Information Technology',
                'Software Developer',
                'juan.delacruz@company.com',
                '+63 912 345 6789'
            ],
            [
                'EMP002',
                'Maria Santos',
                'Human Resources',
                'HR Manager',
                'maria.santos@company.com',
                '+63 912 345 6790'
            ],
            [
                'EMP003',
                'José Rizal',
                'Finance',
                'Accountant',
                'jose.rizal@company.com',
                '+63 912 345 6791'
            ],
            [
                'EMP004',
                'Ana García',
                'Marketing',
                'Marketing Specialist',
                'ana.garcia@company.com',
                '+63 912 345 6792'
            ],
            [
                'EMP005',
                'Carlos López',
                'Operations',
                'Operations Manager',
                'carlos.lopez@company.com',
                '+63 912 345 6793'
            ]
        ];
    }

    public function headings(): array
    {
        return [
            'employee_number',
            'full_name',
            'department',
            'position',
            'email',
            'phone'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold with background color
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '2563EB'] // Blue color
                ]
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15, // Employee Number
            'B' => 25, // Full Name
            'C' => 20, // Department
            'D' => 20, // Position
            'E' => 30, // Email
            'F' => 15, // Phone
        ];
    }
}
