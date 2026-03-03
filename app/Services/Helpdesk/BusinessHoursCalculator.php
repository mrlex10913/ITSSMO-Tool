<?php

namespace App\Services\Helpdesk;

use Carbon\CarbonImmutable;

class BusinessHoursCalculator
{
    protected bool $enabled;

    protected string $timezone;

    protected array $workingDays;

    protected int $startHour;

    protected int $startMinute;

    protected int $endHour;

    protected int $endMinute;

    protected array $holidays;

    protected bool $pauseOnHolidays;

    public function __construct()
    {
        $this->enabled = config('helpdesk.business_hours.enabled', true);
        $this->timezone = config('helpdesk.business_hours.timezone', 'Asia/Manila');
        $this->workingDays = config('helpdesk.business_hours.working_days', [1, 2, 3, 4, 5]);
        $this->startHour = config('helpdesk.business_hours.start_hour', 8);
        $this->startMinute = config('helpdesk.business_hours.start_minute', 0);
        $this->endHour = config('helpdesk.business_hours.end_hour', 17);
        $this->endMinute = config('helpdesk.business_hours.end_minute', 0);
        $this->holidays = config('helpdesk.holidays', []);
        $this->pauseOnHolidays = config('helpdesk.sla.pause_on_holidays', true);
    }

    /**
     * Calculate the due date by adding business minutes to the start time.
     *
     * @param  int  $minutes  Number of business minutes to add
     * @param  CarbonImmutable|null  $from  Starting point (defaults to now)
     */
    public function addBusinessMinutes(int $minutes, ?CarbonImmutable $from = null): CarbonImmutable
    {
        if (! $this->enabled || $minutes <= 0) {
            return ($from ?? CarbonImmutable::now($this->timezone))->addMinutes($minutes);
        }

        $current = ($from ?? CarbonImmutable::now($this->timezone))->setTimezone($this->timezone);
        $remainingMinutes = $minutes;

        // If starting outside business hours, move to next business hour start
        if (! $this->isWithinBusinessHours($current)) {
            $current = $this->getNextBusinessStart($current);
        }

        // Maximum iterations to prevent infinite loops (1 year of days)
        $maxIterations = 365;
        $iterations = 0;

        while ($remainingMinutes > 0 && $iterations < $maxIterations) {
            $iterations++;

            // Skip if current day is not a working day or is a holiday
            if (! $this->isWorkingDay($current) || $this->isHoliday($current)) {
                $current = $this->getNextBusinessStart($current);

                continue;
            }

            // Calculate minutes remaining until end of business day
            $endOfDay = $current->copy()->setTime($this->endHour, $this->endMinute, 0);
            $minutesUntilEndOfDay = max(0, $current->diffInMinutes($endOfDay, false));

            if ($remainingMinutes <= $minutesUntilEndOfDay) {
                // We can complete within today's business hours
                $current = $current->addMinutes($remainingMinutes);
                $remainingMinutes = 0;
            } else {
                // Use up today's remaining business hours and move to next day
                $remainingMinutes -= $minutesUntilEndOfDay;
                $current = $this->getNextBusinessStart($current->addDay()->startOfDay());
            }
        }

        return CarbonImmutable::instance($current);
    }

    /**
     * Calculate the number of business minutes between two dates.
     */
    public function getBusinessMinutesBetween(CarbonImmutable $start, CarbonImmutable $end): int
    {
        if (! $this->enabled) {
            return max(0, $start->diffInMinutes($end));
        }

        $start = $start->setTimezone($this->timezone);
        $end = $end->setTimezone($this->timezone);

        if ($end->lessThanOrEqualTo($start)) {
            return 0;
        }

        $totalMinutes = 0;
        $current = $start->copy();

        // If starting outside business hours, move to next business start
        if (! $this->isWithinBusinessHours($current)) {
            $current = $this->getNextBusinessStart($current);
        }

        // Maximum iterations to prevent infinite loops
        $maxIterations = 365;
        $iterations = 0;

        while ($current->lessThan($end) && $iterations < $maxIterations) {
            $iterations++;

            if (! $this->isWorkingDay($current) || $this->isHoliday($current)) {
                $current = $this->getNextBusinessStart($current);

                continue;
            }

            $dayStart = max(
                $current,
                $current->copy()->setTime($this->startHour, $this->startMinute, 0)
            );

            $dayEnd = min(
                $end,
                $current->copy()->setTime($this->endHour, $this->endMinute, 0)
            );

            if ($dayEnd->greaterThan($dayStart)) {
                $totalMinutes += $dayStart->diffInMinutes($dayEnd);
            }

            // Move to next day
            $current = $this->getNextBusinessStart($current->addDay()->startOfDay());
        }

        return $totalMinutes;
    }

