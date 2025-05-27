<?php

namespace App\Livewire\PAMO;

use App\Exports\PAMO\AssetsExport;
use App\Models\PAMO\MasterList;
use App\Models\PAMO\PamoAssetMovement;
use App\Models\PAMO\PamoAssets;
use App\Models\PAMO\PamoCategory;
use App\Models\PAMO\PamoLocations;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

#[Layout('layouts.pamo')]
class AssetTracker extends Component
{
    use WithPagination;

    // Filters
    public $search = '';
    public $categoryId = '';
    public $department = '';
    public $locationId = '';
    public $status = '';

    // Modal states
    public $showTransferModal = false;
    public $showScanModal = false;
    public $showExportModal = false;
    public $showAssetDetailModal = false;

    // Selected asset data
    public $selectedAsset = null;

    public $fromLocationId;
    public $toLocationId;
    // public $assignedToUserId;
    public $assignedToEmployeeId;
    public $movementNotes;
    public $movementDate;

    //  AssetTracker class
    public $reportType = 'inventory'; // Default report type
    public $exportCategory = '';
    public $exportLocation = '';
    public $exportStatus = '';
    public $dateFrom;
    public $dateTo;
    public $exportFields = [
        'property_tag_number',
        'brand',
        'model',
        'serial_number',
        'status',
        'location'
    ];

    protected $queryString = [
        'search' => ['except' => ''],
        'categoryId' => ['except' => ''],
        'department' => ['except' => ''],
        'locationId' => ['except' => ''],
        'status' => ['except' => ''],
    ];

    public function mount()
    {
        $this->movementDate = now()->format('Y-m-d');
        $this->dateFrom = now()->subDays(30)->format('Y-m-d');
        $this->dateTo = now()->format('Y-m-d');
    }


    public function render()
    {
        $assets = PamoAssets::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('brand', 'like', '%' . $this->search . '%')
                      ->orWhere('model', 'like', '%' . $this->search . '%')
                      ->orWhere('serial_number', 'like', '%' . $this->search . '%')
                      ->orWhere('property_tag_number', 'like', '%' . $this->search . '%')
                      ->orWhere('barcode', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->categoryId, fn($query) => $query->where('category_id', $this->categoryId))
            ->when($this->department, fn($query) => $query->whereHas('assignedEmployee', fn($q) => $q->where('department', $this->department))) // Changed from assignedUser
            ->when($this->locationId, fn($query) => $query->where('location_id', $this->locationId))
            ->when($this->status, fn($query) => $query->where('status', $this->status))
            ->with(['assignedEmployee', 'location', 'category']) // Changed from assignedUser
            ->paginate(10);

         $recentMovements = PamoAssetMovement::with(['asset', 'fromLocation', 'toLocation', 'assignedBy', 'assignedEmployee']) // Updated relationships
            ->latest('movement_date')
            ->take(5)
            ->get();

        return view('livewire.p-a-m-o.asset-tracker', [
            'assets' => $assets,
            'recentMovements' => $recentMovements,
            'locations' => PamoLocations::where('is_active', true)->get(),
            'categories' => PamoCategory::all(),
            'employees' => MasterList::where('status', 'active')->orderBy('full_name')->get(), // Changed from users
            'departments' => MasterList::select('department')->distinct()->whereNotNull('department')->pluck('department'), // Changed from User
            'statuses' => PamoAssets::select('status')->distinct()->pluck('status'),

        ]);
    }

    // Reset filters
    public function resetFilters()
    {
        $this->search = '';
        $this->categoryId = '';
        $this->department = '';
        $this->locationId = '';
        $this->status = '';
    }

    // Record Transfer
    public function openTransferModal($assetId = null)
    {
        if ($assetId) {
            $this->selectedAsset = PamoAssets::with(['assignedEmployee', 'location'])->findOrFail($assetId); // Changed from assignedUser
            $this->fromLocationId = $this->selectedAsset->location_id;
            $this->assignedToEmployeeId = $this->selectedAsset->assigned_to; // Changed property name
        }
        $this->showTransferModal = true;
    }

