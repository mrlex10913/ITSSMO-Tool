<?php

namespace App\Livewire\PAMO;

use App\Models\PAMO\PamoAssetMovement;
use App\Models\PAMO\PamoAssets;
use App\Models\PAMO\PamoCategory;
use App\Models\PAMO\PamoLocations;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Layout('layouts.pamo')]
class Inventory extends Component
{
    use WithPagination;
    use WithFileUploads;

    public $activeTab = 'assets';

    public $search = '';
    public $categoryFilter = '';
    public $statusFilter = '';
    public $perPage = 10;

    // Categories
    public $categories = [];
    public $majorCategories = [];
    public $minorCategories = [];
    public $category = [
        'name' => '',
        'type' => 'minor',
        'parent_id' => '',
        'description' => '',
    ];

    // Asset management
    public $assets = [];
    public $unassignedAssets = [];
    public $asset = [
        'po_number' => '',
        'property_tag_number' => '',
        'barcode' => '',
        'brand' => '',
        'model' => '',
        'serial_number' => '',
        'major_category_id' => '',
        'category_id' => '',
        'status' => 'available',
        'purchase_date' => '',
        'purchase_value' => '',
        'description' => '',
    ];

    // Asset assignment
    public $selectedAssets = [];
    public $bulkAction = '';
    public $assignLocation = null;

    public $users = [];
    public $assignToUser = null;
    public $movementNotes = '';

    //Location
    public $locations = [];
    public $location = [
        'name' => '',
        'code' => '',
        'address' => '',
        'type' => 'office',
        'description' => '',
        'is_active' => true,
    ];

    // Bulk add items - manual entry
    public $bulkItems = [];
    public $bulkItemsDefaults = [
        'major_category_id' => '',
        'category_id' => '',
        'status' => 'available',
    ];


    // CSV import
    public $csvFile;
    public $csvPreviewData = null;
    public $csvProcessedData = [];
    public $csvErrors = [];

    // CSV category defaults
    public $csvCategoryDefaults = [
        'major_category_id' => '',
        'category_id' => '',
    ];

    // For displaying the selected category name in the preview
    protected $selectedCategoryName = '';

    // Modals
    public $editingAssetId = null;

    public $confirmingCategoryDeletion = false;
    public $confirmingAssetDeletion = false;
    public $assetToDelete = null;
    public $categoryToDelete = null;
    public $showAssignModal = false;

    public $editingLocationId = null;
    public $confirmingLocationDeletion = false;
    public $locationToDelete = null;

    public $viewingAsset = null;
    public $showAssetDetailsModal = false;

    public $selectAll = false;
    public $hasSelectedAssets = false;



    public function mount()
    {
        $this->loadCategories();
        $this->loadLocations();
        $this->loadUsers();
        $this->refreshAssets();
        $this->minorCategories = collect();
        $this->locations = PamoLocations::orderBy('name')->get();
        $this->users = User::orderBy('name')->get();
    }
    public function loadLocations()
    {
        $this->locations = PamoLocations::where('is_active', true)
            ->orderBy('name')
            ->get();
    }
    public function loadUsers()
    {
        $this->users = User::orderBy('name')->get();
    }
    public function refreshAssets()
    {
        $this->assets = PamoAssets::with(['category', 'location', 'assignedUser'])
            ->orderBy('created_at', 'desc')
            ->get();

        $this->unassignedAssets = PamoAssets::whereNull('location_id')
            ->whereNull('assigned_to')
            ->where('status', 'available')
            ->get();
    }

    public function updatedAssetMajorCategoryId($value)
    {
        if ($value) {
            $this->minorCategories = PamoCategory::where('type', 'minor')
                ->where('parent_id', $value)
                ->orderBy('name')
                ->get();
        } else {
            $this->minorCategories = collect();
        }

        // Always reset the minor category when major category changes
        $this->asset['category_id'] = '';
    }
    public function updateMinorCategories()
    {
        if (!empty($this->asset['major_category_id'])) {
            $this->minorCategories = PamoCategory::where('type', 'minor')
                ->where('parent_id', $this->asset['major_category_id'])
                ->get();
        } else {
            $this->minorCategories = collect([]);
        }
    }

