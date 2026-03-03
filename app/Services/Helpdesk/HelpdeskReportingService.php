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
    /**
     * Get ticket volume trends over a period.
     *
     * @return Collection<int, array{date: string, created: int, resolved: int, closed: int}>
     */
    public function getTicketVolumeTrends(int $days = 30): Collection
    {
        $startDate = now()->subDays($days)->startOfDay();

        // Use PHP-based grouping for database compatibility
        $createdTickets = Ticket::where('created_at', '>=', $startDate)
            ->get(['created_at'])
            ->groupBy(fn ($t) => Carbon::parse($t->created_at)->format('Y-m-d'))
            ->map(fn ($g) => $g->count());

        $resolvedTickets = Ticket::whereNotNull('resolved_at')
            ->where('resolved_at', '>=', $startDate)
            ->get(['resolved_at'])
            ->groupBy(fn ($t) => Carbon::parse($t->resolved_at)->format('Y-m-d'))
            ->map(fn ($g) => $g->count());

        $closedTickets = Ticket::whereNotNull('closed_at')
            ->where('closed_at', '>=', $startDate)
            ->get(['closed_at'])
            ->groupBy(fn ($t) => Carbon::parse($t->closed_at)->format('Y-m-d'))
            ->map(fn ($g) => $g->count());

        // Build daily data
        $data = collect();
        for ($i = $days; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $data->push([
                'date' => $date,
                'label' => Carbon::parse($date)->format('M j'),
                'created' => $createdTickets[$date] ?? 0,
                'resolved' => $resolvedTickets[$date] ?? 0,
                'closed' => $closedTickets[$date] ?? 0,
            ]);
        }

        return $data;
    }

    /**
     * Get agent performance metrics.
     *
     * @return Collection<int, array>
     */
    public function getAgentPerformance(int $days = 30): Collection
    {
        $startDate = now()->subDays($days)->startOfDay();

        // Get agents (users with ITSS/admin/developer roles)
        $agents = User::whereHas('role', function ($q) {
            $q->whereIn('slug', ['itss', 'administrator', 'developer']);
        })->get(['id', 'name']);

        return $agents->map(function ($agent) use ($startDate) {
            // Tickets assigned
            $assigned = Ticket::where('assignee_id', $agent->id)
                ->where('created_at', '>=', $startDate)
                ->count();

            // Tickets resolved
            $resolved = Ticket::where('assignee_id', $agent->id)
                ->whereNotNull('resolved_at')
                ->where('resolved_at', '>=', $startDate)
                ->count();

            // Average resolution time (in hours) - PHP-based calculation
            $resolvedTickets = Ticket::where('assignee_id', $agent->id)
                ->whereNotNull('resolved_at')
                ->where('resolved_at', '>=', $startDate)
                ->get(['created_at', 'resolved_at']);

            $avgResolutionTime = $resolvedTickets->isNotEmpty()
                ? $resolvedTickets->avg(fn ($t) => Carbon::parse($t->created_at)->diffInMinutes(Carbon::parse($t->resolved_at)))
                : null;

            // First response time (average minutes) - PHP-based calculation
            $respondedTickets = Ticket::where('assignee_id', $agent->id)
                ->whereNotNull('responded_at')
                ->where('responded_at', '>=', $startDate)
                ->get(['created_at', 'responded_at']);

            $avgFrt = $respondedTickets->isNotEmpty()
                ? $respondedTickets->avg(fn ($t) => Carbon::parse($t->created_at)->diffInMinutes(Carbon::parse($t->responded_at)))
                : null;

            // Comments made
            $comments = TicketComment::where('user_id', $agent->id)
                ->where('created_at', '>=', $startDate)
                ->count();

            // SLA compliance (tickets resolved before SLA breach)
            $slaTotal = Ticket::where('assignee_id', $agent->id)
                ->whereNotNull('sla_due_at')
                ->whereNotNull('resolved_at')
                ->where('resolved_at', '>=', $startDate)
                ->count();

            $slaCompliant = Ticket::where('assignee_id', $agent->id)
                ->whereNotNull('sla_due_at')
                ->whereNotNull('resolved_at')
                ->where('resolved_at', '>=', $startDate)
                ->whereColumn('resolved_at', '<=', 'sla_due_at')
                ->count();

            // Time logged
            $timeLogged = TicketTimeEntry::where('user_id', $agent->id)
                ->where('work_date', '>=', $startDate->format('Y-m-d'))
                ->sum('duration_mins');

            // CSAT scores - PHP-based calculation for compatibility
            $csatResponses = DB::table('csat_responses')
                ->join('tickets', 'csat_responses.ticket_id', '=', 'tickets.id')
                ->where('tickets.assignee_id', $agent->id)
                ->whereNotNull('csat_responses.submitted_at')
                ->where('csat_responses.submitted_at', '>=', $startDate)
                ->get(['csat_responses.rating']);

            $csatTotal = $csatResponses->count();
            $csatGood = $csatResponses->where('rating', 'good')->count();
            $csatScore = $csatTotal > 0 ? round(($csatGood / $csatTotal) * 100, 1) : null;

            return [
                'id' => $agent->id,
                'name' => $agent->name,
                'assigned' => $assigned,
                'resolved' => $resolved,
                'resolution_rate' => $assigned > 0 ? round(($resolved / $assigned) * 100, 1) : 0,
                'avg_resolution_hours' => $avgResolutionTime ? round($avgResolutionTime / 60, 1) : null,
                'avg_frt_mins' => $avgFrt ? round($avgFrt, 0) : null,
                'comments' => $comments,
                'sla_compliance' => $slaTotal > 0 ? round(($slaCompliant / $slaTotal) * 100, 1) : null,
                'time_logged_hours' => round($timeLogged / 60, 1),
                'csat_score' => $csatScore,
                'csat_total' => $csatTotal,
            ];
        })->sortByDesc('resolved')->values();
    }

    /**
     * Get SLA compliance metrics.
     */
    public function getSlaCompliance(int $days = 30): array
    {
        $startDate = now()->subDays($days)->startOfDay();

        // Overall SLA stats
        $total = Ticket::whereNotNull('sla_due_at')
            ->where('created_at', '>=', $startDate)
            ->count();

        $breached = Ticket::whereNotNull('sla_due_at')
            ->where('created_at', '>=', $startDate)
            ->where(function ($q) {
                // Breached if: resolved after due OR still open and past due
                $q->where(function ($sub) {
                    $sub->whereNotNull('resolved_at')
                        ->whereColumn('resolved_at', '>', 'sla_due_at');
                })->orWhere(function ($sub) {
                    $sub->whereNull('resolved_at')
                        ->where('sla_due_at', '<', now());
                });
            })
            ->count();

        $compliant = $total - $breached;

        // By priority
        $byPriority = collect(Ticket::PRIORITIES)->map(function ($priority) use ($startDate) {
            $priorityTotal = Ticket::whereNotNull('sla_due_at')
                ->where('priority', $priority)
                ->where('created_at', '>=', $startDate)
                ->count();

            $priorityBreached = Ticket::whereNotNull('sla_due_at')
                ->where('priority', $priority)
                ->where('created_at', '>=', $startDate)
                ->where(function ($q) {
                    $q->where(function ($sub) {
                        $sub->whereNotNull('resolved_at')
                            ->whereColumn('resolved_at', '>', 'sla_due_at');
                    })->orWhere(function ($sub) {
                        $sub->whereNull('resolved_at')
                            ->where('sla_due_at', '<', now());
                    });
                })
                ->count();

            return [
                'priority' => $priority,
                'total' => $priorityTotal,
                'compliant' => $priorityTotal - $priorityBreached,
                'breached' => $priorityBreached,
                'compliance_rate' => $priorityTotal > 0
                    ? round((($priorityTotal - $priorityBreached) / $priorityTotal) * 100, 1)
                    : 100,
            ];
        });

        // Trend by week
        $weeklyTrend = collect();
        for ($i = 3; $i >= 0; $i--) {
            $weekStart = now()->subWeeks($i)->startOfWeek();
            $weekEnd = now()->subWeeks($i)->endOfWeek();

            $weekTotal = Ticket::whereNotNull('sla_due_at')
                ->whereBetween('created_at', [$weekStart, $weekEnd])
                ->count();

            $weekBreached = Ticket::whereNotNull('sla_due_at')
                ->whereBetween('created_at', [$weekStart, $weekEnd])
                ->where(function ($q) {
                    $q->where(function ($sub) {
                        $sub->whereNotNull('resolved_at')
                            ->whereColumn('resolved_at', '>', 'sla_due_at');
                    })->orWhere(function ($sub) {
                        $sub->whereNull('resolved_at')
                            ->where('sla_due_at', '<', now());
                    });
                })
                ->count();

            $weeklyTrend->push([
                'week' => $weekStart->format('M j'),
                'total' => $weekTotal,
                'compliant' => $weekTotal - $weekBreached,
                'breached' => $weekBreached,
                'compliance_rate' => $weekTotal > 0
                    ? round((($weekTotal - $weekBreached) / $weekTotal) * 100, 1)
                    : 100,
            ]);
        }

        return [
            'total' => $total,
            'compliant' => $compliant,
            'breached' => $breached,
            'compliance_rate' => $total > 0 ? round(($compliant / $total) * 100, 1) : 100,
            'by_priority' => $byPriority,
            'weekly_trend' => $weeklyTrend,
        ];
    }

    /**
     * Get summary statistics for the dashboard.
     */
    public function getSummaryStats(int $days = 30): array
    {
        $startDate = now()->subDays($days)->startOfDay();
        $prevStartDate = now()->subDays($days * 2)->startOfDay();
        $prevEndDate = $startDate;

        // Current period stats
        $created = Ticket::where('created_at', '>=', $startDate)->count();
        $resolved = Ticket::whereNotNull('resolved_at')
            ->where('resolved_at', '>=', $startDate)->count();
        $open = Ticket::whereIn('status', ['open', 'in_progress'])->count();

        // Previous period for comparison
        $prevCreated = Ticket::whereBetween('created_at', [$prevStartDate, $prevEndDate])->count();
        $prevResolved = Ticket::whereNotNull('resolved_at')
            ->whereBetween('resolved_at', [$prevStartDate, $prevEndDate])->count();

        // Average resolution time - PHP-based calculation
        $resolvedTickets = Ticket::whereNotNull('resolved_at')
            ->where('resolved_at', '>=', $startDate)
            ->get(['created_at', 'resolved_at']);

        $avgResolution = $resolvedTickets->isNotEmpty()
            ? $resolvedTickets->avg(fn ($t) => Carbon::parse($t->created_at)->diffInHours(Carbon::parse($t->resolved_at)))
            : null;

        // Average first response time - PHP-based calculation
        $respondedTickets = Ticket::whereNotNull('responded_at')
            ->where('responded_at', '>=', $startDate)
            ->get(['created_at', 'responded_at']);

        $avgFrt = $respondedTickets->isNotEmpty()
            ? $respondedTickets->avg(fn ($t) => Carbon::parse($t->created_at)->diffInMinutes(Carbon::parse($t->responded_at)))
            : null;

        // CSAT average - PHP-based calculation
        $csatResponses = DB::table('csat_responses')
            ->whereNotNull('submitted_at')
            ->where('submitted_at', '>=', $startDate)
            ->get(['rating']);

        $csatTotal = $csatResponses->count();
        $csatGood = $csatResponses->where('rating', 'good')->count();
        $csatScore = $csatTotal > 0 ? round(($csatGood / $csatTotal) * 100, 1) : null;

        // By status
        $byStatus = Ticket::where('created_at', '>=', $startDate)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // By type
        $byType = Ticket::where('created_at', '>=', $startDate)
            ->selectRaw('type, COUNT(*) as count')
            ->groupBy('type')
            ->pluck('count', 'type')
            ->toArray();

        // By priority
        $byPriority = Ticket::where('created_at', '>=', $startDate)
            ->selectRaw('priority, COUNT(*) as count')
            ->groupBy('priority')
            ->pluck('count', 'priority')
            ->toArray();

        return [
            'created' => $created,
            'created_change' => $prevCreated > 0
                ? round((($created - $prevCreated) / $prevCreated) * 100, 1)
                : 0,
            'resolved' => $resolved,
            'resolved_change' => $prevResolved > 0
                ? round((($resolved - $prevResolved) / $prevResolved) * 100, 1)
                : 0,
            'open' => $open,
            'avg_resolution_hours' => $avgResolution ? round($avgResolution, 1) : null,
            'avg_frt_mins' => $avgFrt ? round($avgFrt, 0) : null,
            'csat_score' => $csatScore,
            'by_status' => $byStatus,
            'by_type' => $byType,
            'by_priority' => $byPriority,
        ];
    }

    /**
     * Get top categories by ticket volume.
     */
    public function getTopCategories(int $days = 30, int $limit = 10): Collection
    {
        $startDate = now()->subDays($days)->startOfDay();

        return Ticket::with('category:id,name')
            ->where('created_at', '>=', $startDate)
            ->whereNotNull('category_id')
            ->selectRaw('category_id, COUNT(*) as count')
            ->groupBy('category_id')
            ->orderByDesc('count')
            ->limit($limit)
            ->get()
            ->map(fn ($row) => [
                'name' => $row->category?->name ?? 'Uncategorized',
                'count' => $row->count,
            ]);
    }
}
