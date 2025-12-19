<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;

class DevDashboard extends Component
{
    public string $title = 'Developer Dashboard';

    public function render()
    {
        return view('livewire.dashboard.dev-dashboard')
            ->layout('layouts.app');
    }
}
