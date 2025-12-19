<div>
    <div class="sm:px-6 lg:px-8">
        <h1 class="text-2xl font-semibold text-gray-900 mb-6 mt-6">SLA Policies</h1>

        @if (session('success'))
            <div class="mb-4 rounded bg-green-50 text-green-800 px-4 py-3">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="p-4 border-b border-gray-200 flex items-center justify-between gap-4">
                <div class="flex-1">
                    <input type="text" placeholder="Search name..." wire:model.live.debounce.300ms="search"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div class="flex items-center gap-2">
                    <select wire:model.live="type" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Types</option>
                        <option value="incident">Incident</option>
                        <option value="request">Request</option>
                    </select>
                    <select wire:model.live="priority" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Priorities</option>
                        <option value="low">Low</option>
                        <option value="medium">Medium</option>
                        <option value="high">High</option>
                        <option value="critical">Critical</option>
                    </select>
                    <button class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700" wire:click="create">
                        <span class="inline-flex items-center">
                            <x-heroicon name="plus" class="w-4 h-4 mr-1" />
                            New Policy
                        </span>
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Priority</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Respond (mins)</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Resolve (mins)</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($policies as $p)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-3 text-sm text-gray-900">{{ $p->name }}</td>
                                <td class="px-6 py-3 text-sm">
                                    <span class="px-2 py-1 rounded-full text-xs font-medium {{ $p->type === 'incident' ? 'bg-rose-100 text-rose-800' : 'bg-emerald-100 text-emerald-800' }}">{{ ucfirst($p->type) }}</span>
                                </td>
                                <td class="px-6 py-3 text-sm">
                                    <span class="px-2 py-1 rounded-full text-xs font-medium
                                        {{ match($p->priority) {
                                            'low' => 'bg-gray-100 text-gray-800',
                                            'medium' => 'bg-indigo-100 text-indigo-800',
                                            'high' => 'bg-orange-100 text-orange-800',
                                            'critical' => 'bg-red-100 text-red-800',
                                            default => 'bg-gray-100 text-gray-800'
                                        } }}">{{ ucfirst($p->priority) }}</span>
                                </td>
                                <td class="px-6 py-3 text-sm text-gray-700">{{ $p->respond_mins ?? '—' }}</td>
                                <td class="px-6 py-3 text-sm text-gray-700">{{ $p->resolve_mins ?? '—' }}</td>
                                <td class="px-6 py-3 text-sm">
                                    <span class="px-2 py-1 rounded-full text-xs font-medium {{ $p->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-700' }}">{{ $p->is_active ? 'Active' : 'Inactive' }}</span>
                                </td>
                                <td class="px-6 py-3 text-right text-sm">
                                    <button class="text-blue-600 hover:text-blue-800 mr-3" wire:click="edit({{ $p->id }})">Edit</button>
                                    <button class="text-gray-600 hover:text-gray-800" wire:click="toggleActive({{ $p->id }})">{{ $p->is_active ? 'Disable' : 'Enable' }}</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-16 text-center text-gray-500">No SLA policies found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4">{{ $policies->links() }}</div>
        </div>
    </div>

    <!-- Modal -->
    <x-dialog-modal wire:model="showModal">
        <x-slot name="title">{{ $editingId ? 'Edit SLA Policy' : 'New SLA Policy' }}</x-slot>
        <x-slot name="content">
            <div class="space-y-4">
                <div>
                    <x-label value="Name" />
                    <x-input type="text" class="mt-1 w-full" wire:model.defer="name" />
                    @error('name') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <x-label value="Type" />
                        <select class="mt-1 w-full border-gray-300 rounded-md focus:border-blue-500 focus:ring-blue-500" wire:model.defer="form_type">
                            <option value="incident">Incident</option>
                            <option value="request">Request</option>
                        </select>
                        @error('form_type') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <x-label value="Priority" />
                        <select class="mt-1 w-full border-gray-300 rounded-md focus:border-blue-500 focus:ring-blue-500" wire:model.defer="form_priority">
                            <option value="low">Low</option>
                            <option value="medium">Medium</option>
                            <option value="high">High</option>
                            <option value="critical">Critical</option>
                        </select>
                        @error('form_priority') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="flex items-center gap-2 mt-6 md:mt-7">
                        <input id="is_active" type="checkbox" class="rounded border-gray-300" wire:model.defer="is_active">
                        <label for="is_active" class="text-sm text-gray-700">Active</label>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <x-label value="Respond (minutes)" />
                        <x-input type="number" min="1" class="mt-1 w-full" wire:model.defer="respond_mins" />
                        @error('respond_mins') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <x-label value="Resolve (minutes)" />
                        <x-input type="number" min="1" class="mt-1 w-full" wire:model.defer="resolve_mins" />
                        @error('resolve_mins') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>
        </x-slot>
        <x-slot name="footer">
            <x-secondary-button wire:click="$set('showModal', false)" class="mr-2">Cancel</x-secondary-button>
            <x-button wire:click="save">Save</x-button>
        </x-slot>
    </x-dialog-modal>
</div>
