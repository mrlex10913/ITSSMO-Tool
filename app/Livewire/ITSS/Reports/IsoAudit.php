<?php

namespace App\Livewire\ITSS\Reports;

use App\Models\Helpdesk\Ticket;
use App\Models\Helpdesk\TicketCategory;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.enduser')]
class IsoAudit extends Component
{
    public string $from; // Y-m-d

    public string $to;   // Y-m-d

    public ?int $assigneeId = null;

    public ?int $requesterId = null;

    public ?int $categoryId = null;

    public string $priority = '';

    public string $type = '';

    public function mount(): void
    {
        $this->to = now()->toDateString();
        $this->from = now()->subDays(30)->toDateString();
    }

    public function exportCsv()
    {
        $from = \Illuminate\Support\Carbon::parse($this->from)->startOfDay();
        $to = \Illuminate\Support\Carbon::parse($this->to)->endOfDay();

        $q = Ticket::query()
            ->with(['category:id,name', 'requester:id,name,email', 'assignee:id,name,email'])
            ->whereBetween('created_at', [$from, $to]);
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

        $rows = $q->orderBy('created_at')->get();

        $filename = 'iso-audit-'.now()->format('Ymd-His').'.csv';
        $callback = function () use ($rows) {
            $out = fopen('php://output', 'w');
            // ISO audit focus: traceability, timeliness, ownership, closure, verification
            fputcsv($out, [
                'Ticket ID', 'Ticket No', 'Subject', 'Type', 'Priority', 'Category',
                'Requester Name', 'Requester Email', 'Assignee Name', 'Assignee Email',
                'Created At', 'Acknowledged At', 'Responded At', 'Resolved At', 'Closed At',
                'SLA Due At', 'Breached?', 'First Response (mins)', 'Resolve (mins)', 'Status',
            ]);
            foreach ($rows as $t) {
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
                    optional($t->requester)->email,
                    optional($t->assignee)->name,
                    optional($t->assignee)->email,
                    optional($t->created_at)?->toDateTimeString(),
                    optional($t->acknowledged_at)?->toDateTimeString(),
                    optional($t->responded_at)?->toDateTimeString(),
                    optional($t->resolved_at)?->toDateTimeString(),
                    optional($t->closed_at)?->toDateTimeString(),
                    optional($t->sla_due_at)?->toDateTimeString(),
                    $breached,
                    $respMins,
                    $resMins,
                    $t->status ?? '',
                ]);
            }
            fclose($out);
        };

        return response()->streamDownload($callback, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    public function render()
    {
        $categories = TicketCategory::where('is_active', true)->orderBy('name')->get(['id', 'name']);
        $agents = User::whereHas('role', function ($q) {
            $q->whereIn('slug', ['itss', 'administrator', 'developer']);
        })->orderBy('name')->get(['id', 'name']);
        $requesters = User::orderBy('name')->get(['id', 'name']);

        return view('livewire.i-t-s-s.reports.iso-audit', [
            'categories' => $categories,
            'agents' => $agents,
            'requesters' => $requesters,
        ]);
    }
}
