<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">File Categories</h1>
            <p class="mt-2 text-sm text-gray-600">Organize your documents by creating and managing categories</p>
        </div>
        <div class="mt-4 sm:mt-0 flex space-x-3">
            <a href="{{ route('master-file.dashboard') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                <span class="material-symbols-sharp text-sm mr-2">arrow_back</span>
                Back to Dashboard
            </a>
            <button wire:click="openModal" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white hover:bg-blue-700 transition-colors">
                <span class="material-symbols-sharp text-sm mr-2">add</span>
                Add Category
            </button>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-3 sm:space-y-0">
            <div class="flex-1 max-w-lg">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <span class="material-symbols-sharp text-gray-400 text-sm">search</span>
                    </div>
                    <input type="text"
                           wire:model.live.debounce.300ms="search"
                           class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Search categories...">
                </div>
            </div>
            <div class="flex items-center space-x-2">
                <span class="text-sm text-gray-500">{{ $categories->total() }} categories</span>
            </div>
        </div>
    </div>

    <!-- Categories Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($categories as $category)
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow">
            <!-- Category Header -->
            <div class="p-4 border-b border-gray-200" style="background: linear-gradient(135deg, {{ $category->color }}20 0%, {{ $category->color }}10 100%);">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background-color: {{ $category->color }}20;">
                            <span class="material-symbols-sharp text-lg" style="color: {{ $category->color }};">{{ $category->icon }}</span>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">{{ $category->name }}</h3>
                            <p class="text-sm text-gray-500">{{ $category->files_count ?? 0 }} files</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-1">
                        <button wire:click="edit({{ $category->id }})"
                                class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors">
                            <span class="material-symbols-sharp text-sm">edit</span>
                        </button>
                        <button wire:click="delete({{ $category->id }})"
                                wire:confirm="Are you sure you want to delete this category?"
                                class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                            <span class="material-symbols-sharp text-sm">delete</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Category Content -->
            <div class="p-4 space-y-3">
                @if($category->description)
                <p class="text-sm text-gray-600">{{ Str::limit($category->description, 100) }}</p>
                @endif

                <!-- Category Details -->
                <div class="space-y-2 text-xs">
                    <div class="flex items-center justify-between">
                        <span class="text-gray-500">Department:</span>
                        <span class="font-medium text-gray-900">{{ $category->department ?: 'All' }}</span>
                    </div>

                    @if($category->requires_approval)
                    <div class="flex items-center space-x-1">
                        <span class="material-symbols-sharp text-yellow-600 text-sm">approval</span>
                        <span class="text-yellow-700 font-medium">Requires Approval</span>
                    </div>
                    @endif

                    @if($category->allowed_departments && count($category->allowed_departments) > 0)
                    <div>
                        <span class="text-gray-500">Visible to:</span>
                        <div class="flex flex-wrap gap-1 mt-1">
                            @foreach($category->allowed_departments as $dept)
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                {{ $dept }}
                            </span>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Category Stats -->
                <div class="pt-3 border-t border-gray-100">
                    <div class="flex items-center justify-between text-xs text-gray-500">
                        <span>Created {{ $category->created_at->diffForHumans() }}</span>
                        <div class="flex items-center space-x-2">
                            @if($category->is_active)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Active
                            </span>
                            @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                Inactive
                            </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full">
            <div class="text-center py-12">
                <span class="material-symbols-sharp text-6xl text-gray-300">category</span>
                <h3 class="mt-4 text-lg font-medium text-gray-900">No categories found</h3>
                <p class="mt-2 text-sm text-gray-500">
                    @if($search)
                        No categories match your search criteria.
                    @else
                        Get started by creating your first category.
                    @endif
                </p>
                @if(!$search)
                <div class="mt-6">
                    <button wire:click="openModal" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white hover:bg-blue-700">
                        <span class="material-symbols-sharp text-sm mr-2">add</span>
                        Add Category
                    </button>
                </div>
                @endif
            </div>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($categories->hasPages())
    <div class="mt-6">
        {{ $categories->links() }}
    </div>
    @endif

    <!-- Modal -->
    @if($showModal)
    <div class="fixed inset-0 z-50 overflow-y-auto" x-data="{ show: @entangle('showModal') }" x-show="show" x-transition>
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="$wire.closeModal()"></div>

            <!-- Modal content -->
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form wire:submit="save">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                                <span class="material-symbols-sharp text-blue-600">category</span>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                                    {{ $editingId ? 'Edit Category' : 'Add New Category' }}
                                </h3>

                                <div class="space-y-4">
                                    <!-- Category Name -->
                                    <div>
                                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                                            Category Name <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text"
                                               id="name"
                                               wire:model="name"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                               placeholder="Enter category name">
                                        @error('name')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- Description -->
                                    <div>
                                        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                                            Description
                                        </label>
                                        <textarea id="description"
                                                  wire:model="description"
                                                  rows="3"
                                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                                  placeholder="Brief description of this category"></textarea>
                                        @error('description')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- Color and Icon -->
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label for="color" class="block text-sm font-medium text-gray-700 mb-1">
                                                Color
                                            </label>
                                            <div class="flex items-center space-x-2">
                                                <input type="color"
                                                       id="color"
                                                       wire:model="color"
                                                       class="h-10 w-16 border border-gray-300 rounded-lg">
                                                <input type="text"
                                                       wire:model="color"
                                                       class="flex-1 px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                                       placeholder="#3B82F6">
                                            </div>
                                            @error('color')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div>
                                            <label for="icon" class="block text-sm font-medium text-gray-700 mb-1">
                                                Icon
                                            </label>
                                            <select id="icon"
                                                    wire:model="icon"
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                                <option value="folder">📁 Folder</option>
                                                <option value="description">📄 Document</option>
                                                <option value="policy">📋 Policy</option>
                                                <option value="book">📖 Manual</option>
                                                <option value="assignment">📝 Assignment</option>
                                                <option value="security">🔒 Security</option>
                                                <option value="settings">⚙️ Settings</option>
                                                <option value="analytics">📊 Analytics</option>
                                            </select>
                                            @error('icon')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Department -->
                                    <div>
                                        <label for="department" class="block text-sm font-medium text-gray-700 mb-1">
                                            Department
                                        </label>
                                        <select id="department"
                                                wire:model="department"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                            <option value="">All Departments</option>
                                            <option value="ITSS">ITSS</option>
                                            <option value="PAMO">PAMO</option>
                                            <option value="BFO">BFO</option>
                                            <option value="HR">HR</option>
                                            <option value="ACCOUNTING">ACCOUNTING</option>
                                            <option value="ADMIN">ADMIN</option>
                                        </select>
                                        @error('department')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- Allowed Departments -->
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
                                                           wire:model="allowed_departments"
                                                           value="{{ $dept }}"
                                                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                                    <span class="ml-2 text-sm text-gray-700">{{ $dept }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                        @error('allowed_departments')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- Requires Approval -->
                                    <div class="flex items-center">
                                        <input type="checkbox"
                                               id="requires_approval"
                                               wire:model="requires_approval"
                                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                        <label for="requires_approval" class="ml-2 text-sm text-gray-700">
                                            Require approval for files in this category
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit"
                                wire:loading.attr="disabled"
                                class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50">
                            <span wire:loading.remove>
                                {{ $editingId ? 'Update Category' : 'Create Category' }}
                            </span>
                            <span wire:loading class="flex items-center">
                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Saving...
                            </span>
                        </button>
                        <button type="button"
                                wire:click="closeModal"
                                class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>
