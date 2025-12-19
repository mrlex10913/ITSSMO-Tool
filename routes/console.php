<?php

use App\Jobs\CheckSlaBreaches;
use App\Jobs\CheckSlaEscalations;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

// SLA Breach checker - runs every minute
Schedule::job(new CheckSlaBreaches)->everyMinute();
// SLA escalation notifier - runs every minute
Schedule::job(new CheckSlaEscalations)->everyMinute();
