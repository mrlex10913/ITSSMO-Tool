<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserNotificationPreference extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'email_comments',
        'email_status',
        'email_assignment',
        'email_escalation',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
