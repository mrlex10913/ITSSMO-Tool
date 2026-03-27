<div wire:poll.5s="refreshData"
    x-data="{
        showCountdown: false,
        countdown: 5,
        startCountdown() {
            this.showCountdown = true;
            this.countdown = 5;
            const timer = setInterval(() => {
                this.countdown--;
                if (this.countdown <= 0) {
                    clearInterval(timer);
                    window.location.href = '{{ route('itss.helpdesk') }}';
                }
            }, 1000);
        }
    }"
    x-init="window.addEventListener('helpdesk-comment-created', (e) => { try { $wire.refreshData() } catch(_) {} })"
    @ticket-closed.window="startCountdown()">

    {{-- Ticket Closed Countdown Modal --}}
    <div x-show="showCountdown" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
        <div class="fixed inset-0 bg-slate-900/70 backdrop-blur-sm"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-sm text-center p-8">
                <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-emerald-100 flex items-center justify-center">
                    <x-heroicon name="check-circle" class="w-10 h-10 text-emerald-600" />
                </div>
                <h3 class="text-lg font-semibold text-slate-800 mb-2">Ticket Closed</h3>
                <p class="text-sm text-slate-600 mb-6">This ticket has been closed successfully.</p>
                <div class="text-4xl font-bold text-blue-600 mb-4" x-text="countdown"></div>
                <p class="text-xs text-slate-500">Redirecting to helpdesk...</p>
                <a href="{{ route('itss.helpdesk') }}" class="mt-4 inline-flex items-center gap-1.5 text-sm text-blue-600 hover:text-blue-700 font-medium">
                    <x-heroicon name="arrow-left" class="w-4 h-4" />
                    Go now
                </a>
            </div>
        </div>
    </div>

    <div class="px-4 sm:px-6 lg:px-8 py-6">
        {{-- Page Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div class="flex items-start gap-4">
                <a href="{{ route('itss.helpdesk') }}" class="mt-1 p-2 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors" title="Back to list">
                    <x-heroicon name="arrow-left" class="w-5 h-5" />
                </a>
                <div>
                    <div class="flex items-center gap-3 flex-wrap">
                        <h1 class="text-xl font-semibold text-slate-800">{{ $ticket->ticket_no }}</h1>
                        @php
                            $statusColors = [
                                'open' => 'bg-blue-100 text-blue-700 ring-blue-600/20',
                                'in_progress' => 'bg-amber-100 text-amber-700 ring-amber-600/20',
                                'resolved' => 'bg-emerald-100 text-emerald-700 ring-emerald-600/20',
                                'closed' => 'bg-slate-100 text-slate-600 ring-slate-500/20',
                            ];
                            $priorityColors = [
                                'low' => 'bg-slate-100 text-slate-600',
                                'medium' => 'bg-yellow-100 text-yellow-700',
                                'high' => 'bg-orange-100 text-orange-700',
                                'critical' => 'bg-red-100 text-red-700',
                            ];
                            $breachedSla = $ticket->sla_due_at && now()->greaterThan($ticket->sla_due_at) && in_array($ticket->status, ['open','in_progress']);
                        @endphp
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium ring-1 ring-inset {{ $statusColors[$ticket->status] ?? $statusColors['open'] }}">
                            {{ \Illuminate\Support\Str::of($ticket->status)->replace('_',' ')->title() }}
                        </span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $priorityColors[$ticket->priority] ?? $priorityColors['medium'] }}">
                            {{ ucfirst($ticket->priority) }}
                        </span>
                        @if($breachedSla)
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700 ring-1 ring-inset ring-red-600/20 animate-pulse">
                                <x-heroicon name="exclamation-triangle" class="w-3.5 h-3.5" />
                                SLA Breached
                            </span>
                        @elseif($ticket->sla_due_at && in_array($ticket->status, ['open','in_progress']))
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-medium bg-emerald-50 text-emerald-700">
                                <x-heroicon name="clock" class="w-3.5 h-3.5" />
                                Due {{ $ticket->sla_due_at->diffForHumans() }}
                            </span>
                        @endif
                    </div>
                    <p class="text-sm text-slate-500 mt-1">{{ $ticket->subject }}</p>
                </div>
            </div>
            @if($isAgent)
            <div class="flex items-center gap-2">
                <button wire:click="$set('newAssigneeId', {{ auth()->id() }}); $wire.updateDetails()" class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-medium text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition-colors">
                    <x-heroicon name="user-plus" class="w-4 h-4" />
                    Assign to me
                </button>
            </div>
            @endif
        </div>

        @if (session('success'))
            <div class="mb-4 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 text-sm flex items-center gap-2">
                <x-heroicon name="check-circle" class="w-5 h-5 text-emerald-600 shrink-0" />
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-5">
                {{-- Description Card --}}
                <div class="bg-white rounded-xl border border-slate-200 p-5">
                    <div class="flex items-center gap-2 text-xs text-slate-500 mb-3">
                        <x-heroicon name="calendar" class="w-4 h-4" />
                        <span>Created {{ $ticket->created_at->format('M j, Y \a\t g:i A') }}</span>
                        <span class="text-slate-300">•</span>
                        <span>{{ $ticket->created_at->diffForHumans() }}</span>
                    </div>
                    <div class="prose prose-slate max-w-none text-slate-700 whitespace-pre-line text-sm leading-relaxed">{{ $ticket->description }}</div>
                </div>

                {{-- Attachments Card --}}
                <div class="bg-white rounded-xl border border-slate-200 p-5">
                    <h3 class="text-sm font-semibold text-slate-700 mb-4 flex items-center gap-2">
                        <x-heroicon name="paper-clip" class="w-4 h-4 text-slate-400" />
                        Attachments
                    </h3>
                    @if($ticket->attachments->isEmpty())
                        <div class="text-sm text-slate-400">No attachments.</div>
                    @else
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            @foreach($ticket->attachments->where('ticket_comment_id', null) as $att)
                                @php
                                    $isImage = str_starts_with((string) $att->mime, 'image/');
                                @endphp
                                <div class="border border-slate-200 rounded-lg p-3 flex items-center gap-3 hover:border-slate-300 transition-colors">
                                    @if($isImage)
                                        <a href="{{ route('attachments.preview', ['attachment' => $att->id]) }}" target="_blank" class="block w-16 h-16 overflow-hidden rounded-lg bg-slate-100 shrink-0">
                                            <img src="{{ route('attachments.preview', ['attachment' => $att->id]) }}" alt="{{ $att->filename }}" class="w-full h-full object-cover" />
                                        </a>
                                    @else
                                        <div class="w-16 h-16 flex items-center justify-center bg-slate-100 rounded-lg shrink-0">
                                            <x-heroicon name="document" class="w-6 h-6 text-slate-400" />
                                        </div>
                                    @endif
                                    <div class="flex-1 min-w-0">
                                        <div class="text-sm font-medium text-slate-700 truncate">{{ $att->filename }}</div>
                                        <div class="text-xs text-slate-500 uppercase">{{ $att->type }} @if($att->size) • {{ number_format($att->size / 1024, 0) }} KB @endif</div>
                                        <div class="mt-1.5 flex gap-3">
                                            <a href="{{ route('attachments.download', ['attachment' => $att->id]) }}" class="text-blue-600 hover:text-blue-700 text-xs font-medium">Download</a>
                                            @if($isImage)
                                                <a href="{{ route('attachments.preview', ['attachment' => $att->id]) }}" target="_blank" class="text-blue-600 hover:text-blue-700 text-xs font-medium">Preview</a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Comments Card --}}
                <div class="bg-white rounded-xl border border-slate-200 p-5">
                    <h3 class="text-sm font-semibold text-slate-700 mb-4 flex items-center gap-2">
                        <x-heroicon name="chat-bubble-left-right" class="w-4 h-4 text-slate-400" />
                        Comments
                    </h3>
                    <div class="space-y-4 mb-5">
                        <div class="space-y-3 max-h-80 overflow-y-auto pr-1" x-ref="comments" x-data="{ k: {{ $comments->count() }} }" x-init="$nextTick(() => { if ($refs.comments) { $refs.comments.scrollTop = $refs.comments.scrollHeight } })" x-effect="k = {{ $comments->count() }}; $nextTick(() => { if ($refs.comments) { $refs.comments.scrollTop = $refs.comments.scrollHeight } })">
                            @forelse($comments->reverse() as $c)
                                @php
                                    $name = $c->user->name ?? '—';
                                    $initials = '';
                                    foreach (preg_split('/\s+/', trim($name)) as $part) {
                                        if ($part !== '') { $initials .= mb_substr($part, 0, 1); }
                                    }
                                    // Align by role: agents on right (blue), requester on left (light blue)
                                    $isRequester = ($c->user_id === $ticket->requester_id);
                                    $alignRight = !$isRequester;
                                    $inline = $ticket->attachments->where('ticket_comment_id', $c->id);
                                @endphp
                                <div class="flex w-full items-end gap-2 {{ $alignRight ? 'justify-end' : '' }}">
                                    @unless($alignRight)
                                        <div class="w-6 h-6 rounded-full bg-slate-200 flex items-center justify-center text-[10px] font-semibold text-slate-600">{{ $initials }}</div>
                                    @endunless
                                    <div class="max-w-[75%] p-3 rounded-lg shadow-sm {{ $alignRight ? 'bg-blue-600 text-white rounded-br-none' : 'bg-blue-50 text-slate-900 rounded-bl-none' }}">
                                        <div class="flex items-center gap-2 mb-1 text-xs {{ $alignRight ? 'text-blue-50/80' : 'text-slate-600' }}">
                                            <span class="font-medium">{{ $name }}</span>
                                            <span>{{ $c->created_at?->format('Y-m-d H:i') }}</span>
                                            @if($c->is_internal)
                                                <span class="ml-auto text-[10px] uppercase tracking-wide {{ $alignRight ? 'text-yellow-200' : 'text-yellow-700' }}">Internal</span>
                                            @endif
                                        </div>
                                        <div class="text-sm whitespace-pre-line">{{ $c->body }}</div>
                                        @if($inline->count())
                                            <div class="mt-2 grid grid-cols-2 gap-2">
                                                @foreach($inline as $img)
                                                    <a href="{{ route('attachments.preview', ['attachment' => $img->id]) }}" target="_blank" class="block">
                                                        @if(\Illuminate\Support\Str::startsWith((string) $img->mime, 'image/'))
                                                            <img src="{{ route('attachments.preview', ['attachment' => $img->id]) }}" class="w-full h-24 object-cover rounded" />
                                                        @else
                                                            <div class="p-2 border rounded text-xs {{ $alignRight ? 'bg-blue-500/30 border-blue-300 text-white' : '' }}">{{ $img->filename }}</div>
                                                        @endif
                                                    </a>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                    @if($alignRight)
                                        <div class="w-6 h-6 rounded-full bg-blue-100 flex items-center justify-center text-[10px] font-semibold text-blue-700">{{ $initials }}</div>
                                    @endif
                                </div>

                            @empty
                                <div class="text-sm text-slate-400 text-center py-4">No comments yet.</div>
                            @endforelse
                        </div>
                    </div>

                    {{-- Comment Input Area --}}
                    <div class="space-y-3 pt-4 border-t border-slate-100">
                        @if($isAgent)
                        <div class="flex items-center gap-3">
                            <div class="flex-1">
                                <label class="text-xs font-medium text-slate-500 mb-1 block">Canned response</label>
                                <select class="w-full text-sm border-slate-200 rounded-lg bg-slate-50 focus:bg-white focus:border-blue-500 focus:ring-blue-500" x-data x-on:change="if ($event.target.value) { $wire.set('commentBody', $event.target.value) }">
                                    <option value="">— Select —</option>
                                    @foreach($cannedResponses as $cr)
                                        <option value="{{ $cr->body }}">{{ $cr->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="text-xs font-medium text-slate-500 mb-1 block">Macros</label>
                                <select class="w-40 text-sm border-slate-200 rounded-lg bg-slate-50 focus:bg-white focus:border-blue-500 focus:ring-blue-500" x-data x-on:change="$wire.call('applyMacro', $event.target.value); $event.target.selectedIndex=0;">
                                    <option value="">Run macro…</option>
                                    @foreach($macros as $m)
                                        <option value="{{ $m->id }}">{{ $m->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        @endif
                        <textarea rows="3" class="w-full text-sm border-slate-200 rounded-lg bg-slate-50 placeholder-slate-400 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 resize-none" placeholder="Write a comment..." wire:model.defer="commentBody"></textarea>
                        @if($isAgent)
                            <label class="inline-flex items-center gap-2 text-sm text-slate-600 cursor-pointer">
                                <input type="checkbox" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500" wire:model.defer="isInternal">
                                <span>Internal note <span class="text-slate-400">(visible to agents only)</span></span>
                            </label>
                        @endif
                        <div class="flex items-center justify-between">
                            @error('commentBody') <p class="text-sm text-red-600 flex items-center gap-1"><x-heroicon name="exclamation-circle" class="w-4 h-4" />{{ $message }}</p> @else <span></span> @enderror
                            <button wire:click="addComment" wire:loading.attr="disabled" wire:loading.class="opacity-75" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                <span wire:loading.remove wire:target="addComment">Add Comment</span>
                                <span wire:loading wire:target="addComment" class="inline-flex items-center gap-2">
                                    <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Sending...
                                </span>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Activity Card --}}
                <div class="bg-white rounded-xl border border-slate-200" x-data="{ open: false }">
                    <button @click="open = !open" class="w-full p-5 flex items-center justify-between text-left">
                        <h3 class="text-sm font-semibold text-slate-700 flex items-center gap-2">
                            <x-heroicon name="clock" class="w-4 h-4 text-slate-400" />
                            Activity
                        </h3>
                        <x-heroicon name="chevron-down" class="w-4 h-4 text-slate-400 transition-transform duration-200" ::class="{ 'rotate-180': open }" />
                    </button>
                    <div class="px-5 pb-5" x-show="open" x-collapse>
                    @if(($activity ?? collect())->isEmpty())
                        <div class="text-sm text-slate-400 text-center py-4">No activity yet.</div>
                    @else
                        <ul class="space-y-2 max-h-64 overflow-y-auto pr-1">
                            @foreach($activity as $a)
                                <li class="text-sm flex items-center justify-between py-1.5 border-b border-slate-100 last:border-0">
                                    <div class="text-slate-600">
                                        <span class="font-medium text-slate-700">{{ $a->user->name ?? 'System' }}</span>
                                        <span>{{ $a->display_message }}</span>
                                    </div>
                                    <span class="text-xs text-slate-400 shrink-0 ml-2">{{ $a->created_at?->diffForHumans() }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                    </div>
                </div>

                {{-- Audit Trail Card --}}
                <div class="bg-white rounded-xl border border-slate-200" x-data="{ open: false }">
                    <button @click="open = !open" class="w-full p-5 flex items-center justify-between text-left">
                        <div class="flex items-center gap-2">
                            <h3 class="text-sm font-semibold text-slate-700 flex items-center gap-2">
                                <x-heroicon name="document-text" class="w-4 h-4 text-slate-400" />
                                Audit Trail
                            </h3>
                            <span class="text-xs text-slate-400">Immutable change history</span>
                        </div>
                        <x-heroicon name="chevron-down" class="w-4 h-4 text-slate-400 transition-transform duration-200" ::class="{ 'rotate-180': open }" />
                    </button>
                    <div class="px-5 pb-5" x-show="open" x-collapse>
                    @php
                        use App\Models\Helpdesk\TicketAuditLog;
                        use App\Models\Department;
                        use App\Models\User as AppUser;
                        use App\Models\Helpdesk\SlaPolicy;

                        $audits = TicketAuditLog::with('user:id,name')
                            ->where('ticket_id', $ticket->id)
                            ->latest('id')
                            ->limit(50)
                            ->get();

                        // Collect referenced user IDs and department slugs from change sets to build lookups (avoid N+1)
                        $userIds = [];
                        $deptSlugs = [];
                        $slaPolicyIds = [];
                        foreach ($audits as $log) {
                            if ($log->user_id) { $userIds[] = (int) $log->user_id; }
                            $changes = (array) ($log->changes ?? []);
                            foreach ($changes as $field => $diff) {
                                if (str_ends_with($field, '_by') || str_ends_with($field, '_id')) {
                                    $from = is_array($diff) ? ($diff['from'] ?? null) : null;
                                    $to = is_array($diff) ? ($diff['to'] ?? null) : null;
                                    if (is_numeric($from)) { $userIds[] = (int) $from; }
                                    if (is_numeric($to)) { $userIds[] = (int) $to; }
                                }
                                // Track SLA policy IDs to map to policy names later
                                if ($field === 'sla_policy_id') {
                                    $from = is_array($diff) ? ($diff['from'] ?? null) : null;
                                    $to = is_array($diff) ? ($diff['to'] ?? null) : null;
                                    if (is_numeric($from)) { $slaPolicyIds[] = (int) $from; }
                                    if (is_numeric($to)) { $slaPolicyIds[] = (int) $to; }
                                }
                                if ($field === 'department') {
                                    $from = is_array($diff) ? ($diff['from'] ?? null) : null;
                                    $to = is_array($diff) ? ($diff['to'] ?? null) : null;
                                    if (is_string($from) && $from !== '') { $deptSlugs[] = strtolower($from); }
                                    if (is_string($to) && $to !== '') { $deptSlugs[] = strtolower($to); }
                                }
                            }
                        }
                        $userNames = empty($userIds) ? collect() : AppUser::whereIn('id', array_values(array_unique($userIds)))->pluck('name','id');
                        $deptNames = empty($deptSlugs) ? collect() : Department::whereIn('slug', array_values(array_unique($deptSlugs)))->pluck('name','slug');
                        $policyNames = empty($slaPolicyIds ?? []) ? collect() : SlaPolicy::whereIn('id', array_values(array_unique($slaPolicyIds)))->pluck('name','id');

                        // Helpers for labels and values
                        $labelFor = function (string $field): string {
                            $map = [
                                'status' => 'Status',
                                'priority' => 'Priority',
                                'type' => 'Type',
                                'category_id' => 'Category',
                                'assignee_id' => 'Assignee',
                                'requester_id' => 'Requester',
                                'verification_status' => 'Verification Status',
                                'verification_method' => 'Verification Method',
                                'verified_by' => 'Verified By',
                                'verified_at' => 'Verified At',
                                'closed_at' => 'Closed At',
                                'department' => 'Department',
                                'acknowledged_at' => 'Acknowledged At',
                                'acknowledged_by' => 'Acknowledged By',
                                'sla_policy_id' => 'SLA Policy Id',
                                'sla_due_at' => 'SLA Due At',
                            ];
                            return $map[$field] ?? \Illuminate\Support\Str::of($field)->replace('_',' ')->title();
                        };
                        $formatVal = function (string $field, $val) use ($userNames, $deptNames, $policyNames) {
                            if ($val === null || $val === '') { return '—'; }
                            // SLA Policy: map ID to policy name (include ID as hint)
                            if ($field === 'sla_policy_id') {
                                if (is_numeric($val)) {
                                    $name = $policyNames[(int) $val] ?? null;
                                    return $name ? ($name.' (#'.$val.')') : ('#'.$val);
                                }
                                return (string) $val;
                            }
                            // User IDs
                            if (in_array($field, ['assignee_id', 'requester_id', 'verified_by'])) {
                                return is_numeric($val) ? ($userNames[(int) $val] ?? ('#'.$val)) : (string) $val;
                            }
                            // Department slug to name (fallback to upper slug)
                            if ($field === 'department') {
                                $slug = strtolower((string) $val);
                                return $deptNames[$slug] ?? strtoupper((string) $val);
                            }
                            // Status, priority, type
                            if (in_array($field, ['status','priority','type','verification_status'])) {
                                return \Illuminate\Support\Str::of((string) $val)->replace('_',' ')->title();
                            }
                            if ($field === 'verification_method') {
                                return \Illuminate\Support\Str::of((string) $val)->replace('_',' ')->title();
                            }
                            // Timestamps: show friendly absolute with relative time
                            $isDateField = in_array($field, ['closed_at','verified_at','created_at','updated_at','acknowledged_at','sla_due_at']) || str_ends_with($field, '_at');
                            if ($isDateField) {
                                try {
                                    $dt = \Illuminate\Support\Carbon::parse($val);
                                    $abs = $dt->timezone(config('app.timezone'))->format('M j, Y g:i A');
                                    $rel = $dt->diffForHumans();
                                    return $abs.' ('.$rel.')';
                                } catch (\Throwable $e) { return (string) $val; }
                            }
                            return is_scalar($val) ? (string) $val : json_encode($val);
                        };
                    @endphp
                    @if($audits->isEmpty())
                        <div class="text-sm text-slate-400 text-center py-4">No audits yet.</div>
                    @else
                        <div class="max-h-64 overflow-y-auto pr-1">
                            <table class="min-w-full text-sm">
                                <thead>
                                    <tr class="text-left text-slate-500 text-xs uppercase tracking-wider">
                                        <th class="py-2 pr-4 font-medium">When</th>
                                        <th class="py-2 pr-4 font-medium">Who</th>
                                        <th class="py-2 pr-4 font-medium">Event</th>
                                        <th class="py-2 pr-4 font-medium">Changes</th>
                                        <th class="py-2 pr-4 font-medium">IP</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($audits as $log)
                                        <tr class="border-t border-slate-100">
                                            <td class="py-2 pr-4 text-slate-600">
                                                <div title="{{ $log->created_at?->toISOString() }}">
                                                    {{ optional($log->created_at)->timezone(config('app.timezone'))->format('M j, Y g:i A') }}
                                                </div>
                                                <div class="text-xs text-slate-400">{{ $log->created_at?->diffForHumans() }}</div>
                                            </td>
                                            <td class="py-2 pr-4 text-slate-700">{{ optional($log->user)->name ?? 'System' }}</td>
                                            <td class="py-2 pr-4 text-slate-600">{{ ucfirst($log->event) }}</td>
                                            <td class="py-2 pr-4">
                                                @php $changes = (array) ($log->changes ?? []); @endphp
                                                @if(empty($changes))
                                                    <span class="text-slate-400">—</span>
                                                @else
                                                    <ul class="ml-0 space-y-1">
                                                        @foreach($changes as $field => $diff)
                                                            @if(is_array($diff) && array_key_exists('from_hash', $diff))
                                                                <li class="flex items-center gap-2 text-slate-600">
                                                                    <span class="min-w-[140px]">{{ $labelFor($field) }}</span>
                                                                    <span class="text-xs text-slate-400">[hash]</span>
                                                                    <span class="text-slate-500">{{ substr($diff['from_hash'],0,8) }}</span>
                                                                    <span class="text-slate-400">→</span>
                                                                    <span class="font-medium text-slate-700">{{ substr($diff['to_hash'],0,8) }}</span>
                                                                </li>
                                                            @else
                                                                @php
                                                                    $from = is_array($diff) ? ($diff['from'] ?? null) : null;
                                                                    $to = is_array($diff) ? ($diff['to'] ?? null) : null;
                                                                @endphp
                                                                <li class="flex items-center gap-2 text-slate-600">
                                                                    <span class="min-w-[140px]">{{ $labelFor($field) }}</span>
                                                                    <span class="text-slate-500">{{ $formatVal($field, $from) }}</span>
                                                                    <span class="text-slate-400">→</span>
                                                                    <span class="font-medium text-slate-700">{{ $formatVal($field, $to) }}</span>
                                                                </li>
                                                            @endif
                                                        @endforeach
                                                    </ul>
                                                @endif
                                            </td>
                                            <td class="py-2 pr-4 text-slate-400">{{ $log->ip_address ?? '—' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                    </div>
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="space-y-5">
                {{-- Details Card --}}
                <div class="bg-white rounded-xl border border-slate-200" x-data="{ open: false }">
                    <button @click="open = !open" class="w-full p-5 flex items-center justify-between text-left">
                        <h3 class="text-sm font-semibold text-slate-700 flex items-center gap-2">
                            <x-heroicon name="information-circle" class="w-4 h-4 text-slate-400" />
                            Details
                        </h3>
                        <x-heroicon name="chevron-down" class="w-4 h-4 text-slate-400 transition-transform duration-200" ::class="{ 'rotate-180': open }" />
                    </button>
                    <dl class="text-sm space-y-3 px-5 pb-5" x-show="open" x-collapse>
                        @php
                            $detailStatusColors = [
                                'open' => 'bg-blue-100 text-blue-700',
                                'in_progress' => 'bg-amber-100 text-amber-700',
                                'resolved' => 'bg-emerald-100 text-emerald-700',
                                'closed' => 'bg-slate-100 text-slate-600',
                            ];
                            $detailPriorityColors = [
                                'low' => 'bg-slate-100 text-slate-600',
                                'medium' => 'bg-yellow-100 text-yellow-700',
                                'high' => 'bg-orange-100 text-orange-700',
                                'critical' => 'bg-red-100 text-red-700',
                            ];
                        @endphp
                        <div class="flex justify-between items-center py-2 border-b border-slate-100">
                            <dt class="text-slate-500">Status</dt>
                            <dd><span class="px-2 py-0.5 rounded text-xs font-medium {{ $detailStatusColors[$ticket->status] ?? 'bg-slate-100 text-slate-600' }}">{{ \Illuminate\Support\Str::of($ticket->status)->replace('_',' ')->title() }}</span></dd>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-slate-100">
                            <dt class="text-slate-500">Type</dt>
                            <dd class="font-medium text-slate-700">{{ ucfirst($ticket->type ?? 'incident') }}</dd>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-slate-100">
                            <dt class="text-slate-500">Priority</dt>
                            <dd><span class="px-2 py-0.5 rounded text-xs font-medium {{ $detailPriorityColors[$ticket->priority] ?? 'bg-slate-100 text-slate-600' }}">{{ ucfirst($ticket->priority) }}</span></dd>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-slate-100">
                            <dt class="text-slate-500">Category</dt>
                            <dd class="font-medium text-slate-700">{{ $ticket->category->name ?? '—' }}</dd>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-slate-100">
                            <dt class="text-slate-500">Department</dt>
                            <dd class="font-medium text-slate-700">{{ optional($ticket->departmentRef)->name ?? ($ticket->department ? strtoupper($ticket->department) : '—') }}</dd>
                        </div>
                        <div class="flex justify-between items-start py-2 border-b border-slate-100">
                            <dt class="text-slate-500">Verification</dt>
                            <dd class="font-medium text-right text-slate-700">
                                <div>
                                    {{ ucfirst($ticket->verification_status ?? 'pending') }}
                                    @if($ticket->verification_method)
                                        ({{ strtoupper($ticket->verification_method) }})
                                    @endif
                                </div>
                                @if($ticket->verified_by)
                                    <div class="text-xs text-slate-400">by {{ $ticket->verifiedBy->name ?? '—' }} • {{ optional($ticket->verified_at)->diffForHumans() }}</div>
                                @endif
                            </dd>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-slate-100">
                            <dt class="text-slate-500">Requester</dt>
                            <dd class="font-medium text-slate-700 text-right">
                            @if($ticket->requester)
                                {{ $ticket->requester->name }}
                            @elseif($ticket->requester_name)
                                {{ $ticket->requester_name }} (Guest)
                                @if($ticket->requester_email)
                                    <span class="block text-xs text-slate-400">{{ $ticket->requester_email }}</span>
                                @endif
                            @else
                                —
                            @endif
                        </dd>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-slate-100">
                            <dt class="text-slate-500">Assignee</dt>
                            <dd class="font-medium text-slate-700">{{ $ticket->assignee->name ?? 'Unassigned' }}</dd>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-slate-100">
                            <dt class="text-slate-500">SLA</dt>
                            <dd>
                                @php $breached = $ticket->sla_due_at && now()->greaterThan($ticket->sla_due_at) && in_array($ticket->status, ['open','in_progress']); @endphp
                                @if($ticket->sla_due_at)
                                    <span class="px-2 py-0.5 rounded text-xs font-medium {{ $breached ? 'bg-red-100 text-red-700' : 'bg-emerald-100 text-emerald-700' }}">
                                        {{ $breached ? 'Breached' : 'Due ' . $ticket->sla_due_at->diffForHumans() }}
                                    </span>
                                    @if(!$breached && !empty($escalation))
                                        <span class="ml-1 px-2 py-0.5 rounded text-xs font-medium bg-orange-100 text-orange-700" title="Escalates when within {{ $escalation['threshold'] }} mins of breach">
                                            Escalates {{ $escalation['in_human'] }}
                                        </span>
                                    @endif
                                @else
                                    <span class="text-slate-400">—</span>
                                @endif
                            </dd>
                        </div>
                        <div class="flex justify-between items-center py-2">
                            <dt class="text-slate-500">CSAT</dt>
                            <dd>
                                @php
                                    $badge = '';
                                    $label = '—';
                                    if (!empty($latestCsat) && $latestCsat->submitted_at) {
                                        $label = ucfirst($latestCsat->rating ?? '—');
                                        $badge = match($latestCsat->rating) {
                                            'good' => 'bg-emerald-100 text-emerald-700',
                                            'neutral' => 'bg-yellow-100 text-yellow-700',
                                            'poor' => 'bg-red-100 text-red-700',
                                            default => 'bg-slate-100 text-slate-600',
                                        };
                                    }
                                @endphp
                                @if(!empty($latestCsat) && $latestCsat->submitted_at)
                                    <span class="px-2 py-0.5 rounded text-xs font-medium {{ $badge }}">{{ $label }}</span>
                                    <span class="text-xs text-slate-400 ml-1">{{ $latestCsat->submitted_at->diffForHumans() }}</span>
                                @else
                                    <span class="text-slate-400">—</span>
                                @endif
                            </dd>
                        </div>
                    </dl>
                </div>

                {{-- Tags Section --}}
                @if($isAgent)
                <div class="bg-white rounded-xl border border-slate-200" x-data="{ open: false }">
                    <div class="flex items-center justify-between p-5 pb-0" :class="{ 'pb-5': !open }">
                        <button @click="open = !open" class="flex items-center gap-2 text-left">
                            <h3 class="text-sm font-semibold text-slate-700 flex items-center gap-2">
                                <x-heroicon name="tag" class="w-4 h-4 text-slate-400" />
                                Tags
                            </h3>
                            <x-heroicon name="chevron-down" class="w-4 h-4 text-slate-400 transition-transform duration-200" ::class="{ 'rotate-180': open }" />
                        </button>
                        <button wire:click="openTagModal" class="text-blue-600 hover:text-blue-700 text-xs font-medium flex items-center gap-1">
                            <x-heroicon name="plus" class="w-3.5 h-3.5" /> Manage
                        </button>
                    </div>
                    <div class="flex flex-wrap gap-2 px-5 pb-5 pt-3" x-show="open" x-collapse>
                        @forelse($tags as $tag)
                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium" style="background-color: {{ $tag->color }}20; color: {{ $tag->color }};">
                                {{ $tag->name }}
                                <button wire:click="removeTag({{ $tag->id }})" class="hover:opacity-70" title="Remove tag">
                                    <x-heroicon name="x-mark" class="w-3 h-3" />
                                </button>
                            </span>
                        @empty
                            <span class="text-sm text-slate-400">No tags</span>
                        @endforelse
                    </div>
                </div>
                @endif

                {{-- Time Tracking Section --}}
                @if($isAgent)
                <div class="bg-white rounded-xl border border-slate-200" x-data="{ open: false }">
                    <div class="flex items-center justify-between p-5 pb-0" :class="{ 'pb-5': !open }">
                        <button @click="open = !open" class="flex items-center gap-2 text-left">
                            <h3 class="text-sm font-semibold text-slate-700 flex items-center gap-2">
                                <x-heroicon name="clock" class="w-4 h-4 text-slate-400" />
                                Time Tracked
                            </h3>
                            <x-heroicon name="chevron-down" class="w-4 h-4 text-slate-400 transition-transform duration-200" ::class="{ 'rotate-180': open }" />
                        </button>
                        <button wire:click="openTimeModal" class="text-blue-600 hover:text-blue-700 text-xs font-medium flex items-center gap-1">
                            <x-heroicon name="plus" class="w-3.5 h-3.5" /> Log Time
                        </button>
                    </div>
                    <div class="px-5 pb-5 pt-3" x-show="open" x-collapse>
                    <div class="text-2xl font-bold text-slate-800 mb-3">
                        @php
                            $totalMins = $totalTimeMinutes ?? 0;
                            $hours = intdiv($totalMins, 60);
                            $mins = $totalMins % 60;
                        @endphp
                        {{ $hours > 0 ? $hours . 'h ' : '' }}{{ $mins }}m
                    </div>
                    @if(($timeEntries ?? collect())->isNotEmpty())
                        <div class="space-y-2 max-h-40 overflow-y-auto">
                            @foreach($timeEntries as $entry)
                                <div class="flex items-center justify-between text-sm border-b border-slate-100 pb-2 last:border-0">
                                    <div>
                                        <div class="font-medium text-slate-700">{{ $entry->formatted_duration }}</div>
                                        <div class="text-xs text-slate-500">
                                            {{ $entry->user->name ?? 'Unknown' }} • {{ $entry->work_date?->format('M j') }}
                                            @if($entry->is_billable) <span class="text-emerald-600">• Billable</span> @endif
                                        </div>
                                        @if($entry->description)
                                            <div class="text-xs text-slate-500 mt-1">{{ Str::limit($entry->description, 50) }}</div>
                                        @endif
                                    </div>
                                    <button wire:click="deleteTimeEntry({{ $entry->id }})" class="text-red-500 hover:text-red-600" title="Delete">
                                        <x-heroicon name="trash" class="w-4 h-4" />
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-sm text-slate-400">No time logged yet</div>
                    @endif
                    </div>
                </div>
                @endif

                {{-- Linked Tickets Section --}}
                @if($isAgent)
                <div class="bg-white rounded-xl border border-slate-200" x-data="{ open: false }">
                    <div class="flex items-center justify-between p-5 pb-0" :class="{ 'pb-5': !open }">
                        <button @click="open = !open" class="flex items-center gap-2 text-left">
                            <h3 class="text-sm font-semibold text-slate-700 flex items-center gap-2">
                                <x-heroicon name="link" class="w-4 h-4 text-slate-400" />
                                Linked Tickets
                            </h3>
                            <x-heroicon name="chevron-down" class="w-4 h-4 text-slate-400 transition-transform duration-200" ::class="{ 'rotate-180': open }" />
                        </button>
                        <button wire:click="openLinkModal" class="text-blue-600 hover:text-blue-700 text-xs font-medium flex items-center gap-1">
                            <x-heroicon name="plus" class="w-3.5 h-3.5" /> Link
                        </button>
                    </div>
                    <div class="px-5 pb-5 pt-3" x-show="open" x-collapse>
                    @if(($linkedTickets ?? collect())->isNotEmpty())
                        <div class="space-y-2 max-h-40 overflow-y-auto">
                            @foreach($linkedTickets as $link)
                                <div class="flex items-center justify-between text-sm border-b border-slate-100 pb-2 last:border-0">
                                    <div>
                                        <a href="{{ route('itss.ticket.show', ['ticket' => $link['ticket']->id]) }}" class="text-blue-600 hover:text-blue-700 font-medium">
                                            {{ $link['ticket']->ticket_no }}
                                        </a>
                                        <span class="text-xs text-slate-400 ml-1">({{ $link['link_label'] }})</span>
                                        <div class="text-xs text-slate-500">{{ Str::limit($link['ticket']->subject, 40) }}</div>
                                        <span class="text-xs px-1.5 py-0.5 rounded font-medium {{ $link['ticket']->status === 'closed' ? 'bg-slate-100 text-slate-600' : ($link['ticket']->status === 'resolved' ? 'bg-emerald-100 text-emerald-700' : 'bg-blue-100 text-blue-700') }}">
                                            {{ ucfirst(str_replace('_', ' ', $link['ticket']->status)) }}
                                        </span>
                                    </div>
                                    <button wire:click="removeLink({{ $link['link_id'] }})" class="text-red-500 hover:text-red-600" title="Remove link">
                                        <x-heroicon name="link-slash" class="w-4 h-4" />
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-sm text-slate-400">No linked tickets</div>
                    @endif
                    </div>
                </div>
                @endif

                {{-- Update Section --}}
                @if($isAgent)
                <div class="bg-white rounded-xl border border-slate-200 p-5">
                    <h3 class="text-sm font-semibold text-slate-700 mb-4 flex items-center gap-2">
                        <x-heroicon name="pencil-square" class="w-4 h-4 text-slate-400" />
                        Update Ticket
                    </h3>
                    <div class="space-y-4">
                        <div>
                            <label for="status" class="block text-sm font-medium text-slate-700 mb-1.5">Status</label>
                            <select id="status" class="w-full text-sm border-slate-200 rounded-lg bg-slate-50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20" wire:model.defer="newStatus">
                                @foreach($statuses as $s)
                                    <option value="{{ $s }}">{{ \Illuminate\Support\Str::of($s)->replace('_',' ')->title() }}</option>
                                @endforeach
                            </select>
                            @error('newStatus') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="type" class="block text-sm font-medium text-slate-700 mb-1.5">Type</label>
                            <select id="type" class="w-full text-sm border-slate-200 rounded-lg bg-slate-50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20" wire:model.defer="newType">
                                <option value="incident">Incident</option>
                                <option value="request">Request</option>
                            </select>
                            @error('newType') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="assignee" class="block text-sm font-medium text-slate-700 mb-1.5">Assignee</label>
                            <select id="assignee" class="w-full text-sm border-slate-200 rounded-lg bg-slate-50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20" wire:model.defer="newAssigneeId">
                                <option value="">Unassigned</option>
                                @foreach($agents as $a)
                                    <option value="{{ $a->id }}">{{ $a->name }}</option>
                                @endforeach
                            </select>
                            @error('newAssigneeId') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        @php $canSave = $ticket->verification_status === 'approved'; @endphp
                        <div class="relative" @if(!$canSave) title="Ticket must be approved before making changes" @endif>
                            <button wire:click="updateDetails" wire:loading.attr="disabled" wire:loading.class="opacity-75" {{ !$canSave ? 'disabled' : '' }} class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:bg-blue-600">
                                <span wire:loading.remove wire:target="updateDetails">Save Changes</span>
                                <span wire:loading wire:target="updateDetails" class="inline-flex items-center gap-2">
                                    <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Saving...
                                </span>
                            </button>
                            @if(!$canSave)
                                <p class="text-xs text-amber-600 mt-2 flex items-center gap-1">
                                    <x-heroicon name="exclamation-triangle" class="w-3.5 h-3.5" />
                                    Approve the ticket first to enable updates
                                </p>
                            @endif
                        </div>

                        @if($isAgent)
                            <div class="pt-4 border-t border-slate-100 mt-4">
                                <h4 class="text-sm font-medium text-slate-700 mb-3">Verification Actions</h4>
                                <div class="flex gap-2">
                                    <button wire:click="approveVerification" {{ $ticket->verification_status === 'approved' ? 'disabled' : '' }} class="flex-1 px-3 py-2 text-sm font-medium text-emerald-700 bg-emerald-50 hover:bg-emerald-100 rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed">Approve</button>
                                    <button wire:click="rejectVerification" {{ $ticket->verification_status === 'rejected' ? 'disabled' : '' }} class="flex-1 px-3 py-2 text-sm font-medium text-red-700 bg-red-50 hover:bg-red-100 rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed">Reject</button>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Tag Management Modal --}}
    @if($showTagModal)
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <div class="fixed inset-0 bg-slate-900/70 backdrop-blur-sm" wire:click="$set('showTagModal', false)"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md">
                <div class="flex items-center justify-between p-5 border-b border-slate-100">
                    <h3 class="text-lg font-semibold text-slate-800">Manage Tags</h3>
                    <button wire:click="$set('showTagModal', false)" class="p-2 -m-2 text-slate-400 hover:text-slate-600 rounded-lg hover:bg-slate-100 transition-colors">
                        <x-heroicon name="x-mark" class="w-5 h-5" />
                    </button>
                </div>

                <div class="p-5 space-y-4">
                    {{-- Quick Create Tag --}}
                    <div class="flex gap-2">
                        <input type="text" wire:model.defer="newTagName" placeholder="Create new tag..." class="flex-1 text-sm border-slate-200 rounded-lg bg-slate-50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20" />
                        <button wire:click="createQuickTag" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">Add</button>
                    </div>
                    @error('newTagName') <p class="text-sm text-red-600">{{ $message }}</p> @enderror

                    {{-- Available Tags --}}
                    <div class="border border-slate-200 rounded-lg p-3 max-h-48 overflow-y-auto">
                        <div class="text-xs text-slate-500 mb-2">Select tags:</div>
                        @forelse($availableTags as $tag)
                            <label class="flex items-center gap-2 py-1.5 cursor-pointer hover:bg-slate-50 rounded-lg px-2">
                                <input type="checkbox" wire:model="selectedTagIds" value="{{ $tag->id }}" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500" />
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium" style="background-color: {{ $tag->color }}20; color: {{ $tag->color }};">
                                    {{ $tag->name }}
                                </span>
                            </label>
                        @empty
                            <div class="text-sm text-slate-400">No tags available. Create one above.</div>
                        @endforelse
                    </div>
                </div>

                <div class="flex justify-end gap-3 px-5 py-4 border-t border-slate-100 bg-slate-50/50 rounded-b-2xl">
                    <button wire:click="$set('showTagModal', false)" class="px-4 py-2.5 text-sm font-medium text-slate-600 hover:text-slate-800 hover:bg-slate-100 rounded-lg transition-colors">Cancel</button>
                    <button wire:click="saveTags" class="px-4 py-2.5 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors">Save Tags</button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Time Entry Modal --}}
    @if($showTimeModal)
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <div class="fixed inset-0 bg-slate-900/70 backdrop-blur-sm" wire:click="$set('showTimeModal', false)"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md">
                <div class="flex items-center justify-between p-5 border-b border-slate-100">
                    <h3 class="text-lg font-semibold text-slate-800">Log Time</h3>
                    <button wire:click="$set('showTimeModal', false)" class="p-2 -m-2 text-slate-400 hover:text-slate-600 rounded-lg hover:bg-slate-100 transition-colors">
                        <x-heroicon name="x-mark" class="w-5 h-5" />
                    </button>
                </div>

                <div class="p-5 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Duration (minutes)</label>
                        <input type="number" wire:model.defer="timeEntryMinutes" min="1" max="1440" class="w-full text-sm border-slate-200 rounded-lg bg-slate-50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20" />
                        <div class="text-xs text-slate-500 mt-2 flex items-center gap-1">Quick:
                            <button type="button" wire:click="$set('timeEntryMinutes', 15)" class="px-2 py-0.5 bg-slate-100 hover:bg-slate-200 rounded text-slate-600 transition-colors">15m</button>
                            <button type="button" wire:click="$set('timeEntryMinutes', 30)" class="px-2 py-0.5 bg-slate-100 hover:bg-slate-200 rounded text-slate-600 transition-colors">30m</button>
                            <button type="button" wire:click="$set('timeEntryMinutes', 60)" class="px-2 py-0.5 bg-slate-100 hover:bg-slate-200 rounded text-slate-600 transition-colors">1h</button>
                            <button type="button" wire:click="$set('timeEntryMinutes', 120)" class="px-2 py-0.5 bg-slate-100 hover:bg-slate-200 rounded text-slate-600 transition-colors">2h</button>
                        </div>
                        @error('timeEntryMinutes') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Work Date</label>
                        <input type="date" wire:model.defer="timeEntryDate" class="w-full text-sm border-slate-200 rounded-lg bg-slate-50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20" />
                        @error('timeEntryDate') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Description (optional)</label>
                        <textarea wire:model.defer="timeEntryDescription" rows="2" class="w-full text-sm border-slate-200 rounded-lg bg-slate-50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 resize-none" placeholder="What did you work on?"></textarea>
                        @error('timeEntryDescription') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" wire:model.defer="timeEntryBillable" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500" />
                        <span class="text-sm text-slate-700">Billable time</span>
                    </label>
                </div>

                <div class="flex justify-end gap-3 px-5 py-4 border-t border-slate-100 bg-slate-50/50 rounded-b-2xl">
                    <button wire:click="$set('showTimeModal', false)" class="px-4 py-2.5 text-sm font-medium text-slate-600 hover:text-slate-800 hover:bg-slate-100 rounded-lg transition-colors">Cancel</button>
                    <button wire:click="saveTimeEntry" class="px-4 py-2.5 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors">Log Time</button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Link Ticket Modal --}}
    @if($showLinkModal)
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <div class="fixed inset-0 bg-slate-900/70 backdrop-blur-sm" wire:click="$set('showLinkModal', false)"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md">
                <div class="flex items-center justify-between p-5 border-b border-slate-100">
                    <h3 class="text-lg font-semibold text-slate-800">Link Ticket</h3>
                    <button wire:click="$set('showLinkModal', false)" class="p-2 -m-2 text-slate-400 hover:text-slate-600 rounded-lg hover:bg-slate-100 transition-colors">
                        <x-heroicon name="x-mark" class="w-5 h-5" />
                    </button>
                </div>

                <div class="p-5 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Ticket Number</label>
                        <input type="text" wire:model.defer="linkTicketNo" placeholder="e.g., TKT-00001234" class="w-full text-sm border-slate-200 rounded-lg bg-slate-50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20" />
                        @error('linkTicketNo') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Relationship</label>
                        <select wire:model.defer="linkType" class="w-full text-sm border-slate-200 rounded-lg bg-slate-50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">
                            @foreach($linkTypes as $type => $label)
                                <option value="{{ $type }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('linkType') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="flex justify-end gap-3 px-5 py-4 border-t border-slate-100 bg-slate-50/50 rounded-b-2xl">
                    <button wire:click="$set('showLinkModal', false)" class="px-4 py-2.5 text-sm font-medium text-slate-600 hover:text-slate-800 hover:bg-slate-100 rounded-lg transition-colors">Cancel</button>
                    <button wire:click="createLink" class="px-4 py-2.5 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors">Link Ticket</button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
