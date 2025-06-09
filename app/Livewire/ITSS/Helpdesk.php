<?php

namespace App\Livewire\ITSS;

use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.enduser')]
class Helpdesk extends Component
{
    public function render()
    {
        return view('livewire.i-t-s-s.helpdesk');
    }
}
