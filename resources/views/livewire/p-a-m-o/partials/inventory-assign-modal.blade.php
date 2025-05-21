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
            <h2 class="text-lg font-medium text-gray-900">
                @if($bulkAction === 'assign-location')
                    Assign to Location
                @elseif($bulkAction === 'assign-user')
                    Assign to User
                @elseif($bulkAction === 'transfer')
                    Transfer Assets
                @endif
            </h2>

            <p class="mt-2 text-sm text-gray-500">
                You've selected {{ count($selectedAssets) }} assets
            </p>

            <form wire:submit.prevent="assignAssets" class="mt-6">
                @if($bulkAction === 'assign-location' || $bulkAction === 'transfer')
                    <div class="mb-4">
                        <label for="assignLocation" class="block text-sm font-medium text-gray-700">Location</label>
                        <select
                            id="assignLocation"
                            wire:model="assignLocation"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                        >
                            <option value="">Select a location</option>
                            @foreach($locations as $location)
                                <option value="{{ $location->id }}">{{ $location->name }}</option>
                            @endforeach
                        </select>
                        @error('assignLocation') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                @endif

                @if($bulkAction === 'assign-user' || $bulkAction === 'transfer')
                    <div class="mb-4">
                        <label for="assignToUser" class="block text-sm font-medium text-gray-700">User</label>
                        <select
                            id="assignToUser"
                            wire:model="assignToUser"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                        >
                            <option value="">Select a user</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                        @error('assignToUser') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                @endif

                <div class="mb-4">
                    <label for="movementNotes" class="block text-sm font-medium text-gray-700">Notes</label>
                    <textarea
                        id="movementNotes"
                        wire:model="movementNotes"
                        rows="3"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                        placeholder="Enter any additional notes here..."
                    ></textarea>
                </div>

                <div class="mt-6 flex justify-end space-x-3">
                    <button
                        type="button"
                        @click="open = false"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    >
                        Cancel
                    </button>
                    <button
                        type="submit"
                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    >
                        Confirm
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
