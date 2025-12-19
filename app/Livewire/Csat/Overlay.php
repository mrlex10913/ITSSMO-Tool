<?php

namespace App\Livewire\Csat;

use App\Models\SystemCsatResponse;
use App\Services\CsatEnforcement;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class Overlay extends Component
{
    public bool $show = false;
    public int $rating = 0;
    public string $comment = '';

    public function mount(): void
    {
        $this->evaluateVisibility();
    }

    #[On('csat:check')]
    public function evaluateVisibility(): void
    {
        $user = Auth::user();
        $this->show = $user ? CsatEnforcement::requiresOverlay($user) : false;
    }

    public function setRating(int $value): void
    {
        if ($value < 1) { $value = 1; }
        if ($value > 5) { $value = 5; }
        $this->rating = $value;
    }

    public function submit(): void
    {
        $user = Auth::user();
        if (! $user) {
            return;
        }
        $this->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:2000',
        ]);

        SystemCsatResponse::create([
            'user_id' => $user->id,
            'rating' => $this->rating,
            'comment' => $this->comment ?: null,
            'submitted_at' => now(),
        ]);

    $this->dispatch('csat-thankyou');
    $this->show = false;
    $this->dispatch('success', message: 'Thanks for your feedback!');
    }

    public function render()
    {
        return view('livewire.csat.overlay');
    }
}
