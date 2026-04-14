<?php

namespace App\Models\Helpdesk;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Ticket extends Model
{
    use HasFactory;

    /**
     * Priority levels available for tickets.
     */
    public const PRIORITIES = ['low', 'medium', 'high', 'critical'];

    /**
     * Status workflow for tickets.
     */
    public const STATUSES = ['scheduled', 'open', 'in_progress', 'resolved', 'closed'];

    /**
     * Ticket types.
     */
    public const TYPES = ['incident', 'request'];

    /**
     * ITIL Impact levels (currently unused, reserved for future priority matrix).
     *
     * @see https://wiki.en.it-processmaps.com/index.php/Checklist_Incident_Priority
     */
    public const IMPACTS = ['low', 'medium', 'high'];

    /**
     * ITIL Urgency levels (currently unused, reserved for future priority matrix).
     * Future implementation: Priority = f(Impact, Urgency)
     */
    public const URGENCIES = ['low', 'medium', 'high'];

    protected $fillable = [
        // Core ticket fields
        'ticket_no', 'subject', 'description', 'type', 'status', 'priority',
        // ITIL fields (reserved for future priority matrix implementation)
        'impact', 'urgency',
        // Relationships
        'category_id', 'requester_id', 'assignee_id', 'asset_id', 'department',
        // SLA tracking
        'due_at', 'acknowledged_at', 'responded_at', 'resolved_at', 'closed_at', 'sla_policy_id', 'sla_due_at',
        // Scheduling
        'scheduled_at', 'scheduled_until', 'location',
        // Verification
        'verification_status', 'verification_method', 'verified_by', 'verified_at',
        // Guest submitter fields (for unauthenticated portal)
        'requester_name', 'requester_email', 'requester_idno',
    ];

    protected $casts = [
        'due_at' => 'datetime',
        'acknowledged_at' => 'datetime',
        'responded_at' => 'datetime',
        'resolved_at' => 'datetime',
        'closed_at' => 'datetime',
        'sla_due_at' => 'datetime',
        'scheduled_at' => 'datetime',
        'scheduled_until' => 'datetime',
        'verified_at' => 'datetime',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(TicketCategory::class, 'category_id');
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'requester_id');
    }

    /**
     * Department reference resolved by slug stored in tickets.department.
     */
    public function departmentRef(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Department::class, 'department', 'slug');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'assignee_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(TicketComment::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(TicketAttachment::class);
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'verified_by');
    }

    /**
     * Users watching this ticket with per-watcher options via TicketWatcher.
     */
    public function watchers(): HasMany
    {
        return $this->hasMany(TicketWatcher::class);
    }

    /**
     * Shortcut: users who are watchers.
     */
    public function watcherUsers(): BelongsToMany
    {
        return $this->belongsToMany(\App\Models\User::class, 'ticket_watchers')
            ->withPivot(['role', 'notify_comment', 'notify_status', 'notify_assignment', 'notify_escalation'])
            ->withTimestamps();
    }

    public function csatResponses(): HasMany
    {
        return $this->hasMany(CsatResponse::class);
    }

    public function latestCsat(): ?CsatResponse
    {
        return $this->csatResponses()->whereNotNull('submitted_at')->latest('submitted_at')->first();
    }

    /**
     * Relationship to fetch the latest submitted CSAT in a single eager load.
     */
    public function latestSubmittedCsat(): HasOne
    {
        return $this->hasOne(CsatResponse::class)
            ->whereNotNull('submitted_at')
            ->latestOfMany('submitted_at');
    }

    public function audits(): HasMany
    {
        return $this->hasMany(TicketAuditLog::class, 'ticket_id');
    }

    /**
     * Tags associated with this ticket.
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(TicketTag::class, 'ticket_tag_pivot')
            ->withPivot('added_by')
            ->withTimestamps();
    }

    /**
     * Time entries logged against this ticket.
     */
    public function timeEntries(): HasMany
    {
        return $this->hasMany(TicketTimeEntry::class);
    }

    /**
     * Get total logged time in minutes.
     */
    public function getTotalTimeMinutesAttribute(): int
    {
        return $this->timeEntries()->sum('duration_mins');
    }

    /**
     * Get formatted total time.
     */
    public function getFormattedTotalTimeAttribute(): string
    {
        $minutes = $this->total_time_minutes;
        $hours = intdiv($minutes, 60);
        $mins = $minutes % 60;

        if ($hours > 0 && $mins > 0) {
            return "{$hours}h {$mins}m";
        } elseif ($hours > 0) {
            return "{$hours}h";
        }

        return "{$mins}m";
    }

    /**
     * Links where this ticket is the source.
     */
    public function outgoingLinks(): HasMany
    {
        return $this->hasMany(TicketLink::class, 'ticket_id');
    }

    /**
     * Links where this ticket is the target.
     */
    public function incomingLinks(): HasMany
    {
        return $this->hasMany(TicketLink::class, 'linked_ticket_id');
    }

    /**
     * Get all linked tickets (both incoming and outgoing).
     */
    public function getLinkedTicketsAttribute(): \Illuminate\Support\Collection
    {
        $outgoing = $this->outgoingLinks()
            ->with('linkedTicket:id,ticket_no,subject,status,priority')
            ->get()
            ->map(fn ($link) => [
                'ticket' => $link->linkedTicket,
                'link_type' => $link->link_type,
                'link_label' => $link->link_type_label,
                'link_id' => $link->id,
            ]);

        $incoming = $this->incomingLinks()
            ->with('ticket:id,ticket_no,subject,status,priority')
            ->get()
            ->map(fn ($link) => [
                'ticket' => $link->ticket,
                'link_type' => TicketLink::getInverseLinkType($link->link_type),
                'link_label' => TicketLink::LINK_TYPES[TicketLink::getInverseLinkType($link->link_type)] ?? $link->link_type,
                'link_id' => $link->id,
            ]);

        return $outgoing->merge($incoming);
    }

    /**
     * Borrowed item records linked to this ticket.
     */
    public function borrowedItems(): HasMany
    {
        return $this->hasMany(\App\Models\Borrowers\BorrowerDetails::class);
    }

    /**
     * Check if this ticket has any unreturned borrowed items.
     */
    public function hasUnreturnedItems(): bool
    {
        return $this->borrowedItems()->where('status', 'Borrowed')->exists();
    }

    /**
     * Get count of unreturned borrowed items.
     */
    public function getUnreturnedItemsCountAttribute(): int
    {
        return $this->borrowedItems()->where('status', 'Borrowed')->count();
    }

    // Convenience for tests and internal updates
    public function setStatus(string $status): void
    {
        $this->status = $status;
        if ($status === 'resolved') {
            $this->resolved_at = $this->resolved_at ?: now();
            $this->closed_at = null;
        } elseif ($status === 'closed') {
            $this->closed_at = $this->closed_at ?: now();
        } elseif (in_array($status, ['open', 'in_progress'], true)) {
            $this->resolved_at = null;
            $this->closed_at = null;
        }
    }
}
