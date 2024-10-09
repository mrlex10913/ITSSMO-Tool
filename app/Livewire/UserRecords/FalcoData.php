<?php

namespace App\Livewire\UserRecords;

use App\Imports\FalcoImport;
use App\Models\UserRecords\FalcoData as UserRecordsFalcoData;
use Illuminate\Validation\Rules\File;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;

class FalcoData extends Component
{
    use WithFileUploads;

    public $file;

    public $editMode = false;
    public $NewStaffRecord = false;

    public $bulkUpload = false;

    protected $rules = [
        'file' => 'required|mimes:xlsx,xls,csv|max:2048'
    ];
    public function updatedFile(){
        $this->validate();
    }
    public function import()
    {
        try{
            $this->validate();
            Excel::import(new FalcoImport, $this->file);
            flash()->success('Data Successfully imported');
            $this->bulkUpload = false;
            $this->reset('file');
        }catch(ValidationException $e){
            flash()->error('Oops! Something went wrong');
            throw $e;
        }
    }

    public function addNewStaffRecord(){
        $this->NewStaffRecord = true;
    }
    public function uploadBulkRecord(){
        $this->bulkUpload = true;
    }

    public function bulkSave(){
        dd('test');
        // dd($this->import_excel);
    }
    public function render()
    {
        $falcoData = UserRecordsFalcoData::all();
        return view('livewire.user-records.falco-data', compact('falcoData'))->layout('layouts.app');
    }
}
