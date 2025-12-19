<?php

namespace App\Livewire\ITSS;

use App\Events\TicketChanged;
use App\Models\Helpdesk\CsatResponse;
use App\Models\Helpdesk\SlaEscalation;
use App\Models\Helpdesk\Ticket;
use App\Models\Helpdesk\TicketAttachment;
use App\Models\Helpdesk\TicketCategory;
use App\Models\User;
use App\Services\Helpdesk\AssignmentResolver;
use App\Services\Helpdesk\SlaResolver;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Layout('layouts.enduser')]
class Helpdesk extends Component
{
    use WithFileUploads, WithPagination;

    // Filters
    public string $search = '';

    public string $status = '';

    public string $priority = '';

    public string $type = '';

    public ?int $category = null;

    // Filter: specific assignee
    public ?int $assignee = null;

    // Polling state for admin realtime fallback
    public int $lastNotifiedCount = 0;

    // Create ticket modal state
    public bool $showCreate = false;

    // CSAT comment modal
    public bool $showCsatModal = false;

    public ?array $csatModal = null; // ['ticket_no'=>..., 'rating'=>..., 'comment'=>..., 'submitted_at'=>...]

    // Filters: only my assigned tickets
    public bool $mine = false;

    // Filters: only unassigned tickets
    public bool $unassigned = false;

    // Filters: show only tickets within any escalation threshold window
    public bool $escalationsOnly = false;

    public string $subject = '';

    public string $description = '';

    public ?int $category_id = null;

    public string $priority_new = 'medium';

    // Verification uploads
    public ?string $verification_option = null; // id_card, cor

    public $id_front;

    public $id_back;

    public $cor_file;

    protected $queryString = [
        'search' => ['except' => ''],
        'status' => ['except' => ''],
        'priority' => ['except' => ''],
        'type' => ['except' => ''],
        'category' => ['except' => null],
        'assignee' => ['except' => null],
        'page' => ['except' => 1],
        'mine' => ['except' => false],
        'unassigned' => ['except' => false],
        'escalationsOnly' => ['except' => false],
    ];

    public function updating($name, $value)
    {
        if (in_array($name, ['search', 'status', 'priority', 'type', 'category', 'assignee', 'mine', 'unassigned', 'escalationsOnly'])) {
            $this->resetPage();
        }
        // Ensure mutually exclusive toggles for mine vs unassigned
        if ($name === 'mine' && (bool) $value === true) {
            $this->unassigned = false;
            $this->assignee = null; // override specific assignee
        }
        if ($name === 'unassigned' && (bool) $value === true) {
            $this->mine = false;
            $this->assignee = null; // override specific assignee
        }
        if ($name === 'assignee' && $value) {
            // When a specific assignee is chosen, clear mine/unassigned toggles
            $this->mine = false;
            $this->unassigned = false;
        }
    }

    public function updatedAssignee($value): void
    {
        // Normalize select values: '' => null, 'none' => unassigned
        if ($value === '' || $value === null) {
            $this->assignee = null;

            return;
        }
        if ($value === 'none') {
            $this->assignee = null;
            $this->unassigned = true;
            $this->mine = false;

            return;
        }
        // Cast to int if numeric
        if (is_numeric($value)) {
            $this->assignee = (int) $value;
        }
    }

    public function updated($name, $value): void
    {
        // Persist filters after any update
        $this->persistFilters();
    }

    public function showCsat(int $ticketId): void
    {
        $ticket = Ticket::query()->with('latestSubmittedCsat')->find($ticketId);
        if (! $ticket || ! $ticket->latestSubmittedCsat) {
            $this->showCsatModal = false;
            $this->csatModal = null;

            return;
        }
        $r = $ticket->latestSubmittedCsat;
        $this->csatModal = [
            'ticket_no' => $ticket->ticket_no,
            'rating' => (string) $r->rating,
            'comment' => (string) ($r->comment ?? ''),
            'submitted_at' => optional($r->submitted_at)->toDateTimeString(),
        ];
        $this->showCsatModal = true;
    }

