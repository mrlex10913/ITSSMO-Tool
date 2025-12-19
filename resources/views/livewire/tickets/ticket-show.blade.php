<div wire:init="refreshData" wire:poll.8s="refreshData" class="sm:px-6 lg:px-8 space-y-6">
    @if (session('success'))
        <div class="mt-2 rounded bg-green-50 text-green-800 px-4 py-3">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="mt-2 rounded bg-red-50 text-red-800 px-4 py-3">{{ session('error') }}</div>
    @endif

    <!-- Header -->
    <div class="flex items-start justify-between gap-4 border-b border-gray-200 pb-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Ticket {{ $ticket->ticket_no ?? $ticket->id }}</h1>
            <div class="mt-1 flex items-center gap-2">
                <span class="px-2 py-0.5 rounded text-xs font-medium {{ match($ticket->status){ 'open'=>'bg-yellow-500 text-white', 'in_progress'=>'bg-blue-600 text-white', 'resolved'=>'bg-blue-400 text-white', 'closed'=>'bg-gray-300 text-gray-800', default=>'bg-gray-300 text-gray-800' } }}">{{ \Illuminate\Support\Str::of($ticket->status)->replace('_',' ')->title() }}</span>
                <span class="px-2 py-0.5 rounded text-xs font-medium {{ match($ticket->priority){ 'low'=>'bg-blue-100 text-blue-800', 'medium'=>'bg-yellow-100 text-yellow-800', 'high'=>'bg-orange-100 text-orange-800', 'critical'=>'bg-red-100 text-red-800', default=>'bg-gray-100 text-gray-800' } }}">{{ ucfirst($ticket->priority) }}</span>
                <span class="text-xs text-slate-500">{{ $ticket->category->name ?? '—' }}</span>
            </div>
        </div>
        <div class="text-right text-sm text-slate-600">
            <p>Created {{ $ticket->created_at?->format('Y-m-d h:i A') }}</p>
            <p>Updated {{ $ticket->updated_at?->diffForHumans() }}</p>
        </div>
    </div>

    <!-- Details Card -->
    <div class="bg-white border border-gray-200 rounded-lg p-6">
        <h2 class="font-semibold text-slate-900 mb-2">Details</h2>
        <p class="whitespace-pre-line text-slate-800">{{ $ticket->description }}</p>

        @if($ticket->attachments && $ticket->attachments->where('ticket_comment_id', null)->count())
            <div class="mt-4">
                <h3 class="text-sm font-medium text-slate-700 mb-2">Attachments</h3>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                    @foreach($ticket->attachments->where('ticket_comment_id', null) as $file)
                        <a href="{{ route('attachments.preview', $file) }}" target="_blank" class="block group border rounded overflow-hidden">
                            @if(\Illuminate\Support\Str::startsWith($file->mime, 'image/'))
                                <img src="{{ route('attachments.preview', $file) }}" alt="{{ $file->filename }}" class="w-full h-36 object-cover" />
                            @else
                                <div class="p-3 text-sm flex items-center gap-2">
                                    <span aria-hidden="true">📎</span>
                                    <span class="truncate">{{ $file->filename }}</span>
                                </div>
                            @endif
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    <!-- Conversation Card -->
    <div class="bg-white border border-gray-200 rounded-lg p-6">
        <h2 class="font-semibold text-slate-900 mb-4">Comments</h2>
        <div class="space-y-5">
            @foreach($comments as $c)
                <div class="border border-gray-200 rounded p-3">
                    <div class="text-sm text-slate-600 flex items-center justify-between">
                        <span>{{ $c->user?->name ?? 'You' }}</span>
                        <span>{{ $c->created_at?->diffForHumans() }}</span>
                    </div>
                    <div class="mt-2 whitespace-pre-line text-slate-900">{{ $c->body }}</div>

                    @php
                        $inline = $ticket->attachments->where('ticket_comment_id', $c->id);
                    @endphp
                    @if($inline->count())
                        <div class="mt-3 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                            @foreach($inline as $img)
                                <a href="{{ route('attachments.preview', $img) }}" target="_blank" class="block">
                                    @if(\Illuminate\Support\Str::startsWith($img->mime, 'image/'))
                                        <img src="{{ route('attachments.preview', $img) }}" alt="{{ $img->filename }}" class="w-full h-32 object-cover rounded" />
                                    @else
                                        <div class="p-2 border rounded text-sm">
                                            <span aria-hidden="true">📎</span>
                                            <span class="truncate">{{ $img->filename }}</span>
                                        </div>
                                    @endif
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            @if($ticket->status === 'closed')
                <div class="p-3 bg-yellow-50 text-yellow-800 rounded text-sm">This ticket is closed. You can no longer post new follow-ups.</div>
            @else
                <div class="space-y-3">
                    <x-textarea wire:model.defer="guestComment" placeholder="Add a comment..." />
                    <div class="flex items-center gap-3">
                        <input type="file" wire:model="photo" accept="image/*" capture="environment" class="block w-full text-sm" />
                        @if($photo)
                            <img src="{{ $photo->temporaryUrl() }}" class="w-20 h-20 object-cover rounded border" />
                        @endif
                    </div>
                    <div class="flex items-center gap-2">
                        <x-button wire:click="postComment" wire:loading.attr="disabled" :disabled="$submitting" class="bg-yellow-500 hover:bg-yellow-600 text-white">Add Comment</x-button>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- CSAT Modal (end-user prompt) -->
<x-dialog-modal wire:model="showCsatModal">
    <x-slot name="title">Rate Your Support</x-slot>
    <x-slot name="content">
        <div class="space-y-4">
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
        <x-secondary-button wire:click="dismissCsat">Later</x-secondary-button>
        <x-button class="ml-2" wire:click="submitCsat">Submit</x-button>
    </x-slot>
    </x-dialog-modal>

@if(!$showCsatModal)
    @php
        // Show a small inline "Rate now" prompt if the ticket is resolved/closed and an invite exists (dismissed state)
        $canPrompt = in_array($ticket->status, ['resolved','closed']);
    @endphp
    @if($canPrompt)
        <div class="fixed bottom-4 right-4 bg-white border border-gray-200 rounded-lg shadow p-3 text-sm">
            <span class="text-slate-700">How was your support experience?</span>
            <button type="button" class="ml-2 text-blue-600 hover:underline" wire:click="openCsatNow">Rate now</button>
        </div>
    @endif
@endif
