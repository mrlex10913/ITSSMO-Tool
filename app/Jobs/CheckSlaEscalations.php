<?php

namespace App\Jobs;

use App\Events\TicketChanged;
use App\Models\Helpdesk\SlaEscalation;
use App\Models\Helpdesk\Ticket;
use App\Models\Helpdesk\TicketEscalationLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CheckSlaEscalations implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $now = now();
        // Find tickets with due_at within thresholds
        $tickets = Ticket::query()
            ->whereIn('status', ['open', 'in_progress'])
            ->whereNotNull('sla_due_at')
            ->whereNotNull('sla_policy_id')
            ->get(['id', 'sla_policy_id', 'sla_due_at']);

        if ($tickets->isEmpty()) {
            return;
        }

        $policies = [];
        foreach ($tickets as $t) {
            $pid = (int) $t->sla_policy_id;
            if (! isset($policies[$pid])) {
                $policies[$pid] = SlaEscalation::where('sla_policy_id', $pid)->where('is_active', true)->get();
            }
            foreach ($policies[$pid] as $esc) {
                $thresholdAt = $t->sla_due_at->copy()->subMinutes((int) $esc->threshold_mins_before_breach);
                if ($thresholdAt->lte($now)) {
                    // Check if already notified
                    $exists = TicketEscalationLog::where('ticket_id', $t->id)
                        ->where('sla_escalation_id', $esc->id)
                        ->exists();
                    if (! $exists) {
                        // Log notification and fire generic event for realtime UI; email/notify can be added
                        TicketEscalationLog::create([
                            'ticket_id' => $t->id,
                            'sla_escalation_id' => $esc->id,
                            'notified_at' => now(),
                        ]);
                        try {
                            event(new TicketChanged($t->id, 'escalation'));
                        } catch (\Throwable $e) { /* noop */
                        }
                    }
                }
            }
        }
    }
}
