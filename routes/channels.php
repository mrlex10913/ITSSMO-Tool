<?php

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Log;

Broadcast::channel('user.{id}', function ($user, $id) {
    $authorized = (int) $user->id === (int) $id;
    if (! $authorized) {
        Log::debug('Broadcast auth failed for user channel', [
            'user_id' => $user->id,
            'channel_id' => $id,
        ]);
    }

    return $authorized;
});

Broadcast::channel('tickets', function ($user) {
    // Allow ITSS/Administrator/Developer roles
    $role = optional($user->role)->slug ? strtolower($user->role->slug) : '';

    $authorized = in_array($role, ['itss', 'administrator', 'developer']);
    if (! $authorized) {
        Log::debug('Broadcast auth failed for tickets channel', [
            'user_id' => $user->id,
            'role' => $role,
        ]);
    }

    return $authorized;
});

// Public CSAT channel (no auth required)
Broadcast::channel('csat', function () {
    return true;
});
