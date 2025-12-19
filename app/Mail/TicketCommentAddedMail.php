<?php

namespace App\Mail;

use App\Models\Helpdesk\Ticket;
use App\Models\Helpdesk\TicketComment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TicketCommentAddedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Ticket $ticket, public TicketComment $comment) {}

    public function build(): self
    {
        return $this->subject('New comment on Ticket #'.$this->ticket->ticket_no)
            ->view('mail.tickets.comment-added');
    }
}
