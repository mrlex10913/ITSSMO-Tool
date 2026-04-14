<?php

namespace App\Livewire\Assets;

use App\Models\Assets\AssetCategory;
use App\Models\Assets\AssetList;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.enduser')]
class Dashboard extends Component
{
    use WithPagination;

    public string $title = 'Assets Dashboard';

    // Stats
    public array $stats = [
        'total' => 0,
        'deployed' => 0,
        'available' => 0,
        'defective' => 0,
    ];

    // Distribution data
    public array $categoryDistribution = [];

    public array $statusDistribution = [];

    public array $locationDistribution = [];

    // Recent assets
    public array $recentAssets = [];

    // Asset management
    public string $search = '';

    public string $statusFilter = '';

    public string $categoryFilter = '';

    public bool $showAssetModal = false;

    public bool $editMode = false;

    public ?int $editAssetId = null;

    public bool $showDeleteModal = false;

    public ?int $deleteAssetId = null;

    // Category management
    public bool $showCategoryModal = false;

    public bool $editCategoryMode = false;

    public ?int $editCategoryId = null;

    public string $categoryName = '';

    public bool $showDeleteCategoryModal = false;

    public ?int $deleteCategoryId = null;

    // Form fields
    public string $category = '';

    public string $item_barcode = '';

    public string $item_brand = '';

    public string $item_model = '';

    public string $itss_serial = '';

    public string $purch_serial = '';

    public string $location = '';

    public string $status = '';

    public string $assign_to = '';

    protected array $rules = [
        'category' => 'required|exists:asset_categories,id',
        'item_barcode' => 'required|string|max:255',
        'item_brand' => 'nullable|string|max:255',
        'item_model' => 'nullable|string|max:255',
        'itss_serial' => 'required|string|max:255',
        'location' => 'required|string|max:255',
        'status' => 'required|string|max:255',
        'assign_to' => 'required|string|max:255',
    ];

    public function mount(): void
    {
        $this->assign_to = Auth::user()->name ?? '';
        $this->loadDashboardData();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatedCategoryFilter(): void
    {
        $this->resetPage();
    }

    public function loadDashboardData(): void
    {
        $this->loadStats();
        $this->loadCategoryDistribution();
        $this->loadStatusDistribution();
        $this->loadLocationDistribution();
        $this->loadRecentAssets();
    }

    protected function loadStats(): void
    {
        $total = AssetList::count();
        $deployed = AssetList::whereIn('status', ['Deployed', 'In Use', 'Assigned'])->count();
        $available = AssetList::whereIn('status', ['Available', 'Stock', 'In Stock'])->count();
        $defective = AssetList::whereIn('status', ['Defective', 'For Repair', 'Damaged'])->count();

        $this->stats = [
            'total' => $total,
            'deployed' => $deployed,
            'available' => $available,
            'defective' => $defective,
        ];
    }

    protected function loadCategoryDistribution(): void
    {
        $categories = AssetList::join('asset_categories', 'asset_lists.asset_categories_id', '=', 'asset_categories.id')
            ->selectRaw('asset_categories.name, COUNT(*) as count')
            ->groupBy('asset_categories.name')
            ->orderByDesc('count')
            ->limit(10)
            ->get();

        $total = $categories->sum('count') ?: 1;

        $this->categoryDistribution = $categories->map(fn ($item) => [
            'name' => $item->name,
            'count' => $item->count,
            'percentage' => round(($item->count / $total) * 100, 1),
        ])->toArray();
    }

    protected function loadStatusDistribution(): void
    {
        $statuses = AssetList::selectRaw('COALESCE(status, \'Unknown\') as status, COUNT(*) as count')
            ->groupBy('status')
            ->orderByDesc('count')
            ->get();

        $total = $statuses->sum('count') ?: 1;

        $this->statusDistribution = $statuses->map(fn ($item) => [
            'name' => $item->status,
            'count' => $item->count,
            'percentage' => round(($item->count / $total) * 100, 1),
        ])->toArray();
    }

    protected function loadLocationDistribution(): void
    {
        $locations = AssetList::selectRaw('COALESCE(location, \'Unassigned\') as location, COUNT(*) as count')
            ->groupBy('location')
            ->orderByDesc('count')
            ->limit(10)
            ->get();

        $total = $locations->sum('count') ?: 1;

        $this->locationDistribution = $locations->map(fn ($item) => [
            'name' => $item->location,
            'count' => $item->count,
            'percentage' => round(($item->count / $total) * 100, 1),
        ])->toArray();
    }

    protected function loadRecentAssets(): void
    {
        $this->recentAssets = AssetList::with('assetList:id,name')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get()
            ->map(fn ($asset) => [
                'id' => $asset->id,
                'barcode' => $asset->item_barcode,
                'name' => $asset->item_name,
                'model' => $asset->item_model,
                'category' => $asset->assetList?->name ?? 'Uncategorized',
                'status' => $asset->status,
                'location' => $asset->location,
                'created_at' => $asset->created_at?->diffForHumans(),
            ])
            ->toArray();
    }

    public function refreshData(): void
    {
        $this->loadDashboardData();
        flash()->success('Dashboard data refreshed.');
    }

    // Asset CRUD methods
    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->editMode = false;
        $this->showAssetModal = true;
    }

