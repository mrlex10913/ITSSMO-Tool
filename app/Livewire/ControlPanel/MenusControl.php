<?php

namespace App\Livewire\ControlPanel;

use App\Models\Menu;
use App\Models\Roles;
use App\Services\Menu\MenuBuilder;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class MenusControl extends Component
{
    use WithPagination;

    public string $search = '';

    public int $perPage = 10;

    /** @var array<int> */
    public array $selected = [];

    // Form fields
    public ?int $menuId = null;

    public string $label = '';

    public ?string $route = '';

    public ?string $url = '';

    public ?string $section = '';

    public ?string $icon = '';

    public int $sort_order = 0;

    public bool $is_active = true;

    /** @var array<int> */
    public array $role_ids = [];

    public bool $showCreate = false;

    public bool $showEdit = false;

    /** @var array<int> */
    public array $bulk_role_ids = [];

    /** @var array<int> */
    public array $bulk_user_ids = [];

    public ?string $departmentFilter = null; // 'pamo','bfo','itss', etc.

    public bool $groupByRole = false; // toggle grouped view

    public function mount(): void
    {
        $this->ensureAuthorized();
    }

    protected function rules(): array
    {
        return [
            'label' => 'required|string|max:255',
            // Allow either route or url (at least one required)
            'route' => 'nullable|string|max:255|required_without:url',
            'url' => 'nullable|url|max:2048|required_without:route',
            'section' => 'nullable|string|max:100',
            'icon' => 'nullable|string|max:64',
            'sort_order' => 'nullable|integer|min:0|max:65535',
            'is_active' => 'boolean',
            'role_ids' => 'array',
            'role_ids.*' => 'exists:roles,id',
        ];
    }

    public function updatedLabel(): void
    {
        // no-op: keep explicit
    }

    public function openCreate(): void
    {
        $this->resetForm();
        $this->resetErrorBag();
        $this->showCreate = true;
    }

    public function create(): void
    {
        $this->ensureAuthorized();
        $data = $this->validate();

        $menu = Menu::create([
            'label' => $data['label'],
            'route' => $data['route'] ?? null,
            'url' => $data['url'] ?? null,
            'section' => $data['section'] ?? null,
            'icon' => $data['icon'] ?? null,
            'sort_order' => $data['sort_order'] ?? 0,
            'is_active' => (bool) ($data['is_active'] ?? true),
        ]);
        $menu->roles()->sync($data['role_ids'] ?? []);

        // Clear caches for roles attached to this menu
        $this->clearMenuCachesForRoleIds($menu->roles()->pluck('roles.id')->all());

        $this->dispatch('success', message: 'Menu created.');
        $this->showCreate = false;
        $this->resetForm();
    }

    public function openEdit(int $id): void
    {
        $this->ensureAuthorized();
        $menu = Menu::findOrFail($id);
        $this->menuId = $menu->id;
        $this->label = $menu->label;
        $this->route = $menu->route;
        $this->url = $menu->url;
        $this->section = $menu->section;
        $this->icon = $menu->icon;
        $this->sort_order = (int) $menu->sort_order;
        $this->is_active = (bool) $menu->is_active;
        $this->role_ids = $menu->roles()->pluck('roles.id')->toArray();
        $this->resetErrorBag();
        $this->showEdit = true;
    }

    public function update(): void
    {
        $this->ensureAuthorized();
        $this->validate();
        $menu = Menu::findOrFail($this->menuId);

        $menu->update([
            'label' => $this->label,
            'route' => $this->route ?: null,
            'url' => $this->url ?: null,
            'section' => $this->section ?: null,
            'icon' => $this->icon ?: null,
            'sort_order' => (int) $this->sort_order,
            'is_active' => (bool) $this->is_active,
        ]);
        $menu->roles()->sync($this->role_ids ?? []);

        // Clear caches for roles attached to this menu
        $this->clearMenuCachesForRoleIds($menu->roles()->pluck('roles.id')->all());

        $this->dispatch('success', message: 'Menu updated.');
        $this->showEdit = false;
        $this->resetForm();
    }

    public function updateSort(int $id, int $order): void
    {
        $menu = Menu::find($id);
        if (! $menu) {
            return;
        }
        $menu->update(['sort_order' => max(0, $order)]);
        $this->clearMenuCachesForMenuId($id);
        $this->dispatch('success', message: 'Sort updated');
    }

    public function delete(int $id): void
    {
        $this->ensureAuthorized();
        $menu = Menu::findOrFail($id);
        $roleIds = $menu->roles()->pluck('roles.id')->all();
        $menu->delete();
        $this->dispatch('success', message: 'Menu deleted.');
        $this->clearMenuCachesForRoleIds($roleIds);
    }

    public function bulkSetActive(bool $state): void
    {
        $this->ensureAuthorized();
        if (empty($this->selected)) {
            return;
        }
        $ids = $this->selected;
        Menu::query()->whereIn('id', $ids)->update(['is_active' => $state ? 1 : 0]);
        $this->clearMenuCachesForMenuIds($ids);
        $this->selected = [];
    }

    public function bulkAssignRoles(): void
    {
        $this->ensureAuthorized();
        if (empty($this->selected) || empty($this->bulk_role_ids)) {
            return;
        }
        $menus = Menu::query()->whereIn('id', $this->selected)->get();
        foreach ($menus as $menu) {
            $current = $menu->roles()->pluck('roles.id')->toArray();
            $menu->roles()->sync(array_values(array_unique(array_merge($current, $this->bulk_role_ids))));
        }
        $this->clearMenuCachesForMenuIds($this->selected);
        $this->selected = [];
        $this->bulk_role_ids = [];
        $this->dispatch('success', message: 'Roles assigned.');
    }

    public function bulkRemoveRoles(): void
    {
        $this->ensureAuthorized();
        if (empty($this->selected) || empty($this->bulk_role_ids)) {
            return;
        }
        $menus = Menu::query()->whereIn('id', $this->selected)->get();
        foreach ($menus as $menu) {
            $menu->roles()->detach($this->bulk_role_ids);
        }
        $this->clearMenuCachesForMenuIds($this->selected);
        $this->selected = [];
        $this->bulk_role_ids = [];
        $this->dispatch('success', message: 'Roles removed.');
    }

    public function bulkAssignUsers(): void
    {
        $this->ensureAuthorized();
        if (empty($this->selected) || empty($this->bulk_user_ids)) {
            return;
        }
        $menus = Menu::query()->whereIn('id', $this->selected)->get();
        foreach ($menus as $menu) {
            $current = $menu->users()->pluck('users.id')->toArray();
            $menu->users()->sync(array_values(array_unique(array_merge($current, $this->bulk_user_ids))));
        }
        $this->clearMenuCachesForMenuIds($this->selected);
        $this->selected = [];
        $this->bulk_user_ids = [];
        $this->dispatch('success', message: 'Users assigned.');
    }

    public function bulkRemoveUsers(): void
    {
        $this->ensureAuthorized();
        if (empty($this->selected) || empty($this->bulk_user_ids)) {
            return;
        }
        $menus = Menu::query()->whereIn('id', $this->selected)->get();
        foreach ($menus as $menu) {
            $menu->users()->detach($this->bulk_user_ids);
        }
        $this->clearMenuCachesForMenuIds($this->selected);
        $this->selected = [];
        $this->bulk_user_ids = [];
        $this->dispatch('success', message: 'Users removed.');
    }

    public function setIcon(string $icon): void
    {
        $this->icon = $icon;
    }

    public function reorder(array $orderedIds): void
    {
        $this->ensureAuthorized();
        $ids = array_values(array_unique(array_map('intval', $orderedIds)));
        if (empty($ids)) {
            return;
        }
        $menus = Menu::query()->whereIn('id', $ids)->get()->keyBy('id');
        $n = 0;
        foreach ($ids as $id) {
            if (isset($menus[$id])) {
                $menus[$id]->update(['sort_order' => $n]);
                $n += 10;
            }
        }
        $this->clearMenuCachesForMenuIds($ids);
        $this->dispatch('success', message: 'Order updated');
    }

    protected function resetForm(): void
    {
        $this->menuId = null;
        $this->label = '';
        $this->route = '';
        $this->url = '';
        $this->section = '';
        $this->icon = '';
        $this->sort_order = 0;
        $this->is_active = true;
        $this->role_ids = [];
        $this->selected = [];
    }

    protected function clearMenuCachesForMenuId(int $menuId): void
    {
        $slugs = Menu::query()
            ->whereKey($menuId)
            ->with('roles:id,slug')
            ->first()?->roles->pluck('slug')->unique()->all() ?? [];
        foreach ($slugs as $slug) {
            app(MenuBuilder::class)->clearMenuCacheForRoleSlug($slug);
        }
    }

    protected function clearMenuCachesForMenuIds(array $menuIds): void
    {
        $slugs = Menu::query()
            ->whereIn('id', $menuIds)
            ->with('roles:id,slug')
            ->get()
            ->pluck('roles')
            ->flatten()
            ->pluck('slug')
            ->unique()
            ->all();
        foreach ($slugs as $slug) {
            app(MenuBuilder::class)->clearMenuCacheForRoleSlug($slug);
        }
    }

    protected function clearMenuCachesForRoleIds(array $roleIds): void
    {
        $slugs = Roles::query()->whereIn('id', $roleIds)->pluck('slug')->all();
        foreach ($slugs as $slug) {
            app(MenuBuilder::class)->clearMenuCacheForRoleSlug($slug);
        }
    }

    protected function ensureAuthorized(): void
    {
        $user = Auth::user();
        if (! $user) {
            abort(403);
        }
        $roleSlug = strtolower((string) optional(Roles::find($user->role_id))->slug);
        $allowed = in_array($roleSlug, ['administrator', 'developer', 'itss', 'pamo', 'bfo']);
        if (! $allowed) {
            abort(403);
        }
    }

    // Selection helpers
    public function getAreAllPageSelectedProperty(): bool
    {
        $pageIds = $this->menus->pluck('id')->map(fn ($i) => (int) $i)->all();
        if (empty($pageIds)) {
            return false;
        }

        return count(array_diff($pageIds, $this->selected)) === 0;
    }

    public function getSelectedCountProperty(): int
    {
        return count($this->selected);
    }

    public function toggleSelectPage(): void
    {
        $pageIds = $this->menus->pluck('id')->map(fn ($i) => (int) $i)->all();
        if ($this->areAllPageSelected) {
            // Unselect all on this page
            $this->selected = array_values(array_diff($this->selected, $pageIds));
        } else {
            // Select all on this page
            $this->selected = array_values(array_unique(array_merge($this->selected, $pageIds)));
        }
    }

    public function clearSelection(): void
    {
        $this->selected = [];
    }

    public function render()
    {
        return view('livewire.control-panel.menus-control');
    }

    public function getMenusProperty()
    {
        $query = Menu::query()
            ->with(['roles:id,name'])
            ->withCount(['users', 'roles'])
            ->when($this->search, function ($q) {
                $search = '%'.$this->search.'%';
                $q->where(function ($qq) use ($search) {
                    $qq->where('label', 'like', $search)
                        ->orWhere('route', 'like', $search)
                        ->orWhere('url', 'like', $search);
                });
            })
            ->when($this->departmentFilter, function ($q) {
                $slug = strtolower((string) $this->departmentFilter);
                $q->whereHas('roles', function ($r) use ($slug) {
                    $r->whereRaw('LOWER(roles.slug) = ?', [$slug]);
                });
            })
            ->orderBy('sort_order')
            ->orderBy('label');

        return $query->paginate($this->perPage);
    }

    public function getGroupedMenusByRoleProperty(): array
    {
        // Build a non-paginated list filtered the same way, then group by role slug and section
        $base = Menu::query()
            ->with(['roles:id,name,slug'])
            ->when($this->search, function ($q) {
                $search = '%'.$this->search.'%';
                $q->where(function ($qq) use ($search) {
                    $qq->where('label', 'like', $search)
                        ->orWhere('route', 'like', $search)
                        ->orWhere('url', 'like', $search);
                });
            })
            ->when($this->departmentFilter, function ($q) {
                $slug = strtolower((string) $this->departmentFilter);
                $q->whereHas('roles', function ($r) use ($slug) {
                    $r->whereRaw('LOWER(roles.slug) = ?', [$slug]);
                });
            })
            ->orderBy('sort_order')
            ->orderBy('label')
            ->get();

        $groups = [];
        foreach ($base as $m) {
            foreach ($m->roles as $role) {
                $slug = strtolower($role->slug);
                if (! isset($groups[$slug])) {
                    $groups[$slug] = [
                        'name' => $role->name,
                        'sections' => [],
                    ];
                }
                $section = $m->section ?: '';
                $groups[$slug]['sections'][$section][] = [
                    'id' => $m->id,
                    'label' => $m->label,
                    'route' => $m->route,
                    'url' => $m->url,
                    'icon' => $m->icon,
                    'sort_order' => (int) $m->sort_order,
                    'is_active' => (bool) $m->is_active,
                ];
            }
        }

        // Ensure predictable section order
        foreach ($groups as &$g) {
            ksort($g['sections']);
        }

        return $groups;
    }

    public function getRolesProperty()
    {
        return Roles::orderBy('name')->get(['id', 'name']);
    }

    public function getUsersProperty()
    {
        return \App\Models\User::orderBy('name')->limit(100)->get(['id', 'name', 'email']);
    }

    // Reset pagination when filters change
    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedDepartmentFilter(): void
    {
        $this->resetPage();
    }

    public function updatedPerPage(): void
    {
        $this->resetPage();
    }
}
