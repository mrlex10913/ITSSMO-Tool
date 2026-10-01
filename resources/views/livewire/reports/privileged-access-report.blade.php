<div>
    {{-- Header --}}
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-slate-800">Privileged Access Management Report</h2>
                <p class="text-sm text-slate-500 mt-1">Cross-role asset and access overview</p>
            </div>
            <div class="flex gap-2">
                <button wire:click="export" class="inline-flex items-center px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Export Excel
                </button>
            </div>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl border border-slate-200 p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-slate-500 uppercase">Total Users</p>
                    <p class="text-xl font-bold text-slate-800">{{ $stats['total_users'] ?? 0 }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-purple-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-slate-500 uppercase">With Assets</p>
                    <p class="text-xl font-bold text-slate-800">{{ $stats['with_assets'] ?? 0 }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-amber-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-slate-500 uppercase">Assets Assigned</p>
                    <p class="text-xl font-bold text-slate-800">{{ $stats['total_assets_assigned'] ?? 0 }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-green-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-slate-500 uppercase">Active Borrows</p>
                    <p class="text-xl font-bold text-slate-800">{{ $stats['active_borrows'] ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-xl border border-slate-200 p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-6 gap-4">
            <div>
                <label class="text-xs font-medium text-slate-500 mb-1 block">Search</label>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Name or email..." class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="text-xs font-medium text-slate-500 mb-1 block">Role</label>
                <select wire:model="roleFilter" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Roles</option>
                    @foreach($roles as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-xs font-medium text-slate-500 mb-1 block">Date From</label>
                <input wire:model="dateFrom" type="date" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="text-xs font-medium text-slate-500 mb-1 block">Date To</label>
                <input wire:model="dateTo" type="date" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="text-xs font-medium text-slate-500 mb-1 block">Report Type</label>
                <select wire:model="reportType" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="summary">Summary</option>
                    <option value="detailed">Detailed</option>
                    <option value="audit">Audit Trail</option>
                </select>
            </div>
            <div class="flex items-end">
                <button wire:click="resetFilters" class="w-full px-4 py-2 text-sm font-medium text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-lg transition-colors">
                    Reset Filters
                </button>
            </div>
        </div>
    </div>

    {{-- Results Table --}}
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">User</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Role</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">ITSS Assets</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">PAMO Assets</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Borrowed</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Total</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-200">
                    @forelse($users as $user)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-xs font-bold text-blue-600">
                                        {{ strtoupper(substr($user->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-slate-800">{{ $user->name }}</p>
                                        <p class="text-xs text-slate-500">{{ $user->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-slate-100 text-slate-700">
                                    {{ $user->role?->name ?? 'N/A' }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-sm text-slate-700">{{ $user->itss_assets->count() }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-sm text-slate-700">{{ $user->pamo_assets->count() }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-sm text-slate-700">{{ $user->borrowed_items->count() }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-sm font-semibold text-slate-800">{{ $user->total_assets }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <button wire:click="$set('selectedUser', {{ $user->id }})" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                    View Details
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-slate-500">
                                No users with assets found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- User Detail Modal --}}
    @if($selectedUser)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" wire:click="$set('selectedUser', null)">
            <div class="bg-white rounded-xl shadow-xl max-w-2xl w-full mx-4 max-h-[80vh] overflow-y-auto" wire:click.stop>
                <div class="px-6 py-4 border-b border-slate-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-slate-800">User Asset Details</h3>
                        <button wire:click="$set('selectedUser', null)" class="text-slate-400 hover:text-slate-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="px-6 py-4">
                    @php
                        $selected = $users->firstWhere('id', $selectedUser);
                    @endphp
                    @if($selected)
                        <div class="space-y-4">
                            <div class="bg-slate-50 rounded-lg p-4">
                                <h4 class="font-medium text-slate-800 mb-2">{{ $selected->name }}</h4>
                                <p class="text-sm text-slate-500">{{ $selected->email }}</p>
                                <p class="text-sm text-slate-500">Role: {{ $selected->role?->name ?? 'N/A' }}</p>
                            </div>

                            @if($selected->itss_assets->count() > 0)
                                <div>
                                    <h5 class="text-sm font-semibold text-slate-700 mb-2">ITSS Assets ({{ $selected->itss_assets->count() }})</h5>
                                    <div class="space-y-2">
                                        @foreach($selected->itss_assets as $asset)
                                            <div class="flex items-center justify-between p-2 bg-slate-50 rounded text-sm">
                                                <span>{{ $asset->item_name }} ({{ $asset->item_serial_itss }})</span>
                                                <span class="px-2 py-0.5 rounded text-xs bg-blue-100 text-blue-700">{{ $asset->status }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            @if($selected->pamo_assets->count() > 0)
                                <div>
                                    <h5 class="text-sm font-semibold text-slate-700 mb-2">PAMO Assets ({{ $selected->pamo_assets->count() }})</h5>
                                    <div class="space-y-2">
                                        @foreach($selected->pamo_assets as $asset)
                                            <div class="flex items-center justify-between p-2 bg-slate-50 rounded text-sm">
                                                <span>{{ $asset->brand }} {{ $asset->model }} ({{ $asset->serial_number }})</span>
                                                <span class="px-2 py-0.5 rounded text-xs bg-purple-100 text-purple-700">{{ $asset->status }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            @if($selected->borrowed_items->count() > 0)
                                <div>
                                    <h5 class="text-sm font-semibold text-slate-700 mb-2">Borrowed Equipment ({{ $selected->borrowed_items->count() }})</h5>
                                    <div class="space-y-2">
                                        @foreach($selected->borrowed_items as $borrow)
                                            <div class="p-2 bg-slate-50 rounded text-sm">
                                                <div class="flex items-center justify-between">
                                                    <span>{{ $borrow->doc_tracker }}</span>
                                                    <span class="px-2 py-0.5 rounded text-xs bg-amber-100 text-amber-700">{{ $borrow->status }}</span>
                                                </div>
                                                <p class="text-xs text-slate-500 mt-1">Location: {{ $borrow->location }}</p>
                                                <p class="text-xs text-slate-500">Borrowed: {{ $borrow->date_to_borrow }}</p>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
