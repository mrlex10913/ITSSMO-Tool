<?php

namespace App\Livewire\MasterFiles;

use App\Models\MasterFiles\MasterFileCategory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;

class Categories extends Component
{
    use WithPagination;

    public $showModal = false;
    public $editingId = null;

     // Form fields
    public $name = '';
    public $description = '';
    public $color = '#3B82F6';
    public $icon = 'folder';
    public $department = '';
    public $allowed_departments = [];
    public $requires_approval = false;

    public $search = '';

    protected $rules = [
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'color' => 'required|string',
        'icon' => 'required|string',
        'department' => 'nullable|string',
        'allowed_departments' => 'array',
        'requires_approval' => 'boolean'
    ];

    public function mount()
    {
        $this->department = Auth::user()->department ?? 'ITSS';
    }

    public function openModal()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->editingId = null;
        $this->resetForm();
    }

    public function save()
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'slug' => Str::slug($this->name),
            'description' => $this->description,
            'color' => $this->color,
            'icon' => $this->icon,
            'department' => $this->department,
            'allowed_departments' => $this->allowed_departments,
            'requires_approval' => $this->requires_approval,
            'created_by' => Auth::id()
        ];

        if ($this->editingId) {
            MasterFileCategory::find($this->editingId)->update($data);
            flash()->success('Category updated successfully');
        } else {
            MasterFileCategory::create($data);
            flash()->success('Category created successfully');
        }

        $this->closeModal();
    }

    public function edit($id)
    {
        $category = MasterFileCategory::findOrFail($id);

        $this->editingId = $id;
        $this->name = $category->name;
        $this->description = $category->description;
        $this->color = $category->color;
        $this->icon = $category->icon;
        $this->department = $category->department;
        $this->allowed_departments = $category->allowed_departments ?? [];
        $this->requires_approval = $category->requires_approval;

        $this->showModal = true;
    }

    public function delete($id)
    {
        $category = MasterFileCategory::findOrFail($id);

        if ($category->files()->count() > 0) {
            flash()->error('Cannot delete category with existing files');
            return;
        }

        $category->delete();
        flash()->success('Category deleted successfully');
    }

    private function resetForm()
    {
        $this->name = '';
        $this->description = '';
        $this->color = '#3B82F6';
        $this->icon = 'folder';
        $this->department = Auth::user()->department ?? 'ITSS';
        $this->allowed_departments = [];
        $this->requires_approval = false;
    }
    public function render()
    {
        $categories = MasterFileCategory::query()
            ->when($this->search, function($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%');
            })
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.master-files.categories', compact('categories'));
    }
}
