<div x-show="$wire.showTransferModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;" x-cloak>
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" x-show="$wire.showTransferModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen"></span>&#8203;

        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full" x-show="$wire.showTransferModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
            <div class="absolute top-0 right-0 pt-4 pr-4">
                <button @click="$wire.showTransferModal = false" type="button" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                    <span class="sr-only">Close</span>
                    <span class="material-symbols-sharp">close</span>
                </button>
            </div>

            <div class="px-4 pt-5 pb-4 bg-white sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="flex items-center justify-center flex-shrink-0 w-12 h-12 mx-auto bg-blue-100 rounded-full sm:mx-0 sm:h-10 sm:w-10">
                        <i class="fas fa-exchange-alt text-blue-600"></i>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg font-medium leading-6 text-gray-900">
                            Record Asset Transfer
                        </h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">
                                Complete the form below to record a transfer for the selected asset.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="mt-5">
                    <form wire:submit.prevent="recordTransfer">
                        @if($selectedAsset)
                        <div class="mb-4 p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10 bg-gray-200 rounded-full flex items-center justify-center text-gray-500">
                                    <i class="fas fa-laptop"></i>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $selectedAsset->brand }} {{ $selectedAsset->model }}</div>
                                    <div class="text-xs text-gray-500">SN: {{ $selectedAsset->serial_number }}</div>
                                    <div class="text-xs text-gray-500">Tag: {{ $selectedAsset->property_tag_number }}</div>
                                    @if($selectedAsset->assignedEmployee)
                                        <div class="text-xs text-blue-600 mt-1">
                                            Currently assigned to: {{ $selectedAsset->assignedEmployee->employee_number }} - {{ $selectedAsset->assignedEmployee->full_name }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @else
                        <div class="mb-4">
                            <label for="asset" class="block text-sm font-medium text-gray-700">Select Asset</label>
                            <select wire:model="selectedAssetId" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                                <option value="">Select an asset</option>
                                @foreach(App\Models\PAMO\PamoAssets::all() as $asset)
                                    <option value="{{ $asset->id }}">{{ $asset->brand }} {{ $asset->model }} ({{ $asset->property_tag_number }})</option>
                                @endforeach
                            </select>
                            @error('selectedAsset') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        @endif

                        <!-- From Location -->
                        <div class="mb-4">
                            <label for="fromLocation" class="block text-sm font-medium text-gray-700">Current Location</label>

                            @if($selectedAsset && $selectedAsset->location_id)
                                <select id="fromLocation" wire:model="fromLocationId" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm" disabled>
                                    @foreach($locations as $location)
                                        <option value="{{ $location->id }}">{{ $location->name }}</option>
                                    @endforeach
                                </select>
                            @else
                                <div class="mt-1 block w-full p-2 bg-gray-100 border border-gray-300 rounded-md text-sm text-gray-500">
                                    No assigned location
                                </div>
                                <input type="hidden" wire:model="fromLocationId" value="">
                            @endif
                        </div>

                        <!-- To Location -->
                        <div class="mb-4">
                            <label for="toLocation" class="block text-sm font-medium text-gray-700">New Location</label>
                            <select id="toLocation" wire:model="toLocationId" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                                <option value="">Select Location</option>
                                @foreach($locations as $location)
                                    <option value="{{ $location->id }}">{{ $location->name }}</option>
                                @endforeach
                            </select>
                            @error('toLocationId') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <!-- Assigned To Employee - Updated to use Master List -->
                        <div class="mb-4">
                            <label for="assignedTo" class="block text-sm font-medium text-gray-700">
                                <span class="flex items-center">
                                    <span class="material-symbols-sharp text-sm mr-2">badge</span>
                                    Assign To Employee (Optional)
                                </span>
                            </label>
                            <select id="assignedTo" wire:model="assignedToEmployeeId" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                                <option value="">None (Store in Location)</option>
                                @if(isset($employees) && $employees->count() > 0)
                                    @foreach($employees as $employee)
                                        <option value="{{ $employee->id }}">
                                            {{ $employee->employee_number }} - {{ $employee->full_name }}
                                        </option>
                                    @endforeach
                                @else
                                    {{-- Fallback to get employees from master list directly --}}
                                    @foreach(\App\Models\PAMO\MasterList::where('status', 'active')->orderBy('full_name')->get() as $employee)
                                        <option value="{{ $employee->id }}">
                                            {{ $employee->employee_number }} - {{ $employee->full_name }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                            @error('assignedToEmployeeId') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <!-- Movement Date -->
                        <div class="mb-4">
                            <label for="movementDate" class="block text-sm font-medium text-gray-700">Transfer Date</label>
                            <input type="date" id="movementDate" wire:model="movementDate" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                            @error('movementDate') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <!-- Notes -->
                        <div class="mb-4">
                            <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                            <textarea id="notes" wire:model="movementNotes" rows="3" placeholder="Add any notes about this transfer..." class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm"></textarea>
                        </div>

                        <!-- Transfer Summary -->
                        @if($selectedAsset && $toLocationId)
                            <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                                <div class="flex items-center">
                                    <span class="material-symbols-sharp text-blue-600 mr-2">info</span>
                                    <div class="text-sm text-blue-800">
                                        <strong>Transfer Summary:</strong>
                                        <div class="mt-1">
                                            Moving {{ $selectedAsset->brand }} {{ $selectedAsset->model }}
                                            from {{ $selectedAsset->location->name ?? 'No Location' }}
                                            to {{ $locations->where('id', $toLocationId)->first()->name ?? 'Selected Location' }}
                                            @if($assignedToEmployeeId)
                                                and assigning to employee
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </form>
                </div>
            </div>

            <div class="px-4 py-3 bg-gray-50 sm:px-6 sm:flex sm:flex-row-reverse">
                <button wire:click="recordTransfer" type="button" class="inline-flex justify-center w-full px-4 py-2 text-base font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                    <span class="flex items-center">
                        <span class="material-symbols-sharp text-sm mr-2">transfer_within_a_station</span>
                        Record Transfer
                    </span>
                </button>
                <button wire:click="$set('showTransferModal', false)" type="button" class="inline-flex justify-center w-full px-4 py-2 mt-3 text-base font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>
