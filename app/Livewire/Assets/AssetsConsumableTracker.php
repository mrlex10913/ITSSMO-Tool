<?php

namespace App\Livewire\Assets;

use Livewire\Component;

class AssetsConsumableTracker extends Component
{
    public function render()
    {
        return view('livewire.assets.assets-consumable-tracker')->layout('layouts.app');
    }
}
