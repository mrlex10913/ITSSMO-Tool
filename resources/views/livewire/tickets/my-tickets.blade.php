<div class="sm:px-6 lg:px-8" wire:poll.8s="refreshData">
    <!-- Header -->
    <div class="flex items-center justify-between py-4 border-b border-gray-200">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Support Center</h1>
            <p class="text-slate-500">Manage and track your support tickets</p>
        </div>
    </div>

    @if (session('success'))
        <div class="mt-4 mb-2 rounded bg-green-50 text-green-800 px-4 py-3">{{ session('success') }}</div>
    @endif

    <!-- Tabs -->
    <div
        x-data="{
            tab: (localStorage.getItem('helpdeskTab') || 'dashboard'),
            selectedId: null,
            prevCounts: {},
            toasts: [],
            notify(msg){
                // Browser Notification API, fallback to in-app toast
                try {
                    if (window.Notification && Notification.permission === 'granted') {
                        new Notification('Helpdesk', { body: msg });
                    } else if (window.Notification && Notification.permission !== 'denied') {
                        Notification.requestPermission().then(p => { if (p === 'granted') new Notification('Helpdesk', { body: msg }); });
                    }
                } catch(e) {}
                const id = Date.now() + Math.random();
                this.toasts.push({ id, msg });
                setTimeout(() => { this.toasts = this.toasts.filter(t => t.id !== id); }, 4000);
            },
            updateCounts(){
                try {
                    const curr = JSON.parse(this.$refs.counts.dataset.counts || '{}');
                    // Compare with previous counts
                    for (const [k,v] of Object.entries(curr)) {
                        const prev = this.prevCounts[k] || 0;
                        if (v > prev) {
                            // Find ticket number for friendly message
                            const btn = document.querySelector(`[data-tid='${k}']`);
                            const tno = btn ? btn.getAttribute('data-ticket-no') : k;
                            this.notify(`New comment on Ticket #${tno}`);
                        }
                    }
                    this.prevCounts = curr;
                } catch(e) {}
            },
        }"
        x-init="
            $watch('tab', v => localStorage.setItem('helpdeskTab', v));
            window.addEventListener('ticket-selected', e => { selectedId = e.detail.id; });
            // Real-time push from Echo (see layout): refresh data and let Alpine notify using unread delta
            window.addEventListener('helpdesk-comment-created', e => {
                try {
                    const d = e.detail || {};
                    // Toast only if it's not the currently open ticket (avoid duplicate toast while typing)
                    if (!selectedId || Number(selectedId) !== Number(d.ticketId)) {
                        const msg = d.ticketNo ? `New comment on Ticket #${d.ticketNo} by ${d.byName || 'someone'}` : 'New ticket comment';
                        notify(msg);
                    }
                    // Refresh to pull the latest comments and unread counts
                    $wire.refreshData();
                } catch(_) {}
            });
        "
        class="mt-6 relative"
    >
        <!-- Live counts bridge for Alpine; updates every Livewire refresh -->
    <span x-ref="counts" :data-counts='@json($unreadCounts)' class="hidden" x-effect="updateCounts()"></span>
        <div class="mb-6">
            <div class="grid w-full grid-cols-2 max-w-md bg-slate-50 rounded-lg p-1">
                <button @click="tab='dashboard'" :class="tab==='dashboard' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-900'" class="px-3 py-2 text-sm font-medium rounded-md">Dashboard</button>
                <button @click="tab='tickets'" :class="tab==='tickets' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-900'" class="px-3 py-2 text-sm font-medium rounded-md">My Tickets</button>
            </div>
        </div>

        <!-- Dashboard Tab -->
        <div x-show="tab==='dashboard'" x-cloak class="space-y-6">
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white border border-gray-200 rounded-lg p-6">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-sm font-medium text-slate-900">Open Tickets</h3>
                        <span class="h-4 w-4 rounded-full bg-yellow-500 inline-block"></span>
                    </div>
                    <div class="text-2xl font-bold text-slate-900">{{ $this->stats['open'] ?? 0 }}</div>
                    <p class="text-xs text-slate-500">Awaiting response</p>
                </div>
                <div class="bg-white border border-gray-200 rounded-lg p-6">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-sm font-medium text-slate-900">In Progress</h3>
                        <span class="h-4 w-4 rounded-full bg-blue-600 inline-block"></span>
                    </div>
                    <div class="text-2xl font-bold text-slate-900">{{ $this->stats['in_progress'] ?? 0 }}</div>
                    <p class="text-xs text-slate-500">Being worked on</p>
                </div>
                <div class="bg-white border border-gray-200 rounded-lg p-6">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-sm font-medium text-slate-900">Resolved</h3>
                        <span class="h-4 w-4 rounded-full bg-blue-400 inline-block"></span>
                    </div>
                    <div class="text-2xl font-bold text-slate-900">{{ $this->stats['resolved'] ?? 0 }}</div>
                    <p class="text-xs text-slate-500">Successfully closed</p>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="bg-white border border-gray-200 rounded-lg">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-slate-900">Recent Activity</h3>
                    <p class="text-sm text-slate-500">Your latest ticket updates</p>
                </div>
                    <div class="p-6">
                    <div class="space-y-4">
                        @php $shown = 0; @endphp
                        @forelse($tickets as $t)
                            @php if ($shown >= 3) { break; } $shown++; @endphp
                            <div class="flex items-center space-x-4 p-4 border border-gray-200 rounded-lg">
                                <span class="h-5 w-5 rounded-full bg-slate-200 inline-block"></span>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-slate-900 truncate">{{ $t->subject }}</p>
                                    <p class="text-sm text-slate-500">Updated {{ optional($t->updated_at)->format('Y-m-d') }}</p>
                                </div>
                                <span class="px-2 py-1 rounded text-xs font-medium {{ match($t->status){ 'open'=>'bg-green-600 text-white', 'in_progress'=>'bg-blue-600 text-white', 'resolved'=>'bg-blue-400 text-white', 'closed'=>'bg-gray-300 text-gray-800', default=>'bg-gray-300 text-gray-800' } }}">{{ \Illuminate\Support\Str::of($t->status)->replace('_',' ')->title() }}</span>
                            </div>
                        @empty
                            <div class="text-sm text-slate-500">No recent activity.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Tickets Tab -->
        <div x-show="tab==='tickets'" x-cloak class="space-y-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Tickets List -->
                <div class="bg-white border border-gray-200 rounded-lg">
                    <div class="p-6 border-b border-gray-200">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <h3 class="text-lg font-semibold text-slate-900">All Tickets</h3>
                                <p class="text-sm text-slate-500">Click a ticket to view details</p>
                            </div>
                            <x-button wire:click="$set('showCreate', true)" class="bg-blue-600 hover:bg-blue-700 text-white">
                                <span class="mr-2">＋</span>
                                New Ticket
                            </x-button>
                        </div>
                        <div class="mt-4 grid grid-cols-1 md:grid-cols-4 gap-2">
                            <input type="text" placeholder="Search my tickets..." wire:model.live.debounce.300ms="search" class="col-span-1 md:col-span-1 px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <select wire:model.live="status" class="px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">All Status</option>
                                <option value="open">Open</option>
                                <option value="in_progress">In Progress</option>
                                <option value="resolved">Resolved</option>
                                <option value="closed">Closed</option>
                            </select>
                            <input type="date" wire:model.live="createdOn" class="px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
                            <select wire:model.live="priority" class="px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">All Priorities</option>
                                <option value="low">Low</option>
                                <option value="medium">Medium</option>
                                <option value="high">High</option>
                                <option value="critical">Critical</option>
                            </select>
                        </div>
                        <div class="mt-2 text-[12px] text-slate-500">
                            <span>Total tickets: <span class="font-semibold text-slate-700">{{ $this->stats['total'] ?? 0 }}</span></span>
                            <span class="mx-2">•</span>
                            <span>Created today: <span class="font-semibold text-slate-700">{{ $this->stats['created_today'] ?? 0 }}</span></span>
                        </div>
                    </div>
                    <div class="p-6">
                        <!-- Scrollable tickets list -->
                        <div class="max-h-[65vh] md:max-h-[70vh] overflow-y-auto pr-2 space-y-3">
                            @php
                                $routeBase = request()->routeIs('pamo.*') ? 'pamo.tickets.show' : (request()->routeIs('bfo.*') ? 'bfo.tickets.show' : 'tickets.show');
                            @endphp
                            @forelse($tickets as $t)
                                <button type="button" wire:click="selectTicket({{ $t->id }})" class="w-full text-left p-4 border rounded-lg transition-colors hover:bg-slate-50 relative {{ strtolower($t->priority) === 'high' ? 'border-red-200' : 'border-gray-200' }}" data-tid="{{ $t->id }}" data-ticket-no="{{ $t->ticket_no }}">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-2 mb-2">
                                                <span class="text-sm font-mono text-slate-500">{{ $t->ticket_no }}</span>
                                                <span class="px-2 py-0.5 rounded text-xs font-medium {{ match($t->status){ 'open'=>'bg-green-600 text-white', 'in_progress'=>'bg-blue-600 text-white', 'resolved'=>'bg-blue-400 text-white', 'closed'=>'bg-gray-300 text-gray-800', default=>'bg-gray-300 text-gray-800' } }}">{{ \Illuminate\Support\Str::of($t->status)->replace('_',' ')->title() }}</span>
                                                <span class="px-2 py-0.5 rounded text-xs font-medium {{ match($t->priority){ 'low'=>'bg-blue-100 text-blue-800', 'medium'=>'bg-yellow-100 text-yellow-800', 'high'=>'bg-red-100 text-red-800', 'critical'=>'bg-red-100 text-red-800', default=>'bg-gray-100 text-gray-800' } }}">{{ ucfirst($t->priority) }}</span>
                                                @php $hasCsat = (bool) optional($t->latestSubmittedCsat)->submitted_at; @endphp
                                                @if(in_array($t->status, ['resolved','closed']) && !$hasCsat)
                                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-amber-100 text-amber-800">Feedback needed</span>
                                                @endif
                                            </div>
                                            <h4 class="text-sm font-medium text-slate-900 mb-1 truncate">{{ $t->subject }}</h4>
                                            <p class="text-xs text-slate-500 mb-2">{{ $t->category->name ?? '—' }}</p>
                                            <div class="flex items-center gap-4 text-xs text-slate-500">
                                                <span>Created {{ optional($t->created_at)->format('Y-m-d') }}</span>
                                                <span>{{ optional($t->updated_at)->diffForHumans() }}</span>
                                            </div>
                                        </div>
                    <template x-if="selectedId !== '{{ $t->id }}' && {{ ($unreadCounts[$t->id] ?? 0) }} > 0">
                                            <span
                                                class="absolute -top-1 -right-1 inline-flex items-center justify-center rounded-full bg-red-600 text-white text-[11px] h-5 min-w-[1.25rem] px-1.5 font-bold ring-2 ring-white shadow"
                        title="{{ ($unreadCounts[$t->id] ?? 0) }} unread"
                        x-text="{{ ($unreadCounts[$t->id] ?? 0) }} > 9 ? '9+' : '{{ ($unreadCounts[$t->id] ?? 0) }}'"
                                            ></span>
                                        </template>
                                    </div>
                                </button>
                            @empty
                                <div class="text-sm text-slate-500">No tickets yet.</div>
                            @endforelse
                        </div>
                        <div class="mt-4">{{ $tickets->links() }}</div>
                    </div>
                </div>
                <!-- Toasts -->
                <div class="fixed right-4 bottom-4 z-50 space-y-2">
                    <template x-for="t in toasts" :key="t.id">
                        <div class="bg-slate-900 text-white text-sm px-4 py-2 rounded shadow-lg ring-1 ring-slate-700/50">
                            <span x-text="t.msg"></span>
                        </div>
                    </template>
                </div>

                <!-- Ticket Details Placeholder -->
                <div class="bg-white border rounded-lg {{ (strtolower(optional($this->selectedTicket)->priority ?? '') === 'high') ? 'border-red-200' : 'border-gray-200' }}">
                    <div class="p-6 border-b border-gray-200">
                        @php $selHeader = $this->selectedTicket ?? null; @endphp
                        <div class="flex items-start justify-between">
                            <div>
                                <h3 class="text-lg font-semibold text-slate-900">
                                    {{ $selHeader ? 'Ticket #'.($selHeader->ticket_no ?? $selHeader->id) : 'Select a Ticket' }}
                                </h3>
                                <p class="text-sm text-slate-500">{{ $selHeader ? 'View details and comment' : 'Choose a ticket from the list to view details' }}</p>
                            </div>
                            @if($selHeader)
                                <div class="text-right text-xs text-slate-500">
                                    <div>Created {{ optional($selHeader->created_at)->format('Y-m-d') }}</div>
                                    <div>Updated {{ optional($selHeader->updated_at)->diffForHumans() }}</div>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="p-6">
                        @php $sel = $this->selectedTicket ?? null; @endphp
                        @if($sel)
                            <div class="space-y-6">


                                <!-- Title/Meta -->
                <div class="text-center space-y-2">
                                    <h4 class="font-medium text-slate-900">{{ $sel->subject }}</h4>
                                    <p class="text-sm text-slate-500">{{ \Illuminate\Support\Str::limit($sel->description, 180) }}</p>
                                    <div class="flex items-center justify-center gap-3">
                    <span class="px-2 py-1 rounded text-xs font-medium {{ match($sel->status){ 'open'=>'bg-green-600 text-white', 'in_progress'=>'bg-blue-600 text-white', 'resolved'=>'bg-blue-400 text-white', 'closed'=>'bg-gray-300 text-gray-800', default=>'bg-gray-300 text-gray-800' } }}">{{ \Illuminate\Support\Str::of($sel->status)->replace('_',' ')->title() }}</span>
                    <span class="px-2 py-1 rounded text-xs font-medium {{ match($sel->priority){ 'low'=>'bg-blue-100 text-blue-800', 'medium'=>'bg-yellow-100 text-yellow-800', 'high'=>'bg-red-100 text-red-800', 'critical'=>'bg-red-100 text-red-800', default=>'bg-gray-100 text-gray-800' } }}">{{ ucfirst($sel->priority) }}</span>
                                        <span class="text-xs text-slate-500">{{ $sel->category->name ?? '—' }}</span>
                                    </div>
                                </div>

                                <!-- Feedback Required banner when Closed and no CSAT yet -->
                                @php $selHasCsat = (bool) optional($sel->latestSubmittedCsat)->submitted_at; @endphp
                                @if($sel && $sel->status === 'closed' && !$selHasCsat)
                                    <div class="p-3 rounded border border-amber-300 bg-amber-50 text-amber-800 text-sm text-center">
                                        Your ticket is closed. Please provide your feedback to complete the process.
                                    </div>
                                @endif

                                <!-- Comments -->
                                <div class="space-y-4">
                                    <h5 class="font-medium text-slate-900 text-center">Comments</h5>
                                    <div class="space-y-3 max-h-64 overflow-y-auto pr-1" x-ref="comments" x-data="{ k: {{ $this->selectedComments->count() }} }" x-init="$nextTick(() => { if ($refs.comments) { $refs.comments.scrollTop = $refs.comments.scrollHeight } })" x-effect="k = {{ $this->selectedComments->count() }}; $nextTick(() => { if ($refs.comments) { $refs.comments.scrollTop = $refs.comments.scrollHeight } })">
                                        @forelse($this->selectedComments as $c)
                                            @php
                                                $name = $c->user?->name ?? 'You';
                                                $initials = '';
                                                foreach (preg_split('/\s+/', trim($name)) as $part) {
                                                    if ($part !== '') { $initials .= mb_substr($part, 0, 1); }
                                                }
                                                $isMine = ($c->user?->id === auth()->id());
                                                $inline = $sel->attachments->where('ticket_comment_id', $c->id);
                                            @endphp
                                            <div class="flex items-end gap-2 {{ $isMine ? 'justify-end' : '' }}">
                                                @unless($isMine)
                                                    <div class="w-6 h-6 rounded-full bg-slate-200 flex items-center justify-center text-[10px] font-semibold text-slate-600">{{ $initials }}</div>
                                                @endunless
                                                <div class="max-w-[75%] p-3 rounded-lg {{ $isMine ? 'bg-blue-600 text-white rounded-br-none' : 'bg-slate-100 text-slate-900 rounded-bl-none' }}">
                                                    <div class="flex items-center gap-2 mb-1 text-xs {{ $isMine ? 'text-blue-50/80' : 'text-slate-600' }}">
                                                        <span class="font-medium">{{ $name }}</span>
                                                        <span>{{ $c->created_at?->format('Y-m-d H:i') }}</span>
                                                    </div>
                                                    <div class="text-sm whitespace-pre-line">{{ $c->body }}</div>
                                                    @if($inline->count())
                                                        <div class="mt-2 grid grid-cols-2 gap-2">
                                                            @foreach($inline as $img)
                                                                <a href="{{ route('attachments.preview', $img) }}" target="_blank" class="block">
                                                                    @if(\Illuminate\Support\Str::startsWith($img->mime, 'image/'))
                                                                        <img src="{{ route('attachments.preview', $img) }}" class="w-full h-24 object-cover rounded" />
                                                                    @else
                                                                        <div class="p-2 border rounded text-xs {{ $isMine ? 'bg-blue-500/30 border-blue-300 text-white' : '' }}">{{ $img->filename }}</div>
                                                                    @endif
                                                                </a>
                                                            @endforeach
                                                        </div>
                                                    @endif
                                                </div>
                                                @if($isMine)
                                                    <div class="w-6 h-6 rounded-full bg-blue-100 flex items-center justify-center text-[10px] font-semibold text-blue-700">{{ $initials }}</div>
                                                @endif
                                            </div>
                                        @empty
                                            <div class="text-sm text-slate-500 text-center">No comments yet.</div>
                                        @endforelse
                                    </div>
                                </div>

                                <!-- Add Comment -->
                                @if(strtolower($sel->status) === 'closed')
                                    <div class="p-4 mt-2 rounded border border-gray-200 bg-gray-50 text-center text-sm text-gray-600">
                                        This ticket is already closed. Commenting is disabled.
                                    </div>
                                @else
                                    <div class="space-y-3">
                                        <label class="block text-sm font-medium text-slate-900 text-center">Add a comment</label>
                                        <textarea rows="3" wire:model.defer="previewComment" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Type your message here..."></textarea>
                                        <div class="flex items-center gap-3">
                                            <input type="file" wire:model="previewPhoto" accept="image/*" capture="environment" class="block w-full text-sm" />
                                            @if($previewPhoto)
                                                <img src="{{ $previewPhoto->temporaryUrl() }}" class="w-16 h-16 object-cover rounded border" />
                                            @endif
                                        </div>
                                        <div class="flex justify-center">
                                            <x-button wire:click="postPreviewComment" wire:loading.attr="disabled" :disabled="$previewSubmitting" class="bg-yellow-500 hover:bg-yellow-600 text-white">Add Comment</x-button>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endif
                        @if(!$sel)
                            <div class="text-center py-8 text-slate-500">Open a ticket to view its details and comments</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- End-user CSAT Modal on same page (no per-ticket route required) -->
    <x-dialog-modal wire:model="showEnduserCsatModal">
        <x-slot name="title">Rate Your Support</x-slot>
        <x-slot name="content">
            <div x-data="{ open: @entangle('showEnduserCsatModal') }" x-cloak x-show="open"
                 x-transition:enter="transform ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-3 scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                 x-transition:leave="transform ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                 x-transition:leave-end="opacity-0 translate-y-2 scale-95"
                 class="space-y-4">
                <p class="text-sm text-slate-700">Please rate your experience for this ticket.</p>
                <div class="flex items-center gap-2">
                    <button type="button" class="px-3 py-1.5 rounded text-sm border {{ $csatRating==='good' ? 'bg-green-600 text-white border-green-600' : 'border-green-300 text-green-700' }}" wire:click="$set('csatRating','good')">Good</button>
                    <button type="button" class="px-3 py-1.5 rounded text-sm border {{ $csatRating==='neutral' ? 'bg-yellow-500 text-white border-yellow-500' : 'border-yellow-300 text-yellow-700' }}" wire:click="$set('csatRating','neutral')">OK</button>
                    <button type="button" class="px-3 py-1.5 rounded text-sm border {{ $csatRating==='poor' ? 'bg-red-600 text-white border-red-600' : 'border-red-300 text-red-700' }}" wire:click="$set('csatRating','poor')">Poor</button>
                </div>
                <div>
                    <label class="text-xs text-slate-500">Optional comment</label>
                    <textarea rows="3" class="w-full border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" wire:model.defer="csatComment" placeholder="Tell us what went well or what we can improve..."></textarea>
                </div>
                @error('csatRating') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
                @error('csatComment') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        </x-slot>
        <x-slot name="footer">
            @if(!$requireCsat)
                <x-secondary-button wire:click="dismissCsat">Later</x-secondary-button>
            @else
                <span class="text-xs text-amber-700 mr-2">Required for closed tickets</span>
            @endif
            <x-button class="ml-2" wire:click="submitCsat">Submit</x-button>
        </x-slot>
    </x-dialog-modal>

    @if(!$showEnduserCsatModal)
        @php $sel = $this->selectedTicket ?? null; @endphp
        @php $selHasCsat = $sel ? (bool) optional($sel->latestSubmittedCsat)->submitted_at : false; @endphp
        @if($sel && in_array($sel->status, ['resolved','closed']) && !$selHasCsat)
            <div x-data="{ show: false }" x-init="setTimeout(()=> show = true, 50)" x-cloak x-show="show"
                 x-transition:enter="transform ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-3 scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                 x-transition:leave="transform ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                 x-transition:leave-end="opacity-0 translate-y-2 scale-95"
                 class="fixed bottom-4 right-4 bg-amber-600 text-white rounded-lg shadow-lg p-3 text-sm flex items-center gap-2">
                <span class="font-medium">{{ $requireCsat ? 'Feedback required' : 'Rate your support' }}</span>
                <button type="button" class="ml-1 px-2 py-1 bg-white/20 hover:bg-white/30 rounded" wire:click="openCsatNow">Rate now</button>
            </div>
        @endif
    @endif

    <!-- Create Ticket Modal (reuse verified upload inputs) -->
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

                @php /* End-users are authenticated; no verification required. */ @endphp
                @if(false)
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
                                <div class="space-y-2">
                                    <x-label value="ID - Front" />
                                    <input id="id_front_input_user" type="file" accept="image/*" capture="environment" class="sr-only" wire:model="id_front" />
                                    <label for="id_front_input_user" class="cursor-pointer w-full rounded-lg border border-gray-300 bg-white hover:bg-gray-50 p-3 text-sm text-gray-700 flex items-center justify-center gap-2">
                                        <x-heroicon name="camera" class="w-5 h-5" />
                                        <span>Open Camera</span>
                                    </label>
                                    @if($id_front)
                                        <img src="{{ $id_front->temporaryUrl() }}" alt="ID Front Preview" class="w-full h-40 object-cover rounded-lg border" />
                                        <button type="button" class="text-xs text-red-600 hover:underline" wire:click="$set('id_front', null)">Retake</button>
                                    @endif
                                    @error('id_front') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div class="space-y-2">
                                    <x-label value="ID - Back" />
                                    <input id="id_back_input_user" type="file" accept="image/*" capture="environment" class="sr-only" wire:model="id_back" />
                                    <label for="id_back_input_user" class="cursor-pointer w-full rounded-lg border border-gray-300 bg-white hover:bg-gray-50 p-3 text-sm text-gray-700 flex items-center justify-center gap-2">
                                        <x-heroicon name="camera" class="w-5 h-5" />
                                        <span>Open Camera</span>
                                    </label>
                                    @if($id_back)
                                        <img src="{{ $id_back->temporaryUrl() }}" alt="ID Back Preview" class="w-full h-40 object-cover rounded-lg border" />
                                        <button type="button" class="text-xs text-red-600 hover:underline" wire:click="$set('id_back', null)">Retake</button>
                                    @endif
                                    @error('id_back') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                                </div>
                            @endif
                            @if($verification_option === 'cor')
                                <div class="md:col-span-2 space-y-2">
                                    <x-label value="Certificate of Registration (Front Page)" />
                                    <input id="cor_input_user" type="file" accept="image/*" capture="environment" class="sr-only" wire:model="cor_file" />
                                    <label for="cor_input_user" class="cursor-pointer w-full rounded-lg border border-gray-300 bg-white hover:bg-gray-50 p-3 text-sm text-gray-700 flex items-center justify-center gap-2">
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
                        <p class="text-xs text-gray-600 mt-3">We store verification files securely and only ITSS staff can access them. Max 4MB per ID image, 6MB for CoR.</p>
                    </div>
                @endif
            </div>
        </x-slot>
        <x-slot name="footer">
            <x-secondary-button wire:click="$set('showCreate', false)" class="mr-2">Cancel</x-secondary-button>
            <x-button wire:click="createTicket">Create Ticket</x-button>
        </x-slot>
    </x-dialog-modal>
</div>
