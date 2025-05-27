<div
    x-data="{ open: false }"
    x-show="open"
    @open-modal.window="if ($event.detail === 'assign-modal') { open = true }"
    @close-modal.window="if ($event.detail === 'assign-modal') { open = false }"
    @keydown.escape.window="open = false"
    style="display: none;"
    class="fixed inset-0 overflow-y-auto px-4 py-6 sm:px-0 z-50"
>
    <!-- Modal backdrop -->
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

    <!-- Modal panel -->
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
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-medium text-gray-900">
                    @if($bulkAction === 'assign-location')
                        Assign Assets to Location
                    @elseif($bulkAction === 'assign-user')
                        Assign Assets to Employee
                    @elseif($bulkAction === 'transfer')
                        Transfer Assets
                    @endif
                </h2>
                <button @click="open = false" class="text-gray-400 hover:text-gray-600">
                    <span class="material-symbols-sharp">close</span>
                </button>
            </div>
            <!-- Selected Assets Info -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                <div class="flex items-center">
                    <span class="material-symbols-sharp text-blue-600 mr-2">info</span>
                    <p class="text-sm text-blue-800">
                        <span class="font-semibold">{{ count($selectedAssets) }}</span> assets selected for
                        @if($bulkAction === 'assign-location')
                            location assignment
                        @elseif($bulkAction === 'assign-user')
                            employee assignment
                        @elseif($bulkAction === 'transfer')
                            transfer
                        @endif
                    </p>
                </div>
            </div>
            {{-- <p class="mt-2 text-sm text-gray-500">
                You've selected {{ count($selectedAssets) }} assets
            </p> --}}

            <form wire:submit.prevent="assignAssets">
                <div class="space-y-4">
                    <!-- Location Selection -->
                    @if($bulkAction === 'assign-location' || $bulkAction === 'transfer')
                        <div>
                            <label for="assignLocation" class="block text-sm font-medium text-gray-700 mb-2">
                                <span class="flex items-center">
                                    <span class="material-symbols-sharp text-sm mr-2">location_on</span>
                                    @if($bulkAction === 'transfer')
                                        Transfer to Location
                                    @else
                                        Assign to Location
                                    @endif
                                </span>
                            </label>
                            <select
                                id="assignLocation"
                                wire:model="assignLocation"
                                class="block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                            >
                                <option value="">Select a location...</option>
                                @foreach($locationsList as $location)
                                    <option value="{{ $location->id }}">{{ $location->name }}</option>
                                @endforeach
                            </select>
                            @error('assignLocation')
                                <span class="text-red-500 text-xs mt-1 flex items-center">
                                    <span class="material-symbols-sharp text-xs mr-1">error</span>
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>
                    @endif

                    <!-- Employee Selection - Only Master List Users -->
                    @if($bulkAction === 'assign-user' || $bulkAction === 'transfer')
                        <div>
                            <label for="assignToUser" class="block text-sm font-medium text-gray-700 mb-2">
                                <span class="flex items-center">
                                    <span class="material-symbols-sharp text-sm mr-2">badge</span>
                                    @if($bulkAction === 'transfer')
                                        Transfer to Employee
                                    @else
                                        Assign to Employee
                                    @endif
                                </span>
                            </label>
                            <select
                                id="assignToUser"
                                wire:model="assignToUser"
                                class="block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                            >
                                <option value="">Select an employee...</option>
                                @foreach($masterListUsersList as $employee)
                                    <option value="{{ $employee->id }}">
                                        {{ $employee->employee_number }} - {{ $employee->full_name }}
                                        @if($employee->department)
                                            ({{ $employee->department }})
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('assignToUser')
                                <span class="text-red-500 text-xs mt-1 flex items-center">
                                    <span class="material-symbols-sharp text-xs mr-1">error</span>
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>
                    @endif

                    <!-- Movement Notes -->
                    <div>
                        <label for="movementNotes" class="block text-sm font-medium text-gray-700 mb-2">
                            <span class="flex items-center">
                                <span class="material-symbols-sharp text-sm mr-2">note</span>
                                Notes (Optional)
                            </span>
                        </label>
                        <textarea
                            id="movementNotes"
                            wire:model="movementNotes"
                            rows="3"
                            class="block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                            placeholder="Add any notes about this assignment or transfer..."
                        ></textarea>
                    </div>
                </div>

                <div class="flex justify-end space-x-3 mt-6">
                    <button
                        type="button"
                        @click="open = false"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors"
                    >
                        Cancel
                    </button>
                    <button
                        type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors"
                    >
                        <span class="flex items-center">
                            @if($bulkAction === 'assign-location')
                                <span class="material-symbols-sharp text-sm mr-2">location_on</span>
                                Assign to Location
                            @elseif($bulkAction === 'assign-user')
                                <span class="material-symbols-sharp text-sm mr-2">person_add</span>
                                Assign to Employee
                            @elseif($bulkAction === 'transfer')
                                <span class="material-symbols-sharp text-sm mr-2">transfer_within_a_station</span>
                                Complete Transfer
                            @endif
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