    public function recordTransfer()
    {
        $this->validate([
            'selectedAsset' => 'required',
            'toLocationId' => 'required',
            'movementDate' => 'required|date',
        ]);

        // Create movement record
        PamoAssetMovement::create([
            'asset_id' => $this->selectedAsset->id,
            'from_location_id' => $this->fromLocationId,
            'to_location_id' => $this->toLocationId,
            'assigned_by' => auth()->id(),
            'assigned_to' => $this->assignedToEmployeeId, // This now references master_lists
            'movement_date' => $this->movementDate,
            'notes' => $this->movementNotes,
            'movement_type' => 'transfer',
        ]);

        // Update asset location
        $this->selectedAsset->update([
            'location_id' => $this->toLocationId,
            'assigned_to' => $this->assignedToEmployeeId, // This now references master_lists
            'status' => $this->assignedToEmployeeId ? 'assigned' : 'available',
        ]);

        $this->showTransferModal = false;
        $this->reset(['selectedAsset', 'fromLocationId', 'toLocationId', 'assignedToEmployeeId', 'movementNotes']); // Updated property name
        $this->dispatch('notify', ['message' => 'Asset transfer recorded successfully']);
    }

    // Scan Asset
    public function openScanModal()
    {
        $this->showScanModal = true;
    }

    public function processScan($barcode)
    {
        $asset = PamoAssets::where('barcode', $barcode)
            ->orWhere('serial_number', $barcode)
            ->orWhere('property_tag_number', $barcode)
            ->first();

        if ($asset) {
            $this->selectedAsset = $asset;
            $this->showScanModal = false;
            $this->viewAsset($asset->id);
        } else {
            $this->dispatchBrowserEvent('notify', [
                'message' => 'Asset not found with the scanned code: ' . $barcode,
                'type' => 'error'
            ]);
        }
    }

    // Export Report
    public function openExportModal()
    {
        $this->showExportModal = true;
    }

    public function exportReport($format)
    {
        // Validate that at least one field is selected
        if (empty($this->exportFields)) {
            $this->dispatch('notify', [
                'message' => 'Please select at least one field to include in the report',
                'type' => 'error'
            ]);
            return;
        }

        $filename = 'asset-' . $this->reportType . '-report-' . now()->format('Y-m-d');

        if ($this->reportType === 'inventory') {
            $query = PamoAssets::query()
                ->with(['category', 'location', 'assignedEmployee']) // Changed from assignedUser
                ->when($this->exportCategory, fn($q) => $q->where('category_id', $this->exportCategory))
                ->when($this->exportLocation, fn($q) => $q->where('location_id', $this->exportLocation))
                ->when($this->exportStatus, fn($q) => $q->where('status', $this->exportStatus));

            $exportData = $query->get();
        } else {
            $query = PamoAssetMovement::query()
                ->with(['asset', 'fromLocation', 'toLocation', 'assignedBy', 'assignedEmployee']) // Updated relationships
                ->when($this->dateFrom, fn($q) => $q->whereDate('movement_date', '>=', $this->dateFrom))
                ->when($this->dateTo, fn($q) => $q->whereDate('movement_date', '<=', $this->dateTo))
                ->when($this->exportCategory, fn($q) => $q->whereHas('asset', fn($a) => $a->where('category_id', $this->exportCategory)))
                ->when($this->exportLocation, function($q) {
                    $q->where(function($query) {
                        $query->where('from_location_id', $this->exportLocation)
                            ->orWhere('to_location_id', $this->exportLocation);
                    });
                });

            $exportData = $query->get();
        }

        $this->showExportModal = false;

        // Handle different export formats
        switch($format) {
            case 'excel':
                return Excel::download(new AssetsExport($exportData, $this->exportFields), $filename . '.xlsx');

            case 'csv':
                return Excel::download(new AssetsExport($exportData, $this->exportFields), $filename . '.csv');

            case 'pdf':
                $data = [
                    'title' => ucfirst($this->reportType) . ' Report',
                    'date' => now()->format('F d, Y'),
                    'fields' => $this->exportFields,
                    'data' => $exportData,
                ];

                $pdf = Pdf::loadView('exports.assets-pdf', $data)
                    ->setPaper('a4', 'landscape');

                $this->showExportModal = false;

                return response()->streamDownload(
                    function () use ($pdf) {
                        echo $pdf->output();
                    },
                    $filename . '.pdf',
                    ['Content-Type' => 'application/pdf']
                );
        }
    }

    // View Asset Details
    public function viewAsset($assetId)
    {
        $this->selectedAsset = PamoAssets::with([
            'assignedEmployee', // Changed from assignedUser
            'location',
            'category',
            'movements' => function($query) {
                $query->latest('movement_date')->take(5);
            }
        ])->findOrFail($assetId);

        $this->showAssetDetailModal = true;
    }


}
