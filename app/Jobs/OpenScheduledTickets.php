<?php

namespace App\Jobs;

use App\Events\TicketChanged;
use App\Mail\TicketUpdatedMail;
use App\Models\Helpdesk\Ticket;
use App\Models\Helpdesk\TicketComment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class OpenScheduledTickets implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $now = now();

        // Find scheduled tickets where scheduled_at has passed
        $tickets = Ticket::query()
            ->where('status', 'scheduled')
            ->whereNotNull('scheduled_at')
            ->where('scheduled_at', '<=', $now)
            ->get();

        foreach ($tickets as $ticket) {
            // Update status to open
            $ticket->status = 'open';
            $ticket->save();

            // Add internal comment about auto-open
            TicketComment::create([
                'ticket_id' => $ticket->id,
                'user_id' => null,
                'body' => "📅 **Ticket automatically opened** — Scheduled time reached ({$ticket->scheduled_at->format('M d, Y g:i A')})",
                'is_internal' => true,
            ]);

            // Fire event for realtime UI updates
            try {
                event(new TicketChanged($ticket->id, 'updated'));
            } catch (\Throwable $e) {
                // Noop - broadcasting may not be configured
            }

            // Notify assignee if present
            if ($ticket->assignee_id && $ticket->assignee?->email) {
                try {
                    Mail::to($ticket->assignee->email)
                        ->queue(new TicketUpdatedMail($ticket, ['status' => ['scheduled', 'open']]));
                } catch (\Throwable $e) {
                    // Noop - mail may not be configured
                }
            }

            // Notify requester if they have an email
            $requesterEmail = $ticket->requester?->email ?? $ticket->requester_email;
            if ($requesterEmail) {
                try {
                    Mail::to($requesterEmail)
                        ->queue(new TicketUpdatedMail($ticket, ['status' => ['scheduled', 'open']]));
                } catch (\Throwable $e) {
                    // Noop - mail may not be configured
                }
            }
        }
    }
}
