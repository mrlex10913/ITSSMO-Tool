<div class="container mx-auto" x-data="{ showCreate: $wire.entangle('showCreate'), showEdit: $wire.entangle('showEdit') }">
    <div class="mb-4 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight">Menus</h1>
            <div class="text-sm text-gray-500 dark:text-gray-400">
                <x-nav-link href="{{ route('controlPanel.admin') }}" class="hover:text-blue-600">Control Panel</x-nav-link>
                <span>/</span>
                <span class="text-blue-600 font-medium">Navigation</span>
            </div>
        </div>
    <button class="btn btn-primary" @click="showCreate = true; $wire.openCreate()">Create Menu</button>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm ring-1 ring-gray-200 dark:ring-gray-700 p-4 mb-4">
        <div class="flex items-center gap-3">
            <input type="text" placeholder="Search..." class="w-full md:w-80 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" wire:model.live.debounce.400ms="search" />
            <select class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" wire:model.live="departmentFilter">
            <option value="">All Departments</option>
            <option value="pamo">PAMO</option>
            <option value="bfo">BFO</option>
            <option value="itss">ITSS</option>
        </select>
            <select class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" wire:model.live="perPage">
            <option value="10">10</option>
            <option value="25">25</option>
            <option value="50">50</option>
        </select>
        <label class="ml-2 inline-flex items-center gap-2 text-sm select-none">
            <input type="checkbox" class="h-4 w-4" wire:model.live="groupByRole" />
            <span>Grouped by role</span>
        </label>
        <div class="ml-auto flex items-center gap-2">
            <button class="btn btn-outline btn-sm" wire:click="bulkSetActive(true)">Bulk Activate</button>
            <button class="btn btn-outline btn-sm" wire:click="bulkSetActive(false)">Bulk Deactivate</button>
            <details class="ml-2 inline-block">
                <summary class="btn btn-outline btn-sm cursor-pointer select-none">Bulk Roles</summary>
                <div class="mt-1 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded shadow p-3 w-72 z-10">
                    <div class="text-xs font-semibold mb-2">Select roles</div>
                    <div class="grid grid-cols-2 gap-2 max-h-40 overflow-auto mb-3">
                        @foreach($this->roles as $r)
                        <label class="inline-flex items-center gap-2 text-sm">
                            <input type="checkbox" value="{{ $r->id }}" wire:model.live="bulk_role_ids" />
                            <span>{{ $r->name }}</span>
                        </label>
                        @endforeach
                    </div>
                    <div class="flex gap-2 justify-end">
                        <button class="btn btn-outline btn-sm" wire:click="bulkAssignRoles">Assign</button>
                        <button class="btn btn-outline btn-sm" wire:click="bulkRemoveRoles">Remove</button>
                    </div>
                </div>
            </details>
            <details class="ml-2 inline-block">
                <summary class="btn btn-outline btn-sm cursor-pointer select-none">Bulk Users</summary>
                <div class="mt-1 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded shadow p-3 w-80 z-10">
                    <div class="text-xs font-semibold mb-2">Select users</div>
                    <div class="max-h-40 overflow-auto mb-3">
                        @foreach($this->users as $u)
                        <label class="flex items-center gap-2 text-sm py-1">
                            <input type="checkbox" value="{{ $u->id }}" wire:model.live="bulk_user_ids" />
                            <span>{{ $u->name }}</span>
                            <span class="text-gray-400 text-xs">{{ $u->email }}</span>
                        </label>
                        @endforeach
                    </div>
                    <div class="flex gap-2 justify-end">
                        <button class="btn btn-outline btn-sm" wire:click="bulkAssignUsers">Assign</button>
                        <button class="btn btn-outline btn-sm" wire:click="bulkRemoveUsers">Remove</button>
                    </div>
                </div>
            </details>
        </div>
    </div>

    @if($this->groupByRole)
    <div class="space-y-6">
        @php
            $groups = $this->groupedMenusByRole;
        @endphp
        @if(empty($groups))
            <div class="text-center text-slate-500">No menus found.</div>
        @else
        @foreach($groups as $roleSlug => $group)
            <div class="bg-white rounded-xl border">
                <div class="px-4 py-3 border-b flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="px-2 py-0.5 text-xs rounded bg-slate-100 text-slate-700">{{ strtoupper($roleSlug) }}</span>
                        <h2 class="font-semibold">{{ $group['name'] ?? strtoupper($roleSlug) }}</h2>
                    </div>
                    <span class="text-xs text-slate-500">{{ collect($group['sections'] ?? [])->flatten(1)->count() }} item(s)</span>
                </div>
                <div class="p-4 space-y-4">
                    @foreach(($group['sections'] ?? []) as $sectionLabel => $items)
                        <div>
                            <div class="mb-2 flex items-center gap-2">
                                <span class="material-symbols-sharp text-slate-500">folder</span>
                                <h3 class="text-sm font-semibold text-slate-700">{{ $sectionLabel ?: 'General' }}</h3>
                            </div>
                            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-3">
                                @foreach($items as $m)
                                    <div class="border rounded-lg p-3 flex items-start justify-between">
                                        <div class="min-w-0">
                                            <div class="font-medium text-gray-800 truncate">{{ $m['label'] }}</div>
                                            <div class="text-xs text-slate-500 truncate">
                                                @if($m['route'])
                                                    <span class="px-1.5 py-0.5 rounded bg-blue-50 text-blue-700 text-[10px] align-middle">route</span>
                                                    {{ $m['route'] }}
                                                @elseif($m['url'])
                                                    <span class="px-1.5 py-0.5 rounded bg-amber-50 text-amber-700 text-[10px] align-middle">url</span>
                                                    {{ $m['url'] }}
                                                @else
                                                    —
                                                @endif
                                            </div>
                                            <div class="mt-1 text-xs text-slate-400">Order: {{ $m['sort_order'] }}</div>
                                        </div>
                                        <div class="text-right space-y-1">
                                            @if($m['is_active'])
                                                <span class="px-2 py-0.5 text-[10px] bg-green-100 text-green-700 rounded">Active</span>
                                            @else
                                                <span class="px-2 py-0.5 text-[10px] bg-gray-100 text-gray-600 rounded">Inactive</span>
                                            @endif
                                            <div class="space-x-1">
                                                <button class="text-blue-600 text-sm" @click="$wire.openEdit({{ $m['id'] }}); showEdit = true">Edit</button>
                                                <button class="text-red-600 text-sm" wire:click="delete({{ $m['id'] }})">Delete</button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
        @endif
    </div>
    @else
    <div class="overflow-auto rounded border">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50 dark:bg-gray-900/40">
                <tr>
                    <th class="text-left px-3 py-2 w-12">
                        <input type="checkbox" @click.prevent="$wire.toggleSelectPage()" @keydown.enter.prevent="$wire.toggleSelectPage()" @keydown.space.prevent="$wire.toggleSelectPage()" @change.prevent="$wire.toggleSelectPage()" @dblclick.prevent {{ $this->areAllPageSelected ? 'checked' : '' }} />
                    </th>
                    <th class="text-left px-3 py-2">Label</th>
                    <th class="text-left px-3 py-2">Section</th>
                    <th class="text-left px-3 py-2">Route</th>
                    <th class="text-left px-3 py-2">URL</th>
                    <th class="text-left px-3 py-2">Icon</th>
                    <th class="text-left px-3 py-2">Order</th>
                    <th class="text-left px-3 py-2">Active</th>
                    <th class="text-left px-3 py-2">Users</th>
                    <th class="text-left px-3 py-2">Roles</th>
                    <th class="text-right px-3 py-2">Actions</th>
                </tr>
            </thead>
            <tbody x-data="{
                draggingId: null,
                onDragStart(id){ this.draggingId = id },
                onDrop(targetId){
                    if(this.draggingId===null){ return }
                    const rows = [...$el.querySelectorAll('tr[data-id]')].map(r=>r.dataset.id)
                    const from = rows.indexOf(String(this.draggingId))
                    const to = rows.indexOf(String(targetId))
                    if(from<0 || to<0){ this.draggingId=null; return }
                    rows.splice(to,0,rows.splice(from,1)[0])
                    $wire.reorder(rows.map(Number))
                    this.draggingId=null
                }
            }">
                @foreach($this->menus as $menu)
                <tr class="border-t" data-id="{{ $menu->id }}" draggable="true" @dragstart="onDragStart({{ $menu->id }})" @dragover.prevent @drop.prevent="onDrop({{ $menu->id }})">
                    <td class="px-3 py-2">
                        <input type="checkbox" value="{{ $menu->id }}" wire:model.live="selected" />
                    </td>
                    <td class="px-3 py-2">{{ $menu->label }}</td>
                    <td class="px-3 py-2">{{ $menu->section }}</td>
                    <td class="px-3 py-2">{{ $menu->route }}</td>
                    <td class="px-3 py-2">{{ Str::limit($menu->url, 60) }}</td>
                    <td class="px-3 py-2">
                        <span class="inline-flex items-center gap-1">
                            @if($menu->icon)
                                <span class="material-symbols-sharp text-base align-middle">{{ $menu->icon }}</span>
                            @endif
                            <span class="text-gray-700">{{ $menu->icon }}</span>
                        </span>
                    </td>
                    <td class="px-3 py-2">
                        <div class="flex items-center gap-2">
                            <input type="number" class="w-20 border rounded px-2 py-1" value="{{ $menu->sort_order }}" min="0" @change="$wire.call('updateSort', {{ $menu->id }}, parseInt($event.target.value||0))" />
                            <div class="flex flex-col">
                                <button class="leading-none px-1 border rounded" title="Move up" @click="$wire.call('updateSort', {{ $menu->id }}, {{ max(0, $menu->sort_order - 1) }})">▲</button>
                                <button class="leading-none px-1 border rounded" title="Move down" @click="$wire.call('updateSort', {{ $menu->id }}, {{ $menu->sort_order + 1 }})">▼</button>
                            </div>
                        </div>
                    </td>
                    <td class="px-3 py-2">
                        @if($menu->is_active)
                            <span class="px-2 py-0.5 text-xs bg-green-100 text-green-700 rounded">Yes</span>
                        @else
                            <span class="px-2 py-0.5 text-xs bg-gray-100 text-gray-600 rounded">No</span>
                        @endif
                    </td>
                    <td class="px-3 py-2">
                        @if($menu->users_count ?? false)
                            <span class="px-2 py-0.5 text-xs bg-slate-100 text-slate-700 rounded">{{ $menu->users_count }} user(s)</span>
                        @else
                            <span class="text-xs text-slate-400">—</span>
                        @endif
                    </td>
                    <td class="px-3 py-2">
                        {{ $menu->roles->pluck('name')->implode(', ') }}
                    </td>
                    <td class="px-3 py-2 text-right">
                        <button class="btn btn-ghost btn-sm" @click="$wire.openEdit({{ $menu->id }}); showEdit = true">Edit</button>
                        <button class="btn btn-danger btn-sm" wire:click="delete({{ $menu->id }})">Delete</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <div class="mt-3 flex items-center justify-between">
        <div class="text-sm text-gray-600">
            Selected: <span class="font-semibold">{{ $this->selectedCount }}</span>
            @if($this->selectedCount > 0)
            <button class="ml-2 text-blue-700 underline" wire:click="clearSelection">Clear</button>
            @endif
        </div>
        <div>
            {{ $this->menus->links() }}
        </div>
    </div>

    <!-- Create Modal -->
    <div x-show="showCreate" x-cloak class="fixed inset-0 bg-black/40 flex items-center justify-center p-4">
        <div class="bg-white dark:bg-gray-800 rounded shadow-lg w-full max-w-2xl ring-1 ring-gray-200 dark:ring-gray-700">
            <div class="border-b px-4 py-2 flex items-center justify-between">
                <h2 class="font-semibold">Create Menu</h2>
                <button class="text-gray-500" @click="showCreate=false">✕</button>
            </div>
            <div class="p-4 space-y-3">
                <div>
                    <label class="block text-sm mb-1">Label</label>
                    <input type="text" class="w-full border rounded px-2 py-1 dark:bg-gray-700 dark:border-gray-600 dark:text-white" wire:model.defer="label" />
                    @error('label') <div class="text-xs text-red-600">{{ $message }}</div> @enderror
                </div>
                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <label class="block text-sm mb-1">Route name</label>
                        <input type="text" class="w-full border rounded px-2 py-1 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="e.g. tickets.index" wire:model.defer="route" />
                        @error('route') <div class="text-xs text-red-600">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="block text-sm mb-1">External URL</label>
                        <input type="url" class="w-full border rounded px-2 py-1 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="https://..." wire:model.defer="url" />
                        @error('url') <div class="text-xs text-red-600">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="block text-sm mb-1">Section</label>
                        <input type="text" class="w-full border rounded px-2 py-1 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Optional group label" wire:model.defer="section" />
                        @error('section') <div class="text-xs text-red-600">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="grid grid-cols-3 gap-3 items-end">
                    <div>
                        <label class="block text-sm mb-1">Icon</label>
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-sharp text-base align-middle">@if($this->icon) {{ $this->icon }} @endif</span>
                            <input type="text" class="flex-1 border rounded px-2 py-1 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="material symbol name" wire:model.live="icon" />
                        </div>
                        <details class="mt-2">
                            <summary class="cursor-pointer select-none text-sm text-blue-700">Pick icon</summary>
                            <div class="mt-2" x-data="{ q: '' }">
                                <input type="text" placeholder="Search…" class="w-full border rounded px-2 py-1 mb-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white" x-model="q" />
                                <div class="grid grid-cols-8 gap-2 max-h-64 overflow-auto">
                                    @php $icons = ['home','dashboard','menu','build','search','person','group','settings','help','list','assignment','check_circle','info','warning','shopping_cart','inventory','qr_code_scanner','qr_code','print','upload','download','mail','phone','map','schedule']; @endphp
                                    @foreach($icons as $ic)
                                    <button type="button" class="flex flex-col items-center gap-1 p-2 border rounded hover:bg-gray-50" wire:click="setIcon('{{ $ic }}')" x-show="q==='' || '{{ $ic }}'.includes(q)">
                                        <span class="material-symbols-sharp">{{ $ic }}</span>
                                        <span class="text-[10px] text-gray-600">{{ $ic }}</span>
                                    </button>
                                    @endforeach
                                </div>
                            </div>
                        </details>
                        @error('icon') <div class="text-xs text-red-600">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="block text-sm mb-1">Order</label>
                        <input type="number" class="w-full border rounded px-2 py-1 dark:bg-gray-700 dark:border-gray-600 dark:text-white" min="0" wire:model.defer="sort_order" />
                        @error('sort_order') <div class="text-xs text-red-600">{{ $message }}</div> @enderror
                    </div>
                    <div class="flex items-center gap-2">
                        <input id="activeCreate" type="checkbox" class="h-4 w-4" wire:model.defer="is_active" />
                        <label for="activeCreate">Active</label>
                    </div>
                </div>

                <div>
                    <label class="block text-sm mb-1">Roles</label>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-2 max-h-40 overflow-auto border rounded p-2 dark:border-gray-600">
                        @foreach($this->roles as $r)
                        <label class="inline-flex items-center gap-2 text-sm">
                            <input type="checkbox" value="{{ $r->id }}" wire:model.defer="role_ids" />
                            <span>{{ $r->name }}</span>
                        </label>
                        @endforeach
                    </div>
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
        <div class="bg-white dark:bg-gray-800 rounded shadow-lg w-full max-w-2xl ring-1 ring-gray-200 dark:ring-gray-700">
            <div class="border-b px-4 py-2 flex items-center justify-between">
                <h2 class="font-semibold">Edit Menu</h2>
                <button class="text-gray-500" @click="showEdit=false">✕</button>
            </div>
            <div class="p-4 space-y-3">
                <div>
                    <label class="block text-sm mb-1">Label</label>
                    <input type="text" class="w-full border rounded px-2 py-1 dark:bg-gray-700 dark:border-gray-600 dark:text-white" wire:model.defer="label" />
                    @error('label') <div class="text-xs text-red-600">{{ $message }}</div> @enderror
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm mb-1">Route name</label>
                        <input type="text" class="w-full border rounded px-2 py-1 dark:bg-gray-700 dark:border-gray-600 dark:text-white" wire:model.defer="route" />
                        @error('route') <div class="text-xs text-red-600">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="block text-sm mb-1">External URL</label>
                        <input type="url" class="w-full border rounded px-2 py-1 dark:bg-gray-700 dark:border-gray-600 dark:text-white" wire:model.defer="url" />
                        @error('url') <div class="text-xs text-red-600">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="block text-sm mb-1">Section</label>
                        <input type="text" class="w-full border rounded px-2 py-1 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Optional group label" wire:model.defer="section" />
                        @error('section') <div class="text-xs text-red-600">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="grid grid-cols-3 gap-3 items-end">
                    <div>
                        <label class="block text-sm mb-1">Icon</label>
                        <input type="text" class="w-full border rounded px-2 py-1 dark:bg-gray-700 dark:border-gray-600 dark:text-white" wire:model.defer="icon" />
                        @error('icon') <div class="text-xs text-red-600">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="block text-sm mb-1">Order</label>
                        <input type="number" class="w-full border rounded px-2 py-1 dark:bg-gray-700 dark:border-gray-600 dark:text-white" min="0" wire:model.defer="sort_order" />
                        @error('sort_order') <div class="text-xs text-red-600">{{ $message }}</div> @enderror
                    </div>
                    <div class="flex items-center gap-2">
                        <input id="activeEdit" type="checkbox" class="h-4 w-4" wire:model.defer="is_active" />
                        <label for="activeEdit">Active</label>
                    </div>
                </div>

                <div>
                    <label class="block text-sm mb-1">Roles</label>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-2 max-h-40 overflow-auto border rounded p-2 dark:border-gray-600">
                        @foreach($this->roles as $r)
                        <label class="inline-flex items-center gap-2 text-sm">
                            <input type="checkbox" value="{{ $r->id }}" wire:model.defer="role_ids" />
                            <span>{{ $r->name }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="border-t px-4 py-3 flex items-center justify-end gap-2">
                <button class="btn btn-outline" @click="showEdit=false">Cancel</button>
                <button class="btn btn-primary" wire:click="update">Update</button>
            </div>
        </div>
    </div>
</div>
