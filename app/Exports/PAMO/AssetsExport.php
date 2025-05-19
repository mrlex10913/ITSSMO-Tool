<?php

namespace App\Exports\PAMO;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AssetsExport implements FromCollection, WithHeadings, WithMapping
{

    protected $data;
    protected $fields;

    public function __construct($data, $fields)
    {
        $this->data = $data;
        $this->fields = $fields;
    }
    public function collection()
    {
        return $this->data;
    }
    public function headings(): array
    {
        $headings = [];
        foreach ($this->fields as $field) {
            $headings[] = ucfirst(str_replace('_', ' ', $field));
        }
        return $headings;
    }
    public function map($row): array
    {
        $mapped = [];
        foreach ($this->fields as $field) {
            switch ($field) {
                case 'category':
                    $mapped[] = $row->category->name ?? 'N/A';
                    break;
                case 'location':
                    $mapped[] = $row->location->name ?? 'N/A';
                    break;
                case 'assigned_to':
                    $mapped[] = $row->assignedUser->name ?? 'Unassigned';
                    break;
                default:
                    $mapped[] = $row->{$field} ?? 'N/A';
            }
        }
        return $mapped;
    }
}