    public function loadCategories()
    {
         $this->categories = PamoCategory::orderBy('type', 'asc')
        ->orderBy('name', 'asc')
        ->get();

        $this->majorCategories = $this->categories->where('type', 'major');
    }
    public function refreshMinorCategories()
    {
         $majorId = $this->asset['major_category_id'];
        Log::info('Refreshing minor categories', ['major_id' => $majorId]);

        if ($majorId) {
            $this->minorCategories = PamoCategory::where('type', 'minor')
                ->where('parent_id', $majorId)
                ->orderBy('name')
                ->get();

            Log::info('Found minor categories', [
                'count' => $this->minorCategories->count(),
                'categories' => $this->minorCategories->pluck('name')
            ]);
        } else {
            $this->minorCategories = collect();
        }
    }


    public function saveCategory($id = null){
        $rules = [
            'category.name' => 'required|string|max:255',
            'category.type' => 'required|in:major,minor',
            'category.parent_id' => 'nullable|exists:pamo_categories,id',
            'category.description' => 'nullable|string|max:1000',
        ];

        if ($this->category['type'] === 'minor') {
            $rules['category.parent_id'] = 'required|exists:pamo_categories,id';
        }
        $this->validate($rules);

        $data = [
            'name' => $this->category['name'],
            'type' => $this->category['type'],
            'description' => $this->category['description'],
            'updated_at' => now(),
        ];

        if ($this->category['type'] === 'major') {
            $data['parent_id'] = null;
        } else {
            $data['parent_id'] = $this->category['parent_id'];
        }

        if ($id) {
            // Update existing category
            PamoCategory::where('id', $id)
                ->update($data);

            $message = 'Category updated successfully!';
        } else {
            // Add created_at for new records
            $data['created_at'] = now();

            // Create new category
            PamoCategory::create($data);

            $message = 'Category added successfully!';
        }

        // Reset the form
        $this->resetCategoryForm();

        // Reload categories
        $this->loadCategories();

        // Show success message
        session()->flash('message', $message);
    }

    public function saveItem()
    {
        $activeTab = request()->input('activeTab', 'asset');

        if ($activeTab === 'asset') {
            $this->saveAsset();
        } else {
            $this->saveConsumable();
        }
    }
    public function saveAsset()
    {
        // Log::info('saveAsset method called', ['asset' => $this->asset]);

        try {
            $this->validate([
                'asset.po_number' => 'nullable|string|max:255',
                'asset.property_tag_number' => 'required|string|max:255',
                'asset.barcode' => 'nullable|string|max:255',
                'asset.brand' => 'nullable|string|max:255',
                'asset.model' => 'nullable|string|max:255',
                'asset.serial_number' => 'nullable|string|max:255',
                'asset.category_id' => 'required|exists:pamo_categories,id',
                'asset.status' => 'required|in:available,Working,in-use,maintenance,disposed',
                'asset.purchase_date' => 'nullable|date',
                'asset.purchase_value' => 'nullable|numeric|min:0',
                'asset.description' => 'nullable|string|max:1000',
            ]);
            // Log::info('Validation passed');

            $purchaseValue = $this->asset['purchase_value'];
            if ($purchaseValue === '' || $purchaseValue === null) {
                $purchaseValue = null;
            }

            $data = [
                'po_number' => $this->asset['po_number'] ?: null,
                'property_tag_number' => $this->asset['property_tag_number'],
                'barcode' => $this->asset['barcode'] ?: null,
                'brand' => $this->asset['brand'] ?: null,
                'model' => $this->asset['model'] ?: null,
                'serial_number' => $this->asset['serial_number'] ?: null,
                'category_id' => $this->asset['category_id'],
                'status' => $this->asset['status'],
                'purchase_date' => $this->asset['purchase_date'] ?: null,
                'purchase_value' => $purchaseValue,
                'description' => $this->asset['description'] ?: null,
            ];

            if ($this->editingAssetId) {
                // Update existing asset
                $asset = PamoAssets::findOrFail($this->editingAssetId);
                $asset->update($data);
                $message = 'Asset updated successfully!';
            } else {
                // Create new asset
                PamoAssets::create($data);
                $message = 'Asset added successfully!';
            }

            $this->resetAssetForm();
            $this->refreshAssets();
            flash()->success($message);

        }

         catch (\Exception $e) {
            Log::error('Error in saveAsset: ' . $e->getMessage());
            // Handle any other unexpected errors
            flash()->error('An unexpected error occurred: ' . $e->getMessage());
            // session()->flash('error', 'An unexpected error occurred: ' . $e->getMessage());
            Log::error('Asset Save Error: ' . $e->getMessage());
        }
    }
    public function openAddItemModal()
    {
        // Reset form before opening modal for a new item
        $this->resetAssetForm();

        // Open the modal
        // $this->dispatch('open-modal', 'add-item-modal');
    }
    public function editAsset($id)
    {
        $this->resetValidation();
        $this->editingAssetId = $id;

        $assetModel = PamoAssets::findOrFail($id);

        // Get the major category ID if available
        $majorCategoryId = null;
        if ($assetModel->category && $assetModel->category->parent) {
            $majorCategoryId = $assetModel->category->parent_id;
        }

        // Populate the asset form with existing data
        $this->asset = [
            'po_number' => $assetModel->po_number,
            'property_tag_number' => $assetModel->property_tag_number,
            'barcode' => $assetModel->barcode,
            'brand' => $assetModel->brand,
            'model' => $assetModel->model,
            'serial_number' => $assetModel->serial_number,
            'category_id' => $assetModel->category_id,
            'major_category_id' => $majorCategoryId,
            'status' => $assetModel->status,
            'purchase_date' => $assetModel->purchase_date ? $assetModel->purchase_date->format('Y-m-d') : null,
            'purchase_value' => $assetModel->purchase_value,
            'description' => $assetModel->description,
            'location_id' => $assetModel->location_id,
            'assigned_to' => $assetModel->assigned_to,
        ];

        // Load the minor categories based on major category
        $this->updateMinorCategories();

        // Set form mode to edit
        // $this->formMode = 'edit';

        // Open the modal
        // $this->dispatch('open-modal', 'add-item-modal');

    }

