<div>
    <div class="sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-4">
            <h1 class="text-2xl font-semibold text-gray-900">Ticket Macros</h1>
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
                        <div class="font-medium">{{ $it->name }}</div>
                        <div class="text-xs text-gray-500">{{ json_encode($it->actions) }}</div>
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
        <x-slot name="title">{{ $editingId ? 'Edit' : 'New' }} Macro</x-slot>
        <x-slot name="content">
            <div class="space-y-4">
                <div>
                    <x-label value="Name" />
                    <x-input type="text" class="mt-1 w-full" wire:model.defer="name" />
                    @error('name') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <div>
                        <x-label value="Status" />
                        <select class="w-full border-gray-300 rounded-md" wire:model.defer="actions.status">
                            <option value="">—</option>
                            <option value="open">Open</option>
                            <option value="in_progress">In Progress</option>
                            <option value="resolved">Resolved</option>
                            <option value="closed">Closed</option>
                        </select>
                    </div>
                    <div>
                        <x-label value="Type" />
                        <select class="w-full border-gray-300 rounded-md" wire:model.defer="actions.type">
                            <option value="">—</option>
                            <option value="incident">Incident</option>
                            <option value="request">Request</option>
                        </select>
                    </div>
                    <div>
                        <x-label value="Assignee ID" />
                        <x-input type="number" class="w-full" wire:model.defer="actions.assignee_id" />
                    </div>
                </div>
                <div>
                    <x-label value="Reply (optional)" />
                    <textarea rows="4" class="mt-1 w-full border-gray-300 rounded-md" wire:model.defer="actions.reply"></textarea>
                </div>
                <label class="inline-flex items-center text-sm text-gray-700">
                    <input type="checkbox" class="rounded border-gray-300 mr-2" wire:model.defer="is_active">
                    Active
                </label>
            </div>
        </x-slot>
        <x-slot name="footer">
            <x-secondary-button wire:click="$set('showModal', false)" class="mr-2">Cancel</x-secondary-button>
            <x-button wire:click="save">Save</x-button>
        </x-slot>
    </x-dialog-modal>
</div>
