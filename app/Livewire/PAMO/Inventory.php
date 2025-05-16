<?php

namespace App\Livewire\PAMO;

use App\Models\PAMO\PamoAssetMovement;
use App\Models\PAMO\PamoAssets;
use App\Models\PAMO\PamoCategory;
use App\Models\PAMO\PamoLocations;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithPagination;

class Inventory extends Component
{
    use WithPagination;

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


    public function mount()
    {
        $this->loadCategories();
        $this->loadLocations();
        $this->loadUsers();
        $this->refreshAssets();
        $this->minorCategories = collect();
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

    public function loadCategories()
    {
        // Load all categories
        // $this->categories = PamoCategory::orderBy('type')
        //     ->orderBy('name')
        //     ->get();

        // // Load major categories for dropdowns
        // $this->majorCategories = PamoCategory::where('type', 'major')
        //     ->orderBy('name')
        //     ->get();

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
        Log::info('saveAsset method called', ['asset' => $this->asset]);

        try {
            $this->validate([
                'asset.po_number' => 'nullable|string|max:255',
                'asset.property_tag_number' => 'required|string|max:255',
                'asset.barcode' => 'nullable|string|max:255',
                'asset.brand' => 'nullable|string|max:255',
                'asset.model' => 'nullable|string|max:255',
                'asset.serial_number' => 'nullable|string|max:255',
                'asset.category_id' => 'required|exists:pamo_categories,id',
                'asset.status' => 'required|in:available,in-use,maintenance,disposed',
                'asset.purchase_date' => 'nullable|date',
                'asset.purchase_value' => 'nullable|numeric|min:0',
                'asset.description' => 'nullable|string|max:1000',
            ]);
            Log::info('Validation passed');

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
    public function editAsset($id)
    {
        $asset = PamoAssets::findOrFail($id);

        // Get major category id from category relation
        $majorCategoryId = null;
        if ($asset->category && $asset->category->parent_id) {
            $majorCategoryId = $asset->category->parent_id;
        }

        $this->asset = [
            'po_number' => $asset->po_number,
            'property_tag_number' => $asset->property_tag_number,
            'barcode' => $asset->barcode,
            'brand' => $asset->brand,
            'model' => $asset->model,
            'serial_number' => $asset->serial_number,
            'major_category_id' => $majorCategoryId,
            'category_id' => $asset->category_id,
            'status' => $asset->status,
            'purchase_date' => $asset->purchase_date,
            'purchase_value' => $asset->purchase_value,
            'description' => $asset->description,
        ];

        $this->editingAssetId = $asset->id;

        // Open the modal
        $this->dispatch('open-modal', 'add-item-modal');
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
        $this->editingAssetId = null;
    }
    public function openAssignModal()
    {
        $this->validate([
            'selectedAssets' => 'required|array|min:1',
            'bulkAction' => 'required|in:assign-location,assign-user',
        ]);

        $this->showAssignModal = true;
    }

    public function assignAssets()
    {
        if ($this->bulkAction === 'assign-location') {
            $this->validate([
                'assignLocation' => 'required|exists:locations,id',
                'movementNotes' => 'nullable|string|max:1000',
            ]);

            foreach ($this->selectedAssets as $assetId) {
                $asset = PamoAssets::find($assetId);
                if ($asset) {
                    // Record the movement
                    PamoAssetMovement::create([
                        'asset_id' => $asset->id,
                        'from_location_id' => $asset->location_id,
                        'to_location_id' => $this->assignLocation,
                        'assigned_by' => Auth::id(),
                        'movement_date' => now(),
                        'notes' => $this->movementNotes,
                        'movement_type' => 'transfer',
                    ]);

                    // Update the asset
                    $asset->update([
                        'location_id' => $this->assignLocation,
                        'assigned_to' => null, // Clear any user assignment
                    ]);
                }
            }

            session()->flash('message', count($this->selectedAssets) . ' assets assigned to location successfully');
        } elseif ($this->bulkAction === 'assign-user') {
            $this->validate([
                'assignToUser' => 'required|exists:users,id',
                'movementNotes' => 'nullable|string|max:1000',
            ]);

            foreach ($this->selectedAssets as $assetId) {
                $asset = PamoAssets::find($assetId);
                if ($asset) {
                    // Record the movement
                    PamoAssetMovement::create([
                        'asset_id' => $asset->id,
                        'assigned_by' => Auth::id(),
                        'assigned_to' => $this->assignToUser,
                        'movement_date' => now(),
                        'notes' => $this->movementNotes,
                        'movement_type' => 'assignment',
                    ]);

                    // Update the asset
                    $asset->update([
                        'assigned_to' => $this->assignToUser,
                        'status' => 'in-use',
                    ]);
                }
            }

            session()->flash('message', count($this->selectedAssets) . ' assets assigned to user successfully');
        }

        // Reset and refresh
        $this->selectedAssets = [];
        $this->bulkAction = '';
        $this->assignLocation = null;
        $this->assignToUser = null;
        $this->movementNotes = '';
        $this->showAssignModal = false;
        $this->refreshAssets();
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