    /**
     * Check if a given time is within business hours.
     */
    public function isWithinBusinessHours(CarbonImmutable $time): bool
    {
        $time = $time->setTimezone($this->timezone);

        if (! $this->isWorkingDay($time)) {
            return false;
        }

        if ($this->isHoliday($time)) {
            return false;
        }

        $startOfBusiness = $time->copy()->setTime($this->startHour, $this->startMinute, 0);
        $endOfBusiness = $time->copy()->setTime($this->endHour, $this->endMinute, 0);

        return $time->greaterThanOrEqualTo($startOfBusiness) && $time->lessThan($endOfBusiness);
    }

    /**
     * Check if a given date is a working day (not weekend).
     */
    public function isWorkingDay(CarbonImmutable $date): bool
    {
        return in_array($date->dayOfWeek, $this->workingDays);
    }

    /**
     * Check if a given date is a holiday.
     */
    public function isHoliday(CarbonImmutable $date): bool
    {
        if (! $this->pauseOnHolidays) {
            return false;
        }

        $date = $date->setTimezone($this->timezone);
        $fullDate = $date->format('Y-m-d');
        $recurringDate = $date->format('m-d');

        foreach ($this->holidays as $holiday) {
            // Check full date match (YYYY-MM-DD)
            if ($holiday === $fullDate) {
                return true;
            }
            // Check recurring date match (MM-DD)
            if (strlen($holiday) === 5 && $holiday === $recurringDate) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get the next business hours start time from a given time.
     */
    public function getNextBusinessStart(CarbonImmutable $from): CarbonImmutable
    {
        $current = $from->setTimezone($this->timezone);

        // If we're before start time on a working day, return today's start
        if ($this->isWorkingDay($current) && ! $this->isHoliday($current)) {
            $todayStart = $current->copy()->setTime($this->startHour, $this->startMinute, 0);
            if ($current->lessThan($todayStart)) {
                return $todayStart;
            }
            // If we're within business hours, return current time
            $todayEnd = $current->copy()->setTime($this->endHour, $this->endMinute, 0);
            if ($current->lessThan($todayEnd)) {
                return $current;
            }
        }

        // Move to next day and find next working day
        $current = $current->addDay()->startOfDay();
        $maxIterations = 14; // 2 weeks max to find a working day
        $iterations = 0;

        while ($iterations < $maxIterations) {
            if ($this->isWorkingDay($current) && ! $this->isHoliday($current)) {
                return $current->setTime($this->startHour, $this->startMinute, 0);
            }
            $current = $current->addDay();
            $iterations++;
        }

        // Fallback: return original + 1 day at start time
        return $from->addDay()->setTime($this->startHour, $this->startMinute, 0);
    }

    /**
     * Get business hours per day in minutes.
     */
    public function getBusinessMinutesPerDay(): int
    {
        return ($this->endHour * 60 + $this->endMinute) - ($this->startHour * 60 + $this->startMinute);
    }

    /**
     * Format a duration in business minutes to human readable string.
     */
    public function formatDuration(int $minutes): string
    {
        if ($minutes < 60) {
            return $minutes.' min'.($minutes !== 1 ? 's' : '');
        }

        $hours = (int) floor($minutes / 60);
        $mins = $minutes % 60;

        $businessHoursPerDay = $this->getBusinessMinutesPerDay();
        if ($minutes >= $businessHoursPerDay) {
            $days = (int) floor($minutes / $businessHoursPerDay);
            $remainingMins = $minutes % $businessHoursPerDay;
            $remainingHours = (int) floor($remainingMins / 60);
            $remainingMinutes = $remainingMins % 60;

            $parts = [];
            if ($days > 0) {
                $parts[] = $days.' business day'.($days !== 1 ? 's' : '');
            }
            if ($remainingHours > 0) {
                $parts[] = $remainingHours.' hr'.($remainingHours !== 1 ? 's' : '');
            }
            if ($remainingMinutes > 0) {
                $parts[] = $remainingMinutes.' min'.($remainingMinutes !== 1 ? 's' : '');
            }

            return implode(' ', $parts);
        }

        if ($mins === 0) {
            return $hours.' hr'.($hours !== 1 ? 's' : '');
        }

        return $hours.' hr'.($hours !== 1 ? 's' : '').' '.$mins.' min'.($mins !== 1 ? 's' : '');
    }

    /**
     * Check if business hours calculation is enabled.
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }
}
