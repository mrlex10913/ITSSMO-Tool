<?php

namespace App\Livewire\Tickets;

use App\Models\Helpdesk\CsatResponse;
use App\Models\Helpdesk\Ticket;
use App\Models\Helpdesk\TicketAttachment;
use App\Models\Helpdesk\TicketComment;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.guest')]
class GuestTrack extends Component
{
    use WithFileUploads;

    public string $ticket_no = '';

    public string $email = '';

    public ?Ticket $ticket = null;

    public $comments = [];

    public string $guestComment = '';

    public bool $submitting = false;

    public $photo = null; // Livewire temporary uploaded file

    // CSAT modal state for guests
    public bool $showCsatModal = false;

    public ?int $csatRating = null; // 1..5

    public ?string $csatComment = null;

    // Gate: require survey before showing agent replies
    public bool $requireCsatToView = false;

    protected function rules(): array
    {
        return [
            'ticket_no' => 'required|string',
            'email' => 'required|email:rfc',
        ];
    }

    public function lookup(): void
    {
        $this->validate();
        $this->ticket = Ticket::with(['category:id,name'])
            ->where('ticket_no', $this->ticket_no)
            ->where(function ($q) {
                $q->where('requester_email', $this->email)
                    ->orWhereHas('requester', function ($rq) {
                        $rq->where('email', $this->email);
                    });
            })
            ->first();

        if (! $this->ticket) {
            session()->flash('error', 'No matching ticket found.');
            $this->comments = [];
            $this->requireCsatToView = false;

            return;
        }

        // Load public (non-internal) comments for guest visibility
        $this->comments = TicketComment::with('user:id,name')
            ->where('ticket_id', $this->ticket->id)
            ->where('is_internal', false)
            ->orderBy('id')
            ->get();

        // Require CSAT until submitted for this ticket
        $this->requireCsatToView = ! CsatResponse::where('ticket_id', $this->ticket->id)
            ->whereNotNull('submitted_at')
            ->exists();
    }

    public function render()
    {
        return view('livewire.tickets.guest-track');
    }

    public function postComment(): void
    {
        if (! $this->ticket) {
            return;
        }
        if (in_array($this->ticket->status, ['closed'])) {
            session()->flash('error', 'This ticket is closed and no longer accepts new comments.');

            return;
        }

        // Require either a message or a photo
        if ((! $this->guestComment || trim($this->guestComment) === '') && ! $this->photo) {
            $this->addError('guestComment', 'Please add a message or attach a photo.');

            return;
        }

        $this->validate([
            'guestComment' => 'nullable|string|min:2|max:2000',
            'photo' => 'nullable|image|max:6144', // up to ~6MB
        ]);

        // Lightweight duplicate submission guard
        if ($this->submitting) {
            return;
        }
        $this->submitting = true;

        // Create the public comment (even if image-only, we create a minimal comment)
        $comment = TicketComment::create([
            'ticket_id' => $this->ticket->id,
            'user_id' => $this->ticket->requester_id, // nullable for guest tickets
            'is_internal' => false,
            'body' => $this->guestComment && trim($this->guestComment) !== '' ? trim($this->guestComment) : ' ',
        ]);

        // Save the photo as an attachment if provided
        if ($this->photo) {
            $storedPath = $this->photo->store("tickets/{$this->ticket->id}", 'private');
            TicketAttachment::create([
                'ticket_id' => $this->ticket->id,
                'ticket_comment_id' => $comment->id,
                'user_id' => $this->ticket->requester_id, // nullable
                'type' => 'guest_followup',
                'disk' => 'private',
                'path' => $storedPath,
                'filename' => $this->photo->getClientOriginalName() ?? basename($storedPath),
                'mime' => $this->photo->getMimeType(),
                'size' => $this->photo->getSize(),
            ]);
        }

        $this->guestComment = '';
        // Refresh thread
        $this->comments = TicketComment::with('user:id,name')
            ->where('ticket_id', $this->ticket->id)
            ->where('is_internal', false)
            ->orderBy('id')
            ->get();

        session()->flash('success', 'Comment posted.');
        $this->submitting = false;
        $this->guestComment = '';
        $this->photo = null;
    }

    public function refreshData(): void
    {
        if (! $this->ticket) {
            return;
        }
        $this->ticket->refresh();
        // If CSAT was submitted elsewhere, lift the gate
        if ($this->requireCsatToView) {
            $this->requireCsatToView = ! CsatResponse::where('ticket_id', $this->ticket->id)
                ->whereNotNull('submitted_at')
                ->exists();
        }
        // Keep comments in sync
        $this->comments = TicketComment::with('user:id,name')
            ->where('ticket_id', $this->ticket->id)
            ->where('is_internal', false)
            ->orderBy('id')
            ->get();
    }

    public function openCsat(): void
    {
        if (! $this->ticket) {
            return;
        }
        $this->showCsatModal = true;
    }

    public function submitCsat(): void
    {
        if (! $this->ticket) {
            return;
        }
        $this->validate([
            'csatRating' => 'required|integer|min:1|max:5',
            'csatComment' => 'nullable|string|max:1000',
        ]);

        $resp = new CsatResponse;
        $resp->ticket_id = $this->ticket->id;
        $resp->user_id = $this->ticket->requester_id; // may be null for pure guest tickets
        $resp->requester_email = $this->ticket->requester->email ?? $this->ticket->requester_email ?? $this->email;
        $resp->token = \Illuminate\Support\Str::random(40);
        // Map 1..5 stars to enum values used elsewhere: 4-5 => good, 3 => neutral, 1-2 => poor
        $resp->rating = ($this->csatRating >= 4) ? 'good' : (($this->csatRating === 3) ? 'neutral' : 'poor');
        if ($this->csatComment !== null) {
            $resp->comment = $this->csatComment;
        }
        $resp->submitted_at = now();
        $resp->save();

        $this->showCsatModal = false;
        $this->requireCsatToView = false;
        $this->csatRating = null;
        $this->csatComment = null;

        session()->flash('success', 'Thanks for your feedback!');
    }
}
