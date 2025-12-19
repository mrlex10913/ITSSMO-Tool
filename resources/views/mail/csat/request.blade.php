<x-mail::message>
# Please rate your support experience

Ticket #{{ $invite->ticket->ticket_no }} — {{ $invite->ticket->subject }}

Click a rating below (one click):

- [Good]({{ route('csat.show', ['token' => $invite->token, 'rating' => 'good']) }})
- [Neutral]({{ route('csat.show', ['token' => $invite->token, 'rating' => 'neutral']) }})
- [Poor]({{ route('csat.show', ['token' => $invite->token, 'rating' => 'poor']) }})

You can optionally leave a comment on the page after clicking.

Thanks,
ITSS Helpdesk
</x-mail::message>
