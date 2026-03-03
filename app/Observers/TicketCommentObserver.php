<?php

namespace App\Observers;

use App\Models\Helpdesk\TicketActivityLog;
use App\Models\Helpdesk\TicketComment;
use App\Models\Roles;
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

        // Auto-set responded_at on first public agent response (FRT tracking)
        $this->trackFirstResponseTime($comment);

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

    /**
     * Track First Response Time (FRT) by setting responded_at when:
     * - Comment is public (not internal)
     * - Commenter is an agent (itss, administrator, developer)
     * - Ticket hasn't been responded to yet
     * - Commenter is not the requester
     */
    protected function trackFirstResponseTime(TicketComment $comment): void
    {
        try {
            // Only track public comments
            if ((bool) $comment->is_internal) {
                return;
            }

            $ticket = $comment->ticket;
            if (! $ticket) {
                return;
            }

            // Skip if already responded
            if ($ticket->responded_at !== null) {
                return;
            }

            // Skip if commenter is the requester (self-reply doesn't count as agent response)
            if ($comment->user_id && $comment->user_id === $ticket->requester_id) {
                return;
            }

            // Check if commenter is an agent
            $user = $comment->user;
            if (! $user || ! $user->role_id) {
                return;
            }

            $roleSlug = strtolower((string) optional(Roles::find($user->role_id))->slug);
            $isAgent = in_array($roleSlug, ['itss', 'administrator', 'developer']);

            if ($isAgent) {
                $ticket->responded_at = now();
                $ticket->saveQuietly(); // Avoid triggering observer loops

                TicketActivityLog::create([
                    'ticket_id' => $ticket->id,
                    'user_id' => $comment->user_id,
                    'action' => 'first_response',
                    'message' => 'First agent response recorded',
                    'meta' => [
                        'response_time_mins' => $ticket->created_at->diffInMinutes(now()),
                    ],
                ]);
            }
        } catch (\Throwable $e) {
            // Silently fail - FRT tracking is not critical
        }
    }
}
