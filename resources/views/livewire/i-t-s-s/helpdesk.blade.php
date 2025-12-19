<div>
        <div x-data="{
            lastNotifiedId: null,
            lastNotifiedAt: 0,
            notifyNewTickets(e){
                const play = () => {
                    try { new Audio('{{ asset('sounds/notificationbell.wav') }}').play(); } catch(_) {}
                };
                // Request permission if needed, then show notification
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
                    // Expect Laravel Echo payload to include type and ticketId
                    if (detail?.type === 'created') {
                        const now = Date.now();
                        const id = detail?.ticketId || null;
                        // de-dupe if we just alerted for the same ticket within 8s
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
                // Also trigger bell if this was a newly created ticket via Reverb
                try { maybeNotifyFromEvent(ev); } catch(_) {}
            });
            window.addEventListener('tickets-new', (ev) => { notifyNewTickets(ev); });
        ">
    <div class="sm:px-6 lg:px-8">
        <h1 class="text-2xl font-semibold text-gray-900 mb-6">Helpdesk Support System</h1>

        @if (session('success'))
            <div class="mb-4 rounded bg-green-50 text-green-800 px-4 py-3">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            <!-- Sidebar with Stats -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <h2 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                        <x-heroicon name="chart-bar" class="w-5 h-5 text-blue-600 mr-2" />
                        Ticket Summary
                    </h2>

                    <div class="space-y-4">
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <div class="text-sm text-blue-700">Open</div>
                            <div class="text-2xl font-bold text-blue-800">{{ $stats['open'] ?? 0 }}</div>
                        </div>

                        <div class="bg-yellow-50 p-4 rounded-lg">
                            <div class="text-sm text-yellow-700">In Progress</div>
                            <div class="text-2xl font-bold text-yellow-800">{{ $stats['in_progress'] ?? 0 }}</div>
                        </div>

                        <div class="bg-green-50 p-4 rounded-lg">
                            <div class="text-sm text-green-700">Resolved</div>
                            <div class="text-2xl font-bold text-green-800">{{ $stats['resolved'] ?? 0 }}</div>
                        </div>

                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="text-sm text-gray-700">Total</div>
                            <div class="text-2xl font-bold text-gray-800">{{ $stats['total'] ?? 0 }}</div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                        <x-heroicon name="squares-2x2" class="w-5 h-5 text-blue-600 mr-2" />
                        Categories
                    </h2>

                    <div class="space-y-2">
                        @forelse($categories as $c)
                            <button class="w-full flex items-center justify-between p-2 rounded-lg hover:bg-gray-50"
                                    wire:click="$set('category', {{ $c->id }})">
                                <span class="text-sm {{ ($category ?? null) == $c->id ? 'font-semibold text-blue-700' : '' }}">{{ $c->name }}</span>
                                <span class="bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded-full">&nbsp;</span>
                            </button>
                        @empty
                            <div class="text-sm text-gray-500">No categories</div>
                        @endforelse
                        <button class="w-full text-left text-sm text-gray-500 hover:text-gray-700" wire:click="$set('category', null)">Clear filter</button>
                    </div>
                </div>

        <!-- CSAT (60d) -->
                <div class="bg-white rounded-lg shadow-md p-6 mt-6">
                    <h2 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                        <x-heroicon name="star" class="w-5 h-5 text-yellow-500 mr-2" />
            CSAT ({{ $csatStats['window'] ?? '60d' }})
                    </h2>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Average</span>
                            @php $avg = $csatStats['avg'] ?? null; @endphp
                            <span class="text-sm font-semibold {{ $avg ? ($avg >= 2.5 ? 'text-green-700' : ($avg >= 1.5 ? 'text-yellow-700' : 'text-red-700')) : 'text-gray-500' }}">
                                {{ $avg ? number_format($avg, 2) : '—' }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Responses</span>
                            <span class="text-sm font-medium text-gray-800">{{ $csatStats['count'] ?? 0 }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Response rate</span>
                            <span class="text-sm font-medium text-gray-800">{{ $csatStats['responseRate'] ?? 0 }}%</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="lg:col-span-3">
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="p-6 border-b border-gray-200">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                            <div class="flex items-center mb-4 md:mb-0">
                                <x-heroicon name="lifebuoy" class="w-6 h-6 text-blue-600 mr-2" />
                                <h2 class="text-lg font-medium text-gray-900">Support Tickets</h2>
                            </div>
                            <div class="flex items-center space-x-3">
                                <button class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors" wire:click="$set('showCreate', true)">
                                    <span class="flex items-center">
                                        <x-heroicon name="plus" class="w-4 h-4 mr-1" />
                                        New Ticket
                                    </span>
                                </button>
                            </div>
                        </div>
                        @php
                            $dueToday = \App\Models\Helpdesk\Ticket::query()
                                ->whereIn('status', ['open','in_progress'])
                                ->whereBetween('sla_due_at', [now()->startOfDay(), now()->endOfDay()])
                                ->count();
                            $breachedOpen = \App\Models\Helpdesk\Ticket::query()
                                ->whereIn('status', ['open','in_progress'])
                                ->whereNotNull('sla_due_at')
                                ->where('sla_due_at', '<', now())
                                ->count();
                        @endphp
                        <div class="mt-3 flex flex-wrap items-center gap-2">
                            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded bg-amber-50 text-amber-800 border border-amber-200">
                                <x-heroicon name="clock" class="w-4 h-4" /> Due today: <span class="font-semibold">{{ $dueToday }}</span>
                            </div>
                            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded bg-red-50 text-red-800 border border-red-200">
                                <x-heroicon name="exclamation-triangle" class="w-4 h-4" /> Breached: <span class="font-semibold">{{ $breachedOpen }}</span>
                            </div>
                            <button class="text-xs underline text-orange-800" wire:click="$set('escalationsOnly', true)">Show due-soon only</button>
                        </div>
                    </div>

                    <!-- Filters & Search -->
                    <div class="p-4 bg-gray-50 border-b border-gray-200">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                            <div class="md:flex-1 flex items-center gap-2">
                                <input type="text" placeholder="Search tickets..."
                                       wire:model.live.debounce.300ms="search"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <button x-data x-on:click="$wire.set('search',''); $wire.set('status',''); $wire.set('type',''); $wire.set('priority',''); $wire.set('category', null); $wire.set('assignee', null); $wire.set('mine', false); $wire.set('unassigned', false); $wire.set('escalationsOnly', false)"
                                        class="px-3 py-2 text-xs text-gray-600 hover:text-gray-800 border border-gray-300 rounded-lg">
                                    Reset
                                </button>
                            </div>
                            <div class="flex flex-col gap-2 md:items-end md:justify-end">
                                <div class="flex flex-wrap gap-2">
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
                                    <select wire:model.live="assignee" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                        <option value="">All Assignees</option>
                                        <option value="none">— Unassigned —</option>
                                        @foreach(($agents ?? collect()) as $a)
                                            <option value="{{ $a->id }}">{{ $a->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="flex flex-wrap items-center gap-2 mt-1">
                                    @php $statusVal = $status ?? ''; @endphp
                                    <button class="px-2 py-1 rounded text-xs {{ $statusVal === '' ? 'bg-blue-600 text-white' : 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-50' }}"
                                            wire:click="$set('status','')">All</button>
                    <button class="px-2 py-1 rounded text-xs {{ $statusVal === 'open' ? 'bg-blue-600 text-white' : 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-50' }}"
                        wire:click="$set('status','open')">Open</button>
                                    <button class="px-2 py-1 rounded text-xs {{ $statusVal === 'in_progress' ? 'bg-blue-600 text-white' : 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-50' }}"
                                            wire:click="$set('status','in_progress')">In Progress</button>
                                    <button class="px-2 py-1 rounded text-xs {{ $statusVal === 'resolved' ? 'bg-blue-600 text-white' : 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-50' }}"
                                            wire:click="$set('status','resolved')">Resolved</button>
                                    <button class="px-2 py-1 rounded text-xs {{ $statusVal === 'closed' ? 'bg-blue-600 text-white' : 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-50' }}"
                                            wire:click="$set('status','closed')">Closed</button>
                                    <label class="inline-flex items-center text-xs text-gray-700 ml-2">
                                        <input type="checkbox" class="mr-1 rounded border-gray-300" wire:model.live="mine">
                                        My queue
                                    </label>
                                    <label class="inline-flex items-center text-xs text-gray-700 ml-2">
                                        <input type="checkbox" class="mr-1 rounded border-gray-300" wire:model.live="unassigned">
                                        Unassigned
                                    </label>
                                    <label class="inline-flex items-center text-xs text-orange-800 ml-2">
                                        <input type="checkbox" class="mr-1 rounded border-gray-300 text-orange-600 focus:ring-orange-500" wire:model.live="escalationsOnly">
                                        Escalations only
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Active Filters Chips -->
                    <div class="px-4 py-2 bg-white border-b border-gray-200">
                        <div class="flex flex-wrap gap-2 text-xs">
                            @if(!empty($search))
                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded bg-gray-100 text-gray-700">Search: "{{ $search }}" <button class="ml-1" wire:click="$set('search','')">×</button></span>
                            @endif
                            @if(!empty($type))
                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded bg-blue-100 text-blue-800">Type: {{ ucfirst($type) }} <button class="ml-1" wire:click="$set('type','')">×</button></span>
                            @endif
                            @if(!empty($priority))
                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded bg-emerald-100 text-emerald-800">Priority: {{ ucfirst($priority) }} <button class="ml-1" wire:click="$set('priority','')">×</button></span>
                            @endif
                            @if(!empty($category))
                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded bg-purple-100 text-purple-800">Category <button class="ml-1" wire:click="$set('category', null)">×</button></span>
                            @endif
                            @if(!empty($assignee))
                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded bg-teal-100 text-teal-800">Assignee <button class="ml-1" wire:click="$set('assignee', null)">×</button></span>
                            @endif
                            @if($mine)
                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded bg-indigo-100 text-indigo-800">My queue <button class="ml-1" wire:click="$set('mine', false)">×</button></span>
                            @endif
                            @if($unassigned)
                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded bg-slate-100 text-slate-800">Unassigned <button class="ml-1" wire:click="$set('unassigned', false)">×</button></span>
                            @endif
                            @if($escalationsOnly)
                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded bg-orange-100 text-orange-800">Escalations only <button class="ml-1" wire:click="$set('escalationsOnly', false)">×</button></span>
                            @endif
                        </div>
                    </div>

                    <!-- Tickets List -->
                    <!-- Tickets List (polling) -->
                    <div class="relative overflow-auto max-h-[70vh]" wire:poll.5s>
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-white sticky top-0 z-10 shadow-sm">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider"></th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ticket ID</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/3 lg:w-[40%]">Subject</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Requestor</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Priority</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">CSAT</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SLA</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>

                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($tickets as $t)
                                    <tr class="odd:bg-white even:bg-slate-50 hover:bg-slate-100">
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                            <a href="{{ request()->routeIs('itss.*') ? route('itss.ticket.show', ['ticket' => $t->id]) : route('tickets.show', ['ticket' => $t->id]) }}" class="text-blue-600 hover:text-blue-800 inline-flex items-center group" title="View ticket" aria-label="View ticket">
                                                <x-heroicon name="magnifying-glass" class="w-5 h-5 transform transition-transform duration-150 ease-out group-hover:scale-125" />
                                                <span class="sr-only">View</span>
                                            </a>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-slate-700">{{ $t->ticket_no }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <span class="px-2 py-1 rounded-full text-xs font-medium {{ $t->type === 'incident' ? 'bg-rose-100 text-rose-800' : 'bg-emerald-100 text-emerald-800' }}">
                                                {{ ucfirst($t->type ?? 'incident') }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm w-2/3 lg:w-[40%] whitespace-normal break-words align-top">
                                            <div class="font-medium text-slate-900">{{ $t->subject }}</div>
                                            <div class="mt-0.5 text-[12px] text-slate-500 flex items-center gap-2">
                                                <span>{{ $t->category->name ?? '—' }}</span>
                                                @if($t->assignee)
                                                    <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded bg-blue-50 text-blue-700 whitespace-nowrap max-w-[220px] truncate" title="{{ $t->assignee->name }}">
                                                        <x-heroicon name="user" class="w-3.5 h-3.5" /> {{ $t->assignee->name }}
                                                    </span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                            {{ $t->requester->name ?? ($t->requester_name ? $t->requester_name.' (Guest)' : '—') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <span class="px-2 py-1 rounded-full text-xs font-medium
                                                {{ match($t->status) {
                                                    'open' => 'bg-blue-100 text-blue-800',
                                                    'in_progress' => 'bg-yellow-100 text-yellow-800',
                                                    'resolved' => 'bg-green-100 text-green-800',
                                                    'closed' => 'bg-gray-100 text-gray-800',
                                                    default => 'bg-gray-100 text-gray-800'
                                                } }}">
                                                {{ \Illuminate\Support\Str::of($t->status)->replace('_',' ')->title() }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <span class="px-2 py-1 rounded-full text-xs font-medium
                                                {{ match($t->priority) {
                                                    'low' => 'bg-gray-100 text-gray-800',
                                                    'medium' => 'bg-indigo-100 text-indigo-800',
                                                    'high' => 'bg-orange-100 text-orange-800',
                                                    'critical' => 'bg-red-100 text-red-800',
                                                    default => 'bg-gray-100 text-gray-800'
                                                } }}">
                                                {{ ucfirst($t->priority) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-xs">
                        @php $cs = $t->latestSubmittedCsat ?? null; @endphp
                                            @if($cs)
                                                @php
                            $rating = (string) $cs->rating;
                            $classes = $rating === 'good' ? 'bg-green-100 text-green-800' : ($rating === 'neutral' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800');
                            $label = $rating === 'good' ? 'Good' : ($rating === 'neutral' ? 'OK' : 'Poor');
                                                    $title = trim(($cs->comment ?? '') !== '' ? $cs->comment : $label);
                                                @endphp
                                                <div class="inline-flex items-center gap-2">
                                                    <span class="px-2 py-0.5 rounded {{ $classes }}" title="{{ $title }}">{{ $label }}</span>
                                                    @if(!empty($cs->comment))
                                                        <button type="button" class="text-gray-500 hover:text-gray-700" title="View CSAT comment" wire:click="showCsat({{ $t->id }})">
                                                            <x-heroicon name="chat-bubble-left-right" class="w-4 h-4" />
                                                        </button>
                                                    @endif
                                                </div>
                                            @else
                                                <span class="text-gray-400">—</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-700">
                                            @php
                                                $breached = $t->sla_due_at && now()->greaterThan($t->sla_due_at) && in_array($t->status, ['open','in_progress']);
                                                $minsLeft = $t->sla_due_at ? now()->diffInMinutes($t->sla_due_at, false) : null;
                                                $thresholds = ($t->sla_policy_id && isset($escalationsByPolicy[$t->sla_policy_id])) ? $escalationsByPolicy[$t->sla_policy_id] : [];
                                                $withinEsc = $minsLeft !== null && $minsLeft >= 0 && collect($thresholds)->first(fn($th) => $minsLeft <= (int) $th);
                                            @endphp
                                            @if($t->sla_due_at)
                                                <span class="px-2 py-1 rounded {{ $breached ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                                                    {{ $breached ? 'Breached' : 'Due ' . $t->sla_due_at->diffForHumans() }}
                                                </span>
                                                @if(!$breached && $withinEsc)
                                                    @php
                                                        $left = $minsLeft;
                                                        $friendly = $left < 60 ? ($left . 'm') : (floor($left/60) . 'h ' . ($left%60) . 'm');
                                                        $tooltip = 'Within escalation window (≤ ' . (collect($thresholds)->min() ?? '?') . ' mins before breach) — ' . $left . ' mins left';
                                                    @endphp
                                                    <span class="ml-2 inline-flex items-center gap-1 text-orange-700" title="{{ $tooltip }}">
                                                        <x-heroicon name="exclamation-triangle" class="w-4 h-4" /> Esc in {{ $friendly }}
                                                    </span>
                                                @endif
                                            @else
                                                <span class="text-gray-400">—</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-xs text-slate-500">{{ $t->created_at->diffForHumans() }}</td>

                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-16 text-center">
                                            <x-heroicon name="lifebuoy" class="w-16 h-16 text-gray-300 mb-4 block mx-auto" />
                                            <p class="text-gray-500 text-lg font-medium">No support tickets found</p>
                                            <p class="text-gray-400 text-sm mt-1">Create a new ticket to get started</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="px-6 py-4 relative">
                        {{ $tickets->links() }}
                        <div wire:loading.delay.class.remove="hidden" wire:loading.class="flex" wire:target="search,status,type,priority,category,page" class="hidden absolute inset-0 bg-white/60 items-center justify-center">
                            <div class="h-6 w-6 rounded-full border-2 border-blue-600 border-t-transparent animate-spin"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Ticket Modal -->
    <x-dialog-modal wire:model="showCreate">
        <x-slot name="title">Create New Ticket</x-slot>
        <x-slot name="content">
            <div class="space-y-4">
                <div>
                    <x-label for="subject" value="Subject" />
                    <x-input id="subject" type="text" class="mt-1 w-full" wire:model.defer="subject" />
                    @error('subject') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <x-label for="description" value="Description" />
                    <textarea id="description" rows="4" class="mt-1 w-full border-gray-300 rounded-md focus:border-blue-500 focus:ring-blue-500" wire:model.defer="description"></textarea>
                    @error('description') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <x-label for="category_id" value="Category" />
                        <select id="category_id" class="mt-1 w-full border-gray-300 rounded-md focus:border-blue-500 focus:ring-blue-500" wire:model.defer="category_id">
                            <option value="">Select category</option>
                            @foreach($categories as $c)
                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                        @error('category_id') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <x-label for="priority_new" value="Priority" />
                        <select id="priority_new" class="mt-1 w-full border-gray-300 rounded-md focus:border-blue-500 focus:ring-blue-500" wire:model.defer="priority_new">
                            <option value="low">Low</option>
                            <option value="medium">Medium</option>
                            <option value="high">High</option>
                            <option value="critical">Critical</option>
                        </select>
                        @error('priority_new') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                @php
                    $selectedCategory = isset($category_id) ? $categories->firstWhere('id', $category_id) : null;
                    $needsVerification = $selectedCategory && \Illuminate\Support\Str::lower($selectedCategory->name) === 'account access';
                @endphp

                @if($needsVerification)
                    <div class="mt-4 p-4 rounded border border-yellow-300 bg-yellow-50">
                        <p class="text-sm text-yellow-800 mb-3">For Account Access requests, please verify your identity: either capture ID (front and back) or a clear photo of your Certificate of Registration (CoR).</p>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="md:col-span-1">
                                <x-label value="Verification Method" />
                                <select class="mt-1 w-full border-gray-300 rounded-md focus:border-blue-500 focus:ring-blue-500" wire:model="verification_option">
                                    <option value="">Select method</option>
                                    <option value="id_card">School/Valid ID (front & back)</option>
                                    <option value="cor">Certificate of Registration (CoR)</option>
                                </select>
                                @error('verification_option') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                            </div>

                            @if($verification_option === 'id_card')
                                <!-- ID Front -->
                                <div class="space-y-2">
                                    <x-label value="ID - Front" />
                                    <input id="id_front_input_auth" type="file" accept="image/*" capture="environment" class="sr-only" wire:model="id_front" />
                                    <label for="id_front_input_auth" class="cursor-pointer w-full rounded-lg border border-gray-300 bg-white hover:bg-gray-50 p-3 text-sm text-gray-700 flex items-center justify-center gap-2">
                                        <x-heroicon name="camera" class="w-5 h-5" />
                                        <span>Open Camera</span>
                                    </label>
                                    @if($id_front)
                                        <img src="{{ $id_front->temporaryUrl() }}" alt="ID Front Preview" class="w-full h-40 object-cover rounded-lg border" />
                                        <button type="button" class="text-xs text-red-600 hover:underline" wire:click="$set('id_front', null)">Retake</button>
                                    @endif
                                    @error('id_front') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                                </div>

                                <!-- ID Back -->
                                <div class="space-y-2">
                                    <x-label value="ID - Back" />
                                    <input id="id_back_input_auth" type="file" accept="image/*" capture="environment" class="sr-only" wire:model="id_back" />
                                    <label for="id_back_input_auth" class="cursor-pointer w-full rounded-lg border border-gray-300 bg-white hover:bg-gray-50 p-3 text-sm text-gray-700 flex items-center justify-center gap-2">
                                        <x-heroicon name="camera" class="w-5 h-5" />
                                        <span>Open Camera</span>
                                    </label>
                                    @if($id_back)
                                        <img src="{{ $id_back->temporaryUrl() }}" alt="ID Back Preview" class="w-full h-40 object-cover rounded-lg border" />
                                        <button type="button" class="text-xs text-red-600 hover:underline" wire:click="$set('id_back', null)">Retake</button>
                                    @endif
                                    @error('id_back') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                                </div>
                            @elseif($verification_option === 'cor')
                                <!-- CoR Front Page -->
                                <div class="md:col-span-2 space-y-2">
                                    <x-label value="Certificate of Registration (Front Page)" />
                                    <input id="cor_input_auth" type="file" accept="image/*" capture="environment" class="sr-only" wire:model="cor_file" />
                                    <label for="cor_input_auth" class="cursor-pointer w-full rounded-lg border border-gray-300 bg-white hover:bg-gray-50 p-3 text-sm text-gray-700 flex items-center justify-center gap-2">
                                        <x-heroicon name="camera" class="w-5 h-5" />
                                        <span>Open Camera</span>
                                    </label>
                                    @if($cor_file)
                                        <img src="{{ $cor_file->temporaryUrl() }}" alt="CoR Preview" class="w-full h-40 object-cover rounded-lg border" />
                                        <button type="button" class="text-xs text-red-600 hover:underline" wire:click="$set('cor_file', null)">Retake</button>
                                    @endif
                                    @error('cor_file') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                                </div>
                            @endif
                        </div>
                        <p class="text-xs text-gray-600 mt-3">We store verification files securely and only ITSS staff can access them. Large files may be rejected; max 4MB per ID image, 6MB for CoR.</p>
                    </div>
                @endif
            </div>
        </x-slot>
        <x-slot name="footer">
            <x-secondary-button wire:click="$set('showCreate', false)" class="mr-2">Cancel</x-secondary-button>
            <x-button wire:click="createTicket">Create Ticket</x-button>
        </x-slot>
    </x-dialog-modal>

    <!-- CSAT Comment Modal -->
    <x-dialog-modal wire:model="showCsatModal">
        <x-slot name="title">CSAT Feedback</x-slot>
        <x-slot name="content">
            @if($csatModal)
                <div class="space-y-2">
                    <div class="text-sm text-gray-600">Ticket: <span class="font-medium text-gray-900">{{ $csatModal['ticket_no'] }}</span></div>
                    <div class="text-sm text-gray-600">Rating: <span class="font-medium text-gray-900">{{ $csatModal['rating'] }}</span></div>
                    <div class="text-sm text-gray-600">Submitted: <span class="font-medium text-gray-900">{{ $csatModal['submitted_at'] }}</span></div>
                    @if(!empty($csatModal['comment']))
                        <div class="mt-2 p-3 rounded bg-gray-50 text-sm text-gray-800 whitespace-pre-line">{{ $csatModal['comment'] }}</div>
                    @endif
                </div>
            @else
                <div class="text-sm text-gray-600">No CSAT details available.</div>
            @endif
        </x-slot>
        <x-slot name="footer">
            <x-button wire:click="$set('showCsatModal', false)">Close</x-button>
        </x-slot>
    </x-dialog-modal>
</div>
