<div class="flex-1 flex flex-col overflow-hidden">
    <main class="flex-1 overflow-y-auto bg-gray-50 p-4">
        {{-- Header --}}
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-800">{{ $title }}</h2>
            <p class="text-gray-600">Equipment inventory overview and statistics</p>
        </div>

        {{-- Action Bar --}}
        <div class="bg-white rounded-lg shadow p-4 mb-6">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div class="text-sm text-gray-500">
                    Showing real-time inventory data
                </div>
                <div class="flex gap-2">
                    <button wire:click="openCreateModal" class="bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-medium py-2 px-4 rounded flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Add Asset
                    </button>
                    <button wire:click="openCreateCategoryModal" class="bg-indigo-500 hover:bg-indigo-600 text-white text-sm font-medium py-2 px-4 rounded flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                        </svg>
                        Manage Categories
                    </button>
                    <button wire:click="refreshData" wire:loading.attr="disabled" class="bg-orange-500 hover:bg-orange-600 disabled:opacity-50 text-white text-sm font-medium py-2 px-4 rounded flex items-center gap-2">
                        <svg wire:loading.remove wire:target="refreshData" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        <svg wire:loading wire:target="refreshData" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        Refresh
                    </button>
                </div>
            </div>
        </div>

        {{-- Stats Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            {{-- Total Assets --}}
            <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-blue-600">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Total Assets</p>
                        <p class="text-3xl font-bold text-blue-600">{{ number_format($stats['total']) }}</p>
                        <p class="text-gray-500 text-sm mt-1">All registered equipment</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                </div>
            </div>

            {{-- Deployed --}}
            <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-green-600">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Deployed</p>
                        <p class="text-3xl font-bold text-green-600">{{ number_format($stats['deployed']) }}</p>
                        <p class="text-gray-500 text-sm mt-1">{{ $stats['total'] > 0 ? round(($stats['deployed'] / $stats['total']) * 100, 1) : 0 }}% of total</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            {{-- Available --}}
            <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-yellow-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Available</p>
                        <p class="text-3xl font-bold text-yellow-500">{{ number_format($stats['available']) }}</p>
                        <p class="text-gray-500 text-sm mt-1">Ready for deployment</p>
                    </div>
                    <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                    </div>
                </div>
            </div>

            {{-- Defective --}}
            <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-red-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Defective</p>
                        <p class="text-3xl font-bold text-red-500">{{ number_format($stats['defective']) }}</p>
                        <p class="text-gray-500 text-sm mt-1">Needs attention</p>
                    </div>
                    <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        {{-- Distribution Charts --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            {{-- Category Distribution --}}
            <div class="bg-white rounded-lg shadow">
                <div class="p-4 border-b border-gray-200">
                    <h3 class="font-semibold text-gray-800">Assets by Category</h3>
                </div>
                <div class="p-4">
                    @if(count($categoryDistribution) > 0)
                        <div class="space-y-3">
                            @foreach($categoryDistribution as $category)
                                <div>
                                    <div class="flex justify-between text-sm mb-1">
                                        <span class="font-medium text-gray-700">{{ $category['name'] }}</span>
                                        <span class="text-gray-500">{{ $category['count'] }} ({{ $category['percentage'] }}%)</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-orange-500 h-2 rounded-full transition-all duration-500" style="width: {{ $category['percentage'] }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-8">No category data available</p>
                    @endif
                </div>
            </div>

            {{-- Status Distribution --}}
            <div class="bg-white rounded-lg shadow">
                <div class="p-4 border-b border-gray-200">
                    <h3 class="font-semibold text-gray-800">Assets by Status</h3>
                </div>
                <div class="p-4">
                    @if(count($statusDistribution) > 0)
                        <div class="space-y-3">
                            @foreach($statusDistribution as $status)
                                @php
                                    $statusColors = [
                                        'Deployed' => 'bg-green-500',
                                        'In Use' => 'bg-green-500',
                                        'Assigned' => 'bg-green-500',
                                        'Available' => 'bg-blue-500',
                                        'Stock' => 'bg-blue-500',
                                        'In Stock' => 'bg-blue-500',
                                        'Defective' => 'bg-red-500',
                                        'For Repair' => 'bg-red-500',
                                        'Damaged' => 'bg-red-500',
                                    ];
                                    $barColor = $statusColors[$status['name']] ?? 'bg-gray-400';
                                @endphp
                                <div>
                                    <div class="flex justify-between text-sm mb-1">
                                        <span class="font-medium text-gray-700">{{ $status['name'] }}</span>
                                        <span class="text-gray-500">{{ $status['count'] }} ({{ $status['percentage'] }}%)</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="{{ $barColor }} h-2 rounded-full transition-all duration-500" style="width: {{ $status['percentage'] }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-8">No status data available</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Location Distribution & Recent Assets --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Location Distribution --}}
            <div class="bg-white rounded-lg shadow">
                <div class="p-4 border-b border-gray-200">
                    <h3 class="font-semibold text-gray-800">Top Locations</h3>
                </div>
                <div class="p-4">
                    @if(count($locationDistribution) > 0)
                        <div class="space-y-3">
                            @foreach($locationDistribution as $location)
                                <div>
                                    <div class="flex justify-between text-sm mb-1">
                                        <span class="font-medium text-gray-700">{{ $location['name'] }}</span>
                                        <span class="text-gray-500">{{ $location['count'] }} ({{ $location['percentage'] }}%)</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-indigo-500 h-2 rounded-full transition-all duration-500" style="width: {{ $location['percentage'] }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-8">No location data available</p>
                    @endif
                </div>
            </div>

            {{-- Recent Assets --}}
            <div class="bg-white rounded-lg shadow">
                <div class="p-4 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="font-semibold text-gray-800">Recently Added</h3>
                </div>
                <div class="divide-y divide-gray-100">
                    @forelse($recentAssets as $asset)
                        <div class="p-4 hover:bg-gray-50 transition-colors">
                            <div class="flex items-start justify-between">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">
                                        {{ $asset['name'] ?: $asset['model'] ?: 'Unnamed Asset' }}
                                    </p>
                                    <p class="text-xs text-gray-500 mt-1">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                            {{ $asset['category'] }}
                                        </span>
                                        @if($asset['barcode'])
                                            <span class="ml-2 font-mono">{{ $asset['barcode'] }}</span>
                                        @endif
                                    </p>
                                </div>
                                <div class="ml-4 text-right">
                                    @php
                                        $statusBadge = match(true) {
                                            in_array($asset['status'], ['Deployed', 'In Use', 'Assigned']) => 'bg-green-100 text-green-800',
                                            in_array($asset['status'], ['Available', 'Stock', 'In Stock']) => 'bg-blue-100 text-blue-800',
                                            in_array($asset['status'], ['Defective', 'For Repair', 'Damaged']) => 'bg-red-100 text-red-800',
                                            default => 'bg-gray-100 text-gray-800',
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $statusBadge }}">
                                        {{ $asset['status'] ?? 'Unknown' }}
                                    </span>
                                    <p class="text-xs text-gray-400 mt-1">{{ $asset['created_at'] }}</p>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center text-gray-500">
                            No recent assets found
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Asset Management Section --}}
        <div class="bg-white rounded-lg shadow mt-6">
            <div class="p-4 border-b border-gray-200">
                <h3 class="font-semibold text-gray-800">Asset Inventory</h3>
                <p class="text-sm text-gray-500 mt-1">Search, filter, and manage your assets</p>
            </div>

            {{-- Search & Filters --}}
            <div class="p-4 border-b border-gray-100 bg-gray-50">
                <div class="flex flex-wrap gap-4">
                    <div class="flex-1 min-w-[200px]">
                        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search barcode, name, model, serial..." class="w-full text-sm border-gray-300 rounded-lg focus:ring-orange-500 focus:border-orange-500" />
                    </div>
                    <div class="w-40">
                        <select wire:model.live="categoryFilter" class="w-full text-sm border-gray-300 rounded-lg focus:ring-orange-500 focus:border-orange-500">
                            <option value="">All Categories</option>
                            @foreach($this->categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="w-40">
                        <select wire:model.live="statusFilter" class="w-full text-sm border-gray-300 rounded-lg focus:ring-orange-500 focus:border-orange-500">
                            <option value="">All Statuses</option>
                            <option value="Deployed">Deployed</option>
                            <option value="In Use">In Use</option>
                            <option value="Available">Available</option>
                            <option value="Stock">Stock</option>
                            <option value="Defective">Defective</option>
                            <option value="For Repair">For Repair</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- Asset Table --}}
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-gray-600">
                        <tr>
                            <th class="px-4 py-3 text-left font-medium">Barcode</th>
                            <th class="px-4 py-3 text-left font-medium">Name / Model</th>
                            <th class="px-4 py-3 text-left font-medium">Category</th>
                            <th class="px-4 py-3 text-left font-medium">Location</th>
                            <th class="px-4 py-3 text-left font-medium">Status</th>
                            <th class="px-4 py-3 text-left font-medium">Assigned To</th>
                            <th class="px-4 py-3 text-center font-medium">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($this->assets as $asset)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-3 font-mono text-xs">{{ $asset->item_barcode }}</td>
                                <td class="px-4 py-3">
                                    <p class="font-medium text-gray-900">{{ $asset->item_name ?: '—' }}</p>
                                    @if($asset->item_model)
                                        <p class="text-xs text-gray-500">{{ $asset->item_model }}</p>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                        {{ $asset->assetList?->name ?? 'Uncategorized' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-gray-600">{{ $asset->location ?: '—' }}</td>
                                <td class="px-4 py-3">
                                    @php
                                        $statusBadge = match(true) {
                                            in_array($asset->status, ['Deployed', 'In Use', 'Assigned']) => 'bg-green-100 text-green-800',
                                            in_array($asset->status, ['Available', 'Stock', 'In Stock']) => 'bg-blue-100 text-blue-800',
                                            in_array($asset->status, ['Defective', 'For Repair', 'Damaged']) => 'bg-red-100 text-red-800',
                                            default => 'bg-gray-100 text-gray-800',
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $statusBadge }}">
                                        {{ $asset->status ?? 'Unknown' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-gray-600">{{ $asset->assigned_to ?: '—' }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-center gap-2">
                                        <button wire:click="openEditModal({{ $asset->id }})" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded transition-colors" title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>
                                        <button wire:click="confirmDelete({{ $asset->id }})" class="p-1.5 text-red-600 hover:bg-red-50 rounded transition-colors" title="Delete">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                                    No assets found matching your criteria
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($this->assets->hasPages())
                <div class="p-4 border-t border-gray-100">
                    {{ $this->assets->links() }}
                </div>
            @endif
        </div>
    </main>

    {{-- Add/Edit Asset Modal --}}
    @if($showAssetModal)
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <div class="fixed inset-0 bg-gray-900/70 backdrop-blur-sm" wire:click="$set('showAssetModal', false)"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-hidden flex flex-col">
                {{-- Header --}}
                <div class="flex items-center justify-between p-5 border-b border-gray-100 flex-shrink-0">
                    <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                        <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        {{ $editMode ? 'Edit Asset' : 'Add New Asset' }}
                    </h3>
                    <button wire:click="$set('showAssetModal', false)" class="p-2 -m-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                {{-- Form Content --}}
                <div class="p-5 overflow-y-auto flex-1">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Category --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Category <span class="text-red-500">*</span></label>
                            <select wire:model="category" class="w-full text-sm border-gray-300 rounded-lg focus:ring-orange-500 focus:border-orange-500">
                                <option value="">Select category...</option>
                                @foreach($this->categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                            @error('category') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Barcode --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Barcode <span class="text-red-500">*</span></label>
                            <input type="text" wire:model="item_barcode" class="w-full text-sm border-gray-300 rounded-lg focus:ring-orange-500 focus:border-orange-500" placeholder="e.g., ITSS-001" />
                            @error('item_barcode') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Brand/Name --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Brand / Name</label>
                            <input type="text" wire:model="item_brand" class="w-full text-sm border-gray-300 rounded-lg focus:ring-orange-500 focus:border-orange-500" placeholder="e.g., Dell, HP" />
                        </div>

                        {{-- Model --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Model</label>
                            <input type="text" wire:model="item_model" class="w-full text-sm border-gray-300 rounded-lg focus:ring-orange-500 focus:border-orange-500" placeholder="e.g., Latitude 5520" />
                        </div>

                        {{-- ITSS Serial --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">ITSS Serial <span class="text-red-500">*</span></label>
                            <input type="text" wire:model="itss_serial" class="w-full text-sm border-gray-300 rounded-lg focus:ring-orange-500 focus:border-orange-500" placeholder="Internal serial number" />
                            @error('itss_serial') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Purchase Serial --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Purchase Serial</label>
                            <input type="text" wire:model="purch_serial" class="w-full text-sm border-gray-300 rounded-lg focus:ring-orange-500 focus:border-orange-500" placeholder="Manufacturer serial" />
                        </div>

                        {{-- Location --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Location <span class="text-red-500">*</span></label>
                            <input type="text" wire:model="location" class="w-full text-sm border-gray-300 rounded-lg focus:ring-orange-500 focus:border-orange-500" placeholder="e.g., IT Office, Lab 1" />
                            @error('location') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Status --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status <span class="text-red-500">*</span></label>
                            <select wire:model="status" class="w-full text-sm border-gray-300 rounded-lg focus:ring-orange-500 focus:border-orange-500">
                                <option value="">Select status...</option>
                                <option value="Deployed">Deployed</option>
                                <option value="In Use">In Use</option>
                                <option value="Available">Available</option>
                                <option value="Stock">Stock</option>
                                <option value="Defective">Defective</option>
                                <option value="For Repair">For Repair</option>
                            </select>
                            @error('status') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Assigned To --}}
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Assigned To <span class="text-red-500">*</span></label>
                            <input type="text" wire:model="assign_to" class="w-full text-sm border-gray-300 rounded-lg focus:ring-orange-500 focus:border-orange-500" placeholder="Person or department" />
                            @error('assign_to') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                    </div>
                </div>

                {{-- Footer --}}
                <div class="flex justify-end gap-3 px-5 py-4 border-t border-gray-100 bg-gray-50 flex-shrink-0">
                    <button wire:click="$set('showAssetModal', false)" class="px-4 py-2.5 text-sm font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-lg transition-colors">
                        Cancel
                    </button>
                    <button wire:click="saveAsset" wire:loading.attr="disabled" class="px-4 py-2.5 text-sm font-medium text-white bg-orange-500 hover:bg-orange-600 disabled:opacity-50 rounded-lg transition-colors flex items-center gap-2">
                        <span wire:loading.remove wire:target="saveAsset">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </span>
                        <span wire:loading wire:target="saveAsset">
                            <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                        </span>
                        {{ $editMode ? 'Update Asset' : 'Create Asset' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Delete Confirmation Modal --}}
    @if($showDeleteModal)
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <div class="fixed inset-0 bg-gray-900/70 backdrop-blur-sm" wire:click="$set('showDeleteModal', false)"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md p-6">
                <div class="flex items-center gap-4">
                    <div class="flex-shrink-0 w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Delete Asset</h3>
                        <p class="text-sm text-gray-500 mt-1">Are you sure you want to delete this asset? This action cannot be undone.</p>
                    </div>
                </div>
                <div class="flex justify-end gap-3 mt-6">
                    <button wire:click="$set('showDeleteModal', false)" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-lg transition-colors">
                        Cancel
                    </button>
                    <button wire:click="deleteAsset" class="px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg transition-colors">
                        Delete
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Category Management Modal --}}
    @if($showCategoryModal)
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <div class="fixed inset-0 bg-gray-900/70 backdrop-blur-sm" wire:click="$set('showCategoryModal', false)"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-hidden flex flex-col">
                {{-- Header --}}
                <div class="flex items-center justify-between p-5 border-b border-gray-100 flex-shrink-0">
                    <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                        </svg>
                        {{ $editCategoryMode ? 'Edit Category' : 'Manage Categories' }}
                    </h3>
                    <button wire:click="$set('showCategoryModal', false)" class="p-2 -m-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                {{-- Content --}}
                <div class="p-5 overflow-y-auto flex-1">
                    {{-- Add/Edit Form --}}
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ $editCategoryMode ? 'Edit Category Name' : 'New Category Name' }}</label>
                        <div class="flex gap-2">
                            <input type="text" wire:model="categoryName" wire:keydown.enter="saveCategory" class="flex-1 text-sm border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500" placeholder="e.g., Laptop, Monitor, Printer" />
                            <button wire:click="saveCategory" class="px-4 py-2 text-sm font-medium text-white bg-indigo-500 hover:bg-indigo-600 rounded-lg transition-colors flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                {{ $editCategoryMode ? 'Update' : 'Add' }}
                            </button>
                            @if($editCategoryMode)
                                <button wire:click="resetCategoryForm" class="px-3 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                                    Cancel
                                </button>
                            @endif
                        </div>
                        @error('categoryName') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Existing Categories List --}}
                    <div>
                        <h4 class="text-sm font-medium text-gray-700 mb-3">Existing Categories ({{ $this->categories->count() }})</h4>
                        @if($this->categories->count() > 0)
                            <div class="border border-gray-200 rounded-lg divide-y divide-gray-100 max-h-64 overflow-y-auto">
                                @foreach($this->categories as $cat)
                                    <div class="flex items-center justify-between p-3 hover:bg-gray-50 transition-colors">
                                        <span class="text-sm text-gray-800">{{ $cat->name }}</span>
                                        <div class="flex items-center gap-1">
                                            <button wire:click="openEditCategoryModal({{ $cat->id }})" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded transition-colors" title="Edit">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </button>
                                            <button wire:click="confirmDeleteCategory({{ $cat->id }})" class="p-1.5 text-red-600 hover:bg-red-50 rounded transition-colors" title="Delete">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8 text-gray-500 border border-dashed border-gray-300 rounded-lg">
                                <svg class="w-8 h-8 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                </svg>
                                <p class="text-sm">No categories yet</p>
                                <p class="text-xs text-gray-400">Add your first category above</p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Footer --}}
                <div class="flex justify-end px-5 py-4 border-t border-gray-100 bg-gray-50 flex-shrink-0">
                    <button wire:click="$set('showCategoryModal', false)" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-lg transition-colors">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Delete Category Confirmation Modal --}}
    @if($showDeleteCategoryModal)
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <div class="fixed inset-0 bg-gray-900/70 backdrop-blur-sm" wire:click="$set('showDeleteCategoryModal', false)"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md p-6">
                <div class="flex items-center gap-4">
                    <div class="flex-shrink-0 w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Delete Category</h3>
                        <p class="text-sm text-gray-500 mt-1">Are you sure you want to delete this category? Categories with assigned assets cannot be deleted.</p>
                    </div>
                </div>
                <div class="flex justify-end gap-3 mt-6">
                    <button wire:click="$set('showDeleteCategoryModal', false)" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-lg transition-colors">
                        Cancel
                    </button>
                    <button wire:click="deleteCategory" class="px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg transition-colors">
                        Delete
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