    protected function persistFilters(): void
    {
        session(['itss.helpdesk.filters' => [
            'search' => $this->search,
            'status' => $this->status,
            'priority' => $this->priority,
            'type' => $this->type,
            'category' => $this->category,
            'assignee' => $this->assignee,
            'mine' => $this->mine,
            'unassigned' => $this->unassigned,
            'escalationsOnly' => $this->escalationsOnly,
        ]]);
    }

    public function mount(): void
    {
        // Initialize last known count for polling notifications
        $this->lastNotifiedCount = Ticket::query()->count();

        // Restore persisted filters if present
        $saved = session('itss.helpdesk.filters');
        if (is_array($saved)) {
            $this->search = (string) ($saved['search'] ?? $this->search);
            $this->status = (string) ($saved['status'] ?? $this->status);
            $this->priority = (string) ($saved['priority'] ?? $this->priority);
            $this->type = (string) ($saved['type'] ?? $this->type);
            $this->category = $saved['category'] ?? $this->category;
            $this->assignee = $saved['assignee'] ?? $this->assignee;
            $this->mine = (bool) ($saved['mine'] ?? $this->mine);
            $this->unassigned = (bool) ($saved['unassigned'] ?? $this->unassigned);
            $this->escalationsOnly = (bool) ($saved['escalationsOnly'] ?? $this->escalationsOnly);
        }
    }

    public function getStatsProperty(): array
    {
        $base = Ticket::query();

        return [
            'open' => (clone $base)->where('status', 'open')->count(),
            'in_progress' => (clone $base)->where('status', 'in_progress')->count(),
            'resolved' => (clone $base)->where('status', 'resolved')->count(),
            'total' => (clone $base)->count(),
        ];
    }

    protected function rules(): array
    {
        $base = [
            'subject' => 'required|string|min:3|max:255',
            'description' => 'required|string|min:5',
            'priority_new' => 'required|in:low,medium,high,critical',
            'category_id' => 'nullable|exists:ticket_categories,id',
        ];

        if ($this->requiresVerification()) {
            $base['verification_option'] = 'required|in:id_card,cor';
            if ($this->verification_option === 'id_card') {
                $base['id_front'] = 'required|image|max:4096';
                $base['id_back'] = 'required|image|max:4096';
            } elseif ($this->verification_option === 'cor') {
                $base['cor_file'] = 'required|image|max:6144';
            }
        }

        return $base;
    }

    public function requiresVerification(): bool
    {
        // if category is "Account Access", enforce verification
        if (! $this->category_id) {
            return false;
        }
        $cat = TicketCategory::find($this->category_id);

        return $cat && Str::lower($cat->name) === 'account access';
    }

