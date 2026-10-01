<?php

namespace App\Exports\PAM;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PrivilegedAccessReportExport implements FromCollection, WithHeadings, WithStyles
{
    protected Collection $users;

    protected array $stats;

    public function __construct(Collection $users, array $stats)
    {
        $this->users = $users;
        $this->stats = $stats;
    }

    public function collection(): Collection
    {
        $data = collect();

        foreach ($this->users as $user) {
            // ITSS Assets
            foreach ($user->itss_assets as $asset) {
                $data->push([
                    'User Name' => $user->name,
                    'Email' => $user->email,
                    'Role' => $user->role?->name ?? 'N/A',
                    'Asset Type' => 'ITSS Asset',
                    'Asset Name' => $asset->item_name,
                    'Serial' => $asset->item_serial_itss,
                    'Location' => $asset->location,
                    'Status' => $asset->status,
                    'Category' => $asset->category?->name ?? $asset->assetList?->name ?? 'N/A',
                ]);
            }

            // PAMO Assets
            foreach ($user->pamo_assets as $asset) {
                $data->push([
                    'User Name' => $user->name,
                    'Email' => $user->email,
                    'Role' => $user->role?->name ?? 'N/A',
                    'Asset Type' => 'PAMO Asset',
                    'Asset Name' => $asset->brand.' '.$asset->model,
                    'Serial' => $asset->serial_number,
                    'Location' => $asset->location?->name ?? 'N/A',
                    'Status' => $asset->status,
                    'Category' => $asset->category?->name ?? 'N/A',
                ]);
            }

            // Borrowed Items
            foreach ($user->borrowed_items as $borrow) {
                foreach ($borrow->itemBorrow as $item) {
                    $data->push([
                        'User Name' => $user->name,
                        'Email' => $user->email,
                        'Role' => $user->role?->name ?? 'N/A',
                        'Asset Type' => 'Borrowed Equipment',
                        'Asset Name' => $item->assetCategory?->name ?? $item->brand,
                        'Serial' => $item->serial ?? 'N/A',
                        'Location' => $borrow->location ?? 'N/A',
                        'Status' => $borrow->status,
                        'Category' => 'Borrowed',
                    ]);
                }
            }

            // If no assets, add a row showing user with no assets
            if ($user->itss_assets->isEmpty() && $user->pamo_assets->isEmpty() && $user->borrowed_items->isEmpty()) {
                $data->push([
                    'User Name' => $user->name,
                    'Email' => $user->email,
                    'Role' => $user->role?->name ?? 'N/A',
                    'Asset Type' => 'None',
                    'Asset Name' => '-',
                    'Serial' => '-',
                    'Location' => '-',
                    'Status' => 'No Assets',
                    'Category' => '-',
                ]);
            }
        }

        return $data;
    }

    public function headings(): array
    {
        return [
            'User Name',
            'Email',
            'Role',
            'Asset Type',
            'Asset Name',
            'Serial',
            'Location',
            'Status',
            'Category',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 12],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E0E7FF'],
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                'borders' => [
                    'allBorders' => ['borderStyle' => Border::BORDER_THIN],
                ],
            ],
        ];
    }
}
