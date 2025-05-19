<div x-show="$wire.showExportModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div x-show="$wire.showExportModal"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 transition-opacity">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen"></span>&#8203;

        <div x-show="$wire.showExportModal"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             class="inline-block px-4 pt-5 pb-4 overflow-hidden text-left align-bottom bg-white rounded-lg shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">

            <div class="absolute top-0 right-0 pt-4 pr-4">
                <button wire:click="$set('showExportModal', false)" type="button" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                    <span class="sr-only">Close</span>
                    <span class="material-symbols-sharp">close</span>
                </button>
            </div>

            <div class="sm:flex sm:items-start">
                <div class="flex items-center justify-center flex-shrink-0 w-12 h-12 mx-auto bg-purple-100 rounded-full sm:mx-0 sm:h-10 sm:w-10">
                    <i class="fas fa-file-export text-purple-600"></i>
                </div>
                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">
                        Export Asset Report
                    </h3>
                    <div class="mt-2">
                        <p class="text-sm text-gray-500">
                            Select options below to customize your report before exporting.
                        </p>
                    </div>
                </div>
            </div>

            <div class="mt-5">
                <div class="space-y-4">
                    <!-- Report Type -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Report Type</label>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="border rounded-lg p-3 cursor-pointer hover:bg-gray-50 transition-colors"
                                 :class="{ 'border-purple-500 bg-purple-50': $wire.reportType === 'inventory' }"
                                 wire:click="$set('reportType', 'inventory')">
                                <div class="flex items-center">
                                    <i class="fas fa-clipboard-list text-gray-500 mr-2"></i>
                                    <span class="text-sm font-medium text-gray-700">Inventory Report</span>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">List of all assets with details</p>
                            </div>
                            <div class="border rounded-lg p-3 cursor-pointer hover:bg-gray-50 transition-colors"
                                 :class="{ 'border-purple-500 bg-purple-50': $wire.reportType === 'movement' }"
                                 wire:click="$set('reportType', 'movement')">
                                <div class="flex items-center">
                                    <i class="fas fa-exchange-alt text-gray-500 mr-2"></i>
                                    <span class="text-sm font-medium text-gray-700">Movement Report</span>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Asset transfer/movement history</p>
                            </div>
                        </div>
                    </div>

                    <!-- Filter Options -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Filter Options</label>
                        <div class="space-y-2">
                            <div>
                                <select wire:model="exportCategory" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 text-sm">
                                    <option value="">All Categories</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <select wire:model="exportLocation" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 text-sm">
                                    <option value="">All Locations</option>
                                    @foreach($locations as $location)
                                        <option value="{{ $location->id }}">{{ $location->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <select wire:model="exportStatus" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 text-sm">
                                    <option value="">All Statuses</option>
                                    @foreach($statuses as $status)
                                        <option value="{{ $status }}">{{ ucfirst($status) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div x-show="$wire.reportType === 'movement'">
                                <div class="flex space-x-2">
                                    <div class="flex-1">
                                        <label class="block text-xs font-medium text-gray-700">Date From</label>
                                        <input type="date" wire:model="dateFrom" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 text-sm">
                                    </div>
                                    <div class="flex-1">
                                        <label class="block text-xs font-medium text-gray-700">Date To</label>
                                        <input type="date" wire:model="dateTo" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 text-sm">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Fields to Include -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Fields to Include</label>
                        <div class="grid grid-cols-3 gap-2">
                            <label class="flex items-center space-x-2">
                                <input type="checkbox" wire:model="exportFields" value="property_tag_number" class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                                <span class="text-xs">Property Tag</span>
                            </label>
                            <label class="flex items-center space-x-2">
                                <input type="checkbox" wire:model="exportFields" value="brand" class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                                <span class="text-xs">Brand</span>
                            </label>
                            <label class="flex items-center space-x-2">
                                <input type="checkbox" wire:model="exportFields" value="model" class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                                <span class="text-xs">Model</span>
                            </label>
                            <label class="flex items-center space-x-2">
                                <input type="checkbox" wire:model="exportFields" value="serial_number" class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                                <span class="text-xs">Serial Number</span>
                            </label>
                            <label class="flex items-center space-x-2">
                                <input type="checkbox" wire:model="exportFields" value="category" class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                                <span class="text-xs">Category</span>
                            </label>
                            <label class="flex items-center space-x-2">
                                <input type="checkbox" wire:model="exportFields" value="status" class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                                <span class="text-xs">Status</span>
                            </label>
                            <label class="flex items-center space-x-2">
                                <input type="checkbox" wire:model="exportFields" value="purchase_date" class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                                <span class="text-xs">Purchase Date</span>
                            </label>
                            <label class="flex items-center space-x-2">
                                <input type="checkbox" wire:model="exportFields" value="purchase_value" class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                                <span class="text-xs">Purchase Value</span>
                            </label>
                            <label class="flex items-center space-x-2">
                                <input type="checkbox" wire:model="exportFields" value="location" class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                                <span class="text-xs">Location</span>
                            </label>
                            <label class="flex items-center space-x-2">
                                <input type="checkbox" wire:model="exportFields" value="assigned_to" class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                                <span class="text-xs">Assigned To</span>
                            </label>
                            <label class="flex items-center space-x-2">
                                <input type="checkbox" wire:model="exportFields" value="description" class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                                <span class="text-xs">Description</span>
                            </label>
                            <label class="flex items-center space-x-2">
                                <input type="checkbox" wire:model="exportFields" value="po_number" class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                                <span class="text-xs">PO Number</span>
                            </label>
                        </div>
                    </div>

                    <!-- Export Format -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Export Format</label>
                        <div class="grid grid-cols-3 gap-2">
                            <button wire:click="exportReport('pdf')" class="flex items-center justify-center py-2 bg-red-50 border border-red-200 rounded-md hover:bg-red-100 transition-colors">
                                <i class="fas fa-file-pdf text-red-600 mr-2"></i>
                                <span class="text-sm">PDF</span>
                            </button>
                            <button wire:click="exportReport('excel')" class="flex items-center justify-center py-2 bg-green-50 border border-green-200 rounded-md hover:bg-green-100 transition-colors">
                                <i class="fas fa-file-excel text-green-600 mr-2"></i>
                                <span class="text-sm">Excel</span>
                            </button>
                            <button wire:click="exportReport('csv')" class="flex items-center justify-center py-2 bg-blue-50 border border-blue-200 rounded-md hover:bg-blue-100 transition-colors">
                                <i class="fas fa-file-csv text-blue-600 mr-2"></i>
                                <span class="text-sm">CSV</span>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="mt-5 sm:mt-6 sm:flex sm:flex-row-reverse">
                    <button wire:click="$set('showExportModal', false)" type="button" class="inline-flex justify-center w-full px-4 py-2 mt-3 text-base font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 sm:mt-0 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
