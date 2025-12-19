<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TicketChanged implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public int $ticketId,
        public string $type = 'updated' // created|updated|commented|deleted
    ) {}

    public function broadcastOn(): array
    {
        return [new PrivateChannel('tickets')];
    }

    public function broadcastAs(): string
    {
        return 'TicketChanged';
    }
}
