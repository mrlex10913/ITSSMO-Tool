<div>
    {{-- If you look to others for fulfillment, you will never truly be fulfilled. --}}
<div class="max-w-7xl mx-auto p-4">
    <h1 class="text-xl font-semibold">Escalations</h1>
    <div class="mt-3 p-3 rounded border bg-white">
        <div class="flex flex-wrap items-center gap-2">
            <select wire:model.live="type" class="px-3 py-2 border rounded">
                <option value="">All Types</option>
                <option value="incident">Incident</option>
                <option value="request">Request</option>
            </select>
            <select wire:model.live="priority" class="px-3 py-2 border rounded">
                <option value="">All Priorities</option>
                <option value="low">Low</option>
                <option value="medium">Medium</option>
                <option value="high">High</option>
                <option value="critical">Critical</option>
            </select>
            <select wire:model.live="assignee" class="px-3 py-2 border rounded">
                <option value="">All Assignees</option>
                @foreach(($agents ?? collect()) as $a)
                    <option value="{{ $a->id }}">{{ $a->name }}</option>
                @endforeach
            </select>
            <label class="inline-flex items-center text-sm">
                <input type="checkbox" wire:model.live="breachedOnly" class="mr-1 rounded border-gray-300" /> Breached only
            </label>
        </div>
    </div>

    <div class="mt-3 overflow-x-auto" wire:poll.10s>
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Ticket</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Subject</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Priority</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Assignee</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">SLA</th>
                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @php $now = now(); $ids = $this->escalatingIds; @endphp
                @forelse($tickets as $t)
                    @php
                        $breached = $t->sla_due_at && $now->greaterThan($t->sla_due_at) && in_array($t->status, ['open','in_progress']);
                        $minsLeft = $t->sla_due_at ? $now->diffInMinutes($t->sla_due_at, false) : null;
                        $isEscalating = in_array($t->id, $ids, true);
                    @endphp
                    <tr class="hover:bg-gray-50 {{ $isEscalating ? 'bg-orange-50' : '' }}">
                        <td class="px-3 py-2 text-sm text-gray-700">#{{ $t->id }}</td>
                        <td class="px-3 py-2 text-sm text-gray-800">{{ $t->subject }}</td>
                        <td class="px-3 py-2 text-sm">
                            <span class="px-2 py-0.5 rounded text-xs {{ $t->priority === 'critical' ? 'bg-red-100 text-red-800' : ($t->priority==='high' ? 'bg-orange-100 text-orange-800' : 'bg-slate-100 text-slate-700') }}">{{ ucfirst($t->priority ?? '-') }}</span>
                        </td>
                        <td class="px-3 py-2 text-sm text-gray-700">{{ $t->assignee->name ?? '—' }}</td>
                        <td class="px-3 py-2 text-sm">
                            @if($t->sla_due_at)
                                <span class="{{ $breached ? 'text-red-700' : 'text-gray-700' }}">{{ $breached ? 'Breached' : 'Due ' . $t->sla_due_at->diffForHumans() }}</span>
                                @if(!$breached && $minsLeft !== null)
                                    <span class="ml-2 text-xs {{ $isEscalating ? 'text-orange-700' : 'text-slate-500' }}">{{ $minsLeft }} mins</span>
                                @endif
                            @else
                                <span class="text-slate-500">—</span>
                            @endif
                        </td>
                        <td class="px-3 py-2 text-sm text-right">
                            <div class="inline-flex gap-2">
                                <button wire:click="acknowledge({{ $t->id }})" class="px-2 py-1 text-xs rounded border bg-white hover:bg-gray-50">Acknowledge</button>
                                <div x-data="{m:30}">
                                    <button @click="$wire.snooze({{ $t->id }}, m)" class="px-2 py-1 text-xs rounded border bg-white hover:bg-gray-50">Snooze</button>
                                    <input type="number" x-model.number="m" class="w-14 ml-1 px-1 py-0.5 text-xs border rounded" min="5" step="5" />
                                </div>
                                <div>
                                    <select class="px-2 py-1 text-xs border rounded" @change="$wire.reassign({{ $t->id }}, parseInt($event.target.value))">
                                        <option value="">Reassign…</option>
                                        @foreach(($agents ?? collect()) as $a)
                                            <option value="{{ $a->id }}">{{ $a->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-3 py-8 text-center text-sm text-gray-600">No escalations in your filters.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="mt-3">{{ $tickets->links() }}</div>
    </div>
</div>
