<?php

namespace App\Livewire\UserRecords;

use Livewire\Component;

class StaffRecords extends Component
{
    public $editMode = false;
    public $NewStaffRecord = false;

    public $bulkUpload = false;


    public function addNewStaffRecord(){
        $this->NewStaffRecord = true;
    }
    public function uploadBulkRecord(){
        $this->bulkUpload = true;
    }

    public function render()
    {
        return view('livewire.user-records.staff-records');
    }
}
