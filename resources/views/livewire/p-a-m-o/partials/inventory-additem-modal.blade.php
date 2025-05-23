<!-- Add Item Modal -->
<div
    x-data="{ open: false, activeTab: 'asset' }"
     @open-modal.window="if ($event.detail === 'add-item-modal') { open = true; activeTab = 'asset'; }"
    @close-modal.window="if ($event.detail === 'add-item-modal') { open = false; }"
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
                                    <option value="Working">Working</option>
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
