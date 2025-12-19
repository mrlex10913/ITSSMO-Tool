<?php

namespace App\Mail;

use App\Models\Helpdesk\CsatResponse;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CsatRequestMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public CsatResponse $invite) {}

    public function build(): self
    {
        return $this->subject('How did we do? Please rate your support experience')
            ->view('mail.csat.request');
    }
}
