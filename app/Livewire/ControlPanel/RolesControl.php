<?php

namespace App\Livewire\ControlPanel;

use App\Models\Roles;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class RolesControl extends Component
{
    use WithPagination;

    public string $search = '';

    public int $perPage = 10;

    // Form state
    public ?int $editingId = null;

    public string $name = '';

    public string $slug = '';

    public ?string $description = '';

    public ?string $home_route = null;

    public bool $is_default = false;

    public bool $showCreateModal = false;

    public bool $showEditModal = false;

    public ?int $confirmingDeleteId = null;

    public bool $showDeleteModal = false;

    protected function rules(): array
    {
        $id = $this->editingId;

        return [
            'name' => ['required', 'string', 'max:255', 'unique:roles,name'.($id ? ",{$id}" : '')],
            'slug' => ['required', 'string', 'max:255', 'unique:roles,slug'.($id ? ",{$id}" : '')],
            'description' => ['nullable', 'string', 'max:1000'],
            'home_route' => ['nullable', 'string', 'max:255'],
            'is_default' => ['boolean'],
        ];
    }

    public function updatedName(): void
    {
        if (! $this->editingId) {
            $this->slug = Str::slug($this->name);
        }
    }

    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->showCreateModal = true;
    }

    public function createRole(): void
    {
        $this->editingId = null;
        $validated = $this->validate();

        if ($validated['is_default'] ?? false) {
            Roles::query()->update(['is_default' => false]);
        }

        Roles::create([
            'name' => $validated['name'],
            'slug' => strtolower($validated['slug']),
            'description' => $validated['description'] ?? null,
            'home_route' => $validated['home_route'] ?: 'generic.dashboard',
            'is_default' => (bool) ($validated['is_default'] ?? false),
        ]);

        $this->showCreateModal = false;
        $this->resetForm();
        $this->dispatch('success', message: 'Role created');
    }

    public function openEditModal(int $id): void
    {
        $role = Roles::findOrFail($id);
        $this->editingId = $role->id;
        $this->name = (string) $role->name;
        $this->slug = (string) $role->slug;
        $this->description = $role->description;
        $this->home_route = $role->home_route ?: 'generic.dashboard';
        $this->is_default = (bool) $role->is_default;
        $this->showEditModal = true;
    }

    public function updateRole(): void
    {
        if (! $this->editingId) {
            return;
        }

        $validated = $this->validate();

        if ($validated['is_default'] ?? false) {
            Roles::query()->where('id', '!=', $this->editingId)->update(['is_default' => false]);
        }

        $role = Roles::findOrFail($this->editingId);
        $role->update([
            'name' => $validated['name'],
            'slug' => strtolower($validated['slug']),
            'description' => $validated['description'] ?? null,
            'home_route' => $validated['home_route'] ?: $role->home_route ?: 'generic.dashboard',
            'is_default' => (bool) ($validated['is_default'] ?? false),
        ]);

        $this->showEditModal = false;
        $this->resetForm();
        $this->dispatch('success', message: 'Role updated');
    }

    public function confirmDelete(int $id): void
    {
        $this->confirmingDeleteId = $id;
        $this->showDeleteModal = true;
    }

    public function deleteRoleConfirmed(): void
    {
        if (! $this->confirmingDeleteId) {
            $this->showDeleteModal = false;

            return;
        }

        $role = Roles::find($this->confirmingDeleteId);
        if (! $role) {
            $this->showDeleteModal = false;
            $this->dispatch('error', message: 'Role not found');

            return;
        }

        // Prevent deleting protected roles
        $slug = strtolower((string) $role->slug);
        if (in_array($slug, ['administrator', 'developer'], true)) {
            $this->showDeleteModal = false;
            $this->dispatch('error', message: 'Protected role cannot be deleted');

            return;
        }

        // Prevent deleting roles with assigned users
        if ($role->users()->exists()) {
            $this->showDeleteModal = false;
            $this->dispatch('error', message: 'Role is assigned to users and cannot be deleted');

            return;
        }

        $role->delete();
        $this->showDeleteModal = false;
        $this->confirmingDeleteId = null;
        $this->dispatch('success', message: 'Role deleted');
    }

    public function resetForm(): void
    {
        $this->editingId = null;
        $this->name = '';
        $this->slug = '';
        $this->description = '';
        $this->home_route = null;
        $this->is_default = false;
        $this->resetValidation();
    }

    public function render()
    {
        $query = Roles::query()
            ->when($this->search, function ($q) {
                $term = '%'.strtolower($this->search).'%';
                $q->whereRaw('LOWER(name) LIKE ?', [$term])
                    ->orWhereRaw('LOWER(slug) LIKE ?', [$term]);
            })
            ->orderBy('name');

        return view('livewire.control-panel.roles-control', [
            'roles' => $query->paginate($this->perPage),
        ]);
    }
}
