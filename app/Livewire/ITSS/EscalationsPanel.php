<?php

namespace App\Livewire\ITSS;

use App\Models\Helpdesk\SlaEscalation;
use App\Models\Helpdesk\Ticket;
use App\Models\Helpdesk\TicketEscalationLog;
use App\Models\User;
use App\Services\Helpdesk\Notifier;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.enduser')]
class EscalationsPanel extends Component
{
    use WithPagination;

    public ?int $assignee = null;

    public ?string $priority = null;

    public ?string $type = null;

    public bool $breachedOnly = false;

    protected $queryString = [
        'assignee' => ['except' => null],
        'priority' => ['except' => null],
        'type' => ['except' => null],
        'breachedOnly' => ['except' => false, 'as' => 'breached'],
        'page' => ['except' => 1],
    ];

    public function updating($name, $value): void
    {
        if (in_array($name, ['assignee', 'priority', 'type', 'breachedOnly'])) {
            $this->resetPage();
        }
    }

    public function acknowledge(int $ticketId): void
    {
        $t = Ticket::find($ticketId);
        if (! $t) {
            return;
        }
        $t->acknowledged_at = now();
        $t->save();
        session()->flash('success', 'Ticket acknowledged.');
    }

    public function snooze(int $ticketId, int $minutes = 30): void
    {
        $t = Ticket::find($ticketId);
        if (! $t || ! $t->sla_due_at) {
            return;
        }
        $t->sla_due_at = $t->sla_due_at->copy()->addMinutes($minutes);
        $t->save();
        session()->flash('success', 'Ticket snoozed by '.$minutes.' minutes.');
    }

    public function reassign(int $ticketId, int $userId): void
    {
        $t = Ticket::find($ticketId);
        if (! $t) {
            return;
        }
        $before = $t->assignee_id;
        $t->assignee_id = $userId;
        $t->save();
        try {
            Notifier::sendUpdateEmails($t, ['assignee_id' => ['from' => $before, 'to' => $userId]], Auth::id());
        } catch (\Throwable $e) { /* noop */
        }
        session()->flash('success', 'Ticket reassigned.');
    }

    public function getAgentsProperty()
    {
        return User::orderBy('name')->get(['id', 'name']);
    }

    public function getTicketsProperty()
    {
        $q = Ticket::query()
            ->with(['assignee:id,name'])
            ->whereIn('status', ['open', 'in_progress'])
            ->whereNotNull('sla_due_at');

        if ($this->assignee) {
            $q->where('assignee_id', $this->assignee);
        }
        if ($this->priority) {
            $q->where('priority', $this->priority);
        }
        if ($this->type) {
            $q->where('type', $this->type);
        }

        if ($this->breachedOnly) {
            $q->where('sla_due_at', '<', now());
        }

        $tickets = $q->latest('sla_due_at')->paginate(10);

        // Compute escalation state for current page without mutating paginator collection
        $items = collect($tickets->items());
        $policyIds = $items->pluck('sla_policy_id')->filter()->unique()->values();
        $thresholdsByPolicy = [];
        if ($policyIds->isNotEmpty()) {
            $escalations = SlaEscalation::whereIn('sla_policy_id', $policyIds)->where('is_active', true)->get();
            foreach ($escalations as $e) {
                $thresholdsByPolicy[$e->sla_policy_id][] = (int) $e->threshold_mins_before_breach;
            }
        }

        return $tickets;
    }

    public function getEscalatingIdsProperty(): array
    {
        $tickets = $this->tickets;
        if (! $tickets->count()) {
            return [];
        }
        $items = collect($tickets->items());
        $ids = $items->pluck('id')->all();
        $policyIds = $items->pluck('sla_policy_id')->filter()->unique()->values();
        $thresholdsByPolicy = [];
        if ($policyIds->isNotEmpty()) {
            $escalations = SlaEscalation::whereIn('sla_policy_id', $policyIds)->where('is_active', true)->get();
            foreach ($escalations as $e) {
                $thresholdsByPolicy[$e->sla_policy_id][] = (int) $e->threshold_mins_before_breach;
            }
        }
        $logged = TicketEscalationLog::whereIn('ticket_id', $ids)->pluck('ticket_id')->unique()->all();
        $now = now();
        $escalating = [];
        foreach ($items as $t) {
            if (! $t->sla_due_at) {
                continue;
            }
            if (in_array($t->id, $logged, true)) {
                $escalating[] = $t->id;

                continue;
            }
            $minsLeft = $now->diffInMinutes($t->sla_due_at, false);
            $thresholds = $thresholdsByPolicy[$t->sla_policy_id] ?? [];
            foreach ($thresholds as $thr) {
                if ($minsLeft <= $thr) {
                    $escalating[] = $t->id;
                    break;
                }
            }
        }

        return array_values(array_unique($escalating));
    }

    public function render()
    {
        return view('livewire.i-t-s-s.escalations-panel', [
            'tickets' => $this->tickets,
            'agents' => $this->agents,
        ]);
    }
}
