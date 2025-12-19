<div class="min-h-screen bg-gradient-to-b from-blue-50 to-white dark:from-gray-900 dark:to-gray-900" @if($ticket) wire:poll.8s="refreshData" @endif>
    <div class="max-w-2xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-3">
                <x-heroicon name="magnifying-glass" class="w-8 h-8 text-blue-600" />
                <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Track Your Ticket</h1>
            </div>
            <a href="{{ route('helpdesk.home') }}" class="inline-flex items-center gap-2 px-3 py-2 text-sm font-medium rounded-md border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <x-heroicon name="arrow-left" class="w-4 h-4" />
                Back to Helpdesk
            </a>
        </div>

        @if (session('error'))
            <div class="mb-4 rounded bg-red-50 text-red-800 px-4 py-3 border border-red-200">{{ session('error') }}</div>
        @endif
        @if (session('success'))
            <div class="mb-4 rounded bg-green-50 text-green-800 px-4 py-3 border border-green-200">{{ session('success') }}</div>
        @endif
        @if (session('track_hint'))
            <div class="mb-4 rounded bg-blue-50 text-blue-900 px-4 py-3 border border-blue-200 dark:bg-blue-900/30 dark:text-blue-100 dark:border-blue-700 flex gap-2">
                <x-heroicon name="information-circle" class="w-5 h-5 text-blue-600 dark:text-blue-300 mt-0.5" />
                <span class="text-sm">{{ session('track_hint') }}</span>
            </div>
        @endif

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow ring-1 ring-black/5 dark:ring-white/10 p-6">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="sm:col-span-2">
                    <x-label value="Ticket Number" />
                    <x-input type="text" class="mt-1 w-full bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-300" placeholder="e.g. HD-2025-00001" wire:model.defer="ticket_no" />
                    @error('ticket_no') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <x-label value="Email" />
                    <x-input type="email" class="mt-1 w-full bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-300" placeholder="name@school.edu" wire:model.defer="email" />
                    @error('email') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
            <div class="mt-4 text-right">
                <x-button wire:click="lookup">Check Status</x-button>
            </div>
        </div>

        @if($ticket)
            <div class="mt-6 bg-white dark:bg-gray-800 rounded-xl shadow ring-1 ring-black/5 dark:ring-white/10 p-6">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ $ticket->subject }}</h2>
                    <span class="text-sm px-2 py-1 rounded-full
                        {{ match($ticket->status){
                            'open' => 'bg-blue-100 text-blue-800',
                            'in_progress' => 'bg-yellow-100 text-yellow-800',
                            'resolved' => 'bg-green-100 text-green-800',
                            'closed' => 'bg-gray-100 text-gray-800',
                            default => 'bg-gray-100 text-gray-800'
                        } }}
                    ">{{ \Illuminate\Support\Str::of($ticket->status)->replace('_',' ')->title() }}</span>
                </div>
                <div class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                    <div><span class="font-medium">Ticket #:</span> {{ $ticket->ticket_no }}</div>
                    <div><span class="font-medium">Category:</span> {{ $ticket->category->name ?? '—' }}</div>
                    <div><span class="font-medium">Created:</span> {{ optional($ticket->created_at)->toDayDateTimeString() }}</div>
                </div>
                <div class="mt-4">
                    <h3 class="text-sm font-medium text-gray-900 dark:text-gray-100 mb-1">Description</h3>
                    <div class="text-sm text-gray-800 dark:text-gray-200 whitespace-pre-line">{{ $ticket->description }}</div>
                </div>
                <div class="mt-6">
                    <h3 class="text-sm font-medium text-gray-900 dark:text-gray-100 mb-2">Updates from Support</h3>
                    @if($requireCsatToView)
                        <div class="rounded-lg border border-amber-300 bg-amber-50 dark:border-amber-600 dark:bg-amber-500/10 p-4 flex items-start gap-3">
                            <x-heroicon name="star" class="w-5 h-5 text-amber-500 mt-0.5" />
                            <div class="text-sm text-amber-900 dark:text-amber-200">
                                <p class="mb-2">Please take a quick satisfaction survey to view the agent's replies.</p>
                                <x-button size="sm" wire:click="openCsat">Take Survey</x-button>
                            </div>
                        </div>
                    @else
                        @if(empty($comments) || count($comments) === 0)
                            <div class="text-sm text-gray-500 dark:text-gray-400">No updates yet.</div>
                        @else
                            <div class="space-y-3">
                                @foreach($comments as $c)
                                    <div class="border rounded p-3 bg-gray-50 dark:bg-gray-700/50 border-gray-200 dark:border-gray-600">
                                        <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">{{ $c->user->name ?? 'Support' }} • {{ optional($c->created_at)->diffForHumans() }}</div>
                                        <div class="text-sm text-gray-800 dark:text-gray-200 whitespace-pre-line">{{ $c->body }}</div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    @endif
                </div>

                <div class="mt-6">
                    <h3 class="text-sm font-medium text-gray-900 dark:text-gray-100 mb-2">Add a follow-up</h3>
                    @if($ticket->status === 'closed')
                        <div class="text-sm text-gray-500 dark:text-gray-400">This ticket is closed and no longer accepts new comments.</div>
                    @else
                        <div class="space-y-3">
                            <textarea rows="3" class="w-full border-gray-300 dark:border-gray-600 rounded-md focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-300" placeholder="Write your follow-up here..." wire:model.defer="guestComment"></textarea>
                            @error('guestComment') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
                            <div>
                                <x-label value="Attach a photo (optional)" />
                                <input id="guest_followup_photo" type="file" accept="image/*" capture="environment" class="sr-only" wire:model="photo" />
                                <label for="guest_followup_photo" class="cursor-pointer w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 p-3 text-sm text-gray-700 dark:text-gray-200 flex items-center justify-center gap-2">
                                    <x-heroicon name="camera" class="w-5 h-5" />
                                    <span>Open Camera / Choose Photo</span>
                                </label>
                                @if($photo)
                                    <img src="{{ $photo->temporaryUrl() }}" alt="Follow-up Preview" class="mt-2 w-full h-48 object-cover rounded border border-gray-300 dark:border-gray-600" />
                                    <button type="button" class="text-xs text-red-600 hover:underline mt-1" wire:click="$set('photo', null)">Remove</button>
                                @endif
                                @error('photo') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Images only, up to 6MB.</p>
                            </div>
                            <div class="text-right">
                                <x-button wire:click="postComment" wire:loading.attr="disabled">Post Comment</x-button>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>

    <!-- CSAT Survey Modal -->
    <x-dialog-modal wire:model="showCsatModal">
        <x-slot name="title">Rate your support experience</x-slot>
        <x-slot name="content">
            <div class="space-y-4">
                <div x-data="{ hover: 0, value: @entangle('csatRating').live }">
                    <div class="flex items-center gap-2">
                        @for($i = 1; $i <= 5; $i++)
                            <button type="button"
                                @mouseenter="hover = {{ $i }}" @mouseleave="hover = 0" @click="value = {{ $i }}"
                                class="p-1 transition-transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-yellow-400 rounded"
                                :aria-pressed="(hover || value || 0) >= {{ $i }} ? 'true' : 'false'"
                                aria-label="Rate {{ $i }} star{{ $i>1 ? 's' : '' }}">
                                <template x-if="(hover || value || 0) >= {{ $i }}">
                                    <svg class="w-8 h-8 text-yellow-400" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.802 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.802-2.034a1 1 0 00-1.175 0l-2.802 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81H7.03a1 1 0 00.95-.69l1.07-3.292z"/>
                                    </svg>
                                </template>
                                <template x-if="(hover || value || 0) < {{ $i }}">
                                    <svg class="w-8 h-8 text-gray-300 dark:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.802 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.802-2.034a1 1 0 00-1.175 0l-2.802 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L4.98 8.72c-.783-.57-.38-1.81.588-1.81H9.03a1 1 0 00.95-.69l1.07-3.292z" />
                                    </svg>
                                </template>
                            </button>
                        @endfor
                    </div>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                        <span x-show="value" x-text="`Selected: ${value}/5`"></span>
                        <span x-show="!value">Select a rating.</span>
                    </p>
                    @error('csatRating') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <x-label value="Comments (optional)" />
                    <textarea rows="3" class="mt-1 w-full border-gray-300 dark:border-gray-600 rounded-md focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-300" placeholder="Tell us what went well or what could be improved" wire:model.defer="csatComment"></textarea>
                    @error('csatComment') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
        </x-slot>
        <x-slot name="footer">
            <x-secondary-button wire:click="$set('showCsatModal', false)">Cancel</x-secondary-button>
            <x-button class="ml-2" wire:click="submitCsat">Submit</x-button>
        </x-slot>
    </x-dialog-modal>
</div>
