<?php

namespace App\Livewire\Assets;

use Livewire\Component;

class AssetsLists extends Component
{
    public $NewAssets = false;



    public function createNewAssets(){
        $this->NewAssets = true;
    }
    public function render()
    {
        return view('livewire.assets.assets-lists')->layout('layouts.app');
    }
}
