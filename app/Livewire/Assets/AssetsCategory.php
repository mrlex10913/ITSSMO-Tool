<?php

namespace App\Livewire\Assets;

use App\Models\Assets\AssetCategory;
use Livewire\Component;

class AssetsCategory extends Component
{
    public $NewCategory = false;

    public $category_name;

    public function saveCategory(){
        $this->validate([
            'category_name' => 'required|string'
        ],[
            'category_name.required' => 'Please fill in the empty field'
        ]);

        AssetCategory::create([
            'name' => $this->category_name
        ]);
        $this->dispatch('success', ['message' => 'Category created successfully']);
        // back()->with('message', 'Category created successfullt');
        // session()->flash('message', 'Category created successfully');

        $this->reset('category_name');
        $this->NewCategory = false;
    }

    public function createNewCategory(){
        $this->NewCategory = true;
    }
    public function render()
    {
        return view('livewire.assets.assets-category')->layout('layouts.app');
    }
}