    public function openEditModal(int $assetId): void
    {
        $asset = AssetList::findOrFail($assetId);

        $this->editMode = true;
        $this->editAssetId = $assetId;
        $this->category = (string) $asset->asset_categories_id;
        $this->item_barcode = $asset->item_barcode ?? '';
        $this->item_brand = $asset->item_name ?? '';
        $this->item_model = $asset->item_model ?? '';
        $this->itss_serial = $asset->item_serial_itss ?? '';
        $this->purch_serial = $asset->item_serial_purch ?? '';
        $this->location = $asset->location ?? '';
        $this->status = $asset->status ?? '';
        $this->assign_to = $asset->assigned_to ?? '';

        $this->showAssetModal = true;
    }

    public function saveAsset(): void
    {
        $this->validate();

        if ($this->editMode && $this->editAssetId) {
            $asset = AssetList::findOrFail($this->editAssetId);
            $asset->update([
                'asset_categories_id' => $this->category,
                'item_barcode' => $this->item_barcode,
                'item_name' => $this->item_brand,
                'item_model' => $this->item_model,
                'item_serial_itss' => $this->itss_serial,
                'item_serial_purch' => $this->purch_serial,
                'assigned_to' => $this->assign_to,
                'location' => $this->location,
                'status' => $this->status,
            ]);
            flash()->success('Asset updated successfully.');
        } else {
            AssetList::create([
                'asset_categories_id' => $this->category,
                'item_barcode' => $this->item_barcode,
                'item_name' => $this->item_brand,
                'item_model' => $this->item_model,
                'item_serial_itss' => $this->itss_serial,
                'item_serial_purch' => $this->purch_serial,
                'assigned_to' => $this->assign_to,
                'location' => $this->location,
                'status' => $this->status,
            ]);
            flash()->success('New asset created successfully.');
        }

        $this->showAssetModal = false;
        $this->resetForm();
        $this->loadDashboardData();
    }

    public function confirmDelete(int $assetId): void
    {
        $this->deleteAssetId = $assetId;
        $this->showDeleteModal = true;
    }

    public function deleteAsset(): void
    {
        if ($this->deleteAssetId) {
            AssetList::destroy($this->deleteAssetId);
            flash()->warning('Asset has been deleted.');
            $this->loadDashboardData();
        }

        $this->showDeleteModal = false;
        $this->deleteAssetId = null;
    }

    protected function resetForm(): void
    {
        $this->editAssetId = null;
        $this->category = '';
        $this->item_barcode = '';
        $this->item_brand = '';
        $this->item_model = '';
        $this->itss_serial = '';
        $this->purch_serial = '';
        $this->location = '';
        $this->status = '';
        $this->assign_to = Auth::user()->name ?? '';
        $this->resetValidation();
    }

    // Category CRUD methods
    public function openCreateCategoryModal(): void
    {
        $this->resetCategoryForm();
        $this->showCategoryModal = true;
    }

    public function openEditCategoryModal(int $categoryId): void
    {
        $category = AssetCategory::findOrFail($categoryId);

        $this->editCategoryMode = true;
        $this->editCategoryId = $categoryId;
        $this->categoryName = $category->name;

        $this->showCategoryModal = true;
    }

    public function saveCategory(): void
    {
        $this->validate([
            'categoryName' => 'required|string|max:255',
        ], [
            'categoryName.required' => 'Category name is required.',
        ]);

        if ($this->editCategoryMode && $this->editCategoryId) {
            $category = AssetCategory::findOrFail($this->editCategoryId);
            $category->update(['name' => $this->categoryName]);
            flash()->success('Category updated successfully.');
        } else {
            AssetCategory::create(['name' => $this->categoryName]);
            flash()->success('New category created successfully.');
        }

        $this->showCategoryModal = false;
        $this->resetCategoryForm();
        $this->loadDashboardData();
    }

    public function confirmDeleteCategory(int $categoryId): void
    {
        $this->deleteCategoryId = $categoryId;
        $this->showDeleteCategoryModal = true;
    }

    public function deleteCategory(): void
    {
        if ($this->deleteCategoryId) {
            $category = AssetCategory::find($this->deleteCategoryId);

            // Check if category has assets
            $assetCount = AssetList::where('asset_categories_id', $this->deleteCategoryId)->count();
            if ($assetCount > 0) {
                flash()->error("Cannot delete category. It has {$assetCount} asset(s) assigned.");
                $this->showDeleteCategoryModal = false;
                $this->deleteCategoryId = null;

                return;
            }

            $category?->delete();
            flash()->warning('Category has been deleted.');
            $this->loadDashboardData();
        }

        $this->showDeleteCategoryModal = false;
        $this->deleteCategoryId = null;
    }

    public function resetCategoryForm(): void
    {
        $this->editCategoryMode = false;
        $this->editCategoryId = null;
        $this->categoryName = '';
        $this->resetValidation();
    }

    public function getCategoriesProperty()
    {
        return AssetCategory::orderBy('name')->get();
    }

    public function getAssetsProperty()
    {
        return AssetList::with('assetList:id,name')
            ->when($this->search, fn ($q) => $q->where(function ($query) {
                $query->where('item_barcode', 'like', "%{$this->search}%")
                    ->orWhere('item_name', 'like', "%{$this->search}%")
                    ->orWhere('item_model', 'like', "%{$this->search}%")
                    ->orWhere('item_serial_itss', 'like', "%{$this->search}%")
                    ->orWhere('location', 'like', "%{$this->search}%");
            }))
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))
            ->when($this->categoryFilter, fn ($q) => $q->where('asset_categories_id', $this->categoryFilter))
            ->orderByDesc('created_at')
            ->paginate(10);
    }

    public function render()
    {
        return view('livewire.assets.dashboard');
    }
}
