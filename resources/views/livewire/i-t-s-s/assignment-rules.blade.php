<div>
    <div class="sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-4">
            <h1 class="text-2xl font-semibold text-gray-900">Assignment Rules</h1>
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
                        <div class="text-xs text-gray-500">{{ json_encode($it->criteria) }}</div>
                        <div class="text-xs text-gray-500">Assignee: {{ $it->assignee->name ?? '—' }}</div>
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
        <x-slot name="title">{{ $editingId ? 'Edit' : 'New' }} Rule</x-slot>
        <x-slot name="content">
            <div class="space-y-4">
                <div>
                    <x-label value="Name" />
                    <x-input type="text" class="mt-1 w-full" wire:model.defer="name" />
                    @error('name') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div>
                        <x-label value="Type" />
                        <select class="w-full border-gray-300 rounded-md" wire:model.defer="criteria.type">
                            <option value="">—</option>
                            <option value="incident">Incident</option>
                            <option value="request">Request</option>
                        </select>
                    </div>
                    <div>
                        <x-label value="Priority" />
                        <select class="w-full border-gray-300 rounded-md" wire:model.defer="criteria.priority">
                            <option value="">—</option>
                            <option value="low">Low</option>
                            <option value="medium">Medium</option>
                            <option value="high">High</option>
                            <option value="urgent">Urgent</option>
                        </select>
                    </div>
                    <div>
                        <x-label value="Category" />
                        <select class="w-full border-gray-300 rounded-md" wire:model.defer="criteria.category_id">
                            <option value="">—</option>
                            @foreach($categories as $c)
                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-label value="Keywords (comma-separated)" />
                        <x-input type="text" class="w-full" wire:model.defer="criteria.keywords" />
                    </div>
                </div>
                <div>
                    <x-label value="Assignee" />
                    <select class="w-full border-gray-300 rounded-md" wire:model.defer="assignee_id">
                        <option value="">— select —</option>
                        @foreach($agents as $a)
                            <option value="{{ $a->id }}">{{ $a->name }}</option>
                        @endforeach
                    </select>
                    @error('assignee_id') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
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
