<div>
    <div class="sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-4">
            <h1 class="text-2xl font-semibold text-gray-900">SLA Escalations</h1>
            <x-button wire:click="create">New</x-button>
        </div>

        @if (session('success'))
            <div class="mb-4 rounded bg-green-50 text-green-800 px-4 py-3">{{ session('success') }}</div>
        @endif

        <div class="bg-white rounded-lg shadow p-4 mb-4 grid grid-cols-1 md:grid-cols-3 gap-3">
            <div class="md:col-span-2">
                <label class="text-xs text-gray-600">SLA Policy</label>
                <select class="w-full border-gray-300 rounded-md" wire:model.live="policyId">
                    @foreach($policies as $p)
                        <option value="{{ $p->id }}">{{ $p->name }} ({{ ucfirst($p->type) }} • {{ ucfirst($p->priority) }})</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow divide-y">
            @forelse($items as $it)
                <div class="p-4 flex items-center justify-between">
                    <div>
                        <div class="font-medium">{{ $it->threshold_mins_before_breach }} minutes before breach</div>
                        <div class="text-xs text-gray-500">Escalate to: {{ $it->escalateTo->name ?? '—' }}</div>
                        <div class="text-xs text-gray-500">Status: {{ $it->is_active ? 'Active' : 'Inactive' }}</div>
                    </div>
                    <div class="flex gap-2">
                        <x-secondary-button wire:click="edit({{ $it->id }})">Edit</x-secondary-button>
                        <x-danger-button wire:click="delete({{ $it->id }})">Delete</x-danger-button>
                    </div>
                </div>
            @empty
                <div class="p-6 text-center text-gray-500">No escalation rules for this policy</div>
            @endforelse
        </div>
        <div class="mt-4">{{ $items->links() }}</div>
    </div>

    <x-dialog-modal wire:model="showModal">
        <x-slot name="title">{{ $editingId ? 'Edit' : 'New' }} Escalation</x-slot>
        <x-slot name="content">
            <div class="space-y-4">
                <div>
                    <x-label value="Threshold (minutes before breach)" />
                    <x-input type="number" min="1" class="mt-1 w-full" wire:model.defer="threshold_mins_before_breach" />
                    @error('threshold_mins_before_breach') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <x-label value="Escalate To" />
                    <select class="w-full border-gray-300 rounded-md" wire:model.defer="escalate_to_user_id">
                        <option value="">— select —</option>
                        @foreach($agents as $a)
                            <option value="{{ $a->id }}">{{ $a->name }}</option>
                        @endforeach
                    </select>
                    @error('escalate_to_user_id') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
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
