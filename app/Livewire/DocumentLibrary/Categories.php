<?php

namespace App\Livewire\DocumentLibrary;

use App\Models\DocumentLibrary\MasterFileCategory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.enduser')]
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
        'requires_approval' => 'boolean',
    ];

    public function mount()
    {
        // Only admins/developers can manage categories
        if (! Auth::user()->hasRole(['administrator', 'developer'])) {
            session()->flash('error', 'You do not have permission to manage categories.');

            return redirect()->route('document-library.dashboard');
        }

        $this->department = null; // Categories are now general, not department-specific
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

        // Check for duplicate name within the same department
        $existingQuery = MasterFileCategory::where('name', $this->name);
        if ($this->department) {
            $existingQuery->where('department', $this->department);
        } else {
            $existingQuery->where(function ($q) {
                $q->whereNull('department')->orWhere('department', '');
            });
        }
        if ($this->editingId) {
            $existingQuery->where('id', '!=', $this->editingId);
        }
        if ($existingQuery->exists()) {
            $this->addError('name', 'A category with this name already exists'.($this->department ? ' in '.$this->department : '').'.');

            return;
        }

        // Generate unique slug with department prefix to avoid collisions
        $slugBase = $this->department ? Str::slug($this->department.'-'.$this->name) : Str::slug($this->name);

        $data = [
            'name' => $this->name,
            'slug' => $slugBase,
            'description' => $this->description,
            'color' => $this->color,
            'icon' => $this->icon,
            'department' => $this->department ?: null,
            'allowed_departments' => $this->allowed_departments,
            'requires_approval' => $this->requires_approval,
            'is_active' => true,
            'created_by' => Auth::id(),
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
        $this->department = null; // Categories are general, not department-specific
        $this->allowed_departments = [];
        $this->requires_approval = false;
    }

    public function render()
    {
        $categories = MasterFileCategory::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('description', 'like', '%'.$this->search.'%');
            })
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.document-library.categories', compact('categories'));
    }
}
