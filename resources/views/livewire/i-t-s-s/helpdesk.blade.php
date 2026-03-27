<div>
    <div x-data="{
        showFilters: false,
        lastNotifiedId: null,
        lastNotifiedAt: 0,
        notifyNewTickets(e){
            const play = () => {
                try { new Audio('{{ asset('sounds/notificationbell.wav') }}').play(); } catch(_) {}
            };
            try {
                const title = 'New support ticket';
                const body = (e?.detail?.delta || 1) + ' new ticket' + ((e?.detail?.delta||1) > 1 ? 's' : '') + ' arrived';
                if ('Notification' in window) {
                    if (Notification.permission === 'granted') {
                        new Notification(title, { body });
                        play();
                    } else if (Notification.permission !== 'denied') {
                        Notification.requestPermission().then(p => {
                            if (p === 'granted') { new Notification(title, { body }); play(); }
                        });
                    } else { play(); }
                } else { play(); }
            } catch(_) { play(); }
        },
        maybeNotifyFromEvent(ev){
            try {
                const detail = ev?.detail || {};
                if (detail?.type === 'created') {
                    const now = Date.now();
                    const id = detail?.ticketId || null;
                    if (id && this.lastNotifiedId === id && (now - this.lastNotifiedAt) < 8000) { return; }
                    this.lastNotifiedId = id;
                    this.lastNotifiedAt = now;
                    this.notifyNewTickets({ detail: { delta: 1 } });
                }
            } catch(_) {}
        }
    }"
    x-init="
        window.addEventListener('helpdesk-comment-created', () => { try { $wire.$refresh() } catch(_) {} });
        window.addEventListener('helpdesk-ticket-changed', (ev) => {
            try {
                if ($wire.page && $wire.page !== 1) { $wire.set('page', 1); }
                $wire.$refresh();
            } catch(_) {}
            try { maybeNotifyFromEvent(ev); } catch(_) {}
        });
        window.addEventListener('tickets-new', (ev) => { notifyNewTickets(ev); });
    ">
    <div class="px-4 sm:px-6 lg:px-8 py-6">
        {{-- Page Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
            <div>
                <h1 class="text-2xl font-semibold text-slate-800">Helpdesk</h1>
                <p class="text-sm text-slate-500 mt-1">Manage and track support tickets</p>
            </div>
            <button wire:click="$set('showCreate', true)"
                    class="mt-3 sm:mt-0 inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                <x-heroicon name="plus" class="w-4 h-4" />
                New Ticket
            </button>
        </div>

        {{-- Flash Messages --}}
        @if (session('success'))
            <div class="mb-4 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 text-sm flex items-center gap-2">
                <x-heroicon name="check-circle" class="w-5 h-5 text-emerald-600 shrink-0" />
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="mb-4 rounded-lg bg-red-50 border border-red-200 text-red-800 px-4 py-3 text-sm flex items-center gap-2">
                <x-heroicon name="exclamation-circle" class="w-5 h-5 text-red-600 shrink-0" />
                {{ session('error') }}
            </div>
        @endif

        <div class="grid grid-cols-1 xl:grid-cols-5 gap-6">
            {{-- Sidebar --}}
            <aside class="xl:col-span-1 space-y-4">
                {{-- Quick Stats --}}
                <div class="bg-white rounded-xl border border-slate-200 p-4">
                    <h2 class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-3">Overview</h2>
                    <div class="grid grid-cols-2 xl:grid-cols-1 gap-3">
                        <button wire:click="$set('status', 'open')" class="group flex items-center justify-between p-3 rounded-lg transition-colors {{ ($status ?? '') === 'open' ? 'bg-blue-50 ring-1 ring-blue-200' : 'hover:bg-slate-50' }}">
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                                <span class="text-sm text-slate-700">Open</span>
                            </div>
                            <span class="text-lg font-semibold text-slate-800">{{ $stats['open'] ?? 0 }}</span>
                        </button>
                        <button wire:click="$set('status', 'in_progress')" class="group flex items-center justify-between p-3 rounded-lg transition-colors {{ ($status ?? '') === 'in_progress' ? 'bg-amber-50 ring-1 ring-amber-200' : 'hover:bg-slate-50' }}">
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                                <span class="text-sm text-slate-700">In Progress</span>
                            </div>
                            <span class="text-lg font-semibold text-slate-800">{{ $stats['in_progress'] ?? 0 }}</span>
                        </button>
                        <button wire:click="$set('status', 'resolved')" class="group flex items-center justify-between p-3 rounded-lg transition-colors {{ ($status ?? '') === 'resolved' ? 'bg-emerald-50 ring-1 ring-emerald-200' : 'hover:bg-slate-50' }}">
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                <span class="text-sm text-slate-700">Resolved</span>
                            </div>
                            <span class="text-lg font-semibold text-slate-800">{{ $stats['resolved'] ?? 0 }}</span>
                        </button>
                        <button wire:click="$set('status', '')" class="group flex items-center justify-between p-3 rounded-lg transition-colors {{ ($status ?? '') === '' ? 'bg-slate-100 ring-1 ring-slate-300' : 'hover:bg-slate-50' }}">
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-slate-400"></span>
                                <span class="text-sm text-slate-700">All</span>
                            </div>
                            <span class="text-lg font-semibold text-slate-800">{{ $stats['total'] ?? 0 }}</span>
                        </button>
                    </div>
                </div>

                {{-- SLA Alerts --}}
                @php
                    $dueToday = \App\Models\Helpdesk\Ticket::query()->whereIn('status', ['open','in_progress'])->whereBetween('sla_due_at', [now()->startOfDay(), now()->endOfDay()])->count();
                    $breachedOpen = \App\Models\Helpdesk\Ticket::query()->whereIn('status', ['open','in_progress'])->whereNotNull('sla_due_at')->where('sla_due_at', '<', now())->count();
                @endphp
                @if($dueToday > 0 || $breachedOpen > 0)
                <div class="bg-white rounded-xl border border-slate-200 p-4">
                    <h2 class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-3">SLA Alerts</h2>
                    <div class="space-y-2">
                        @if($dueToday > 0)
                        <button wire:click="$set('escalationsOnly', true)" class="w-full flex items-center gap-3 p-3 rounded-lg bg-amber-50 border border-amber-200 hover:bg-amber-100 transition-colors">
                            <x-heroicon name="clock" class="w-5 h-5 text-amber-600 shrink-0" />
                            <div class="flex-1 text-left">
                                <div class="text-sm font-medium text-amber-800">Due Today</div>
                                <div class="text-xs text-amber-600">{{ $dueToday }} {{ Str::plural('ticket', $dueToday) }}</div>
                            </div>
                        </button>
                        @endif
                        @if($breachedOpen > 0)
                        <div class="flex items-center gap-3 p-3 rounded-lg bg-red-50 border border-red-200">
                            <x-heroicon name="exclamation-triangle" class="w-5 h-5 text-red-600 shrink-0" />
                            <div class="flex-1">
                                <div class="text-sm font-medium text-red-800">Breached</div>
                                <div class="text-xs text-red-600">{{ $breachedOpen }} {{ Str::plural('ticket', $breachedOpen) }}</div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                {{-- Categories --}}
                <div class="bg-white rounded-xl border border-slate-200 p-4">
                    <h2 class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-3">Categories</h2>
                    <div class="space-y-1 max-h-48 overflow-y-auto">
                        @forelse($categories as $c)
                            <button wire:click="$set('category', {{ $c->id }})"
                                    class="w-full flex items-center justify-between px-3 py-2 rounded-lg text-sm transition-colors {{ ($category ?? null) == $c->id ? 'bg-blue-50 text-blue-700 font-medium' : 'text-slate-600 hover:bg-slate-50' }}">
                                <span class="truncate">{{ $c->name }}</span>
                                @if($c->open_count > 0)
                                    <span class="ml-2 px-2 py-0.5 text-xs rounded-full {{ ($category ?? null) == $c->id ? 'bg-blue-200 text-blue-800' : 'bg-slate-100 text-slate-600' }}">{{ $c->open_count }}</span>
                                @endif
                            </button>
                        @empty
                            <p class="text-sm text-slate-400 px-3">No categories</p>
                        @endforelse
                    </div>
                    @if($category)
                        <button wire:click="$set('category', null)" class="mt-2 text-xs text-slate-500 hover:text-slate-700 px-3">Clear filter</button>
                    @endif
                </div>

                {{-- CSAT Summary --}}
                <div class="bg-white rounded-xl border border-slate-200 p-4">
                    <h2 class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-3">CSAT (60 days)</h2>
                    @php
                        $avg = $csatStats['avg'] ?? null;
                        $avgPct = $avg ? (($avg - 1) / 4) * 100 : 0;
                        $dist = $csatStats['distribution'] ?? ['good' => 0, 'neutral' => 0, 'poor' => 0];
                        $distTotal = max(1, $dist['good'] + $dist['neutral'] + $dist['poor']);
                    @endphp
                    <div class="flex items-center gap-3 mb-3">
                        <div class="text-2xl font-bold {{ $avg ? ($avg >= 3.5 ? 'text-emerald-600' : ($avg >= 2.5 ? 'text-amber-600' : 'text-red-600')) : 'text-slate-400' }}">
                            {{ $avg ? number_format($avg, 1) : '—' }}
                        </div>
                        <div class="flex-1">
                            <div class="h-2 bg-slate-100 rounded-full overflow-hidden">
                                <div class="h-full rounded-full transition-all {{ $avg >= 3.5 ? 'bg-emerald-500' : ($avg >= 2.5 ? 'bg-amber-500' : 'bg-red-500') }}" style="width: {{ $avgPct }}%"></div>
                            </div>
                        </div>
                    </div>
                    {{-- Distribution bar --}}
                    <div class="flex h-1.5 rounded-full overflow-hidden bg-slate-100 mb-2">
                        <div class="bg-emerald-500 transition-all" style="width: {{ ($dist['good'] / $distTotal) * 100 }}%"></div>
                        <div class="bg-amber-400 transition-all" style="width: {{ ($dist['neutral'] / $distTotal) * 100 }}%"></div>
                        <div class="bg-red-500 transition-all" style="width: {{ ($dist['poor'] / $distTotal) * 100 }}%"></div>
                    </div>
                    <div class="flex justify-between text-xs text-slate-500">
                        <span>{{ $csatStats['count'] ?? 0 }} responses</span>
                        <span>{{ $csatStats['responseRate'] ?? 0 }}% rate</span>
                    </div>
                </div>
            </aside>

            {{-- Main Content --}}
            <div class="xl:col-span-4">
                <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
                    {{-- Search & Filter Bar --}}
                    <div class="p-4 border-b border-slate-200">
                        <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                            {{-- Search --}}
                            <div class="relative flex-1">
                                <x-heroicon name="magnifying-glass" class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" />
                                <input type="text" placeholder="Search tickets..." wire:model.live.debounce.300ms="search"
                                       class="w-full pl-9 pr-4 py-2 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" />
                            </div>
                            {{-- Quick toggles --}}
                            <div class="flex items-center gap-2">
                                <label class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg text-sm cursor-pointer transition-colors {{ $mine ? 'bg-blue-50 text-blue-700 ring-1 ring-blue-200' : 'text-slate-600 hover:bg-slate-50' }}">
                                    <input type="checkbox" wire:model.live="mine" class="sr-only" />
                                    <x-heroicon name="user" class="w-4 h-4" />
                                    My Queue
                                </label>
                                <label class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg text-sm cursor-pointer transition-colors {{ $unassigned ? 'bg-slate-100 text-slate-700 ring-1 ring-slate-300' : 'text-slate-600 hover:bg-slate-50' }}">
                                    <input type="checkbox" wire:model.live="unassigned" class="sr-only" />
                                    Unassigned
                                </label>
                                <button @click="showFilters = !showFilters"
                                        class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg text-sm transition-colors"
                                        :class="showFilters ? 'bg-slate-100 text-slate-700' : 'text-slate-600 hover:bg-slate-50'">
                                    <x-heroicon name="funnel" class="w-4 h-4" />
                                    Filters
                                    <x-heroicon name="chevron-down" class="w-3 h-3 transition-transform" ::class="showFilters && 'rotate-180'" />
                                </button>
                                <button wire:click="$set('search',''); $set('status',''); $set('type',''); $set('priority',''); $set('category', null); $set('assignee', null); $set('mine', false); $set('unassigned', false); $set('escalationsOnly', false)"
                                        class="p-2 text-slate-400 hover:text-slate-600 rounded-lg hover:bg-slate-50 transition-colors" title="Reset all filters">
                                    <x-heroicon name="arrow-path" class="w-4 h-4" />
                                </button>
                            </div>
                        </div>

                        {{-- Expanded Filters --}}
                        <div x-show="showFilters" x-collapse class="mt-4 pt-4 border-t border-slate-100">
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                                <div>
                                    <label class="text-xs font-medium text-slate-500 mb-1 block">Type</label>
                                    <select wire:model.live="type" class="w-full text-sm border-slate-200 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                        <option value="">All Types</option>
                                        <option value="incident">Incident</option>
                                        <option value="request">Request</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="text-xs font-medium text-slate-500 mb-1 block">Priority</label>
                                    <select wire:model.live="priority" class="w-full text-sm border-slate-200 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                        <option value="">All Priorities</option>
                                        <option value="low">Low</option>
                                        <option value="medium">Medium</option>
                                        <option value="high">High</option>
                                        <option value="critical">Critical</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="text-xs font-medium text-slate-500 mb-1 block">Assignee</label>
                                    <select wire:model.live="assignee" class="w-full text-sm border-slate-200 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                        <option value="">All Agents</option>
                                        <option value="none">— Unassigned —</option>
                                        @foreach(($agents ?? collect()) as $a)
                                            <option value="{{ $a->id }}">{{ $a->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="flex items-end">
                                    <label class="inline-flex items-center gap-2 px-3 py-2 rounded-lg text-sm cursor-pointer transition-colors {{ $escalationsOnly ? 'bg-orange-50 text-orange-700 ring-1 ring-orange-200' : 'text-slate-600 hover:bg-slate-50 border border-slate-200' }}">
                                        <input type="checkbox" wire:model.live="escalationsOnly" class="sr-only" />
                                        <x-heroicon name="exclamation-triangle" class="w-4 h-4" />
                                        Escalations Only
                                    </label>
                                </div>
                            </div>
                        </div>

                        {{-- Active Filter Chips --}}
                        @php
                            $hasFilters = !empty($search) || !empty($type) || !empty($priority) || !empty($category) || !empty($assignee) || $mine || $unassigned || $escalationsOnly;
                        @endphp
                        @if($hasFilters)
                        <div class="flex flex-wrap gap-2 mt-3">
                            @if(!empty($search))
                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-slate-100 text-slate-700 text-xs">
                                    Search: "{{ Str::limit($search, 15) }}"
                                    <button wire:click="$set('search','')" class="hover:text-slate-900">&times;</button>
                                </span>
                            @endif
                            @if(!empty($type))
                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-blue-100 text-blue-700 text-xs">
                                    {{ ucfirst($type) }}
                                    <button wire:click="$set('type','')" class="hover:text-blue-900">&times;</button>
                                </span>
                            @endif
                            @if(!empty($priority))
                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-purple-100 text-purple-700 text-xs">
                                    {{ ucfirst($priority) }}
                                    <button wire:click="$set('priority','')" class="hover:text-purple-900">&times;</button>
                                </span>
                            @endif
                            @if(!empty($category))
                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-teal-100 text-teal-700 text-xs">
                                    Category
                                    <button wire:click="$set('category', null)" class="hover:text-teal-900">&times;</button>
                                </span>
                            @endif
                            @if(!empty($assignee))
                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-indigo-100 text-indigo-700 text-xs">
                                    Assignee
                                    <button wire:click="$set('assignee', null)" class="hover:text-indigo-900">&times;</button>
                                </span>
                            @endif
                            @if($mine)
                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-blue-100 text-blue-700 text-xs">
                                    My Queue
                                    <button wire:click="$set('mine', false)" class="hover:text-blue-900">&times;</button>
                                </span>
                            @endif
                            @if($unassigned)
                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-slate-200 text-slate-700 text-xs">
                                    Unassigned
                                    <button wire:click="$set('unassigned', false)" class="hover:text-slate-900">&times;</button>
                                </span>
                            @endif
                            @if($escalationsOnly)
                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-orange-100 text-orange-700 text-xs">
                                    Escalations
                                    <button wire:click="$set('escalationsOnly', false)" class="hover:text-orange-900">&times;</button>
                                </span>
                            @endif
                        </div>
                        @endif
                    </div>

                    {{-- Tickets Table --}}
                    <div class="overflow-x-auto" wire:poll.5s>
                        <table class="w-full">
                            <thead>
                                <tr class="bg-slate-50 border-b border-slate-200">
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider w-24">ID</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Ticket</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider w-28">Status</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider w-24">Priority</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider w-36">SLA</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider w-32">Activity</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($tickets as $t)
                                    @php
                                        $breached = $t->sla_due_at && now()->greaterThan($t->sla_due_at) && in_array($t->status, ['open','in_progress']);
                                        $minsLeft = $t->sla_due_at ? now()->diffInMinutes($t->sla_due_at, false) : null;
                                        $thresholds = ($t->sla_policy_id && isset($escalationsByPolicy[$t->sla_policy_id])) ? $escalationsByPolicy[$t->sla_policy_id] : [];
                                        $withinEsc = $minsLeft !== null && $minsLeft >= 0 && collect($thresholds)->first(fn($th) => $minsLeft <= (int) $th);
                                        $cs = $t->latestSubmittedCsat ?? null;
                                    @endphp
                                    <tr onclick="window.location='{{ route('itss.ticket.show', ['ticket' => $t->id]) }}'" class="group hover:bg-blue-50 transition-colors cursor-pointer {{ $breached ? 'bg-red-50/50' : '' }}">
                                        {{-- ID --}}
                                        <td class="px-4 py-3">
                                            <span class="font-mono text-sm text-slate-600">{{ $t->ticket_no }}</span>
                                        </td>

                                        {{-- Ticket (Subject + Requester + Category + Assignee) --}}
                                        <td class="px-4 py-3">
                                            <div class="flex flex-col gap-1">
                                                <span class="text-sm font-medium text-slate-800 group-hover:text-blue-600 transition-colors line-clamp-1">
                                                    {{ $t->subject }}
                                                </span>
                                                <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-xs">
                                                    <span class="text-slate-500">
                                                        {{ $t->requester->name ?? ($t->requester_name ? $t->requester_name.' (Guest)' : '—') }}
                                                    </span>
                                                    @if($t->category)
                                                        <span class="px-1.5 py-0.5 rounded bg-slate-100 text-slate-600">{{ $t->category->name }}</span>
                                                    @endif
                                                    @if($t->assignee)
                                                        <span class="inline-flex items-center gap-1 text-blue-600">
                                                            <x-heroicon name="user-circle" class="w-3.5 h-3.5" />
                                                            {{ $t->assignee->name }}
                                                        </span>
                                                    @else
                                                        <span class="text-slate-400 italic">Unassigned</span>
                                                    @endif
                                                    @if($t->type === 'request')
                                                        <span class="px-1.5 py-0.5 rounded bg-emerald-100 text-emerald-700">Request</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>

                                        {{-- Status --}}
                                        <td class="px-4 py-3">
                                            <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded-full text-xs font-medium
                                                {{ match($t->status) {
                                                    'open' => 'bg-blue-100 text-blue-700',
                                                    'in_progress' => 'bg-amber-100 text-amber-700',
                                                    'resolved' => 'bg-emerald-100 text-emerald-700',
                                                    'closed' => 'bg-slate-100 text-slate-600',
                                                    default => 'bg-slate-100 text-slate-600'
                                                } }}">
                                                <span class="w-1.5 h-1.5 rounded-full {{ match($t->status) {
                                                    'open' => 'bg-blue-500',
                                                    'in_progress' => 'bg-amber-500',
                                                    'resolved' => 'bg-emerald-500',
                                                    'closed' => 'bg-slate-400',
                                                    default => 'bg-slate-400'
                                                } }}"></span>
                                                {{ Str::of($t->status)->replace('_',' ')->title() }}
                                            </span>
                                        </td>

                                        {{-- Priority --}}
                                        <td class="px-4 py-3">
                                            <span class="px-2 py-1 rounded text-xs font-medium
                                                {{ match($t->priority) {
                                                    'low' => 'bg-slate-100 text-slate-600',
                                                    'medium' => 'bg-blue-50 text-blue-700',
                                                    'high' => 'bg-orange-100 text-orange-700',
                                                    'critical' => 'bg-red-100 text-red-700',
                                                    default => 'bg-slate-100 text-slate-600'
                                                } }}">
                                                {{ ucfirst($t->priority) }}
                                            </span>
                                        </td>

                                        {{-- SLA --}}
                                        <td class="px-4 py-3">
                                            @if($t->sla_due_at)
                                                <div class="flex flex-col gap-0.5">
                                                    <span class="text-xs font-medium {{ $breached ? 'text-red-600' : ($withinEsc ? 'text-orange-600' : 'text-slate-600') }}">
                                                        {{ $breached ? 'Breached' : $t->sla_due_at->diffForHumans() }}
                                                    </span>
                                                    @if($withinEsc && !$breached)
                                                        <span class="inline-flex items-center gap-1 text-xs text-orange-500">
                                                            <x-heroicon name="exclamation-triangle" class="w-3 h-3" />
                                                            Escalation
                                                        </span>
                                                    @endif
                                                </div>
                                            @else
                                                <span class="text-xs text-slate-400">—</span>
                                            @endif
                                        </td>

                                        {{-- Activity (CSAT + Time) --}}
                                        <td class="px-4 py-3">
                                            <div class="flex flex-col gap-0.5">
                                                <span class="text-xs text-slate-500">{{ $t->created_at->diffForHumans() }}</span>
                                                @if($cs)
                                                    @php
                                                        $rating = (string) $cs->rating;
                                                        $csatClasses = $rating === 'good' ? 'text-emerald-600' : ($rating === 'neutral' ? 'text-amber-600' : 'text-red-600');
                                                        $csatIcon = $rating === 'good' ? 'face-smile' : ($rating === 'neutral' ? 'face-frown' : 'face-frown');
                                                    @endphp
                                                    <button wire:click.stop="showCsat({{ $t->id }})" class="inline-flex items-center gap-1 text-xs {{ $csatClasses }} hover:underline">
                                                        <x-heroicon name="{{ $csatIcon }}" class="w-3.5 h-3.5" />
                                                        {{ ucfirst($rating) }}
                                                    </button>
                                                @endif
                                            </div>
                                        </td>

                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-4 py-16 text-center">
                                            <div class="flex flex-col items-center gap-3">
                                                <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center">
                                                    <x-heroicon name="ticket" class="w-8 h-8 text-slate-400" />
                                                </div>
                                                <div>
                                                    <p class="text-sm font-medium text-slate-600">No tickets found</p>
                                                    <p class="text-xs text-slate-400 mt-1">Try adjusting your filters or create a new ticket</p>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    <div class="px-4 py-3 border-t border-slate-200 bg-slate-50 relative">
                        <div class="flex items-center justify-between">
                            <div class="text-xs text-slate-500">
                                Showing {{ $tickets->firstItem() ?? 0 }} - {{ $tickets->lastItem() ?? 0 }} of {{ $tickets->total() }}
                            </div>
                            <div>
                                {{ $tickets->links() }}
                            </div>
                        </div>
                        {{-- Loading overlay --}}
                        <div wire:loading.delay.class.remove="hidden" wire:loading.class="flex" wire:target="search,status,type,priority,category,page,mine,unassigned,assignee,escalationsOnly" class="hidden absolute inset-0 bg-white/60 items-center justify-center rounded-xl">
                            <div class="flex items-center gap-2 px-4 py-2 bg-white rounded-lg shadow-sm border border-slate-200">
                                <div class="w-4 h-4 rounded-full border-2 border-blue-600 border-t-transparent animate-spin"></div>
                                <span class="text-sm text-slate-600">Loading...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Create Ticket Modal --}}
    @if($showCreate)
    <div x-data="{ show: @entangle('showCreate') }"
         x-show="show"
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto"
         x-transition:enter="ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        {{-- Backdrop --}}
        <div class="fixed inset-0 bg-slate-900/70 backdrop-blur-sm" @click="show = false"></div>

        {{-- Modal Panel --}}
        <div class="flex min-h-full items-center justify-center p-4">
            <div x-show="show"
                 x-transition:enter="ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="ease-in duration-150"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 @click.stop
                 class="relative w-full max-w-xl bg-white rounded-2xl shadow-2xl">

                {{-- Header --}}
                <div class="flex items-start justify-between p-5 border-b border-slate-100">
                    <div>
                        <h3 class="text-lg font-semibold text-slate-800">Create New Ticket</h3>
                        <p class="text-sm text-slate-500">Submit a support request</p>
                    </div>
                    <button @click="show = false" class="p-2 -m-2 text-slate-400 hover:text-slate-600 transition-colors rounded-lg hover:bg-slate-100">
                        <x-heroicon name="x-mark" class="w-5 h-5" />
                    </button>
                </div>

                {{-- Body --}}
                <div class="p-5 space-y-5 max-h-[calc(100vh-16rem)] overflow-y-auto">
                    {{-- Subject --}}
                    <div>
                        <label for="subject" class="block text-sm font-medium text-slate-700 mb-1.5">Subject <span class="text-red-500">*</span></label>
                        <input id="subject"
                               type="text"
                               wire:model.defer="subject"
                               placeholder="e.g., Cannot access student portal"
                               class="w-full px-3.5 py-2.5 text-sm border border-slate-200 rounded-lg bg-slate-50 placeholder-slate-400 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all" />
                        @error('subject') <p class="text-sm text-red-600 mt-1.5 flex items-center gap-1"><x-heroicon name="exclamation-circle" class="w-4 h-4" />{{ $message }}</p> @enderror
                    </div>

                    {{-- Description --}}
                    <div>
                        <label for="description" class="block text-sm font-medium text-slate-700 mb-1.5">Description <span class="text-red-500">*</span></label>
                        <textarea id="description"
                                  rows="4"
                                  wire:model.defer="description"
                                  placeholder="Please describe your issue in detail. Include any error messages, steps to reproduce, and what you were trying to accomplish..."
                                  class="w-full px-3.5 py-2.5 text-sm border border-slate-200 rounded-lg bg-slate-50 placeholder-slate-400 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all resize-none"></textarea>
                        @error('description') <p class="text-sm text-red-600 mt-1.5 flex items-center gap-1"><x-heroicon name="exclamation-circle" class="w-4 h-4" />{{ $message }}</p> @enderror
                    </div>

                    {{-- Category & Priority --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="category_id" class="block text-sm font-medium text-slate-700 mb-1.5">Category <span class="text-red-500">*</span></label>
                            <select id="category_id"
                                    wire:model.live="category_id"
                                    class="w-full px-3.5 py-2.5 text-sm border border-slate-200 rounded-lg bg-slate-50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all">
                                <option value="">Select category...</option>
                                @foreach($categories as $c)
                                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                                @endforeach
                            </select>
                            @error('category_id') <p class="text-sm text-red-600 mt-1.5 flex items-center gap-1"><x-heroicon name="exclamation-circle" class="w-4 h-4" />{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="priority_new" class="block text-sm font-medium text-slate-700 mb-1.5">Priority</label>
                            <select id="priority_new"
                                    wire:model.defer="priority_new"
                                    class="w-full px-3.5 py-2.5 text-sm border border-slate-200 rounded-lg bg-slate-50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all">
                                <option value="low">Low</option>
                                <option value="medium">Medium</option>
                                <option value="high">High</option>
                                <option value="critical">Critical</option>
                            </select>
                            @error('priority_new') <p class="text-sm text-red-600 mt-1.5 flex items-center gap-1"><x-heroicon name="exclamation-circle" class="w-4 h-4" />{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- Account Access Verification --}}
                    @php
                        $selectedCategory = isset($category_id) ? $categories->firstWhere('id', $category_id) : null;
                        $needsVerification = $selectedCategory && \Illuminate\Support\Str::lower($selectedCategory->name) === 'account access';
                    @endphp

                    @if($needsVerification)
                        <div class="p-4 rounded-xl border border-amber-200 bg-amber-50/50">
                            <div class="flex items-start gap-3 mb-4">
                                <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-amber-100 shrink-0">
                                    <x-heroicon name="shield-check" class="w-4 h-4 text-amber-600" />
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-amber-800">Identity Verification Required</p>
                                    <p class="text-xs text-amber-700 mt-0.5">Please provide ID (front & back) or Certificate of Registration</p>
                                </div>
                            </div>

                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Verification Method <span class="text-red-500">*</span></label>
                                    <select wire:model.live="verification_option"
                                            class="w-full px-3.5 py-2.5 text-sm border border-slate-200 rounded-lg bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all">
                                        <option value="">Select method...</option>
                                        <option value="id_card">School/Valid ID (front & back)</option>
                                        <option value="cor">Certificate of Registration (CoR)</option>
                                    </select>
                                    @error('verification_option') <p class="text-sm text-red-600 mt-1.5">{{ $message }}</p> @enderror
                                </div>

                                @if($verification_option === 'id_card')
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-slate-700 mb-1.5">ID - Front <span class="text-red-500">*</span></label>
                                            <input id="id_front_input_auth" type="file" accept="image/*" capture="environment" class="sr-only" wire:model="id_front" />
                                            <label for="id_front_input_auth" class="cursor-pointer flex flex-col items-center justify-center gap-2 p-4 rounded-lg border-2 border-dashed border-slate-300 bg-white hover:border-blue-400 hover:bg-blue-50/50 transition-colors">
                                                @if($id_front)
                                                    <img src="{{ $id_front->temporaryUrl() }}" alt="ID Front" class="w-full h-24 object-cover rounded-lg" />
                                                    <button type="button" wire:click="$set('id_front', null)" class="text-xs text-red-600 hover:underline">Remove</button>
                                                @else
                                                    <x-heroicon name="camera" class="w-6 h-6 text-slate-400" />
                                                    <span class="text-xs text-slate-500">Tap to capture</span>
                                                @endif
                                            </label>
                                            @error('id_front') <p class="text-sm text-red-600 mt-1.5">{{ $message }}</p> @enderror
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-slate-700 mb-1.5">ID - Back <span class="text-red-500">*</span></label>
                                            <input id="id_back_input_auth" type="file" accept="image/*" capture="environment" class="sr-only" wire:model="id_back" />
                                            <label for="id_back_input_auth" class="cursor-pointer flex flex-col items-center justify-center gap-2 p-4 rounded-lg border-2 border-dashed border-slate-300 bg-white hover:border-blue-400 hover:bg-blue-50/50 transition-colors">
                                                @if($id_back)
                                                    <img src="{{ $id_back->temporaryUrl() }}" alt="ID Back" class="w-full h-24 object-cover rounded-lg" />
                                                    <button type="button" wire:click="$set('id_back', null)" class="text-xs text-red-600 hover:underline">Remove</button>
                                                @else
                                                    <x-heroicon name="camera" class="w-6 h-6 text-slate-400" />
                                                    <span class="text-xs text-slate-500">Tap to capture</span>
                                                @endif
                                            </label>
                                            @error('id_back') <p class="text-sm text-red-600 mt-1.5">{{ $message }}</p> @enderror
                                        </div>
                                    </div>
                                @elseif($verification_option === 'cor')
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Certificate of Registration <span class="text-red-500">*</span></label>
                                        <input id="cor_input_auth" type="file" accept="image/*" capture="environment" class="sr-only" wire:model="cor_file" />
                                        <label for="cor_input_auth" class="cursor-pointer flex flex-col items-center justify-center gap-2 p-4 rounded-lg border-2 border-dashed border-slate-300 bg-white hover:border-blue-400 hover:bg-blue-50/50 transition-colors">
                                            @if($cor_file)
                                                <img src="{{ $cor_file->temporaryUrl() }}" alt="CoR" class="w-full h-32 object-cover rounded-lg" />
                                                <button type="button" wire:click="$set('cor_file', null)" class="text-xs text-red-600 hover:underline">Remove</button>
                                            @else
                                                <x-heroicon name="camera" class="w-6 h-6 text-slate-400" />
                                                <span class="text-xs text-slate-500">Tap to capture CoR front page</span>
                                            @endif
                                        </label>
                                        @error('cor_file') <p class="text-sm text-red-600 mt-1.5">{{ $message }}</p> @enderror
                                    </div>
                                @endif

                                <p class="text-xs text-slate-500 flex items-center gap-1">
                                    <x-heroicon name="lock-closed" class="w-3.5 h-3.5" />
                                    Files are stored securely. Max 4MB per ID, 6MB for CoR.
                                </p>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Footer --}}
                <div class="flex items-center justify-end gap-3 px-5 py-4 border-t border-slate-100 bg-slate-50/50 rounded-b-2xl">
                    <button @click="show = false"
                            wire:loading.attr="disabled"
                            class="px-4 py-2.5 text-sm font-medium text-slate-600 hover:text-slate-800 hover:bg-slate-100 rounded-lg transition-colors">
                        Cancel
                    </button>
                    <button wire:click="createTicket"
                            wire:loading.attr="disabled"
                            wire:loading.class="opacity-75 cursor-wait"
                            class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50">
                        <span wire:loading.remove wire:target="createTicket">Create Ticket</span>
                        <span wire:loading wire:target="createTicket" class="inline-flex items-center gap-2">
                            <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Creating...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- CSAT Comment Modal --}}
    <x-dialog-modal wire:model="showCsatModal">
        <x-slot name="title">
            <div class="flex items-center gap-2">
                <x-heroicon name="chat-bubble-left-right" class="w-5 h-5 text-blue-600" />
                CSAT Feedback
            </div>
        </x-slot>
        <x-slot name="content">
            @if($csatModal)
                <div class="space-y-3">
                    <div class="flex items-center justify-between p-3 bg-slate-50 rounded-lg">
                        <span class="text-sm text-slate-600">Ticket</span>
                        <span class="text-sm font-medium text-slate-800">{{ $csatModal['ticket_no'] }}</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-slate-50 rounded-lg">
                        <span class="text-sm text-slate-600">Rating</span>
                        @php
                            $rating = $csatModal['rating'];
                            $ratingClass = $rating === 'good' ? 'text-emerald-600 bg-emerald-100' : ($rating === 'neutral' ? 'text-amber-600 bg-amber-100' : 'text-red-600 bg-red-100');
                        @endphp
                        <span class="px-2 py-1 rounded text-sm font-medium {{ $ratingClass }}">{{ ucfirst($rating) }}</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-slate-50 rounded-lg">
                        <span class="text-sm text-slate-600">Submitted</span>
                        <span class="text-sm text-slate-800">{{ $csatModal['submitted_at'] }}</span>
                    </div>
                    @if(!empty($csatModal['comment']))
                        <div class="mt-3">
                            <span class="text-xs font-medium text-slate-500 uppercase tracking-wider">Comment</span>
                            <div class="mt-2 p-3 rounded-lg bg-slate-50 text-sm text-slate-800 whitespace-pre-line">{{ $csatModal['comment'] }}</div>
                        </div>
                    @endif
                </div>
            @else
                <div class="text-sm text-slate-500 text-center py-6">No CSAT details available.</div>
            @endif
        </x-slot>
        <x-slot name="footer">
            <x-button wire:click="$set('showCsatModal', false)">Close</x-button>
        </x-slot>
    </x-dialog-modal>
</div>
</div>
