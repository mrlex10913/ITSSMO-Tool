<!-- filepath: c:\inetpub\wwwroot\ITSSMO-Tool\resources\views\livewire\master-files\upload.blade.php -->
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Upload Document</h1>
            <p class="mt-2 text-sm text-gray-600">Add a new document to the Master File Archive</p>
        </div>
        <div class="mt-4 sm:mt-0">
            <a href="{{ route('master-file.dashboard') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                <span class="material-symbols-sharp text-sm mr-2">arrow_back</span>
                Back to Dashboard
            </a>
        </div>
    </div>

    <!-- Upload Form -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <form wire:submit="uploadDocument" class="p-6 space-y-6">
            <!-- File Upload Section -->
            <div class="space-y-4">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <span class="material-symbols-sharp text-blue-600 mr-2">upload_file</span>
                    File Upload
                </h3>

                <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-blue-400 transition-colors"
                     x-data="{
                        dragover: false,
                        handleDrop(e) {
                            this.dragover = false;
                            const files = e.dataTransfer.files;
                            if (files.length > 0) {
                                // Directly set the file property
                                @this.set('file', files[0]);
                            }
                        }
                     }"
                     @dragover.prevent="dragover = true"
                     @dragleave.prevent="dragover = false"
                     @drop.prevent="handleDrop($event)"
                     :class="{ 'border-blue-400 bg-blue-50': dragover }">

                    @if ($file)
                        <div class="flex items-center justify-center space-x-3">
                            <span class="material-symbols-sharp text-green-600 text-3xl">check_circle</span>
                            <div class="text-left">
                                <p class="text-sm font-medium text-gray-900">{{ $file->getClientOriginalName() }}</p>
                                <p class="text-xs text-gray-500">{{ number_format($file->getSize() / 1024, 2) }} KB</p>
                            </div>
                            <button type="button" wire:click="removeFile" class="text-red-600 hover:text-red-800">
                                <span class="material-symbols-sharp">close</span>
                            </button>
                        </div>
                    @else
                        <div class="space-y-2">
                            <span class="material-symbols-sharp text-gray-400 text-4xl">cloud_upload</span>
                            <div>
                                <p class="text-gray-600">
                                    <label for="file-upload" class="font-medium text-blue-600 hover:text-blue-500 cursor-pointer">
                                        Click to upload
                                    </label>
                                    or drag and drop
                                </p>
                                <p class="text-xs text-gray-500">PDF, DOC, DOCX up to 10MB</p>
                            </div>
                        </div>
                        <input id="file-upload" wire:model="file" type="file" class="sr-only" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx">
                    @endif

                    <!-- Upload Progress -->
                    <div wire:loading wire:target="file" class="mt-4">
                        <div class="flex items-center justify-center space-x-2">
                            <svg class="animate-spin h-5 w-5 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span class="text-sm text-blue-600">Uploading...</span>
                        </div>
                    </div>
                </div>

                @error('file')
                    <p class="text-sm text-red-600 flex items-center">
                        <span class="material-symbols-sharp text-sm mr-1">error</span>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <!-- Document Information -->
            <div class="space-y-4">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <span class="material-symbols-sharp text-purple-600 mr-2">description</span>
                    Document Information
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Title -->
                    <div class="md:col-span-2">
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                            Document Title <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               id="title"
                               wire:model="title"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                               placeholder="Enter document title">
                        @error('title')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Category -->
                    <div>
                        <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Category <span class="text-red-500">*</span>
                        </label>
                        <select id="category_id"
                                wire:model.live="category_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select a category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror

                        <!-- Debug info -->
                        @if($categories->isEmpty())
                            <p class="text-red-600 text-sm mt-1">No categories found. Please create categories first.</p>
                        @else
                            <p class="text-green-600 text-sm mt-1">{{ $categories->count() }} categories available.</p>
                        @endif
                    </div>

                    <!-- Document Code -->
                    <div>
                        <label for="document_code" class="block text-sm font-medium text-gray-700 mb-2">
                            Document Code
                        </label>
                        <input type="text"
                               id="document_code"
                               wire:model="document_code"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                               placeholder="e.g., ITSS-MAN-2024-001">
                        @error('document_code')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div class="md:col-span-2">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                            Description
                        </label>
                        <textarea id="description"
                                  wire:model="description"
                                  rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="Brief description of the document"></textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Date Information -->
            <div class="space-y-4">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <span class="material-symbols-sharp text-green-600 mr-2">schedule</span>
                    Date Information
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Effective Date -->
                    <div>
                        <label for="effective_date" class="block text-sm font-medium text-gray-700 mb-2">
                            Effective Date
                        </label>
                        <input type="date"
                               id="effective_date"
                               wire:model="effective_date"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        @error('effective_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Review Date -->
                    <div>
                        <label for="review_date" class="block text-sm font-medium text-gray-700 mb-2">
                            Review Date
                        </label>
                        <input type="date"
                               id="review_date"
                               wire:model="review_date"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        @error('review_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Expiry Date -->
                    <div>
                        <label for="expiry_date" class="block text-sm font-medium text-gray-700 mb-2">
                            Expiry Date
                        </label>
                        <input type="date"
                               id="expiry_date"
                               wire:model="expiry_date"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        @error('expiry_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Additional Options -->
            <div class="space-y-4">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <span class="material-symbols-sharp text-orange-600 mr-2">settings</span>
                    Additional Options
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Tags -->
                    <div>
                        <label for="tags" class="block text-sm font-medium text-gray-700 mb-2">
                            Tags
                        </label>
                        <input type="text"
                               id="tags"
                               wire:model="tags"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                               placeholder="manual, policy, procedure (comma separated)">
                        <p class="mt-1 text-xs text-gray-500">Separate tags with commas</p>
                        @error('tags')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Visible to Departments -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Visible to Departments
                        </label>
                        <div class="space-y-2 max-h-32 overflow-y-auto border border-gray-300 rounded-lg p-3">
                            @php
                                $departments = ['ITSS', 'PAMO', 'BFO', 'HR', 'ACCOUNTING', 'ADMIN'];
                            @endphp
                            @foreach($departments as $dept)
                                <label class="flex items-center">
                                    <input type="checkbox"
                                           wire:model="visible_to_departments"
                                           value="{{ $dept }}"
                                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <span class="ml-2 text-sm text-gray-700">{{ $dept }}</span>
                                </label>
                            @endforeach
                        </div>
                        @error('visible_to_departments')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Confidential Checkbox -->
                <div class="flex items-center">
                    <input type="checkbox"
                           id="is_confidential"
                           wire:model="is_confidential"
                           class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                    <label for="is_confidential" class="ml-2 text-sm text-gray-700 flex items-center">
                        <span class="material-symbols-sharp text-red-600 text-sm mr-1">lock</span>
                        Mark as Confidential
                    </label>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                <button type="button"
                        onclick="window.history.back()"
                        class="px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                    Cancel
                </button>

                <button type="submit"
                        wire:loading.attr="disabled"
                        wire:target="uploadDocument"
                        class="px-6 py-2 bg-blue-600 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 transition-colors">
                    <span wire:loading.remove wire:target="uploadDocument">
                        <span class="material-symbols-sharp text-sm mr-2">upload</span>
                        Upload Document
                    </span>
                    <span wire:loading wire:target="uploadDocument" class="flex items-center">
                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Uploading...
                    </span>
                </button>
            </div>
        </form>
    </div>

    <!-- Upload Guidelines -->
    <div class="bg-blue-50 rounded-xl border border-blue-200 p-6">
        <h3 class="text-lg font-semibold text-blue-900 mb-4 flex items-center">
            <span class="material-symbols-sharp text-blue-600 mr-2">info</span>
            Upload Guidelines
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-blue-800">
            <div>
                <h4 class="font-medium mb-2">Supported File Types:</h4>
                <ul class="space-y-1">
                    <li>• PDF documents (.pdf)</li>
                    <li>• Microsoft Word (.doc, .docx)</li>
                    <li>• Excel spreadsheets (.xls, .xlsx)</li>
                    <li>• PowerPoint presentations (.ppt, .pptx)</li>
                </ul>
            </div>
            <div>
                <h4 class="font-medium mb-2">Best Practices:</h4>
                <ul class="space-y-1">
                    <li>• Use clear, descriptive titles</li>
                    <li>• Include relevant tags for searchability</li>
                    <li>• Set appropriate expiry dates</li>
                    <li>• Choose the correct category</li>
                </ul>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Auto-generate document code based on category and date
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('categoryChanged', (categoryId) => {
            // You can implement auto-generation logic here
        });
    });
</script>
@endpush
