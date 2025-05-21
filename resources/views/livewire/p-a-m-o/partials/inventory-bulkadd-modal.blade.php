<div
    x-data="{ open: false, activeTab: 'manual' }"
    @open-modal.window="if ($event.detail === 'bulk-add-modal') { open = true; activeTab = 'manual'; }"
    @close-modal.window="if ($event.detail === 'bulk-add-modal') { open = false; }"
    @keydown.escape.window="open = false"
    x-show="open"
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
        class="mb-6 bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:w-full sm:max-w-5xl sm:mx-auto"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
    >
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900">
                Bulk Add Items
            </h2>

            <div class="mt-4">
                <!-- Tabs -->
                <div class="border-b border-gray-200">
                    <nav class="-mb-px flex space-x-8">
                        <button
                            @click="activeTab = 'manual'"
                            :class="{'border-blue-500 text-blue-600': activeTab === 'manual', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'manual'}"
                            class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                            Manual Entry
                        </button>
                        <button
                            @click="activeTab = 'csv'"
                            :class="{'border-blue-500 text-blue-600': activeTab === 'csv', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'csv'}"
                            class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                            CSV Upload
                        </button>
                    </nav>
                </div>

                <!-- Manual Entry Form -->
                <div x-show="activeTab === 'manual'" class="mt-4">
                    <div class="mb-4">
                        <div class="flex justify-between items-center mb-2">
                            <div class="flex space-x-2">
                                <select
                                    wire:model.live="bulkItemsDefaults.major_category_id"
                                    class="block w-auto rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                >
                                    <option value="">Select Major Category</option>
                                    @foreach($majorCategories as $major)
                                        <option value="{{ $major->id }}">{{ $major->name }}</option>
                                    @endforeach
                                </select>

                                <select
                                    wire:model="bulkItemsDefaults.category_id"
                                    class="block w-auto rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                >
                                    <option value="">Select Minor Category</option>
                                    @foreach($minorCategories as $minor)
                                        <option value="{{ $minor->id }}">{{ $minor->name }}</option>
                                    @endforeach
                                </select>

                                <select
                                    wire:model="bulkItemsDefaults.status"
                                    class="block w-auto rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                >
                                    <option value="available">Available</option>
                                    <option value="in-use">In Use</option>
                                    <option value="maintenance">Under Repair</option>
                                    <option value="disposed">Disposed</option>
                                </select>
                            </div>
                            <button
                                wire:click="addBulkItem"
                                type="button"
                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                                </svg>
                                Add Row
                            </button>
                        </div>
                        <p class="text-xs text-gray-500">Set common values for all items. You can override per item below.</p>
                    </div>

                    <div class="overflow-x-auto bg-gray-50 p-3 rounded-md">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                                    <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tag Number*</th>
                                    <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">PO Number</th>
                                    <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Brand</th>
                                    <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Model</th>
                                    <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Serial</th>
                                    <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category*</th>
                                    <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                    <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($bulkItems as $index => $item)
                                <tr>
                                    <td class="px-2 py-2 whitespace-nowrap">{{ $index + 1 }}</td>
                                    <td class="px-2 py-2 whitespace-nowrap">
                                        <input type="text" wire:model="bulkItems.{{ $index }}.property_tag_number" class="block w-28 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                        @error('bulkItems.'.$index.'.property_tag_number') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                                    </td>
                                    <td class="px-2 py-2 whitespace-nowrap">
                                        <input type="text" wire:model="bulkItems.{{ $index }}.po_number" class="block w-28 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    </td>
                                    <td class="px-2 py-2 whitespace-nowrap">
                                        <input type="text" wire:model="bulkItems.{{ $index }}.brand" class="block w-28 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    </td>
                                    <td class="px-2 py-2 whitespace-nowrap">
                                        <input type="text" wire:model="bulkItems.{{ $index }}.model" class="block w-28 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    </td>
                                    <td class="px-2 py-2 whitespace-nowrap">
                                        <input type="text" wire:model="bulkItems.{{ $index }}.serial_number" class="block w-28 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    </td>
                                    <td class="px-2 py-2 whitespace-nowrap">
                                        <select wire:model="bulkItems.{{ $index }}.category_id" class="block w-36 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                            <option value="">Select Category</option>
                                            @foreach($minorCategories as $category)
                                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('bulkItems.'.$index.'.category_id') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                                    </td>
                                    <!-- New description field -->
                                    <td class="px-2 py-2">
                                        <input type="text"
                                            wire:model="bulkItems.{{ $index }}.description"
                                            class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                            placeholder="Item description">
                                    </td>
                                    <td class="px-2 py-2 whitespace-nowrap">
                                        <button wire:click="removeBulkItem({{ $index }})" type="button" class="text-red-600 hover:text-red-900">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach

                                @if(empty($bulkItems))
                                <tr>
                                    <td colspan="9" class="px-2 py-4 text-center text-sm text-gray-500">
                                        No items added yet. Click "Add Row" to begin.
                                    </td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- CSV Upload Form -->
                <div x-show="activeTab === 'csv'" class="mt-4">
                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-yellow-700">
                                    Upload CSV with these headers: property_tag_number, po_number, brand, model, serial_number, status, description<br>
                                    <strong>Note:</strong> Category can be assigned manually below for all imported items.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Manual Category Assignment -->
                    <div class="mb-6 p-4 bg-gray-50 rounded-md">
                        <h3 class="text-sm font-medium text-gray-700 mb-3">Assign Categories to All Items</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="csv-major-category" class="block text-xs font-medium text-gray-700 mb-1">Major Category</label>
                                <select
                                    wire:model.live="csvCategoryDefaults.major_category_id"
                                    id="csv-major-category"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                >
                                    <option value="">Select Major Category</option>
                                    @foreach($majorCategories as $major)
                                        <option value="{{ $major->id }}">{{ $major->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="csv-minor-category" class="block text-xs font-medium text-gray-700 mb-1">Minor Category</label>
                                <select
                                    wire:model="csvCategoryDefaults.category_id"
                                    id="csv-minor-category"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                >
                                    <option value="">Select Minor Category</option>
                                    @foreach($minorCategories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                                @error('csvCategoryDefaults.category_id') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="mt-2 text-xs text-gray-500 italic">
                            Note: For individual descriptions, include a "description" column in your CSV. Each row's description will be preserved during import.
                        </div>
                    </div>

                    <div
                        x-data="{
                            isUploading: false,
                            progress: 0,
                            uploadFile() {
                                this.isUploading = true;
                                $wire.upload('csvFile', this.$refs.csv.files[0], () => {
                                    this.progress = 0;
                                    this.isUploading = false;
                                }, () => {}, (event) => {
                                    this.progress = event.detail.progress;
                                });
                            }
                        }"
                        class="flex items-center justify-center w-full"
                    >
                        <label class="flex flex-col w-full h-32 border-2 border-dashed border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                            <div class="flex flex-col items-center justify-center pt-7">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-gray-400 group-hover:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                </svg>
                                <p class="pt-1 text-sm tracking-wider text-gray-400 group-hover:text-gray-600">
                                    <span x-show="!isUploading">Attach CSV file</span>
                                    <span x-show="isUploading">Uploading... <span x-text="progress"></span>%</span>
                                </p>
                            </div>
                            <input type="file" x-ref="csv" class="opacity-0" wire:model="csvFile" accept=".csv" @change="uploadFile()" />
                        </label>
                    </div>

                    <div class="mt-4">
                        <button
                            wire:click="processCSV"
                            wire:loading.attr="disabled"
                            class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50"
                            :disabled="$wire.csvFile == null"
                        >
                            <svg wire:loading wire:target="processCSV" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Process CSV
                        </button>

                        @if($csvPreviewData)
                        <div class="mt-4 p-4 bg-gray-50 rounded-md overflow-auto max-h-72">
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Preview ({{ count($csvPreviewData) }} items)</h3>
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        @foreach(array_keys($csvPreviewData[0]) as $header)
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                {{ $header }}
                                                @if($header === 'description')
                                                    <span class="text-xs font-normal normal-case italic">(truncated in preview)</span>
                                                @endif
                                            </th>
                                        @endforeach
                                        <!-- Add category preview column -->
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-green-50">CATEGORY (ASSIGNED)</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach(array_slice($csvPreviewData, 0, 5) as $row)
                                        <tr>
                                            @foreach($row as $key => $cell)
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    @if($key === 'description' && strlen($cell) > 50)
                                                        {{ substr($cell, 0, 50) }}...
                                                    @else
                                                        {{ $cell }}
                                                    @endif
                                                </td>
                                            @endforeach
                                            <!-- Display the selected category -->
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-green-600 bg-green-50">
                                                @if(!empty($csvCategoryDefaults['category_id']))
                                                    {{ $selectedCategoryName }}
                                                @else
                                                    <span class="text-orange-500">No category selected</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            @if(count($csvPreviewData) > 5)
                                <p class="mt-2 text-sm text-gray-500">Showing 5 of {{ count($csvPreviewData) }} items...</p>
                            @endif
                        </div>
                        @endif
                    </div>
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
                        wire:click="saveBulkItems"
                        type="button"
                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    >
                        Save All Items
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
