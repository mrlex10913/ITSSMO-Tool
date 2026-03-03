<?php

namespace App\Models\Helpdesk;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketLink extends Model
{
    public const LINK_TYPES = [
        'related' => 'Related to',
        'parent' => 'Parent of',
        'child' => 'Child of',
        'duplicate' => 'Duplicate of',
        'blocks' => 'Blocks',
        'blocked_by' => 'Blocked by',
    ];

    protected $fillable = [
        'ticket_id',
        'linked_ticket_id',
        'link_type',
        'created_by',
    ];

    /**
     * Get the source ticket.
     */
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class, 'ticket_id');
    }

    /**
     * Get the linked ticket.
     */
    public function linkedTicket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class, 'linked_ticket_id');
    }

    /**
     * Get the user who created this link.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the human-readable link type label.
     */
    public function getLinkTypeLabelAttribute(): string
    {
        return self::LINK_TYPES[$this->link_type] ?? $this->link_type;
    }

    /**
     * Get the inverse link type for bidirectional relationships.
     */
    public static function getInverseLinkType(string $linkType): string
    {
        return match ($linkType) {
            'parent' => 'child',
            'child' => 'parent',
            'blocks' => 'blocked_by',
            'blocked_by' => 'blocks',
            default => $linkType, // related, duplicate are symmetric
        };
    }
}
