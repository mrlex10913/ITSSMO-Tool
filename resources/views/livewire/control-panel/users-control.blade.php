<!-- filepath: c:\inetpub\wwwroot\ITSSMO-Tool\resources\views\livewire\control-panel\users-control.blade.php -->
<div class="container mx-auto">
    <!-- Header Section -->
    <div class="mb-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">User Management</h1>
                <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                    <x-nav-link href="{{route('controlPanel.admin')}}" class="hover:text-blue-600">
                        <i class="fas fa-home mr-1"></i>Control Panel
                    </x-nav-link>
                    <span>/</span>
                    <span class="text-blue-600 font-medium">{{session('breadcrumb')}}</span>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-2 gap-4 mt-4 lg:mt-0">
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg p-4 text-white">
                    <div class="text-2xl font-bold">{{ $totalUsers }}</div>
                    <div class="text-sm opacity-80">Total Users</div>
                </div>
                <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg p-4 text-white">
                    <div class="text-2xl font-bold">{{ $pendingSetup }}</div>
                    <div class="text-sm opacity-80">Pending Setup</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Actions -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 mb-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <!-- Search and Filter -->
            <div class="flex flex-col sm:flex-row gap-4 flex-1">
                <div class="relative flex-1">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input type="text"
                           wire:model.live.debounce.300ms="search"
                           placeholder="Search users by name, email, or ID..."
                           class="w-full pl-10 pr-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                </div>

                <select wire:model="roleFilter"
                        class="px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                    <option value="">All Roles</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}">{{ $role->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Add User Button -->
            <x-button wire:click="OpenNewUserAccessModal"
                      class="bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 px-6 py-3 rounded-lg shadow-lg transform hover:scale-105 transition-all duration-200">
                <i class="fas fa-plus mr-2"></i>Add New User
            </x-button>
        </div>
    </div>

    <!-- Users Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @forelse ($userAccess as $user)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 overflow-hidden">
                <!-- User Header -->
                <div class="bg-gradient-to-r from-blue-500 to-purple-600 p-6 text-center">
                    <div class="w-20 h-20 mx-auto bg-white rounded-full flex items-center justify-center mb-4 shadow-lg">
                        @if($user->profile_photo_path)
                            <img src="{{ asset('storage/' . $user->profile_photo_path) }}"
                                alt="{{ $user->name }}"
                                class="w-full h-full object-cover">
                        @else
                            <i class="fas fa-user text-3xl text-gray-600"></i>
                        @endif
                        </div>
                    <h3 class="text-xl font-bold text-white">{{ $user->name }}</h3>
                    <p class="text-blue-100 text-sm">{{ $user->id_number }}</p>
                </div>

                <!-- User Details -->
                <div class="p-6">
                    <div class="space-y-3">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-envelope text-gray-400 w-4"></i>
                            <span class="text-sm text-gray-600 dark:text-gray-300 truncate">{{ $user->email }}</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <i class="fas fa-building text-gray-400 w-4"></i>
                            <span class="text-sm text-gray-600 dark:text-gray-300">{{ $user->department }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <i class="fas fa-user-tag text-gray-400 w-4"></i>
                                <span class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ $user->role->name ?? 'No Role' }}</span>
                            </div>
                            @if(!$user->is_temporary_password_used)
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    <span class="w-2 h-2 bg-yellow-400 rounded-full mr-1"></span>
                                    Setup Pending
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex gap-2 mt-6">
                        <button wire:click="openSpecificUserAccessModal({{ $user->id }})"
                                class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200">
                            <i class="fas fa-edit mr-1"></i>Edit
                        </button>
                        <button wire:click="confirmDelete({{ $user->id }})"
                                class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-12">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-8">
                    <i class="fas fa-users text-6xl text-gray-300 mb-4"></i>
                    <h3 class="text-xl font-semibold text-gray-600 dark:text-gray-300 mb-2">No users found</h3>
                    <p class="text-gray-500">
                        @if($search)
                            No results for "{{ $search }}". Try adjusting your search.
                        @elseif($roleFilter)
                            No users found with the selected role.
                        @else
                            No users have been created yet. Add your first user!
                        @endif
                    </p>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $userAccess->links() }}
    </div>

    <!-- Edit User Modal -->
    <x-dialog-modal wire:model="openSpecificUserModal" maxWidth="2xl">
        <x-slot name="title">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-user-edit text-blue-600"></i>
                </div>
                <div>
                    <h3 class="text-xl font-semibold text-gray-900">Edit User Account</h3>
                    <p class="text-sm text-gray-500">Update user information and reset password if needed</p>
                </div>
            </div>
        </x-slot>

        <x-slot name="content">
            <div class="space-y-6">
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        <i class="fas fa-camera mr-2 text-purple-500"></i>Profile Image
                    </label>

                    @if($temporaryProfileImage)
                        <div class="mb-3">
                            <img src="{{ $temporaryProfileImage->temporaryUrl() }}"
                                class="w-24 h-24 object-cover rounded-full border-2 border-blue-500">
                        </div>
                    @elseif($profile_image)
                        <div class="mb-3">
                            <img src="{{ asset('storage/' . $profile_image) }}"
                                class="w-24 h-24 object-cover rounded-full border-2 border-blue-500">
                        </div>
                    @endif

                    <input type="file" wire:model="temporaryProfileImage"
                        class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    <div class="text-xs text-gray-500 mt-1">
                        Upload a new image to replace the current one
                    </div>
                    @error('temporaryProfileImage') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <!-- Email Field -->
                <div class="relative">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        <i class="fas fa-envelope mr-2 text-blue-500"></i>Email Address
                    </label>
                    <input type="email" wire:model.defer="email"
                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                           placeholder="user@company.com">
                    <p class="text-xs text-gray-500 mt-1">Use company O365 account for SSO integration</p>
                    @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Password Section -->
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                    <div class="flex items-center justify-between mb-3">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            <i class="fas fa-key mr-2 text-green-500"></i>Temporary Password
                        </label>
                        <x-button wire:click="generateTemporaryPassword"
                                  class="bg-green-600 hover:bg-green-700 text-sm px-4 py-2">
                            <i class="fas fa-refresh mr-1"></i>Reset Password
                        </x-button>
                    </div>
                    <input type="text" wire:model.defer="temporaryPassword" disabled
                           class="w-full px-4 py-3 bg-gray-100 border border-gray-300 rounded-lg text-gray-600 font-mono">
                    @error('temporaryPassword') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- User Information Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <i class="fas fa-id-card mr-2 text-purple-500"></i>ID Number
                        </label>
                        <input type="text" wire:model.defer="id_number"
                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                               placeholder="EMP001">
                        @error('id_number') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <i class="fas fa-user mr-2 text-blue-500"></i>Full Name
                        </label>
                        <input type="text" wire:model.defer="name"
                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                               placeholder="John Doe">
                        @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <i class="fas fa-building mr-2 text-orange-500"></i>Department
                        </label>
                        <select wire:model.defer="department"
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                            <option value="">Select Department</option>
                            @foreach($departments as $key => $deptName)
                                <option value="{{ $key }}">{{ $deptName }}</option>
                            @endforeach
                        </select>
                        @error('department') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <i class="fas fa-user-tag mr-2 text-red-500"></i>User Role
                        </label>
                        <select wire:model.defer="role_id"
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                            <option value="">Select Role</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}">{{ $role->name }}</option>
                            @endforeach
                        </select>
                        @error('role_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>
        </x-slot>

        <x-slot name="footer">
            <div class="flex justify-end gap-3">
                <x-secondary-button wire:click="$set('openSpecificUserModal', false)">
                    <i class="fas fa-times mr-2"></i>Cancel
                </x-secondary-button>
                <x-button wire:click="updateSpecificUserAccessModal"
                          class="bg-blue-600 hover:bg-blue-700">
                    <i class="fas fa-save mr-2"></i>Save Changes
                </x-button>
            </div>
        </x-slot>
    </x-dialog-modal>

    <!-- Add User Modal -->
    <x-dialog-modal wire:model="NewUserAccessModal" maxWidth="2xl">
        <x-slot name="title">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-user-plus text-green-600"></i>
                </div>
                <div>
                    <h3 class="text-xl font-semibold text-gray-900">Add New User</h3>
                    <p class="text-sm text-gray-500">Create a new user account with temporary password</p>
                </div>
            </div>
        </x-slot>

        <x-slot name="content">
            <div class="space-y-6">
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        <i class="fas fa-camera mr-2 text-purple-500"></i>Profile Image
                    </label>

                    @if($temporaryProfileImage)
                        <div class="mb-3">
                            <img src="{{ $temporaryProfileImage->temporaryUrl() }}"
                                class="w-24 h-24 object-cover rounded-full border-2 border-blue-500">
                        </div>
                    @endif

                    <input type="file" wire:model="temporaryProfileImage"
                        class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    <div class="text-xs text-gray-500 mt-1">
                        Recommended: Square image, 300x300px or larger
                    </div>
                    @error('temporaryProfileImage') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <!-- Email Field -->
                <div class="relative">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        <i class="fas fa-envelope mr-2 text-blue-500"></i>Email Address *
                    </label>
                    <input type="email" wire:model.defer="email"
                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                           placeholder="user@company.com">
                    <p class="text-xs text-gray-500 mt-1">Use company O365 account for SSO integration</p>
                    @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Password Section -->
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                    <div class="flex items-center justify-between mb-3">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            <i class="fas fa-key mr-2 text-green-500"></i>Generated Password
                        </label>
                        <x-button wire:click="generateTemporaryPassword"
                                  class="bg-green-600 hover:bg-green-700 text-sm px-4 py-2">
                            <i class="fas fa-refresh mr-1"></i>Generate New
                        </x-button>
                    </div>
                    <input type="text" wire:model.defer="temporaryPassword" disabled
                           class="w-full px-4 py-3 bg-gray-100 border border-gray-300 rounded-lg text-gray-600 font-mono">
                    @error('temporaryPassword') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- User Information Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <i class="fas fa-id-card mr-2 text-purple-500"></i>ID Number *
                        </label>
                        <input type="text" wire:model.defer="id_number"
                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                               placeholder="EMP001">
                        @error('id_number') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <i class="fas fa-user mr-2 text-blue-500"></i>Full Name *
                        </label>
                        <input type="text" wire:model.defer="name"
                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                               placeholder="John Doe">
                        @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <i class="fas fa-building mr-2 text-orange-500"></i>Department *
                        </label>
                        <select wire:model.defer="department"
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                            <option value="">Select Department</option>
                            @foreach($departments as $key => $deptName)
                                <option value="{{ $key }}">{{ $deptName }}</option>
                            @endforeach
                        </select>
                        @error('department') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <i class="fas fa-user-tag mr-2 text-red-500"></i>User Role *
                        </label>
                        <select wire:model.defer="role_id"
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                            <option value="">Select Role</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}">{{ $role->name }}</option>
                            @endforeach
                        </select>
                        @error('role_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>
        </x-slot>

        <x-slot name="footer">
            <div class="flex justify-end gap-3">
                <x-secondary-button wire:click="$set('NewUserAccessModal', false)">
                    <i class="fas fa-times mr-2"></i>Cancel
                </x-secondary-button>
                <x-button wire:click="saveUser"
                          class="bg-green-600 hover:bg-green-700">
                    <i class="fas fa-user-plus mr-2"></i>Create User
                </x-button>
            </div>
        </x-slot>
    </x-dialog-modal>

    <!-- Delete Confirmation Modal -->
    <x-dialog-modal wire:model="deleteConfirmationModal">
        <x-slot name="title">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-exclamation-triangle text-red-600"></i>
                </div>
                <div>
                    <h3 class="text-xl font-semibold text-gray-900">Confirm Deletion</h3>
                    <p class="text-sm text-gray-500">This action cannot be undone</p>
                </div>
            </div>
        </x-slot>

        <x-slot name="content">
            <div class="p-4 text-center">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                    Are you sure you want to delete
                    <span class="font-bold text-red-600">{{ $userToDelete->name ?? '' }}</span>?
                </h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    This will permanently remove the user and all associated data.
                    This action cannot be undone.
                </p>
            </div>
        </x-slot>

        <x-slot name="footer">
            <div class="flex justify-end gap-3">
                <x-secondary-button wire:click="$set('deleteConfirmationModal', false)">
                    <i class="fas fa-times mr-2"></i>Cancel
                </x-secondary-button>
                <x-button wire:click="deleteUser"
                          class="bg-red-600 hover:bg-red-700">
                    <i class="fas fa-trash mr-2"></i>Delete User
                </x-button>
            </div>
        </x-slot>
    </x-dialog-modal>
</div>
