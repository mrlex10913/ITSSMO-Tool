<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Assets\AssetList;
use App\Models\Borrowers\BorrowerDetails;
use App\Models\UserRecords\FalcoData;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

use function Laravel\Prompts\error;

class BorrowersDesktop extends Component
{
    public $id_number;
    public $contact_email;
    public $rfid;
    public $doc_tracker;
    public $brf_name;
    public $brf_contact;
    public $brf_department;
    public $brf_authorizedby;
    public $brf_dateborrowed;
    public $brf_datereturn;
    public $brf_location;
    public $brf_event;
    public $brf_receivedby;
    public $brf_status;
    public $brf_releasedcheckedby;
    public $brf_notedby;

    public $nextField = false;

    public $verfiedPersonel;

    public $editMode = false;

    public $items = [];

    public $availableAssets;
    public $availableSerials = [];

    public $initialReceivedbySet = false;

    public $scanned;


    public $showOverlay = true;

    public function addScannedItem($serial)
    {
        // Query the database to find the item by serial number
        $asset = AssetList::with('assetList')
        ->where('item_barcode', $serial)->first();

        if ($asset) {
            $this->items[] = [
                'name' => $asset->assetList->name,
                'categoryId' => $asset->asset_categories_id,
                'serial' => $asset->item_serial_itss,
                'brand' => $asset->item_name,
                'remarks' => ''
            ];
            $this->scanned = '';
        } else {
            session()->flash('error', 'Item not found');
        }
    }
    public function mount(){

        // $this->availableAssets = AssetList::with('assetList')->where('status', 'Available')->get()->unique('asset_categories_id');
        // if(empty($this->items)){
        //     $this->items[] = [
        //         'name' => '',
        //         'brand' => '',
        //         'serial' => '',
        //         'remarks' => '',
        //     ];
        // }

        $latestTracker = BorrowerDetails::latest('id')->value('doc_tracker');

        if($latestTracker){
            $number = (int) substr($latestTracker, -4);
            $newNumber = str_pad($number + 1, 4, '0', STR_PAD_LEFT);
            $this->doc_tracker = 'BRF-ITSS' . $newNumber;
        }else{
            $this->doc_tracker = 'BRF-ITSS0001';
        }
        $this->brf_notedby = 'Beau Villanueva';
        $this->brf_status = 'Borrowed';
        $this->brf_dateborrowed = Carbon::now()->format('Y-m-d');
    }

    public function updatedRfID($value)
    {
        $this->rfid = ltrim($value, '0');
    }
    public function updatedIdNumber($value)
    {
        // Remove leading zeros
        $this->id_number = ltrim($value, '0');

        $falcoRecord = FalcoData::where('card_no', $this->id_number)->first();

        if($falcoRecord){
            $this->rfid = $falcoRecord->card_no;
            $this->id_number = $falcoRecord->id_number;
            $this->brf_name = $falcoRecord->name;
            $this->brf_contact = $falcoRecord->contact;
            $this->contact_email = $falcoRecord->email;
            $this->brf_department = $falcoRecord->department;
            $this->brf_receivedby = $falcoRecord->name;
        }else{
            $this->id_number = '';
            flash()->error('Card Number is not registered');
        }
    }

    public function updatedverfiedPersonel($value)
    {

        $this->verfiedPersonel = ltrim($value, '0');

        $veriFiedRecord = FalcoData::where('card_no', $this->verfiedPersonel)->first();


        if($veriFiedRecord){
            $this->brf_releasedcheckedby = $veriFiedRecord->name;
            $this->saveBorrowers();
        }else{
            $this->brf_releasedcheckedby = null;
        }
    }

    public function updatedItems($value, $name){
        if(strpos($name, '.name') !== false){
            $index = explode('.', $name)[0];

            $this->availableSerials[$index] = AssetList::with('assetList')->where('asset_categories_id', $value)
            ->where('status', 'Available')
            ->pluck('item_serial_itss')
            ->toArray();

        }
        if(strpos($name, '.serial') !== false)
        {
            $index = explode('.', $name)[0];

            $selectedSerial = $this->items[$index]['serial'];

            $asset = AssetList::where('item_serial_itss', $selectedSerial)->first();

            if($asset){
                $this->items[$index]['brand'] = $asset->item_name;
            }
        }
    }

    public function updatedBrfName($value){
        // Only set brf_receivedby if it has not been manually edited by the user
            if (!$this->initialReceivedbySet) {
                $this->brf_receivedby = $value;
            }
        }