    public function viewAsset($id)
    {
        $this->viewingAsset = PamoAssets::with(['category', 'category.parent', 'location', 'assignedUser', 'movements' => function($query) {
            $query->orderBy('movement_date', 'desc')->limit(5);
        }])->findOrFail($id);

        $this->showAssetDetailsModal = true;
    }
    public function confirmDeleteAsset($id)
    {
        $this->confirmingAssetDeletion = true;
        $this->assetToDelete = $id;
    }
    public function deleteAsset()
    {
        PamoAssets::find($this->assetToDelete)->delete();

        session()->flash('message', 'Asset deleted successfully!');
        $this->confirmingAssetDeletion = false;
        $this->assetToDelete = null;
        $this->refreshAssets();
    }

    public function saveLocation($id = null)
    {
        $rules = [
            'location.name' => 'required|string|max:255',
            'location.code' => 'nullable|string|max:50',
            'location.address' => 'nullable|string|max:255',
            'location.type' => 'required|string|max:50',
            'location.description' => 'nullable|string|max:1000',
            'location.is_active' => 'boolean',
        ];

        $this->validate($rules);

        $data = [
            'name' => $this->location['name'],
            'code' => $this->location['code'],
            'address' => $this->location['address'],
            'type' => $this->location['type'],
            'description' => $this->location['description'],
            'is_active' => $this->location['is_active'],
        ];

        if ($id) {
            // Update existing location
            PamoLocations::where('id', $id)->update($data);
            $message = 'Location updated successfully!';
        } else {
            // Create new location
            PamoLocations::create($data);
            $message = 'Location added successfully!';
        }

        // Reset form and reload locations
        $this->resetLocationForm();
        $this->loadLocations();

        // Show success message
        session()->flash('message', $message);
    }
    public function resetLocationForm()
    {
        $this->location = [
            'name' => '',
            'code' => '',
            'address' => '',
            'type' => 'office',
            'description' => '',
            'is_active' => true,
        ];
        $this->editingLocationId = null;
    }
    public function deleteLocation()
    {
        // Check if location has assets
        $hasAssets = false;
        /* Uncomment when you have an assets table
        $hasAssets = PamoAssets::where('location_id', $this->locationToDelete)
            ->exists();
        */

        if ($hasAssets) {
            session()->flash('error', 'Cannot delete a location with assigned assets.');
            $this->confirmingLocationDeletion = false;
            return;
        }

        // Delete the location
        PamoLocations::where('id', $this->locationToDelete)->delete();

        session()->flash('message', 'Location deleted successfully!');
        $this->confirmingLocationDeletion = false;
        $this->loadLocations();
    }
    public function confirmDeleteLocation($id)
    {
        $this->confirmingLocationDeletion = true;
        $this->locationToDelete = $id;
    }

