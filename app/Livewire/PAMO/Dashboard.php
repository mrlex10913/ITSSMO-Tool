<?php

namespace App\Livewire\PAMO;

use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        return view('livewire.p-a-m-o.dashboard')->layout('layouts.app');
    }
}
