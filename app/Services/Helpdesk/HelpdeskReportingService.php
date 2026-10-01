<?php

namespace App\Services\Helpdesk;

use App\Models\Helpdesk\Ticket;
use App\Models\Helpdesk\TicketComment;
use App\Models\Helpdesk\TicketTimeEntry;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class HelpdeskReportingService
{
    public function getDateRange(string $period, ?string $from = null, ?string $to = null): array
    {
        if ($from && $to) {
            $start    = Carbon::parse($from)->startOfDay();
            $end      = Carbon::parse($to)->endOfDay();
            $diffDays = (int) $start->diffInDays($end) + 1;
            return [
                'start'     => $start,
                'end'       => $end,
                'prevStart' => $start->copy()->subDays($diffDays),
                'prevEnd'   => $start->copy()->subDay()->endOfDay(),
            ];
        }

        return match ($period) {
            'daily' => [
                'start'     => now()->startOfDay(),
                'end'       => now()->endOfDay(),
                'prevStart' => now()->subDay()->startOfDay(),
                'prevEnd'   => now()->subDay()->endOfDay(),
            ],
            'weekly' => [
                'start'     => now()->startOfWeek(),
                'end'       => now()->endOfWeek(),
                'prevStart' => now()->subWeek()->startOfWeek(),
                'prevEnd'   => now()->subWeek()->endOfWeek(),
            ],
            'yearly' => [
                'start'     => now()->startOfYear(),
                'end'       => now()->endOfYear(),
                'prevStart' => now()->subYear()->startOfYear(),
                'prevEnd'   => now()->subYear()->endOfYear(),
            ],
            default => [ // monthly
                'start'     => now()->startOfMonth(),
                'end'       => now()->endOfMonth(),
                'prevStart' => now()->subMonth()->startOfMonth(),
                'prevEnd'   => now()->subMonth()->endOfMonth(),
            ],
        };
    }

    private function resolveGroupBy(string $period, Carbon $start, Carbon $end): string
    {
        if ($period !== 'custom') {
            return match ($period) {
                'daily'  => 'hour',
                'yearly' => 'month',
                default  => 'day',
            };
        }

        $days = (int) $start->diffInDays($end) + 1;

        return match (true) {
            $days <= 2   => 'hour',
            $days <= 90  => 'day',
            default      => 'month',
        };
    }

    /**
     * Get ticket volume trends grouped by the period's natural unit.
     *
     * @return Collection<int, array{label: string, created: int, resolved: int, closed: int}>
     */
    public function getTicketVolumeTrends(string $period = 'monthly', ?string $from = null, ?string $to = null): Collection
    {
        $range   = $this->getDateRange($period, $from, $to);
        $start   = $range['start'];
        $end     = $range['end'];
        $groupBy = $this->resolveGroupBy($period, $start, $end);
        $data    = collect();

        if ($groupBy === 'hour') {
            $created  = $this->fetchGrouped('created_at', $start, $end, 'H');
            $resolved = $this->fetchGrouped('resolved_at', $start, $end, 'H');
            $closed   = $this->fetchGrouped('closed_at', $start, $end, 'H');

            for ($h = 0; $h <= 23; $h++) {
                $key = str_pad($h, 2, '0', STR_PAD_LEFT);
                $data->push([
                    'label'    => $h . ':00',
                    'created'  => $created[$key] ?? 0,
                    'resolved' => $resolved[$key] ?? 0,
                    'closed'   => $closed[$key] ?? 0,
                ]);
            }
        } elseif ($groupBy === 'month') {
            $created  = $this->fetchGrouped('created_at', $start, $end, 'Y-m');
            $resolved = $this->fetchGrouped('resolved_at', $start, $end, 'Y-m');
            $closed   = $this->fetchGrouped('closed_at', $start, $end, 'Y-m');

            $cursor = $start->copy()->startOfMonth();
            while ($cursor->lte($end)) {
                $key = $cursor->format('Y-m');
                $data->push([
                    'label'    => $cursor->format('M Y'),
                    'created'  => $created[$key] ?? 0,
                    'resolved' => $resolved[$key] ?? 0,
                    'closed'   => $closed[$key] ?? 0,
                ]);
                $cursor->addMonth();
            }
        } else {
            // day-level grouping
            $created  = $this->fetchGrouped('created_at', $start, $end, 'Y-m-d');
            $resolved = $this->fetchGrouped('resolved_at', $start, $end, 'Y-m-d');
            $closed   = $this->fetchGrouped('closed_at', $start, $end, 'Y-m-d');

            $current = $start->copy();
            while ($current->lte($end)) {
                $key = $current->format('Y-m-d');
                $data->push([
                    'label'    => $period === 'weekly' ? $current->format('D') : $current->format('M j'),
                    'created'  => $created[$key] ?? 0,
                    'resolved' => $resolved[$key] ?? 0,
                    'closed'   => $closed[$key] ?? 0,
                ]);
                $current->addDay();
            }
        }

        return $data;
    }

    /** @return Collection<int, array> */
    public function getAgentPerformance(string $period = 'monthly', ?string $from = null, ?string $to = null): Collection
    {
        $range     = $this->getDateRange($period, $from, $to);
        $startDate = $range['start'];
        $endDate   = $range['end'];

        $agents = User::whereHas('role', fn ($q) => $q->whereIn('slug', ['itss', 'administrator', 'developer']))
            ->get(['id', 'name']);

        return $agents->map(function ($agent) use ($startDate, $endDate) {
            $assigned = Ticket::where('assignee_id', $agent->id)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count();

            $resolved = Ticket::where('assignee_id', $agent->id)
                ->whereNotNull('resolved_at')
                ->whereBetween('resolved_at', [$startDate, $endDate])
                ->count();

            $resolvedTickets = Ticket::where('assignee_id', $agent->id)
                ->whereNotNull('resolved_at')
                ->whereBetween('resolved_at', [$startDate, $endDate])
                ->get(['created_at', 'resolved_at']);

            $avgResolutionTime = $resolvedTickets->isNotEmpty()
                ? $resolvedTickets->avg(fn ($t) => Carbon::parse($t->created_at)->diffInMinutes(Carbon::parse($t->resolved_at)))
                : null;

            $respondedTickets = Ticket::where('assignee_id', $agent->id)
                ->whereNotNull('responded_at')
                ->whereBetween('responded_at', [$startDate, $endDate])
                ->get(['created_at', 'responded_at']);

            $avgFrt = $respondedTickets->isNotEmpty()
                ? $respondedTickets->avg(fn ($t) => Carbon::parse($t->created_at)->diffInMinutes(Carbon::parse($t->responded_at)))
                : null;

            $comments = TicketComment::where('user_id', $agent->id)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count();

            $slaTotal = Ticket::where('assignee_id', $agent->id)
                ->whereNotNull('sla_due_at')
                ->whereNotNull('resolved_at')
                ->whereBetween('resolved_at', [$startDate, $endDate])
                ->count();

            $slaCompliant = Ticket::where('assignee_id', $agent->id)
                ->whereNotNull('sla_due_at')
                ->whereNotNull('resolved_at')
                ->whereBetween('resolved_at', [$startDate, $endDate])
                ->whereColumn('resolved_at', '<=', 'sla_due_at')
                ->count();

            $timeLogged = TicketTimeEntry::where('user_id', $agent->id)
                ->whereBetween('work_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                ->sum('duration_mins');

            $csatResponses = DB::table('csat_responses')
                ->join('tickets', 'csat_responses.ticket_id', '=', 'tickets.id')
                ->where('tickets.assignee_id', $agent->id)
                ->whereNotNull('csat_responses.submitted_at')
                ->whereBetween('csat_responses.submitted_at', [$startDate, $endDate])
                ->get(['csat_responses.rating']);

            $csatTotal = $csatResponses->count();
            $csatGood  = $csatResponses->where('rating', 'good')->count();

            return [
                'id'                   => $agent->id,
                'name'                 => $agent->name,
                'assigned'             => $assigned,
                'resolved'             => $resolved,
                'resolution_rate'      => $assigned > 0 ? round(($resolved / $assigned) * 100, 1) : 0,
                'avg_resolution_hours' => $avgResolutionTime ? round($avgResolutionTime / 60, 1) : null,
                'avg_frt_mins'         => $avgFrt ? round($avgFrt, 0) : null,
                'comments'             => $comments,
                'sla_compliance'       => $slaTotal > 0 ? round(($slaCompliant / $slaTotal) * 100, 1) : null,
                'time_logged_hours'    => round($timeLogged / 60, 1),
                'csat_score'           => $csatTotal > 0 ? round(($csatGood / $csatTotal) * 100, 1) : null,
                'csat_total'           => $csatTotal,
            ];
        })->sortByDesc('resolved')->values();
    }

    public function getSlaCompliance(string $period = 'monthly', ?string $from = null, ?string $to = null): array
    {
        $range     = $this->getDateRange($period, $from, $to);
        $startDate = $range['start'];
        $endDate   = $range['end'];

        $total   = $this->slaCount($startDate, $endDate);
        $breached = $this->slaBreachedCount($startDate, $endDate);
        $compliant = $total - $breached;

        $byPriority = collect(Ticket::PRIORITIES)->map(function ($priority) use ($startDate, $endDate) {
            $t = Ticket::whereNotNull('sla_due_at')
                ->where('priority', $priority)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count();

            $b = Ticket::whereNotNull('sla_due_at')
                ->where('priority', $priority)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->where(fn ($q) => $this->applyBreachCondition($q))
                ->count();

            return [
                'priority'        => $priority,
                'total'           => $t,
                'compliant'       => $t - $b,
                'breached'        => $b,
                'compliance_rate' => $t > 0 ? round((($t - $b) / $t) * 100, 1) : 100,
            ];
        });

        return [
            'total'           => $total,
            'compliant'       => $compliant,
            'breached'        => $breached,
            'compliance_rate' => $total > 0 ? round(($compliant / $total) * 100, 1) : 100,
            'by_priority'     => $byPriority,
            'trend'           => $this->buildSlaTrend($period, $startDate, $endDate, $this->resolveGroupBy($period, $startDate, $endDate)),
        ];
    }

    public function getSummaryStats(string $period = 'monthly', ?string $from = null, ?string $to = null): array
    {
        $range         = $this->getDateRange($period, $from, $to);
        $startDate     = $range['start'];
        $endDate       = $range['end'];
        $prevStartDate = $range['prevStart'];
        $prevEndDate   = $range['prevEnd'];

        $created  = Ticket::whereBetween('created_at', [$startDate, $endDate])->count();
        $resolved = Ticket::whereNotNull('resolved_at')
            ->whereBetween('resolved_at', [$startDate, $endDate])->count();
        $open = Ticket::whereIn('status', ['open', 'in_progress'])->count();

        $prevCreated  = Ticket::whereBetween('created_at', [$prevStartDate, $prevEndDate])->count();
        $prevResolved = Ticket::whereNotNull('resolved_at')
            ->whereBetween('resolved_at', [$prevStartDate, $prevEndDate])->count();

        $resolvedTickets = Ticket::whereNotNull('resolved_at')
            ->whereBetween('resolved_at', [$startDate, $endDate])
            ->get(['created_at', 'resolved_at']);

        $avgResolution = $resolvedTickets->isNotEmpty()
            ? $resolvedTickets->avg(fn ($t) => Carbon::parse($t->created_at)->diffInHours(Carbon::parse($t->resolved_at)))
            : null;

        $respondedTickets = Ticket::whereNotNull('responded_at')
            ->whereBetween('responded_at', [$startDate, $endDate])
            ->get(['created_at', 'responded_at']);

        $avgFrt = $respondedTickets->isNotEmpty()
            ? $respondedTickets->avg(fn ($t) => Carbon::parse($t->created_at)->diffInMinutes(Carbon::parse($t->responded_at)))
            : null;

        $csatResponses = DB::table('csat_responses')
            ->whereNotNull('submitted_at')
            ->whereBetween('submitted_at', [$startDate, $endDate])
            ->get(['rating']);

        $csatTotal = $csatResponses->count();
        $csatGood  = $csatResponses->where('rating', 'good')->count();

        $slaTotal   = $this->slaCount($startDate, $endDate);
        $slaBreached = $this->slaBreachedCount($startDate, $endDate);
        $slaRate     = $slaTotal > 0 ? round((($slaTotal - $slaBreached) / $slaTotal) * 100, 1) : null;

        $byStatus = Ticket::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $byType = Ticket::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('type, COUNT(*) as count')
            ->groupBy('type')
            ->pluck('count', 'type')
            ->toArray();

        $byPriority = Ticket::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('priority, COUNT(*) as count')
            ->groupBy('priority')
            ->pluck('count', 'priority')
            ->toArray();

        return [
            'created'              => $created,
            'created_change'       => $prevCreated > 0 ? round((($created - $prevCreated) / $prevCreated) * 100, 1) : 0,
            'resolved'             => $resolved,
            'resolved_change'      => $prevResolved > 0 ? round((($resolved - $prevResolved) / $prevResolved) * 100, 1) : 0,
            'open'                 => $open,
            'avg_resolution_hours' => $avgResolution ? round($avgResolution, 1) : null,
            'avg_frt_mins'         => $avgFrt ? round($avgFrt, 0) : null,
            'csat_score'           => $csatTotal > 0 ? round(($csatGood / $csatTotal) * 100, 1) : null,
            'sla_rate'             => $slaRate,
            'by_status'            => $byStatus,
            'by_type'              => $byType,
            'by_priority'          => $byPriority,
        ];
    }

    public function getTopCategories(string $period = 'monthly', int $limit = 10, ?string $from = null, ?string $to = null): Collection
    {
        $range = $this->getDateRange($period, $from, $to);

        return Ticket::with('category:id,name')
            ->whereBetween('created_at', [$range['start'], $range['end']])
            ->whereNotNull('category_id')
            ->selectRaw('category_id, COUNT(*) as count')
            ->groupBy('category_id')
            ->orderByDesc('count')
            ->limit($limit)
            ->get()
            ->map(fn ($row) => [
                'name'  => $row->category?->name ?? 'Uncategorized',
                'count' => $row->count,
            ]);
    }

    // ─── Private helpers ─────────────────────────────────────────────────────

    private function fetchGrouped(string $column, Carbon $start, Carbon $end, string $format): Collection
    {
        return Ticket::whereNotNull($column)
            ->whereBetween($column, [$start, $end])
            ->get([$column])
            ->groupBy(fn ($t) => Carbon::parse($t->{$column})->format($format))
            ->map(fn ($g) => $g->count());
    }

    private function slaCount(Carbon $start, Carbon $end): int
    {
        return Ticket::whereNotNull('sla_due_at')
            ->whereBetween('created_at', [$start, $end])
            ->count();
    }

    private function slaBreachedCount(Carbon $start, Carbon $end): int
    {
        return Ticket::whereNotNull('sla_due_at')
            ->whereBetween('created_at', [$start, $end])
            ->where(fn ($q) => $this->applyBreachCondition($q))
            ->count();
    }

    private function applyBreachCondition($query): void
    {
        $query->where(function ($sub) {
            $sub->whereNotNull('resolved_at')
                ->whereColumn('resolved_at', '>', 'sla_due_at');
        })->orWhere(function ($sub) {
            $sub->whereNull('resolved_at')
                ->where('sla_due_at', '<', now());
        });
    }

    private function buildSlaTrend(string $period, Carbon $start, Carbon $end, string $groupBy = 'day'): Collection
    {
        $trend = collect();

        if ($groupBy === 'hour') {
            for ($h = 0; $h <= 23; $h++) {
                $from = $start->copy()->setHour($h)->startOfHour();
                $to   = $start->copy()->setHour($h)->endOfHour();
                $this->pushSlaTrendEntry($trend, $from, $to, $h . ':00');
            }
        } elseif ($groupBy === 'day') {
            $current = $start->copy();
            while ($current->lte($end)) {
                $from = $current->copy()->startOfDay();
                $to   = $current->copy()->endOfDay();
                $this->pushSlaTrendEntry($trend, $from, $to, $current->format($period === 'weekly' ? 'D' : 'M j'));
                $current->addDay();
            }
        } elseif ($groupBy === 'month' && $period === 'monthly') {
            $weekNum = 1;
            $current = $start->copy()->startOfWeek();
            while ($current->lte($end)) {
                $from = $current->copy();
                $to   = $current->copy()->endOfWeek()->min($end);
                $this->pushSlaTrendEntry($trend, $from, $to, 'Wk ' . $weekNum);
                $current->addWeek();
                $weekNum++;
            }
        } else {
            // monthly grouping (yearly or custom wide range)
            $cursor = $start->copy()->startOfMonth();
            while ($cursor->lte($end)) {
                $from = $cursor->copy()->startOfMonth();
                $to   = $cursor->copy()->endOfMonth();
                $this->pushSlaTrendEntry($trend, $from, $to, $cursor->format('M Y'));
                $cursor->addMonth();
            }
        }

        return $trend;
    }

    private function pushSlaTrendEntry(Collection $trend, Carbon $from, Carbon $to, string $label): void
    {
        $t = $this->slaCount($from, $to);
        $b = $this->slaBreachedCount($from, $to);

        $trend->push([
            'label'           => $label,
            'total'           => $t,
            'compliant'       => $t - $b,
            'breached'        => $b,
            'compliance_rate' => $t > 0 ? round((($t - $b) / $t) * 100, 1) : 100,
        ]);
    }
}
