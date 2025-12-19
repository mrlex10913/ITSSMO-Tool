<div>
    <div class="sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-4">
            <h1 class="text-2xl font-semibold text-gray-900">Canned Responses</h1>
            <x-button wire:click="create">New</x-button>
        </div>

        @if (session('success'))
            <div class="mb-4 rounded bg-green-50 text-green-800 px-4 py-3">{{ session('success') }}</div>
        @endif

        <div class="bg-white rounded-lg shadow p-4 mb-4">
            <input type="text" class="w-full border-gray-300 rounded-md" placeholder="Search..." wire:model.live="search">
        </div>

        <div class="bg-white rounded-lg shadow divide-y">
            @forelse($items as $it)
                <div class="p-4 flex items-center justify-between">
                    <div>
                        <div class="font-medium">{{ $it->title }}</div>
                        <div class="text-sm text-gray-500 line-clamp-1">{{ $it->body }}</div>
                    </div>
                    <div class="flex gap-2">
                        <x-secondary-button wire:click="edit({{ $it->id }})">Edit</x-secondary-button>
                        <x-danger-button wire:click="delete({{ $it->id }})">Delete</x-danger-button>
                    </div>
                </div>
            @empty
                <div class="p-6 text-center text-gray-500">No items</div>
            @endforelse
        </div>
        <div class="mt-4">{{ $items->links() }}</div>
    </div>

    <x-dialog-modal wire:model="showModal">
        <x-slot name="title">{{ $editingId ? 'Edit' : 'New' }} Canned Response</x-slot>
        <x-slot name="content">
            <div class="space-y-4">
                <div>
                    <x-label value="Title" />
                    <x-input type="text" class="mt-1 w-full" wire:model.defer="title" />
                    @error('title') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <x-label value="Body" />
                    <textarea rows="6" class="mt-1 w-full border-gray-300 rounded-md" wire:model.defer="body"></textarea>
                    @error('body') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <label class="inline-flex items-center text-sm text-gray-700">
                    <input type="checkbox" class="rounded border-gray-300 mr-2" wire:model.defer="is_global">
                    Global (visible to all agents)
                </label>
            </div>
        </x-slot>
        <x-slot name="footer">
            <x-secondary-button wire:click="$set('showModal', false)" class="mr-2">Cancel</x-secondary-button>
            <x-button wire:click="save">Save</x-button>
        </x-slot>
    </x-dialog-modal>
</div>
