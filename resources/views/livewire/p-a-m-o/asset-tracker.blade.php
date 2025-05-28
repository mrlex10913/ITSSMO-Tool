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
                                                @if($asset->category)
                                                    <i class="fas {{
                                                        str_contains(strtolower($asset->category->name), 'laptop') ? 'fa-laptop' :
                                                        (str_contains(strtolower($asset->category->name), 'desktop') ? 'fa-desktop' :
                                                        (str_contains(strtolower($asset->category->name), 'phone') ? 'fa-mobile-alt' :
                                                        (str_contains(strtolower($asset->category->name), 'tablet') ? 'fa-tablet-alt' :
                                                        'fa-laptop'))) }}"></i>
                                                @else
                                                    <i class="fas fa-laptop"></i>
                                                @endif
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
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-8 w-8 bg-gray-100 rounded-full flex items-center justify-center text-gray-400 mr-2">
                                                    <span class="material-symbols-sharp text-xs">person_off</span>
                                                </div>
                                                <div class="text-sm text-gray-500">Unassigned</div>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            @if($asset->location)
                                                <div class="flex-shrink-0 h-6 w-6 bg-green-100 rounded-full flex items-center justify-center text-green-600 mr-2">
                                                    <span class="material-symbols-sharp text-xs">location_on</span>
                                                </div>
                                                <div class="text-sm text-gray-900">{{ $asset->location->name }}</div>
                                            @else
                                                <div class="flex-shrink-0 h-6 w-6 bg-gray-100 rounded-full flex items-center justify-center text-gray-400 mr-2">
                                                    <span class="material-symbols-sharp text-xs">location_off</span>
                                                </div>
                                                <div class="text-sm text-gray-500">No Location</div>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($asset->status == 'active' || $asset->status == 'assigned' || $asset->status == 'in-use')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                <span class="w-1.5 h-1.5 bg-green-400 rounded-full mr-1 mt-0.5"></span>
                                                {{ ucfirst(str_replace('-', ' ', $asset->status)) }}
                                            </span>
                                        @elseif($asset->status == 'maintenance')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                <span class="w-1.5 h-1.5 bg-red-400 rounded-full mr-1 mt-0.5"></span>
                                                Maintenance
                                            </span>
                                        @elseif($asset->status == 'in_transfer' || $asset->status == 'in-transfer')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                <span class="w-1.5 h-1.5 bg-yellow-400 rounded-full mr-1 mt-0.5"></span>
                                                In Transfer
                                            </span>
                                        @elseif($asset->status == 'disposed' || $asset->status == 'retired')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                <span class="w-1.5 h-1.5 bg-gray-400 rounded-full mr-1 mt-0.5"></span>
                                                {{ ucfirst(str_replace('-', ' ', $asset->status)) }}
                                            </span>
                                        @else
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                <span class="w-1.5 h-1.5 bg-blue-400 rounded-full mr-1 mt-0.5"></span>
                                                {{ ucfirst(str_replace('-', ' ', $asset->status)) }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex justify-end space-x-2">
                                            <button wire:click="viewAsset({{ $asset->id }})"
                                                    class="inline-flex items-center px-2 py-1 border border-transparent text-xs font-medium rounded text-blue-700 bg-blue-100 hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                                    title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button wire:click="openTransferModal({{ $asset->id }})"
                                                    class="inline-flex items-center px-2 py-1 border border-transparent text-xs font-medium rounded text-green-700 bg-green-100 hover:bg-green-200 focus:outline-none focus:ring-2 focus:ring-green-500"
                                                    title="Transfer Asset">
                                                <i class="fas fa-exchange-alt"></i>
                                            </button>
                                            <div class="relative inline-block text-left" x-data="{ open: false }">
                                                <button @click="open = !open"
                                                        class="inline-flex items-center px-2 py-1 border border-transparent text-xs font-medium rounded text-gray-700 bg-gray-100 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500"
                                                        title="More Options">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </button>
                                                <div x-show="open" @click.away="open = false"
                                                     class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-10">
                                                    <div class="py-1">
                                                        <button wire:click="generateQR({{ $asset->id }})"
                                                                class="block w-full text-left px-4 py-2 text-xs text-gray-700 hover:bg-gray-100">
                                                            <i class="fas fa-qrcode mr-2"></i> Generate QR Code
                                                        </button>
                                                        <button wire:click="printLabel({{ $asset->id }})"
                                                                class="block w-full text-left px-4 py-2 text-xs text-gray-700 hover:bg-gray-100">
                                                            <i class="fas fa-print mr-2"></i> Print Label
                                                        </button>
                                                        <button wire:click="viewHistory({{ $asset->id }})"
                                                                class="block w-full text-left px-4 py-2 text-xs text-gray-700 hover:bg-gray-100">
                                                            <i class="fas fa-history mr-2"></i> View History
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                        <div class="flex flex-col items-center py-8">
                                            <i class="fas fa-search text-4xl text-gray-300 mb-3"></i>
                                            <p class="text-lg font-medium">No assets found</p>
                                            <p class="text-sm">Try adjusting your search criteria or filters</p>
                                        </div>
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
                                    Assigned To
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
                                                @if($movement->asset && $movement->asset->category)
                                                    <i class="fas {{
                                                        str_contains(strtolower($movement->asset->category->name), 'laptop') ? 'fa-laptop' :
                                                        (str_contains(strtolower($movement->asset->category->name), 'desktop') ? 'fa-desktop' :
                                                        (str_contains(strtolower($movement->asset->category->name), 'phone') ? 'fa-mobile-alt' :
                                                        (str_contains(strtolower($movement->asset->category->name), 'tablet') ? 'fa-tablet-alt' :
                                                        'fa-laptop'))) }}"></i>
                                                @else
                                                    <i class="fas fa-laptop"></i>
                                                @endif
                                            </div>
                                            <div class="ml-3">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $movement->asset->brand ?? 'Unknown' }} {{ $movement->asset->model ?? '' }}
                                                </div>
                                                <div class="text-xs text-gray-500">
                                                    Tag: {{ $movement->asset->property_tag_number ?? 'No Tag' }}
                                                </div>
                                                <div class="text-xs text-gray-500">
                                                    SN: {{ $movement->asset->serial_number ?? 'No SN' }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            @if($movement->fromLocation)
                                                <div class="flex-shrink-0 h-6 w-6 bg-red-100 rounded-full flex items-center justify-center text-red-600 mr-2">
                                                    <span class="material-symbols-sharp text-xs">location_on</span>
                                                </div>
                                                <div class="text-sm text-gray-900">{{ $movement->fromLocation->name }}</div>
                                            @else
                                                <div class="flex-shrink-0 h-6 w-6 bg-gray-100 rounded-full flex items-center justify-center text-gray-400 mr-2">
                                                    <span class="material-symbols-sharp text-xs">location_off</span>
                                                </div>
                                                <div class="text-sm text-gray-500">Unknown</div>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            @if($movement->toLocation)
                                                <div class="flex-shrink-0 h-6 w-6 bg-green-100 rounded-full flex items-center justify-center text-green-600 mr-2">
                                                    <span class="material-symbols-sharp text-xs">location_on</span>
                                                </div>
                                                <div class="text-sm text-gray-900">{{ $movement->toLocation->name }}</div>
                                            @else
                                                <div class="flex-shrink-0 h-6 w-6 bg-gray-100 rounded-full flex items-center justify-center text-gray-400 mr-2">
                                                    <span class="material-symbols-sharp text-xs">location_off</span>
                                                </div>
                                                <div class="text-sm text-gray-500">Unknown</div>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($movement->assignedEmployee)
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-6 w-6 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 mr-2">
                                                    <span class="material-symbols-sharp text-xs">badge</span>
                                                </div>
                                                <div>
                                                    <div class="text-sm font-medium text-gray-900">{{ $movement->assignedEmployee->employee_number }}</div>
                                                    <div class="text-xs text-gray-500">{{ $movement->assignedEmployee->full_name }}</div>
                                                </div>
                                            </div>
                                        @else
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-6 w-6 bg-gray-100 rounded-full flex items-center justify-center text-gray-400 mr-2">
                                                    <span class="material-symbols-sharp text-xs">person_off</span>
                                                </div>
                                                <div class="text-sm text-gray-500">Unassigned</div>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <div class="flex items-center">
                                            <i class="fas fa-calendar-alt text-gray-400 mr-2"></i>
                                            {{ $movement->movement_date ? $movement->movement_date->format('M j, Y') : 'Unknown' }}
                                        </div>
                                        <div class="text-xs text-gray-400 mt-1">
                                            {{ $movement->movement_date ? $movement->movement_date->diffForHumans() : '' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if(($movement->movement_type ?? 'transfer') == 'transfer')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                <i class="fas fa-exchange-alt mr-1"></i>
                                                Transfer
                                            </span>
                                        @elseif(($movement->movement_type ?? 'transfer') == 'maintenance')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                <i class="fas fa-tools mr-1"></i>
                                                Maintenance
                                            </span>
                                        @elseif(($movement->movement_type ?? 'transfer') == 'assignment')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                <i class="fas fa-user-plus mr-1"></i>
                                                Assignment
                                            </span>
                                        @else
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                <i class="fas fa-question mr-1"></i>
                                                {{ ucfirst($movement->movement_type ?? 'Unknown') }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($movement->assignedBy)
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-6 w-6 bg-purple-100 rounded-full flex items-center justify-center text-purple-600 mr-2">
                                                    <span class="material-symbols-sharp text-xs">person</span>
                                                </div>
                                                <div>
                                                    <div class="text-sm text-gray-900">{{ $movement->assignedBy->name }}</div>
                                                    <div class="text-xs text-gray-500">{{ $movement->assignedBy->role ?? 'User' }}</div>
                                                </div>
                                            </div>
                                        @else
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-6 w-6 bg-gray-100 rounded-full flex items-center justify-center text-gray-400 mr-2">
                                                    <span class="material-symbols-sharp text-xs">person_off</span>
                                                </div>
                                                <div class="text-sm text-gray-500">Unknown</div>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                        <div class="flex flex-col items-center py-8">
                                            <i class="fas fa-exchange-alt text-4xl text-gray-300 mb-3"></i>
                                            <p class="text-lg font-medium">No recent movements found</p>
                                            <p class="text-sm">Asset transfers and assignments will appear here</p>
                                        </div>
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
