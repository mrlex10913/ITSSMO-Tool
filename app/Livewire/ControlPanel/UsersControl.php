<?php

namespace App\Livewire\ControlPanel;

use App\Models\Roles;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class UsersControl extends Component
{
    use WithPagination;
    use WithFileUploads;
    // User properties
    public $id_number;
    public $name;
    public $email;
    public $department;
    public $role_id;
    public $password;
    public $temporaryPassword;
    public $updateAccessID;

    // UI control
    public $openSpecificUserModal = false;
    public $NewUserAccessModal = false;
    public $deleteConfirmationModal = false;
    public $userToDelete;

    // Search and filter
    public $search = '';
    public $roleFilter = '';
    public $perPage = 12;

    public $profile_image;
    public $temporaryProfileImage;

    // Department options - could be moved to a model in a more complex system
    public $departments = [
        'IT' => 'Information Technology',
        'HR' => 'Human Resources',
        'Finance' => 'Finance Department',
        'Operations' => 'Operations',
        'Admin' => 'Administration',
        'Procurement' => 'Procurement',
        'Logistics' => 'Logistics'
    ];



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
        if (!$this->updateAccessID) {
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
        'id_number.required' => 'ID number is required'
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

        if (!$userFind) {
            flash()->error('User not found');
            return;
        }

        $this->updateAccessID = $userId;
        $this->email = $userFind->email;
        $this->name = $userFind->name;

        if($userFind->temporary_password == null){
            $this->temporaryPassword = 'Password Already Changed';
        } else {
            $this->temporaryPassword = $userFind->temporary_password;
        }

        $this->department = $userFind->department;
        $this->id_number = $userFind->id_number;
        $this->role_id = $userFind->role_id;
        // Store the current profile photo path
        $this->profile_image = $userFind->profile_photo_path;
    }
    public function updateSpecificUserAccessModal()
    {
        try {
            $this->validate();

            $updateAccess = User::find($this->updateAccessID);
            if (!$updateAccess) {
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

            flash()->success('User account has been updated successfully');
            $this->openSpecificUserModal = false;
            $this->resetForm();

        } catch(ValidationException $e) {
            flash()->error('Please check the form for errors');
            throw $e;
        }
    }
    public function generateTemporaryPassword()
    {
        // Generate a more secure password with mixed characters
        $this->temporaryPassword = Str::password(10, true, true, true, false);
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

        if (!$this->userToDelete) {
            flash()->error('User not found');
            return;
        }

        $this->deleteConfirmationModal = true;
    }
    public function deleteUser()
    {
        try {
            if (!$this->userToDelete) {
                flash()->error('User not found');
                return;
            }

            // Prevent deleting yourself
            if ($this->userToDelete->id === auth()->id()) {
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
        try{
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

        } catch(ValidationException $e){
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
            'temporaryProfileImage'
        ]);
    }
    public function render()
    {
        $this->breadCrumbUsersPanel();

        $query = User::query();

        if ($this->search) {
            $query->where(function($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%')
                  ->orWhere('id_number', 'like', '%' . $this->search . '%')
                  ->orWhere('department', 'like', '%' . $this->search . '%');
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
