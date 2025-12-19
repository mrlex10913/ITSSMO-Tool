<?php

namespace App\Jobs;

use App\Events\TicketChanged;
use App\Models\Helpdesk\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CheckSlaBreaches implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $now = now();
        $breached = Ticket::query()
            ->whereIn('status', ['open', 'in_progress'])
            ->whereNotNull('sla_due_at')
            ->where('sla_due_at', '<', $now)
            ->limit(200)
            ->get(['id']);

        foreach ($breached as $t) {
            try {
                event(new TicketChanged($t->id, 'updated'));
            } catch (\Throwable $e) { /* noop */
            }
        }
    }
}
