<div>
    {{-- The whole world belongs to you. --}}
<div class="max-w-7xl mx-auto p-4 space-y-4">
    <h1 class="text-xl font-semibold">ISO Audit Report</h1>
    <div class="p-3 bg-white border rounded">
        <div class="flex flex-wrap items-center gap-2">
            <label class="text-sm">From</label>
            <input type="date" wire:model="from" class="rounded border-gray-300" />
            <label class="text-sm">To</label>
            <input type="date" wire:model="to" class="rounded border-gray-300" />
            <select wire:model="type" class="rounded border-gray-300 text-sm">
                <option value="">All Types</option>
                <option value="incident">Incident</option>
                <option value="request">Request</option>
            </select>
            <select wire:model="priority" class="rounded border-gray-300 text-sm">
                <option value="">All Priorities</option>
                <option value="low">Low</option>
                <option value="medium">Medium</option>
                <option value="high">High</option>
                <option value="critical">Critical</option>
            </select>
            <select wire:model="assigneeId" class="rounded border-gray-300 text-sm">
                <option value="">All Assignees</option>
                @foreach(($agents ?? collect()) as $a)
                    <option value="{{ $a->id }}">{{ $a->name }}</option>
                @endforeach
            </select>
            <select wire:model="requesterId" class="rounded border-gray-300 text-sm">
                <option value="">All Requesters</option>
                @foreach(($requesters ?? collect()) as $r)
                    <option value="{{ $r->id }}">{{ $r->name }}</option>
                @endforeach
            </select>
            <select wire:model="categoryId" class="rounded border-gray-300 text-sm">
                <option value="">All Categories</option>
                @foreach(($categories ?? collect()) as $c)
                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                @endforeach
            </select>
            <button wire:click="exportCsv" class="px-3 py-1.5 rounded bg-emerald-600 text-white text-sm">Export CSV</button>
        </div>
    </div>
    <p class="text-xs text-gray-500">Includes traceability fields (requester/assignee), timestamps, SLA due and breach flag, and response/resolve times for ISO audit sampling.</p>
</div>
