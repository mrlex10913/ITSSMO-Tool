<!-- Update Status Modal -->
<div
    x-data="{ open: false }"
    @open-modal.window="if ($event.detail === 'update-status-modal') { open = true; }"
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
        class="mb-6 bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:w-full sm:max-w-lg sm:mx-auto"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
    >
        <div class="p-6">
            <h3 class="text-lg font-medium text-gray-900">Update Asset Status</h3>
            <p class="mt-2 text-sm text-gray-500">
                Select a new status for {{ count($selectedAssets) }} selected asset(s)
            </p>

            <div class="mt-4 space-y-4">
                <div>
                    <button
                        wire:click="updateBulkStatus('available')"
                        class="w-full inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">
                        Available
                    </button>
                </div>
                <div>
                    <button
                        wire:click="updateBulkStatus('in-use')"
                        class="w-full inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                        In Use
                    </button>
                </div>
                <div>
                    <button
                        wire:click="updateBulkStatus('maintenance')"
                        class="w-full inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700">
                        Under Repair
                    </button>
                </div>
                <div>
                    <button
                        wire:click="updateBulkStatus('disposed')"
                        class="w-full inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700">
                        Disposed
                    </button>
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <button
                    type="button"
                    @click="open = false"
                    class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>
