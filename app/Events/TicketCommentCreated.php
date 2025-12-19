<?php

namespace App\Events;

use App\Models\Helpdesk\TicketComment;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TicketCommentCreated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $ticketId;

    public int $commentId;

    public string $ticketNo;

    public string $byName;

    public bool $isInternal;

    public function __construct(public TicketComment $comment)
    {
        $this->ticketId = $comment->ticket_id;
        $this->commentId = $comment->id;
        $this->isInternal = (bool) $comment->is_internal;
        $this->byName = optional($comment->user)->name ?? 'User';
        $this->ticketNo = optional($comment->ticket)->ticket_no ?? (string) $comment->ticket_id;
    }

    public function broadcastOn(): array
    {
        $requesterId = optional($this->comment->ticket)->requester_id;

        return [new PrivateChannel('user.'.$requesterId)];
    }

    public function broadcastAs(): string
    {
        return 'TicketCommentCreated';
    }
}