    public function createTicket(): void
    {
        $this->validate();

        $user = Auth::user();
        $ticketNo = 'HD-'.now()->format('Y').'-'.str_pad((string) (Ticket::max('id') + 1), 5, '0', STR_PAD_LEFT);

        $verificationStatus = $this->requiresVerification() ? 'pending' : 'verified';
        $verificationMethod = null;

        // Default type: incident (admins can change later if needed)
        $type = 'incident';
        $policy = SlaResolver::pickPolicy($type, $this->priority_new);
        $dueAt = SlaResolver::computeDueAt($policy?->resolve_mins);

        $ticket = Ticket::create([
            'ticket_no' => $ticketNo,
            'subject' => $this->subject,
            'description' => $this->description,
            'type' => $type,
            'status' => 'open',
            'priority' => $this->priority_new,
            'category_id' => $this->category_id,
            'requester_id' => $user->id,
            'department' => $user->department ?? null,
            'verification_status' => $verificationStatus,
            'verification_method' => $verificationMethod,
            'sla_policy_id' => $policy?->id,
            'sla_due_at' => $dueAt,
        ]);

        // Auto-assign based on rules if unassigned
        if (! $ticket->assignee_id) {
            $assigneeId = AssignmentResolver::resolveAssigneeId($ticket);
            if ($assigneeId) {
                $ticket->assignee_id = $assigneeId;
                $ticket->save();
            }
        }

        // Handle uploads securely on private disk
        if ($this->requiresVerification()) {
            if ($this->verification_option === 'id_card') {
                $verificationMethod = 'id_card';
                $frontPath = $this->id_front->store("tickets/{$ticket->id}", 'private');
                $backPath = $this->id_back->store("tickets/{$ticket->id}", 'private');
                TicketAttachment::create([
                    'ticket_id' => $ticket->id,
                    'user_id' => $user->id,
                    'type' => 'id_front',
                    'disk' => 'private',
                    'path' => $frontPath,
                    'filename' => $this->id_front->getClientOriginalName(),
                    'mime' => $this->id_front->getMimeType(),
                    'size' => $this->id_front->getSize(),
                ]);
                TicketAttachment::create([
                    'ticket_id' => $ticket->id,
                    'user_id' => $user->id,
                    'type' => 'id_back',
                    'disk' => 'private',
                    'path' => $backPath,
                    'filename' => $this->id_back->getClientOriginalName(),
                    'mime' => $this->id_back->getMimeType(),
                    'size' => $this->id_back->getSize(),
                ]);
            } elseif ($this->verification_option === 'cor') {
                $verificationMethod = 'cor';
                $corPath = $this->cor_file->store("tickets/{$ticket->id}", 'private');
                TicketAttachment::create([
                    'ticket_id' => $ticket->id,
                    'user_id' => $user->id,
                    'type' => 'cor',
                    'disk' => 'private',
                    'path' => $corPath,
                    'filename' => $this->cor_file->getClientOriginalName(),
                    'mime' => $this->cor_file->getMimeType(),
                    'size' => $this->cor_file->getSize(),
                ]);
            }

            $ticket->verification_method = $verificationMethod;
            $ticket->save();
        }

        $this->reset(['showCreate', 'subject', 'description', 'category_id', 'priority_new', 'verification_option', 'id_front', 'id_back', 'cor_file']);
        $this->priority_new = 'medium';
        session()->flash('success', 'Ticket created successfully.');

        // Notify agents to refresh tickets table
        try {
            event(new TicketChanged($ticket->id, 'created'));
        } catch (\Throwable $e) { /* noop */
        }
    }

    // Optional explicit poll method (not used by the view anymore)
    public function refreshData(): void
    {
        // No-op: default polling triggers render() which handles notification & paging
    }

    public function getTicketsProperty()
    {
        return Ticket::query()
            ->when($this->search, fn ($q) => $q->where(function ($qq) {
                $qq->where('ticket_no', 'like', "%{$this->search}%")
                    ->orWhere('subject', 'like', "%{$this->search}%");
            }))
            ->when($this->type, fn ($q) => $q->where('type', $this->type))
            ->when($this->status, fn ($q) => $q->where('status', $this->status))
            ->when($this->priority, fn ($q) => $q->where('priority', $this->priority))
            ->when($this->category, fn ($q) => $q->where('category_id', $this->category))
            ->when($this->assignee, fn ($q) => $q->where('assignee_id', $this->assignee))
            ->when($this->mine, function ($q) {
                $uid = Auth::id();
                if ($uid) {
                    $q->where('assignee_id', $uid);
                }
            })
            ->when($this->unassigned, fn ($q) => $q->whereNull('assignee_id'))
            ->with([
                'requester:id,name',
                'assignee:id,name',
                'category:id,name',
                'latestSubmittedCsat' => function ($q) {
                    $q->select(
                        'csat_responses.id',
                        'csat_responses.ticket_id',
                        'csat_responses.rating',
                        'csat_responses.comment',
                        'csat_responses.submitted_at'
                    );
                },
            ])
            ->latest()
            ->paginate(10);
    }

