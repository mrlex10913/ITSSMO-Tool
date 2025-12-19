<?php

namespace App\Livewire\Notifications;

use App\Models\Helpdesk\TicketActivityLog;
use App\Models\MasterFiles\MasterFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Lazy;
use Livewire\Component;

#[Lazy]
class BellDropdown extends Component
{
    /** @var array<int, array{message:string, ticket_no:string|null, actor:string|null, type:string, when:string, created_at: \Illuminate\Support\Carbon|null}> */
    public array $items = [];

    public int $count = 0;
    public int $unread = 0;

    public function mount(): void
    {
        $this->loadItems();
    }

    public function refresh(): void
    {
        $this->loadItems();
    }

    protected function loadItems(): void
    {
        $user = Auth::user();
        if (! $user) {
            $this->items = [];
            $this->count = 0;
            $this->unread = 0;

            return;
        }

        // Determine role
        $query = TicketActivityLog::query()
            ->with(['ticket:id,ticket_no,requester_id,assignee_id,requester_name', 'ticket.requester:id,name', 'user:id,name'])
            ->latest();

        // Admin/ITSS/Developer get global feed; others see their own tickets (requester/assignee)
        $roleSlug = '';
        if (property_exists($user, 'role') && $user->role) {
            $roleSlug = strtolower((string) optional($user->role)->slug);
        } elseif (property_exists($user, 'role_id') && $user->role_id) {
            $roleModel = \App\Models\Roles::find($user->role_id);
            $roleSlug = $roleModel ? strtolower((string) $roleModel->slug) : '';
        }
        $canGlobal = in_array($roleSlug, ['itss', 'administrator', 'developer'], true);
        if (! $canGlobal) {
            $query->whereHas('ticket', function ($t) use ($user) {
                $t->where('requester_id', $user->id)->orWhere('assignee_id', $user->id);
            });
        }

        // Only include "new tickets created"
    $ticketCreates = (clone $query)
            ->where('action', 'created')
            ->limit(10)
            ->get()
            ->map(function (TicketActivityLog $log): array {
                $ticketNo = optional($log->ticket)->ticket_no;
                $actor = optional($log->user)->name
                    ?: (optional($log->ticket)->requester->name ?? (optional($log->ticket)->requester_name ?: 'Guest'));
                return [
                    'type' => 'ticket',
                    'message' => sprintf('Ticket #%s created by %s', (string) $ticketNo, (string) $actor),
                    'actor' => (string) $actor,
                    'ticket_no' => $ticketNo,
                    'when' => optional($log->created_at)?->diffForHumans() ?? '',
                    'created_at' => $log->created_at?->copy(),
                ];
            });

        // Recent asset uploads (Master Files). Limit to admin/itss/developer for now
        $assets = collect();
        if ($canGlobal) {
            $assets = MasterFile::query()
                ->with('uploader:id,name')
                ->latest()
                ->limit(10)
                ->get()
                ->map(function (MasterFile $f): array {
                    $actor = optional($f->uploader)->name ?: 'Unknown';
                    return [
                        'type' => 'asset',
                        'message' => sprintf('Asset uploaded: %s by %s', (string) $f->title, (string) $actor),
                        'actor' => (string) $actor,
                        'ticket_no' => null,
                        'when' => optional($f->created_at)?->diffForHumans() ?? '',
                        'created_at' => $f->created_at?->copy(),
                    ];
                });
        }

        // Merge, sort by created_at desc, take 10
        $merged = $ticketCreates->merge($assets)
            ->sortByDesc(fn ($row) => $row['created_at'] ?? now())
            ->values()
            ->take(10);

        $this->items = $merged->map(function (array $row): array {
            // Normalize when text based on created_at to keep it fresh
            $row['when'] = ($row['created_at'] instanceof Carbon) ? $row['created_at']->diffForHumans() : (string) ($row['when'] ?? '');
            return $row;
        })->all();

        // Badge counts
        $this->count = count($this->items);
        $lastSeen = Cache::get($this->lastSeenKey($user->id));
        $lastSeenAt = $lastSeen instanceof Carbon ? $lastSeen : (is_string($lastSeen) ? Carbon::parse($lastSeen) : null);
        if (! $lastSeenAt) {
            // If never seen, treat all as unread
            $this->unread = $this->count;
        } else {
            $this->unread = collect($this->items)->filter(function ($row) use ($lastSeenAt) {
                return isset($row['created_at']) && $row['created_at'] instanceof Carbon && $row['created_at']->gt($lastSeenAt);
            })->count();
        }
    }

    public function markSeen(): void
    {
        $user = Auth::user();
        if ($user) {
            Cache::put($this->lastSeenKey($user->id), now(), now()->addDays(30));
        }
        $this->refresh();
    }

    protected function lastSeenKey(int $userId): string
    {
        return 'notifications:last_seen:'.$userId;
    }

    public function render()
    {
        return view('livewire.notifications.bell-dropdown');
    }
}
