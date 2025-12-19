<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('user.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('tickets', function ($user) {
    // Allow ITSS/Administrator/Developer roles
    $role = optional($user->role)->slug ? strtolower($user->role->slug) : '';

    return in_array($role, ['itss', 'administrator', 'developer']);
});
