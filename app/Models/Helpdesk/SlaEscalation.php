<?php

namespace App\Models\Helpdesk;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SlaEscalation extends Model
{
    use HasFactory;

    protected $fillable = [
        'sla_policy_id', 'threshold_mins_before_breach', 'escalate_to_user_id', 'is_active',
    ];

    protected $casts = [
        'threshold_mins_before_breach' => 'integer',
        'is_active' => 'boolean',
    ];

    public function policy(): BelongsTo
    {
        return $this->belongsTo(SlaPolicy::class, 'sla_policy_id');
    }

    public function escalateTo(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'escalate_to_user_id');
    }
}
