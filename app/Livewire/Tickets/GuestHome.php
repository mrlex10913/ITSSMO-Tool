<?php

namespace App\Livewire\Tickets;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.guest')]
class GuestHome extends Component
{
    public function render()
    {
        return view('livewire.tickets.guest-home');
    }
}