    public function render()
    {
        // Persist current filters so they survive page navigation and refresh
        $this->persistFilters();

        // Detect new tickets on each poll (render) and notify
        $totalTickets = Ticket::query()->count();
        if ($totalTickets > $this->lastNotifiedCount) {
            $delta = $totalTickets - $this->lastNotifiedCount;
            $this->dispatch('tickets-new', total: $totalTickets, delta: $delta);
            // Jump to page 1 so newest appears
            try {
                $this->resetPage();
            } catch (\Throwable $e) { /* noop */
            }
        }
        $this->lastNotifiedCount = $totalTickets;

        // Preload escalation thresholds for current page tickets to avoid N+1
        $tickets = $this->tickets; // paginate collection
        $policyIds = collect($tickets->items())->pluck('sla_policy_id')->filter()->unique()->values();
        $escalationsByPolicy = [];
        if ($policyIds->isNotEmpty()) {
            $escalations = SlaEscalation::whereIn('sla_policy_id', $policyIds)
                ->where('is_active', true)
                ->orderBy('threshold_mins_before_breach')
                ->get(['sla_policy_id', 'threshold_mins_before_breach']);
            foreach ($escalations as $e) {
                $escalationsByPolicy[$e->sla_policy_id][] = (int) $e->threshold_mins_before_breach;
            }
        }

        // If escalationsOnly is set, filter tickets in-memory to those within any threshold
        if ($this->escalationsOnly) {
            $tickets->setCollection($tickets->getCollection()->filter(function ($t) use ($escalationsByPolicy) {
                if (! $t->sla_due_at || ! in_array($t->status, ['open', 'in_progress'])) {
                    return false;
                }
                $minsLeft = now()->diffInMinutes($t->sla_due_at, false);
                if ($minsLeft < 0) {
                    return false;
                }
                $thresholds = ($t->sla_policy_id && isset($escalationsByPolicy[$t->sla_policy_id])) ? $escalationsByPolicy[$t->sla_policy_id] : [];
                if (empty($thresholds)) {
                    return false;
                }

                return collect($thresholds)->first(fn ($th) => $minsLeft <= (int) $th) !== null;
            }));
        }

        return view('livewire.i-t-s-s.helpdesk', [
            'tickets' => $tickets,
            'categories' => TicketCategory::where('is_active', true)->orderBy('name')->get(['id', 'name']),
            'stats' => $this->stats,
            'escalationsByPolicy' => $escalationsByPolicy,
            'agents' => User::whereHas('role', function ($q) {
                $q->whereIn('slug', ['itss', 'administrator', 'developer']);
            })
                ->orderBy('name')->get(['id', 'name']),
            'csatStats' => $this->computeCsatStats(),
        ]);
    }

    protected function computeCsatStats(): array
    {
        // Last 60 days stats
        $since = now()->subDays(60);
        $q = CsatResponse::query()->whereNotNull('submitted_at')->where('submitted_at', '>=', $since);
        $count = (clone $q)->count();
        // Ratings standardized to good/neutral/poor; map to 5/3/1 for average
        $avg = (clone $q)
            ->selectRaw("AVG(CASE rating WHEN 'good' THEN 5 WHEN 'neutral' THEN 3 WHEN 'poor' THEN 1 ELSE NULL END) as avg_rating")
            ->value('avg_rating');

        // Response rate: CSAT submissions over tickets resolved in window
        $resolvedCount = Ticket::query()->whereNotNull('resolved_at')->where('resolved_at', '>=', $since)->count();
        $responseRate = $resolvedCount > 0 ? round(($count / $resolvedCount) * 100) : 0;

        return [
            'avg' => $avg ? round($avg, 2) : null,
            'count' => $count,
            'responseRate' => $responseRate,
            'window' => '60d',
            // Preparation for admin stats: distribution and per-category averages
            'distribution' => [
                'good' => (clone $q)->where('rating', 'good')->count(),
                'neutral' => (clone $q)->where('rating', 'neutral')->count(),
                'poor' => (clone $q)->where('rating', 'poor')->count(),
            ],
            'perCategory' => CsatResponse::query()
                ->join('tickets', 'tickets.id', '=', 'csat_responses.ticket_id')
                ->join('ticket_categories', 'ticket_categories.id', '=', 'tickets.category_id')
                ->whereNotNull('csat_responses.submitted_at')
                ->where('csat_responses.submitted_at', '>=', $since)
                ->groupBy('ticket_categories.name')
                ->selectRaw("ticket_categories.name as category, AVG(CASE WHEN csat_responses.rating IN ('good','neutral','poor') THEN CASE csat_responses.rating WHEN 'good' THEN 5 WHEN 'neutral' THEN 3 WHEN 'poor' THEN 1 END ELSE CAST(csat_responses.rating as INTEGER) END) as avg_rating, COUNT(*) as responses")
                ->orderBy('avg_rating', 'desc')
                ->get()
                ->toArray(),
        ];
    }
}