    public function updatedBrfReceivedby(){
    // Once the user edits brf_receivedby, stop automatic updating
        $this->initialReceivedbySet = true;
    }


    public function saveBorrowers(){

        try{
            $this->validate([
            'id_number' => 'required',
            'brf_name' => 'required',
            'brf_contact' => 'required',
            'brf_department' => 'required',
            'brf_dateborrowed' => 'required',
            'brf_datereturn' => 'required',
            'brf_location' => 'required',
            'brf_event' => 'required',
            'brf_location' => 'required',
            'brf_location' => 'required',
            'items.*.name' => 'required',
            'brf_receivedby' => 'required',
            'brf_status' => 'required',
            'brf_releasedcheckedby' => 'required',
            'brf_notedby' => 'required',
            ]);
            $borrower = BorrowerDetails::create([
            'id_number' => $this->id_number,
            'doc_tracker' => $this->doc_tracker,
            'name' => $this->brf_name,
            'contact' => $this->brf_contact,
            'department' => $this->brf_department,
            'location' => $this->brf_location,
            'authorizedby' => $this->brf_authorizedby,
            'date_to_borrow' => $this->brf_dateborrowed,
            'date_to_return' => $this->brf_datereturn,
            'event' => $this->brf_event,
            'receivedby' => $this->brf_receivedby,
            'status' => $this->brf_status,
            'released_checkedby' => $this->brf_releasedcheckedby,
            'notedby' => $this->brf_notedby,
            ]);
            foreach ($this->items as $item){
            $borrower->itemBorrow()->create([
                'asset_category_id' => $item['categoryId'],
                'brand' => $item['brand'],
                'serial' => $item['serial'],
                'remarks' => $item['remarks'],
            ]);
            $asset = AssetList::where('item_serial_itss', $item['serial'])->first();
            if ($asset) {
                $asset->update(['status' => 'Borrowed']);
            }
            }
            $falcoRecord = FalcoData::where('card_no', $this->rfid)->first();
            if ($falcoRecord) {
            // Check if the department has changed
            if ($falcoRecord->department !== $this->brf_department || $falcoRecord->contact !== $this->brf_contact || $falcoRecord->email !== $this->contact_email || $falcoRecord->id_number !== $this->contact_email) {

                $falcoRecord->update([
                    'id_number' => $this->id_number,
                    'contact' => $this->brf_contact,
                    'department' => $this->brf_department,
                    'email' => $this->contact_email
                ]);
            }
        }else{
            FalcoData::create([
                'card_no' => $this->rfid,
                'id_number' => $this->id_number,
                'name' => $this->brf_name,
                'contact' => $this->brf_contact,
                'department' => $this->brf_department,
                'email' => $this->contact_email
            ]);
        }

            flash()->success('Borrower Transaction Success');
            $this->cleareFields();

            $latestTracker = BorrowerDetails::latest('id')->value('doc_tracker');
            if($latestTracker){
                $number = (int) substr($latestTracker, -4);
                $newNumber = str_pad($number + 1, 4, '0', STR_PAD_LEFT);
                $this->doc_tracker = 'BRF-ITSS' . $newNumber;
            }else{
                $this->doc_tracker = 'BRF-ITSS0001';
            }
        }catch(ValidationException $e){
            flash()->error('Ooops! Something went wrong');
            throw $e;
        }
    }

    public function addItem(){
        $this->items[] = [
            'name' => '',
            'brand' => '',
            'serial' => '',
            'remarks' => '',
        ];
        // if(count($this->items) > 6){

        // }
    }
    public function removeItem($index){
        unset($this->items[$index]);
        $this->items = array_values($this->items);
    }

    public function cleareFields(){
        $this->id_number = '';
        $this->brf_name = '';
        $this->brf_contact = '';
        $this->contact_email = '';
        $this->brf_department = '';
        $this->brf_authorizedby = '';
        $this->brf_dateborrowed = Carbon::now()->format('Y-m-d');
        $this->brf_datereturn = '';
        $this->brf_location = '';
        $this->brf_event = '';
        foreach ($this->items as $key => $item) {
            $this->items[$key]['name'] = '';
            $this->items[$key]['brand'] = '';
            $this->items[$key]['serial'] = '';
            $this->items[$key]['remarks'] = '';
        }
        $this->brf_receivedby = '';
    }

    public function render()
    {
        return view('livewire.borrowers-desktop')->layout('layouts.guest');
    }
}
