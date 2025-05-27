<div>
    <main class="flex-1 overflow-y-auto">
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Asset Tracking Dashboard</h2>
            <p class="text-gray-600">Monitor and manage company assets assigned to employees</p>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-lg shadow p-4 mb-6">
            <div class="p-4 border-b border-gray-200">
                <h3 class="font-semibold text-gray-800">Quick Actions</h3>
            </div>
            <div class="p-4 flex flex-wrap gap-3">
                <button wire:click="openTransferModal" class="bg-green-500 hover:bg-green-600 text-white text-sm font-medium py-2 px-4 rounded flex items-center">
                    <i class="fas fa-exchange-alt mr-2"></i> Record Transfer
                </button>
                <button wire:click="openScanModal" class="bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-medium py-2 px-4 rounded flex items-center">
                    <i class="fas fa-qrcode mr-2"></i> Scan Asset
                </button>
                <button wire:click="openExportModal" class="bg-purple-500 hover:bg-purple-600 text-white text-sm font-medium py-2 px-4 rounded flex items-center">
                    <i class="fas fa-file-export mr-2"></i> Export Report
                </button>
            </div>
        </div>

        <!-- Asset List -->
        <div class="bg-white rounded-lg shadow mb-6">
            <div class="p-4 border-b border-gray-200 flex justify-between items-center">
                <h3 class="font-semibold text-gray-800">Asset Inventory</h3>
                <div class="flex">
                    <div class="relative">
                        <input wire:model.debounce.300ms="search" type="text" placeholder="Search assets..."
                               class="text-sm border-gray-300 rounded-lg pr-8 focus:ring-blue-500 focus:border-blue-500">
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="p-4">
                <div class="flex justify-between items-center mb-4">
                    <div class="flex space-x-2">
                        <select wire:model.live="categoryId" class="text-xs border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500">
                            <option value="">All Categories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                        <select wire:model.live="department" class="text-xs border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500">
                            <option value="">All Departments</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept }}">{{ $dept }}</option>
                            @endforeach
                        </select>
                        <select wire:model.live="locationId" class="text-xs border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500">
                            <option value="">All Locations</option>
                            @foreach($locations as $loc)
                                <option value="{{ $loc->id }}">{{ $loc->name }}</option>
                            @endforeach
                        </select>
                        <select wire:model.live="status" class="text-xs border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500">
                            <option value="">All Statuses</option>
                            @foreach($statuses as $stat)
                                <option value="{{ $stat }}">{{ ucfirst($stat) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <button wire:click="resetFilters" class="text-xs bg-gray-100 hover:bg-gray-200 text-gray-800 font-medium py-1 px-2 rounded">
                            <i class="fas fa-filter mr-1"></i> Clear Filters
                        </button>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Asset
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Category
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Assigned To
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Location
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($assets as $asset)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10 bg-gray-100 rounded-full flex items-center justify-center text-gray-500">
                                                <i class="fas fa-laptop"></i>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">{{ $asset->brand }} {{ $asset->model }}</div>
                                                <div class="text-xs text-gray-500">SN: {{ $asset->serial_number }}</div>
                                                <div class="text-xs text-gray-500">Tag: {{ $asset->property_tag_number }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $asset->category->name ?? 'N/A' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($asset->assignedEmployee)
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-8 w-8 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 mr-2">
                                                    <span class="material-symbols-sharp text-xs">badge</span>
                                                </div>
                                                <div>
                                                    <div class="text-sm font-medium text-gray-900">{{ $asset->assignedEmployee->employee_number }}</div>
                                                    <div class="text-sm text-gray-500">{{ $asset->assignedEmployee->full_name }}</div>
                                                    @if($asset->assignedEmployee->department)
                                                        <div class="text-xs text-gray-400">{{ $asset->assignedEmployee->department }}</div>
                                                    @endif
                                                </div>
                                            </div>
                                        @else
                                            <div class="text-sm text-gray-500">Unassigned</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $asset->location->name ?? 'N/A' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($asset->status == 'active' || $asset->status == 'assigned')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                {{ ucfirst($asset->status) }}
                                            </span>
                                        @elseif($asset->status == 'maintenance')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                {{ ucfirst($asset->status) }}
                                            </span>
                                        @elseif($asset->status == 'in_transfer')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                In Transfer
                                            </span>
                                        @else
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                {{ ucfirst($asset->status) }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <button wire:click="viewAsset({{ $asset->id }})" class="text-blue-600 hover:text-blue-900 mr-3">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button wire:click="openTransferModal({{ $asset->id }})" class="text-green-600 hover:text-green-900 mr-3">
                                            <i class="fas fa-exchange-alt"></i>
                                        </button>
                                        <button class="text-gray-600 hover:text-gray-900">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                        No assets found matching your filters.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $assets->links() }}
                </div>
            </div>
        </div>

        <!-- Recent Asset Movements -->
        <div class="bg-white rounded-lg shadow mb-6">
            <div class="p-4 border-b border-gray-200 flex justify-between items-center">
                <h3 class="font-semibold text-gray-800">Recent Asset Movements</h3>
                <button class="text-xs bg-gray-100 hover:bg-gray-200 text-gray-800 font-medium py-1 px-2 rounded">
                    View All
                </button>
            </div>
            <div class="p-4">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Asset
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    From
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    To
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Date
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Type
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    By
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($recentMovements as $movement)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-8 w-8 bg-gray-100 rounded-full flex items-center justify-center text-gray-500">
                                                <i class="fas fa-laptop"></i>
                                            </div>
                                            <div class="ml-3">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $movement->asset->brand ?? 'Unknown' }} {{ $movement->asset->model ?? '' }}
                                                </div>
                                                <div class="text-xs text-gray-500">
                                                    {{ $movement->asset->property_tag_number ?? 'No Tag' }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $movement->fromLocation->name ?? 'N/A' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $movement->toLocation->name ?? 'N/A' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $movement->movement_date->format('M j, Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($movement->movement_type == 'transfer')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                Transfer
                                            </span>
                                        @elseif($movement->movement_type == 'maintenance')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                Maintenance
                                            </span>
                                        @else
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                {{ ucfirst($movement->movement_type) }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $movement->assignedBy->name ?? 'N/A' }}</div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                        No recent movements found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <!-- Include modals -->
    @include('livewire.p-a-m-o.partials.transfer-modal')
    @include('livewire.p-a-m-o.partials.scan-modal')
    @include('livewire.p-a-m-o.partials.export-modal')
    @include('livewire.p-a-m-o.partials.asset-detail-modal')
</div>
