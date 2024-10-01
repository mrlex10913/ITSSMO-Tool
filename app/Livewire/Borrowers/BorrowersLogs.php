<?php

namespace App\Livewire\Borrowers;

use App\Models\Assets\AssetList;
use App\Models\Borrowers\BorrowerDetails;
use App\Models\Borrowers\BorrowerItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\WithPagination;

class BorrowersLogs extends Component
{
    use WithPagination;
    public $search = '';
    public $id_number;
    public $doc_tracker;
    public $brfLogs_name;
    public $brfLogs_contact;
    public $brfLogs_department;
    public $brfLogs_authorizedby;
    public $brfLogs_dateborrowed;
    public $brfLogs_datereturn;
    public $brfLogs_location;
    public $brfLogs_event;
    public $brfLogs_remarks;
    public $brfLogs_receivedby;
    public $brfLogs_releasecheckedby;
    public $brfLogs_notedby;
    public $brfLogs_retrunedBy;
    public $brfLogs_status;
    public $brfLogs_receivedcheckedby;
    public $brfLogs_items = [];
    public $brfLogs_returnremarks = [];

    public $updateBorrowed = false;
    public $editBorrower;
    public $asset_serial = [];

    public function updateBorrower($brfID){
        $this->updateBorrowed = true;
        $this->editBorrower = $brfID;
        $brfFind = BorrowerDetails::with('itemBorrow.assetCategory')->find($brfID);
        $this->id_number = $brfFind->id_number;
        $this->doc_tracker = $brfFind->doc_tracker;
        $this->brfLogs_name = $brfFind->name;
        $this->brfLogs_contact = $brfFind->contact;
        $this->brfLogs_department = $brfFind->department;
        $this->brfLogs_authorizedby = $brfFind->authorizedby;
        $this->brfLogs_dateborrowed = $brfFind->date_to_borrow;
        $this->brfLogs_datereturn = $brfFind->date_to_return;
        $this->brfLogs_location = $brfFind->location;
        $this->brfLogs_event = $brfFind->event;
        $this->brfLogs_receivedby = $brfFind->receivedby;
        $this->brfLogs_releasecheckedby = $brfFind->released_checkedby;
        $this->brfLogs_notedby = $brfFind->notedby;
        $this->brfLogs_status = $brfFind->status;
        $this->brfLogs_receivedcheckedby = Auth::user()->name;

        // $this->brfLogs_items = $brfFind->assetCategories->toArray();

        $this->brfLogs_items = $brfFind->itemBorrow->map(function ($item) {
            return [
                'asset_category_name' => $item->assetCategory->name,
                'serial' => $item->serial,
                'brand' => $item->brand,
                'remarks' => $item->remarks
            ];
        })->toArray();

        $this->asset_serial =
        $brfFind->itemBorrow->map(function ($item) {
            return [

                'serial' => $item->serial,
            ];
        })->toArray();

    }

    public function submitUpdatedBorrowers(){
        try{
            $this->validate([
            'brfLogs_receivedcheckedby' => 'required',
             'brfLogs_retrunedBy' => 'required',
            ]);
            $updatedBorrowers = BorrowerDetails::with('itemBorrow')->find($this->editBorrower);
            $updateItemBorrow = BorrowerItem::where('borrower_id', $this->editBorrower)->get();

            $updatedBorrowers->recieved_checkedby = $this->brfLogs_receivedcheckedby;
            $updatedBorrowers->returnby = $this->brfLogs_retrunedBy;
            $updatedBorrowers->status = $this->brfLogs_status;
            $updatedBorrowers->save();

            foreach ($updateItemBorrow as $index => $item) {
                if (isset($this->brfLogs_returnremarks[$index])) {
                    $item->return_remarks = $this->brfLogs_returnremarks[$index]['logs'];
                    $item->date_of_return_remarks = Carbon::now();
                    $item->save();

                    $asset = AssetList::where('item_serial_itss', $item->serial)->first();
                    if ($asset) {
                        $asset->status = 'Available';
                        $asset->save();
                    }
                }
            }
            flash()->success('Borrowed Item has been return with remarks');

        // Optionally close the modal or reset some values
        $this->reset(['updateBorrowed']);

         }catch(ValidationException $e){
             flash()->error('Ooops! Something went wrong');
             throw $e;
         }
    }

    public function resetForm()
{
    $this->reset([
        'brfLogs_returnremarks',
        'brfLogs_receivedcheckedby',
        'brfLogs_retrunedBy',
        'brfLogs_status'
    ]);
}
public function updatingSearch(){
    $this->resetPage();
}

    public function render()
    {
        $brfLogs = BorrowerDetails::with(['itemBorrow.assetCategory'])
        ->where('status', 'Borrowed')
        ->orderBy('created_at', 'desc')
        ->when($this->search, function($query){
            $query->where('doc_tracker', 'like', '%' .$this->search . '%')
            ->orWHere('id_number', 'like', '%' .$this->search . '%')
            ->orWHere('name', 'like', '%' .$this->search . '%');
        })
        ->paginate(5);;
        // dd($brfLogs);
        return view('livewire.borrowers.borrowers-logs', compact('brfLogs'))->layout('layouts.app');
    }
}
