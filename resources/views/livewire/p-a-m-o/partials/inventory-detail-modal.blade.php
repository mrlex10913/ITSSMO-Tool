<!-- Asset Details Modal -->
<div
    x-data="{ open: @entangle('showAssetDetailsModal') }"
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
        class="mb-6 bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:w-full sm:max-w-4xl sm:mx-auto"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
    >
        <div class="p-6">
            @if($viewingAsset)
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-medium text-gray-900">
                    Asset Details: {{ $viewingAsset->property_tag_number }}
                </h2>
                <button @click="open = false" class="text-gray-400 hover:text-gray-500">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Asset Information -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-md font-medium text-gray-900 mb-2">Asset Information</h3>
                    <dl class="grid grid-cols-1 gap-x-4 gap-y-2 sm:grid-cols-2">
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500">Property Tag</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $viewingAsset->property_tag_number }}</dd>
                        </div>
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500">PO Number</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $viewingAsset->po_number ?? 'N/A' }}</dd>
                        </div>
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500">Barcode</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $viewingAsset->barcode ?? 'N/A' }}</dd>
                        </div>
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500">Status</dt>
                            <dd class="mt-1 text-sm">
                                @if($viewingAsset->status == 'available')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Available</span>
                                @elseif($viewingAsset->status == 'in-use')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">In Use</span>
                                @elseif($viewingAsset->status == 'maintenance')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Under Repair</span>
                                @elseif($viewingAsset->status == 'disposed')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Disposed</span>
                                @endif
                            </dd>
                        </div>
                    </dl>
                </div>

                <!-- Device Information -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-md font-medium text-gray-900 mb-2">Device Information</h3>
                    <dl class="grid grid-cols-1 gap-x-4 gap-y-2 sm:grid-cols-2">
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500">Brand</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $viewingAsset->brand ?? 'N/A' }}</dd>
                        </div>
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500">Model</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $viewingAsset->model ?? 'N/A' }}</dd>
                        </div>
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500">Serial Number</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $viewingAsset->serial_number ?? 'N/A' }}</dd>
                        </div>
                        <div class="sm:col-span-2">
                            <dt class="text-sm font-medium text-gray-500">Description</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $viewingAsset->description ?? 'No description available' }}</dd>
                        </div>
                    </dl>
                </div>

                <!-- Category Information -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-md font-medium text-gray-900 mb-2">Categorization</h3>
                    <dl class="grid grid-cols-1 gap-x-4 gap-y-2">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Major Category</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ $viewingAsset->category && $viewingAsset->category->parent ? $viewingAsset->category->parent->name : 'Uncategorized' }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Minor Category</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $viewingAsset->category ? $viewingAsset->category->name : 'Uncategorized' }}</dd>
                        </div>
                    </dl>
                </div>

                <!-- Financial Information -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-md font-medium text-gray-900 mb-2">Financial Information</h3>
                    <dl class="grid grid-cols-1 gap-x-4 gap-y-2 sm:grid-cols-2">
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500">Purchase Date</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $viewingAsset->purchase_date ? $viewingAsset->purchase_date->format('M d, Y') : 'N/A' }}</dd>
                        </div>
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500">Purchase Value</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                ₱{{ $viewingAsset->purchase_value ? number_format($viewingAsset->purchase_value, 2) : '0.00' }}
                            </dd>
                        </div>
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500">Current Value</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                ₱{{ number_format($viewingAsset->getCurrentValue(), 2) }}
                            </dd>
                        </div>
                    </dl>
                </div>

                <!-- Location and Assignment -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-md font-medium text-gray-900 mb-2">Location & Assignment</h3>
                    <dl class="grid grid-cols-1 gap-x-4 gap-y-2">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Current Location</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $viewingAsset->location ? $viewingAsset->location->name : 'Not assigned to a location' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Assigned To</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $viewingAsset->assignedUser ? $viewingAsset->assignedUser->name : 'Not assigned to a user' }}</dd>
                        </div>
                    </dl>
                </div>

                <!-- Recent Activity -->
                <div class="bg-gray-50 p-4 rounded-lg sm:col-span-2">
                    <h3 class="text-md font-medium text-gray-900 mb-2">Recent Activity</h3>
                    @if($viewingAsset->movements && $viewingAsset->movements->count() > 0)
                        <div class="flow-root">
                            <ul role="list" class="-mb-8">
                                @foreach($viewingAsset->movements as $movement)
                                    <li>
                                        <div class="relative pb-8">
                                            @if(!$loop->last)
                                                <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                            @endif
                                            <div class="relative flex space-x-3">
                                                <div>
                                                    <span class="h-8 w-8 rounded-full {{ $movement->movement_type == 'transfer' ? 'bg-blue-500' : 'bg-green-500' }} flex items-center justify-center ring-8 ring-white">
                                                        <svg class="h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                            @if($movement->movement_type == 'transfer')
                                                                <path d="M8 5a1 1 0 100 2h5.586l-1.293 1.293a1 1 0 001.414 1.414l3-3a1 1 0 000-1.414l-3-3a1 1 0 10-1.414 1.414L13.586 5H8zM12 15a1 1 0 100-2H6.414l1.293-1.293a1 1 0 10-1.414-1.414l-3 3a1 1 0 000 1.414l3 3a1 1 0 001.414-1.414L6.414 15H12z" />
                                                            @else
                                                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                                                <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                                                            @endif
                                                        </svg>
                                                    </span>
                                                </div>
                                                <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                                    <div>
                                                        <p class="text-sm text-gray-500">
                                                            @if($movement->movement_type == 'transfer')
                                                                Transferred from
                                                                <span class="font-medium text-gray-900">{{ $movement->fromLocation ? $movement->fromLocation->name : 'Unknown' }}</span>
                                                                to
                                                                <span class="font-medium text-gray-900">{{ $movement->toLocation ? $movement->toLocation->name : 'Unknown' }}</span>
                                                            @else
                                                                Assigned to
                                                                <span class="font-medium text-gray-900">{{ $movement->assignedToUser ? $movement->assignedToUser->name : 'Unknown' }}</span>
                                                            @endif
                                                        </p>
                                                        @if($movement->notes)
                                                            <p class="text-xs text-gray-500 mt-1">{{ $movement->notes }}</p>
                                                        @endif
                                                    </div>
                                                    <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                                        <time datetime="{{ $movement->movement_date }}">{{ $movement->movement_date ? $movement->movement_date->format('M d, Y') : 'Unknown date' }}</time>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @else
                        <p class="text-sm text-gray-500">No recent activity recorded for this asset.</p>
                    @endif
                </div>
            </div>

            <div class="mt-6 flex justify-end space-x-3">
                <button
                    type="button"
                    wire:click="editAsset({{ $viewingAsset->id }})"
                    @click="open = false"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                >
                    <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                    </svg>
                    Edit Asset
                </button>
                <button
                    type="button"
                    @click="open = false"
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                >
                    Close
                </button>
            </div>
            @endif
        </div>
    </div>
</div>
