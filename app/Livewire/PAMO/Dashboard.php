<?php

namespace App\Livewire\PAMO;

use App\Models\PAMO\PamoAssets;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;


#[Layout('layouts.pamo')]
class Dashboard extends Component
{
    public $totalAssets = 0;
    public $totalConsumables = 0;
    public $lowStock = 0;
    public $maintenanceDue = 0;

    public function mount()
    {
        // Check for developer role access
        if (!Auth::user()->hasRole('Developer')) {
            return redirect()->route('dashboard')
                ->with('error', 'You do not have permission to access the PAMO system.');
        }

        // Load dashboard statistics
        $this->loadStatistics();
    }
    public function loadStatistics()
    {
        // These queries would depend on your actual data structures
        $this->totalAssets = PamoAssets::count();
        // Add other statistics calculations
    }
    public function render()
    {
        return view('livewire.p-a-m-o.dashboard');
    }
}
