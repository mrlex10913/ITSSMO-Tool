<div>
    <div class="p-0">
        <!-- Header and Quick Stats -->
        <div class="mb-6 space-y-4">
            <div class="w-full">
                <div class="bg-white rounded-lg shadow">
                    <div class="flex justify-between items-center p-4">
                        <h2 class="text-xl font-semibold text-gray-800">Inventory & Supplies Overview</h2>
                        <div class="flex space-x-2">
                            <button
                                type="button"
                                x-data="{}"
                                @click="$dispatch('open-modal', 'manage-categories-modal')"
                                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:border-indigo-800 focus:ring ring-indigo-300 disabled:opacity-25 transition">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5 3a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2V5a2 2 0 00-2-2H5zm0 2h10v10H5V5zm2 3h6v1H7V8zm0 2h6v1H7v-1zm0 2h3v1H7v-1z" clip-rule="evenodd" />
                                </svg>
                                Manage Categories
                            </button>

                            <button
                                type="button"
                                x-data="{}"
                                @click="$dispatch('open-modal', 'manage-locations-modal')"
                                class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-800 focus:outline-none focus:border-green-800 focus:ring ring-green-300 disabled:opacity-25 transition">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                                </svg>
                                Manage Locations
                            </button>
                            <button
                                wire:click="exportInventory"
                                class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:border-blue-300 focus:ring ring-blue-200 active:text-gray-800 active:bg-gray-50 disabled:opacity-25 transition">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                                Export
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dashboard Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="bg-blue-600 text-white rounded-lg shadow">
                <div class="p-4">
                    <div class="flex justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-white">Total Assets</h3>
                            <p class="text-2xl font-bold">{{ $totalAssets ?? '1,256' }}</p>
                        </div>
                        <div class="opacity-50">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-green-600 text-white rounded-lg shadow">
                <div class="p-4">
                    <div class="flex justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-white">Consumables</h3>
                            <p class="text-2xl font-bold">{{ $totalConsumables ?? '843' }}</p>
                        </div>
                        <div class="opacity-50">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-yellow-500 text-white rounded-lg shadow">
                <div class="p-4">
                    <div class="flex justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-white">Low Stock</h3>
                            <p class="text-2xl font-bold">{{ $lowStock ?? '12' }}</p>
                        </div>
                        <div class="opacity-50">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-red-600 text-white rounded-lg shadow">
                <div class="p-4">
                    <div class="flex justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-white">Maintenance Due</h3>
                            <p class="text-2xl font-bold">{{ $maintenanceDue ?? '5' }}</p>
                        </div>
                        <div class="opacity-50">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Inventory Content -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-4">
                <div x-data="{ activeTab: 'assets' }">
                    <!-- Tabs -->
                    <div class="border-b border-gray-200">
                        <nav class="-mb-px flex justify-between">
                            <div class="flex space-x-8">
                                <button
                                    @click="activeTab = 'assets'"
                                    :class="{'border-blue-500 text-blue-600': activeTab === 'assets', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'assets'}"
                                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="inline-block h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                    Assets
                                </button>
                                <button
                                    @click="activeTab = 'consumables'"
                                    :class="{'border-blue-500 text-blue-600': activeTab === 'consumables', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'consumables'}"
                                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="inline-block h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                                    </svg>
                                    Consumables
                                </button>
                            </div>

                        </nav>
                    </div>

                    <!-- Search and Filters -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 my-4">
                        <div>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <input
                                    wire:model.live.debounce.300ms="search"
                                    type="text"
                                    class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                    placeholder="Search by tag, name, or serial...">
                            </div>
                        </div>
                        <div>
                            <select
                                wire:model.live="categoryFilter"
                                class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                <option value="">All Categories</option>
                                @foreach($majorCategories as $major)
                                    <option value="{{ $major->id }}">{{ $major->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <select
                                wire:model.live="statusFilter"
                                class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                <option value="">All Statuses</option>
                                <option value="available">Available</option>
                                <option value="in-use">In Use</option>
                                <option value="maintenance">Under Repair</option>
                                <option value="disposed">Disposed</option>
                            </select>
                        </div>
                        <div>
                            <button
                                wire:click="resetFilters"
                                class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                Reset Filters
                            </button>
                        </div>
                    </div>

                    <!-- Tab Content -->
                    <div class="">
                        <!-- Assets Tab -->
                        <div x-show="activeTab === 'assets'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                            <button
                                @click="$dispatch('open-modal', 'add-item-modal')"
                                class="inline-flex items-center px-4 py-2 my-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-800 focus:outline-none focus:border-blue-800 focus:ring ring-blue-300 disabled:opacity-25 transition">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                                </svg>
                                Add Item
                            </button>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                P.O Number
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Property Tag
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Item Description
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Major Category
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Minor Category
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Status
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Value
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Actions
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <!-- Static Asset Data -->
                                        @forelse($assetsList as $asset)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                    {{ $asset->po_number ?? 'N/A' }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $asset->property_tag_number }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $asset->description }}
                                                </td>
                                                 <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                        @if($asset->category && $asset->category->parent)
                                                            {{ $asset->category->parent->name }}
                                                        @else
                                                            Uncategorized
                                                        @endif
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $asset->category->name ?? 'Uncategorized' }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    @if($asset->status == 'available')
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                            Available
                                                        </span>
                                                    @elseif($asset->status == 'in-use')
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                            In Use
                                                        </span>
                                                    @elseif($asset->status == 'maintenance')
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                            Under Repair
                                                        </span>
                                                    @elseif($asset->status == 'disposed')
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                            Disposed
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    <span>&#8369;</span> {{ number_format($asset->purchase_value, 2) ?? '0.00' }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                    <div class="flex space-x-2">
                                                        <button class="text-blue-600 hover:text-blue-900" title="View Details">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                            </svg>
                                                        </button>
                                                        <button class="text-indigo-600 hover:text-indigo-900" title="Edit"
                                                            wire:click="editAsset({{ $asset->id }})">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                            </svg>
                                                        </button>
                                                        <button class="text-red-600 hover:text-red-900" title="Delete"
                                                            wire:click="confirmDeleteAsset({{ $asset->id }})">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                            </svg>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                                    No assets found. Click "Add Item" to create one.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <!-- Pagination -->
                            <div class="py-3">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1 flex justify-between sm:hidden">
                                        {{ $assetsList->links() }}
                                    </div>
                                    <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                                        <div>
                                            <p class="text-sm text-gray-700">
                                                Showing
                                                <span class="font-medium">{{ $assetsList->firstItem() }}</span>
                                                to
                                                <span class="font-medium">{{ $assetsList->lastItem() }}</span>
                                                of
                                                <span class="font-medium">{{ $assetsList->total() }}</span>
                                                results
                                            </p>
                                        </div>
                                        <div>
                                            {{ $assetsList->links() }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Pagination -->
                            {{-- <div class="py-3">
                                <div class="flex justify-end">
                                    {{ $assets->links() ?? '' }}
                                </div>
                            </div> --}}
                        </div>

                        <!-- Consumables Tab -->
                        <div x-show="activeTab === 'consumables'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Item ID
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Name
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Category
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Quantity
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Unit
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Reorder Level
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Location
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Last Restock
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Actions
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <!-- Static Consumable Data -->
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                CONS-001
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                A4 Paper
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                Office Supplies
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                15
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                Ream
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                10
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                Supply Room
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                2023-04-18
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <div class="flex space-x-2">
                                                    <button class="text-blue-600 hover:text-blue-900">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                        </svg>
                                                    </button>
                                                    <button class="text-indigo-600 hover:text-indigo-900">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                        </svg>
                                                    </button>
                                                    <button class="text-green-600 hover:text-green-900">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                                        </svg>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="py-3">
                                <div class="flex justify-end">
                                    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                        <a href="#" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                            <span class="sr-only">Previous</span>
                                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                                            </svg>
                                        </a>
                                        <a href="#" aria-current="page" class="z-10 bg-blue-50 border-blue-500 text-blue-600 relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                                            1
                                        </a>
                                        <a href="#" class="bg-white border-gray-300 text-gray-500 hover:bg-gray-50 relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                                            2
                                        </a>
                                        <a href="#" class="bg-white border-gray-300 text-gray-500 hover:bg-gray-50 relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                                            3
                                        </a>
                                        <a href="#" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                            <span class="sr-only">Next</span>
                                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                            </svg>
                                        </a>
                                    </nav>
                                </div>
                            </div>

                            <!-- Pagination -->
                            {{-- <div class="py-3">
                                <div class="flex justify-end">
                                    {{ $consumables->links() ?? '' }}
                                </div>
                            </div> --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Item Modal -->
    <div
        x-data="{ open: false, activeTab: 'asset' }"
        @open-modal.window="if ($event.detail === 'add-item-modal') { open = true; activeTab = 'asset'; }"
        @keydown.escape.window="open = false"
        x-show="open"
        style="display: none;"
        class="fixed inset-0 overflow-y-auto px-4 py-6 sm:px-0 z-50"
    >
        <div
            x-show="open"
            class="fixed inset-0 transform transition-all"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click="open = false"
        >
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>

        <div
            x-show="open"
            class="mb-6 bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:w-full sm:max-w-2xl sm:mx-auto"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        >
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900">
                    {{ $editingAssetId ? 'Edit Inventory Item' : 'Add Inventory Item' }}
                </h2>

                <div class="mt-4">
                    <!-- Tabs -->
                    <div class="border-b border-gray-200">
                        <nav class="-mb-px flex space-x-8">
                            <button
                                @click="activeTab = 'asset'"
                                :class="{'border-blue-500 text-blue-600': activeTab === 'asset', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'asset'}"
                                class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                Asset
                            </button>
                            <button
                                @click="activeTab = 'consumable'"
                                :class="{'border-blue-500 text-blue-600': activeTab === 'consumable', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'consumable'}"
                                class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                Consumable
                            </button>
                        </nav>
                    </div>

                    <!-- Asset Form -->
                    <div x-show="activeTab === 'asset'" class="mt-4">
                        <form wire:submit.prevent="saveAsset">
                            <!-- Required Asset Information -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <x-label for="asset_po_number" value="P.O. Number" />
                                    <x-input wire:model="asset.po_number" id="asset_po_number" type="text" class="mt-1 block w-full" />
                                </div>
                                <div>
                                    <x-label for="asset_property_tag" value="Property/Tag Number *" />
                                    <x-input wire:model="asset.property_tag_number" id="asset_property_tag" type="text" class="mt-1 block w-full" />
                                    @error('asset.property_tag_number') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <!-- Device Information -->
                            <div class="mb-4">
                                <div class="mb-2">
                                    <h3 class="text-sm font-medium text-gray-700">Device Information</h3>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <x-label for="asset_brand" value="Brand" />
                                        <x-input wire:model="asset.brand" id="asset_brand" type="text" class="mt-1 block w-full" />
                                    </div>
                                    <div>
                                        <x-label for="asset_model" value="Model" />
                                        <x-input wire:model="asset.model" id="asset_model" type="text" class="mt-1 block w-full" />
                                    </div>
                                    <div>
                                        <x-label for="asset_serial" value="Serial Number" />
                                        <x-input wire:model="asset.serial_number" id="asset_serial" type="text" class="mt-1 block w-full" />
                                    </div>
                                </div>
                            </div>

                            <!-- Identification and Status -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <x-label for="asset_barcode" value="Barcode/Inventory ID" />
                                    <x-input wire:model="asset.barcode" id="asset_barcode" type="text" class="mt-1 block w-full" />
                                </div>
                                <div>
                                    <x-label for="asset_status" value="Status *" />
                                    <select wire:model="asset.status" id="asset_status" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                        <option value="">Select Status</option>
                                        <option value="available">Available</option>
                                        <option value="in-use">In Use</option>
                                        <option value="maintenance">Under Repair</option>
                                        <option value="disposed">Disposed</option>
                                    </select>
                                    @error('asset.status') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <!-- Categorization -->
                            <div class="mb-4">
                                <x-label value="Category *" />
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <select
                                            wire:model.live="asset.major_category_id"
                                            id="asset_major_category"
                                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                            <option value="">Select Major Category</option>
                                            @foreach($majorCategories as $major)
                                                <option value="{{ $major->id }}">{{ $major->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('asset.major_category_id') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <select
                                            wire:model="asset.category_id"
                                            id="asset_category"
                                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                            @if($asset['major_category_id']) disabled @endif
                                        >
                                            <option value="">Select Minor Category</option>
                                            @foreach($minorCategories as $minor)
                                                <option value="{{ $minor->id }}">{{ $minor->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('asset.category_id') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Financial Information -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <x-label for="asset_purchase_date" value="Purchase Date" />
                                    <x-input wire:model="asset.purchase_date" id="asset_purchase_date" type="date" class="mt-1 block w-full" />
                                </div>
                                <div>
                                    <x-label for="asset_value" value="Purchase Value" />
                                    <div class="mt-1 relative rounded-md shadow-sm">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500 sm:text-sm">&#8369;</span>
                                        </div>
                                        <x-input wire:model="asset.purchase_value" id="asset_value" type="number" step="0.01" class="block w-full pl-7" />
                                    </div>
                                </div>
                            </div>

                            <!-- Description -->
                            <div class="mb-4">
                                <x-label for="asset_description" value="Description" />
                                <textarea wire:model="asset.description" id="asset_description" rows="3" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"></textarea>
                            </div>

                            <div class="mt-6 flex justify-end">
                                <button
                                    type="button"
                                    @click="open = false"
                                    class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                                >
                                    Cancel
                                </button>

                                <button
                                    type="submit"
                                    wire:loading.attr="disabled"
                                    class="ml-3 inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-25"
                                >
                                    <span wire:loading.remove wire:target="saveAsset">{{ $editingAssetId ? 'Update' : 'Save' }} Item</span>
                                    <span wire:loading wire:target="saveAsset">
                                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        Processing...
                                    </span>
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Consumable Form (simplified) -->
                    <div x-show="activeTab === 'consumable'" class="mt-4">
                        <form wire:submit.prevent="saveConsumable">
                            <!-- Your consumable form fields here -->
                            <p class="py-4 text-center text-gray-500">Consumable form implementation in progress</p>

                            <div class="mt-6 flex justify-end">
                                <button
                                    type="button"
                                    @click="open = false"
                                    class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                                >
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- <div
        x-data="{ open: false }"
        @open-modal.window="if ($event.detail === 'add-item-modal') open = true"
        @keydown.escape.window="open = false"
        x-show="open"
        style="display: none;"
        class="fixed inset-0 overflow-y-auto px-4 py-6 sm:px-0 z-50"
        >
        <div
            x-show="open"
            class="fixed inset-0 transform transition-all"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click="open = false"
        >
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>

        <div
            x-show="open"
            class="mb-6 bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:w-full sm:max-w-2xl sm:mx-auto"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        >
            <!-- Copy the content from your x-modal component here -->
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900">
                    Add Inventory Item
                </h2>

                <div x-data="{ activeTab: 'asset' }" class="mt-4">
                    <!-- Tabs -->
                    <div class="border-b border-gray-200">
                        <nav class="-mb-px flex space-x-8">
                            <button
                                @click="activeTab = 'asset'"
                                :class="{'border-blue-500 text-blue-600': activeTab === 'asset', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'asset'}"
                                class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                Asset
                            </button>
                            <button
                                @click="activeTab = 'consumable'"
                                :class="{'border-blue-500 text-blue-600': activeTab === 'consumable', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'consumable'}"
                                class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                Consumable
                            </button>
                        </nav>
                    </div>
                    <div class="text-xs text-gray-500 mb-2">
                        Debug: Selected Major: {{ $asset['major_category_id'] ?? 'none' }} |
                        Minor Categories Available: {{ $minorCategories->count() }}
                    </div>
                    <!-- Asset Form -->
                    <div x-show="activeTab === 'asset'" class="mt-4">
                        <form wire:submit.prevent="saveAsset">
                            <!-- Required Asset Information -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <x-label for="asset_po_number" value="P.O. Number" />
                                    <x-input wire:model="asset.po_number" id="asset_po_number" type="text" class="mt-1 block w-full" />
                                </div>
                                <div>
                                    <x-label for="asset_property_tag" value="Property/Tag Number *" />
                                    <x-input wire:model="asset.property_tag_number" id="asset_property_tag" type="text" class="mt-1 block w-full" />
                                    @error('asset.property_tag_number') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <!-- Optional Device Information (if applicable) -->
                            <div class="mb-4">
                                <div class="flex items-center justify-between mb-2">
                                    <h3 class="text-sm font-medium text-gray-700">Device Information (Optional)</h3>
                                    <span class="text-xs text-gray-500">Leave blank if not applicable</span>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <x-label for="asset_brand" value="Brand" />
                                        <x-input wire:model="asset.brand" id="asset_brand" type="text" class="mt-1 block w-full" placeholder="e.g., Dell, HP" />
                                    </div>
                                    <div>
                                        <x-label for="asset_model" value="Model" />
                                        <x-input wire:model="asset.model" id="asset_model" type="text" class="mt-1 block w-full" placeholder="e.g., Latitude 5520" />
                                    </div>
                                    <div>
                                        <x-label for="asset_serial" value="Serial Number" />
                                        <x-input wire:model="asset.serial_number" id="asset_serial" type="text" class="mt-1 block w-full" placeholder="e.g., ABC123XYZ" />
                                    </div>
                                </div>
                            </div>

                            <!-- Identification and Tracking -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <x-label for="asset_barcode" value="Barcode/Inventory ID" />
                                    <x-input wire:model="asset.barcode" id="asset_barcode" type="text" class="mt-1 block w-full" />
                                </div>
                                <div>
                                    <x-label for="asset_status" value="Status *" />
                                    <select wire:model="asset.status" id="asset_status" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                        <option value="">Select Status</option>
                                        <option value="available">Available</option>
                                        <option value="in-use">In Use</option>
                                        <option value="maintenance">Under Repair</option>
                                        <option value="disposed">Disposed</option>
                                    </select>
                                    @error('asset.status') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <!-- Categorization -->
                            <div class="mb-4">
                                <x-label for="asset_category" value="Category *" />
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <select
                                            wire:model.live="asset.major_category_id"
                                            id="asset_major_category"
                                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                            <option value="">Select Major Category</option>
                                            @foreach($majorCategories as $major)
                                                <option value="{{ $major->id }}">{{ $major->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <select wire:model="asset.category_id" id="asset_category" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" {{ !$asset['major_category_id']}}>
                                            <option value="">Select Minor Category</option>
                                            @foreach($minorCategories as $minor)
                                                <option value="{{ $minor->id }}">{{ $minor->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('asset.category_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Financial Information -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <x-label for="asset_purchase_date" value="Purchase Date" />
                                    <x-input wire:model="asset.purchase_date" id="asset_purchase_date" type="date" class="mt-1 block w-full" />
                                </div>
                                <div>
                                    <x-label for="asset_value" value="Purchase Value" />
                                    <div class="mt-1 relative rounded-md shadow-sm">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500 sm:text-sm">&#8369;</span>
                                        </div>
                                        <x-input wire:model="asset.purchase_value" id="asset_value" type="number" step="0.01" class="block w-full pl-7" />
                                    </div>
                                </div>
                            </div>

                            <!-- Additional Information -->
                            <div class="mb-4">
                                <x-label for="asset_description" value="Description" />
                                <textarea wire:model="asset.description" id="asset_description" rows="3" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"></textarea>
                            </div>

                            <div class="mt-6 flex justify-end">
                                <button
                                    type="button"
                                    @click="open = false"
                                    class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:border-blue-300 focus:ring ring-blue-200 active:text-gray-800 active:bg-gray-50 disabled:opacity-25 transition"
                                >
                                    Cancel
                                </button>

                                <button
                                    type="submit"
                                    wire:loading.attr="disabled"
                                    class="ml-3 inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-800 focus:outline-none focus:border-blue-800 focus:ring ring-blue-300 disabled:opacity-25 transition"
                                >
                                    <div wire:loading wire:target="saveAsset" class="absolute inset-0 bg-white bg-opacity-50 flex items-center justify-center z-50">
                                        <svg class="animate-spin h-10 w-10 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                    </div>
                                    Save Item
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Consumable Form -->
                    <div x-show="activeTab === 'consumable'" class="mt-4">
                        <form wire:submit="saveConsumable">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <x-label for="consumable_name" value="Item Name" />
                                    <x-input wire:model="consumable.name" id="consumable_name" type="text" class="mt-1 block w-full" required />
                                </div>
                                <div>
                                    <x-label for="consumable_id" value="Item ID" />
                                    <x-input wire:model="consumable.item_id" id="consumable_id" type="text" class="mt-1 block w-full" required />
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                <div>
                                    <x-label for="consumable_category" value="Category" />
                                    <x-select wire:model="consumable.category_id" id="consumable_category" class="mt-1 block w-full">
                                        <option value="">Select Category</option>
                                        <option value="1">Office Supplies</option>
                                        <option value="2">Printer Supplies</option>
                                        <option value="3">Kitchen Supplies</option>
                                        <option value="4">Cleaning Materials</option>
                                    </x-select>
                                </div>
                                <div>
                                    <x-label for="consumable_quantity" value="Quantity" />
                                    <x-input wire:model="consumable.quantity" id="consumable_quantity" type="number" min="0" class="mt-1 block w-full" />
                                </div>
                                <div>
                                    <x-label for="consumable_unit" value="Unit" />
                                    <x-select wire:model="consumable.unit" id="consumable_unit" class="mt-1 block w-full">
                                        <option value="">Select Unit</option>
                                        <option value="each">Each</option>
                                        <option value="pack">Pack</option>
                                        <option value="box">Box</option>
                                        <option value="ream">Ream</option>
                                        <option value="cartridge">Cartridge</option>
                                    </x-select>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <x-label for="consumable_reorder_level" value="Reorder Level" />
                                    <x-input wire:model="consumable.reorder_level" id="consumable_reorder_level" type="number" min="0" class="mt-1 block w-full" />
                                </div>
                                <div>
                                    <x-label for="consumable_location" value="Location" />
                                    <x-select wire:model="consumable.location_id" id="consumable_location" class="mt-1 block w-full">
                                        <option value="">Select Location</option>
                                        <option value="1">Supply Room</option>
                                        <option value="2">IT Storage</option>
                                        <option value="3">Main Office</option>
                                        <option value="4">Warehouse</option>
                                    </x-select>
                                </div>
                            </div>
                            <div class="mb-4">
                                <x-label for="consumable_description" value="Description" />
                                <x-textarea wire:model="consumable.description" id="consumable_description" rows="3" class="mt-1 block w-full" />
                            </div>
                        </form>
                    </div>
                    <!-- Unassigned Assets List -->
                    <div x-show="activeTab === 'assignments'" class="mt-4">
                        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                            <div class="px-4 py-5 sm:px-6 flex justify-between items-center">
                                <h3 class="text-lg leading-6 font-medium text-gray-900">Unassigned Assets</h3>

                                <div class="flex space-x-2">
                                    <select
                                        wire:model="bulkAction"
                                        class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                    >
                                        <option value="">Select Action</option>
                                        <option value="assign-location">Assign to Location</option>
                                        <option value="assign-user">Assign to User</option>
                                    </select>

                                    <button
                                        wire:click="openAssignModal"
                                        type="button"
                                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                        {{ count($selectedAssets) === 0 || empty($bulkAction) ? 'disabled' : '' }}
                                    >
                                        Apply
                                    </button>
                                </div>
                            </div>

                            <div class="border-t border-gray-200">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                <input
                                                    type="checkbox"
                                                    wire:model="selectAll"
                                                    class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"
                                                >
                                            </th>
                                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Tag Number
                                            </th>
                                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Barcode
                                            </th>
                                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Brand & Model
                                            </th>
                                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Category
                                            </th>
                                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Status
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @forelse($unassignedAssetsList as $unassignedAsset)
                                            <tr>
                                                <td class="px-4 py-4 whitespace-nowrap">
                                                    <input
                                                        type="checkbox"
                                                        wire:model="selectedAssets"
                                                        value="{{ $unassignedAsset->id }}"
                                                        class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"
                                                    >
                                                </td>
                                                <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                    {{ $unassignedAsset->property_tag_number }}
                                                </td>
                                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $unassignedAsset->barcode ?? 'N/A' }}
                                                </td>
                                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $unassignedAsset->brand }} {{ $unassignedAsset->model }}
                                                </td>
                                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $unassignedAsset->category->name ?? 'Uncategorized' }}
                                                </td>
                                                <td class="px-4 py-4 whitespace-nowrap">
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                        {{ ucfirst($unassignedAsset->status) }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                                    No unassigned assets found.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>


            </div>
        </div>
    </div> --}}

    <!-- Asset Deletion Confirmation Modal -->
    <div
        x-data="{ open: @entangle('confirmingAssetDeletion') }"
        x-show="open"
        style="display: none;"
        class="fixed inset-0 overflow-y-auto px-4 py-6 sm:px-0 z-50"
    >
        <div
            x-show="open"
            class="fixed inset-0 transform transition-all"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click="open = false"
        >
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>

        <div
            x-show="open"
            class="mb-6 bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:w-full sm:max-w-lg sm:mx-auto"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        >
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900">Confirm Asset Deletion</h3>
                <p class="mt-2 text-sm text-gray-500">
                    Are you sure you want to delete this asset? This action cannot be undone.
                </p>
                <div class="mt-6 flex justify-end space-x-3">
                    <button
                        type="button"
                        wire:click="$set('confirmingAssetDeletion', false)"
                        class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                    >
                        Cancel
                    </button>
                    <button
                        type="button"
                        wire:click="deleteAsset"
                        class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                    >
                        Delete Asset
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Manage Categories Modal -->
   <div
    x-data="{ open: false, mode: 'list', editingCategory: null }"
    @open-modal.window="if ($event.detail === 'manage-categories-modal') { open = true; mode = 'list'; }"
    @keydown.escape.window="open = false"
    x-show="open"
    style="display: none;"
    class="fixed inset-0 overflow-y-auto px-4 py-6 sm:px-0 z-50"
    >
        <div
            x-show="open"
            class="fixed inset-0 transform transition-all"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click="open = false"
        >
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>

        <div
            x-show="open"
            class="mb-6 bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:w-full sm:max-w-3xl sm:mx-auto"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        >

            <div class="p-6">
                <!-- Header -->
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-medium text-gray-900" x-text="mode === 'list' ? 'Manage Categories' : (mode === 'add' ? 'Add Category' : 'Edit Category')"></h2>
                    <button @click="open = false" class="text-gray-400 hover:text-gray-500">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Flash messages -->
                @if (session()->has('message'))
                    <div class="rounded-md bg-green-50 p-4 mb-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-green-800">
                                    {{ session('message') }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endif

                @if (session()->has('error'))
                    <div class="rounded-md bg-red-50 p-4 mb-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-red-800">
                                    {{ session('error') }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- List Mode -->
                <div x-show="mode === 'list'" x-data="{ expandedCategories: {} }">
                    <div class="flex justify-end mb-4">
                        <button
                            @click="mode = 'add'; editingCategory = null"
                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                            </svg>
                            Add New Category
                        </button>
                    </div>

                    <!-- Categories Table -->
                    <div class="mt-2 overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Category Name
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Type
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Parent Category
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <!-- Major Categories -->
                                @forelse($categories->where('type', 'major') as $majorCategory)
                                    <!-- Major category row -->
                                    <tr class="bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            <div class="flex items-center">
                                                <button @click="expandedCategories[{{ $majorCategory->id }}] = !expandedCategories[{{ $majorCategory->id }}]"
                                                        class="mr-2 text-gray-500 hover:text-gray-700 focus:outline-none">
                                                    <svg x-show="!expandedCategories[{{ $majorCategory->id }}]" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                                    </svg>
                                                    <svg x-show="expandedCategories[{{ $majorCategory->id }}]" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 12H6" />
                                                    </svg>
                                                </button>
                                                <span>{{ $majorCategory->name }}</span>
                                                <span class="ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-200 text-gray-800">
                                                    {{ $categories->where('type', 'minor')->where('parent_id', $majorCategory->id)->count() }}
                                                </span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                Major
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            -
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <button
                                                @click="mode = 'edit'; editingCategory = {
                                                    id: {{ $majorCategory->id }},
                                                    name: '{{ $majorCategory->name }}',
                                                    type: 'major',
                                                    parent_id: null,
                                                    description: '{{ $majorCategory->description ?? '' }}'
                                                }"
                                                class="text-indigo-600 hover:text-indigo-900 mr-3"
                                            >
                                                Edit
                                            </button>
                                            <button
                                                @click="$wire.confirmDeleteCategory({{ $majorCategory->id }})"
                                                class="text-red-600 hover:text-red-900"
                                            >
                                                Delete
                                            </button>
                                        </td>
                                    </tr>

                                    <!-- Minor categories for this major category -->
                                    @foreach($categories->where('type', 'minor')->where('parent_id', $majorCategory->id) as $minorCategory)
                                        <tr
                                            x-show="expandedCategories[{{ $majorCategory->id }}]"
                                            x-transition:enter="transition ease-out duration-200"
                                            x-transition:enter-start="opacity-0 transform -translate-y-2"
                                            x-transition:enter-end="opacity-100 transform translate-y-0"
                                            x-transition:leave="transition ease-in duration-150"
                                            x-transition:leave-start="opacity-100 transform translate-y-0"
                                            x-transition:leave-end="opacity-0 transform -translate-y-2"
                                            x-cloak
                                            class="bg-gray-100">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 pl-10">
                                                <span class="ml-5">{{ $minorCategory->name }}</span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                    Minor
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $majorCategory->name }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <button
                                                    @click="mode = 'edit'; editingCategory = {
                                                        id: {{ $minorCategory->id }},
                                                        name: '{{ $minorCategory->name }}',
                                                        type: 'minor',
                                                        parent_id: {{ $minorCategory->parent_id }},
                                                        description: '{{ $minorCategory->description ?? '' }}'
                                                    }"
                                                    class="text-indigo-600 hover:text-indigo-900 mr-3"
                                                >
                                                    Edit
                                                </button>
                                                <button
                                                    @click="$wire.confirmDeleteCategory({{ $minorCategory->id }})"
                                                    class="text-red-600 hover:text-red-900"
                                                >
                                                    Delete
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                            No categories found. Click "Add New Category" to create one.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Add/Edit Mode -->
                <div x-show="mode === 'add' || mode === 'edit'">
                    <form wire:submit.prevent="saveCategory">
                        <div class="space-y-4">
                            <div>
                                <x-label for="category_name" value="Category Name" />
                                <x-input
                                    id="category_name"
                                    type="text"
                                    class="mt-1 block w-full"
                                    x-bind:value="editingCategory ? editingCategory.name : ''"
                                    wire:model="category.name"
                                    required
                                />
                            </div>

                            <div>
                                <x-label for="category_type" value="Category Type" />
                                <select
                                    id="category_type"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                    x-on:change="editingCategory ? editingCategory.type = $event.target.value : null"
                                    wire:model="category.type"
                                >
                                    <option value="major">Major Category</option>
                                    <option value="minor">Minor Category</option>
                                </select>
                                <p class="mt-1 text-sm text-gray-500">Major categories are top-level categories. Minor categories belong to a major category.</p>
                            </div>

                            <div x-show="!editingCategory || editingCategory.type === 'minor'">
                                <x-label for="parent_category" value="Parent Category" />
                                <select
                                    id="parent_category"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                    x-on:change="editingCategory ? editingCategory.parent_id = $event.target.value : null"
                                    wire:model="category.parent_id"
                                    x-bind:disabled="editingCategory && editingCategory.type === 'major'"
                                >
                                    <option value="">Select Parent Category</option>
                                        @foreach($majorCategories as $major)
                                            <option value="{{ $major->id }}">{{ $major->name }}</option>
                                        @endforeach
                                </select>
                            </div>

                            <div>
                                <x-label for="category_description" value="Description (Optional)" />
                                <textarea
                                    id="category_description"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                    rows="3"
                                    x-bind:value="editingCategory ? editingCategory.description : ''"
                                    wire:model="category.description"
                                ></textarea>
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end space-x-3">
                            <button
                                type="button"
                                @click="mode = 'list'"
                                class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition"
                            >
                                Cancel
                            </button>

                            <button
                                type="button"
                                @click="$wire.saveCategory(editingCategory ? editingCategory.id : null); mode = 'list';"
                                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition"
                            >
                                Save Category
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Category Deletion Confirmation Modal -->
    <div
    x-data="{ open: @entangle('confirmingCategoryDeletion') }"
    x-show="open"
    style="display: none;"
    class="fixed inset-0 overflow-y-auto px-4 py-6 sm:px-0 z-50"
    >
        <div
            x-show="open"
            class="fixed inset-0 transform transition-all"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click="open = false"
        >
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>

        <div
            x-show="open"
            class="mb-6 bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:w-full sm:max-w-lg sm:mx-auto"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        >
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900">Confirm Category Deletion</h3>
                <p class="mt-2 text-sm text-gray-500">
                    Are you sure you want to delete this category? This action cannot be undone.
                </p>
                <div class="mt-6 flex justify-end space-x-3">
                    <button
                        type="button"
                        wire:click="$set('confirmingCategoryDeletion', false)"
                        class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition"
                    >
                        Cancel
                    </button>
                    <button
                        type="button"
                        wire:click="deleteCategory"
                        class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 disabled:opacity-25 transition"
                    >
                        Delete Category
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Assign Assets Modal -->
    <div
    x-data="{ open: @entangle('showAssignModal') }"
    x-show="open"
    style="display: none;"
    class="fixed inset-0 overflow-y-auto px-4 py-6 sm:px-0 z-50"
    >
        <div
            x-show="open"
            class="fixed inset-0 transform transition-all"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click="open = false"
        >
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>

        <div
            x-show="open"
            class="mb-6 bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:w-full sm:max-w-lg sm:mx-auto"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        >
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900">
                    {{ $bulkAction === 'assign-location' ? 'Assign Assets to Location' : 'Assign Assets to User' }}
                </h3>

                <div class="mt-4">
                    <p class="text-sm text-gray-600">
                        You are about to assign {{ count($selectedAssets) }} asset(s).
                    </p>

                    @if($bulkAction === 'assign-location')
                        <div class="mt-4">
                            <x-label for="location" value="Select Location" />
                            <select
                                wire:model="assignLocation"
                                id="location"
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                            >
                                <option value="">Select Location</option>
                                @foreach($locationsList as $location)
                                    <option value="{{ $location->id }}">{{ $location->name }} ({{ ucfirst($location->type) }})</option>
                                @endforeach
                            </select>
                        </div>
                    @elseif($bulkAction === 'assign-user')
                        <div class="mt-4">
                            <x-label for="user" value="Select User" />
                            <select
                                wire:model="assignToUser"
                                id="user"
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                            >
                                <option value="">Select User</option>
                                @foreach($usersList as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <div class="mt-4">
                        <x-label for="notes" value="Notes (Optional)" />
                        <textarea
                            wire:model="movementNotes"
                            id="notes"
                            rows="3"
                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                        ></textarea>
                    </div>
                </div>

                <div class="mt-6 flex justify-end space-x-3">
                    <button
                        type="button"
                        wire:click="$set('showAssignModal', false)"
                        class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                    >
                        Cancel
                    </button>

                    <button
                        type="button"
                        wire:click="assignAssets"
                        class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                    >
                        Confirm Assignment
                    </button>
                </div>
            </div>
        </div>
    </div>


    <!-- Manage Locations Modal -->
    <div
        x-data="{ open: false, mode: 'list', editingLocation: null }"
        @open-modal.window="if ($event.detail === 'manage-locations-modal') { open = true; mode = 'list'; }"
        @keydown.escape.window="open = false"
        x-show="open"
        style="display: none;"
        class="fixed inset-0 overflow-y-auto px-4 py-6 sm:px-0 z-50"
    >
        <div
            x-show="open"
            class="fixed inset-0 transform transition-all"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click="open = false"
        >
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>

        <div
            x-show="open"
            class="mb-6 bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:w-full sm:max-w-3xl sm:mx-auto"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        >
            <div class="p-6">
                <!-- Header -->
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-medium text-gray-900" x-text="mode === 'list' ? 'Manage Locations' : (mode === 'add' ? 'Add Location' : 'Edit Location')"></h2>
                    <button @click="open = false" class="text-gray-400 hover:text-gray-500">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Flash messages -->
                @if (session()->has('message'))
                    <div class="rounded-md bg-green-50 p-4 mb-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-green-800">
                                    {{ session('message') }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endif

                @if (session()->has('error'))
                    <div class="rounded-md bg-red-50 p-4 mb-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-red-800">
                                    {{ session('error') }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- List Mode -->
                <div x-show="mode === 'list'">
                    <div class="flex justify-end mb-4">
                        <button
                            @click="mode = 'add'; editingLocation = null"
                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                            </svg>
                            Add New Location
                        </button>
                    </div>

                    <!-- Locations Table -->
                    <div class="mt-2 overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Location Name
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Code
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Type
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($locationsList as $loc)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $loc->name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $loc->code ?? 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                {{ ucfirst($loc->type) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            @if($loc->is_active)
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                    Active
                                                </span>
                                            @else
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                    Inactive
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <button
                                                @click="mode = 'edit'; editingLocation = {
                                                    id: {{ $loc->id }},
                                                    name: '{{ $loc->name }}',
                                                    code: '{{ $loc->code ?? '' }}',
                                                    address: '{{ $loc->address ?? '' }}',
                                                    type: '{{ $loc->type }}',
                                                    description: '{{ $loc->description ?? '' }}',
                                                    is_active: {{ $loc->is_active ? 'true' : 'false' }}
                                                }"
                                                class="text-green-600 hover:text-green-900 mr-3"
                                            >
                                                Edit
                                            </button>
                                            <button
                                                @click="$wire.confirmDeleteLocation({{ $loc->id }})"
                                                class="text-red-600 hover:text-red-900"
                                            >
                                                Delete
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                            No locations found. Click "Add New Location" to create one.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Add/Edit Mode -->
                <div x-show="mode === 'add' || mode === 'edit'">
                    <form wire:submit.prevent="saveLocation">
                        <div class="space-y-4">
                            <div>
                                <x-label for="location_name" value="Location Name" />
                                <x-input
                                    id="location_name"
                                    type="text"
                                    class="mt-1 block w-full"
                                    x-bind:value="editingLocation ? editingLocation.name : ''"
                                    wire:model="location.name"
                                    required
                                />
                            </div>

                            <div>
                                <x-label for="location_code" value="Location Code (Optional)" />
                                <x-input
                                    id="location_code"
                                    type="text"
                                    class="mt-1 block w-full"
                                    x-bind:value="editingLocation ? editingLocation.code : ''"
                                    wire:model="location.code"
                                />
                            </div>

                            <div>
                                <x-label for="location_type" value="Location Type" />
                                <select
                                    id="location_type"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                    x-on:change="editingLocation ? editingLocation.type = $event.target.value : null"
                                    wire:model="location.type"
                                >
                                    <option value="office">Office</option>
                                    <option value="storage">Storage</option>
                                    <option value="warehouse">Warehouse</option>
                                    <option value="remote">Remote Site</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>

                            <div>
                                <x-label for="location_address" value="Address (Optional)" />
                                <x-input
                                    id="location_address"
                                    type="text"
                                    class="mt-1 block w-full"
                                    x-bind:value="editingLocation ? editingLocation.address : ''"
                                    wire:model="location.address"
                                />
                            </div>

                            <div>
                                <x-label for="location_description" value="Description (Optional)" />
                                <textarea
                                    id="location_description"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                    rows="3"
                                    x-bind:value="editingLocation ? editingLocation.description : ''"
                                    wire:model="location.description"
                                ></textarea>
                            </div>

                            <div class="flex items-center">
                                <input
                                    id="location_active"
                                    type="checkbox"
                                    class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                                    x-bind:checked="editingLocation ? editingLocation.is_active : true"
                                    wire:model="location.is_active"
                                />
                                <label for="location_active" class="ml-2 block text-sm text-gray-900">
                                    Active
                                </label>
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end space-x-3">
                            <button
                                type="button"
                                @click="mode = 'list'"
                                class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition"
                            >
                                Cancel
                            </button>

                            <button
                                type="button"
                                @click="$wire.saveLocation(editingLocation ? editingLocation.id : null); mode = 'list';"
                                class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 disabled:opacity-25 transition"
                            >
                                Save Location
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Location Deletion Confirmation Modal -->
    <div
        x-data="{ open: @entangle('confirmingLocationDeletion') }"
        x-show="open"
        style="display: none;"
        class="fixed inset-0 overflow-y-auto px-4 py-6 sm:px-0 z-50"
    >
        <div
            x-show="open"
            class="fixed inset-0 transform transition-all"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click="open = false"
        >
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>

        <div
            x-show="open"
            class="mb-6 bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:w-full sm:max-w-lg sm:mx-auto"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        >
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900">Confirm Location Deletion</h3>
                <p class="mt-2 text-sm text-gray-500">
                    Are you sure you want to delete this location? This action cannot be undone.
                </p>
                <div class="mt-6 flex justify-end space-x-3">
                    <button
                        type="button"
                        wire:click="$set('confirmingLocationDeletion', false)"
                        class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                    >
                        Cancel
                    </button>
                    <button
                        type="button"
                        wire:click="deleteLocation"
                        class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                    >
                        Delete Location
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
