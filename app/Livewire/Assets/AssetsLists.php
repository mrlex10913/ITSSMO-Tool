<?php

namespace App\Livewire\Assets;

use App\Models\Assets\AssetCategory;
use App\Models\Assets\AssetList;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\Attributes\Validate;
use Livewire\WithPagination;
use Picqer\Barcode\BarcodeGeneratorPNG;

class AssetsLists extends Component
{
    use WithPagination;

    public $NewAssets = false;
    public $editMode = false;
    public $editAsset;
    public $deleteAsset = false;
    public $assetToDelete;

    public $generatedBarcodeImage;

    #[Validate('required|string')]
    public $category;

    public $item_brand;
    #[Validate('required|string')]
    public $item_barcode;
    public $item_model;
    #[Validate('required|string')]
    public $itss_serial;

    public $purch_serial;
    #[Validate('required|string')]
    public $location;
    #[Validate('required|string')]
    public $status;
    #[Validate('required|string')]
    public $assign_to;

    public $specification;
    public $search = '';


    public function mount(){
        $this->assign_to = Auth::user()->name;
    }
    public function createNewAssets(){
        $this->NewAssets = true;
        $this->resetField();
    }
    public function resetField(){
        $this->category = '';
        $this->item_brand = '';
        $this->item_model = '';
        $this->itss_serial = '';
        $this->purch_serial = '';
        $this->specification = '';
        $this->location = '';
        $this->status = '';
    }
    public function saveAsset(){
        try{
            $this->validate();
            AssetList::create([
                'asset_categories_id' => $this->category,
                'item_barcode' => $this->item_barcode,
                'item_name' => $this->item_brand,
                'item_model' => $this->item_model,
                'item_serial_itss' => $this->itss_serial,
                'item_serial_purch' => $this->purch_serial,
                'assigned_to' => $this->assign_to,
                'location' => $this->location,
                'status' => $this->status,
                'specification' => $this->specification
            ]);
            flash()->success('New Asset created');
            $this->reset(['category', 'item_brand', 'item_model', 'itss_serial','purch_serial', 'location', 'status', 'specification', 'item_barcode']);
            $this->NewAssets = false;
        }catch(ValidationException $e){
            flash()->error('Ooops! Something went wrong');
            throw $e;
        }

    }

    public function updateAssetId($assetId){
        $this->NewAssets = true;
        $this->editMode = true;
        $this->editAsset = $assetId;
        $assetsFind = AssetList::find($assetId);
        $this->category = $assetsFind->asset_categories_id;
        $this->item_barcode = $assetsFind->item_barcode;
        $this->item_brand = $assetsFind->item_name;
        $this->item_model = $assetsFind->item_model;
        $this->itss_serial = $assetsFind->item_serial_itss;
        $this->purch_serial = $assetsFind->item_serial_purch;
        $this->assign_to = $assetsFind->assigned_to;
        $this->location = $assetsFind->location;
        $this->status = $assetsFind->status;
        $this->specification = $assetsFind->specification;

    }
    public function updateAsset(){
        try{
            // $this->validate();
            $assets = AssetList::find($this->editAsset);
            // dd($assets);
            $assets->asset_categories_id = $this->category;
            $assets->item_barcode = $this->item_barcode;
            $assets->item_name = $this->item_brand;
            $assets->item_model = $this->item_model;
            $assets->item_serial_itss = $this->itss_serial;
            $assets->item_serial_purch = $this->purch_serial;
            $assets->assigned_to = $this->assign_to;
            $assets->location = $this->location;
            $assets->status = $this->status;
            $assets->specification = $this->specification;
            $assets->save();
            flash()->success('Assets updated');
            $this->NewAssets = false;
        }catch(ValidationException $e){
            flash()->error('Oops! Something went wrong');
            throw $e;
        }
    }

    public function deleteAssetId($assetId){
        $this->assetToDelete = $assetId;
        $this->deleteAsset = true;
    }
    public function deleteToAsset(){
        try{
            $assets = AssetList::find($this->assetToDelete);
            $assets->delete();
            $this->deleteAsset = false;
            $this->assetToDelete = null;
            flash()->warning('Asset has been deleted!');
        }catch(ValidationException $e){
            flash()->error('Oops! Something went wrong');
            throw $e;
        }
    }
    public function render()
    {

        $assets = AssetList::when($this->search, function($query) {
            $query->where('item_barcode', 'like', '%' . $this->search . '%')
            ->orWhere('item_name', 'like', '%' . $this->search . '%');
        })
        ->orderBy('created_at', 'desc')
        ->where('asset_categories_id', '!=', 9)
        ->orWhere('asset_categories_id', '!=', 12)
        ->paginate(10);
        // $assets = AssetList::with('assetList')->orderBy('created_at', 'desc')
        // ->where('asset_categories_id', '!=', 21)
        // ->paginate(10);

        // This Query will use when transfer to server
        // $assets = AssetList::with('assetList')->orderBy('created_at', 'desc')
        // ->where('asset_categories_id', '!=', 9)
        // ->orWhere('asset_categories_id', '!=', 12)
        // ->get();

        $categoryOption = AssetCategory::all();
        return view('livewire.assets.assets-lists', compact('categoryOption','assets'))->layout('layouts.app');
    }
}
