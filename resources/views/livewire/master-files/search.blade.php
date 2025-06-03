<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Search Archive</h1>
            <p class="mt-2 text-sm text-gray-600">Find documents in the Master File Archive</p>
        </div>
        <div class="mt-4 sm:mt-0 flex space-x-3">
            <a href="{{ route('master-file.dashboard') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                <span class="material-symbols-sharp text-sm mr-2">arrow_back</span>
                Back to Dashboard
            </a>
            <a href="{{ route('master-file.upload') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white hover:bg-blue-700 transition-colors">
                <span class="material-symbols-sharp text-sm mr-2">upload_file</span>
                Upload Document
            </a>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <span class="material-symbols-sharp text-blue-600 mr-2">filter_list</span>
                Search & Filters
            </h3>
        </div>

        <div class="p-6 space-y-4">
            <!-- Main Search -->
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <span class="material-symbols-sharp text-gray-400">search</span>
                </div>
                <input type="text"
                       wire:model.live.debounce.300ms="search"
                       class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-lg"
                       placeholder="Search by title, description, document code, or tags...">
            </div>

            <!-- Advanced Filters -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Category Filter -->
                <div>
                    <label for="category_filter" class="block text-sm font-medium text-gray-700 mb-2">
                        Category
                    </label>
                    <select id="category_filter"
                            wire:model.live="category_filter"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Department Filter -->
                <div>
                    <label for="department_filter" class="block text-sm font-medium text-gray-700 mb-2">
                        Department
                    </label>
                    <select id="department_filter"
                            wire:model.live="department_filter"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Departments</option>
                        @foreach($departments as $department)
                            <option value="{{ $department }}">{{ $department }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Date From -->
                <div>
                    <label for="date_from" class="block text-sm font-medium text-gray-700 mb-2">
                        Date From
                    </label>
                    <input type="date"
                           id="date_from"
                           wire:model.live="date_from"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>

                <!-- Date To -->
                <div>
                    <label for="date_to" class="block text-sm font-medium text-gray-700 mb-2">
                        Date To
                    </label>
                    <input type="date"
                           id="date_to"
                           wire:model.live="date_to"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>

            <!-- Filter Actions -->
            <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                <div class="flex items-center space-x-4 text-sm text-gray-600">
                    <span>{{ $files->total() }} files found</span>
                    @if($search || $category_filter || $department_filter || $date_from || $date_to)
                        <span class="text-blue-600">• Filters applied</span>
                    @endif
                </div>
                @if($search || $category_filter || $department_filter || $date_from || $date_to)
                <button wire:click="resetFilters"
                        class="inline-flex items-center px-3 py-1.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                    <span class="material-symbols-sharp text-sm mr-1">clear</span>
                    Clear Filters
                </button>
                @endif
            </div>
        </div>
    </div>

    <!-- Quick Categories -->
    @if(!$search && !$category_filter && !$department_filter && !$date_from && !$date_to)
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <span class="material-symbols-sharp text-purple-600 mr-2">category</span>
                Browse by Category
            </h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-4">
                @foreach($categories as $category)
                <button wire:click="$set('category_filter', '{{ $category->id }}')"
                        class="flex flex-col items-center p-4 rounded-lg border border-gray-200 hover:border-blue-300 hover:bg-blue-50 transition-colors group">
                    <div class="w-12 h-12 rounded-lg flex items-center justify-center mb-2 group-hover:scale-110 transition-transform"
                         style="background-color: {{ $category->color }}20;">
                        <span class="material-symbols-sharp text-xl" style="color: {{ $category->color }};">{{ $category->icon }}</span>
                    </div>
                    <span class="text-sm font-medium text-gray-900 text-center">{{ $category->name }}</span>
                    <span class="text-xs text-gray-500">{{ $category->files_count ?? 0 }} files</span>
                </button>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- Search Results -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <span class="material-symbols-sharp text-green-600 mr-2">folder_open</span>
                    Search Results
                    @if($files->total() > 0)
                        <span class="ml-2 text-sm font-normal text-gray-500">({{ $files->total() }} files)</span>
                    @endif
                </h3>
                <div class="flex items-center space-x-2">
                    <span class="text-sm text-gray-500">View:</span>
                    <button class="p-2 rounded-lg bg-blue-100 text-blue-600">
                        <span class="material-symbols-sharp text-sm">grid_view</span>
                    </button>
                    <button class="p-2 rounded-lg text-gray-400 hover:bg-gray-100">
                        <span class="material-symbols-sharp text-sm">list</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Files Grid -->
        @if($files->count() > 0)
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($files as $file)
                <div class="group bg-white border border-gray-200 rounded-lg overflow-hidden hover:shadow-md hover:border-blue-300 transition-all duration-200">
                    <!-- File Header -->
                    <div class="p-4 border-b border-gray-100" style="background: linear-gradient(135deg, {{ $file->category->color }}15 0%, {{ $file->category->color }}05 100%);">
                        <div class="flex items-start justify-between">
                            <div class="flex items-center space-x-2">
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background-color: {{ $file->category->color }}20;">
                                    <span class="material-symbols-sharp text-sm" style="color: {{ $file->category->color }};">{{ $file->category->icon }}</span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-medium text-gray-600 truncate">{{ $file->category->name }}</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-1">
                                @if($file->is_confidential)
                                <span class="material-symbols-sharp text-red-600 text-sm" title="Confidential">lock</span>
                                @endif
                                @if($file->expiry_date && $file->expiry_date->diffInDays() < 30)
                                <span class="material-symbols-sharp text-yellow-600 text-sm" title="Expiring Soon">warning</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- File Content -->
                    <div class="p-4 space-y-3">
                        <!-- File Icon and Info -->
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0">
                                @if(Str::contains($file->mime_type, 'pdf'))
                                    <span class="material-symbols-sharp text-2xl text-red-500">picture_as_pdf</span>
                                @elseif(Str::contains($file->mime_type, 'word') || Str::endsWith($file->original_filename, ['.doc', '.docx']))
                                    <span class="material-symbols-sharp text-2xl text-blue-500">description</span>
                                @else
                                    <span class="material-symbols-sharp text-2xl text-gray-400">insert_drive_file</span>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="text-sm font-semibold text-gray-900 line-clamp-2 group-hover:text-blue-600 transition-colors">
                                    {{ $file->title }}
                                </h4>
                                @if($file->document_code)
                                <p class="text-xs text-blue-600 font-medium mt-1">{{ $file->document_code }}</p>
                                @endif
                                @if($file->description)
                                <p class="text-xs text-gray-500 mt-1 line-clamp-2">{{ $file->description }}</p>
                                @endif
                            </div>
                        </div>

                        <!-- File Meta -->
                        <div class="space-y-2 text-xs text-gray-500">
                            <div class="flex items-center justify-between">
                                <span>{{ $file->formatted_file_size }}</span>
                                <span>v{{ $file->version }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span>{{ $file->department }}</span>
                                <span>{{ $file->created_at->format('M d, Y') }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span>{{ $file->uploader->name }}</span>
                                <div class="flex items-center space-x-2">
                                    <span>{{ $file->download_count }} downloads</span>
                                </div>
                            </div>
                        </div>

                        <!-- Tags -->
                        @if($file->tags && count($file->tags) > 0)
                        <div class="flex flex-wrap gap-1">
                            @foreach(array_slice($file->tags, 0, 3) as $tag)
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-700">
                                {{ $tag }}
                            </span>
                            @endforeach
                            @if(count($file->tags) > 3)
                            <span class="text-xs text-gray-500">+{{ count($file->tags) - 3 }} more</span>
                            @endif
                        </div>
                        @endif
                    </div>

                    <!-- File Actions -->
                    <div class="px-4 py-3 bg-gray-50 border-t border-gray-100">
                        <div class="flex items-center justify-between">
                            <a href="{{ route('master-file.show', $file) }}"
                               class="inline-flex items-center text-sm font-medium text-blue-600 hover:text-blue-800 transition-colors">
                                <span class="material-symbols-sharp text-sm mr-1">visibility</span>
                                View
                            </a>
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('master-file.download', $file) }}"
                                   class="inline-flex items-center p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded transition-colors">
                                    <span class="material-symbols-sharp text-sm">download</span>
                                </a>
                                <button class="inline-flex items-center p-1.5 text-gray-400 hover:text-green-600 hover:bg-green-50 rounded transition-colors">
                                    <span class="material-symbols-sharp text-sm">share</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Pagination -->
        @if($files->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $files->links() }}
        </div>
        @endif

        @else
        <!-- Empty State -->
        <div class="p-12 text-center">
            <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                <span class="material-symbols-sharp text-4xl text-gray-400">search_off</span>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No files found</h3>
            <p class="text-gray-500 mb-6">
                @if($search || $category_filter || $department_filter || $date_from || $date_to)
                    No files match your search criteria. Try adjusting your filters.
                @else
                    No files are available in the archive yet.
                @endif
            </p>
            <div class="flex flex-col sm:flex-row gap-3 justify-center">
                @if($search || $category_filter || $department_filter || $date_from || $date_to)
                <button wire:click="resetFilters"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                    <span class="material-symbols-sharp text-sm mr-2">clear</span>
                    Clear Filters
                </button>
                @endif
                <a href="{{ route('master-file.upload') }}"
                   class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white hover:bg-blue-700 transition-colors">
                    <span class="material-symbols-sharp text-sm mr-2">upload_file</span>
                    Upload Document
                </a>
            </div>
        </div>
        @endif
    </div>
</div>

@push('styles')
<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
@endpush
