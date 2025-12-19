<?php

namespace App\Livewire\ControlPanel\Reports;

use App\Models\Helpdesk\CsatResponse;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class SurveyReport extends Component
{
    public ?string $startDate = null; // Y-m-d

    public ?string $endDate = null; // Y-m-d

    public string $department = ''; // department slug or '' for all

    /**
     * Source filter: 'all' (default), 'guest', 'end_user'
     */
    public string $source = 'all';

    /**
     * Distinct departments that have tickets with CSAT.
     *
     * @return array<int, array{slug:string,label:string}>
     */
    public function getDepartmentsProperty(): array
    {
        // Guest: departments from ticket.department
        $guest = DB::table('csat_responses')
            ->join('tickets', 'tickets.id', '=', 'csat_responses.ticket_id')
            ->leftJoin('departments', 'departments.slug', '=', 'tickets.department')
            ->whereNotNull('csat_responses.submitted_at')
            ->selectRaw("COALESCE(departments.slug, tickets.department) as slug, COALESCE(departments.name, tickets.department, 'Unassigned') as label");

        // End-user: departments from users.department
        $endUser = DB::table('system_csat_responses')
            ->join('users', 'users.id', '=', 'system_csat_responses.user_id')
            ->leftJoin('departments', 'departments.slug', '=', 'users.department')
            ->whereNotNull('system_csat_responses.submitted_at')
            ->selectRaw("COALESCE(departments.slug, users.department) as slug, COALESCE(departments.name, users.department, 'Unassigned') as label");

        $union = $guest->union($endUser);
        $rows = DB::query()->fromSub($union, 'u')->select('slug', 'label')->distinct()->orderBy('label')->get();

        return $rows->map(fn ($r) => ['slug' => (string) ($r->slug ?? ''), 'label' => (string) ($r->label ?? 'Unassigned')])->all();
    }

    public function getStatsProperty(): array
    {
        $start = $this->startDate ? Carbon::parse($this->startDate)->startOfDay() : now()->subDays(60)->startOfDay();
        $end = $this->endDate ? Carbon::parse($this->endDate)->endOfDay() : now()->endOfDay();
        // Guest base
        $guest = DB::table('csat_responses')
            ->whereNotNull('submitted_at')
            ->whereBetween('submitted_at', [$start, $end]);
        if ($this->department !== '') {
            $guest->join('tickets', 'tickets.id', '=', 'csat_responses.ticket_id')
                ->where('tickets.department', $this->department);
        }

        // End-user base
        $endUser = DB::table('system_csat_responses')
            ->whereNotNull('submitted_at')
            ->whereBetween('submitted_at', [$start, $end]);
        if ($this->department !== '') {
            $endUser->join('users', 'users.id', '=', 'system_csat_responses.user_id')
                ->where('users.department', $this->department);
        }

        // Counts
        $countGuest = (clone $guest)->count();
        $countEnd = (clone $endUser)->count();

        // Sums for weighted average
        $sumGuest = (clone $guest)
            ->selectRaw("SUM(CASE rating WHEN 'good' THEN 5 WHEN 'neutral' THEN 3 WHEN 'poor' THEN 1 ELSE 0 END) as s")
            ->value('s') ?? 0;
        $sumEnd = (clone $endUser)
            ->selectRaw('SUM(rating) as s')
            ->value('s') ?? 0;

        $countAll = $countGuest + $countEnd;
        $avgAll = $countAll > 0 ? round(($sumGuest + $sumEnd) / $countAll, 2) : null;

        // Distribution buckets
        $distGuest = [
            'good' => (clone $guest)->where('rating', 'good')->count(),
            'neutral' => (clone $guest)->where('rating', 'neutral')->count(),
            'poor' => (clone $guest)->where('rating', 'poor')->count(),
        ];
        $distEnd = [
            'good' => (clone $endUser)->where('rating', '>=', 4)->count(),
            'neutral' => (clone $endUser)->where('rating', 3)->count(),
            'poor' => (clone $endUser)->where('rating', '<=', 2)->count(),
        ];

        $distributionAll = [
            'good' => $distGuest['good'] + $distEnd['good'],
            'neutral' => $distGuest['neutral'] + $distEnd['neutral'],
            'poor' => $distGuest['poor'] + $distEnd['poor'],
        ];

        // PerCategory and PerAgent are ticket-specific; only compute for guest
        $perCategoryGuest = DB::table('csat_responses')
            ->join('tickets', 'tickets.id', '=', 'csat_responses.ticket_id')
            ->leftJoin('ticket_categories', 'ticket_categories.id', '=', 'tickets.category_id')
            ->whereNotNull('csat_responses.submitted_at')
            ->whereBetween('csat_responses.submitted_at', [$start, $end])
            ->when($this->department !== '', fn ($qq) => $qq->where('tickets.department', $this->department))
            ->groupBy('ticket_categories.name')
            ->selectRaw("COALESCE(ticket_categories.name, 'Uncategorized') as category, COUNT(*) as responses, AVG(CASE csat_responses.rating WHEN 'good' THEN 5 WHEN 'neutral' THEN 3 WHEN 'poor' THEN 1 ELSE NULL END) as avg_rating")
            ->orderBy('avg_rating', 'desc')
            ->get();

        $perAgentGuest = DB::table('csat_responses')
            ->join('tickets', 'tickets.id', '=', 'csat_responses.ticket_id')
            ->leftJoin('users', 'users.id', '=', 'tickets.assignee_id')
            ->whereNotNull('csat_responses.submitted_at')
            ->whereBetween('csat_responses.submitted_at', [$start, $end])
            ->when($this->department !== '', fn ($qq) => $qq->where('tickets.department', $this->department))
            ->groupBy('users.name')
            ->selectRaw("COALESCE(users.name, 'Unassigned') as agent, COUNT(*) as responses, AVG(CASE csat_responses.rating WHEN 'good' THEN 5 WHEN 'neutral' THEN 3 WHEN 'poor' THEN 1 ELSE NULL END) as avg_rating")
            ->orderBy('avg_rating', 'desc')
            ->get();

        // By Department: union guest + end user
        $byDeptGuest = DB::table('csat_responses')
            ->join('tickets', 'tickets.id', '=', 'csat_responses.ticket_id')
            ->leftJoin('departments', 'departments.slug', '=', 'tickets.department')
            ->whereNotNull('csat_responses.submitted_at')
            ->whereBetween('csat_responses.submitted_at', [$start, $end])
            ->when($this->department !== '', fn ($qq) => $qq->where('tickets.department', $this->department))
            ->groupBy('departments.slug', 'tickets.department')
            ->selectRaw("COALESCE(departments.slug, tickets.department, 'unassigned') as label, COUNT(*) as responses, AVG(CASE csat_responses.rating WHEN 'good' THEN 5 WHEN 'neutral' THEN 3 WHEN 'poor' THEN 1 ELSE NULL END) as avg_rating");

        $byDeptEnd = DB::table('system_csat_responses')
            ->join('users', 'users.id', '=', 'system_csat_responses.user_id')
            ->leftJoin('departments', 'departments.slug', '=', 'users.department')
            ->whereNotNull('system_csat_responses.submitted_at')
            ->whereBetween('system_csat_responses.submitted_at', [$start, $end])
            ->when($this->department !== '', fn ($qq) => $qq->where('users.department', $this->department))
            ->groupBy('departments.slug', 'users.department')
            ->selectRaw("COALESCE(departments.slug, users.department, 'unassigned') as label, COUNT(*) as responses, AVG(system_csat_responses.rating) as avg_rating");

        // Apply source filter
        $useGuest = $this->source === 'guest' || $this->source === 'all';
        $useEnd = $this->source === 'end_user' || $this->source === 'all';

        $count = ($useGuest ? $countGuest : 0) + ($useEnd ? $countEnd : 0);
        $avg = ($this->source === 'guest') ? ($countGuest > 0 ? round($sumGuest / $countGuest, 2) : null)
            : (($this->source === 'end_user') ? ($countEnd > 0 ? round($sumEnd / $countEnd, 2) : null) : $avgAll);

        $distribution = ($this->source === 'guest') ? $distGuest : (($this->source === 'end_user') ? $distEnd : $distributionAll);

    $perCategory = ($this->source === 'end_user') ? [] : $perCategoryGuest;
    $perAgent = ($this->source === 'end_user') ? [] : $perAgentGuest;

        $byDepartmentQuery = null;
        if ($useGuest && $useEnd) {
            $union = $byDeptGuest->union($byDeptEnd);
            $byDepartmentQuery = DB::query()->fromSub($union, 'x')
                ->select('label', DB::raw('SUM(responses) as responses'), DB::raw('AVG(avg_rating) as avg_rating'))
                ->groupBy('label')
                ->orderBy('responses', 'desc')
                ->limit(12);
        } elseif ($useGuest) {
            $byDepartmentQuery = $byDeptGuest->orderBy('responses', 'desc')->limit(12);
        } else {
            $byDepartmentQuery = $byDeptEnd->orderBy('responses', 'desc')->limit(12);
        }

        $byDepartment = $byDepartmentQuery->get();

        return [
            'range' => [$start->toDateString(), $end->toDateString()],
            'count' => $count,
            'avg' => $avg,
            'distribution' => $distribution,
            'perCategory' => $perCategory,
            'perAgent' => $perAgent,
            'byDepartment' => $byDepartment,
        ];
    }

    /**
     * Build a daily series for the chart from actual data.
     *
     * @return array<int, array{date:string, avg:float|null}>
     */
    public function getSeriesProperty(): array
    {
        $start = $this->startDate ? Carbon::parse($this->startDate)->startOfDay() : now()->subDays(60)->startOfDay();
        $end = $this->endDate ? Carbon::parse($this->endDate)->endOfDay() : now()->endOfDay();

        $driver = DB::getDriverName();
        $guestDateExpr = match ($driver) {
            'sqlsrv' => 'CONVERT(date, csat_responses.submitted_at)',
            'pgsql' => 'DATE(csat_responses.submitted_at)',
            'mysql' => 'DATE(csat_responses.submitted_at)',
            default => 'DATE(csat_responses.submitted_at)',
        };
        $endDateExpr = match ($driver) {
            'sqlsrv' => 'CONVERT(date, system_csat_responses.submitted_at)',
            'pgsql' => 'DATE(system_csat_responses.submitted_at)',
            'mysql' => 'DATE(system_csat_responses.submitted_at)',
            default => 'DATE(system_csat_responses.submitted_at)',
        };

        $useGuest = $this->source === 'guest' || $this->source === 'all';
        $useEnd = $this->source === 'end_user' || $this->source === 'all';

        $guestDaily = collect();
        if ($useGuest) {
            $guestDaily = DB::table('csat_responses')
                ->when($this->department !== '', function ($q) {
                    $q->join('tickets', 'tickets.id', '=', 'csat_responses.ticket_id')
                        ->where('tickets.department', $this->department);
                })
                ->whereNotNull('csat_responses.submitted_at')
                ->whereBetween('csat_responses.submitted_at', [$start, $end])
                ->selectRaw("$guestDateExpr as d, COUNT(*) as c, SUM(CASE rating WHEN 'good' THEN 5 WHEN 'neutral' THEN 3 WHEN 'poor' THEN 1 ELSE 0 END) as s")
                ->groupByRaw($guestDateExpr)
                ->orderByRaw($guestDateExpr)
                ->get()
                ->keyBy('d');
        }

        $endDaily = collect();
        if ($useEnd) {
            $endDaily = DB::table('system_csat_responses')
                ->when($this->department !== '', function ($q) {
                    $q->join('users', 'users.id', '=', 'system_csat_responses.user_id')
                        ->where('users.department', $this->department);
                })
                ->whereNotNull('system_csat_responses.submitted_at')
                ->whereBetween('system_csat_responses.submitted_at', [$start, $end])
                ->selectRaw("$endDateExpr as d, COUNT(*) as c, SUM(rating) as s")
                ->groupByRaw($endDateExpr)
                ->orderByRaw($endDateExpr)
                ->get()
                ->keyBy('d');
        }

        $out = [];
        $cursor = $start->copy();
        while ($cursor->lte($end)) {
            $date = $cursor->toDateString();
            $g = $guestDaily->get($date);
            $e = $endDaily->get($date);
            $sum = (int) (($g->s ?? 0) + ($e->s ?? 0));
            $cnt = (int) (($g->c ?? 0) + ($e->c ?? 0));
            $avg = $cnt > 0 ? round($sum / $cnt, 2) : null;
            $out[] = ['date' => $date, 'avg' => $avg];
            $cursor->addDay();
        }

        return $out;
    }

    public function render(): View
    {
        return view('livewire.control-panel.reports.survey-report', [
            'stats' => $this->stats,
            'departments' => $this->departments,
            'series' => $this->series,
            'exportCsvUrl' => route('controlPanel.reports.surveys.export', [
                'start_date' => $this->startDate,
                'end_date' => $this->endDate,
                'department' => $this->department,
                'source' => $this->source,
            ]),
            'source' => $this->source,
        ]);
    }
}
