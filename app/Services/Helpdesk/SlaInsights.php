<?php

namespace App\Services\Helpdesk;

use App\Models\Helpdesk\Ticket;
use Illuminate\Support\Collection;

class SlaInsights
{
    /**
     * Compute SLA insights for a given window (days) and optional scoping.
     * Returns an array with totals and per-category breakdown.
     *
     * @param  int  $windowDays  Days to look back from now()
     * @return array{
     *   window:string,
     *   response_avg_mins:float|null,
     *   resolve_avg_mins:float|null,
     *   responded_count:int,
     *   resolved_count:int,
     *   due_today:int,
     *   breached_open:int,
     *   breach_rate:int,
     *   categories: array<int, array{
     *     id:int|null,name:string,on_time:int,breached:int,on_time_rate:int,response_avg_mins:float|null,resolve_avg_mins:float|null
     *   }>
     * }
     */
    public static function compute(int $windowDays = 30, array $filters = [], string $aggregate = 'daily'): array
    {
        $since = now()->subDays($windowDays);

        // Base tickets in window by creation or resolution
        $ticketsQ = Ticket::query()
            ->with('category:id,name')
            ->where(function ($q) use ($since) {
                $q->where('created_at', '>=', $since)
                    ->orWhere('resolved_at', '>=', $since);
            });

        // Optional filters
        if (! empty($filters['assignee_id'])) {
            $ticketsQ->where('assignee_id', (int) $filters['assignee_id']);
        }
        if (! empty($filters['category_id'])) {
            $ticketsQ->where('category_id', (int) $filters['category_id']);
        }
        if (! empty($filters['requester_id'])) {
            $ticketsQ->where('requester_id', (int) $filters['requester_id']);
        }
        if (! empty($filters['priority'])) {
            $ticketsQ->where('priority', (string) $filters['priority']);
        }
        if (! empty($filters['type'])) {
            $ticketsQ->where('type', (string) $filters['type']);
        }

        $tickets = $ticketsQ->get([
            'id', 'category_id', 'created_at', 'responded_at', 'resolved_at', 'status', 'sla_due_at',
        ]);

        // Response metrics
        $responded = $tickets->filter(fn ($t) => $t->responded_at !== null);
        $responseAvg = self::avgMinutes($responded->map(fn ($t) => [$t->created_at, $t->responded_at]));

        // Resolve metrics
        $resolved = $tickets->filter(fn ($t) => $t->resolved_at !== null);
        $resolveAvg = self::avgMinutes($resolved->map(fn ($t) => [$t->created_at, $t->resolved_at]));

        // Due today and breached open counts (live)
        $todayStart = now()->startOfDay();
        $todayEnd = now()->endOfDay();
        $dueToday = Ticket::query()
            ->whereIn('status', ['open', 'in_progress'])
            ->whereBetween('sla_due_at', [$todayStart, $todayEnd])
            ->count();
        $breachedOpen = Ticket::query()
            ->whereIn('status', ['open', 'in_progress'])
            ->whereNotNull('sla_due_at')
            ->where('sla_due_at', '<', now())
            ->count();

        // Breach rate among resolved tickets in window
        $resolvedWithDue = $resolved->filter(fn ($t) => $t->sla_due_at !== null);
        $resolvedCount = $resolvedWithDue->count();
        $breachedResolved = $resolvedWithDue->filter(fn ($t) => $t->resolved_at->gt($t->sla_due_at))->count();
        $breachRate = $resolvedCount > 0 ? (int) round(($breachedResolved / $resolvedCount) * 100) : 0;

        // Category breakdown (top categories by volume)
        $byCat = $tickets->groupBy(fn ($t) => (int) ($t->category_id ?? 0));
        $categories = [];
        foreach ($byCat as $catId => $group) {
            $name = optional($group->first()->category)->name ?? 'Uncategorized';
            $grpResolved = $group->filter(fn ($t) => $t->resolved_at !== null && $t->sla_due_at !== null);
            $onTime = $grpResolved->filter(fn ($t) => $t->resolved_at->lte($t->sla_due_at))->count();
            $breached = $grpResolved->count() - $onTime;
            $respAvg = self::avgMinutes($group->filter(fn ($t) => $t->responded_at !== null)->map(fn ($t) => [$t->created_at, $t->responded_at]));
            $resAvg = self::avgMinutes($group->filter(fn ($t) => $t->resolved_at !== null)->map(fn ($t) => [$t->created_at, $t->resolved_at]));
            $rate = ($onTime + $breached) > 0 ? (int) round(($onTime / ($onTime + $breached)) * 100) : 0;
            $categories[] = [
                'id' => $catId ?: null,
                'name' => $name,
                'on_time' => $onTime,
                'breached' => $breached,
                'on_time_rate' => $rate,
                'response_avg_mins' => $respAvg,
                'resolve_avg_mins' => $resAvg,
            ];
        }

        // Sort by breached desc, then by name
        usort($categories, function ($a, $b) {
            if ($a['breached'] === $b['breached']) {
                return strcmp($a['name'], $b['name']);
            }

            return $b['breached'] <=> $a['breached'];
        });

        return [
            'window' => $windowDays.'d',
            'response_avg_mins' => $responseAvg,
            'resolve_avg_mins' => $resolveAvg,
            'responded_count' => $responded->count(),
            'resolved_count' => $resolved->count(),
            'due_today' => $dueToday,
            'breached_open' => $breachedOpen,
            'breach_rate' => $breachRate,
            'categories' => $categories,
            'series' => $aggregate === 'weekly' ? self::seriesWeekly($tickets, $windowDays) : self::seriesDaily($tickets, $windowDays),
        ];
    }

