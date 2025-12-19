<?php

namespace App\Livewire\Tickets;

use App\Livewire\ITSS\Helpdesk as ITSSHelpdesk;
use App\Livewire\Tickets\Concerns\EndUserComments;
use App\Models\Helpdesk\CsatResponse;
use App\Models\Helpdesk\Ticket;
use App\Models\Helpdesk\TicketComment;
use App\Models\Helpdesk\TicketUserRead;
use Illuminate\Support\Facades\Auth;

class MyTickets extends ITSSHelpdesk
{
    use EndUserComments;

    public ?int $selectedTicketId = null;

    public string $previewComment = '';

    public $previewPhoto = null; // temp upload

    public bool $previewSubmitting = false;

    public ?string $createdOn = null; // Y-m-d

    // End-user CSAT prompt state (works on the same page without ticket route)
    public bool $showEnduserCsatModal = false;

    public ?string $csatRating = null; // good|neutral|poor

    public ?string $csatComment = null;

    public ?string $csatToken = null; // persist invite context

    public ?int $csatInviteId = null;

    public bool $requireCsat = false; // force rating when Closed

    public function mount(): void
    {
        // Default filters: show today's tickets and status 'open'
        $this->createdOn = $this->createdOn ?: now()->toDateString();
    }

    /**
     * End-users are authenticated; no extra identity verification is required.
     */
    public function requiresVerification(): bool
    {
        return false;
    }

    public function selectTicket(int $ticketId): void
    {
        $this->selectedTicketId = $ticketId;
        $this->dispatch('ticket-selected', id: $ticketId);
        // Mark as read server-side immediately
        $this->markRead($ticketId);
        // Re-evaluate CSAT prompt when selecting a ticket
        $this->checkCsatToPrompt();
    }

    // Scope list and stats to the current requester
    public function getTicketsProperty()
    {
        $userId = Auth::id();
        $query = Ticket::query()
            ->where('requester_id', $userId)
            ->withCount(['comments as public_comments_count' => function ($q) {
                $q->where('is_internal', false);
            }])
            ->when($this->search, fn ($q) => $q->where(function ($qq) {
                $qq->where('ticket_no', 'like', "%{$this->search}%")
                    ->orWhere('subject', 'like', "%{$this->search}%");
            }))
            // Date/status logic: if a date is selected and no explicit status filter,
            // show tickets from that date (any status) OR any tickets that are currently open (any date).
            ->when($this->createdOn, function ($q) {
                if (empty($this->status)) {
                    $q->where(function ($qq) {
                        $qq->whereDate('created_at', $this->createdOn)
                            ->orWhere('status', 'open');
                    });
                } else {
                    $q->whereDate('created_at', $this->createdOn);
                }
            })
            ->when($this->status, fn ($q) => $q->where('status', $this->status))
            ->when($this->priority, fn ($q) => $q->where('priority', $this->priority))
            ->when($this->category, fn ($q) => $q->where('category_id', $this->category))
            ->with([
                'requester:id,name',
                'assignee:id,name',
                'category:id,name',
                'latestSubmittedCsat' => function ($q) {
                    $q->select(
                        'csat_responses.id',
                        'csat_responses.ticket_id',
                        'csat_responses.rating',
                        'csat_responses.comment',
                        'csat_responses.submitted_at'
                    );
                },
            ])
            ->latest();

        return $query->paginate(10);
    }

    public function getSelectedTicketProperty(): ?Ticket
    {
        $id = $this->selectedTicketId;
        if (! $id) {
            return null;
        }
        $userId = Auth::id();

        return Ticket::query()
            ->where('id', $id)
            ->where('requester_id', $userId)
            ->with([
                'category:id,name',
                'attachments',
                'requester:id,name,email',
                'latestSubmittedCsat' => function ($q) {
                    $q->select(
                        'csat_responses.id',
                        'csat_responses.ticket_id',
                        'csat_responses.rating',
                        'csat_responses.comment',
                        'csat_responses.submitted_at'
                    );
                },
            ])
            ->first();
    }

    public function getSelectedCommentsProperty()
    {
        if (! $this->selectedTicketId) {
            return collect();
        }

        return \App\Models\Helpdesk\TicketComment::with('user:id,name')
            ->where('ticket_id', $this->selectedTicketId)
            ->where('is_internal', false)
            ->orderBy('id')
            ->get();
    }