    public function resetAssetForm()
    {
        $this->editingAssetId = null;
        $this->asset = [
            'po_number' => '',
            'property_tag_number' => '',
            'barcode' => '',
            'brand' => '',
            'model' => '',
            'serial_number' => '',
            'major_category_id' => '',
            'category_id' => '',
            'status' => 'available',
            'purchase_date' => '',
            'purchase_value' => '',
            'description' => '',
        ];
        $this->minorCategories = collect();

    }
    public function openAssignModal()
    {
        $this->validate([
            'selectedAssets' => 'required|array|min:1',
            'bulkAction' => 'required|in:assign-location,assign-user',
        ]);

        $this->dispatch('open-modal', 'assign-modal');
    }

    public function assignAssets()
    {
        // Validate based on action type
    if ($this->bulkAction === 'assign-location' || $this->bulkAction === 'transfer') {
        $this->validate([
            'assignLocation' => 'required|exists:pamo_locations,id',
        ]);
    }

    if ($this->bulkAction === 'assign-user' || $this->bulkAction === 'transfer') {
        $this->validate([
            'assignToUser' => 'required|exists:users,id',
        ]);
    }

    $count = count($this->selectedAssets);
    $message = '';

    // Process assets based on action type
    foreach ($this->selectedAssets as $assetId) {
        $asset = PamoAssets::find($assetId);
        if (!$asset) continue;

        $movement = new PamoAssetMovement();
        $movement->asset_id = $asset->id;
        $movement->assigned_by = auth()->id();
        $movement->movement_date = now();
        $movement->notes = $this->movementNotes;

        if ($this->bulkAction === 'assign-location') {
            // Record from location
            $movement->from_location_id = $asset->location_id;
            $movement->to_location_id = $this->assignLocation;
            $movement->movement_type = 'location_assignment';

            // Update asset
            $asset->location_id = $this->assignLocation;
            $asset->save();

            $message = "Successfully assigned {$count} assets to location";
        }
        elseif ($this->bulkAction === 'assign-user') {
            $movement->assigned_to = $this->assignToUser;
            $movement->movement_type = 'user_assignment';

            // Update asset
            $asset->assigned_to = $this->assignToUser;
            $asset->status = 'in-use';
            $asset->save();

            $message = "Successfully assigned {$count} assets to user";
        }
        elseif ($this->bulkAction === 'transfer') {
            $movement->from_location_id = $asset->location_id;
            $movement->to_location_id = $this->assignLocation;
            $movement->assigned_to = $this->assignToUser;
            $movement->movement_type = 'transfer';

            // Update asset
            $asset->location_id = $this->assignLocation;
            $asset->assigned_to = $this->assignToUser;
            $asset->status = 'in-use';
            $asset->save();

            $message = "Successfully transferred {$count} assets";
        }

        $movement->save();
    }

    // Reset and show message
    flash()->success($message);
    $this->selectedAssets = [];
    $this->bulkAction = '';
    $this->assignLocation = null;
    $this->assignToUser = null;
    $this->movementNotes = '';

    // Close modal and refresh
    $this->dispatch('close-modal', 'assign-modal');
    }
    public function resetBulkForm()
    {
        $this->selectedAssets = [];
        $this->bulkAction = '';
        $this->assignLocation = null;
        $this->assignToUser = null;
        $this->movementNotes = '';
        $this->hasSelectedAssets = false;
    }

    public function resetCategoryForm()
    {
        $this->category = [
            'name' => '',
            'type' => 'minor',
            'parent_id' => '',
            'description' => '',
        ];
    }

    public function confirmDeleteCategory($id)
    {
        $this->confirmingCategoryDeletion = true;
        $this->categoryToDelete = $id;
    }
    public function deleteCategory()
    {
        // Check if category has children
        $hasChildren = PamoCategory::where('parent_id', $this->categoryToDelete)
            ->exists();

        if ($hasChildren) {
            session()->flash('error', 'Cannot delete a category with subcategories.');
            $this->confirmingCategoryDeletion = false;
            return;
        }

        // Check if category is used by items
        // This depends on your specific table structure
        /*
        $isUsed = DB::table('assets')
            ->where('category_id', $this->categoryToDelete)
            ->exists();

        if ($isUsed) {
            session()->flash('error', 'Cannot delete a category that is in use by inventory items.');
            $this->confirmingCategoryDeletion = false;
            return;
        }
        */

        // Delete the category
        PamoCategory::where('id', $this->categoryToDelete)
            ->delete();

        session()->flash('message', 'Category deleted successfully!');
        $this->confirmingCategoryDeletion = false;
        $this->loadCategories();
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->categoryFilter = '';
        $this->statusFilter = '';
    }

