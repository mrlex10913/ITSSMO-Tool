<?php

namespace App\Livewire\ITSS\SLA;

use App\Models\Helpdesk\Ticket;
use App\Models\Helpdesk\TicketCategory;
use App\Models\User;
use App\Services\Helpdesk\SlaInsights as Service;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts.enduser')]
class Insights extends Component
{
    #[Url(as: 'w')]
    public int $window = 30;

    public array $data = [];

    // Filters
    #[Url(as: 'assignee')]
    public ?int $assigneeId = null;

    #[Url(as: 'requester')]
    public ?int $requesterId = null;

    #[Url(as: 'category')]
    public ?int $categoryId = null;

    #[Url(as: 'priority')]
    public string $priority = '';

    #[Url(as: 'type')]
    public string $type = '';

    #[Url(as: 'agg')]
    public string $aggregate = 'daily'; // daily|weekly

    public function mount(): void
    {
        $this->refreshData();
    }

    public function updatedWindow(): void
    {
        if ($this->window < 7) {
            $this->window = 7;
        }
        if ($this->window > 180) {
            $this->window = 180;
        }
        $this->refreshData();
    }

    public function refreshData(): void
    {
        $filters = [
            'assignee_id' => $this->assigneeId,
            'requester_id' => $this->requesterId,
            'category_id' => $this->categoryId,
            'priority' => $this->priority ?: null,
            'type' => $this->type ?: null,
        ];
        $this->data = Service::compute($this->window, $filters, $this->aggregate);
    }

    public function render()
    {
        $categories = TicketCategory::query()->where('is_active', true)->orderBy('name')->get(['id', 'name']);
        $agents = User::whereHas('role', function ($q) {
            $q->whereIn('slug', ['itss', 'administrator', 'developer']);
        })
            ->orderBy('name')->get(['id', 'name']);
        $since = now()->subDays($this->window);
        $requesterIds = Ticket::query()
            ->where(function ($q) use ($since) {
                $q->where('created_at', '>=', $since)->orWhere('resolved_at', '>=', $since);
            })
            ->pluck('requester_id')->filter()->unique()->values();
        $requesters = $requesterIds->isNotEmpty()
            ? User::whereIn('id', $requesterIds)->orderBy('name')->get(['id', 'name'])
            : collect();

        return view('livewire.i-t-s-s.s-l-a.insights', [
            'data' => $this->data,
            'window' => $this->window,
            'agents' => $agents,
            'categories' => $categories,
            'assigneeId' => $this->assigneeId,
            'requesterId' => $this->requesterId,
            'categoryId' => $this->categoryId,
            'priority' => $this->priority,
            'type' => $this->type,
            'aggregate' => $this->aggregate,
            'requesters' => $requesters,
        ]);
    }

    public function exportCsv()
    {
        $data = $this->data ?: Service::compute($this->window, [
            'assignee_id' => $this->assigneeId,
            'requester_id' => $this->requesterId,
            'category_id' => $this->categoryId,
            'priority' => $this->priority ?: null,
            'type' => $this->type ?: null,
        ]);

        $filename = 'sla-insights-'.now()->format('Ymd-His').'.csv';
        $callback = function () use ($data) {
            $out = fopen('php://output', 'w');
            // Overall metrics
            fputcsv($out, ['Window', $data['window']]);
            fputcsv($out, ['Avg First Response (mins)', $data['response_avg_mins']]);
            fputcsv($out, ['Avg Resolve (mins)', $data['resolve_avg_mins']]);
            fputcsv($out, ['Responded', $data['responded_count']]);
            fputcsv($out, ['Resolved', $data['resolved_count']]);
            fputcsv($out, ['Due Today', $data['due_today']]);
            fputcsv($out, ['Breached Open', $data['breached_open']]);
            fputcsv($out, ['Breach Rate %', $data['breach_rate']]);
            fputcsv($out, []);
            // Category header
            fputcsv($out, ['Category', 'On-Time', 'Breached', 'On-Time %', 'Avg Response (mins)', 'Avg Resolve (mins)']);
            foreach ($data['categories'] as $row) {
                fputcsv($out, [
                    $row['name'],
                    $row['on_time'],
                    $row['breached'],
                    $row['on_time_rate'],
                    $row['response_avg_mins'],
                    $row['resolve_avg_mins'],
                ]);
            }
            fclose($out);
        };

        return response()->streamDownload($callback, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    public function exportDetailedCsv()
    {
        $since = now()->subDays($this->window);
        $q = Ticket::query()
            ->with(['category:id,name', 'requester:id,name', 'assignee:id,name'])
            ->where(function ($q) use ($since) {
                $q->where('created_at', '>=', $since)->orWhere('resolved_at', '>=', $since);
            });
        if ($this->assigneeId) {
            $q->where('assignee_id', $this->assigneeId);
        }
        if ($this->requesterId) {
            $q->where('requester_id', $this->requesterId);
        }
        if ($this->categoryId) {
            $q->where('category_id', $this->categoryId);
        }
        if ($this->priority) {
            $q->where('priority', $this->priority);
        }
        if ($this->type) {
            $q->where('type', $this->type);
        }

        $tickets = $q->orderBy('created_at')->get();

        $filename = 'sla-detailed-'.now()->format('Ymd-His').'.csv';
        $callback = function () use ($tickets) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['ID', 'Ticket No', 'Subject', 'Type', 'Priority', 'Category', 'Requester', 'Assignee', 'Created At', 'Responded At', 'Resp (mins)', 'Resolved At', 'Resolve (mins)', 'SLA Due', 'Breached', 'Status']);
            foreach ($tickets as $t) {
                $respMins = ($t->responded_at && $t->created_at) ? max(0, \Illuminate\Support\Carbon::parse($t->created_at)->diffInMinutes(\Illuminate\Support\Carbon::parse($t->responded_at))) : null;
                $resMins = ($t->resolved_at && $t->created_at) ? max(0, \Illuminate\Support\Carbon::parse($t->created_at)->diffInMinutes(\Illuminate\Support\Carbon::parse($t->resolved_at))) : null;
                $breached = ($t->resolved_at && $t->sla_due_at) ? (\Illuminate\Support\Carbon::parse($t->resolved_at)->gt(\Illuminate\Support\Carbon::parse($t->sla_due_at)) ? 'Yes' : 'No') : '';
                fputcsv($out, [
                    $t->id,
                    $t->ticket_no ?? '',
                    $t->subject ?? '',
                    $t->type ?? '',
                    $t->priority ?? '',
                    optional($t->category)->name,
                    optional($t->requester)->name,
                    optional($t->assignee)->name,
                    optional($t->created_at)?->toDateTimeString(),
                    optional($t->responded_at)?->toDateTimeString(),
                    $respMins,
                    optional($t->resolved_at)?->toDateTimeString(),
                    $resMins,
                    optional($t->sla_due_at)?->toDateTimeString(),
                    $breached,
                    $t->status ?? '',
                ]);
            }
            fclose($out);
        };

        return response()->streamDownload($callback, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }
}
