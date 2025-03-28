<?php

namespace App\Livewire\Assets;

use App\Models\Assets\AssetCategory;
use App\Models\Assets\AssetList;
use Livewire\Attributes\Validate;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class AssetsConsumable extends Component
{
    public $NewConsumable = false;
    public $categoryConsumable;

    #[Validate('required|string')]
    public $item_name;

    public $item_brand;

    #[Validate('required|string')]
    public $specification;

    #[Validate('required')]
    public $quantity;
    #[Validate('required|string')]
    public $assigned_to;

    public function consumableModal(){
        $this->reset([
            'item_name',
            'item_brand',
            'quantity',
            'specification',
            'assigned_to',
        ]);
        $this->NewConsumable = true;
    }
    public function saveConsumable(){
        try{
            $this->validate();
            AssetList::create([
                'asset_categories_id' => 21,
                'item_name' => $this->item_name,
                'item_model' => $this->item_brand,
                'specification' => $this->specification,
                'quantity' => $this->quantity,
                'assigned_to' => $this->assigned_to,
            ]);
            flash()->success('Consumable added');
            $this->reset([
                'item_name',
                'item_brand',
                'quantity',
                'assigned_to',
            ]);
            $this->NewConsumable = false;
        }catch(ValidationException $e){
            flash()->error('Ooops! Something went wrong');
            throw $e;
        }
    }
    public function render()
    {
        $assetConsumable = AssetList::with('assetList')
        ->orderBy('created_at', 'desc')
        ->where('asset_categories_id', '=', 21)
        ->get();
        return view('livewire.assets.assets-consumable', compact('assetConsumable'));
    }
}
