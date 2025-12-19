<?php

namespace App\Models\Helpdesk;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketUserRead extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id', 'user_id', 'last_seen_comment_id', 'last_seen_comment_count', 'last_seen_at',
    ];

    protected $casts = [
        'last_seen_at' => 'datetime',
    ];
}
