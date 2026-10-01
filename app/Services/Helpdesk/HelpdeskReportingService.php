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
    public function getDateRange(string $period): array
    {
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

    /**
     * Get ticket volume trends grouped by the period's natural unit.
     *
     * @return Collection<int, array{label: string, created: int, resolved: int, closed: int}>
     */
    public function getTicketVolumeTrends(string $period = 'monthly'): Collection
    {
        $range = $this->getDateRange($period);
        $start = $range['start'];
        $end   = $range['end'];
        $data  = collect();

        if ($period === 'daily') {
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
        } elseif ($period === 'yearly') {
            $created  = $this->fetchGrouped('created_at', $start, $end, 'Y-m');
            $resolved = $this->fetchGrouped('resolved_at', $start, $end, 'Y-m');
            $closed   = $this->fetchGrouped('closed_at', $start, $end, 'Y-m');

            for ($m = 1; $m <= 12; $m++) {
                $key = $start->year . '-' . str_pad($m, 2, '0', STR_PAD_LEFT);
                $data->push([
                    'label'    => Carbon::createFromDate($start->year, $m, 1)->format('M'),
                    'created'  => $created[$key] ?? 0,
                    'resolved' => $resolved[$key] ?? 0,
                    'closed'   => $closed[$key] ?? 0,
                ]);
            }
        } else {
            // weekly & monthly: group by calendar date
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
    public function getAgentPerformance(string $period = 'monthly'): Collection
    {
        $range     = $this->getDateRange($period);
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

    public function getSlaCompliance(string $period = 'monthly'): array
    {
        $range     = $this->getDateRange($period);
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
            'trend'           => $this->buildSlaTrend($period, $startDate, $endDate),
        ];
    }

    public function getSummaryStats(string $period = 'monthly'): array
    {
        $range         = $this->getDateRange($period);
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

    public function getTopCategories(string $period = 'monthly', int $limit = 10): Collection
    {
        $range = $this->getDateRange($period);

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

    private function buildSlaTrend(string $period, Carbon $start, Carbon $end): Collection
    {
        $trend = collect();

        if ($period === 'daily') {
            for ($h = 0; $h <= 23; $h++) {
                $from = $start->copy()->setHour($h)->startOfHour();
                $to   = $start->copy()->setHour($h)->endOfHour();
                $this->pushSlaTrendEntry($trend, $from, $to, $h . ':00');
            }
        } elseif ($period === 'weekly') {
            $current = $start->copy();
            while ($current->lte($end)) {
                $from = $current->copy()->startOfDay();
                $to   = $current->copy()->endOfDay();
                $this->pushSlaTrendEntry($trend, $from, $to, $current->format('D'));
                $current->addDay();
            }
        } elseif ($period === 'monthly') {
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
            // yearly: monthly breakdown
            for ($m = 1; $m <= 12; $m++) {
                $from = Carbon::createFromDate($start->year, $m, 1)->startOfMonth();
                $to   = $from->copy()->endOfMonth();
                $this->pushSlaTrendEntry($trend, $from, $to, $from->format('M'));
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
