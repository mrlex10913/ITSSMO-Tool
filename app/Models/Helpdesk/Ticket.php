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

    protected $fillable = [
        'ticket_no', 'subject', 'description', 'type', 'status', 'priority', 'impact', 'urgency',
        'category_id', 'requester_id', 'assignee_id', 'asset_id', 'department',
        'due_at', 'acknowledged_at', 'responded_at', 'resolved_at', 'closed_at', 'sla_policy_id', 'sla_due_at',
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
