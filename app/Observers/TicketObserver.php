<?php

namespace App\Observers;

use App\Models\Helpdesk\CsatResponse;
use App\Models\Helpdesk\Ticket;
use App\Models\Helpdesk\TicketActivityLog;
use App\Models\Helpdesk\TicketAuditLog;
use App\Models\User;
use App\Services\Helpdesk\Notifier;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class TicketObserver
{
    public function created(Ticket $ticket): void
    {
        TicketActivityLog::create([
            'ticket_id' => $ticket->id,
            'user_id' => Auth::id(),
            'action' => 'created',
            'message' => 'Ticket created',
            'meta' => [
                'status' => $ticket->status,
                'priority' => $ticket->priority,
                'type' => $ticket->type,
            ],
        ]);

        // Watchers disabled: do not auto-create watcher rows

        // Audit (immutable)
        TicketAuditLog::create([
            'ticket_id' => $ticket->id,
            'user_id' => Auth::id(),
            'event' => 'created',
            'changes' => null,
            'ip_address' => request()?->ip(),
            'user_agent' => request()?->header('User-Agent'),
        ]);
    }

    public function updated(Ticket $ticket): void
    {
        $changes = $ticket->getChanges();
        $original = $ticket->getOriginal();
        $map = [
            'status' => 'updated_status',
            'assignee_id' => 'updated_assignee',
            'type' => 'updated_type',
            'priority' => 'updated_priority',
            'verification_status' => 'updated_verification',
        ];
        foreach ($map as $field => $action) {
            if (array_key_exists($field, $changes)) {
                $message = null;
                $meta = [
                    'from' => $original[$field] ?? null,
                    'to' => $changes[$field] ?? null,
                ];

                if ($field === 'assignee_id') {
                    $fromId = $original['assignee_id'] ?? null;
                    $toId = $changes['assignee_id'] ?? null;
                    $fromName = $fromId ? optional(User::find($fromId))->name : 'Unassigned';
                    $toName = $toId ? optional(User::find($toId))->name : 'Unassigned';
                    // Fallback to ID label if name missing
                    if ($fromId && ! $fromName) {
                        $fromName = 'User #'.$fromId;
                    }
                    if ($toId && ! $toName) {
                        $toName = 'User #'.$toId;
                    }
                    $message = sprintf('Assignee changed: %s → %s', $fromName, $toName);
                    $meta['from_name'] = $fromName;
                    $meta['to_name'] = $toName;
                } else {
                    $message = match ($field) {
                        'status' => sprintf('Status changed: %s → %s', (string) ($original['status'] ?? ''), (string) $ticket->status),
                        'type' => sprintf('Type changed: %s → %s', (string) ($original['type'] ?? ''), (string) $ticket->type),
                        'priority' => sprintf('Priority changed: %s → %s', (string) ($original['priority'] ?? ''), (string) $ticket->priority),
                        'verification_status' => sprintf('Verification: %s', (string) $ticket->verification_status),
                        default => 'Updated',
                    };
                }

                TicketActivityLog::create([
                    'ticket_id' => $ticket->id,
                    'user_id' => Auth::id(),
                    'action' => $action,
                    'message' => $message,
                    'meta' => $meta,
                ]);
            }
        }

        // Audit diff with blacklist and hashing for large fields
        $diff = [];
        $blacklist = ['updated_at', 'created_at', 'ticket_no'];
        $hashFields = ['description'];
        foreach ($changes as $field => $newVal) {
            if (in_array($field, $blacklist, true)) {
                continue;
            }
            if (in_array($field, $hashFields, true)) {
                $from = (string) ($original[$field] ?? '');
                $to = (string) $newVal;
                $diff[$field] = [
                    'from_hash' => hash('sha256', $from),
                    'to_hash' => hash('sha256', $to),
                    'from_len' => mb_strlen($from),
                    'to_len' => mb_strlen($to),
                ];

                continue;
            }
            $diff[$field] = [
                'from' => $original[$field] ?? null,
                'to' => $newVal,
            ];
        }
        if (! empty($diff)) {
            TicketAuditLog::create([
                'ticket_id' => $ticket->id,
                'user_id' => Auth::id(),
                'event' => 'updated',
                'changes' => $diff,
                'ip_address' => request()?->ip(),
                'user_agent' => request()?->header('User-Agent'),
            ]);
        }

        // Email on important updates (status or assignment)
        try {
            $notifyChanges = [];
            foreach (['status', 'assignee_id'] as $k) {
                if (array_key_exists($k, $changes)) {
                    $notifyChanges[$k] = [
                        'from' => $original[$k] ?? null,
                        'to' => $changes[$k] ?? null,
                    ];
                }
            }
            if (! empty($notifyChanges)) {
                $ticket->loadMissing(['requester:id,name,email', 'assignee:id,name,email']);
                Notifier::sendUpdateEmails($ticket, $notifyChanges, Auth::id());
            }
        } catch (\Throwable $e) { /* noop */
        }

        // On resolve/close, send CSAT invitation if applicable
        try {
            if (array_key_exists('status', $changes) && in_array((string) $ticket->status, ['resolved', 'closed'], true)) {
                // Do not spam: if already submitted, skip; if invite exists and was sent in last 3 days, skip
                $existing = CsatResponse::where('ticket_id', $ticket->id)->latest('id')->first();
                if ($existing && $existing->submitted_at) {
                    // already rated
                } else {
                    $shouldSend = true;
                    if ($existing && $existing->sent_at && $existing->sent_at->gt(now()->subDays(3))) {
                        $shouldSend = false;
                    }
                    if ($shouldSend) {
                        $invite = $existing ?? new CsatResponse;
                        $invite->ticket_id = $ticket->id;
                        $invite->user_id = $ticket->requester_id;
                        $invite->requester_email = $ticket->requester->email ?? $ticket->requester_email;
                        $invite->token = Str::random(48);
                        $invite->expires_at = now()->addDays(14);
                        $invite->sent_at = now();
                        $invite->save();

                        // Email send disabled: we now prompt CSAT in-app on ticket view
                    }
                }
            }
        } catch (\Throwable $e) { /* noop */
        }
    }

    public function deleted(Ticket $ticket): void
    {
        try {
            TicketAuditLog::create([
                'ticket_id' => $ticket->id,
                'user_id' => Auth::id(),
                'event' => 'deleted',
                'changes' => null,
                'ip_address' => request()?->ip(),
                'user_agent' => request()?->header('User-Agent'),
            ]);
        } catch (\Throwable $e) { /* noop */
        }
    }
}
