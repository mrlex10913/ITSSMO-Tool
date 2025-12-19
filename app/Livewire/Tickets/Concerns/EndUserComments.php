<?php

namespace App\Livewire\Tickets\Concerns;

use App\Events\TicketChanged;
use App\Events\TicketCommentCreated;
use App\Models\Helpdesk\Ticket;
use App\Models\Helpdesk\TicketAttachment;
use App\Models\Helpdesk\TicketComment;
use App\Models\Roles;
use Illuminate\Support\Facades\Auth;

trait EndUserComments
{
    protected function isAgentUser(): bool
    {
        $user = Auth::user();
        if (! $user || ! $user->role_id) {
            return false;
        }
        $slug = strtolower((string) optional(Roles::find($user->role_id))->slug);

        return in_array($slug, ['itss', 'administrator', 'developer']);
    }

    protected function authorizeEndUserOrAgent(Ticket $ticket): void
    {
        $user = Auth::user();
        if (! $user) {
            abort(403);
        }

        // Agents can view any ticket
        if ($this->isAgentUser()) {
            return;
        }

        // Requester (owner) can view
        if ($ticket->requester_id === $user->id) {
            return;
        }

        // Same-department visibility (if both sides have a department value)
        $userDept = strtolower(trim((string) ($user->department ?? '')));
        $ticketDept = strtolower(trim((string) ($ticket->department ?? '')));
        if ($userDept !== '' && $ticketDept !== '' && $userDept === $ticketDept) {
            return;
        }

        abort(403);
    }

    protected function loadTicketBase(Ticket $ticket): Ticket
    {
        return $ticket->load(['requester:id,name,email', 'category:id,name', 'attachments']);
    }

    protected function getPublicCommentsFor(int $ticketId)
    {
        return TicketComment::with('user:id,name')
            ->where('ticket_id', $ticketId)
            ->where('is_internal', false)
            ->orderBy('id')
            ->get();
    }

    protected function createEndUserCommentWithOptionalPhoto(Ticket $ticket, ?string $body = null, $photo = null): TicketComment
    {
        $text = ($body && trim($body) !== '') ? trim($body) : ' ';
        $comment = TicketComment::create([
            'ticket_id' => $ticket->id,
            'user_id' => Auth::id(),
            'is_internal' => false,
            'body' => $text,
        ]);

        if ($photo) {
            $storedPath = $photo->store("tickets/{$ticket->id}", 'private');
            TicketAttachment::create([
                'ticket_id' => $ticket->id,
                'ticket_comment_id' => $comment->id,
                'user_id' => Auth::id(),
                'type' => 'user_followup',
                'disk' => 'private',
                'path' => $storedPath,
                'filename' => method_exists($photo, 'getClientOriginalName') ? ($photo->getClientOriginalName() ?? basename($storedPath)) : basename($storedPath),
                'mime' => method_exists($photo, 'getMimeType') ? $photo->getMimeType() : null,
                'size' => method_exists($photo, 'getSize') ? $photo->getSize() : null,
            ]);
        }
        // Broadcast new comment (non-internal) and notify agents to refresh lists
        try {
            event(new TicketCommentCreated($comment->load(['user', 'ticket'])));
        } catch (\Throwable $e) {
        }
        try {
            event(new TicketChanged($ticket->id, 'commented'));
        } catch (\Throwable $e) {
        }

        return $comment;
    }
}
