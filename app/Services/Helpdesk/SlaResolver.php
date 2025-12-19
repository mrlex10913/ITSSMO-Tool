<?php

namespace App\Services\Helpdesk;

use App\Models\Helpdesk\SlaPolicy;
use Carbon\CarbonImmutable;

class SlaResolver
{
    public static function pickPolicy(string $type, string $priority): ?SlaPolicy
    {
        return SlaPolicy::query()
            ->where('type', $type)
            ->where('priority', $priority)
            ->where('is_active', true)
            ->orderByDesc('id')
            ->first();
    }

    public static function computeDueAt(?int $minutes): ?CarbonImmutable
    {
        // Cast to int in case DB returns string numerics
        $mins = $minutes !== null ? (int) $minutes : null;

        if (! $mins || $mins <= 0) {
            return null;
        }

        return CarbonImmutable::now()->addMinutes($mins);
    }
}
