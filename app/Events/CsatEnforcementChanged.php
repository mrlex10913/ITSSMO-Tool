<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CsatEnforcementChanged implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public bool $active,
        public ?string $since // ISO string when active
    ) {}

    public function broadcastOn(): array
    {
        // Public channel; payload has no sensitive data
        return [new Channel('csat')];
    }

    public function broadcastAs(): string
    {
        return 'CsatEnforcementChanged';
    }
}
