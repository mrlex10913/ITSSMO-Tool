<?php

namespace App\Console\Commands;

use App\Models\Helpdesk\Ticket;
use App\Services\Helpdesk\SlaResolver;
use Illuminate\Console\Command;

class BackfillTicketSla extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tickets:sla-backfill {--limit=1000} {--dry-run}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backfill SLA policy and due date for tickets missing sla_due_at';

    public function handle(): int
    {
        $limit = (int) $this->option('limit');
        $dry = (bool) $this->option('dry-run');

        $q = Ticket::query()
            ->whereNull('sla_due_at')
            ->whereIn('status', ['open', 'in_progress'])
            ->orderBy('id');

        $count = (clone $q)->count();
        $this->info("Found {$count} tickets missing SLA due date.");

        $processed = 0;
        $updated = 0;
        $skipped = 0;

        $q->limit($limit)->chunkById(200, function ($tickets) use (&$processed, &$updated, &$skipped, $dry) {
            foreach ($tickets as $t) {
                $processed++;
                $type = $t->type ?: 'incident';
                $priority = strtolower($t->priority ?: 'medium');
                $policy = SlaResolver::pickPolicy($type, $priority);
                $mins = $policy?->resolve_mins ? (int) $policy->resolve_mins : null;

                if (! $mins || ! $t->created_at) {
                    $skipped++;
                    $this->warn("Skipping #{$t->id} (type={$type}, priority={$priority}) - no policy or missing created_at.");

                    continue;
                }

                $due = $t->created_at->copy()->addMinutes((int) $mins);

                if ($dry) {
                    $this->line("[DRY] Ticket #{$t->id} -> policy={$policy?->id} due={$due}");
                } else {
                    $t->sla_policy_id = $policy?->id;
                    $t->sla_due_at = $due;
                    $t->save();
                }
                $updated++;
            }
        });

        $this->info("Processed: {$processed}, Updated: {$updated}, Skipped: {$skipped}");

        return self::SUCCESS;
    }
}
