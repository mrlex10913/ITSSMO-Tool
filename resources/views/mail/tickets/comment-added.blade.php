<x-mail::message>
# New Comment on Ticket #{{ $ticket->ticket_no }}

Hello {{ optional($ticket->requester)->name ?? 'there' }},

A new {{ $comment->is_internal ? 'internal' : 'public' }} comment was added by {{ optional($comment->user)->name ?? 'a user' }}.

Subject: {{ $ticket->subject }}

Comment:

"{{ \Illuminate\Support\Str::limit($comment->body, 500) }}"

Thanks,
ITSS Helpdesk
</x-mail::message>
