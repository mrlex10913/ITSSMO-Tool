<div class="container mx-auto" x-data="{ showCreate: $wire.entangle('showCreate'), showEdit: $wire.entangle('showEdit') }">
    <!-- Header -->
    <div class="mb-4 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight">Departments</h1>
            <div class="text-sm text-gray-500 dark:text-gray-400">
                <x-nav-link href="{{ route('controlPanel.admin') }}" class="hover:text-blue-600">Control Panel</x-nav-link>
                <span>/</span>
                <span class="text-blue-600 font-medium">Directory</span>
            </div>
        </div>
    <button class="btn btn-primary" @click="showCreate = true; $wire.openCreate()">Create Department</button>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm ring-1 ring-gray-200 dark:ring-gray-700 p-4 mb-4">
        <div class="flex items-center gap-3">
            <input type="text" placeholder="Search..." class="w-full md:w-80 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" wire:model.live.debounce.500ms="search" />
            <select class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" wire:model.live="perPage">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
            </select>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm ring-1 ring-gray-200 dark:ring-gray-700 overflow-hidden">
        <table class="min-w-full text-sm divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-900/40">
                <tr>
                    <th class="text-left px-4 py-2">Name</th>
                    <th class="text-left px-4 py-2">Slug</th>
                    <th class="text-left px-4 py-2">Order</th>
                    <th class="text-left px-4 py-2">Active</th>
                    <th class="text-left px-4 py-2">Guest Visible</th>
                    <th class="text-right px-4 py-2">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @foreach($this->departments as $d)
                <tr>
                    <td class="px-4 py-2 text-gray-800 dark:text-gray-100">{{ $d->name }}</td>
                    <td class="px-4 py-2 text-gray-700 dark:text-gray-300">{{ $d->slug }}</td>
                    <td class="px-4 py-2 text-gray-700 dark:text-gray-300">{{ $d->sort_order }}</td>
                    <td class="px-4 py-2">
                        @if($d->is_active)
                            <span class="px-2 py-0.5 text-xs bg-green-100 text-green-700 rounded">Yes</span>
                        @else
                            <span class="px-2 py-0.5 text-xs bg-gray-100 text-gray-600 rounded">No</span>
                        @endif
                    </td>
                    <td class="px-4 py-2">
                        @if($d->is_guest_visible)
                            <span class="px-2 py-0.5 text-xs bg-blue-100 text-blue-700 rounded">Yes</span>
                        @else
                            <span class="px-2 py-0.5 text-xs bg-gray-100 text-gray-600 rounded">No</span>
                        @endif
                    </td>
                    <td class="px-4 py-2 text-right">
                        <button class="btn btn-ghost btn-sm" @click="$wire.openEdit({{ $d->id }}); showEdit = true">Edit</button>
                        <button class="btn btn-danger btn-sm" wire:click="delete({{ $d->id }})">Delete</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="px-4 py-3">{{ $this->departments->links() }}</div>
    </div>

    <!-- Create Modal -->
    <div x-show="showCreate" x-cloak class="fixed inset-0 bg-black/40 flex items-center justify-center p-4">
        <div class="bg-white dark:bg-gray-800 rounded shadow-lg w-full max-w-md ring-1 ring-gray-200 dark:ring-gray-700">
            <div class="border-b px-4 py-2 flex items-center justify-between">
                <h2 class="font-semibold">Create Department</h2>
                <button class="text-gray-500" @click="showCreate=false">✕</button>
            </div>
            <div class="p-4 space-y-3">
                <div>
                    <label class="block text-sm mb-1">Name</label>
                    <input type="text" class="w-full border rounded px-2 py-1 dark:bg-gray-700 dark:border-gray-600 dark:text-white" wire:model.defer="name" />
                    @error('name') <div class="text-xs text-red-600">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="block text-sm mb-1">Slug</label>
                    <input type="text" class="w-full border rounded px-2 py-1 dark:bg-gray-700 dark:border-gray-600 dark:text-white" wire:model.defer="slug" />
                    @error('slug') <div class="text-xs text-red-600">{{ $message }}</div> @enderror
                </div>
                <div class="grid grid-cols-2 gap-3 items-end">
                    <div>
                        <label class="block text-sm mb-1">Order</label>
                        <input type="number" class="w-full border rounded px-2 py-1 dark:bg-gray-700 dark:border-gray-600 dark:text-white" min="0" wire:model.defer="sort_order" />
                        @error('sort_order') <div class="text-xs text-red-600">{{ $message }}</div> @enderror
                    </div>
                    <label class="flex items-center gap-2">
                        <input type="checkbox" class="h-4 w-4" wire:model.defer="is_active" />
                        <span>Active</span>
                    </label>
                    <label class="flex items-center gap-2">
                        <input type="checkbox" class="h-4 w-4" wire:model.defer="is_guest_visible" />
                        <span>Guest Visible</span>
                    </label>
                </div>
            </div>
            <div class="border-t px-4 py-3 flex items-center justify-end gap-2">
                <button class="btn btn-outline" @click="showCreate=false">Cancel</button>
                <button class="btn btn-primary" wire:click="create">Save</button>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div x-show="showEdit" x-cloak class="fixed inset-0 bg-black/40 flex items-center justify-center p-4">
        <div class="bg-white dark:bg-gray-800 rounded shadow-lg w-full max-w-md ring-1 ring-gray-200 dark:ring-gray-700">
            <div class="border-b px-4 py-2 flex items-center justify-between">
                <h2 class="font-semibold">Edit Department</h2>
                <button class="text-gray-500" @click="showEdit=false">✕</button>
            </div>
            <div class="p-4 space-y-3">
                <div>
                    <label class="block text-sm mb-1">Name</label>
                    <input type="text" class="w-full border rounded px-2 py-1 dark:bg-gray-700 dark:border-gray-600 dark:text-white" wire:model.defer="name" />
                    @error('name') <div class="text-xs text-red-600">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="block text-sm mb-1">Slug</label>
                    <input type="text" class="w-full border rounded px-2 py-1 dark:bg-gray-700 dark:border-gray-600 dark:text-white" wire:model.defer="slug" />
                    @error('slug') <div class="text-xs text-red-600">{{ $message }}</div> @enderror
                </div>
                <div class="grid grid-cols-2 gap-3 items-end">
                    <div>
                        <label class="block text-sm mb-1">Order</label>
                        <input type="number" class="w-full border rounded px-2 py-1 dark:bg-gray-700 dark:border-gray-600 dark:text-white" min="0" wire:model.defer="sort_order" />
                        @error('sort_order') <div class="text-xs text-red-600">{{ $message }}</div> @enderror
                    </div>
                    <label class="flex items-center gap-2">
                        <input type="checkbox" class="h-4 w-4" wire:model.defer="is_active" />
                        <span>Active</span>
                    </label>
                    <label class="flex items-center gap-2">
                        <input type="checkbox" class="h-4 w-4" wire:model.defer="is_guest_visible" />
                        <span>Guest Visible</span>
                    </label>
                </div>
            </div>
            <div class="border-t px-4 py-3 flex items-center justify-end gap-2">
                <button class="btn btn-outline" @click="showEdit=false">Cancel</button>
                <button class="btn btn-primary" wire:click="update">Update</button>
            </div>
        </div>
    </div>
</div>
