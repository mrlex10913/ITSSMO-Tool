<?php

namespace App\Livewire\Borrowers;

use App\Models\Borrowers\BorrowerDetails;
use Livewire\Component;

class BorrowersLogs extends Component
{

    public function render()
    {
        $brfLogs = BorrowerDetails::with(['itemBorrow.assetCategory'])->get();
        // dd($brfLogs);
        return view('livewire.borrowers.borrowers-logs', compact('brfLogs'))->layout('layouts.app');
    }
}
