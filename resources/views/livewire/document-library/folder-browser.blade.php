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

    <!-- Header -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 flex items-center">
                    <span class="material-symbols-sharp text-blue-600 mr-3">folder_open</span>
                    My Drive
                </h1>
                <p class="text-gray-500 mt-1">Organize your documents in folders</p>
            </div>
            <div class="flex items-center gap-3">
                <button wire:click="openCreateFolderModal"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors shadow-sm">
                    <span class="material-symbols-sharp text-sm mr-2">create_new_folder</span>
                    New Folder
                </button>
                <a href="{{ route('document-library.upload') }}{{ $currentFolderId ? '?folder='.$currentFolderId : '' }}"
                   class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors shadow-sm">
                    <span class="material-symbols-sharp text-sm mr-2">upload_file</span>
                    Upload File
                </a>
            </div>
        </div>
    </div>

    <!-- Toolbar -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <!-- Breadcrumbs -->
            <nav class="flex items-center space-x-2 text-sm overflow-x-auto">
                <button wire:click="navigateToFolder(null)"
                    class="flex items-center text-gray-600 hover:text-blue-600 transition-colors {{ !$currentFolderId ? 'font-semibold text-blue-600' : '' }}">
                    <span class="material-symbols-sharp text-lg mr-1">home</span>
                    My Drive
                </button>
                @foreach($breadcrumbs as $crumb)
                <span class="text-gray-400">/</span>
                <button wire:click="navigateToFolder({{ $crumb['id'] }})"
                    class="text-gray-600 hover:text-blue-600 transition-colors truncate max-w-32 {{ $loop->last ? 'font-semibold text-blue-600' : '' }}">
                    {{ $crumb['name'] }}
                </button>
                @endforeach
            </nav>

            <!-- Actions -->
            <div class="flex items-center gap-3">
                <!-- Search -->
                <div class="relative">
                    <input type="text" wire:model.live.debounce.300ms="search"
                        placeholder="Search in folder..."
                        class="w-48 sm:w-64 pl-10 pr-4 py-2 text-sm border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    <span class="material-symbols-sharp absolute left-3 top-2.5 text-gray-400 text-sm">search</span>
                </div>

                <!-- View Toggle -->
                <button wire:click="toggleViewMode"
                    class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors"
                    title="{{ $viewMode === 'grid' ? 'List view' : 'Grid view' }}">
                    <span class="material-symbols-sharp">{{ $viewMode === 'grid' ? 'view_list' : 'grid_view' }}</span>
                </button>

                <!-- Up Button -->
                @if($currentFolderId)
                <button wire:click="navigateUp"
                    class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors"
                    title="Go up">
                    <span class="material-symbols-sharp">arrow_upward</span>
                </button>
                @endif
            </div>
        </div>
    </div>

    <!-- Content Area -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        @if($folders->isEmpty() && $files->isEmpty())
            <!-- Empty State -->
            <div class="text-center py-16">
                <span class="material-symbols-sharp text-6xl text-gray-300">folder_off</span>
                <h3 class="mt-4 text-lg font-medium text-gray-900">{{ $search ? 'No results found' : 'This folder is empty' }}</h3>
                <p class="mt-2 text-gray-500">{{ $search ? 'Try a different search term' : 'Create a folder or upload files to get started' }}</p>
                @if(!$search)
                <div class="mt-6 flex items-center justify-center gap-3">
                    <button wire:click="openCreateFolderModal"
                        class="inline-flex items-center px-4 py-2 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition-colors">
                        <span class="material-symbols-sharp text-sm mr-2">create_new_folder</span>
                        New Folder
                    </button>
                    <a href="{{ route('document-library.upload') }}{{ $currentFolderId ? '?folder='.$currentFolderId : '' }}"
                       class="inline-flex items-center px-4 py-2 bg-indigo-100 text-indigo-700 rounded-lg hover:bg-indigo-200 transition-colors">
                        <span class="material-symbols-sharp text-sm mr-2">upload_file</span>
                        Upload File
                    </a>
                </div>
                @endif
            </div>
        @else
            @if($viewMode === 'grid')
                <!-- Grid View -->
                <div class="p-6 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
                    <!-- Folders -->
                    @foreach($folders as $folder)
                    <div class="group relative">
                        <div wire:click="navigateToFolder({{ $folder->id }})"
                            class="cursor-pointer p-4 rounded-xl border-2 border-transparent hover:border-blue-200 hover:bg-blue-50 transition-all">
                            <div class="flex flex-col items-center text-center">
                                <div class="relative w-16 h-16 rounded-xl flex items-center justify-center mb-3" style="background-color: {{ $folder->color }}20;">
                                    <span class="material-symbols-sharp text-4xl" style="color: {{ $folder->color }};">folder</span>
                                    @if($folder->isShared())
                                    <span class="absolute -bottom-1 -right-1 w-5 h-5 bg-green-500 rounded-full flex items-center justify-center" title="Shared">
                                        <span class="material-symbols-sharp text-white text-xs">group</span>
                                    </span>
                                    @elseif($folder->is_private && $folder->created_by === auth()->id())
                                    <span class="absolute -bottom-1 -right-1 w-5 h-5 bg-gray-400 rounded-full flex items-center justify-center" title="Private">
                                        <span class="material-symbols-sharp text-white text-xs">lock</span>
                                    </span>
                                    @endif
                                </div>
                                <p class="text-sm font-medium text-gray-900 truncate w-full" title="{{ $folder->name }}">{{ $folder->name }}</p>
                                <p class="text-xs text-gray-500 mt-1">
                                    {{ $folder->children->count() }} folders · {{ $folder->files->count() }} files
                                </p>
                            </div>
                        </div>
                        <!-- Folder Actions -->
                        <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity">
                            <div class="relative" x-data="{ open: false }">
                                <button @click="open = !open" class="p-1 text-gray-400 hover:text-gray-600 hover:bg-white rounded-lg shadow-sm">
                                    <span class="material-symbols-sharp text-sm">more_vert</span>
                                </button>
                                <div x-show="open" @click.away="open = false"
                                    class="absolute right-0 mt-1 w-40 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-10">
                                    @if($folder->created_by === auth()->id() || auth()->user()->hasRole(['administrator', 'developer']))
                                    <button wire:click="openShareModal({{ $folder->id }})" @click="open = false"
                                        class="w-full px-3 py-2 text-left text-sm text-gray-700 hover:bg-gray-50 flex items-center">
                                        <span class="material-symbols-sharp text-sm mr-2">share</span>
                                        Share
                                    </button>
                                    @if($folder->is_private)
                                    <button wire:click="makePublic({{ $folder->id }})" @click="open = false"
                                        class="w-full px-3 py-2 text-left text-sm text-gray-700 hover:bg-gray-50 flex items-center">
                                        <span class="material-symbols-sharp text-sm mr-2">public</span>
                                        Make Public
                                    </button>
                                    @else
                                    <button wire:click="makePrivate({{ $folder->id }})" @click="open = false"
                                        class="w-full px-3 py-2 text-left text-sm text-gray-700 hover:bg-gray-50 flex items-center">
                                        <span class="material-symbols-sharp text-sm mr-2">lock</span>
                                        Make Private
                                    </button>
                                    @endif
                                    <hr class="my-1">
                                    @endif
                                    <button wire:click="openRenameModal('folder', {{ $folder->id }})" @click="open = false"
                                        class="w-full px-3 py-2 text-left text-sm text-gray-700 hover:bg-gray-50 flex items-center">
                                        <span class="material-symbols-sharp text-sm mr-2">edit</span>
                                        Rename
                                    </button>
                                    <button wire:click="openMoveModal('folder', {{ $folder->id }})" @click="open = false"
                                        class="w-full px-3 py-2 text-left text-sm text-gray-700 hover:bg-gray-50 flex items-center">
                                        <span class="material-symbols-sharp text-sm mr-2">drive_file_move</span>
                                        Move
                                    </button>
                                    <hr class="my-1">
                                    <button wire:click="deleteFolder({{ $folder->id }})" wire:confirm="Are you sure you want to delete this folder?" @click="open = false"
                                        class="w-full px-3 py-2 text-left text-sm text-red-600 hover:bg-red-50 flex items-center">
                                        <span class="material-symbols-sharp text-sm mr-2">delete</span>
                                        Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach

                    <!-- Files -->
                    @foreach($files as $file)
                    <div class="group relative">
                        <a href="{{ route('document-library.show', $file) }}"
                            class="block p-4 rounded-xl border-2 border-transparent hover:border-indigo-200 hover:bg-indigo-50 transition-all">
                            <div class="flex flex-col items-center text-center">
                                <div class="w-16 h-16 rounded-xl flex items-center justify-center mb-3
                                    @if(Str::contains($file->mime_type, 'pdf')) bg-red-100
                                    @elseif(Str::contains($file->mime_type, 'word')) bg-blue-100
                                    @elseif(Str::contains($file->mime_type, 'excel') || Str::contains($file->mime_type, 'spreadsheet')) bg-green-100
                                    @elseif(Str::contains($file->mime_type, 'powerpoint') || Str::contains($file->mime_type, 'presentation')) bg-orange-100
                                    @elseif(Str::contains($file->mime_type, 'image')) bg-purple-100
                                    @else bg-gray-100
                                    @endif">
                                    <span class="material-symbols-sharp text-3xl
                                        @if(Str::contains($file->mime_type, 'pdf')) text-red-600
                                        @elseif(Str::contains($file->mime_type, 'word')) text-blue-600
                                        @elseif(Str::contains($file->mime_type, 'excel') || Str::contains($file->mime_type, 'spreadsheet')) text-green-600
                                        @elseif(Str::contains($file->mime_type, 'powerpoint') || Str::contains($file->mime_type, 'presentation')) text-orange-600
                                        @elseif(Str::contains($file->mime_type, 'image')) text-purple-600
                                        @else text-gray-600
                                        @endif">
                                        @if(Str::contains($file->mime_type, 'pdf'))
                                            picture_as_pdf
                                        @elseif(Str::contains($file->mime_type, 'word'))
                                            description
                                        @elseif(Str::contains($file->mime_type, 'excel') || Str::contains($file->mime_type, 'spreadsheet'))
                                            table_chart
                                        @elseif(Str::contains($file->mime_type, 'powerpoint') || Str::contains($file->mime_type, 'presentation'))
                                            slideshow
                                        @elseif(Str::contains($file->mime_type, 'image'))
                                            image
                                        @else
                                            insert_drive_file
                                        @endif
                                    </span>
                                </div>
                                <p class="text-sm font-medium text-gray-900 truncate w-full" title="{{ $file->title }}">{{ $file->title }}</p>
                                <p class="text-xs text-gray-500 mt-1">{{ $file->formatted_file_size }}</p>
                            </div>
                        </a>
                        <!-- File Actions -->
                        <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity">
                            <div class="relative" x-data="{ open: false }">
                                <button @click="open = !open" class="p-1 text-gray-400 hover:text-gray-600 hover:bg-white rounded-lg shadow-sm">
                                    <span class="material-symbols-sharp text-sm">more_vert</span>
                                </button>
                                <div x-show="open" @click.away="open = false"
                                    class="absolute right-0 mt-1 w-40 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-10">
                                    <a href="{{ route('document-library.show', $file) }}"
                                        class="w-full px-3 py-2 text-left text-sm text-gray-700 hover:bg-gray-50 flex items-center">
                                        <span class="material-symbols-sharp text-sm mr-2">visibility</span>
                                        View
                                    </a>
                                    <a href="{{ route('document-library.download', $file) }}"
                                        class="w-full px-3 py-2 text-left text-sm text-gray-700 hover:bg-gray-50 flex items-center">
                                        <span class="material-symbols-sharp text-sm mr-2">download</span>
                                        Download
                                    </a>
                                    <button wire:click="openRenameModal('file', {{ $file->id }})" @click="open = false"
                                        class="w-full px-3 py-2 text-left text-sm text-gray-700 hover:bg-gray-50 flex items-center">
                                        <span class="material-symbols-sharp text-sm mr-2">edit</span>
                                        Rename
                                    </button>
                                    <button wire:click="openMoveModal('file', {{ $file->id }})" @click="open = false"
                                        class="w-full px-3 py-2 text-left text-sm text-gray-700 hover:bg-gray-50 flex items-center">
                                        <span class="material-symbols-sharp text-sm mr-2">drive_file_move</span>
                                        Move
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <!-- List View -->
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Size</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Modified</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <!-- Folders -->
                            @foreach($folders as $folder)
                            <tr class="hover:bg-gray-50 cursor-pointer" wire:click="navigateToFolder({{ $folder->id }})">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="relative w-10 h-10 rounded-lg flex items-center justify-center mr-3" style="background-color: {{ $folder->color }}20;">
                                            <span class="material-symbols-sharp text-xl" style="color: {{ $folder->color }};">folder</span>
                                            @if($folder->isShared())
                                            <span class="absolute -bottom-1 -right-1 w-4 h-4 bg-green-500 rounded-full flex items-center justify-center" title="Shared">
                                                <span class="material-symbols-sharp text-white" style="font-size: 10px;">group</span>
                                            </span>
                                            @elseif($folder->is_private && $folder->created_by === auth()->id())
                                            <span class="absolute -bottom-1 -right-1 w-4 h-4 bg-gray-400 rounded-full flex items-center justify-center" title="Private">
                                                <span class="material-symbols-sharp text-white" style="font-size: 10px;">lock</span>
                                            </span>
                                            @endif
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">{{ $folder->name }}</p>
                                            <p class="text-xs text-gray-500">{{ $folder->children->count() }} folders · {{ $folder->files->count() }} files</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    Folder
                                    @if($folder->isShared())
                                    <span class="ml-2 inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">Shared</span>
                                    @elseif($folder->is_private)
                                    <span class="ml-2 inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-600">Private</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">—</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $folder->updated_at->format('M d, Y') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-right" wire:click.stop>
                                    <div class="relative inline-block" x-data="{ open: false }">
                                        <button @click="open = !open" class="p-1 text-gray-400 hover:text-gray-600 rounded">
                                            <span class="material-symbols-sharp text-sm">more_horiz</span>
                                        </button>
                                        <div x-show="open" @click.away="open = false"
                                            class="absolute right-0 mt-1 w-40 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-10">
                                            @if($folder->created_by === auth()->id() || auth()->user()->hasRole(['administrator', 'developer']))
                                            <button wire:click="openShareModal({{ $folder->id }})" @click="open = false"
                                                class="w-full px-3 py-2 text-left text-sm text-gray-700 hover:bg-gray-50 flex items-center">
                                                <span class="material-symbols-sharp text-sm mr-2">share</span>
                                                Share
                                            </button>
                                            @if($folder->is_private)
                                            <button wire:click="makePublic({{ $folder->id }})" @click="open = false"
                                                class="w-full px-3 py-2 text-left text-sm text-gray-700 hover:bg-gray-50 flex items-center">
                                                <span class="material-symbols-sharp text-sm mr-2">public</span>
                                                Make Public
                                            </button>
                                            @else
                                            <button wire:click="makePrivate({{ $folder->id }})" @click="open = false"
                                                class="w-full px-3 py-2 text-left text-sm text-gray-700 hover:bg-gray-50 flex items-center">
                                                <span class="material-symbols-sharp text-sm mr-2">lock</span>
                                                Make Private
                                            </button>
                                            @endif
                                            <hr class="my-1">
                                            @endif
                                            <button wire:click="openRenameModal('folder', {{ $folder->id }})" @click="open = false"
                                                class="w-full px-3 py-2 text-left text-sm text-gray-700 hover:bg-gray-50 flex items-center">
                                                <span class="material-symbols-sharp text-sm mr-2">edit</span>
                                                Rename
                                            </button>
                                            <button wire:click="openMoveModal('folder', {{ $folder->id }})" @click="open = false"
                                                class="w-full px-3 py-2 text-left text-sm text-gray-700 hover:bg-gray-50 flex items-center">
                                                <span class="material-symbols-sharp text-sm mr-2">drive_file_move</span>
                                                Move
                                            </button>
                                            <hr class="my-1">
                                            <button wire:click="deleteFolder({{ $folder->id }})" wire:confirm="Delete this folder?" @click="open = false"
                                                class="w-full px-3 py-2 text-left text-sm text-red-600 hover:bg-red-50 flex items-center">
                                                <span class="material-symbols-sharp text-sm mr-2">delete</span>
                                                Delete
                                            </button>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @endforeach

                            <!-- Files -->
                            @foreach($files as $file)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <a href="{{ route('document-library.show', $file) }}" class="flex items-center">
                                        <div class="w-10 h-10 rounded-lg flex items-center justify-center mr-3
                                            @if(Str::contains($file->mime_type, 'pdf')) bg-red-100
                                            @elseif(Str::contains($file->mime_type, 'word')) bg-blue-100
                                            @elseif(Str::contains($file->mime_type, 'excel') || Str::contains($file->mime_type, 'spreadsheet')) bg-green-100
                                            @else bg-gray-100
                                            @endif">
                                            <span class="material-symbols-sharp text-xl
                                                @if(Str::contains($file->mime_type, 'pdf')) text-red-600
                                                @elseif(Str::contains($file->mime_type, 'word')) text-blue-600
                                                @elseif(Str::contains($file->mime_type, 'excel') || Str::contains($file->mime_type, 'spreadsheet')) text-green-600
                                                @else text-gray-600
                                                @endif">
                                                @if(Str::contains($file->mime_type, 'pdf'))
                                                    picture_as_pdf
                                                @elseif(Str::contains($file->mime_type, 'word'))
                                                    description
                                                @elseif(Str::contains($file->mime_type, 'excel') || Str::contains($file->mime_type, 'spreadsheet'))
                                                    table_chart
                                                @else
                                                    insert_drive_file
                                                @endif
                                            </span>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900 hover:text-blue-600">{{ $file->title }}</p>
                                            @if($file->document_code)
                                            <p class="text-xs text-gray-500">{{ $file->document_code }}</p>
                                            @endif
                                        </div>
                                    </a>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ strtoupper(pathinfo($file->original_filename, PATHINFO_EXTENSION)) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $file->formatted_file_size }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $file->updated_at->format('M d, Y') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <div class="relative inline-block" x-data="{ open: false }">
                                        <button @click="open = !open" class="p-1 text-gray-400 hover:text-gray-600 rounded">
                                            <span class="material-symbols-sharp text-sm">more_horiz</span>
                                        </button>
                                        <div x-show="open" @click.away="open = false"
                                            class="absolute right-0 mt-1 w-40 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-10">
                                            <a href="{{ route('document-library.show', $file) }}"
                                                class="block px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 flex items-center">
                                                <span class="material-symbols-sharp text-sm mr-2">visibility</span>
                                                View
                                            </a>
                                            <a href="{{ route('document-library.download', $file) }}"
                                                class="block px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 flex items-center">
                                                <span class="material-symbols-sharp text-sm mr-2">download</span>
                                                Download
                                            </a>
                                            <button wire:click="openRenameModal('file', {{ $file->id }})" @click="open = false"
                                                class="w-full px-3 py-2 text-left text-sm text-gray-700 hover:bg-gray-50 flex items-center">
                                                <span class="material-symbols-sharp text-sm mr-2">edit</span>
                                                Rename
                                            </button>
                                            <button wire:click="openMoveModal('file', {{ $file->id }})" @click="open = false"
                                                class="w-full px-3 py-2 text-left text-sm text-gray-700 hover:bg-gray-50 flex items-center">
                                                <span class="material-symbols-sharp text-sm mr-2">drive_file_move</span>
                                                Move
                                            </button>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        @endif
    </div>

    <!-- Create Folder Modal -->
    @if($showCreateFolderModal)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="create-folder-modal" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeCreateFolderModal"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full">
                <form wire:submit="createFolder">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6">
                        <div class="flex items-center justify-between mb-6">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                    <span class="material-symbols-sharp text-blue-600">create_new_folder</span>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900">New Folder</h3>
                            </div>
                            <button type="button" wire:click="closeCreateFolderModal" class="text-gray-400 hover:text-gray-600">
                                <span class="material-symbols-sharp">close</span>
                            </button>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Folder Name <span class="text-red-500">*</span></label>
                                <input type="text" wire:model="newFolderName"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    placeholder="Enter folder name" autofocus>
                                @error('newFolderName') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                <textarea wire:model="newFolderDescription" rows="2"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    placeholder="Optional description"></textarea>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Color</label>
                                <div class="flex items-center gap-2">
                                    <input type="color" wire:model="newFolderColor"
                                        class="w-10 h-10 rounded border border-gray-300 cursor-pointer">
                                    <input type="text" wire:model="newFolderColor"
                                        class="w-24 rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                                        placeholder="#3B82F6">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit"
                            class="w-full sm:w-auto inline-flex justify-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 sm:ml-3">
                            Create Folder
                        </button>
                        <button type="button" wire:click="closeCreateFolderModal"
                            class="mt-3 sm:mt-0 w-full sm:w-auto inline-flex justify-center px-4 py-2 border border-gray-300 rounded-lg text-gray-700 bg-white hover:bg-gray-50">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    <!-- Move Modal -->
    @if($showMoveModal)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="move-modal" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeMoveModal"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                                <span class="material-symbols-sharp text-indigo-600">drive_file_move</span>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900">Move {{ $moveItemType === 'folder' ? 'Folder' : 'File' }}</h3>
                        </div>
                        <button type="button" wire:click="closeMoveModal" class="text-gray-400 hover:text-gray-600">
                            <span class="material-symbols-sharp">close</span>
                        </button>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Select Destination</label>
                            <select wire:model="moveTargetFolderId"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">My Drive (Root)</option>
                                @foreach($allFolders as $folder)
                                <option value="{{ $folder['id'] }}">
                                    {{ str_repeat('— ', $folder['depth']) }}{{ $folder['name'] }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" wire:click="moveItem"
                        class="w-full sm:w-auto inline-flex justify-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 sm:ml-3">
                        Move Here
                    </button>
                    <button type="button" wire:click="closeMoveModal"
                        class="mt-3 sm:mt-0 w-full sm:w-auto inline-flex justify-center px-4 py-2 border border-gray-300 rounded-lg text-gray-700 bg-white hover:bg-gray-50">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Rename Modal -->
    @if($showRenameModal)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="rename-modal" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeRenameModal"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full">
                <form wire:submit="renameItem">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6">
                        <div class="flex items-center justify-between mb-6">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center">
                                    <span class="material-symbols-sharp text-yellow-600">edit</span>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900">Rename {{ $renameItemType === 'folder' ? 'Folder' : 'File' }}</h3>
                            </div>
                            <button type="button" wire:click="closeRenameModal" class="text-gray-400 hover:text-gray-600">
                                <span class="material-symbols-sharp">close</span>
                            </button>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">New Name <span class="text-red-500">*</span></label>
                            <input type="text" wire:model="renameName"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500"
                                autofocus>
                            @error('renameName') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit"
                            class="w-full sm:w-auto inline-flex justify-center px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 sm:ml-3">
                            Rename
                        </button>
                        <button type="button" wire:click="closeRenameModal"
                            class="mt-3 sm:mt-0 w-full sm:w-auto inline-flex justify-center px-4 py-2 border border-gray-300 rounded-lg text-gray-700 bg-white hover:bg-gray-50">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    <!-- Share Folder Modal -->
    @if($showShareModal && $shareFolder)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="share-modal" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeShareModal"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                                <span class="material-symbols-sharp text-green-600">share</span>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Share Folder</h3>
                                <p class="text-sm text-gray-500">{{ $shareFolder->name }}</p>
                            </div>
                        </div>
                        <button type="button" wire:click="closeShareModal" class="text-gray-400 hover:text-gray-600">
                            <span class="material-symbols-sharp">close</span>
                        </button>
                    </div>

                    <div class="space-y-6">
                        <!-- Share with specific users -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Share with Users</label>

                            <!-- User search -->
                            <div class="relative mb-2">
                                <input type="text" wire:model.live.debounce.300ms="shareUserSearch"
                                    class="w-full pl-10 pr-4 py-2 text-sm border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500"
                                    placeholder="Search users by name or email...">
                                <span class="material-symbols-sharp absolute left-3 top-2.5 text-gray-400 text-sm">search</span>
                            </div>

                            <!-- User search results -->
                            @if($shareUserSearch && $availableUsers->count() > 0)
                            <div class="border border-gray-200 rounded-lg max-h-40 overflow-y-auto mb-2">
                                @foreach($availableUsers as $user)
                                <button wire:click="addShareUser({{ $user->id }})" type="button"
                                    class="w-full px-3 py-2 text-left text-sm hover:bg-gray-50 flex items-center justify-between {{ in_array($user->id, $shareWithUsers) ? 'bg-green-50' : '' }}">
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $user->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $user->email }} • {{ $user->department }}</p>
                                    </div>
                                    @if(in_array($user->id, $shareWithUsers))
                                    <span class="material-symbols-sharp text-green-600 text-sm">check_circle</span>
                                    @else
                                    <span class="material-symbols-sharp text-gray-400 text-sm">add_circle</span>
                                    @endif
                                </button>
                                @endforeach
                            </div>
                            @endif

                            <!-- Selected users -->
                            @if(count($shareWithUsers) > 0)
                            <div class="flex flex-wrap gap-2">
                                @foreach($sharedUsersInfo as $user)
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    {{ $user->name }}
                                    <button wire:click="removeShareUser({{ $user->id }})" class="ml-1 text-green-600 hover:text-green-800">
                                        <span class="material-symbols-sharp text-xs">close</span>
                                    </button>
                                </span>
                                @endforeach
                            </div>
                            @else
                            <p class="text-xs text-gray-500">No users selected</p>
                            @endif
                        </div>

                        <!-- Share with departments -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Share with Departments</label>
                            <div class="grid grid-cols-2 gap-2 max-h-40 overflow-y-auto">
                                @foreach($departments as $department)
                                <label class="flex items-center p-2 rounded-lg border {{ in_array($department, $shareWithDepartments) ? 'border-green-300 bg-green-50' : 'border-gray-200 hover:bg-gray-50' }} cursor-pointer">
                                    <input type="checkbox" wire:click="toggleShareDepartment('{{ $department }}')"
                                        {{ in_array($department, $shareWithDepartments) ? 'checked' : '' }}
                                        class="h-4 w-4 text-green-600 border-gray-300 rounded focus:ring-green-500">
                                    <span class="ml-2 text-sm text-gray-700">{{ $department }}</span>
                                </label>
                                @endforeach
                            </div>
                        </div>

                        <!-- Department head only option -->
                        @if(count($shareWithDepartments) > 0)
                        <div>
                            <label class="flex items-center p-3 rounded-lg border border-gray-200 cursor-pointer hover:bg-gray-50">
                                <input type="checkbox" wire:model="shareWithDepartmentHead"
                                    class="h-4 w-4 text-green-600 border-gray-300 rounded focus:ring-green-500">
                                <div class="ml-3">
                                    <span class="text-sm font-medium text-gray-700">Department Head Only</span>
                                    <p class="text-xs text-gray-500">Only share with the head of selected departments, not all members</p>
                                </div>
                            </label>
                        </div>
                        @endif

                        <!-- Current sharing status -->
                        <div class="bg-gray-50 rounded-lg p-3">
                            <p class="text-xs font-medium text-gray-600 mb-1">Current Status</p>
                            <div class="flex items-center space-x-2">
                                @if($shareFolder->is_private)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-200 text-gray-700">
                                    <span class="material-symbols-sharp text-xs mr-1">lock</span>
                                    Private
                                </span>
                                @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-700">
                                    <span class="material-symbols-sharp text-xs mr-1">public</span>
                                    Public to Department
                                </span>
                                @endif
                                @if($shareFolder->isShared())
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-700">
                                    <span class="material-symbols-sharp text-xs mr-1">group</span>
                                    Shared
                                </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" wire:click="saveSharing"
                        class="w-full sm:w-auto inline-flex justify-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 sm:ml-3">
                        Save Sharing Settings
                    </button>
                    <button type="button" wire:click="closeShareModal"
                        class="mt-3 sm:mt-0 w-full sm:w-auto inline-flex justify-center px-4 py-2 border border-gray-300 rounded-lg text-gray-700 bg-white hover:bg-gray-50">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
