<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Rate your support</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="min-h-screen bg-gray-50 flex items-center justify-center p-6">
    <div class="w-full max-w-lg bg-white shadow rounded p-6">
        <h1 class="text-xl font-semibold text-gray-900 mb-2">Rate your support</h1>
        <p class="text-gray-600 mb-4">Ticket #{{ $invite->ticket->ticket_no }} — {{ $invite->ticket->subject }}</p>

        @if (session('success'))
            <div class="mb-4 rounded bg-green-50 text-green-800 px-4 py-3">{{ session('success') }}</div>
        @endif

        <div class="flex gap-3 mb-6">
            <a class="px-3 py-2 rounded bg-green-600 text-white" href="{{ route('csat.show', ['token' => $invite->token, 'rating' => 'good']) }}">Good</a>
            <a class="px-3 py-2 rounded bg-yellow-500 text-white" href="{{ route('csat.show', ['token' => $invite->token, 'rating' => 'neutral']) }}">Neutral</a>
            <a class="px-3 py-2 rounded bg-red-600 text-white" href="{{ route('csat.show', ['token' => $invite->token, 'rating' => 'poor']) }}">Poor</a>
        </div>

        <form method="POST" action="{{ route('csat.submit', ['token' => $invite->token]) }}" class="space-y-3">
            @csrf
            <div>
                <label class="block text-sm text-gray-700">Selected rating</label>
                <select name="rating" class="mt-1 w-full border-gray-300 rounded">
                    <option value="good" @selected($invite->rating==='good')>Good</option>
                    <option value="neutral" @selected($invite->rating==='neutral')>Neutral</option>
                    <option value="poor" @selected($invite->rating==='poor')>Poor</option>
                </select>
            </div>
            <div>
                <label class="block text-sm text-gray-700">Comment (optional)</label>
                <textarea name="comment" rows="3" class="mt-1 w-full border-gray-300 rounded" placeholder="Tell us more...">{{ old('comment', $invite->comment) }}</textarea>
            </div>
            <div class="text-right">
                <button class="px-4 py-2 rounded bg-blue-600 text-white" :disabled="!$canUpdate">Submit</button>
            </div>
            @if(!$canUpdate)
                <p class="text-xs text-gray-500">Updates are allowed within 24 hours of submission.</p>
            @endif
        </form>
    </div>
</body>
</html>
