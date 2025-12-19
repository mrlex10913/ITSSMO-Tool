<div wire:poll.5s="refreshData" x-data x-init="window.addEventListener('helpdesk-comment-created', (e) => { try { $wire.refreshData() } catch(_) {} })">
    <div class="sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-4">
            <h1 class="text-2xl font-semibold text-gray-900">Ticket {{ $ticket->ticket_no }}</h1>
            <a href="{{ route('itss.helpdesk') }}" class="text-blue-600 hover:underline">Back to list</a>
        </div>

        @if (session('success'))
            <div class="mb-4 rounded bg-green-50 text-green-800 px-4 py-3">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-medium text-gray-900 mb-2">{{ $ticket->subject }}</h2>
                    <div class="text-sm text-gray-500 mb-4">Created {{ $ticket->created_at->diffForHumans() }}</div>
                    <div class="prose max-w-none text-gray-800 whitespace-pre-line">{{ $ticket->description }}</div>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-md font-medium text-gray-900 mb-4">Attachments</h3>
                    @if($ticket->attachments->isEmpty())
                        <div class="text-sm text-gray-500">No attachments.</div>
                    @else
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @foreach($ticket->attachments->where('ticket_comment_id', null) as $att)
                                @php
                                    $isImage = str_starts_with((string) $att->mime, 'image/');
                                @endphp
                                <div class="border rounded-md p-3 flex items-center gap-3">
                                    @if($isImage)
                                        <a href="{{ route('attachments.preview', ['attachment' => $att->id]) }}" target="_blank" class="block w-20 h-20 overflow-hidden rounded">
                                            <img src="{{ route('attachments.preview', ['attachment' => $att->id]) }}" alt="{{ $att->filename }}" class="w-full h-full object-cover" />
                                        </a>
                                    @else
                                        <div class="w-20 h-20 flex items-center justify-center bg-gray-100 rounded">
                                            <span class="material-symbols-sharp text-gray-500">attach_file</span>
                                        </div>
                                    @endif
                                    <div class="flex-1 min-w-0">
                                        <div class="text-sm font-medium text-gray-900 truncate">{{ $att->filename }}</div>
                                        <div class="text-xs text-gray-500 uppercase">{{ $att->type }} @if($att->size) • {{ number_format($att->size / 1024, 0) }} KB @endif</div>
                                        <div class="mt-2 flex gap-3">
                                            <a href="{{ route('attachments.download', ['attachment' => $att->id]) }}" class="text-blue-600 hover:text-blue-800 text-sm">Download</a>
                                            @if($isImage)
                                                <a href="{{ route('attachments.preview', ['attachment' => $att->id]) }}" target="_blank" class="text-blue-600 hover:text-blue-800 text-sm">Preview</a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-md font-medium text-gray-900 mb-4">Comments</h3>
                    <div class="space-y-4 mb-6">
                        <div class="space-y-3 max-h-72 overflow-y-auto pr-1" x-ref="comments" x-data="{ k: {{ $comments->count() }} }" x-init="$nextTick(() => { if ($refs.comments) { $refs.comments.scrollTop = $refs.comments.scrollHeight } })" x-effect="k = {{ $comments->count() }}; $nextTick(() => { if ($refs.comments) { $refs.comments.scrollTop = $refs.comments.scrollHeight } })">
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
                                <div class="text-sm text-gray-500">No comments yet.</div>
                            @endforelse
                        </div>
                    </div>

                    <div class="space-y-3">
                        @if($isAgent)
                        <div class="flex items-center gap-2">
                            <div class="flex-1">
                                <label class="text-xs text-gray-500">Canned response</label>
                                <select class="w-full border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" x-data x-on:change="if ($event.target.value) { $wire.set('commentBody', $event.target.value) }">
                                    <option value="">— Select —</option>
                                    @foreach(\App\Models\Helpdesk\CannedResponse::orderBy('title')->get(['id','title','body']) as $cr)
                                        <option value="{{ $cr->body }}">{{ $cr->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="text-xs text-gray-500">Macros</label>
                                <select class="w-40 border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" x-data x-on:change="$wire.call('applyMacro', $event.target.value); $event.target.selectedIndex=0;">
                                    <option value="">Run macro…</option>
                                    @foreach(\App\Models\Helpdesk\TicketMacro::where('is_active', true)->orderBy('name')->get(['id','name']) as $m)
                                        <option value="{{ $m->id }}">{{ $m->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        @endif
                        <textarea rows="3" class="w-full border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" placeholder="Write a comment..." wire:model.defer="commentBody"></textarea>
                        @if($isAgent)
                            <label class="inline-flex items-center text-sm text-gray-600">
                                <input type="checkbox" class="rounded border-gray-300 mr-2" wire:model.defer="isInternal">
                                Internal note (visible to agents only)
                            </label>
                        @endif
                        <div class="text-right">
                            <x-button wire:click="addComment">Add Comment</x-button>
                        </div>
                        @error('commentBody') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-md font-medium text-gray-900 mb-4">Activity</h3>
                    @if(($activity ?? collect())->isEmpty())
                        <div class="text-sm text-gray-500">No activity yet.</div>
                    @else
                        <ul class="space-y-2 max-h-64 overflow-y-auto pr-1">
                            @foreach($activity as $a)
                <li class="text-sm text-gray-700 flex items-center justify-between">
                                    <div>
                    <span class="font-medium">{{ $a->user->name ?? 'System' }}</span>
                    <span class="text-gray-500">{{ $a->display_message }}</span>
                                    </div>
                                    <span class="text-xs text-gray-400">{{ $a->created_at?->diffForHumans() }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-md font-medium text-gray-900">Audit Trail</h3>
                        <span class="text-xs text-gray-500">Immutable change history</span>
                    </div>
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
                        <div class="text-sm text-gray-500">No audits yet.</div>
                    @else
                        <div class="max-h-64 overflow-y-auto pr-1">
                            <table class="min-w-full text-sm">
                                <thead>
                                    <tr class="text-left text-gray-600">
                                        <th class="py-2 pr-4">When</th>
                                        <th class="py-2 pr-4">Who</th>
                                        <th class="py-2 pr-4">Event</th>
                                        <th class="py-2 pr-4">Changes</th>
                                        <th class="py-2 pr-4">IP</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($audits as $log)
                                        <tr class="border-t">
                                            <td class="py-2 pr-4 text-gray-700">
                                                <div title="{{ $log->created_at?->toISOString() }}">
                                                    {{ optional($log->created_at)->timezone(config('app.timezone'))->format('M j, Y g:i A') }}
                                                </div>
                                                <div class="text-xs text-gray-400">{{ $log->created_at?->diffForHumans() }}</div>
                                            </td>
                                            <td class="py-2 pr-4">{{ optional($log->user)->name ?? 'System' }}</td>
                                            <td class="py-2 pr-4">{{ ucfirst($log->event) }}</td>
                                            <td class="py-2 pr-4">
                                                @php $changes = (array) ($log->changes ?? []); @endphp
                                                @if(empty($changes))
                                                    <span class="text-gray-400">—</span>
                                                @else
                                                    <ul class="ml-0 space-y-1">
                                                        @foreach($changes as $field => $diff)
                                                            @if(is_array($diff) && array_key_exists('from_hash', $diff))
                                                                <li class="flex items-center gap-2">
                                                                    <span class="text-gray-600 min-w-[160px]">{{ $labelFor($field) }}</span>
                                                                    <span class="text-xs text-gray-500">[hash]</span>
                                                                    <span class="text-gray-500">{{ substr($diff['from_hash'],0,8) }}</span>
                                                                    <span class="text-gray-400">→</span>
                                                                    <span class="font-medium">{{ substr($diff['to_hash'],0,8) }}</span>
                                                                    <span class="text-xs text-gray-500">({{ $diff['from_len'] }} → {{ $diff['to_len'] }} chars)</span>
                                                                </li>
                                                            @else
                                                                @php
                                                                    $from = is_array($diff) ? ($diff['from'] ?? null) : null;
                                                                    $to = is_array($diff) ? ($diff['to'] ?? null) : null;
                                                                @endphp
                                                                <li class="flex items-center gap-2">
                                                                    <span class="text-gray-600 min-w-[160px]">{{ $labelFor($field) }}</span>
                                                                    <span class="text-gray-500">{{ $formatVal($field, $from) }}</span>
                                                                    <span class="text-gray-400">→</span>
                                                                    <span class="font-medium">{{ $formatVal($field, $to) }}</span>
                                                                </li>
                                                            @endif
                                                        @endforeach
                                                    </ul>
                                                @endif
                                            </td>
                                            <td class="py-2 pr-4 text-gray-500">{{ $log->ip_address ?? '—' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            <div class="space-y-6">
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-md font-medium text-gray-900">Details</h3>
                        @if($isAgent)

                        @endif
                    </div>
                    <dl class="text-sm text-gray-700 space-y-2">
                        <div class="flex justify-between"><dt>Status</dt><dd class="font-medium">{{ \Illuminate\Support\Str::of($ticket->status)->replace('_',' ')->title() }}</dd></div>
                        <div class="flex justify-between"><dt>Type</dt><dd class="font-medium">{{ ucfirst($ticket->type ?? 'incident') }}</dd></div>
                        <div class="flex justify-between"><dt>Priority</dt><dd class="font-medium">{{ ucfirst($ticket->priority) }}</dd></div>
                        <div class="flex justify-between"><dt>Category</dt><dd class="font-medium">{{ $ticket->category->name ?? '—' }}</dd></div>
                        <div class="flex justify-between"><dt>Department</dt><dd class="font-medium">{{ optional($ticket->departmentRef)->name ?? ($ticket->department ? strtoupper($ticket->department) : '—') }}</dd></div>
                        <div class="flex justify-between items-start">
                            <dt>Verification</dt>
                            <dd class="font-medium text-right">
                                <div>
                                    {{ ucfirst($ticket->verification_status ?? 'pending') }}
                                    @if($ticket->verification_method)
                                        ({{ strtoupper($ticket->verification_method) }})
                                    @endif
                                </div>
                                @if($ticket->verified_by)
                                    <div class="text-xs text-gray-500">by {{ $ticket->verifiedBy->name ?? '—' }} • {{ optional($ticket->verified_at)->diffForHumans() }}</div>
                                @endif
                            </dd>
                        </div>
                        <div class="flex justify-between"><dt>Requester</dt><dd class="font-medium">
                            @if($ticket->requester)
                                {{ $ticket->requester->name }}
                            @elseif($ticket->requester_name)
                                {{ $ticket->requester_name }} (Guest)
                                @if($ticket->requester_email)
                                    <span class="block text-xs text-gray-500">{{ $ticket->requester_email }}</span>
                                @endif
                            @else
                                —
                            @endif
                        </dd></div>
                        <div class="flex justify-between"><dt>Assignee</dt><dd class="font-medium">{{ $ticket->assignee->name ?? 'Unassigned' }}</dd></div>
                        <div class="flex justify-between"><dt>SLA</dt>
                            <dd class="font-medium">
                                @php $breached = $ticket->sla_due_at && now()->greaterThan($ticket->sla_due_at) && in_array($ticket->status, ['open','in_progress']); @endphp
                                @if($ticket->sla_due_at)
                                    <span class="px-2 py-1 rounded text-xs {{ $breached ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                                        {{ $breached ? 'Breached' : 'Due ' . $ticket->sla_due_at->diffForHumans() }}
                                    </span>
                                    @if(!$breached && !empty($escalation))
                                        <span class="ml-2 px-2 py-1 rounded text-xs bg-orange-100 text-orange-800" title="Escalates when within {{ $escalation['threshold'] }} mins of breach">
                                            Escalates {{ $escalation['in_human'] }}
                                            @if(!empty($escalation['to']))
                                                to {{ $escalation['to'] }}
                                            @endif
                                        </span>
                                    @endif
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </dd>
                        </div>
                        <div class="flex justify-between"><dt>CSAT</dt>
                            <dd class="font-medium">
                                @php
                                    $badge = '';
                                    $label = '—';
                                    if (!empty($latestCsat) && $latestCsat->submitted_at) {
                                        $label = ucfirst($latestCsat->rating ?? '—');
                                        $badge = match($latestCsat->rating) {
                                            'good' => 'bg-green-100 text-green-800',
                                            'neutral' => 'bg-yellow-100 text-yellow-800',
                                            'poor' => 'bg-red-100 text-red-800',
                                            default => 'bg-gray-100 text-gray-800',
                                        };
                                    }
                                @endphp
                                @if(!empty($latestCsat) && $latestCsat->submitted_at)
                                    <span class="px-2 py-1 rounded text-xs {{ $badge }}">{{ $label }}</span>
                                    <span class="text-xs text-gray-500 ml-2">{{ $latestCsat->submitted_at->diffForHumans() }}</span>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </dd>
                        </div>
                    </dl>
                </div>

                @if($isAgent)
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-md font-medium text-gray-900 mb-4">Update</h3>
                    <div class="space-y-4">
                        <div>
                            <x-label for="status" value="Status" />
                            <select id="status" class="w-full border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" wire:model.defer="newStatus">
                                @foreach($statuses as $s)
                                    <option value="{{ $s }}">{{ \Illuminate\Support\Str::of($s)->replace('_',' ')->title() }}</option>
                                @endforeach
                            </select>
                            @error('newStatus') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <x-label for="type" value="Type" />
                            <select id="type" class="w-full border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" wire:model.defer="newType">
                                <option value="incident">Incident</option>
                                <option value="request">Request</option>
                            </select>
                            @error('newType') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <x-label for="assignee" value="Assignee" />
                            <select id="assignee" class="w-full border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" wire:model.defer="newAssigneeId">
                                <option value="">Unassigned</option>
                                @foreach($agents as $a)
                                    <option value="{{ $a->id }}">{{ $a->name }}</option>
                                @endforeach
                            </select>
                            @error('newAssigneeId') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div class="text-right">
                            <x-button wire:click="updateDetails">Save</x-button>
                        </div>

                        @if($isAgent)
                            <div class="pt-4 border-t mt-4">
                                <h4 class="text-sm font-medium text-gray-900 mb-2">Verification Actions</h4>
                                <div class="flex gap-2">
                                    <x-secondary-button wire:click="approveVerification" :disabled="$ticket->verification_status === 'approved'">Approve</x-secondary-button>
                                    <x-danger-button wire:click="rejectVerification" :disabled="$ticket->verification_status === 'rejected'">Reject</x-danger-button>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
