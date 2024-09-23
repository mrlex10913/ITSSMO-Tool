<?php

namespace App\Livewire\UserRecords;

use Livewire\Component;

class StudentRecords extends Component
{
    public function render()
    {
        return view('livewire.user-records.student-records')->layout('layouts.app');
    }
}