    public function getHasSelectedAssetsProperty()
    {
        return count($this->selectedAssets) > 0;
    }
    public function updatedSelectedAssets()
    {
        $this->hasSelectedAssets = count($this->selectedAssets) > 0;
    }
    public function updatedSelectAll($value)
    {
        if ($value) {
        $this->selectedAssets = $this->assetsList->pluck('id')->map(fn($id) => (string) $id)->toArray();
        } else {
            $this->selectedAssets = [];
        }

        $this->hasSelectedAssets = count($this->selectedAssets) > 0;
    }
    public function bulkAssignLocation()
    {
        // dd('test');
        if (empty($this->selectedAssets)) {
            flash()->warning('No assets selected.');
            return;
        }

        $this->bulkAction = 'assign-location';
        $this->dispatch('open-modal', 'assign-modal');
    }
    public function bulkAssignUser()
    {
        if (empty($this->selectedAssets)) {
            flash()->warning('No assets selected.');
            return;
        }

        $this->bulkAction = 'assign-user';
        $this->dispatch('open-modal', 'assign-modal');
    }
    public function bulkTransfer()
    {
        if (empty($this->selectedAssets)) {
            flash()->warning('No assets selected.');
            return;
        }

        $this->bulkAction = 'transfer';
        $this->dispatch('open-modal', 'assign-modal');
    }
    public function bulkUpdateStatus()
    {
        if (empty($this->selectedAssets)) {
            flash()->warning('No assets selected for bulk action.');
            return;
        }
        $this->dispatch('open-modal', 'update-status-modal');

        // Implement status updating functionality
        // You can reuse your modals or create a new one for status updates
    }
    public function updateBulkStatus($newStatus)
    {
        if (empty($this->selectedAssets)) {
            return;
        }

        foreach ($this->selectedAssets as $assetId) {
            $asset = PamoAssets::find($assetId);
            if ($asset) {
                $asset->update(['status' => $newStatus]);
            }
        }

        // Reset selections and refresh data
        session()->flash('message', count($this->selectedAssets) . ' assets updated to ' . ucfirst($newStatus) . ' status.');
        $this->selectedAssets = [];
        $this->refreshAssets();
        $this->dispatch('close-modal', 'update-status-modal');
    }
    public function getAssetsListProperty()
    {
        // Build a query with filters
        $assetsQuery = PamoAssets::query()
            ->with(['category', 'category.parent', 'location', 'assignedUser']);

        // Apply search filter if provided
        if ($this->search) {
            $assetsQuery->where(function($query) {
                $query->where('property_tag_number', 'like', '%' . $this->search . '%')
                    ->orWhere('barcode', 'like', '%' . $this->search . '%')
                    ->orWhere('serial_number', 'like', '%' . $this->search . '%')
                    ->orWhere('brand', 'like', '%' . $this->search . '%')
                    ->orWhere('model', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%');
            });
        }

        // Apply category filter if provided
        if ($this->categoryFilter) {
            $assetsQuery->whereIn('category_id', function($query) {
                $query->select('id')
                    ->from('pamo_categories')
                    ->where('parent_id', $this->categoryFilter)
                    ->orWhere('id', $this->categoryFilter);
            });
        }

        // Apply status filter if provided
        if ($this->statusFilter) {
            $assetsQuery->where('status', $this->statusFilter);
        }

        // Return paginated results
        return $assetsQuery->orderBy('created_at', 'desc')
            ->paginate($this->perPage);
    }
    public function exportInventory()
    {
        // Basic implementation - you can expand this
        return response()->streamDownload(function() {
            $output = fopen('php://output', 'w');

            // Add CSV headers
            fputcsv($output, [
                'Tag Number',
                'PO Number',
                'Brand',
                'Model',
                'Serial Number',
                'Category',
                'Status',
                'Purchase Date',
                'Purchase Value',
                'Location',
                'Assigned To'
            ]);

            // Get all assets (not paginated)
            $assets = PamoAssets::with(['category', 'location', 'assignedUser'])->get();

            foreach ($assets as $asset) {
                fputcsv($output, [
                    $asset->property_tag_number,
                    $asset->po_number,
                    $asset->brand,
                    $asset->model,
                    $asset->serial_number,
                    $asset->category ? $asset->category->name : 'Uncategorized',
                    ucfirst($asset->status),
                    $asset->purchase_date ? $asset->purchase_date->format('Y-m-d') : '',
                    $asset->purchase_value,
                    $asset->location ? $asset->location->name : '',
                    $asset->assignedUser ? $asset->assignedUser->name : ''
                ]);
            }

            fclose($output);
        }, 'assets-inventory-' . date('Y-m-d') . '.csv');
    }

    public function openBulkAddModal()
    {
        $this->resetBulkItems();
        $this->dispatch('open-modal', 'bulk-add-modal');
    }

    public function resetBulkItems()
    {
        $this->bulkItems = [];
        $this->bulkItemsDefaults = [
            'major_category_id' => '',
            'category_id' => '',
            'status' => 'available',
        ];
        $this->csvFile = null;
        $this->csvPreviewData = null;
        $this->csvProcessedData = [];
        $this->csvErrors = [];
    }

    public function updatedBulkItemsDefaultsMajorCategoryId($value)
    {
        if ($value) {
            $this->minorCategories = PamoCategory::where('type', 'minor')
                ->where('parent_id', $value)
                ->orderBy('name')
                ->get();
        } else {
            $this->minorCategories = collect();
        }

        $this->bulkItemsDefaults['category_id'] = '';
    }

    public function addBulkItem()
    {
        $this->bulkItems[] = [
            'property_tag_number' => '',
            'po_number' => '',
            'brand' => '',
            'model' => '',
            'serial_number' => '',
            'category_id' => $this->bulkItemsDefaults['category_id'],
            'status' => $this->bulkItemsDefaults['status'],
            'description' => '',
        ];
    }

    public function removeBulkItem($index)
    {
        unset($this->bulkItems[$index]);
        $this->bulkItems = array_values($this->bulkItems); // Re-index the array
    }

    public function saveBulkItems()
    {
        if (empty($this->bulkItems) && empty($this->csvProcessedData)) {
            flash()->warning('No items to save.');
            return;
        }

        // If we're using the manual entry
        if (!empty($this->bulkItems)) {
            $this->validate([
                'bulkItems.*.property_tag_number' => 'required|string|max:255',
                'bulkItems.*.category_id' => 'required|exists:pamo_categories,id',
            ]);

            $itemsToSave = $this->bulkItems;
        }
        // If we're using CSV import
        else {
            // Validate that category is selected when using CSV import
            if (empty($this->csvCategoryDefaults['category_id'])) {
                $this->validate([
                    'csvCategoryDefaults.category_id' => 'required',
                ], [
                    'csvCategoryDefaults.category_id.required' => 'Please select a category for the imported items.'
                ]);
                return;
            }

            // Apply the selected category to all CSV items
            foreach ($this->csvProcessedData as &$item) {
                $item['category_id'] = $this->csvCategoryDefaults['category_id'];
            }

            $itemsToSave = $this->csvProcessedData;
        }

        $savedCount = 0;
        $errorCount = 0;

        foreach ($itemsToSave as $item) {
            try {
                // Default values for missing fields
                $item['status'] = $item['status'] ?? 'available';

                PamoAssets::create([
                    'property_tag_number' => $item['property_tag_number'],
                    'po_number' => $item['po_number'] ?? null,
                    'barcode' => $item['barcode'] ?? null,
                    'brand' => $item['brand'] ?? null,
                    'model' => $item['model'] ?? null,
                    'serial_number' => $item['serial_number'] ?? null,
                    'category_id' => $item['category_id'],
                    'status' => $item['status'],
                    'purchase_date' => $item['purchase_date'] ?? null,
                    'purchase_value' => $item['purchase_value'] ?? null,
                    'description' => $item['description'] ?? null,
                ]);

                $savedCount++;
            } catch (\Exception $e) {
                $errorCount++;
                // Log the error
                Log::error('Error saving bulk item: ' . $e->getMessage(), ['item' => $item]);
            }
        }

        // Reset and show message
        $this->resetBulkItems();
        $this->refreshAssets();

        if ($errorCount > 0) {
            flash()->warning("Saved $savedCount items. Failed to save $errorCount items.");
        } else {
            flash()->success("Successfully added $savedCount items to inventory.");
        }

        $this->dispatch('close-modal', 'bulk-add-modal');
    }

    public function processCSV()
    {
        if (!$this->csvFile) {
        return;
    }

    $path = $this->csvFile->getRealPath();
    $file = fopen($path, 'r');

    // Get headers
    $headers = fgetcsv($file);
    $requiredHeaders = ['property_tag_number']; // Only property_tag_number is absolutely required
    $missingHeaders = array_diff($requiredHeaders, $headers);

    if (!empty($missingHeaders)) {
        flash()->error('CSV is missing required headers: ' . implode(', ', $missingHeaders));
        return;
    }

    $data = [];
    $row = 1;

    while (($csvRow = fgetcsv($file)) !== false) {
        $row++;

        if (count($headers) !== count($csvRow)) {
            $this->csvErrors[] = "Row $row has an invalid number of columns";
            continue;
        }

        $item = array_combine($headers, $csvRow);

        // Basic validation - only property_tag_number is required
        if (empty($item['property_tag_number'])) {
            $this->csvErrors[] = "Row $row is missing required property_tag_number field";
            continue;
        }

        // Ensure description exists in the data array even if not in CSV
        if (!isset($item['description'])) {
            $item['description'] = '';
        }

        $data[] = $item;
        }
        if (!empty($this->csvCategoryDefaults['category_id'])) {
            $category = PamoCategory::find($this->csvCategoryDefaults['category_id']);
            $this->selectedCategoryName = $category ? $category->name : '';
        } else {
            $this->selectedCategoryName = '';
        }

        fclose($file);

        $this->csvPreviewData = $data;
        $this->csvProcessedData = $data;

        // Get selected category name for display in preview
        if (!empty($this->csvCategoryDefaults['category_id'])) {
            $category = PamoCategory::find($this->csvCategoryDefaults['category_id']);
            $this->selectedCategoryName = $category ? $category->name : '';
        }

        if (!empty($this->csvErrors)) {
            flash()->warning('CSV processed with errors. Please check the logs.');
        } else {
            flash()->success('CSV processed successfully. Review the data and click "Save All Items" to import.');
        }
    }
    public function getSelectedCategoryNameProperty()
    {
        if (empty($this->csvCategoryDefaults['category_id'])) {
            return '';
        }

        $category = PamoCategory::find($this->csvCategoryDefaults['category_id']);
        return $category ? $category->name : '';
    }

    public function updatedCsvCategoryDefaultsMajorCategoryId($value)
    {
        if ($value) {
            $this->minorCategories = PamoCategory::where('type', 'minor')
                ->where('parent_id', $value)
                ->orderBy('name')
                ->get();
        } else {
            $this->minorCategories = collect();
        }

        $this->csvCategoryDefaults['category_id'] = '';
    }


    public function render()
    {
         // Build a query with filters
        $assetsQuery = PamoAssets::query()
            ->with(['category', 'category.parent', 'location', 'assignedUser']);

        // Apply search filter if provided
        if ($this->search) {
            $assetsQuery->where(function($query) {
                $query->where('property_tag_number', 'like', '%' . $this->search . '%')
                    ->orWhere('barcode', 'like', '%' . $this->search . '%')
                    ->orWhere('serial_number', 'like', '%' . $this->search . '%')
                    ->orWhere('brand', 'like', '%' . $this->search . '%')
                    ->orWhere('model', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%');
            });
        }

        // Apply category filter if provided
        if ($this->categoryFilter) {
            $assetsQuery->whereIn('category_id', function($query) {
                $query->select('id')
                    ->from('pamo_categories')
                    ->where('parent_id', $this->categoryFilter)
                    ->orWhere('id', $this->categoryFilter);
            });
        }

        // Apply status filter if provided
        if ($this->statusFilter) {
            $assetsQuery->where('status', $this->statusFilter);
        }

        // Get paginated results
        $assetsList = $assetsQuery->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        // Get other data needed for the view
        $unassignedAssetsList = $this->unassignedAssets;
        $locationsList = $this->locations;
        $usersList = $this->users;

        return view('livewire.p-a-m-o.inventory', compact(
      'assetsList',
     'unassignedAssetsList',
                'locationsList',
                'usersList'
        ));
    }
}
