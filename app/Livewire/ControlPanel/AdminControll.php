<?php

namespace App\Livewire\ControlPanel;

use Livewire\Component;

class AdminControll extends Component
{
    public function breadCrumbControlPanel(){
        session(['breadcrumb' => 'Control Panel /']);

    }
    public function render()
    {
        $this->breadCrumbControlPanel();
        return view('livewire.control-panel.admin-controll');
    }
}
