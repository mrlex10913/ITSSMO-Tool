<!-- filepath: c:\inetpub\wwwroot\ITSSMO-Tool\resources\views\livewire\master-files\versions.blade.php -->
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Version Management</h1>
            <p class="mt-2 text-sm text-gray-600">Track and manage document versions and revision history</p>
        </div>
        <div class="mt-4 sm:mt-0 flex space-x-3">
            <a href="{{ route('master-file.dashboard') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                <span class="material-symbols-sharp text-sm mr-2">arrow_back</span>
                Back to Dashboard
            </a>
            <a href="{{ route('master-file.upload') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white hover:bg-blue-700 transition-colors">
                <span class="material-symbols-sharp text-sm mr-2">upload_file</span>
                Upload New Version
            </a>
        </div>
    </div>

    <!-- Search Documents -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <span class="material-symbols-sharp text-blue-600 mr-2">search</span>
                Search Documents
            </h3>
        </div>
        <div class="p-6">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <span class="material-symbols-sharp text-gray-400">search</span>
                </div>
                <input type="text"
                       wire:model.live.debounce.300ms="search"
                       class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                       placeholder="Search by document title or code...">
            </div>
            @if($search)
            <div class="mt-3 flex items-center justify-between">
                <span class="text-sm text-gray-600">{{ $files->total() }} documents found</span>
                <button wire:click="$set('search', '')"
                        class="inline-flex items-center text-sm text-blue-600 hover:text-blue-800">
                    <span class="material-symbols-sharp text-sm mr-1">clear</span>
                    Clear search
                </button>
            </div>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 {{ $selectedFile ? 'lg:grid-cols-2' : '' }} gap-6">
        <!-- Documents List -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <span class="material-symbols-sharp text-purple-600 mr-2">folder_open</span>
                    Documents
                    @if($files->total() > 0)
                        <span class="ml-2 text-sm font-normal text-gray-500">({{ $files->total() }})</span>
                    @endif
                </h3>
            </div>

            @if($files->count() > 0)
            <div class="divide-y divide-gray-200">
                @foreach($files as $file)
                <div class="p-6 hover:bg-gray-50 transition-colors cursor-pointer"
                     wire:click="viewVersions({{ $file->id }})"
                     class="{{ $selectedFile == $file->id ? 'bg-blue-50 border-r-4 border-blue-500' : '' }}">
                    <div class="flex items-start space-x-4">
                        <!-- File Icon & Category -->
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 rounded-lg flex items-center justify-center"
                                 style="background-color: {{ $file->category->color }}20;">
                                <span class="material-symbols-sharp text-lg" style="color: {{ $file->category->color }};">
                                    {{ $file->category->icon }}
                                </span>
                            </div>
                        </div>

                        <!-- File Info -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <h4 class="text-base font-semibold text-gray-900 truncate">
                                        {{ $file->title }}
                                    </h4>
                                    @if($file->document_code)
                                    <p class="text-sm text-blue-600 font-medium mt-1">{{ $file->document_code }}</p>
                                    @endif
                                    <p class="text-sm text-gray-500 mt-1">{{ $file->category->name }}</p>
                                </div>
                                <div class="flex-shrink-0 ml-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        v{{ $file->version }}
                                    </span>
                                </div>
                            </div>

                            <!-- Version Stats -->
                            <div class="mt-3 flex items-center space-x-4 text-sm text-gray-500">
                                <div class="flex items-center">
                                    <span class="material-symbols-sharp text-sm mr-1">history</span>
                                    {{ $file->versions->count() + 1 }} versions
                                </div>
                                <div class="flex items-center">
                                    <span class="material-symbols-sharp text-sm mr-1">person</span>
                                    {{ $file->uploader->name }}
                                </div>
                                <div class="flex items-center">
                                    <span class="material-symbols-sharp text-sm mr-1">schedule</span>
                                    {{ $file->created_at->format('M d, Y') }}
                                </div>
                            </div>

                            <!-- Status Indicators -->
                            <div class="mt-2 flex items-center space-x-2">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                    @if($file->status === 'active') bg-green-100 text-green-800
                                    @elseif($file->status === 'draft') bg-yellow-100 text-yellow-800
                                    @elseif($file->status === 'superseded') bg-gray-100 text-gray-800
                                    @elseif($file->status === 'archived') bg-red-100 text-red-800
                                    @else bg-blue-100 text-blue-800
                                    @endif">
                                    {{ ucfirst($file->status) }}
                                </span>
                                @if($file->is_confidential)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    <span class="material-symbols-sharp text-xs mr-1">lock</span>
                                    Confidential
                                </span>
                                @endif
                                @if($file->expiry_date && $file->expiry_date->diffInDays() < 30)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    <span class="material-symbols-sharp text-xs mr-1">warning</span>
                                    Expiring Soon
                                </span>
                                @endif
                            </div>
                        </div>

                        <!-- Action Arrow -->
                        <div class="flex-shrink-0">
                            <span class="material-symbols-sharp text-gray-400 {{ $selectedFile == $file->id ? 'text-blue-600' : '' }}">
                                chevron_right
                            </span>
                        </div>
                    </div>
                </div>
                @endforeach
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
                <div class="mx-auto w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                    <span class="material-symbols-sharp text-2xl text-gray-400">folder</span>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No documents found</h3>
                <p class="text-gray-500 mb-6">
                    @if($search)
                        No documents match your search criteria.
                    @else
                        No documents are available for version management.
                    @endif
                </p>
                @if($search)
                <button wire:click="$set('search', '')"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    <span class="material-symbols-sharp text-sm mr-2">clear</span>
                    Clear Search
                </button>
                @endif
            </div>
            @endif
        </div>

        <!-- Version History Panel -->
        @if($selectedFile && $selectedFileVersions)
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <span class="material-symbols-sharp text-orange-600 mr-2">history</span>
                        Version History
                    </h3>
                    <button wire:click="$set('selectedFile', null)"
                            class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                        <span class="material-symbols-sharp">close</span>
                    </button>
                </div>
                <p class="text-sm text-gray-600 mt-1">
                    {{ $selectedFileVersions->first()->title }}
                </p>
            </div>

            <div class="max-h-[600px] overflow-y-auto">
                <div class="divide-y divide-gray-200">
                    @foreach($selectedFileVersions->sortByDesc('version') as $index => $version)
                    <div class="p-6 {{ $index === 0 ? 'bg-blue-50' : '' }}">
                        <div class="flex items-start space-x-4">
                            <!-- Version Icon -->
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center
                                    {{ $index === 0 ? 'bg-blue-600' : 'bg-gray-400' }}">
                                    <span class="material-symbols-sharp text-white text-sm">
                                        {{ $index === 0 ? 'star' : 'history' }}
                                    </span>
                                </div>
                            </div>

                            <!-- Version Details -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-2">
                                            <h4 class="text-base font-semibold {{ $index === 0 ? 'text-blue-900' : 'text-gray-900' }}">
                                                Version {{ $version->version }}
                                            </h4>
                                            @if($index === 0)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                Current
                                            </span>
                                            @endif
                                        </div>

                                        <div class="mt-1 flex items-center space-x-4 text-sm {{ $index === 0 ? 'text-blue-700' : 'text-gray-500' }}">
                                            <div class="flex items-center">
                                                <span class="material-symbols-sharp text-sm mr-1">person</span>
                                                {{ $version->uploader->name }}
                                            </div>
                                            <div class="flex items-center">
                                                <span class="material-symbols-sharp text-sm mr-1">schedule</span>
                                                {{ $version->created_at->format('M d, Y \a\t g:i A') }}
                                            </div>
                                            <div class="flex items-center">
                                                <span class="material-symbols-sharp text-sm mr-1">data_usage</span>
                                                {{ $version->formatted_file_size }}
                                            </div>
                                        </div>

                                        @if($version->revision_notes)
                                        <div class="mt-2 p-3 bg-gray-50 rounded-lg">
                                            <p class="text-sm text-gray-700">
                                                <strong>Revision Notes:</strong> {{ $version->revision_notes }}
                                            </p>
                                        </div>
                                        @endif

                                        <!-- Version Status -->
                                        <div class="mt-3 flex items-center space-x-2">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                                @if($version->status === 'active') bg-green-100 text-green-800
                                                @elseif($version->status === 'draft') bg-yellow-100 text-yellow-800
                                                @elseif($version->status === 'superseded') bg-gray-100 text-gray-800
                                                @elseif($version->status === 'archived') bg-red-100 text-red-800
                                                @else bg-blue-100 text-blue-800
                                                @endif">
                                                {{ ucfirst($version->status) }}
                                            </span>

                                            @if($version->effective_date)
                                            <span class="text-xs text-gray-500">
                                                Effective: {{ $version->effective_date->format('M d, Y') }}
                                            </span>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Version Actions -->
                                    <div class="flex-shrink-0 ml-4">
                                        <div class="flex items-center space-x-2">
                                            <a href="{{ route('master-file.show', $version) }}"
                                               class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                                               title="View Details">
                                                <span class="material-symbols-sharp text-sm">visibility</span>
                                            </a>
                                            <a href="{{ route('master-file.download', $version) }}"
                                               class="p-2 text-gray-400 hover:text-green-600 hover:bg-green-50 rounded-lg transition-colors"
                                               title="Download">
                                                <span class="material-symbols-sharp text-sm">download</span>
                                            </a>
                                            @if(auth()->user()->hasRole(['administrator', 'developer']) || $version->uploaded_by === auth()->id())
                                            <button class="p-2 text-gray-400 hover:text-orange-600 hover:bg-orange-50 rounded-lg transition-colors"
                                                    title="Edit">
                                                <span class="material-symbols-sharp text-sm">edit</span>
                                            </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- File Type & Name -->
                                <div class="mt-3 flex items-center space-x-2">
                                    @if(Str::contains($version->mime_type, 'pdf'))
                                        <span class="material-symbols-sharp text-red-500 text-sm">picture_as_pdf</span>
                                    @elseif(Str::contains($version->mime_type, 'word') || Str::endsWith($version->original_filename, ['.doc', '.docx']))
                                        <span class="material-symbols-sharp text-blue-500 text-sm">description</span>
                                    @else
                                        <span class="material-symbols-sharp text-gray-400 text-sm">insert_drive_file</span>
                                    @endif
                                    <span class="text-sm text-gray-600 truncate">{{ $version->original_filename }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Version Summary -->
            <div class="p-6 border-t border-gray-200 bg-gray-50">
                <div class="grid grid-cols-3 gap-4 text-center">
                    <div>
                        <div class="text-2xl font-bold text-blue-600">{{ $selectedFileVersions->count() }}</div>
                        <div class="text-sm text-gray-600">Total Versions</div>
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-green-600">
                            {{ $selectedFileVersions->where('status', 'active')->count() }}
                        </div>
                        <div class="text-sm text-gray-600">Active Versions</div>
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-gray-600">
                            {{ $selectedFileVersions->first()->created_at->diffInDays($selectedFileVersions->last()->created_at) }}
                        </div>
                        <div class="text-sm text-gray-600">Days Span</div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Version Management Tips -->
    @if(!$selectedFile)
    <div class="bg-amber-50 rounded-xl border border-amber-200 p-6">
        <h3 class="text-lg font-semibold text-amber-900 mb-4 flex items-center">
            <span class="material-symbols-sharp text-amber-600 mr-2">lightbulb</span>
            Version Management Tips
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-amber-800">
            <div>
                <h4 class="font-medium mb-2">Best Practices:</h4>
                <ul class="space-y-1">
                    <li>• Always include revision notes</li>
                    <li>• Use semantic versioning (1.0, 1.1, 2.0)</li>
                    <li>• Archive old versions when no longer needed</li>
                    <li>• Set clear effective dates for new versions</li>
                </ul>
            </div>
            <div>
                <h4 class="font-medium mb-2">Version States:</h4>
                <ul class="space-y-1">
                    <li>• <strong>Active:</strong> Current version in use</li>
                    <li>• <strong>Draft:</strong> Work in progress</li>
                    <li>• <strong>Superseded:</strong> Replaced by newer version</li>
                    <li>• <strong>Archived:</strong> Historical reference only</li>
                </ul>
            </div>
        </div>
    </div>
    @endif
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