    /**
     * @param  Collection<int, array{0:mixed,1:mixed}>  $pairs
     */
    protected static function avgMinutes(Collection $pairs): ?float
    {
        $count = $pairs->count();
        if ($count === 0) {
            return null;
        }
        $sum = 0.0;
        foreach ($pairs as [$a, $b]) {
            $ca = \Illuminate\Support\Carbon::parse($a);
            $cb = \Illuminate\Support\Carbon::parse($b);
            $sum += max(0, $ca->diffInMinutes($cb));
        }

        return round($sum / $count, 1);
    }

    /**
     * Build per-day series arrays for the last N days.
     *
     * @return array{days: array<int,string>, breach_rate: array<int,int>, avg_response_mins: array<int,?float>, avg_resolve_mins: array<int,?float>}
     */
    protected static function seriesDaily(Collection $tickets, int $windowDays): array
    {
        $days = [];
        $breach = [];
        $resp = [];
        $res = [];

        $start = now()->startOfDay()->subDays(max(0, $windowDays - 1));
        for ($i = 0; $i < $windowDays; $i++) {
            $dayStart = (clone $start)->addDays($i);
            $dayEnd = (clone $dayStart)->endOfDay();
            $label = $dayStart->format('m-d');
            $days[] = $label;

            $resolvedDay = $tickets->filter(fn ($t) => $t->resolved_at !== null && $t->resolved_at->between($dayStart, $dayEnd));
            $resolvedWithDue = $resolvedDay->filter(fn ($t) => $t->sla_due_at !== null);
            $countResolved = $resolvedWithDue->count();
            $breachedCount = $resolvedWithDue->filter(fn ($t) => $t->resolved_at->gt($t->sla_due_at))->count();
            $breach[] = $countResolved > 0 ? (int) round(($breachedCount / $countResolved) * 100) : 0;

            $respondedDayPairs = $tickets
                ->filter(fn ($t) => $t->responded_at !== null && $t->responded_at->between($dayStart, $dayEnd))
                ->map(fn ($t) => [$t->created_at, $t->responded_at]);
            $resp[] = self::avgMinutes($respondedDayPairs);

            $resolvedDayPairs = $resolvedDay->map(fn ($t) => [$t->created_at, $t->resolved_at]);
            $res[] = self::avgMinutes($resolvedDayPairs);
        }

        return [
            'days' => $days,
            'breach_rate' => $breach,
            'avg_response_mins' => $resp,
            'avg_resolve_mins' => $res,
        ];
    }

    /**
     * Weekly aggregated series for longer windows.
     */
    protected static function seriesWeekly(Collection $tickets, int $windowDays): array
    {
        $weeks = (int) max(1, ceil($windowDays / 7));
        $start = now()->startOfDay()->subDays(max(0, $windowDays - 1));

        $labels = [];
        $breach = [];
        $resp = [];
        $res = [];

        for ($w = 0; $w < $weeks; $w++) {
            $periodStart = (clone $start)->addDays($w * 7);
            $periodEnd = (clone $periodStart)->addDays(6)->endOfDay();
            if ($periodEnd->gt(now())) {
                $periodEnd = now()->endOfDay();
            }
            $labels[] = 'Wk '.$periodStart->format('m-d');

            $resolvedWeek = $tickets->filter(fn ($t) => $t->resolved_at !== null && $t->resolved_at->between($periodStart, $periodEnd));
            $resolvedWithDue = $resolvedWeek->filter(fn ($t) => $t->sla_due_at !== null);
            $countResolved = $resolvedWithDue->count();
            $breachedCount = $resolvedWithDue->filter(fn ($t) => $t->resolved_at->gt($t->sla_due_at))->count();
            $breach[] = $countResolved > 0 ? (int) round(($breachedCount / $countResolved) * 100) : 0;

            $respondedPairs = $tickets
                ->filter(fn ($t) => $t->responded_at !== null && $t->responded_at->between($periodStart, $periodEnd))
                ->map(fn ($t) => [$t->created_at, $t->responded_at]);
            $resp[] = self::avgMinutes($respondedPairs);

            $resolvedPairs = $resolvedWeek->map(fn ($t) => [$t->created_at, $t->resolved_at]);
            $res[] = self::avgMinutes($resolvedPairs);
        }

        return [
            'days' => $labels,
            'breach_rate' => $breach,
            'avg_response_mins' => $resp,
            'avg_resolve_mins' => $res,
        ];
    }
}
