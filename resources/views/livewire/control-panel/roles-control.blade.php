<div class="container mx-auto">
    <!-- Header -->
    <div class="mb-4 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight">Roles</h1>
            <div class="text-sm text-gray-500 dark:text-gray-400">
                <x-nav-link href="{{ route('controlPanel.admin') }}" class="hover:text-blue-600">Control Panel</x-nav-link>
                <span>/</span>
                <span class="text-blue-600 font-medium">Management</span>
            </div>
        </div>
    <button wire:click="openCreateModal" class="btn btn-primary">New Role</button>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm ring-1 ring-gray-200 dark:ring-gray-700 p-4 mb-4">
        <div class="flex items-center gap-3">
            <input type="text" wire:model.live="search" placeholder="Search roles..." class="w-full md:w-80 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" />
            <select wire:model.live="perPage" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
            </select>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm ring-1 ring-gray-200 dark:ring-gray-700 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-900/40">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Slug</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Default</th>
                    <th class="px-6 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($roles as $role)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $role->name }}</div>
                            @if($role->description)
                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $role->description }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">{{ $role->slug }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($role->is_default)
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">Default</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <button wire:click="openEditModal({{ $role->id }})" class="btn btn-ghost btn-sm">Edit</button>
                            <button wire:click="confirmDelete({{ $role->id }})" class="btn btn-danger btn-sm">Delete</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-6 text-center text-gray-500 dark:text-gray-400">No roles found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-4 py-3">{{ $roles->onEachSide(1)->links() }}</div>
    </div>

    @if($showCreateModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg w-full max-w-lg p-6 ring-1 ring-gray-200 dark:ring-gray-700">
                <h2 class="text-lg font-semibold mb-4">Create Role</h2>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Name</label>
                        <input type="text" wire:model="name" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white" />
                        @error('name')<div class="text-sm text-red-600">{{ $message }}</div>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Slug</label>
                        <input type="text" wire:model="slug" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white" />
                        @error('slug')<div class="text-sm text-red-600">{{ $message }}</div>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Description</label>
                        <textarea wire:model="description" rows="3" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white"></textarea>
                        @error('description')<div class="text-sm text-red-600">{{ $message }}</div>@enderror
                    </div>
                    <label class="inline-flex items-center gap-2">
                        <input type="checkbox" wire:model="is_default" class="rounded" />
                        <span class="text-sm">Make default for new users</span>
                    </label>
                </div>
                <div class="mt-6 flex justify-end gap-3">
                    <button wire:click="$set('showCreateModal', false)" class="btn btn-outline">Cancel</button>
                    <button wire:click="createRole" class="btn btn-primary">Create</button>
                </div>
            </div>
        </div>
    @endif

    @if($showEditModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg w-full max-w-lg p-6 ring-1 ring-gray-200 dark:ring-gray-700">
                <h2 class="text-lg font-semibold mb-4">Edit Role</h2>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Name</label>
                        <input type="text" wire:model="name" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white" />
                        @error('name')<div class="text-sm text-red-600">{{ $message }}</div>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Slug</label>
                        <input type="text" wire:model="slug" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white" />
                        @error('slug')<div class="text-sm text-red-600">{{ $message }}</div>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Description</label>
                        <textarea wire:model="description" rows="3" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white"></textarea>
                        @error('description')<div class="text-sm text-red-600">{{ $message }}</div>@enderror
                    </div>
                    <label class="inline-flex items-center gap-2">
                        <input type="checkbox" wire:model="is_default" class="rounded" />
                        <span class="text-sm">Make default for new users</span>
                    </label>
                </div>
                <div class="mt-6 flex justify-end gap-3">
                    <button wire:click="$set('showEditModal', false)" class="btn btn-outline">Cancel</button>
                    <button wire:click="updateRole" class="btn btn-primary">Save</button>
                </div>
            </div>
        </div>
    @endif

    @if($showDeleteModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg w-full max-w-md p-6 ring-1 ring-gray-200 dark:ring-gray-700">
                <h2 class="text-lg font-semibold mb-2">Delete Role</h2>
                <p class="text-sm text-gray-600">Are you sure you want to delete this role? This action cannot be undone.</p>
                <div class="mt-6 flex justify-end gap-3">
                    <button wire:click="$set('showDeleteModal', false)" class="btn btn-outline">Cancel</button>
                    <button wire:click="deleteRoleConfirmed" class="btn btn-danger">Delete</button>
                </div>
            </div>
        </div>
    @endif
</div>
