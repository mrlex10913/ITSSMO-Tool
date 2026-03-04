<?php

namespace App\Livewire\DocumentLibrary;

use App\Models\DocumentLibrary\MasterFile;
use App\Models\DocumentLibrary\StorageLocation;
use App\Services\DocumentLibrary\StorageMigrationService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.enduser')]
class StorageLocations extends Component
{
    use WithPagination;

    public $showModal = false;

    public $showMigrationModal = false;

    public $editingId = null;

    // Form fields
    public $name = '';

    public $disk = '';

    public $path_prefix = '';

    public $driver = 'local';

    public $root_path = '';

    public $max_size_gb = '';

    public $is_default = false;

    public $is_active = true;

    public $is_readonly = false;

    public $description = '';

    // Migration fields
    public $migrationSourceId = null;

    public $migrationDestinationId = null;

    public $migrationDocumentIds = [];

    public $migrationInProgress = false;

    public $migrationResults = null;

    // Filter/search
    public $search = '';

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:100',
            'disk' => 'required|string|max:50|regex:/^[a-z0-9_]+$/',
            'path_prefix' => 'nullable|string|max:255',
            'driver' => 'required|in:local,s3,ftp',
            'root_path' => 'nullable|string|max:500|required_if:driver,local',
            'max_size_gb' => 'nullable|numeric|min:0',
            'is_default' => 'boolean',
            'is_active' => 'boolean',
            'is_readonly' => 'boolean',
            'description' => 'nullable|string',
        ];
    }

    protected $messages = [
        'disk.regex' => 'Disk name must be lowercase letters, numbers, and underscores only.',
        'root_path.required_if' => 'Root path is required for local storage.',
    ];

    public function mount(): void
    {
        // Only admins/developers can manage storage locations
        if (! Auth::user()->hasRole(['administrator', 'developer'])) {
            session()->flash('error', 'You do not have permission to manage storage locations.');
            $this->redirect(route('document-library.dashboard'));
        }
    }

    public function openModal(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->editingId = null;
        $this->resetForm();
    }

    public function edit(int $id): void
    {
        $location = StorageLocation::findOrFail($id);
        $this->editingId = $id;
        $this->name = $location->name;
        $this->disk = $location->disk;
        $this->path_prefix = $location->path_prefix;
        $this->driver = $location->driver;
        $this->root_path = $location->root_path;
        $this->max_size_gb = $location->max_size_bytes ? round($location->max_size_bytes / 1073741824, 2) : '';
        $this->is_default = $location->is_default;
        $this->is_active = $location->is_active;
        $this->is_readonly = $location->is_readonly;
        $this->description = $location->description;
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate();

        // Check for duplicate disk name
        $existingQuery = StorageLocation::where('disk', $this->disk);
        if ($this->editingId) {
            $existingQuery->where('id', '!=', $this->editingId);
        }
        if ($existingQuery->exists()) {
            $this->addError('disk', 'A storage location with this disk name already exists.');

            return;
        }

        // Validate local path exists (if local driver)
        if ($this->driver === 'local' && $this->root_path) {
            if (! is_dir($this->root_path)) {
                $this->addError('root_path', 'The specified directory does not exist: '.$this->root_path);

                return;
            }
            if (! is_writable($this->root_path) && ! $this->is_readonly) {
                $this->addError('root_path', 'The specified directory is not writable: '.$this->root_path);

                return;
            }
        }

        $data = [
            'name' => $this->name,
            'disk' => $this->disk,
            'path_prefix' => $this->path_prefix ?: null,
            'driver' => $this->driver,
            'root_path' => $this->driver === 'local' ? $this->root_path : null,
            'max_size_bytes' => $this->max_size_gb ? (int) ($this->max_size_gb * 1073741824) : null,
            'is_default' => $this->is_default,
            'is_active' => $this->is_active,
            'is_readonly' => $this->is_readonly,
            'description' => $this->description ?: null,
        ];

        if ($this->editingId) {
            $location = StorageLocation::findOrFail($this->editingId);
            $location->update($data);
            $message = 'Storage location updated.';
        } else {
            $data['created_by'] = Auth::id();
            $location = StorageLocation::create($data);
            $message = 'Storage location created.';
        }

        // Handle default status
        if ($this->is_default) {
            $location->setAsDefault();
        }

        session()->flash('success', $message);
        $this->closeModal();
    }

    public function delete(int $id): void
    {
        $location = StorageLocation::findOrFail($id);

        // Check if location has documents
        $documentCount = $location->documents()->count();
        if ($documentCount > 0) {
            session()->flash('error', "Cannot delete storage location with {$documentCount} documents. Migrate documents first.");

            return;
        }

        $location->delete();
        session()->flash('success', 'Storage location deleted.');
    }

    public function setDefault(int $id): void
    {
        $location = StorageLocation::findOrFail($id);
        $location->setAsDefault();
        session()->flash('success', "{$location->name} is now the default storage location.");
    }

    public function recalculateSpace(int $id): void
    {
        $location = StorageLocation::findOrFail($id);
        $newSize = $location->recalculateUsedSpace();
        session()->flash('success', "Recalculated: {$location->formatted_used_size} used.");
    }

    // =========================================================================
    // MIGRATION METHODS
    // =========================================================================

    public function openMigrationModal(?int $sourceId = null): void
    {
        $this->migrationSourceId = $sourceId;
        $this->migrationDestinationId = null;
        $this->migrationDocumentIds = [];
        $this->migrationResults = null;
        $this->showMigrationModal = true;
    }

    public function closeMigrationModal(): void
    {
        $this->showMigrationModal = false;
        $this->migrationSourceId = null;
        $this->migrationDestinationId = null;
        $this->migrationDocumentIds = [];
        $this->migrationResults = null;
        $this->migrationInProgress = false;
    }

    public function migrateDocuments(): void
    {
        if (! $this->migrationDestinationId) {
            session()->flash('error', 'Please select a destination storage location.');

            return;
        }

        $destination = StorageLocation::findOrFail($this->migrationDestinationId);
        $service = new StorageMigrationService;

        $this->migrationInProgress = true;

        // Determine what to migrate
        if ($this->migrationSourceId === 'legacy') {
            // Migrate legacy documents (no storage_location_id)
            $results = $service->migrateLegacyDocuments($destination, 100);
        } elseif ($this->migrationSourceId) {
            // Migrate all from specific source
            $source = StorageLocation::findOrFail($this->migrationSourceId);
            $results = $service->migrateAllFromLocation($source, $destination);
        } else {
            // Should not happen
            $results = ['total' => 0, 'success' => 0, 'failed' => 0, 'errors' => ['No source selected']];
        }

        $this->migrationInProgress = false;
        $this->migrationResults = $results;

        if ($results['failed'] === 0 && $results['success'] > 0) {
            session()->flash('success', "Successfully migrated {$results['success']} documents.");
        } elseif ($results['success'] > 0) {
            session()->flash('warning', "Migrated {$results['success']} documents, {$results['failed']} failed.");
        } else {
            session()->flash('error', 'Migration failed. Check the errors below.');
        }
    }

    public function getMigrationStatsProperty(): array
    {
        $service = new StorageMigrationService;

        return $service->getMigrationStats();
    }

    // =========================================================================
    // HELPERS
    // =========================================================================

    protected function resetForm(): void
    {
        $this->name = '';
        $this->disk = '';
        $this->path_prefix = '';
        $this->driver = 'local';
        $this->root_path = '';
        $this->max_size_gb = '';
        $this->is_default = false;
        $this->is_active = true;
        $this->is_readonly = false;
        $this->description = '';
    }

    public function getLocationsProperty()
    {
        return StorageLocation::query()
            ->withCount('documents')
            ->when($this->search, function ($q) {
                $q->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('disk', 'like', '%'.$this->search.'%');
            })
            ->orderByDesc('is_default')
            ->orderBy('name')
            ->paginate(10);
    }

    public function getLegacyDocumentCountProperty(): int
    {
        return MasterFile::whereNull('storage_location_id')->count();
    }

    public function getAvailableDestinationsProperty()
    {
        return StorageLocation::where('is_active', true)
            ->where('is_readonly', false)
            ->when($this->migrationSourceId && $this->migrationSourceId !== 'legacy', function ($q) {
                $q->where('id', '!=', $this->migrationSourceId);
            })
            ->orderBy('name')
            ->get();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.document-library.storage-locations');
    }
}
