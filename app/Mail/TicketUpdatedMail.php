<?php

namespace App\Mail;

use App\Models\Helpdesk\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TicketUpdatedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Ticket $ticket, public array $changes) {}

    public function build(): self
    {
        return $this->subject('Ticket #'.$this->ticket->ticket_no.' updated')
            ->view('mail.tickets.updated');
    }
}
