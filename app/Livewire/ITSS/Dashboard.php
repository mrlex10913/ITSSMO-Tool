<?php

namespace App\Livewire\ITSS;

use App\Models\Helpdesk\Ticket;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.enduser')]
class Dashboard extends Component
{
    public function render()
    {
        $today = now()->startOfDay();
        $tomorrow = now()->endOfDay();
        $dueToday = Ticket::query()
            ->whereIn('status', ['open', 'in_progress'])
            ->whereBetween('sla_due_at', [$today, $tomorrow])
            ->count();
        $breached = Ticket::query()
            ->whereIn('status', ['open', 'in_progress'])
            ->whereNotNull('sla_due_at')
            ->where('sla_due_at', '<', now())
            ->count();

        return view('livewire.i-t-s-s.dashboard', [
            'slaDueToday' => $dueToday,
            'slaBreached' => $breached,
        ]);
    }
}
