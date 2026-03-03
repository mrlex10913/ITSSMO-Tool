<?php

namespace App\Services\Helpdesk;

use App\Models\Helpdesk\SlaPolicy;
use Carbon\CarbonImmutable;

class SlaResolver
{
    /**
     * Pick the appropriate SLA policy based on ticket type and priority.
     */
    public static function pickPolicy(string $type, string $priority): ?SlaPolicy
    {
        return SlaPolicy::query()
            ->where('type', $type)
            ->where('priority', $priority)
            ->where('is_active', true)
            ->orderByDesc('id')
            ->first();
    }

    /**
     * Compute the SLA due date by adding minutes, respecting business hours if enabled.
     *
     * @param  int|null  $minutes  Number of minutes to add
     * @param  CarbonImmutable|null  $from  Starting point (defaults to now)
     */
    public static function computeDueAt(?int $minutes, ?CarbonImmutable $from = null): ?CarbonImmutable
    {
        // Cast to int in case DB returns string numerics
        $mins = $minutes !== null ? (int) $minutes : null;

        if (! $mins || $mins <= 0) {
            return null;
        }

        // Use business hours calculator if enabled
        $calculator = new BusinessHoursCalculator;

        if ($calculator->isEnabled()) {
            return $calculator->addBusinessMinutes($mins, $from);
        }

        // Fallback: simple 24/7 calculation
        return ($from ?? CarbonImmutable::now())->addMinutes($mins);
    }

    /**
     * Compute the first response due date.
     *
     * @param  int|null  $minutes  Response time in minutes from policy
     * @param  CarbonImmutable|null  $from  Starting point (defaults to now)
     */
    public static function computeResponseDueAt(?int $minutes, ?CarbonImmutable $from = null): ?CarbonImmutable
    {
        return self::computeDueAt($minutes, $from);
    }

    /**
     * Check if a ticket has breached its SLA.
     *
     * @param  CarbonImmutable|null  $dueAt  The SLA due date
     * @param  CarbonImmutable|null  $completedAt  When the ticket was resolved (null if not yet)
     */
    public static function isBreached(?CarbonImmutable $dueAt, ?CarbonImmutable $completedAt = null): bool
    {
        if (! $dueAt) {
            return false;
        }

        $checkAgainst = $completedAt ?? CarbonImmutable::now();

        // Apply grace period if configured
        $gracePeriod = config('helpdesk.sla.grace_period_mins', 0);
        if ($gracePeriod > 0) {
            $dueAt = $dueAt->addMinutes($gracePeriod);
        }

        return $checkAgainst->greaterThan($dueAt);
    }

    /**
     * Get the remaining business minutes until SLA breach.
     *
     * @return int|null Negative if breached, null if no SLA
     */
    public static function getRemainingMinutes(?CarbonImmutable $dueAt): ?int
    {
        if (! $dueAt) {
            return null;
        }

        $calculator = new BusinessHoursCalculator;
        $now = CarbonImmutable::now();

        if ($now->greaterThan($dueAt)) {
            // Already breached - return negative minutes
            return -$calculator->getBusinessMinutesBetween($dueAt, $now);
        }

        return $calculator->getBusinessMinutesBetween($now, $dueAt);
    }

    /**
     * Format SLA time for display.
     */
    public static function formatSlaTime(int $minutes): string
    {
        $calculator = new BusinessHoursCalculator;

        return $calculator->formatDuration($minutes);
    }
}
