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
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

#[Layout('layouts.enduser')]
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
        // Load assets with all necessary relationships
        $assets = PamoAssets::query()
            ->with([
                'category',           // Load category relationship
                'location',           // Load location relationship
                'assignedEmployee'    // Load assigned employee from MasterList
            ])
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
            ->when($this->department, function($query) {
                $query->whereHas('assignedEmployee', function($q) {
                    $q->where('department', $this->department);
                });
            })
            ->when($this->locationId, fn($query) => $query->where('location_id', $this->locationId))
            ->when($this->status, fn($query) => $query->where('status', $this->status))
            ->orderBy('property_tag_number')
            ->paginate(10);

        // Load recent movements with all relationships
        $recentMovements = PamoAssetMovement::query()
            ->with([
                'asset.category',     // Load asset with category
                'fromLocation',       // Load from location
                'toLocation',         // Load to location
                'assignedBy',         // Load user who performed the action
                'assignedEmployee'    // Load assigned employee from MasterList
            ])
            ->orderByDesc('movement_date')
            ->orderByDesc('created_at')
            ->take(10)
            ->get();

        // Load filter data
        $locations = PamoLocations::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $categories = PamoCategory::query()
            ->orderBy('name')
            ->get();

        $employees = MasterList::query()
            ->where('status', 'active')
            ->orderBy('full_name')
            ->get();

        // Get unique departments from master list
        $departments = MasterList::query()
            ->select('department')
            ->distinct()
            ->whereNotNull('department')
            ->where('department', '!=', '')
            ->orderBy('department')
            ->pluck('department');

        // Get unique statuses from assets
        $statuses = PamoAssets::query()
            ->select('status')
            ->distinct()
            ->whereNotNull('status')
            ->where('status', '!=', '')
            ->orderBy('status')
            ->pluck('status');

        return view('livewire.p-a-m-o.asset-tracker', [
            'assets' => $assets,
            'recentMovements' => $recentMovements,
            'locations' => $locations,
            'categories' => $categories,
            'employees' => $employees,
            'departments' => $departments,
            'statuses' => $statuses,
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
        $this->resetPage(); // Reset pagination when filters are cleared
    }

    // Record Transfer
    public function openTransferModal($assetId = null)
    {
        if ($assetId) {
            $this->selectedAsset = PamoAssets::query()
                ->with(['assignedEmployee', 'location', 'category'])
                ->findOrFail($assetId);

            $this->fromLocationId = $this->selectedAsset->location_id;
            $this->assignedToEmployeeId = $this->selectedAsset->assigned_to;
        } else {
            // Clear form when opening without specific asset
            $this->reset(['selectedAsset', 'fromLocationId', 'toLocationId', 'assignedToEmployeeId', 'movementNotes']);
        }

        $this->movementDate = now()->format('Y-m-d');
        $this->showTransferModal = true;
    }

    public function recordTransfer()
    {
        $this->validate([
            'selectedAsset' => 'required',
            'toLocationId' => 'required|exists:pamo_locations,id',
            'movementDate' => 'required|date|before_or_equal:today',
            'assignedToEmployeeId' => 'nullable|exists:master_lists,id',
            'movementNotes' => 'nullable|string|max:500'
        ]);

        try {
            DB::transaction(function() {
                // Create movement record
                PamoAssetMovement::create([
                    'asset_id' => $this->selectedAsset->id,
                    'from_location_id' => $this->fromLocationId,
                    'to_location_id' => $this->toLocationId,
                    'assigned_by' => auth()->id(),
                    'assigned_to' => $this->assignedToEmployeeId,
                    'movement_date' => $this->movementDate,
                    'notes' => $this->movementNotes,
                    'movement_type' => 'transfer',
                ]);

                // Update asset
                $this->selectedAsset->update([
                    'location_id' => $this->toLocationId,
                    'assigned_to' => $this->assignedToEmployeeId,
                    'status' => $this->assignedToEmployeeId ? 'assigned' : 'available',
                    'updated_at' => now()
                ]);
            });

            $this->showTransferModal = false;
            $this->reset(['selectedAsset', 'fromLocationId', 'toLocationId', 'assignedToEmployeeId', 'movementNotes']);

            $this->dispatch('notify', [
                'message' => 'Asset transfer recorded successfully!',
                'type' => 'success'
            ]);

        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'message' => 'Error recording transfer: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }

    // Scan Asset
    public function openScanModal()
    {
        $this->showScanModal = true;
    }

    public function processScan($barcode)
    {
        $asset = PamoAssets::query()
            ->with(['category', 'location', 'assignedEmployee'])
            ->where(function($query) use ($barcode) {
                $query->where('barcode', $barcode)
                      ->orWhere('serial_number', $barcode)
                      ->orWhere('property_tag_number', $barcode);
            })
            ->first();

        if ($asset) {
            $this->selectedAsset = $asset;
            $this->showScanModal = false;
            $this->viewAsset($asset->id);
        } else {
            $this->dispatch('notify', [
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
        $this->selectedAsset = PamoAssets::query()
            ->with([
                'assignedEmployee',
                'location',
                'category',
                'movements' => function($query) {
                    $query->with(['fromLocation', 'toLocation', 'assignedBy', 'assignedEmployee'])
                          ->orderByDesc('movement_date')
                          ->orderByDesc('created_at')
                          ->take(10);
                }
            ])
            ->findOrFail($assetId);

        $this->showAssetDetailModal = true;
    }

    public function generateQR($assetId)
    {
        $asset = PamoAssets::findOrFail($assetId);

        // You can implement QR code generation logic here
        $this->dispatch('notify', [
            'message' => 'QR Code generated for ' . $asset->property_tag_number,
            'type' => 'success'
        ]);
    }
    public function printLabel($assetId)
    {
        $asset = PamoAssets::findOrFail($assetId);

        // You can implement label printing logic here
        $this->dispatch('notify', [
            'message' => 'Label print requested for ' . $asset->property_tag_number,
            'type' => 'success'
        ]);
    }
    public function viewHistory($assetId)
    {
        $this->viewAsset($assetId); // Reuse the view asset modal
    }

    // Close modals
    public function closeTransferModal()
    {
        $this->showTransferModal = false;
        $this->reset(['selectedAsset', 'fromLocationId', 'toLocationId', 'assignedToEmployeeId', 'movementNotes']);
    }

    public function closeScanModal()
    {
        $this->showScanModal = false;
    }

    public function closeExportModal()
    {
        $this->showExportModal = false;
    }

    public function closeAssetDetailModal()
    {
        $this->showAssetDetailModal = false;
        $this->selectedAsset = null;
    }

    // Updaters for real-time search
    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedCategoryId()
    {
        $this->resetPage();
    }

    public function updatedDepartment()
    {
        $this->resetPage();
    }

    public function updatedLocationId()
    {
        $this->resetPage();
    }

    public function updatedStatus()
    {
        $this->resetPage();
    }


}
