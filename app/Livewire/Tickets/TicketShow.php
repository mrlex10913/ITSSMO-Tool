<?php

namespace App\Livewire\Tickets;

use App\Livewire\Tickets\Concerns\EndUserComments;
use App\Models\Helpdesk\CsatResponse;
use App\Models\Helpdesk\Ticket;
use App\Models\Helpdesk\TicketComment;
use App\Models\Helpdesk\TicketUserRead;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

// duplicate removed

#[Layout('layouts.enduser')]
class TicketShow extends Component
{
    use EndUserComments, WithFileUploads;

    public Ticket $ticket;

    public string $guestComment = '';

    public $photo = null; // temporary upload

    public bool $submitting = false;

    // CSAT modal state
    public bool $showCsatModal = false;

    public ?string $csatRating = null; // 'good' | 'neutral' | 'poor'

    public ?string $csatComment = null;

    public ?string $csatToken = null; // must be public to persist across requests

    public ?int $csatInviteId = null; // must be public to persist across requests

    public function mount(Ticket $ticket): void
    {
        // If an agent accidentally opens end-user TicketShow, redirect immediately to ITSS route
        if ($this->isAgentUser() && Auth::id() !== ($ticket->requester_id ?? 0) && ! request()->routeIs('itss.*')) {
            $this->redirectRoute('itss.ticket.show', ['ticket' => $ticket->id]);

            return;
        }

        $this->ticket = $this->loadTicketBase($ticket);
        $this->authorizeEndUserOrAgent($this->ticket);

        // Mark as read on open
        $this->markRead($this->ticket->id);

        // Prompt CSAT if applicable (no email required)
        $this->checkCsatToPrompt();

    }

    public function getCommentsProperty()
    {
        // End-user sees public comments only
        return $this->getPublicCommentsFor($this->ticket->id);
    }

    public function postComment(): void
    {
        if (in_array($this->ticket->status, ['closed'])) {
            session()->flash('error', 'This ticket is closed and no longer accepts new comments.');

            return;
        }

        if ((! $this->guestComment || trim($this->guestComment) === '') && ! $this->photo) {
            $this->addError('guestComment', 'Please add a message or attach a photo.');

            return;
        }

        $this->validate([
            'guestComment' => 'nullable|string|min:2|max:2000',
            'photo' => 'nullable|image|max:6144',
        ]);

        if ($this->submitting) {
            return;
        }
        $this->submitting = true;

        $comment = $this->createEndUserCommentWithOptionalPhoto($this->ticket, $this->guestComment, $this->photo);

        $this->guestComment = '';
        $this->photo = null;
        $this->submitting = false;
        $this->ticket->refresh();
        $this->ticket->load(['attachments']);
        // Mark as read after posting
        $this->markRead($this->ticket->id);
        session()->flash('success', 'Comment posted.');
    }

    public function refreshData(): void
    {
        $this->ticket->refresh();
        $this->ticket->load(['requester:id,name,email', 'category:id,name', 'attachments']);
        $this->markRead($this->ticket->id);
        // Re-evaluate CSAT prompt on refresh
        if (! $this->showCsatModal) {
            $this->checkCsatToPrompt();
        }
    }

    protected function checkCsatToPrompt(): void
    {
        // Only requester should see CSAT prompt
        if (Auth::id() !== ($this->ticket->requester_id ?? 0)) {
            return;
        }
        if (! in_array($this->ticket->status, ['resolved', 'closed'], true)) {
            return;
        }
        // Find pending invite (not submitted and not expired)
        $inv = CsatResponse::where('ticket_id', $this->ticket->id)
            ->whereNull('submitted_at')
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->latest('id')
            ->first();
        if ($inv && $inv->dismissed_until && $inv->dismissed_until->isFuture()) {
            // Respect dismissal window; no prompt
            $this->csatToken = $inv->token;
            $this->csatInviteId = $inv->id;

            return;
        }
        if (! $inv) {
            // Create an in-app invite if none exists (email not required)
            $inv = new CsatResponse;
            $inv->ticket_id = $this->ticket->id;
            $inv->user_id = $this->ticket->requester_id;
            $inv->requester_email = $this->ticket->requester->email ?? $this->ticket->requester_email;
            $inv->token = \Illuminate\Support\Str::random(48);
            $inv->expires_at = now()->addDays(14);
            $inv->save();
        }
        $this->csatToken = $inv->token;
        $this->csatInviteId = $inv->id;
        $this->showCsatModal = true;
    }

    public function submitCsat(): void
    {
        $this->validate([
            'csatRating' => 'required|in:good,neutral,poor',
            'csatComment' => 'nullable|string|max:1000',
        ]);
        $inv = CsatResponse::where('ticket_id', $this->ticket->id)
            ->where('token', $this->csatToken)
            ->latest('id')
            ->first();
        if (! $inv) {
            // As a fallback, find the latest unsubmitted invite
            $inv = CsatResponse::where('ticket_id', $this->ticket->id)
                ->whereNull('submitted_at')
                ->latest('id')
                ->first();
        }
        if (! $inv) {
            // Create on the fly
            $inv = new CsatResponse;
            $inv->ticket_id = $this->ticket->id;
            $inv->user_id = $this->ticket->requester_id;
            $inv->requester_email = $this->ticket->requester->email ?? $this->ticket->requester_email;
            $inv->token = \Illuminate\Support\Str::random(48);
            $inv->expires_at = now()->addDays(14);
        }
        $inv->rating = $this->csatRating;
        if ($this->csatComment !== null) {
            $inv->comment = $this->csatComment;
        }
        $inv->submitted_at = now();
        $inv->save();

        $this->showCsatModal = false;
        $this->csatRating = null;
        $this->csatComment = null;
        session()->flash('success', 'Thanks for your feedback!');
    }

    public function dismissCsat(): void
    {
        // Persist a dismissal window (e.g., 7 days)
        $until = now()->addDays(7);
        if ($this->csatInviteId) {
            CsatResponse::whereKey($this->csatInviteId)->update(['dismissed_until' => $until]);
        } elseif ($this->csatToken) {
            CsatResponse::where('ticket_id', $this->ticket->id)
                ->where('token', $this->csatToken)
                ->latest('id')
                ->limit(1)
                ->update(['dismissed_until' => $until]);
        }
        $this->showCsatModal = false;
    }

    public function openCsatNow(): void
    {
        $this->showCsatModal = true;
    }

    protected function markRead(int $ticketId): void
    {
        $count = TicketComment::where('ticket_id', $ticketId)->where('is_internal', false)->count();
        $lastId = TicketComment::where('ticket_id', $ticketId)
            ->where('is_internal', false)
            ->max('id');
        TicketUserRead::updateOrCreate([
            'ticket_id' => $ticketId,
            'user_id' => Auth::id(),
        ], [
            'last_seen_comment_id' => $lastId,
            'last_seen_comment_count' => $count,
            'last_seen_at' => now(),
        ]);
    }

    public function render()
    {
        return view('livewire.tickets.ticket-show', [
            'comments' => $this->comments,
        ]);
    }
}
