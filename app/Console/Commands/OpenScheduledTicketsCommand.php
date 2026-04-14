<?php

namespace App\Console\Commands;

use App\Jobs\OpenScheduledTickets;
use Illuminate\Console\Command;

class OpenScheduledTicketsCommand extends Command
{
    protected $signature = 'tickets:open-scheduled';

    protected $description = 'Open scheduled tickets whose scheduled time has arrived';

    public function handle(): int
    {
        $this->info('Checking for scheduled tickets to open...');

        $job = new OpenScheduledTickets;
        $job->handle();

        $this->info('Done.');

        return self::SUCCESS;
    }
}
