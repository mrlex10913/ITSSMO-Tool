<div class="space-y-6">
    <!-- Flash Messages -->
    @if (session()->has('success'))
        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
            <div class="flex items-center">
                <span class="material-symbols-sharp text-green-600 mr-2">check_circle</span>
                <p class="text-sm text-green-800">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="flex items-center">
                <span class="material-symbols-sharp text-red-600 mr-2">error</span>
                <p class="text-sm text-red-800">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    <!-- Newer Version Available Notice -->
    @if($file->hasNewerVersion())
    @php $newestVersion = $file->getNewestVersion(); @endphp
    <div class="bg-yellow-50 border border-yellow-300 rounded-lg p-4">
        <div class="flex items-start">
            <span class="material-symbols-sharp text-yellow-600 mr-3 mt-0.5">update</span>
            <div class="flex-1">
                <h3 class="text-sm font-semibold text-yellow-800">Newer Version Available</h3>
                <p class="text-sm text-yellow-700 mt-1">
                    You are viewing an outdated version (v{{ $file->version }}) of this document.
                    @if($newestVersion)
                    Version {{ $newestVersion->version }} is now available.
                    @endif
                </p>
                @if($newestVersion)
                <a href="{{ route('document-library.show', $newestVersion) }}"
                   class="inline-flex items-center mt-2 text-sm font-medium text-yellow-800 hover:text-yellow-900">
                    <span class="material-symbols-sharp text-sm mr-1">open_in_new</span>
                    View Latest Version (v{{ $newestVersion->version }})
                </a>
                @endif
            </div>
        </div>
    </div>
    @endif

    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between space-y-4 lg:space-y-0">
        <div class="flex-1">
            <div class="flex items-center space-x-3 mb-2">
                <a href="{{ route('document-library.search') }}" class="text-blue-600 hover:text-blue-800">
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
            @if($file->status === 'active' && $file->attachments->count() > 0)
            <button wire:click="downloadAll" class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white hover:bg-blue-700 transition-colors">
                <span class="material-symbols-sharp text-sm mr-2">folder_zip</span>
                Download All (ZIP)
            </button>
            <button wire:click="download" class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                <span class="material-symbols-sharp text-sm mr-2">download</span>
                Download File Only
            </button>
            @else
            <button wire:click="download" class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white hover:bg-blue-700 transition-colors">
                <span class="material-symbols-sharp text-sm mr-2">download</span>
                Download
            </button>
            @endif
            @if(auth()->user()->hasRole(['administrator', 'developer']) || $file->uploaded_by == auth()->id())
            <button wire:click="openEditModal" class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                <span class="material-symbols-sharp text-sm mr-2">edit</span>
                Edit
            </button>
            @endif
            <button class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                <span class="material-symbols-sharp text-sm mr-2">share</span>
                Share
            </button>
            <a href="{{ route('document-library.upload-version', $file->id) }}"
            class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white hover:bg-indigo-700">
                <span class="material-symbols-sharp text-sm mr-2">upload</span>
                Upload New Version
            </a>
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
                    @if(Str::contains($file->mime_type, 'pdf'))
                        <!-- PDF Preview -->
                        <div class="relative">
                            <iframe
                                src="{{ route('document-library.preview', $file) }}"
                                class="w-full rounded-lg border border-gray-200"
                                style="height: 600px;"
                                title="PDF Preview">
                            </iframe>
                            <div class="mt-4 flex items-center justify-center gap-3">
                                <a href="{{ route('document-library.preview', $file) }}"
                                   target="_blank"
                                   class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                                    <span class="material-symbols-sharp text-sm mr-2">open_in_new</span>
                                    Open in New Tab
                                </a>
                                <button wire:click="download" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                    <span class="material-symbols-sharp text-sm mr-2">download</span>
                                    Download
                                </button>
                            </div>
                        </div>
                    @elseif(Str::contains($file->mime_type, 'image'))
                        <!-- Image Preview -->
                        <div class="text-center">
                            <img
                                src="{{ route('document-library.preview', $file) }}"
                                alt="{{ $file->title }}"
                                class="max-w-full max-h-96 mx-auto rounded-lg shadow-sm border border-gray-200 cursor-pointer hover:opacity-90 transition-opacity"
                                onclick="window.open('{{ route('document-library.preview', $file) }}', '_blank')"
                            >
                            <p class="text-sm text-gray-500 mt-2">Click image to view full size</p>
                            <div class="mt-4 flex items-center justify-center gap-3">
                                <a href="{{ route('document-library.preview', $file) }}"
                                   target="_blank"
                                   class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                                    <span class="material-symbols-sharp text-sm mr-2">open_in_new</span>
                                    View Full Size
                                </a>
                                <button wire:click="download" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                    <span class="material-symbols-sharp text-sm mr-2">download</span>
                                    Download
                                </button>
                            </div>
                        </div>
                    @else
                        <!-- Other File Types - Show icon and preview options -->
                        <div class="text-center py-8 bg-gray-50 rounded-lg border-2 border-dashed border-gray-200">
                            <div class="flex flex-col items-center space-y-4">
                                @if(Str::contains($file->mime_type, 'word') || Str::endsWith($file->original_filename, ['.doc', '.docx']))
                                    <span class="material-symbols-sharp text-6xl text-blue-500">description</span>
                                @elseif(Str::contains($file->mime_type, 'excel') || Str::contains($file->mime_type, 'spreadsheet') || Str::endsWith($file->original_filename, ['.xls', '.xlsx']))
                                    <span class="material-symbols-sharp text-6xl text-green-500">table_chart</span>
                                @elseif(Str::contains($file->mime_type, 'powerpoint') || Str::contains($file->mime_type, 'presentation') || Str::endsWith($file->original_filename, ['.ppt', '.pptx']))
                                    <span class="material-symbols-sharp text-6xl text-orange-500">slideshow</span>
                                @else
                                    <span class="material-symbols-sharp text-6xl text-gray-400">insert_drive_file</span>
                                @endif

                                <div class="text-center">
                                    <h4 class="text-lg font-medium text-gray-900">{{ $file->original_filename }}</h4>
                                    <p class="text-sm text-gray-500 mt-1">{{ $file->formatted_file_size }} • {{ strtoupper(pathinfo($file->original_filename, PATHINFO_EXTENSION)) }}</p>
                                    <p class="text-xs text-gray-400 mt-2">This file type cannot be previewed in the browser</p>

                                    <div class="mt-4 flex items-center justify-center gap-3">
                                        <a href="{{ route('document-library.preview', $file) }}"
                                           target="_blank"
                                           class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                                            <span class="material-symbols-sharp text-sm mr-2">open_in_new</span>
                                            Try Preview
                                        </a>
                                        <button wire:click="download" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                            <span class="material-symbols-sharp text-sm mr-2">download</span>
                                            Download
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
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

            <!-- Attachments Section -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <span class="material-symbols-sharp text-indigo-600 mr-2">attach_file</span>
                            Attachments
                            @if($file->attachments->count() > 0)
                            <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                {{ $file->attachments->count() }}
                            </span>
                            @endif
                        </h3>
                        @if(auth()->user()->hasRole(['administrator', 'developer']) || $file->uploaded_by == auth()->id())
                        <button wire:click="openAttachmentModal"
                            class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-indigo-700 bg-indigo-100 rounded-lg hover:bg-indigo-200 transition-colors">
                            <span class="material-symbols-sharp text-sm mr-1">add</span>
                            Add
                        </button>
                        @endif
                    </div>
                </div>
                <div class="p-6">
                    @if($file->attachments->count() > 0)
                    <div class="space-y-3">
                        @foreach($file->attachments as $attachment)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                            <div class="flex items-center space-x-3 flex-1 min-w-0">
                                <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0
                                    @if(Str::contains($attachment->mime_type, 'pdf')) bg-red-100
                                    @elseif(Str::contains($attachment->mime_type, 'word')) bg-blue-100
                                    @elseif(Str::contains($attachment->mime_type, 'excel') || Str::contains($attachment->mime_type, 'spreadsheet')) bg-green-100
                                    @elseif(Str::contains($attachment->mime_type, 'image')) bg-purple-100
                                    @else bg-gray-100
                                    @endif">
                                    <span class="material-symbols-sharp text-lg
                                        @if(Str::contains($attachment->mime_type, 'pdf')) text-red-600
                                        @elseif(Str::contains($attachment->mime_type, 'word')) text-blue-600
                                        @elseif(Str::contains($attachment->mime_type, 'excel') || Str::contains($attachment->mime_type, 'spreadsheet')) text-green-600
                                        @elseif(Str::contains($attachment->mime_type, 'image')) text-purple-600
                                        @else text-gray-600
                                        @endif">
                                        @if(Str::contains($attachment->mime_type, 'pdf'))
                                            picture_as_pdf
                                        @elseif(Str::contains($attachment->mime_type, 'word'))
                                            description
                                        @elseif(Str::contains($attachment->mime_type, 'excel') || Str::contains($attachment->mime_type, 'spreadsheet'))
                                            table_chart
                                        @elseif(Str::contains($attachment->mime_type, 'image'))
                                            image
                                        @else
                                            insert_drive_file
                                        @endif
                                    </span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ $attachment->title }}</p>
                                    <p class="text-xs text-gray-500 truncate">
                                        {{ $attachment->original_filename }} · {{ $attachment->formatted_file_size }}
                                        @if($attachment->description)
                                        · {{ Str::limit($attachment->description, 30) }}
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-1 flex-shrink-0 ml-2">
                                <button wire:click="previewAttachmentModal({{ $attachment->id }})"
                                   class="p-2 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                                   title="Preview">
                                    <span class="material-symbols-sharp text-sm">visibility</span>
                                </button>
                                <a href="{{ route('document-library.download-attachment', $attachment->id) }}"
                                   class="p-2 text-gray-500 hover:text-green-600 hover:bg-green-50 rounded-lg transition-colors"
                                   title="Download">
                                    <span class="material-symbols-sharp text-sm">download</span>
                                </a>
                                @if(auth()->user()->hasRole(['administrator', 'developer']) || $file->uploaded_by == auth()->id())
                                <button wire:click="deleteAttachment({{ $attachment->id }})"
                                    wire:confirm="Are you sure you want to delete this attachment?"
                                    class="p-2 text-gray-500 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                    title="Delete">
                                    <span class="material-symbols-sharp text-sm">delete</span>
                                </button>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-6">
                        <span class="material-symbols-sharp text-4xl text-gray-300">attach_file</span>
                        <p class="mt-2 text-sm text-gray-500">No attachments yet</p>
                        @if(auth()->user()->hasRole(['administrator', 'developer']) || $file->uploaded_by == auth()->id())
                        <button wire:click="openAttachmentModal"
                            class="mt-3 inline-flex items-center px-4 py-2 text-sm font-medium text-indigo-700 bg-indigo-100 rounded-lg hover:bg-indigo-200 transition-colors">
                            <span class="material-symbols-sharp text-sm mr-1">add</span>
                            Add First Attachment
                        </button>
                        @endif
                    </div>
                    @endif
                </div>
            </div>

            <!-- Version History Button -->
            @php $allVersions = $file->getAllVersions(); @endphp
            @if($allVersions->count() > 1)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                            <span class="material-symbols-sharp text-orange-600">history</span>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Version History</h3>
                            <p class="text-sm text-gray-500">{{ $allVersions->count() }} versions available</p>
                        </div>
                    </div>
                    <button wire:click="openVersionModal"
                        class="inline-flex items-center px-4 py-2 bg-orange-100 text-orange-700 rounded-lg hover:bg-orange-200 transition-colors">
                        <span class="material-symbols-sharp text-sm mr-2">visibility</span>
                        View All Versions
                    </button>
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
            @if(($file->visible_to_departments && count($file->visible_to_departments) > 0) || ($file->visible_to_users && count($file->visible_to_users) > 0))
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <span class="material-symbols-sharp text-orange-600 mr-2">visibility</span>
                        Visible To
                    </h3>
                </div>
                <div class="p-6 space-y-4">
                    @if($file->visible_to_departments && count($file->visible_to_departments) > 0)
                    <div>
                        <p class="text-sm font-medium text-gray-700 mb-2">Departments</p>
                        <div class="flex flex-wrap gap-2">
                            @foreach($file->visible_to_departments as $dept)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ $dept }}
                            </span>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    @if($file->visible_to_users && count($file->visible_to_users) > 0)
                    <div>
                        <p class="text-sm font-medium text-gray-700 mb-2">Specific Users</p>
                        <div class="space-y-2">
                            @foreach($file->visible_to_users as $email)
                            @php $visibleUser = \App\Models\User::where('email', $email)->first(); @endphp
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                                    <span class="text-green-700 font-medium text-xs">{{ $visibleUser ? strtoupper(substr($visibleUser->name, 0, 1)) : '?' }}</span>
                                </div>
                                <div class="text-sm">
                                    <span class="font-medium text-gray-900">{{ $visibleUser->name ?? 'Unknown' }}</span>
                                    <span class="text-gray-500">({{ $email }})</span>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
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

    <!-- Edit Modal -->
    @if($showEditModal)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeEditModal"></div>

            <!-- Modal panel -->
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                <form wire:submit="saveEdit">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900" id="modal-title">Edit Document</h3>
                            <button type="button" wire:click="closeEditModal" class="text-gray-400 hover:text-gray-600">
                                <span class="material-symbols-sharp">close</span>
                            </button>
                        </div>

                        <div class="space-y-4">
                            <!-- Title -->
                            <div>
                                <label for="editTitle" class="block text-sm font-medium text-gray-700 mb-1">
                                    Document Title <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="editTitle" wire:model="editTitle"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                @error('editTitle')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Category -->
                                <div>
                                    <label for="editCategoryId" class="block text-sm font-medium text-gray-700 mb-1">
                                        Category <span class="text-red-500">*</span>
                                    </label>
                                    <select id="editCategoryId" wire:model="editCategoryId"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                        <option value="">Select category...</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('editCategoryId')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Document Code -->
                                <div>
                                    <label for="editDocumentCode" class="block text-sm font-medium text-gray-700 mb-1">
                                        Document Code
                                    </label>
                                    <input type="text" id="editDocumentCode" wire:model="editDocumentCode"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                </div>
                            </div>

                            <!-- Description -->
                            <div>
                                <label for="editDescription" class="block text-sm font-medium text-gray-700 mb-1">
                                    Description
                                </label>
                                <textarea id="editDescription" wire:model="editDescription" rows="3"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"></textarea>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Effective Date -->
                                <div>
                                    <label for="editEffectiveDate" class="block text-sm font-medium text-gray-700 mb-1">
                                        Effective Date
                                    </label>
                                    <input type="date" id="editEffectiveDate" wire:model="editEffectiveDate"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                </div>

                                <!-- Review Date -->
                                <div>
                                    <label for="editReviewDate" class="block text-sm font-medium text-gray-700 mb-1">
                                        Review Date
                                    </label>
                                    <input type="date" id="editReviewDate" wire:model="editReviewDate"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                </div>
                            </div>

                            <!-- Tags -->
                            <div>
                                <label for="editTags" class="block text-sm font-medium text-gray-700 mb-1">
                                    Tags (comma-separated)
                                </label>
                                <input type="text" id="editTags" wire:model="editTags"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="e.g. policy, HR, 2026">
                            </div>

                            <!-- Visible to Departments -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Visible to Departments
                                </label>
                                <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                                    @foreach(['ITSS', 'PAMO', 'BFO', 'HR', 'Registrar', 'Accounting'] as $dept)
                                    <label class="flex items-center">
                                        <input type="checkbox" wire:model="editVisibleToDepartments" value="{{ $dept }}"
                                            class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                        <span class="ml-2 text-sm text-gray-700">{{ $dept }}</span>
                                    </label>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Visible to Specific Users -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Visible to Specific Users
                                </label>
                                <div class="relative">
                                    <input type="text"
                                           wire:model.live.debounce.300ms="editUserSearch"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                           placeholder="Search by name or email...">
                                    <span class="material-symbols-sharp absolute right-3 top-2.5 text-gray-400">search</span>

                                    <!-- Search Results Dropdown -->
                                    @if(count($editSearchableUsers) > 0)
                                    <div class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-48 overflow-y-auto">
                                        @foreach($editSearchableUsers as $user)
                                        <button type="button"
                                                wire:click="addEditUser('{{ $user->email }}')"
                                                class="w-full px-4 py-2 text-left hover:bg-blue-50 flex items-center gap-3 border-b border-gray-100 last:border-0">
                                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                                                <span class="text-blue-600 font-medium text-sm">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-medium text-gray-900 truncate">{{ $user->name }}</p>
                                                <p class="text-xs text-gray-500 truncate">{{ $user->email }} · {{ $user->department ?? 'N/A' }}</p>
                                            </div>
                                        </button>
                                        @endforeach
                                    </div>
                                    @elseif(strlen($editUserSearch) >= 2 && count($editSearchableUsers) === 0)
                                    <div class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg p-3 text-center text-gray-500 text-sm">
                                        No users found matching "{{ $editUserSearch }}"
                                    </div>
                                    @endif
                                </div>

                                <!-- Selected Users -->
                                @if(count($editSelectedUsers) > 0)
                                <div class="mt-3 space-y-2">
                                    @foreach($editSelectedUsers as $user)
                                    <div class="flex items-center justify-between px-3 py-2 bg-blue-50 rounded-lg">
                                        <div class="flex items-center gap-2">
                                            <div class="w-6 h-6 bg-blue-200 rounded-full flex items-center justify-center">
                                                <span class="text-blue-700 font-medium text-xs">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                            </div>
                                            <div>
                                                <span class="text-sm font-medium text-gray-900">{{ $user->name }}</span>
                                                <span class="text-xs text-gray-500 ml-1">({{ $user->email }})</span>
                                            </div>
                                        </div>
                                        <button type="button"
                                                wire:click="removeEditUser('{{ $user->email }}')"
                                                class="text-red-500 hover:text-red-700 p-0.5">
                                            <span class="material-symbols-sharp text-sm">close</span>
                                        </button>
                                    </div>
                                    @endforeach
                                </div>
                                @endif
                            </div>

                            <!-- Confidential -->
                            <div>
                                <label class="flex items-center">
                                    <input type="checkbox" wire:model="editIsConfidential"
                                        class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                                    <span class="ml-2 text-sm font-medium text-gray-700">Mark as Confidential</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit"
                            wire:loading.attr="disabled"
                            class="w-full sm:w-auto inline-flex justify-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 sm:ml-3">
                            <span wire:loading.remove wire:target="saveEdit">Save Changes</span>
                            <span wire:loading wire:target="saveEdit">Saving...</span>
                        </button>
                        <button type="button" wire:click="closeEditModal"
                            class="mt-3 sm:mt-0 w-full sm:w-auto inline-flex justify-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    <!-- Version History Modal -->
    @if($showVersionModal)
    @php $allVersions = $file->getAllVersions(); @endphp
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="version-modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" wire:click="closeVersionModal"></div>

            <!-- Modal panel -->
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6">
                    <!-- Header -->
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                                <span class="material-symbols-sharp text-orange-600">history</span>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900" id="version-modal-title">Version History</h3>
                                <p class="text-sm text-gray-500">{{ $allVersions->count() }} versions of "{{ $file->title }}"</p>
                            </div>
                        </div>
                        <button wire:click="closeVersionModal" class="text-gray-400 hover:text-gray-600">
                            <span class="material-symbols-sharp">close</span>
                        </button>
                    </div>

                    <!-- Version Timeline -->
                    <div class="space-y-4 max-h-96 overflow-y-auto">
                        @foreach($allVersions as $version)
                        @php
                            $isCurrentlyViewing = $version->id === $file->id;
                            $isLatest = $version->status === 'active';
                        @endphp
                        <div class="relative pl-8 pb-4 {{ !$loop->last ? 'border-l-2 border-gray-200 ml-4' : 'ml-4' }}">
                            <!-- Timeline dot -->
                            <div class="absolute -left-2 top-0 w-4 h-4 rounded-full {{ $isLatest ? 'bg-green-500' : ($isCurrentlyViewing ? 'bg-blue-500' : 'bg-gray-300') }} border-2 border-white shadow"></div>

                            <div class="bg-gray-50 rounded-lg p-4 {{ $isCurrentlyViewing ? 'ring-2 ring-blue-500 bg-blue-50' : '' }}">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <span class="font-semibold text-gray-900">v{{ $version->version }}</span>
                                            @if($isLatest)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <span class="material-symbols-sharp text-xs mr-0.5">star</span>
                                                Latest
                                            </span>
                                            @endif
                                            @if($isCurrentlyViewing)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                <span class="material-symbols-sharp text-xs mr-0.5">visibility</span>
                                                Currently Viewing
                                            </span>
                                            @endif
                                            @if(!$isLatest && !$isCurrentlyViewing)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                                Superseded
                                            </span>
                                            @endif
                                        </div>
                                        <p class="text-sm text-gray-500 mt-1">
                                            <span class="font-medium">{{ $version->uploader->name }}</span> · {{ $version->created_at->format('M d, Y \a\t g:i A') }}
                                        </p>
                                        @if($version->revision_notes)
                                        <p class="text-sm text-gray-600 mt-2 italic">"{{ $version->revision_notes }}"</p>
                                        @endif
                                    </div>
                                    <div class="flex items-center space-x-1 ml-4">
                                        @if(!$isCurrentlyViewing)
                                        <a href="{{ route('document-library.show', $version) }}"
                                           class="inline-flex items-center px-2 py-1 text-xs font-medium text-blue-700 bg-blue-100 rounded hover:bg-blue-200 transition-colors"
                                           title="View this version">
                                            <span class="material-symbols-sharp text-sm">open_in_new</span>
                                        </a>
                                        @endif
                                        <a href="{{ route('document-library.download', $version) }}"
                                           class="inline-flex items-center px-2 py-1 text-xs font-medium text-gray-700 bg-gray-100 rounded hover:bg-gray-200 transition-colors"
                                           title="Download this version">
                                            <span class="material-symbols-sharp text-sm">download</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Footer -->
                <div class="bg-gray-50 px-4 py-3 sm:px-6 flex justify-end">
                    <button type="button" wire:click="closeVersionModal"
                        class="inline-flex justify-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Add Attachment Modal -->
    @if($showAttachmentModal)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="attachment-modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" wire:click="closeAttachmentModal"></div>

            <!-- Modal panel -->
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form wire:submit="uploadAttachment">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6">
                        <!-- Header -->
                        <div class="flex items-center justify-between mb-6">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                                    <span class="material-symbols-sharp text-indigo-600">attach_file</span>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900" id="attachment-modal-title">Add Attachment</h3>
                                    <p class="text-sm text-gray-500">Attach a supporting file to this document</p>
                                </div>
                            </div>
                            <button type="button" wire:click="closeAttachmentModal" class="text-gray-400 hover:text-gray-600">
                                <span class="material-symbols-sharp">close</span>
                            </button>
                        </div>

                        <div class="space-y-4">
                            <!-- File Upload -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">File <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    @if($attachmentFile)
                                    <div class="flex items-center justify-between p-3 bg-green-50 border border-green-200 rounded-lg">
                                        <div class="flex items-center space-x-3">
                                            <span class="material-symbols-sharp text-green-600">check_circle</span>
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">{{ $attachmentFile->getClientOriginalName() }}</p>
                                                <p class="text-xs text-gray-500">{{ number_format($attachmentFile->getSize() / 1024, 2) }} KB</p>
                                            </div>
                                        </div>
                                        <button type="button" wire:click="$set('attachmentFile', null)" class="text-red-500 hover:text-red-700">
                                            <span class="material-symbols-sharp text-sm">close</span>
                                        </button>
                                    </div>
                                    @else
                                    <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 transition-colors">
                                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                            <span class="material-symbols-sharp text-3xl text-gray-400 mb-2">cloud_upload</span>
                                            <p class="text-sm text-gray-500"><span class="font-medium text-indigo-600">Click to upload</span> or drag and drop</p>
                                            <p class="text-xs text-gray-400 mt-1">Any file type up to 100MB</p>
                                        </div>
                                        <input type="file" wire:model="attachmentFile" class="hidden">
                                    </label>
                                    @endif
                                </div>
                                @error('attachmentFile') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror

                                <!-- Loading indicator -->
                                <div wire:loading wire:target="attachmentFile" class="mt-2">
                                    <div class="flex items-center space-x-2 text-sm text-indigo-600">
                                        <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                        </svg>
                                        <span>Uploading file...</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Title -->
                            <div>
                                <label for="attachmentTitle" class="block text-sm font-medium text-gray-700 mb-1">Title <span class="text-red-500">*</span></label>
                                <input type="text" id="attachmentTitle" wire:model="attachmentTitle"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    placeholder="Enter a title for this attachment">
                                @error('attachmentTitle') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>

                            <!-- Description -->
                            <div>
                                <label for="attachmentDescription" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                <textarea id="attachmentDescription" wire:model="attachmentDescription" rows="2"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    placeholder="Optional description for this attachment"></textarea>
                                @error('attachmentDescription') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit"
                            wire:loading.attr="disabled"
                            class="w-full sm:w-auto inline-flex justify-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 sm:ml-3">
                            <span wire:loading.remove wire:target="uploadAttachment">Upload Attachment</span>
                            <span wire:loading wire:target="uploadAttachment" class="flex items-center">
                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                </svg>
                                Uploading...
                            </span>
                        </button>
                        <button type="button" wire:click="closeAttachmentModal"
                            class="mt-3 sm:mt-0 w-full sm:w-auto inline-flex justify-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    <!-- Attachment Preview Modal -->
    @if($showAttachmentPreviewModal && $previewAttachment)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="attachment-preview-modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" wire:click="closeAttachmentPreviewModal"></div>

            <!-- Modal panel -->
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                <div class="bg-white">
                    <!-- Header -->
                    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center
                                @if(Str::contains($previewAttachment->mime_type, 'pdf')) bg-red-100
                                @elseif(Str::contains($previewAttachment->mime_type, 'word')) bg-blue-100
                                @elseif(Str::contains($previewAttachment->mime_type, 'excel') || Str::contains($previewAttachment->mime_type, 'spreadsheet')) bg-green-100
                                @elseif(Str::contains($previewAttachment->mime_type, 'image')) bg-purple-100
                                @else bg-gray-100
                                @endif">
                                <span class="material-symbols-sharp text-lg
                                    @if(Str::contains($previewAttachment->mime_type, 'pdf')) text-red-600
                                    @elseif(Str::contains($previewAttachment->mime_type, 'word')) text-blue-600
                                    @elseif(Str::contains($previewAttachment->mime_type, 'excel') || Str::contains($previewAttachment->mime_type, 'spreadsheet')) text-green-600
                                    @elseif(Str::contains($previewAttachment->mime_type, 'image')) text-purple-600
                                    @else text-gray-600
                                    @endif">
                                    @if(Str::contains($previewAttachment->mime_type, 'pdf'))
                                        picture_as_pdf
                                    @elseif(Str::contains($previewAttachment->mime_type, 'word'))
                                        description
                                    @elseif(Str::contains($previewAttachment->mime_type, 'excel') || Str::contains($previewAttachment->mime_type, 'spreadsheet'))
                                        table_chart
                                    @elseif(Str::contains($previewAttachment->mime_type, 'image'))
                                        image
                                    @else
                                        insert_drive_file
                                    @endif
                                </span>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900" id="attachment-preview-modal-title">{{ $previewAttachment->title }}</h3>
                                <p class="text-sm text-gray-500">{{ $previewAttachment->original_filename }} · {{ $previewAttachment->formatted_file_size }}</p>
                            </div>
                        </div>
                        <button wire:click="closeAttachmentPreviewModal" class="text-gray-400 hover:text-gray-600">
                            <span class="material-symbols-sharp">close</span>
                        </button>
                    </div>

                    <!-- Preview Content -->
                    <div class="p-6">
                        @if($previewAttachment->description)
                        <div class="mb-4 p-3 bg-gray-50 rounded-lg">
                            <p class="text-sm text-gray-600"><strong>Description:</strong> {{ $previewAttachment->description }}</p>
                        </div>
                        @endif

                        <div class="bg-gray-100 rounded-lg overflow-hidden">
                            @if(Str::contains($previewAttachment->mime_type, 'pdf'))
                                <!-- PDF Preview -->
                                <iframe
                                    src="{{ Storage::url($previewAttachment->file_path) }}"
                                    class="w-full border-0"
                                    style="height: 500px;"
                                    title="PDF Preview">
                                </iframe>
                            @elseif(Str::contains($previewAttachment->mime_type, 'image'))
                                <!-- Image Preview -->
                                <div class="flex items-center justify-center p-4">
                                    <img
                                        src="{{ Storage::url($previewAttachment->file_path) }}"
                                        alt="{{ $previewAttachment->title }}"
                                        class="max-w-full max-h-96 rounded-lg shadow-sm">
                                </div>
                            @elseif(Str::contains($previewAttachment->mime_type, 'text') || Str::endsWith($previewAttachment->original_filename, ['.txt', '.csv', '.log']))
                                <!-- Text File Preview -->
                                <div class="p-4 max-h-96 overflow-y-auto">
                                    <pre class="text-sm text-gray-700 whitespace-pre-wrap font-mono">{{ Storage::disk('public')->get($previewAttachment->file_path) }}</pre>
                                </div>
                            @else
                                <!-- Other File Types - No Preview Available -->
                                <div class="text-center py-12">
                                    <span class="material-symbols-sharp text-6xl text-gray-300">insert_drive_file</span>
                                    <p class="mt-4 text-gray-500">Preview not available for this file type</p>
                                    <p class="text-sm text-gray-400 mt-1">{{ strtoupper(pathinfo($previewAttachment->original_filename, PATHINFO_EXTENSION)) }} file</p>
                                </div>
                            @endif
                        </div>

                        <!-- File Info -->
                        <div class="mt-4 grid grid-cols-2 sm:grid-cols-4 gap-4 text-sm">
                            <div>
                                <span class="text-gray-500">File Type</span>
                                <p class="font-medium text-gray-900">{{ strtoupper(pathinfo($previewAttachment->original_filename, PATHINFO_EXTENSION)) }}</p>
                            </div>
                            <div>
                                <span class="text-gray-500">Size</span>
                                <p class="font-medium text-gray-900">{{ $previewAttachment->formatted_file_size }}</p>
                            </div>
                            <div>
                                <span class="text-gray-500">Uploaded By</span>
                                <p class="font-medium text-gray-900">{{ $previewAttachment->uploader->name ?? 'Unknown' }}</p>
                            </div>
                            <div>
                                <span class="text-gray-500">Uploaded On</span>
                                <p class="font-medium text-gray-900">{{ $previewAttachment->created_at->format('M d, Y') }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="bg-gray-50 px-6 py-4 sm:flex sm:flex-row-reverse gap-3">
                        <a href="{{ route('document-library.download-attachment', $previewAttachment->id) }}"
                           class="w-full sm:w-auto inline-flex justify-center items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <span class="material-symbols-sharp text-sm mr-2">download</span>
                            Download
                        </a>
                        <a href="{{ Storage::url($previewAttachment->file_path) }}"
                           target="_blank"
                           class="mt-3 sm:mt-0 w-full sm:w-auto inline-flex justify-center items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <span class="material-symbols-sharp text-sm mr-2">open_in_new</span>
                            Open in New Tab
                        </a>
                        <button type="button" wire:click="closeAttachmentPreviewModal"
                            class="mt-3 sm:mt-0 w-full sm:w-auto inline-flex justify-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
