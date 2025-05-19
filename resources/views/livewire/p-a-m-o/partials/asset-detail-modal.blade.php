<div x-show="$wire.showAssetDetailModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;" x-cloak>
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" x-show="$wire.showAssetDetailModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen"></span>&#8203;

        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full" x-show="$wire.showAssetDetailModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
            <div class="absolute top-0 right-0 pt-4 pr-4">
                <button @click="$wire.showAssetDetailModal = false" type="button" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                    <span class="sr-only">Close</span>
                    <span class="material-symbols-sharp">close</span>
                </button>
            </div>

            @if($selectedAsset)
            <div class="px-4 pt-5 pb-4 bg-white sm:p-6">
                <div class="sm:flex sm:items-start">
                    <div class="flex items-center justify-center flex-shrink-0 w-12 h-12 mx-auto bg-blue-100 rounded-full sm:mx-0 sm:h-10 sm:w-10">
                        <i class="fas fa-laptop text-blue-600"></i>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg font-medium leading-6 text-gray-900">
                            {{ $selectedAsset->brand }} {{ $selectedAsset->model }}
                        </h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">
                                Asset details and movement history
                            </p>
                        </div>
                    </div>
                </div>

                <div class="mt-5">
                    <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-2">
                        <div>
                            <h4 class="text-sm font-medium text-gray-900">General Information</h4>
                            <dl class="mt-2 text-sm text-gray-700">
                                <div class="mt-1 flex justify-between">
                                    <dt class="text-gray-500">Property Tag:</dt>
                                    <dd class="font-medium">{{ $selectedAsset->property_tag_number }}</dd>
                                </div>
                                <div class="mt-1 flex justify-between">
                                    <dt class="text-gray-500">Serial Number:</dt>
                                    <dd class="font-medium">{{ $selectedAsset->serial_number }}</dd>
                                </div>
                                <div class="mt-1 flex justify-between">
                                    <dt class="text-gray-500">Barcode:</dt>
                                    <dd class="font-medium">{{ $selectedAsset->barcode }}</dd>
                                </div>
                                <div class="mt-1 flex justify-between">
                                    <dt class="text-gray-500">PO Number:</dt>
                                    <dd class="font-medium">{{ $selectedAsset->po_number }}</dd>
                                </div>
                                <div class="mt-1 flex justify-between">
                                    <dt class="text-gray-500">Category:</dt>
                                    <dd class="font-medium">{{ $selectedAsset->category->name ?? 'N/A' }}</dd>
                                </div>
                            </dl>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-900">Purchase Information</h4>
                            <dl class="mt-2 text-sm text-gray-700">
                                <div class="mt-1 flex justify-between">
                                    <dt class="text-gray-500">Purchase Date:</dt>
                                    <dd class="font-medium">{{ $selectedAsset->purchase_date ? $selectedAsset->purchase_date->format('M j, Y') : 'N/A' }}</dd>
                                </div>
                                <div class="mt-1 flex justify-between">
                                    <dt class="text-gray-500">Purchase Value:</dt>
                                    <dd class="font-medium">₱{{ number_format($selectedAsset->purchase_value, 2) }}</dd>
                                </div>
                                <div class="mt-1 flex justify-between border-t border-gray-200 pt-2">
                                    <dt class="text-gray-500">Current Status:</dt>
                                    <dd>
                                        @if($selectedAsset->status == 'active' || $selectedAsset->status == 'assigned')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                {{ ucfirst($selectedAsset->status) }}
                                            </span>
                                        @elseif($selectedAsset->status == 'maintenance')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                {{ ucfirst($selectedAsset->status) }}
                                            </span>
                                        @else
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                {{ ucfirst($selectedAsset->status) }}
                                            </span>
                                        @endif
                                    </dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <div class="mt-6 border-t border-gray-200 pt-6">
                        <h4 class="text-sm font-medium text-gray-900">Current Assignment</h4>
                        <div class="mt-2 grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <h5 class="text-xs font-medium text-gray-700 mb-2">Location</h5>
                                @if($selectedAsset->location)
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-8 w-8 bg-blue-100 rounded-full flex items-center justify-center text-blue-500">
                                            <i class="fas fa-map-marker-alt"></i>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm font-medium text-gray-900">{{ $selectedAsset->location->name }}</p>
                                            <p class="text-xs text-gray-500">{{ $selectedAsset->location->address ?? 'No address available' }}</p>
                                        </div>
                                    </div>
                                @else
                                    <p class="text-sm text-gray-500">Not assigned to any location</p>
                                @endif
                            </div>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <h5 class="text-xs font-medium text-gray-700 mb-2">User</h5>
                                @if($selectedAsset->assignedUser)
                                    <div class="flex items-center">
                                        <img class="h-8 w-8 rounded-full" src="{{ $selectedAsset->assignedUser->profile_photo_url }}" alt="">
                                        <div class="ml-3">
                                            <p class="text-sm font-medium text-gray-900">{{ $selectedAsset->assignedUser->name }}</p>
                                            <p class="text-xs text-gray-500">{{ $selectedAsset->assignedUser->department ?? 'No department' }}</p>
                                        </div>
                                    </div>
                                @else
                                    <p class="text-sm text-gray-500">Not assigned to any user</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 border-t border-gray-200 pt-6">
                        <div class="flex items-center justify-between">
                            <h4 class="text-sm font-medium text-gray-900">Movement History</h4>
                            <a href="#" class="text-xs text-blue-600 hover:text-blue-500">View all movements</a>
                        </div>
                        <div class="mt-4 flow-root">
                            <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                                <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                                    <table class="min-w-full divide-y divide-gray-300">
                                        <thead>
                                            <tr>
                                                <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-xs font-semibold text-gray-900 sm:pl-0">Date</th>
                                                <th scope="col" class="px-3 py-3.5 text-left text-xs font-semibold text-gray-900">From</th>
                                                <th scope="col" class="px-3 py-3.5 text-left text-xs font-semibold text-gray-900">To</th>
                                                <th scope="col" class="px-3 py-3.5 text-left text-xs font-semibold text-gray-900">By</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200">
                                            @forelse($selectedAsset->movements as $movement)
                                                <tr>
                                                    <td class="whitespace-nowrap py-4 pl-4 pr-3 text-xs text-gray-900 sm:pl-0">
                                                        {{ $movement->movement_date->format('M j, Y') }}
                                                    </td>
                                                    <td class="whitespace-nowrap px-3 py-4 text-xs text-gray-500">
                                                        {{ $movement->fromLocation->name ?? 'N/A' }}
                                                    </td>
                                                    <td class="whitespace-nowrap px-3 py-4 text-xs text-gray-500">
                                                        {{ $movement->toLocation->name ?? 'N/A' }}
                                                    </td>
                                                    <td class="whitespace-nowrap px-3 py-4 text-xs text-gray-500">
                                                        {{ $movement->assignedByUser->name ?? 'N/A' }}
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="4" class="py-4 text-center text-xs text-gray-500">
                                                        No movement history found.
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

            <div class="px-4 py-3 bg-gray-50 sm:px-6 sm:flex sm:flex-row-reverse">
                <button wire:click="openTransferModal({{ $selectedAsset->id }})" type="button" class="inline-flex justify-center w-full px-4 py-2 text-base font-medium text-white bg-green-600 border border-transparent rounded-md shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm">
                    <i class="fas fa-exchange-alt mr-2"></i> Record Transfer
                </button>
                <button wire:click="$set('showAssetDetailModal', false)" type="button" class="mt-3 inline-flex justify-center w-full px-4 py-2 text-base font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                    Close
                </button>
            </div>
            @endif
        </div>
    </div>
</div>
