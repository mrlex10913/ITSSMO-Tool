<?php

namespace App\Livewire\ControlPanel;

use App\Models\Department;
use App\Models\Menu;
use App\Models\Roles;
use App\Models\User;
use App\Services\Menu\MenuBuilder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class UsersControl extends Component
{
    use WithFileUploads;
    use WithPagination;

    // User properties
    public $id_number;

    public $name;

    public $email;

    public $department;

    public $role_id;

    public $password;

    public $temporaryPassword;

    public $updateAccessID;

    // Per-user menu management
    /** @var array<int> */
    public $selectedMenuIds = [];

    /** @var array<int> */
    public $roleMenuIds = [];

    // UI control
    public $openSpecificUserModal = false;

    public $NewUserAccessModal = false;

    public $deleteConfirmationModal = false;

    /** @var ?User */
    public $userToDelete;

    // Search and filter
    public $search = '';

    public $roleFilter = '';

    public $perPage = 12;

    public $profile_image;

    public $temporaryProfileImage;

    // Department options (loaded from DB)
    public $departments = [];

    protected function rules()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'role_id' => 'required|exists:roles,id',
            'temporaryPassword' => 'nullable|string',
            'id_number' => 'required|string',
            'temporaryProfileImage' => 'nullable|image|max:1024', // Max 1MB
        ];

        // Only validate email uniqueness when creating a new user or changing email
        if (! $this->updateAccessID) {
            $rules['email'] = 'required|string|email|max:255|unique:users';
            $rules['temporaryPassword'] = 'required|string|min:8';
        } else {
            $rules['email'] = 'required|string|email|max:255|unique:users,email,'.$this->updateAccessID;
        }

        return $rules;
    }

    protected $messages = [
        'name.required' => 'Full name is required',
        'email.required' => 'Email address is required',
        'email.email' => 'Please enter a valid email',
        'email.unique' => 'This email is already registered',
        'department.required' => 'Department is required',
        'role_id.required' => 'User role is required',
        'role_id.exists' => 'Selected role is invalid',
        'id_number.required' => 'ID number is required',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingRoleFilter()
    {
        $this->resetPage();
    }

    public function openSpecificUserAccessModal($userId)
    {
        $this->openSpecificUserModal = true;
        $userFind = User::find($userId);

        if (! $userFind) {
            flash()->error('User not found');

            return;
        }

        $this->updateAccessID = $userId;
        $this->email = $userFind->email;
        $this->name = $userFind->name;

        if ($userFind->temporary_password == null) {
            $this->temporaryPassword = 'Password Already Changed';
        } else {
            $this->temporaryPassword = $userFind->temporary_password;
        }

        $this->department = $userFind->department;
        $this->id_number = $userFind->id_number;
        $this->role_id = $userFind->role_id;
        // Store the current profile photo path
        $this->profile_image = $userFind->profile_photo_path;

        // Load role menus and user-specific overrides
        $this->roleMenuIds = Menu::query()
            ->active()
            ->whereHas('roles', function ($q) use ($userFind) {
                $q->where('roles.id', $userFind->role_id);
            })
            ->orderBy('sort_order')
            ->orderBy('label')
            ->pluck('id')
            ->map(fn ($i) => (int) $i)
            ->all();

        $userMenuIds = $userFind->menus()->pluck('menus.id')->map(fn ($i) => (int) $i)->all();
        $this->selectedMenuIds = ! empty($userMenuIds) ? $userMenuIds : $this->roleMenuIds;
    }

    public function updateSpecificUserAccessModal()
    {
        try {
            $this->validate();

            $updateAccess = User::find($this->updateAccessID);
            if (! $updateAccess) {
                flash()->error('User not found');

                return;
            }

            $updateAccess->name = $this->name;
            $updateAccess->email = $this->email;
            $updateAccess->department = $this->department;
            $updateAccess->role_id = $this->role_id;
            $updateAccess->id_number = $this->id_number;

            // Handle image upload if provided
            if ($this->temporaryProfileImage) {
                // Delete old image if exists
                if ($updateAccess->profile_photo_path && Storage::disk('public')->exists($updateAccess->profile_photo_path)) {
                    Storage::disk('public')->delete($updateAccess->profile_photo_path);
                }

                $updateAccess->profile_photo_path = $this->temporaryProfileImage->store('profile-photos', 'public');
            }

            // Only update password if it's been reset
            if ($this->temporaryPassword && $this->temporaryPassword !== 'Password Already Changed') {
                $updateAccess->password = Hash::make($this->temporaryPassword);
                $updateAccess->temporary_password = $this->temporaryPassword;
                $updateAccess->is_temporary_password_used = false;
            }

            $updateAccess->save();

            // Persist per-user menu access
            $ids = array_map('intval', $this->selectedMenuIds ?? []);
            $updateAccess->menus()->sync($ids);
            app(MenuBuilder::class)->clearMenuCacheForUserId($updateAccess->id);

            flash()->success('User account has been updated successfully');
            $this->openSpecificUserModal = false;
            $this->resetForm();

        } catch (ValidationException $e) {
            flash()->error('Please check the form for errors');
            throw $e;
        }
    }

    public function generateTemporaryPassword()
    {
        // Set default temporary password per request
        $this->temporaryPassword = '#AimHigh';
    }

    public function OpenNewUserAccessModal()
    {
        $this->resetForm();
        $this->generateTemporaryPassword();
        $this->NewUserAccessModal = true;
    }

    public function confirmDelete($userId)
    {
        $this->userToDelete = User::find($userId);

        if (! $this->userToDelete) {
            flash()->error('User not found');

            return;
        }

        $this->deleteConfirmationModal = true;
    }

    public function deleteUser()
    {
        try {
            if (! $this->userToDelete) {
                flash()->error('User not found');

                return;
            }

            // Prevent deleting yourself
            if (($this->userToDelete->id ?? null) === (Auth::user()?->id)) {
                flash()->error('You cannot delete your own account');

                return;
            }

            $userName = $this->userToDelete->name;
            $this->userToDelete->delete();

            flash()->success("User '{$userName}' has been deleted successfully");
            $this->deleteConfirmationModal = false;
            $this->userToDelete = null;

        } catch (\Exception $e) {
            flash()->error('An error occurred while deleting the user');
        }
    }

    public function saveUser()
    {
        try {
            $this->validate();

            $userData = [
                'id_number' => $this->id_number,
                'name' => $this->name,
                'email' => $this->email,
                'department' => $this->department,
                'role_id' => $this->role_id,
                'password' => Hash::make($this->temporaryPassword),
                'temporary_password' => $this->temporaryPassword,
                'is_temporary_password_used' => false,
            ];

            // Handle image upload if provided
            if ($this->temporaryProfileImage) {
                $userData['profile_photo_path'] = $this->temporaryProfileImage->store('profile-photos', 'public');
            }

            User::create($userData);

            flash()->success('New user has been created successfully');
            $this->NewUserAccessModal = false;
            $this->resetForm();

        } catch (ValidationException $e) {
            flash()->error('Please check the form for errors');
            throw $e;
        }
    }

    public function breadCrumbUsersPanel()
    {
        session(['breadcrumb' => 'User Management']);
    }

    public function resetForm()
    {
        $this->reset([
            'id_number',
            'name',
            'email',
            'department',
            'role_id',
            'updateAccessID',
            'temporaryPassword',
            'temporaryProfileImage',
            'selectedMenuIds',
            'roleMenuIds',
        ]);
    }

    /**
     * Computed list of menus available to the user's role for the edit modal.
     */
    public function getRoleMenusProperty()
    {
        $roleId = $this->role_id ?: (User::find($this->updateAccessID)?->role_id ?? null);
        if (! $roleId) {
            return collect();
        }

        return Menu::query()
            ->active()
            ->whereHas('roles', function ($q) use ($roleId) {
                $q->where('roles.id', $roleId);
            })
            ->orderBy('sort_order')
            ->orderBy('label')
            ->get(['id', 'label', 'section', 'icon', 'route', 'url']);
    }

    /** Save only the menu access without closing modal. */
    public function saveUserMenus(): void
    {
        if (! $this->updateAccessID) {
            return;
        }
        $user = User::find($this->updateAccessID);
        if (! $user) {
            return;
        }
        $ids = array_map('intval', $this->selectedMenuIds ?? []);
        $user->menus()->sync($ids);
        app(MenuBuilder::class)->clearMenuCacheForUserId($user->id);
        flash()->success('Menu access updated.');
    }

    /** Reset to role defaults (clears user-specific overrides). */
    public function resetUserMenusToRoleDefaults(): void
    {
        if (! $this->updateAccessID) {
            return;
        }
        $user = User::find($this->updateAccessID);
        if (! $user) {
            return;
        }
        $user->menus()->detach();
        app(MenuBuilder::class)->clearMenuCacheForUserId($user->id);
        // Reflect defaults in UI immediately
        $this->roleMenuIds = Menu::query()
            ->active()
            ->whereHas('roles', function ($q) use ($user) {
                $q->where('roles.id', $user->role_id);
            })
            ->orderBy('sort_order')
            ->orderBy('label')
            ->pluck('id')
            ->map(fn ($i) => (int) $i)
            ->all();
        $this->selectedMenuIds = $this->roleMenuIds;
        flash()->success('Menu access reset to role defaults.');
    }

    /** Select all menus available to the user's role. */
    public function selectAllRoleMenus(): void
    {
        $this->selectedMenuIds = $this->roleMenuIds ?? [];
    }

    /** Deselect all menus. */
    public function deselectAllMenus(): void
    {
        $this->selectedMenuIds = [];
    }

    public function render()
    {
        // Load departments lazily for the UI (key => name)
        if ($this->departments === [] || $this->departments === null) {
            $this->departments = Department::query()
                ->active()
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get(['slug', 'name'])
                ->mapWithKeys(fn ($d) => [$d->slug => $d->name])
                ->toArray();
        }
        $this->breadCrumbUsersPanel();

        $query = User::query();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('email', 'like', '%'.$this->search.'%')
                    ->orWhere('id_number', 'like', '%'.$this->search.'%')
                    ->orWhere('department', 'like', '%'.$this->search.'%');
            });
        }

        if ($this->roleFilter) {
            $query->where('role_id', $this->roleFilter);
        }

        $userAccess = $query->with('role')->paginate($this->perPage);
        $roles = Roles::all();

        $totalUsers = User::count();
        $pendingSetup = User::where('is_temporary_password_used', false)->count();

        return view('livewire.control-panel.users-control', compact(
            'userAccess',
            'roles',
            'totalUsers',
            'pendingSetup'
        ));
    }
}
