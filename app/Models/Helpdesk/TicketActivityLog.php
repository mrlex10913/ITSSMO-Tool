<?php

namespace App\Models\Helpdesk;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id', 'user_id', 'action', 'message', 'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    /**
     * Human-friendly activity message. For assignee updates, show names instead of IDs.
     */
    public function getDisplayMessageAttribute(): string
    {
        $msg = (string) ($this->attributes['message'] ?? '');
        $action = (string) ($this->attributes['action'] ?? '');
        if ($action === 'updated_assignee') {
            $meta = (array) ($this->meta ?? []);
            $fromName = $meta['from_name'] ?? null;
            $toName = $meta['to_name'] ?? null;
            $from = $meta['from'] ?? null;
            $to = $meta['to'] ?? null;

            // Resolve names if not already present
            if (! $fromName) {
                $fromName = $this->resolveUserLabel($from);
            }
            if (! $toName) {
                $toName = $this->resolveUserLabel($to);
            }

            return sprintf('Assignee changed: %s → %s', $fromName, $toName);
        }

        return $msg !== '' ? $msg : ucfirst(str_replace('_', ' ', $action));
    }

    protected function resolveUserLabel($value): string
    {
        if ($value === null || $value === '' || $value === 0 || $value === '0') {
            return 'Unassigned';
        }
        // If already a non-numeric name, return as-is
        if (is_string($value) && ! ctype_digit($value)) {
            return $value;
        }
        $id = (int) $value;
        $name = optional(\App\Models\User::find($id))->name;
        if ($name) {
            return $name;
        }

        return 'User #'.$id;
    }
}
