<?php

namespace App\Observers;

use App\Models\Helpdesk\TicketActivityLog;
use App\Models\Helpdesk\TicketComment;
use App\Services\Helpdesk\Notifier;
use Illuminate\Support\Facades\Auth;

class TicketCommentObserver
{
    public function created(TicketComment $comment): void
    {
        TicketActivityLog::create([
            'ticket_id' => $comment->ticket_id,
            'user_id' => $comment->user_id ?? Auth::id(),
            'action' => $comment->is_internal ? 'comment_internal' : 'comment_public',
            'message' => 'Comment added',
            'meta' => [
                'length' => mb_strlen((string) $comment->body),
            ],
        ]);

        // Email watchers for public comments only
        try {
            if (! (bool) $comment->is_internal) {
                $ticket = $comment->ticket()->with(['requester:id,name,email'])->first();
                if ($ticket) {
                    Notifier::sendCommentEmails($ticket, $comment);
                }
            }
        } catch (\Throwable $e) { /* noop */
        }
    }
}
