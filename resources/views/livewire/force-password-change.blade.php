<div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black bg-opacity-75">
    <!-- Modal Content -->
    <div class="bg-white rounded-xl shadow-2xl overflow-hidden w-full max-w-md relative">
        <!-- Modal Header -->
        <div class="bg-gradient-to-r from-red-600 to-red-700 px-6 py-4">
            <div class="flex items-center">
                <span class="material-symbols-sharp text-white text-2xl mr-3">lock_reset</span>
                <div>
                    <h3 class="text-xl font-semibold text-white">Password Change Required</h3>
                    <p class="text-red-100 text-sm">You must change your temporary password to continue</p>
                </div>
            </div>
        </div>

        <!-- Modal Body -->
        <div class="p-6">
            <div class="mb-4 p-4 bg-amber-50 border border-amber-200 rounded-lg">
                <div class="flex items-start">
                    <span class="material-symbols-sharp text-amber-600 mr-2 mt-0.5">warning</span>
                    <div class="text-sm text-amber-800">
                        <p class="font-medium">Security Notice:</p>
                        <p>For your account security, you must change your temporary password before accessing the system.</p>
                    </div>
                </div>
            </div>

            <form wire:submit.prevent="changePassword" class="space-y-4">
                <!-- Show general error if exists -->
                @error('general')
                    <div class="p-3 bg-red-50 border border-red-200 rounded-lg">
                        <div class="text-sm text-red-600">{{ $message }}</div>
                    </div>
                @enderror

                <!-- Current Password -->
                <div>
                    <label for="currentPassword" class="block text-sm font-medium text-gray-700 mb-2">
                        <span class="flex items-center">
                            <span class="material-symbols-sharp text-sm mr-2 text-gray-500">key</span>
                            Current Password
                        </span>
                    </label>
                    <div class="relative">
                        <input
                            type="{{ $showCurrentPassword ? 'text' : 'password' }}"
                            wire:model="currentPassword"
                            id="currentPassword"
                            class="w-full px-3 py-2 pr-10 border border-gray-300 rounded-lg shadow-sm focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors @error('currentPassword') border-red-500 @enderror"
                            placeholder="Enter your current password">
                        <button type="button"
                                wire:click="$toggle('showCurrentPassword')"
                                class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600">
                            <span class="material-symbols-sharp text-sm">
                                {{ $showCurrentPassword ? 'visibility_off' : 'visibility' }}
                            </span>
                        </button>
                    </div>
                    @error('currentPassword')
                        <div class="mt-1 text-sm text-red-600">{{ $message }}</div>
                    @enderror
                </div>

                <!-- New Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        <span class="flex items-center">
                            <span class="material-symbols-sharp text-sm mr-2 text-gray-500">lock</span>
                            New Password
                        </span>
                    </label>
                    <div class="relative">
                        <input
                            type="{{ $showNewPassword ? 'text' : 'password' }}"
                            wire:model="password"
                            id="password"
                            class="w-full px-3 py-2 pr-10 border border-gray-300 rounded-lg shadow-sm focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors @error('password') border-red-500 @enderror"
                            placeholder="Enter your new password">
                        <button type="button"
                                wire:click="$toggle('showNewPassword')"
                                class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600">
                            <span class="material-symbols-sharp text-sm">
                                {{ $showNewPassword ? 'visibility_off' : 'visibility' }}
                            </span>
                        </button>
                    </div>
                    @error('password')
                        <div class="mt-1 text-sm text-red-600">{{ $message }}</div>
                    @enderror
                    <div class="mt-1 text-xs text-gray-500">
                        Password must be at least 8 characters long
                    </div>
                </div>

                <!-- Confirm Password -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                        <span class="flex items-center">
                            <span class="material-symbols-sharp text-sm mr-2 text-gray-500">lock_check</span>
                            Confirm New Password
                        </span>
                    </label>
                    <div class="relative">
                        <input
                            type="{{ $showConfirmPassword ? 'text' : 'password' }}"
                            wire:model="password_confirmation"
                            id="password_confirmation"
                            class="w-full px-3 py-2 pr-10 border border-gray-300 rounded-lg shadow-sm focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors @error('password_confirmation') border-red-500 @enderror"
                            placeholder="Confirm your new password">
                        <button type="button"
                                wire:click="$toggle('showConfirmPassword')"
                                class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600">
                            <span class="material-symbols-sharp text-sm">
                                {{ $showConfirmPassword ? 'visibility_off' : 'visibility' }}
                            </span>
                        </button>
                    </div>
                    @error('password_confirmation')
                        <div class="mt-1 text-sm text-red-600">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Password Requirements -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                    <p class="text-sm font-medium text-blue-800 mb-2">Password Requirements:</p>
                    <ul class="text-xs text-blue-700 space-y-1">
                        <li class="flex items-center">
                            <span class="w-1.5 h-1.5 bg-blue-400 rounded-full mr-2"></span>
                            At least 8 characters long
                        </li>
                        <li class="flex items-center">
                            <span class="w-1.5 h-1.5 bg-blue-400 rounded-full mr-2"></span>
                            Include uppercase and lowercase letters
                        </li>
                        <li class="flex items-center">
                            <span class="w-1.5 h-1.5 bg-blue-400 rounded-full mr-2"></span>
                            Include at least one number
                        </li>
                        <li class="flex items-center">
                            <span class="w-1.5 h-1.5 bg-blue-400 rounded-full mr-2"></span>
                            Include at least one special character
                        </li>
                    </ul>
                </div>

                <!-- Submit Button -->
                <div class="pt-4">
                    <button type="submit"
                            wire:loading.attr="disabled"
                            class="w-full px-4 py-2 text-sm font-medium text-white bg-gradient-to-r from-red-600 to-red-700 border border-transparent rounded-lg shadow-sm hover:from-red-700 hover:to-red-800 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
                        <span class="flex items-center justify-center">
                            <span class="material-symbols-sharp text-sm mr-2">save</span>
                            <span wire:loading.remove>Change Password</span>
                            <span wire:loading>Changing Password...</span>
                        </span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Modal Footer -->
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
            <div class="flex items-center justify-between">
                <p class="text-xs text-gray-500">This modal cannot be closed until password is changed</p>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-xs text-red-600 hover:text-red-800 underline">
                        Logout Instead
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
