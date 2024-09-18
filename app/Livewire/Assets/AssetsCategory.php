<?php

namespace App\Livewire\Assets;

use App\Models\Assets\AssetCategory;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;

class AssetsCategory extends Component
{
    use WithPagination;
    public $NewCategory = false;

    public $editMode = false;
    public $editCategory;

    public $DeleteCategory = false;
    public $categoryTodelete;


    #[Validate('required|string')]
    public $category_name;

    public $search = '';


    public function createNewCategory(){
        $this->reset(['category_name']);
        $this->NewCategory = true;
    }

    public function saveCategory(){
        try{
            $this->validate();
            AssetCategory::create([
                'name' => $this->category_name
            ]);
            flash()->success('Category created');
            $this->reset('category_name');
            $this->NewCategory = false;
        }catch(ValidationException $e){
            flash()->error('Ooops! Something went wrong');
            throw $e;
        }
    }

    public function updateCategoryModal($categoryId){
        $this->editMode = true;
        $this->NewCategory = true;
        $this->editCategory = $categoryId;
        $category = AssetCategory::find($categoryId);
        $this->category_name = $category->name;

    }

    public function updateCategory(){
        try{
            $this->validate();
            $assets = AssetCategory::find($this->editCategory);
            $assets->name = $this->category_name;
            $assets->save();
            flash()->success('Category updated');
            $this->NewCategory = false;

        }catch(ValidationException $e){
            flash()->error('Oops! Something went wrong');
            throw $e;
        }
    }
    public function deleteCategoryModal($categoryId){
        $this->categoryTodelete = $categoryId;
        $this->DeleteCategory = true;
    }

    public function deleteCategory(){
        try{
            $category = AssetCategory::find($this->categoryTodelete);
            $category->delete();
            $this->DeleteCategory = false;
            $this->categoryTodelete = null;
            flash()->warning('Category has been deleted!');
        }catch(ValidationException $e){
            flash()->error('Oops! Something went wrong');
            throw $e;
        }

    }

    public function updatingSearch(){
        $this->resetPage();
    }


    public function render()
    {
        // $categories = AssetCategory::paginate(5);
        $categories = AssetCategory::when($this->search, function($query) {
            $query->where('name', 'like', '%' . $this->search . '%');
        })->paginate(5);
        return view('livewire.assets.assets-category', compact('categories'))->layout('layouts.app');
    }
}
