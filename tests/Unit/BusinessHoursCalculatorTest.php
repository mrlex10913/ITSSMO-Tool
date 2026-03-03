<?php

namespace Tests\Unit;

use App\Services\Helpdesk\BusinessHoursCalculator;
use Carbon\CarbonImmutable;
use Tests\TestCase;

class BusinessHoursCalculatorTest extends TestCase
{
    protected BusinessHoursCalculator $calculator;

    protected function setUp(): void
    {
        parent::setUp();

        // Configure business hours for testing: Mon-Fri, 8 AM - 5 PM
        config([
            'helpdesk.business_hours.enabled' => true,
            'helpdesk.business_hours.timezone' => 'Asia/Manila',
            'helpdesk.business_hours.working_days' => [1, 2, 3, 4, 5], // Mon-Fri
            'helpdesk.business_hours.start_hour' => 8,
            'helpdesk.business_hours.start_minute' => 0,
            'helpdesk.business_hours.end_hour' => 17,
            'helpdesk.business_hours.end_minute' => 0,
            'helpdesk.holidays' => ['01-01', '12-25'],
            'helpdesk.sla.pause_on_holidays' => true,
        ]);

        $this->calculator = new BusinessHoursCalculator;
    }

    public function test_is_within_business_hours_during_working_time(): void
    {
        // Monday at 10 AM
        $monday10am = CarbonImmutable::create(2026, 2, 9, 10, 0, 0, 'Asia/Manila');

        $this->assertTrue($this->calculator->isWithinBusinessHours($monday10am));
    }

    public function test_is_not_within_business_hours_on_weekend(): void
    {
        // Saturday at 10 AM
        $saturday = CarbonImmutable::create(2026, 2, 7, 10, 0, 0, 'Asia/Manila');

        $this->assertFalse($this->calculator->isWithinBusinessHours($saturday));
    }

    public function test_is_not_within_business_hours_before_start(): void
    {
        // Monday at 7 AM (before 8 AM start)
        $monday7am = CarbonImmutable::create(2026, 2, 9, 7, 0, 0, 'Asia/Manila');

        $this->assertFalse($this->calculator->isWithinBusinessHours($monday7am));
    }

    public function test_is_not_within_business_hours_after_end(): void
    {
        // Monday at 6 PM (after 5 PM end)
        $monday6pm = CarbonImmutable::create(2026, 2, 9, 18, 0, 0, 'Asia/Manila');

        $this->assertFalse($this->calculator->isWithinBusinessHours($monday6pm));
    }

    public function test_is_holiday(): void
    {
        // January 1st (New Year)
        $newYear = CarbonImmutable::create(2026, 1, 1, 10, 0, 0, 'Asia/Manila');

        $this->assertTrue($this->calculator->isHoliday($newYear));
    }

    public function test_is_not_holiday_on_regular_day(): void
    {
        // February 9th (regular Monday)
        $regularDay = CarbonImmutable::create(2026, 2, 9, 10, 0, 0, 'Asia/Manila');

        $this->assertFalse($this->calculator->isHoliday($regularDay));
    }

    public function test_add_business_minutes_same_day(): void
    {
        // Start at Monday 10 AM, add 60 minutes
        $monday10am = CarbonImmutable::create(2026, 2, 9, 10, 0, 0, 'Asia/Manila');
        $result = $this->calculator->addBusinessMinutes(60, $monday10am);

        // Should be 11 AM same day
        $this->assertEquals('2026-02-09 11:00:00', $result->format('Y-m-d H:i:s'));
    }

    public function test_add_business_minutes_spans_overnight(): void
    {
        // Start at Monday 4 PM, add 120 minutes (2 hours)
        // Only 60 mins left in day, should carry 60 mins to next day
        $monday4pm = CarbonImmutable::create(2026, 2, 9, 16, 0, 0, 'Asia/Manila');
        $result = $this->calculator->addBusinessMinutes(120, $monday4pm);

        // Should be Tuesday 9 AM (8 AM + 60 mins)
        $this->assertEquals('2026-02-10 09:00:00', $result->format('Y-m-d H:i:s'));
    }

    public function test_add_business_minutes_skips_weekend(): void
    {
        // Start at Friday 4 PM, add 120 minutes (2 hours)
        $friday4pm = CarbonImmutable::create(2026, 2, 6, 16, 0, 0, 'Asia/Manila');
        $result = $this->calculator->addBusinessMinutes(120, $friday4pm);

        // Should skip weekend and be Monday 9 AM
        $this->assertEquals('2026-02-09 09:00:00', $result->format('Y-m-d H:i:s'));
    }

    public function test_add_business_minutes_from_outside_business_hours(): void
    {
        // Start at Saturday 10 AM, add 60 minutes
        $saturday = CarbonImmutable::create(2026, 2, 7, 10, 0, 0, 'Asia/Manila');
        $result = $this->calculator->addBusinessMinutes(60, $saturday);

        // Should start from Monday 8 AM and add 60 mins = Monday 9 AM
        $this->assertEquals('2026-02-09 09:00:00', $result->format('Y-m-d H:i:s'));
    }

    public function test_get_business_minutes_per_day(): void
    {
        // 8 AM to 5 PM = 9 hours = 540 minutes
        $this->assertEquals(540, $this->calculator->getBusinessMinutesPerDay());
    }

    public function test_format_duration_minutes(): void
    {
        $this->assertEquals('30 mins', $this->calculator->formatDuration(30));
    }

    public function test_format_duration_hours(): void
    {
        $this->assertEquals('2 hrs', $this->calculator->formatDuration(120));
    }

    public function test_format_duration_hours_and_minutes(): void
    {
        $this->assertEquals('2 hrs 30 mins', $this->calculator->formatDuration(150));
    }

    public function test_format_duration_business_days(): void
    {
        // 540 mins = 1 business day
        $this->assertEquals('1 business day', $this->calculator->formatDuration(540));
    }

    public function test_disabled_calculator_uses_simple_calculation(): void
    {
        config(['helpdesk.business_hours.enabled' => false]);
        $calculator = new BusinessHoursCalculator;

        // Start at Saturday 10 AM, add 60 minutes
        $saturday = CarbonImmutable::create(2026, 2, 7, 10, 0, 0, 'Asia/Manila');
        $result = $calculator->addBusinessMinutes(60, $saturday);

        // When disabled, should just add 60 mins regardless of weekend
        $this->assertEquals('2026-02-07 11:00:00', $result->format('Y-m-d H:i:s'));
    }
}
