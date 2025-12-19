<?php

namespace App\Http\Controllers;

use App\Http\Requests\CsatSubmitRequest;
use App\Models\Helpdesk\CsatResponse;
use Illuminate\Http\Request;

class CsatController extends Controller
{
    protected function findValid(string $token): ?CsatResponse
    {
        $inv = CsatResponse::with('ticket:id,ticket_no,subject')
            ->where('token', $token)
            ->first();
        if (! $inv) {
            return null;
        }
        if ($inv->expires_at && now()->greaterThan($inv->expires_at)) {
            return null;
        }

        return $inv;
    }

    public function show(Request $request, string $token)
    {
        $inv = $this->findValid($token);
        if (! $inv) {
            return response()->view('csat.invalid', [], 404);
        }

        // One-click rating via query parameter
        $rate = $request->string('rating')->toString();
        if (in_array($rate, ['good', 'neutral', 'poor'], true)) {
            $this->applyRating($inv, $rate, null);
        }

        return view('csat.rate', [
            'invite' => $inv->fresh(),
            'canUpdate' => $this->canUpdate($inv),
        ]);
    }

    public function submit(CsatSubmitRequest $request, string $token)
    {
        $inv = $this->findValid($token);
        if (! $inv) {
            return response()->view('csat.invalid', [], 404);
        }
        $data = $request->validated();
        $this->applyRating($inv, $data['rating'], $data['comment'] ?? null);

        return redirect()->route('csat.show', ['token' => $inv->token])
            ->with('success', 'Thanks for your feedback!');
    }

    protected function canUpdate(CsatResponse $inv): bool
    {
        if (! $inv->submitted_at) {
            return true;
        }

        return now()->diffInHours($inv->submitted_at) <= 24;
    }

    protected function applyRating(CsatResponse $inv, string $rating, ?string $comment): void
    {
        if (! $this->canUpdate($inv)) {
            return;
        }
        $inv->rating = $rating;
        if ($comment !== null) {
            $inv->comment = $comment;
        }
        if (! $inv->submitted_at) {
            $inv->submitted_at = now();
        }
        $inv->save();
    }
}
