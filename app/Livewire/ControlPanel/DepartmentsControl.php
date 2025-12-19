<?php

namespace App\Livewire\ControlPanel;

use App\Models\Department;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class DepartmentsControl extends Component
{
    use WithPagination;

    public string $search = '';

    public int $perPage = 10;

    public ?int $deptId = null;

    public string $name = '';

    public string $slug = '';

    public int $sort_order = 0;

    public bool $is_active = true;

    public bool $is_guest_visible = true;

    public bool $showCreate = false;

    public bool $showEdit = false;

    protected function rules(): array
    {
        $id = $this->deptId;

        return [
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:departments,slug'.($id ? ','.$id : ''),
            'sort_order' => 'nullable|integer|min:0|max:65535',
            'is_active' => 'boolean',
            'is_guest_visible' => 'boolean',
        ];
    }

    protected function ensureAuthorized(): void
    {
        $user = Auth::user();
        if (! $user) {
            abort(403);
        }
        $role = optional(\App\Models\Roles::find($user->role_id))->slug;
        if (! in_array(strtolower((string) $role), ['administrator', 'developer'])) {
            abort(403);
        }
    }

    public function openCreate(): void
    {
        $this->resetForm();
        $this->showCreate = true;
    }

    public function create(): void
    {
        $this->ensureAuthorized();
        $data = $this->validate();
        Department::create($data);
        $this->dispatch('success', message: 'Department created.');
        $this->showCreate = false;
        $this->resetForm();
    }

    public function openEdit(int $id): void
    {
        $this->ensureAuthorized();
        $d = Department::findOrFail($id);
        $this->deptId = $d->id;
        $this->name = $d->name;
        $this->slug = $d->slug;
        $this->sort_order = (int) $d->sort_order;
        $this->is_active = (bool) $d->is_active;
        $this->is_guest_visible = (bool) $d->is_guest_visible;
        $this->showEdit = true;
    }

    public function update(): void
    {
        $this->ensureAuthorized();
        $this->validate();
        $d = Department::findOrFail($this->deptId);
        $d->update([
            'name' => $this->name,
            'slug' => $this->slug,
            'sort_order' => $this->sort_order,
            'is_active' => $this->is_active,
            'is_guest_visible' => $this->is_guest_visible,
        ]);
        $this->dispatch('success', message: 'Department updated.');
        $this->showEdit = false;
        $this->resetForm();
    }

    public function delete(int $id): void
    {
        $this->ensureAuthorized();
        Department::whereKey($id)->delete();
        $this->dispatch('success', message: 'Department deleted.');
    }

    public function getDepartmentsProperty()
    {
        return Department::query()
            ->when($this->search, function ($q) {
                $q->where(function ($qq) {
                    $qq->where('name', 'like', '%'.$this->search.'%')
                        ->orWhere('slug', 'like', '%'.$this->search.'%');
                });
            })
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate($this->perPage);
    }

    public function render()
    {
        return view('livewire.control-panel.departments-control');
    }

    protected function resetForm(): void
    {
        $this->reset(['deptId', 'name', 'slug', 'sort_order', 'is_active', 'is_guest_visible']);
    }
}
