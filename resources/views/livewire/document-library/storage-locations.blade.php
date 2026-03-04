<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Storage Locations</h1>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Manage file storage directories and migrate documents between locations</p>
        </div>
        <div class="mt-4 sm:mt-0 flex space-x-3">
            <a href="{{ route('document-library.dashboard') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                <span class="material-symbols-sharp text-sm mr-2">arrow_back</span>
                Back to Dashboard
            </a>
            <button wire:click="openMigrationModal" class="inline-flex items-center px-4 py-2 border border-purple-300 dark:border-purple-600 rounded-lg shadow-sm text-sm font-medium text-purple-700 dark:text-purple-300 bg-white dark:bg-gray-800 hover:bg-purple-50 dark:hover:bg-purple-900 transition-colors">
                <span class="material-symbols-sharp text-sm mr-2">drive_file_move</span>
                Migrate Files
            </button>
            <button wire:click="openModal" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white hover:bg-blue-700 transition-colors">
                <span class="material-symbols-sharp text-sm mr-2">add</span>
                Add Storage
            </button>
        </div>
    </div>

    <!-- Storage Overview Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-lg bg-blue-100 dark:bg-blue-900 flex items-center justify-center">
                    <span class="material-symbols-sharp text-blue-600 dark:text-blue-400">storage</span>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Locations</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $this->locations->total() }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-lg bg-green-100 dark:bg-green-900 flex items-center justify-center">
                    <span class="material-symbols-sharp text-green-600 dark:text-green-400">check_circle</span>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Active Locations</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $this->locations->where('is_active', true)->count() }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-lg bg-yellow-100 dark:bg-yellow-900 flex items-center justify-center">
                    <span class="material-symbols-sharp text-yellow-600 dark:text-yellow-400">warning</span>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Legacy Documents</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $this->legacyDocumentCount }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-lg bg-purple-100 dark:bg-purple-900 flex items-center justify-center">
                    <span class="material-symbols-sharp text-purple-600 dark:text-purple-400">folder</span>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Documents</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $this->locations->sum('documents_count') + $this->legacyDocumentCount }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Legacy Documents Warning -->
    @if($this->legacyDocumentCount > 0)
    <div class="bg-yellow-50 dark:bg-yellow-900/30 border border-yellow-200 dark:border-yellow-700 rounded-xl p-4">
        <div class="flex items-start">
            <span class="material-symbols-sharp text-yellow-600 dark:text-yellow-400 mr-3">info</span>
            <div class="flex-1">
                <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-300">Legacy Documents Detected</h3>
                <p class="mt-1 text-sm text-yellow-700 dark:text-yellow-400">
                    You have {{ $this->legacyDocumentCount }} documents stored in the default public storage that have not been assigned to a storage location.
                    <button wire:click="openMigrationModal('legacy')" class="underline hover:no-underline">Migrate them now</button>.
                </p>
            </div>
        </div>
    </div>
    @endif

    <!-- Search -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-3 sm:space-y-0">
            <div class="flex-1 max-w-lg">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <span class="material-symbols-sharp text-gray-400 text-sm">search</span>
                    </div>
                    <input type="text"
                           wire:model.live.debounce.300ms="search"
                           class="block w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg leading-5 bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Search storage locations...">
                </div>
            </div>
        </div>
    </div>

    <!-- Storage Locations Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($this->locations as $location)
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-md transition-shadow {{ $location->is_default ? 'ring-2 ring-blue-500' : '' }}">
            <!-- Location Header -->
            <div class="p-4 border-b border-gray-200 dark:border-gray-700 {{ $location->is_default ? 'bg-blue-50 dark:bg-blue-900/30' : 'bg-gray-50 dark:bg-gray-700/50' }}">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-lg flex items-center justify-center {{ $location->is_active ? 'bg-green-100 dark:bg-green-900' : 'bg-gray-100 dark:bg-gray-700' }}">
                            <span class="material-symbols-sharp {{ $location->is_active ? 'text-green-600 dark:text-green-400' : 'text-gray-400' }}">
                                {{ $location->driver === 'local' ? 'folder' : ($location->driver === 's3' ? 'cloud' : 'dns') }}
                            </span>
                        </div>
                        <div>
                            <div class="flex items-center space-x-2">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $location->name }}</h3>
                                @if($location->is_default)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300">Default</span>
                                @endif
                                @if($location->is_readonly)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">Read-only</span>
                                @endif
                            </div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $location->documents_count }} documents</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-1">
                        <button wire:click="edit({{ $location->id }})"
                                class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/50 rounded-lg transition-colors">
                            <span class="material-symbols-sharp text-sm">edit</span>
                        </button>
                        @if(!$location->is_default)
                        <button wire:click="delete({{ $location->id }})"
                                wire:confirm="Are you sure you want to delete this storage location?"
                                class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/50 rounded-lg transition-colors">
                            <span class="material-symbols-sharp text-sm">delete</span>
                        </button>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Location Content -->
            <div class="p-4 space-y-3">
                <!-- Storage Usage -->
                @if($location->max_size_bytes)
                <div>
                    <div class="flex items-center justify-between text-sm mb-1">
                        <span class="text-gray-500 dark:text-gray-400">Storage Used</span>
                        <span class="font-medium text-gray-900 dark:text-white">{{ $location->formatted_used_size }} / {{ $location->formatted_max_size }}</span>
                    </div>
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                        @php $percentage = $location->usage_percentage ?? 0; @endphp
                        <div class="h-2 rounded-full {{ $percentage > 90 ? 'bg-red-500' : ($percentage > 70 ? 'bg-yellow-500' : 'bg-green-500') }}"
                             style="width: {{ min($percentage, 100) }}%"></div>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $location->formatted_available_space }} available</p>
                </div>
                @else
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-500 dark:text-gray-400">Storage Used</span>
                    <span class="font-medium text-gray-900 dark:text-white">{{ $location->formatted_used_size }} (Unlimited)</span>
                </div>
                @endif

                <!-- Location Details -->
                <div class="space-y-2 text-xs">
                    <div class="flex items-center justify-between">
                        <span class="text-gray-500 dark:text-gray-400">Driver:</span>
                        <span class="font-medium text-gray-900 dark:text-white uppercase">{{ $location->driver }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-500 dark:text-gray-400">Disk:</span>
                        <code class="px-2 py-0.5 bg-gray-100 dark:bg-gray-700 rounded text-gray-700 dark:text-gray-300">{{ $location->disk }}</code>
                    </div>
                    @if($location->root_path)
                    <div>
                        <span class="text-gray-500 dark:text-gray-400">Path:</span>
                        <p class="mt-1 px-2 py-1 bg-gray-100 dark:bg-gray-700 rounded text-gray-700 dark:text-gray-300 font-mono text-xs truncate" title="{{ $location->root_path }}">
                            {{ $location->root_path }}
                        </p>
                    </div>
                    @endif
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-between pt-2 border-t border-gray-200 dark:border-gray-700">
                    <div class="flex space-x-2">
                        @if(!$location->is_default && $location->is_active && !$location->is_readonly)
                        <button wire:click="setDefault({{ $location->id }})"
                                class="text-xs text-blue-600 dark:text-blue-400 hover:underline">
                            Set as Default
                        </button>
                        @endif
                        <button wire:click="recalculateSpace({{ $location->id }})"
                                class="text-xs text-gray-600 dark:text-gray-400 hover:underline">
                            Recalculate
                        </button>
                    </div>
                    @if($location->documents_count > 0 && !$location->is_readonly)
                    <button wire:click="openMigrationModal({{ $location->id }})"
                            class="text-xs text-purple-600 dark:text-purple-400 hover:underline">
                        Migrate Files
                    </button>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-3 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-12 text-center">
            <span class="material-symbols-sharp text-gray-400 text-6xl mb-4">storage</span>
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">No Storage Locations</h3>
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Create your first storage location to organize document storage.</p>
            <button wire:click="openModal" class="mt-4 inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                <span class="material-symbols-sharp text-sm mr-2">add</span>
                Add Storage Location
            </button>
        </div>
        @endforelse
    </div>

    {{ $this->locations->links() }}

    <!-- Create/Edit Modal -->
    @if($showModal)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 dark:bg-gray-900 bg-opacity-75 dark:bg-opacity-75 transition-opacity" wire:click="closeModal"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form wire:submit="save">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                            {{ $editingId ? 'Edit Storage Location' : 'Add Storage Location' }}
                        </h3>
                    </div>

                    <div class="px-6 py-4 space-y-4">
                        <!-- Name -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Name *</label>
                            <input type="text" wire:model="name"
                                   class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="e.g., Primary Storage, Archive Drive">
                            @error('name') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                        </div>

                        <!-- Disk Name -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Disk Name *</label>
                            <input type="text" wire:model="disk"
                                   class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white font-mono focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="e.g., documents_primary, archive_2025"
                                   {{ $editingId ? 'disabled' : '' }}>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Lowercase letters, numbers, and underscores only.</p>
                            @error('disk') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                        </div>

                        <!-- Driver -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Storage Driver *</label>
                            <select wire:model.live="driver"
                                    class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-blue-500 focus:border-blue-500">
                                <option value="local">Local Filesystem</option>
                                <option value="s3">Amazon S3 / S3-Compatible</option>
                                <option value="ftp">FTP Server</option>
                            </select>
                            @error('driver') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                        </div>

                        <!-- Root Path (for local) -->
                        @if($driver === 'local')
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Root Path *</label>
                            <input type="text" wire:model="root_path"
                                   class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white font-mono focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="e.g., D:\DocumentStorage or /mnt/documents">
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Absolute path to the storage directory.</p>
                            @error('root_path') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                        </div>
                        @else
                        <div class="p-3 bg-yellow-50 dark:bg-yellow-900/30 rounded-lg">
                            <p class="text-sm text-yellow-700 dark:text-yellow-300">
                                <span class="material-symbols-sharp text-sm align-middle mr-1">info</span>
                                For {{ strtoupper($driver) }} storage, configure credentials in <code>config/filesystems.php</code>.
                            </p>
                        </div>
                        @endif

                        <!-- Path Prefix -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Path Prefix</label>
                            <input type="text" wire:model="path_prefix"
                                   class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="e.g., documents or archive/2025">
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Optional subfolder within the storage.</p>
                            @error('path_prefix') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                        </div>

                        <!-- Max Size -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Maximum Size (GB)</label>
                            <input type="number" wire:model="max_size_gb" step="0.01" min="0"
                                   class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="Leave empty for unlimited">
                            @error('max_size_gb') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                        </div>

                        <!-- Description -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description</label>
                            <textarea wire:model="description" rows="2"
                                      class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-blue-500 focus:border-blue-500"
                                      placeholder="Optional notes about this storage location"></textarea>
                        </div>

                        <!-- Toggles -->
                        <div class="grid grid-cols-3 gap-4">
                            <label class="flex items-center space-x-2">
                                <input type="checkbox" wire:model="is_default" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <span class="text-sm text-gray-700 dark:text-gray-300">Default</span>
                            </label>
                            <label class="flex items-center space-x-2">
                                <input type="checkbox" wire:model="is_active" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <span class="text-sm text-gray-700 dark:text-gray-300">Active</span>
                            </label>
                            <label class="flex items-center space-x-2">
                                <input type="checkbox" wire:model="is_readonly" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <span class="text-sm text-gray-700 dark:text-gray-300">Read-only</span>
                            </label>
                        </div>
                    </div>

                    <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700/50 flex justify-end space-x-3">
                        <button type="button" wire:click="closeModal"
                                class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            Cancel
                        </button>
                        <button type="submit"
                                class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">
                            {{ $editingId ? 'Update' : 'Create' }} Storage Location
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    <!-- Migration Modal -->
    @if($showMigrationModal)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 dark:bg-gray-900 bg-opacity-75 dark:bg-opacity-75 transition-opacity" wire:click="closeMigrationModal"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                        <span class="material-symbols-sharp align-middle mr-2">drive_file_move</span>
                        Migrate Documents
                    </h3>
                </div>

                <div class="px-6 py-4 space-y-4">
                    @if($migrationResults)
                    <!-- Migration Results -->
                    <div class="p-4 rounded-lg {{ $migrationResults['failed'] === 0 ? 'bg-green-50 dark:bg-green-900/30' : 'bg-yellow-50 dark:bg-yellow-900/30' }}">
                        <h4 class="font-medium {{ $migrationResults['failed'] === 0 ? 'text-green-800 dark:text-green-300' : 'text-yellow-800 dark:text-yellow-300' }}">
                            Migration Complete
                        </h4>
                        <ul class="mt-2 text-sm space-y-1">
                            <li>Total: {{ $migrationResults['total'] }}</li>
                            <li>Success: {{ $migrationResults['success'] }}</li>
                            <li>Failed: {{ $migrationResults['failed'] }}</li>
                            <li>Skipped: {{ $migrationResults['skipped'] ?? 0 }}</li>
                        </ul>
                        @if(count($migrationResults['errors'] ?? []) > 0)
                        <div class="mt-3">
                            <p class="text-sm font-medium text-red-700 dark:text-red-300">Errors:</p>
                            <ul class="mt-1 text-xs text-red-600 dark:text-red-400 max-h-32 overflow-y-auto">
                                @foreach($migrationResults['errors'] as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif
                    </div>
                    @else
                    <!-- Source -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Source</label>
                        @if($migrationSourceId === 'legacy')
                        <div class="p-3 bg-yellow-50 dark:bg-yellow-900/30 rounded-lg">
                            <p class="text-sm text-yellow-700 dark:text-yellow-300">
                                <span class="material-symbols-sharp text-sm align-middle mr-1">folder</span>
                                Legacy Documents ({{ $this->legacyDocumentCount }} files without assigned storage)
                            </p>
                        </div>
                        @elseif($migrationSourceId)
                        @php $source = \App\Models\DocumentLibrary\StorageLocation::find($migrationSourceId); @endphp
                        <div class="p-3 bg-blue-50 dark:bg-blue-900/30 rounded-lg">
                            <p class="text-sm text-blue-700 dark:text-blue-300">
                                <span class="material-symbols-sharp text-sm align-middle mr-1">storage</span>
                                {{ $source?->name ?? 'Unknown' }} ({{ $source?->documents_count ?? 0 }} documents)
                            </p>
                        </div>
                        @else
                        <select wire:model.live="migrationSourceId"
                                class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                            <option value="">Select source...</option>
                            @if($this->legacyDocumentCount > 0)
                            <option value="legacy">Legacy Documents ({{ $this->legacyDocumentCount }})</option>
                            @endif
                            @foreach($this->locations as $loc)
                            @if($loc->documents_count > 0)
                            <option value="{{ $loc->id }}">{{ $loc->name }} ({{ $loc->documents_count }})</option>
                            @endif
                            @endforeach
                        </select>
                        @endif
                    </div>

                    <!-- Destination -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Destination *</label>
                        <select wire:model="migrationDestinationId"
                                class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                            <option value="">Select destination...</option>
                            @foreach($this->availableDestinations as $dest)
                            <option value="{{ $dest->id }}">{{ $dest->name }} ({{ $dest->formatted_available_space }} available)</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            <span class="material-symbols-sharp text-sm align-middle mr-1">info</span>
                            Files will be moved to the new location. This process may take a while for large numbers of documents.
                        </p>
                    </div>
                    @endif
                </div>

                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700/50 flex justify-end space-x-3">
                    <button type="button" wire:click="closeMigrationModal"
                            class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        {{ $migrationResults ? 'Close' : 'Cancel' }}
                    </button>
                    @if(!$migrationResults)
                    <button type="button" wire:click="migrateDocuments"
                            wire:confirm="Are you sure you want to migrate these documents? This action will move files to a new location."
                            wire:loading.attr="disabled"
                            wire:loading.class="opacity-50 cursor-wait"
                            class="px-4 py-2 text-sm font-medium text-white bg-purple-600 rounded-lg hover:bg-purple-700 transition-colors disabled:opacity-50">
                        <span wire:loading.remove wire:target="migrateDocuments">
                            <span class="material-symbols-sharp text-sm align-middle mr-1">drive_file_move</span>
                            Start Migration
                        </span>
                        <span wire:loading wire:target="migrateDocuments">
                            <span class="material-symbols-sharp text-sm align-middle mr-1 animate-spin">progress_activity</span>
                            Migrating...
                        </span>
                    </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
