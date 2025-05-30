<?php

namespace App\Livewire\BFO;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.enduser')]
class Cheque extends Component
{
    public function render()
    {
        return view('livewire.b-f-o.cheque');
    }
}
