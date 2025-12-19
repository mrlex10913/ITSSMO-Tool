<?php

namespace App\Services\Helpdesk;

use App\Mail\TicketCommentAddedMail;
use App\Mail\TicketUpdatedMail;
use App\Models\Helpdesk\Ticket;
use App\Models\Helpdesk\TicketComment;
use App\Models\User;
use App\Models\UserNotificationPreference;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;

class Notifier
{
    /**
     * Resolve recipients for a ticket event, considering watchers and user preferences.
     * $type: comment|status|assignment|escalation
     */
    public static function recipientsFor(Ticket $ticket, string $type, ?int $excludeUserId = null): Collection
    {
        $type = strtolower($type);
        // Watchers disabled: notify requester + assignee only
        $ids = collect([
            $ticket->requester_id,
            $ticket->assignee_id,
        ])->filter()->unique()->values();
        $users = User::whereIn('id', $ids)->get(['id', 'name', 'email']);
        $watchers = $users->map(function ($u) {
            return (object) [
                'user' => $u,
                'notify_comment' => true,
                'notify_status' => true,
                'notify_assignment' => true,
                'notify_escalation' => true,
            ];
        });

        $flagMap = [
            'comment' => 'notify_comment',
            'status' => 'notify_status',
            'assignment' => 'notify_assignment',
            'escalation' => 'notify_escalation',
        ];
        $prefMap = [
            'comment' => 'email_comments',
            'status' => 'email_status',
            'assignment' => 'email_assignment',
            'escalation' => 'email_escalation',
        ];
        $flag = $flagMap[$type] ?? null;
        $pref = $prefMap[$type] ?? null;

        return collect($watchers)
            ->filter(function ($w) use ($flag) {
                return $flag ? (bool) data_get($w, $flag, true) : true;
            })
            ->map(function ($w) {
                return $w->user;
            })
            ->filter(function ($user) use ($pref, $excludeUserId) {
                if (! $user || ! $user->email) {
                    return false;
                }
                if ($excludeUserId && (int) $user->id === (int) $excludeUserId) {
                    return false;
                }
                $p = UserNotificationPreference::where('user_id', $user->id)->first();
                if (! $p) {
                    return true;
                } // default allow

                return $pref ? (bool) data_get($p, $pref, true) : true;
            })
            ->unique('id')
            ->values();
    }

    public static function sendCommentEmails(Ticket $ticket, TicketComment $comment): void
    {
        $users = self::recipientsFor($ticket, 'comment', $comment->user_id);
        foreach ($users as $user) {
            try {
                Mail::to($user->email)->send(new TicketCommentAddedMail($ticket, $comment));
            } catch (\Throwable $e) { /* noop */
            }
        }
    }

    /**
     * @param  array<string, array{from:mixed,to:mixed}>  $changes
     */
    public static function sendUpdateEmails(Ticket $ticket, array $changes, ?int $actorId = null): void
    {
        $types = collect(array_keys($changes))
            ->map(function ($k) {
                return match ($k) {
                    'status' => 'status',
                    'assignee_id' => 'assignment',
                    default => null,
                };
            })
            ->filter()
            ->unique()
            ->values();
        if ($types->isEmpty()) {
            return;
        }

        // Union recipients across relevant types
        $recipients = collect();
        foreach ($types as $t) {
            $recipients = $recipients->merge(self::recipientsFor($ticket, (string) $t, $actorId));
        }
        $recipients = $recipients->unique('id')->values();
        if ($recipients->isEmpty()) {
            return;
        }

        foreach ($recipients as $user) {
            try {
                Mail::to($user->email)->send(new TicketUpdatedMail($ticket, $changes));
            } catch (\Throwable $e) { /* noop */
            }
        }
    }
}
