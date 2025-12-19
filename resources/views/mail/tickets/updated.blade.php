<x-mail::message>
# Ticket #{{ $ticket->ticket_no }} Updated

Subject: {{ $ticket->subject }}

Changes:
@foreach ($changes as $key => $change)
- {{ ucfirst(str_replace('_',' ', $key)) }}: {{ $change['from'] ?? '-' }} → {{ $change['to'] ?? '-' }}
@endforeach

Thanks,
ITSS Helpdesk
</x-mail::message>