    public function postPreviewComment(): void
    {
        $ticket = $this->selectedTicket;
        if (! $ticket) {
            return;
        }
        if (in_array($ticket->status, ['closed'])) {
            session()->flash('error', 'This ticket is closed and no longer accepts new comments.');

            return;
        }
        if ((! $this->previewComment || trim($this->previewComment) === '') && ! $this->previewPhoto) {
            $this->addError('previewComment', 'Please add a message or attach a photo.');

            return;
        }
        $this->validate([
            'previewComment' => 'nullable|string|min:2|max:2000',
            'previewPhoto' => 'nullable|image|max:6144',
        ]);
        if ($this->previewSubmitting) {
            return;
        }
        $this->previewSubmitting = true;

        // Reuse end-user comment creation from trait
        $this->createEndUserCommentWithOptionalPhoto($ticket, $this->previewComment, $this->previewPhoto);

        $this->previewComment = '';
        $this->previewPhoto = null;
        $this->previewSubmitting = false;
        // refresh data for preview
        $this->selectedTicket?->refresh();
        // Update seen count in the browser
        if ($this->selectedTicketId) {
            $count = \App\Models\Helpdesk\TicketComment::where('ticket_id', $this->selectedTicketId)->where('is_internal', false)->count();
            $this->dispatch('ticket-opened', id: $this->selectedTicketId, count: $count);
        }
    }

    public function getStatsProperty(): array
    {
        $userId = Auth::id();
        $base = Ticket::query()->where('requester_id', $userId);

        return [
            'open' => (clone $base)->where('status', 'open')->count(),
            'in_progress' => (clone $base)->where('status', 'in_progress')->count(),
            'resolved' => (clone $base)->where('status', 'resolved')->count(),
            'total' => (clone $base)->count(),
            'created_today' => (clone $base)->whereDate('created_at', now()->toDateString())->count(),
        ];
    }

    // Used by wire:poll to refresh the view
    public function refreshData(): void
    {
        // No-op: re-render will refresh computed properties (tickets, stats, comments)
        // Optionally, you could touch selectedTicket to ensure relationships are fresh
        if ($this->selectedTicketId) {
            // Ensure we have fresh attachments/comments counts if needed
            $this->selectedTicket?->refresh();
        }
        if ($this->selectedTicketId) {
            $count = TicketComment::where('ticket_id', $this->selectedTicketId)->where('is_internal', false)->count();
            $this->dispatch('ticket-opened', id: $this->selectedTicketId, count: $count);
            $this->markRead($this->selectedTicketId, $count);
        }
        // Re-check CSAT prompt state on poll
        if ($this->selectedTicketId && ! $this->showEnduserCsatModal) {
            $this->checkCsatToPrompt();
        }
    }

    protected function markRead(int $ticketId, ?int $count = null): void
    {
        $userId = Auth::id();
        $count = $count ?? TicketComment::where('ticket_id', $ticketId)->where('is_internal', false)->count();
        $lastId = TicketComment::where('ticket_id', $ticketId)
            ->where('is_internal', false)
            ->max('id');
        TicketUserRead::updateOrCreate([
            'ticket_id' => $ticketId,
            'user_id' => $userId,
        ], [
            'last_seen_comment_id' => $lastId,
            'last_seen_comment_count' => $count,
            'last_seen_at' => now(),
        ]);
    }

