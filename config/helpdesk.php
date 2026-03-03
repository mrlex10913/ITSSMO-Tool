<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Business Hours Configuration
    |--------------------------------------------------------------------------
    |
    | Define the working hours for SLA calculations. SLA timers will only
    | count during these hours. Outside business hours, the SLA clock pauses.
    |
    */

    'business_hours' => [
        // Enable/disable business hours calculation
        // When disabled, SLA is calculated 24/7
        'enabled' => env('HELPDESK_BUSINESS_HOURS_ENABLED', true),

        // Timezone for business hours calculation
        'timezone' => env('HELPDESK_TIMEZONE', 'Asia/Manila'),

        // Working days (0 = Sunday, 1 = Monday, ..., 6 = Saturday)
        'working_days' => [1, 2, 3, 4, 5], // Monday to Friday

        // Working hours (24-hour format)
        'start_hour' => env('HELPDESK_START_HOUR', 8),  // 8:00 AM
        'start_minute' => env('HELPDESK_START_MINUTE', 0),
        'end_hour' => env('HELPDESK_END_HOUR', 17),     // 5:00 PM
        'end_minute' => env('HELPDESK_END_MINUTE', 0),
    ],

    /*
    |--------------------------------------------------------------------------
    | Holidays Configuration
    |--------------------------------------------------------------------------
    |
    | Define holidays when the helpdesk is closed. SLA timers will pause
    | during these dates. Format: 'YYYY-MM-DD' or 'MM-DD' for recurring.
    |
    */

    'holidays' => [
        // Fixed date holidays (YYYY-MM-DD) - specific to year
        // '2026-01-01', // New Year's Day
        // '2026-12-25', // Christmas Day

        // Recurring holidays (MM-DD) - same every year
        '01-01', // New Year's Day
        '04-09', // Araw ng Kagitingan (Day of Valor)
        '05-01', // Labor Day
        '06-12', // Independence Day
        '08-21', // Ninoy Aquino Day
        '08-26', // National Heroes Day (last Monday of August - approximate)
        '11-01', // All Saints' Day
        '11-30', // Bonifacio Day
        '12-25', // Christmas Day
        '12-30', // Rizal Day
        '12-31', // New Year's Eve
    ],

    /*
    |--------------------------------------------------------------------------
    | SLA Settings
    |--------------------------------------------------------------------------
    |
    | Additional SLA-related configuration options.
    |
    */

    'sla' => [
        // Whether to pause SLA during holidays
        'pause_on_holidays' => env('HELPDESK_PAUSE_ON_HOLIDAYS', true),

        // Grace period in minutes before marking as breached (0 = no grace)
        'grace_period_mins' => env('HELPDESK_GRACE_PERIOD', 0),
    ],

];
