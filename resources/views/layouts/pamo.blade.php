<!-- filepath: c:\inetpub\wwwroot\ITSSMO-Tool\resources\views\layouts\pamo.blade.php -->
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'ITSSMO Tool') }} - PAMO</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Sharp:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet" />
    {{-- <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script> --}}

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="font-sans antialiased bg-gradient-to-br from-blue-50 to-yellow-100 min-h-screen">
    <div x-data="{ sidebarOpen: false, profileModalOpen: false }" class="bg-cyan-50 h-screen flex flex-col">
        <!-- Mobile menu button -->
        <div class="lg:hidden fixed top-0 left-0 right-0 z-30 bg-white shadow-sm border-b border-cyan-100">
            <div class="flex items-center justify-between p-4">
                <button @click="sidebarOpen = !sidebarOpen" class="text-blue-600 focus:outline-none">
                    <span class="material-symbols-sharp text-2xl">menu</span>
                </button>
                <span class="text-lg font-semibold text-blue-600">PAMO Dashboard</span>
                <div class="w-10 h-10 rounded-full overflow-hidden bg-gray-300">
                    @if(Auth::user()->profile_photo_path)
                        <img src="{{ Storage::url(Auth::user()->profile_photo_path) }}" alt="{{ Auth::user()->name }}" class="w-full h-full object-cover">
                    @else
                        <img src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" class="w-full h-full object-cover">
                    @endif
                </div>
            </div>
        </div>

        <!-- Main layout container -->
        <div class="flex flex-1 overflow-hidden">
            <!-- Sidebar overlay -->
            <div
                x-show="sidebarOpen"
                @click="sidebarOpen = false"
                class="fixed inset-0 z-20 bg-black bg-opacity-50 lg:hidden"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0">
            </div>

            <!-- Side Navigation -->
            <aside
                :class="{'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen}"
                class="fixed lg:sticky top-0 left-0 z-30 bg-blue-600 w-64 h-screen border-r border-blue-500 flex flex-col transform transition-transform duration-200 ease-in-out lg:translate-x-0">
                <!-- Logo -->
                <div class="p-4 flex items-center justify-between border-b border-blue-500">
                    <a href="{{ route('dashboard') }}" class="flex items-center">
                        <span class="text-xl font-semibold text-white"><i class="fas fa-laptop mr-2 text-yellow-300"></i> PAMO</span>
                    </a>
                    <button @click="sidebarOpen = false" class="lg:hidden text-white focus:outline-none">
                        <span class="material-symbols-sharp text-2xl">close</span>
                    </button>
                </div>

                <!-- User Profile -->
                <div class="px-4 py-6 border-b border-blue-500 bg-blue-600/80 backdrop-blur-sm rounded-lg">
                    <div class="flex flex-col items-center">
                        <!-- User Avatar -->
                        <div class="w-20 h-20 rounded-full overflow-hidden bg-yellow-300 mb-3 flex items-center justify-center">
                            @if(Auth::user()->profile_photo_path)
                                <img src="{{ Storage::url(Auth::user()->profile_photo_path) }}" alt="{{ Auth::user()->name }}" class="w-full h-full object-cover">
                            @else
                                @if(Auth::user()->profile_photo_url)
                                    <img src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" class="w-full h-full object-cover">
                                @else
                                    <span class="text-2xl font-bold text-blue-700">{{ substr(Auth::user()->name, 0, 1) }}</span>
                                @endif
                            @endif
                        </div>

                        <!-- User Name and Role -->
                        <div class="text-center">
                            <h4 class="font-medium text-white">{{ Auth::user()->name }}</h4>
                            <p class="text-xs text-blue-50">{{ Auth::user()->role ?? 'User' }}</p>

                            <button @click="profileModalOpen = true" class="w-full mt-3 bg-yellow-300 text-blue-700 px-4 py-2 rounded-lg text-sm font-semibold hover:bg-amber-400 transition-colors">
                                <span class="flex items-center justify-center">
                                    <span class="material-symbols-sharp text-xs mr-1">edit</span>
                                    Edit Profile
                                </span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Navigation Links -->
                <nav class="flex-1 px-4 py-4 overflow-y-auto bg-blue-600">
                    <ul class="space-y-2">
                       <li>
                            <x-end-user-nav-link
                                href="{{ route('pamo.dashboard') }}"
                                :active="request()->routeIs('pamo.dashboard')"
                                icon="dashboard">
                                Overview
                            </x-end-user-nav-link>
                        </li>
                        <li>
                            <x-end-user-nav-link
                                href="{{ route('pamo.inventory') }}"
                                :active="request()->routeIs('pamo.inventory')"
                                icon="inventory_2">
                                Inventory & Supplies
                            </x-end-user-nav-link>
                        </li>
                        <li>
                            <x-end-user-nav-link
                                href="{{ route('pamo.assetTracker') }}"
                                :active="request()->routeIs('pamo.assetTracker')"
                                icon="inventory_2">
                                Asset's Tracker
                            </x-end-user-nav-link>
                        </li>
                        <li>
                            <x-end-user-nav-link
                                href="{{ route('pamo.barcode') }}"
                                :active="request()->routeIs('pamo.barcode')"
                                icon="qr_code_scanner">
                                Barcode Generator
                            </x-end-user-nav-link>
                        </li>
                        <li>
                            <x-end-user-nav-link
                                href="{{ route('pamo.masterList') }}"
                                :active="request()->routeIs('pamo.masterList')"
                                icon="groups">
                                MasterList
                            </x-end-user-nav-link>
                        </li>
                        @if(strtolower(Auth::user()->role) === 'administrator' || strtolower(Auth::user()->role) === 'developer')
                            <li>
                                <x-end-user-nav-link
                                    href="{{ route('dashboard') }}"
                                    :active="request()->routeIs('dashboard')"
                                    icon="arrow_back">
                                    Back to Main System
                                </x-end-user-nav-link>
                            </li>
                        @endif
                    </ul>
                </nav>

                <!-- Logout at Bottom -->
                <div class="p-4 border-t border-blue-500 mt-auto">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                                class="flex items-center w-full p-2 text-left text-yellow-300 rounded hover:bg-blue-700">
                            <span class="material-symbols-sharp">logout</span>
                            <span class="ml-3">Log Out</span>
                        </button>
                    </form>
                </div>
            </aside>

            <!-- Main Content -->
            <div class="flex-1 overflow-y-auto bg-cyan-50">
                <main class="py-16 lg:py-6 px-4 lg:px-8">
                    {{ $slot }}
                </main>
            </div>
        </div>

    <!-- Profile Edit Modal -->
    <div
        x-show="profileModalOpen"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        @click.away="profileModalOpen = false"
        @keydown.escape.window="profileModalOpen = false"
        class="fixed inset-0 z-50 flex items-center justify-center p-4">

        <!-- Modal Backdrop -->
        <div class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm" @click="profileModalOpen = false"></div>

        <!-- Modal Content -->
        <div class="bg-white rounded-xl shadow-2xl overflow-hidden w-full max-w-lg z-10 relative">
            <!-- Modal Header -->
            <div class="relative bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-xl font-semibold text-white">Edit Profile</h3>
                        <p class="text-blue-100 text-sm">Update your personal information</p>
                    </div>
                    <button @click="profileModalOpen = false"
                            class="text-white hover:text-yellow-300 transition-colors p-2 rounded-lg hover:bg-white/10">
                        <span class="material-symbols-sharp text-2xl">close</span>
                    </button>
                </div>
            </div>

            <!-- Modal Body -->
            <div class="p-6">
                <form action="{{ route('user-profile-information.update') }}" method="POST"
                    class="space-y-6" enctype="multipart/form-data"
                    x-data="{
                          photoPreview: null,
                          photoName: null,
                          isSubmitting: false,
                          submitForm() {
                              this.isSubmitting = true;
                              this.$el.submit();
                          }
                      }"
                    @submit="isSubmitting = true">
                    @csrf
                    @method('PUT')

                    <!-- Profile Photo Section -->
                    <div class="text-center">
                        <div class="relative inline-block">
                            <!-- Current/Preview Photo -->
                            <div class="w-24 h-24 rounded-full overflow-hidden bg-gradient-to-br from-blue-400 to-blue-600 mx-auto mb-4 shadow-lg">
                                <template x-if="photoPreview">
                                    <img :src="photoPreview" alt="Photo preview" class="w-full h-full object-cover">
                                </template>
                                <template x-if="!photoPreview">
                                    @if(Auth::user()->profile_photo_path)
                                        <img src="{{ Storage::url(Auth::user()->profile_photo_path) }}"
                                            alt="{{ Auth::user()->name }}"
                                            class="w-full h-full object-cover">
                                    @elseif(Auth::user()->profile_photo_url)
                                        <img src="{{ Auth::user()->profile_photo_url }}"
                                            alt="{{ Auth::user()->name }}"
                                            class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center">
                                            <span class="text-3xl font-bold text-white">{{ substr(Auth::user()->name, 0, 1) }}</span>
                                        </div>
                                    @endif
                                </template>
                            </div>

                            <!-- Upload Button -->
                            <div class="relative">
                                <input type="file"
                                    name="photo"
                                    id="photo"
                                    accept="image/*"
                                    class="hidden"
                                    @change="
                                        photoName = $event.target.files[0]?.name;
                                        const reader = new FileReader();
                                        reader.onload = (e) => photoPreview = e.target.result;
                                        reader.readAsDataURL($event.target.files[0]);
                                    ">
                                <label for="photo"
                                    class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 cursor-pointer transition-colors">
                                    <span class="material-symbols-sharp text-sm mr-2">photo_camera</span>
                                    Choose Photo
                                </label>
                            </div>

                            <!-- Photo Name Display -->
                            <div x-show="photoName" class="mt-2">
                                <p class="text-xs text-gray-500" x-text="photoName"></p>
                            </div>

                            <!-- Remove Photo Button -->
                            <div class="mt-2" x-show="photoPreview">
                                <button type="button"
                                        @click="photoPreview = null; photoName = null; document.getElementById('photo').value = ''"
                                        class="text-xs text-red-600 hover:text-red-800 transition-colors">
                                    Remove photo
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Form Fields -->
                    <div class="space-y-4">
                        <!-- Name -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                <span class="flex items-center">
                                    <span class="material-symbols-sharp text-sm mr-2 text-gray-500">person</span>
                                    Full Name
                                </span>
                            </label>
                            <input type="text"
                                name="name"
                                id="name"
                                value="{{ Auth::user()->name }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-20 transition-colors"
                                placeholder="Enter your full name">
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                <span class="flex items-center">
                                    <span class="material-symbols-sharp text-sm mr-2 text-gray-500">email</span>
                                    Email Address
                                </span>
                            </label>
                            <input type="email"
                                name="email"
                                id="email"
                                value="{{ Auth::user()->email }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-20 transition-colors"
                                placeholder="Enter your email address">
                        </div>

                        <!-- Role (Read-only) -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <span class="flex items-center">
                                    <span class="material-symbols-sharp text-sm mr-2 text-gray-500">badge</span>
                                    Role
                                </span>
                            </label>
                            <div class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg text-gray-600">
                                {{ Auth::user()->role ?? 'User' }}
                            </div>
                        </div>
                        <div>
                            <label for="department" class="block text-sm font-medium text-gray-700 mb-2">
                                <span class="flex items-center">
                                    <span class="material-symbols-sharp text-sm mr-2 text-gray-500">business</span>
                                    Department
                                </span>
                            </label>
                            <input type="text"
                                   name="department"
                                   id="department"
                                   value="{{ Auth::user()->department ?? '' }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-20 transition-colors"
                                   placeholder="Enter your department">
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                        <button @click="profileModalOpen = false"
                                type="button"
                                :disabled="isSubmitting"
                                class="px-6 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors">
                            Cancel
                        </button>
                        <button type="submit"
                                class="px-6 py-2.5 text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-blue-700 border border-transparent rounded-lg shadow-sm hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200">
                            <span class="flex items-center">
                                <span class="material-symbols-sharp text-sm mr-2">save</span>
                                Save Changes
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- <div
        x-show="profileModalOpen"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        @click.away="profileModalOpen = false"
        @keydown.escape.window="profileModalOpen = false"
        class="fixed inset-0 z-50 flex items-center justify-center p-4">

        <!-- Modal Backdrop -->
        <div class="fixed inset-0 bg-black bg-opacity-50" @click="profileModalOpen = false"></div>

        <!-- Modal Content -->
        <div class="bg-white rounded-lg shadow-xl overflow-hidden w-full max-w-md z-10 relative">
            <!-- Modal Header -->
            <div class="flex items-center justify-between p-4 border-b border-cyan-100 bg-blue-600">
                <h3 class="text-lg font-medium text-white">Edit Profile</h3>
                <button @click="profileModalOpen = false" class="text-white hover:text-yellow-300">
                    <span class="material-symbols-sharp">close</span>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="p-4">
                <form action="{{ route('user-profile-information.update') }}" method="POST" class="space-y-4" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                        <input type="text" name="name" id="name" value="{{ Auth::user()->name }}" class="mt-1 block w-full rounded-md border-cyan-100 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" name="email" id="email" value="{{ Auth::user()->email }}" class="mt-1 block w-full rounded-md border-cyan-100 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                    </div>

                    <!-- Photo -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Profile Photo</label>
                        <div class="mt-1 flex items-center space-x-4">
                            <div class="w-12 h-12 rounded-full overflow-hidden bg-gray-300">
                                @if(Auth::user()->profile_photo_path)
                                    <img src="{{ Storage::url(Auth::user()->profile_photo_path) }}" alt="{{ Auth::user()->name }}" class="w-full h-full object-cover">
                                @else
                                    <img src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" class="w-full h-full object-cover">
                                @endif
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 mb-1">To change your photo, please use the User Settings menu in the main application.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end">
                        <button @click="profileModalOpen = false" type="button" class="mr-3 px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md shadow-sm hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                            Cancel
                        </button>
                        <button type="submit" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div> --}}
    </div>

    @stack('modals')
    @stack('scripts')
    @livewireScripts
</body>
</html>
