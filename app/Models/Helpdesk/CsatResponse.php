<?php

namespace App\Models\Helpdesk;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CsatResponse extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id', 'user_id', 'requester_email', 'token', 'rating', 'comment', 'sent_at', 'submitted_at', 'expires_at', 'dismissed_until',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'submitted_at' => 'datetime',
        'expires_at' => 'datetime',
        'dismissed_until' => 'datetime',
    ];

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}
