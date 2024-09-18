<?php

namespace App\Livewire\Borrowers;

use Livewire\Component;

class BorrowersForm extends Component
{
    public function render()
    {
        return view('livewire.borrowers.borrowers-form')->layout('layouts.app');
    }
}
