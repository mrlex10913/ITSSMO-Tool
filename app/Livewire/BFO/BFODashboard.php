<?php

namespace App\Livewire\BFO;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.enduser')]
class BFODashboard extends Component
{
    public function render()
    {
        return view('livewire.b-f-o.b-f-o-dashboard');
    }
}
