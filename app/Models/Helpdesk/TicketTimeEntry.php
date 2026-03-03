<?php

namespace App\Models\Helpdesk;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketTimeEntry extends Model
{
    protected $fillable = [
        'ticket_id',
        'user_id',
        'duration_mins',
        'description',
        'work_date',
        'is_billable',
    ];

    protected function casts(): array
    {
        return [
            'work_date' => 'date',
            'is_billable' => 'boolean',
            'duration_mins' => 'integer',
        ];
    }

    /**
     * Get the ticket this time entry belongs to.
     */
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    /**
     * Get the user who logged this time.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get formatted duration as hours and minutes.
     */
    public function getFormattedDurationAttribute(): string
    {
        $hours = intdiv($this->duration_mins, 60);
        $minutes = $this->duration_mins % 60;

        if ($hours > 0 && $minutes > 0) {
            return "{$hours}h {$minutes}m";
        } elseif ($hours > 0) {
            return "{$hours}h";
        }

        return "{$minutes}m";
    }

    /**
     * Get duration in hours (decimal).
     */
    public function getDurationHoursAttribute(): float
    {
        return round($this->duration_mins / 60, 2);
    }
}
