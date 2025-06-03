<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between space-y-4 lg:space-y-0">
        <div class="flex-1">
            <div class="flex items-center space-x-3 mb-2">
                <a href="{{ route('master-file.search') }}" class="text-blue-600 hover:text-blue-800">
                    <span class="material-symbols-sharp">arrow_back</span>
                </a>
                <div class="flex items-center space-x-2">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background-color: {{ $file->category->color }}20;">
                        <span class="material-symbols-sharp text-sm" style="color: {{ $file->category->color }};">{{ $file->category->icon }}</span>
                    </div>
                    <span class="text-sm text-gray-500">{{ $file->category->name }}</span>
                </div>
            </div>
            <h1 class="text-3xl font-bold text-gray-900">{{ $file->title }}</h1>
            <div class="flex flex-wrap items-center gap-3 mt-2">
                @if($file->document_code)
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                    {{ $file->document_code }}
                </span>
                @endif
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                    v{{ $file->version }}
                </span>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                    @if($file->status === 'active') bg-green-100 text-green-800
                    @elseif($file->status === 'draft') bg-yellow-100 text-yellow-800
                    @elseif($file->status === 'superseded') bg-gray-100 text-gray-800
                    @elseif($file->status === 'archived') bg-red-100 text-red-800
                    @else bg-blue-100 text-blue-800
                    @endif">
                    {{ ucfirst($file->status) }}
                </span>
                @if($file->is_confidential)
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                    <span class="material-symbols-sharp text-xs mr-1">lock</span>
                    Confidential
                </span>
                @endif
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-3">
            <button wire:click="download" class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white hover:bg-blue-700 transition-colors">
                <span class="material-symbols-sharp text-sm mr-2">download</span>
                Download
            </button>
            @if(auth()->user()->hasRole(['administrator', 'developer']) || $file->uploaded_by === auth()->id())
            <button class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                <span class="material-symbols-sharp text-sm mr-2">edit</span>
                Edit
            </button>
            @endif
            <button class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                <span class="material-symbols-sharp text-sm mr-2">share</span>
                Share
            </button>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- File Preview/Info -->
        <div class="lg:col-span-2 space-y-6">
            <!-- File Preview -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <span class="material-symbols-sharp text-blue-600 mr-2">visibility</span>
                        File Preview
                    </h3>
                </div>
                <div class="p-6">
                    <div class="text-center py-12 bg-gray-50 rounded-lg border-2 border-dashed border-gray-200">
                        <div class="flex flex-col items-center space-y-4">
                            @if(Str::contains($file->mime_type, 'pdf'))
                                <span class="material-symbols-sharp text-6xl text-red-500">picture_as_pdf</span>
                            @elseif(Str::contains($file->mime_type, 'word') || Str::endsWith($file->original_filename, ['.doc', '.docx']))
                                <span class="material-symbols-sharp text-6xl text-blue-500">description</span>
                            @else
                                <span class="material-symbols-sharp text-6xl text-gray-400">insert_drive_file</span>
                            @endif

                            <div class="text-center">
                                <h4 class="text-lg font-medium text-gray-900">{{ $file->original_filename }}</h4>
                                <p class="text-sm text-gray-500 mt-1">{{ $file->formatted_file_size }} • {{ strtoupper(pathinfo($file->original_filename, PATHINFO_EXTENSION)) }}</p>

                                <button wire:click="download" class="mt-4 inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                    <span class="material-symbols-sharp text-sm mr-2">download</span>
                                    Download to View
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Description -->
            @if($file->description)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <span class="material-symbols-sharp text-purple-600 mr-2">description</span>
                        Description
                    </h3>
                </div>
                <div class="p-6">
                    <p class="text-gray-700 leading-relaxed">{{ $file->description }}</p>
                </div>
            </div>
            @endif

            <!-- Tags -->
            @if($file->tags && count($file->tags) > 0)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <span class="material-symbols-sharp text-green-600 mr-2">label</span>
                        Tags
                    </h3>
                </div>
                <div class="p-6">
                    <div class="flex flex-wrap gap-2">
                        @foreach($file->tags as $tag)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                            <span class="material-symbols-sharp text-xs mr-1">tag</span>
                            {{ $tag }}
                        </span>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- Version History -->
            @if($file->versions->count() > 0)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <span class="material-symbols-sharp text-orange-600 mr-2">history</span>
                        Version History
                    </h3>
                </div>
                <div class="divide-y divide-gray-200">
                    <!-- Current Version -->
                    <div class="p-6 bg-blue-50">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center">
                                    <span class="material-symbols-sharp text-white text-sm">star</span>
                                </div>
                                <div>
                                    <p class="font-medium text-blue-900">Version {{ $file->version }} (Current)</p>
                                    <p class="text-sm text-blue-700">
                                        Uploaded by {{ $file->uploader->name }} • {{ $file->created_at->format('M d, Y') }}
                                    </p>
                                </div>
                            </div>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                Current
                            </span>
                        </div>
                        @if($file->revision_notes)
                        <div class="mt-3 text-sm text-blue-800">
                            <strong>Notes:</strong> {{ $file->revision_notes }}
                        </div>
                        @endif
                    </div>

                    <!-- Previous Versions -->
                    @foreach($file->versions->sortByDesc('version') as $version)
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 bg-gray-400 rounded-full flex items-center justify-center">
                                    <span class="material-symbols-sharp text-white text-sm">history</span>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">Version {{ $version->version }}</p>
                                    <p class="text-sm text-gray-500">
                                        Uploaded by {{ $version->uploader->name }} • {{ $version->created_at->format('M d, Y') }}
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    {{ ucfirst($version->status) }}
                                </span>
                                <a href="{{ route('master-file.download', $version) }}" class="text-blue-600 hover:text-blue-800">
                                    <span class="material-symbols-sharp text-sm">download</span>
                                </a>
                            </div>
                        </div>
                        @if($version->revision_notes)
                        <div class="mt-3 text-sm text-gray-600">
                            <strong>Notes:</strong> {{ $version->revision_notes }}
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- File Details -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <span class="material-symbols-sharp text-blue-600 mr-2">info</span>
                        File Details
                    </h3>
                </div>
                <div class="p-6 space-y-4">
                    <div class="grid grid-cols-1 gap-4 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500">File Size:</span>
                            <span class="font-medium text-gray-900">{{ $file->formatted_file_size }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">File Type:</span>
                            <span class="font-medium text-gray-900">{{ strtoupper(pathinfo($file->original_filename, PATHINFO_EXTENSION)) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Department:</span>
                            <span class="font-medium text-gray-900">{{ $file->department }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Uploaded:</span>
                            <span class="font-medium text-gray-900">{{ $file->created_at->format('M d, Y') }}</span>
                        </div>
                        @if($file->effective_date)
                        <div class="flex justify-between">
                            <span class="text-gray-500">Effective:</span>
                            <span class="font-medium text-gray-900">{{ $file->effective_date->format('M d, Y') }}</span>
                        </div>
                        @endif
                        @if($file->expiry_date)
                        <div class="flex justify-between">
                            <span class="text-gray-500">Expires:</span>
                            <span class="font-medium text-gray-900 {{ $file->expiry_date->isPast() ? 'text-red-600' : ($file->expiry_date->diffInDays() < 30 ? 'text-yellow-600' : '') }}">
                                {{ $file->expiry_date->format('M d, Y') }}
                                @if($file->expiry_date->isPast())
                                    <span class="text-red-600">(Expired)</span>
                                @elseif($file->expiry_date->diffInDays() < 30)
                                    <span class="text-yellow-600">({{ $file->expiry_date->diffForHumans() }})</span>
                                @endif
                            </span>
                        </div>
                        @endif
                        @if($file->review_date)
                        <div class="flex justify-between">
                            <span class="text-gray-500">Review Due:</span>
                            <span class="font-medium text-gray-900">{{ $file->review_date->format('M d, Y') }}</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Statistics -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <span class="material-symbols-sharp text-green-600 mr-2">analytics</span>
                        Statistics
                    </h3>
                </div>
                <div class="p-6 space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            <span class="material-symbols-sharp text-blue-600 text-sm">visibility</span>
                            <span class="text-sm text-gray-600">Views</span>
                        </div>
                        <span class="text-lg font-semibold text-gray-900">{{ number_format($file->view_count) }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            <span class="material-symbols-sharp text-green-600 text-sm">download</span>
                            <span class="text-sm text-gray-600">Downloads</span>
                        </div>
                        <span class="text-lg font-semibold text-gray-900">{{ number_format($file->download_count) }}</span>
                    </div>
                    @if($file->last_accessed_at)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            <span class="material-symbols-sharp text-purple-600 text-sm">schedule</span>
                            <span class="text-sm text-gray-600">Last Access</span>
                        </div>
                        <span class="text-sm font-medium text-gray-900">{{ $file->last_accessed_at->diffForHumans() }}</span>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Uploader Info -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <span class="material-symbols-sharp text-purple-600 mr-2">person</span>
                        Uploader
                    </h3>
                </div>
                <div class="p-6">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                            <span class="material-symbols-sharp text-blue-600">person</span>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">{{ $file->uploader->name }}</p>
                            <p class="text-sm text-gray-500">{{ $file->uploader->email }}</p>
                            <p class="text-xs text-gray-400">{{ $file->created_at->format('M d, Y \a\t g:i A') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Visibility -->
            @if($file->visible_to_departments && count($file->visible_to_departments) > 0)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <span class="material-symbols-sharp text-orange-600 mr-2">visibility</span>
                        Visible To
                    </h3>
                </div>
                <div class="p-6">
                    <div class="flex flex-wrap gap-2">
                        @foreach($file->visible_to_departments as $dept)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            {{ $dept }}
                        </span>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- Recent Access Logs -->
            @if($file->accessLogs->count() > 0)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <span class="material-symbols-sharp text-red-600 mr-2">history</span>
                        Recent Activity
                    </h3>
                </div>
                <div class="divide-y divide-gray-200 max-h-64 overflow-y-auto">
                    @foreach($file->accessLogs->take(10) as $log)
                    <div class="p-4">
                        <div class="flex items-center space-x-3">
                            <div class="flex-shrink-0">
                                @if($log->action === 'download')
                                    <span class="material-symbols-sharp text-green-600 text-sm">download</span>
                                @elseif($log->action === 'view')
                                    <span class="material-symbols-sharp text-blue-600 text-sm">visibility</span>
                                @else
                                    <span class="material-symbols-sharp text-gray-600 text-sm">history</span>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900">
                                    {{ $log->user->name ?? 'Unknown User' }}
                                </p>
                                <p class="text-xs text-gray-500">
                                    {{ ucfirst($log->action) }} • {{ $log->created_at->diffForHumans() }}
                                </p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