    protected function checkCsatToPrompt(): void
    {
        $sel = $this->selectedTicket;
        if (! $sel) {
            return;
        }
        // Only requester may be prompted
        if (Auth::id() !== ($sel->requester_id ?? 0)) {
            return;
        }
        if (! in_array($sel->status, ['resolved', 'closed'], true)) {
            return;
        }
        // If already submitted CSAT exists, never prompt again
        $hasSubmitted = CsatResponse::where('ticket_id', $sel->id)
            ->whereNotNull('submitted_at')
            ->exists();
        if ($hasSubmitted) {
            $this->showEnduserCsatModal = false;
            $this->requireCsat = false;
            $this->csatToken = null;
            $this->csatInviteId = null;

            return;
        }

        $this->requireCsat = ($sel->status === 'closed');
        // Find latest pending invite (not submitted, not expired)
        $inv = CsatResponse::where('ticket_id', $sel->id)
            ->whereNull('submitted_at')
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->latest('id')
            ->first();
        if (! $this->requireCsat && $inv && $inv->dismissed_until && $inv->dismissed_until->isFuture()) {
            // Respect dismissal window; store context but don't show modal
            $this->csatToken = $inv->token;
            $this->csatInviteId = $inv->id;

            return;
        }
        if (! $inv) {
            // Create in-app invite if none exists yet (email disabled)
            $inv = new CsatResponse;
            $inv->ticket_id = $sel->id;
            $inv->user_id = $sel->requester_id;
            $inv->requester_email = $sel->requester->email ?? $sel->requester_email;
            $inv->token = \Illuminate\Support\Str::random(48);
            $inv->expires_at = now()->addDays(14);
            $inv->save();
        }
        $this->csatToken = $inv->token;
        $this->csatInviteId = $inv->id;
        $this->showEnduserCsatModal = true; // On Closed, always open; on Resolved, unless snoozed
    }

    public function submitCsat(): void
    {
        $this->validate([
            'csatRating' => 'required|in:good,neutral,poor',
            'csatComment' => 'nullable|string|max:1000',
        ]);
        $sel = $this->selectedTicket;
        if (! $sel) {
            return;
        }
        $inv = CsatResponse::where('ticket_id', $sel->id)
            ->where('token', $this->csatToken)
            ->latest('id')
            ->first();
        if (! $inv) {
            $inv = CsatResponse::where('ticket_id', $sel->id)
                ->whereNull('submitted_at')
                ->latest('id')
                ->first();
        }
        if (! $inv) {
            $inv = new CsatResponse;
            $inv->ticket_id = $sel->id;
            $inv->user_id = $sel->requester_id;
            $inv->requester_email = $sel->requester->email ?? $sel->requester_email;
            $inv->token = \Illuminate\Support\Str::random(48);
            $inv->expires_at = now()->addDays(14);
        }
        $inv->rating = $this->csatRating;
        if ($this->csatComment !== null) {
            $inv->comment = $this->csatComment;
        }
        $inv->submitted_at = now();
        $inv->save();

        $this->showEnduserCsatModal = false;
        $this->csatRating = null;
        $this->csatComment = null;
        $this->requireCsat = false; // once submitted, no longer required
        // Refresh selected ticket so UI indicators disappear
        $this->selectedTicket?->refresh();
        session()->flash('success', 'Thanks for your feedback!');
    }

    public function dismissCsat(): void
    {
        if ($this->requireCsat) {
            // Do not allow dismiss when feedback is required for Closed tickets
            $this->showEnduserCsatModal = true;
            session()->flash('error', 'Feedback is required for closed tickets.');

            return;
        }
        $sel = $this->selectedTicket;
        if (! $sel) {
            return;
        }
        $until = now()->addDays(7);
        if ($this->csatInviteId) {
            CsatResponse::whereKey($this->csatInviteId)->update(['dismissed_until' => $until]);
        } elseif ($this->csatToken) {
            CsatResponse::where('ticket_id', $sel->id)
                ->where('token', $this->csatToken)
                ->latest('id')
                ->limit(1)
                ->update(['dismissed_until' => $until]);
        }
        $this->showEnduserCsatModal = false;
    }

    public function openCsatNow(): void
    {
        $this->showEnduserCsatModal = true;
    }

    public function render()
    {
        $userId = Auth::id();
        $tickets = $this->tickets;
        $ids = collect($tickets->items())->pluck('id');
        $reads = TicketUserRead::where('user_id', $userId)
            ->whereIn('ticket_id', $ids)
            ->get()
            ->keyBy('ticket_id');
        $unread = [];
        foreach ($tickets->items() as $t) {
            $seen = optional($reads->get($t->id))->last_seen_comment_count ?? 0;
            $total = $t->public_comments_count ?? 0;
            $unread[$t->id] = max(0, $total - $seen);
        }

        return view('livewire.tickets.my-tickets', [
            'tickets' => $tickets,
            'categories' => \App\Models\Helpdesk\TicketCategory::where('is_active', true)->orderBy('name')->get(['id', 'name']),
            'unreadCounts' => $unread,
        ]);
    }
}
