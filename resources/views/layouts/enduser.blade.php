<!-- filepath: c:\inetpub\wwwroot\ITSSMO-Tool\resources\views\layouts\pamo.blade.php -->
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php
        // Direct query to get role data
        $user = Auth::user();
        $roleRecord = null;
        $userRole = 'user'; // default
        $roleTitle = 'USER'; // default

        if ($user->role_id) {
            $roleRecord = \App\Models\Roles::find($user->role_id);
            if ($roleRecord) {
                $userRole = strtolower($roleRecord->slug);
                $roleTitle = strtoupper($roleRecord->name);
            }
        }
    @endphp

    <title>{{ config('app.name', 'ITSSMO Tool') }} - {{ $roleTitle }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Sharp:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>

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
                    <a href="#" class="flex items-center">
                        <span class="text-xl font-semibold text-white">
                            @if($userRole === 'pamo')
                                <i class="fas fa-laptop mr-2 text-yellow-300"></i> PAMO
                            @elseif($userRole === 'bfo')
                                <i class="fas fa-calculator mr-2 text-yellow-300"></i> BFO
                            @else
                                <i class="fas fa-user mr-2 text-yellow-300"></i> {{ strtoupper($userRole) }}
                            @endif
                        </span>
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
                            <p class="text-xs text-blue-50">{{ $roleRecord ? $roleRecord->name : 'User' }}</p>

                            <button @click="profileModalOpen = true" class="w-full mt-3 bg-yellow-300 text-blue-700 px-4 py-2 rounded-lg text-sm font-semibold hover:bg-amber-400 transition-colors">
                                <span class="flex items-center justify-center">
                                    <span class="material-symbols-sharp text-xs mr-1">person</span>
                                    My Profile & Assets
                                </span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Navigation Links -->
                <nav class="flex-1 px-4 py-4 overflow-y-auto bg-blue-600"
                    x-data="{
                        selectedDepartment: '{{ $userRole }}',
                        userRole: '{{ $userRole }}',

                        init() {
                            // Only for admin/developer users, check localStorage
                            if (['administrator', 'developer'].includes(this.userRole)) {
                                const savedDept = localStorage.getItem('selectedDepartment');
                                if (savedDept && ['pamo', 'bfo'].includes(savedDept)) {
                                    this.selectedDepartment = savedDept;
                                }
                            }

                            // Auto-detect current department from route
                            const currentPath = window.location.pathname;
                            if (currentPath.includes('/pamo/')) {
                                this.selectedDepartment = 'pamo';
                            } else if (currentPath.includes('/bfo/')) {
                                this.selectedDepartment = 'bfo';
                            }

                            // Save to localStorage when changed
                            this.$watch('selectedDepartment', value => {
                                if (['administrator', 'developer'].includes(this.userRole)) {
                                    localStorage.setItem('selectedDepartment', value);
                                }
                            });
                        },

                        selectDepartment(dept) {
                            this.selectedDepartment = dept;
                        }
                    }">
                    <ul class="space-y-2">

                        {{-- Department Selector (only show if user can access multiple departments) --}}
                        @if(in_array($userRole, ['administrator', 'developer']))
                            <li class="mb-4">
                                <div class="text-xs text-blue-200 mb-2">Select Department:</div>
                                <div class="flex space-x-1">
                                    <button @click="selectDepartment('pamo')"
                                            :class="selectedDepartment === 'pamo' ? 'bg-yellow-300 text-blue-700' : 'bg-blue-500 text-white hover:bg-blue-400'"
                                            class="flex-1 px-3 py-2 text-xs font-medium rounded transition-colors">
                                        <i class="fas fa-laptop mr-1"></i> PAMO
                                    </button>
                                    <button @click="selectDepartment('bfo')"
                                            :class="selectedDepartment === 'bfo' ? 'bg-yellow-300 text-blue-700' : 'bg-blue-500 text-white hover:bg-blue-400'"
                                            class="flex-1 px-3 py-2 text-xs font-medium rounded transition-colors">
                                        <i class="fas fa-calculator mr-1"></i> BFO
                                    </button>
                                </div>
                                <hr class="border-blue-500 my-3">
                            </li>
                        @endif

                        {{-- PAMO Menu --}}
                        <div x-show="selectedDepartment === 'pamo'" x-transition>
                            @include('layouts.partials.pamo-menu')
                        </div>

                        {{-- BFO Menu --}}
                        <div x-show="selectedDepartment === 'bfo'" x-transition>
                            @include('layouts.partials.bfo-menu')
                        </div>

                        {{-- Default Menu for other roles --}}
                        @if(!in_array($userRole, ['pamo', 'bfo', 'administrator', 'developer']))
                            <li>
                                <x-end-user-nav-link
                                    href="{{ route('dashboard') }}"
                                    :active="request()->routeIs('dashboard')"
                                    icon="dashboard">
                                    Dashboard
                                </x-end-user-nav-link>
                            </li>
                            <li>
                                <x-end-user-nav-link
                                    href="{{ route('password.change') }}"
                                    :active="request()->routeIs('password.change')"
                                    icon="lock">
                                    Change Password
                                </x-end-user-nav-link>
                            </li>
                        @endif

                        {{-- Back to Main System Link --}}
                        @if(in_array($userRole, ['administrator', 'developer']))
                            <li class="pt-2 border-t border-blue-500 mt-2">
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

        <!-- Modal Content - Made Wider for Two Columns -->
        <div class="bg-white rounded-xl shadow-2xl overflow-hidden w-full max-w-6xl z-10 relative max-h-[90vh] overflow-y-auto">
            <!-- Modal Header -->
            <div class="relative bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-xl font-semibold text-white">My Profile & Assets</h3>
                        <p class="text-blue-100 text-sm">View your information and accountable assets</p>
                    </div>
                    <button @click="profileModalOpen = false"
                            class="text-white hover:text-yellow-300 transition-colors p-2 rounded-lg hover:bg-white/10">
                        <span class="material-symbols-sharp text-2xl">close</span>
                    </button>
                </div>
            </div>

            <!-- Modal Body - Two Grid Layout -->
            <div class="p-6 grid grid-cols-1 lg:grid-cols-2 gap-6">

                <!-- LEFT GRID: User Information -->
                <div class="space-y-6">
                    <div class="bg-gray-50 rounded-lg p-6">
                        <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                            <span class="material-symbols-sharp text-blue-600 mr-2">person</span>
                            Personal Information
                        </h4>

                        <form action="{{ route('user-profile-information.update') }}" method="POST"
                            class="space-y-4" enctype="multipart/form-data"
                            x-data="{
                                photoPreview: null,
                                photoName: null,
                                isSubmitting: false
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
                                            class="inline-flex items-center px-3 py-2 bg-white border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 cursor-pointer transition-colors">
                                            <span class="material-symbols-sharp text-sm mr-2">photo_camera</span>
                                            Change Photo
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Form Fields -->
                            <div class="space-y-4">
                                <!-- Name -->
                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                        <span class="flex items-center">
                                            <span class="material-symbols-sharp text-sm mr-2 text-gray-500">badge</span>
                                            Full Name
                                        </span>
                                    </label>
                                    <input type="text"
                                        name="name"
                                        id="name"
                                        value="{{ Auth::user()->name }}"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors"
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
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors"
                                        placeholder="Enter your email address">
                                </div>

                                <!-- Role (Read-only) -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        <span class="flex items-center">
                                            <span class="material-symbols-sharp text-sm mr-2 text-gray-500">work</span>
                                            Role
                                        </span>
                                    </label>
                                    <div class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-gray-600 text-sm">
                                        {{ $roleRecord ? $roleRecord->name : 'User' }}  <!-- Use $roleRecord instead -->
                                    </div>
                                </div>

                                <!-- Department -->
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
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors"
                                        placeholder="Enter your department">
                                </div>

                                <!-- ID Number (Read-only) -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        <span class="flex items-center">
                                            <span class="material-symbols-sharp text-sm mr-2 text-gray-500">numbers</span>
                                            ID Number
                                        </span>
                                    </label>
                                    <div class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-gray-600 text-sm">
                                        {{ Auth::user()->id_number ?? 'Not Set' }}
                                    </div>
                                </div>
                            </div>

                            <!-- Save Button -->
                            <div class="pt-4">
                                <button type="submit"
                                        :disabled="isSubmitting"
                                        class="w-full px-4 py-2 text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-blue-700 border border-transparent rounded-lg shadow-sm hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200">
                                    <span class="flex items-center justify-center">
                                        <span class="material-symbols-sharp text-sm mr-2">save</span>
                                        <span x-show="!isSubmitting">Update Profile</span>
                                        <span x-show="isSubmitting">Updating...</span>
                                    </span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- RIGHT GRID: Accountable Assets -->
                <div class="space-y-6">
                    <div class="bg-gray-50 rounded-lg p-6">
                        <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                            <span class="material-symbols-sharp text-blue-600 mr-2">inventory_2</span>
                            My Accountable Assets
                        </h4>

                        @php
                            // Get user's ID number and find matching assets
                            $userIdNumber = Auth::user()->id_number; // Changed from idnumber to id_number
                            $masterListUser = null;
                            $accountableAssets = collect();

                            if ($userIdNumber) {
                                // Match user's id_number with master list's employee_number
                                $masterListUser = \App\Models\PAMO\MasterList::where('employee_number', $userIdNumber)->first();
                                if ($masterListUser) {
                                    $accountableAssets = \App\Models\PAMO\PamoAssets::with(['category', 'location'])
                                        ->where('assigned_to', $masterListUser->id)
                                        ->get();
                                }
                            }
                        @endphp

                        @if($masterListUser && $accountableAssets->count() > 0)
                            <!-- Employee Info Display -->
                            <div class="bg-blue-50 rounded-lg p-3 mb-4 border border-blue-200">
                                <div class="flex items-center">
                                    <span class="material-symbols-sharp text-blue-600 mr-2">person</span>
                                    <div>
                                        <p class="text-sm font-medium text-blue-900">{{ $masterListUser->full_name }}</p>
                                        <p class="text-xs text-blue-700">Employee #{{ $masterListUser->employee_number }}</p>
                                        @if($masterListUser->department)
                                            <p class="text-xs text-blue-600">{{ $masterListUser->department }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Assets List -->
                            <div class="space-y-3 max-h-96 overflow-y-auto">
                                @foreach($accountableAssets as $asset)
                                    <div class="bg-white rounded-lg border border-gray-200 p-4 hover:shadow-sm transition-shadow">
                                        <div class="flex items-start space-x-3">
                                            <!-- Asset Icon -->
                                            <div class="flex-shrink-0 w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                                <span class="material-symbols-sharp text-blue-600 text-sm">
                                                    @if(str_contains(strtolower($asset->category->name ?? ''), 'laptop'))
                                                        laptop
                                                    @elseif(str_contains(strtolower($asset->category->name ?? ''), 'desktop'))
                                                        computer
                                                    @elseif(str_contains(strtolower($asset->category->name ?? ''), 'phone'))
                                                        smartphone
                                                    @elseif(str_contains(strtolower($asset->category->name ?? ''), 'tablet'))
                                                        tablet
                                                    @else
                                                        devices
                                                    @endif
                                                </span>
                                            </div>

                                            <!-- Asset Details -->
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-start justify-between">
                                                    <div>
                                                        <p class="text-sm font-medium text-gray-900 truncate">
                                                            {{ $asset->brand }} {{ $asset->model }}
                                                        </p>
                                                        <p class="text-xs text-gray-500">
                                                            SN: {{ $asset->serial_number }}
                                                        </p>
                                                        <p class="text-xs text-gray-500">
                                                            Tag: {{ $asset->property_tag_number }}
                                                        </p>
                                                        @if($asset->category)
                                                            <p class="text-xs text-blue-600 mt-1">
                                                                {{ $asset->category->name }}
                                                            </p>
                                                        @endif
                                                    </div>

                                                    <!-- Status Badge -->
                                                    <div class="flex-shrink-0">
                                                        @if($asset->status == 'in-use' || $asset->status == 'active')
                                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                                <span class="w-1.5 h-1.5 bg-green-400 rounded-full mr-1"></span>
                                                                {{ ucfirst($asset->status) }}
                                                            </span>
                                                        @elseif($asset->status == 'maintenance')
                                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                                <span class="w-1.5 h-1.5 bg-red-400 rounded-full mr-1"></span>
                                                                Maintenance
                                                            </span>
                                                        @else
                                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                                <span class="w-1.5 h-1.5 bg-gray-400 rounded-full mr-1"></span>
                                                                {{ ucfirst($asset->status) }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>

                                                <!-- Location -->
                                                @if($asset->location)
                                                    <div class="mt-2 flex items-center text-xs text-gray-500">
                                                        <span class="material-symbols-sharp text-xs mr-1">location_on</span>
                                                        {{ $asset->location->name }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <!-- Assets Summary -->
                            <div class="mt-4 pt-4 border-t border-gray-200">
                                <div class="grid grid-cols-2 gap-4 text-center">
                                    <div class="bg-white rounded-lg p-3 border">
                                        <div class="text-lg font-semibold text-blue-600">{{ $accountableAssets->count() }}</div>
                                        <div class="text-xs text-gray-500">Total Assets</div>
                                    </div>
                                    <div class="bg-white rounded-lg p-3 border">
                                        <div class="text-lg font-semibold text-green-600">
                                            {{ $accountableAssets->whereIn('status', ['in-use', 'active'])->count() }}
                                        </div>
                                        <div class="text-xs text-gray-500">Active</div>
                                    </div>
                                </div>
                            </div>

                        @elseif($userIdNumber && !$masterListUser)
                            <!-- User not found in master list -->
                            <div class="text-center py-8">
                                <span class="material-symbols-sharp text-4xl text-gray-300 mb-3 block">person_search</span>
                                <p class="text-sm text-gray-500 mb-2">ID Number not found in Master List</p>
                                <p class="text-xs text-gray-400">
                                    Your ID number ({{ $userIdNumber }}) is not registered in the master list.
                                    Please contact your administrator.
                                </p>
                            </div>

                        @elseif(!$userIdNumber)
                            <!-- No ID number set -->
                            <div class="text-center py-8">
                                <span class="material-symbols-sharp text-4xl text-gray-300 mb-3 block">badge</span>
                                <p class="text-sm text-gray-500 mb-2">No ID Number Set</p>
                                <p class="text-xs text-gray-400">
                                    Please contact your administrator to set up your ID number.
                                </p>
                            </div>

                        @else
                            <!-- No assets assigned -->
                            <div class="text-center py-8">
                                <span class="material-symbols-sharp text-4xl text-gray-300 mb-3 block">inventory_2</span>
                                <p class="text-sm text-gray-500 mb-2">No Assets Assigned</p>
                                <p class="text-xs text-gray-400">
                                    You currently have no accountable assets assigned to you.
                                </p>
                            </div>
                        @endif

                        <!-- Contact Admin Button -->
                        <div class="mt-4 pt-4 border-t border-gray-200">
                            <button type="button" class="w-full px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                                <span class="flex items-center justify-center">
                                    <span class="material-symbols-sharp text-sm mr-2">support_agent</span>
                                    Report Asset Issue
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                <div class="flex justify-end">
                    <button @click="profileModalOpen = false"
                            type="button"
                            class="px-6 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    @if(session('force_password_change') || (!Auth::user()->is_temporary_password_used && Auth::user()->temporary_password))
    <div id="passwordChangeModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black bg-opacity-75"
        x-data="{
            isSubmitting: false,
            currentPassword: '',
            newPassword: '',
            confirmPassword: '',
            errors: {},
            showCurrentPassword: false,
            showNewPassword: false,
            showConfirmPassword: false
        }"
        x-init="document.body.style.overflow = 'hidden'">

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

                <form id="passwordChangeForm"
                    action="{{ route('user-password.update') }}"
                    method="POST"
                    class="space-y-4"
                    @submit.prevent="
                        isSubmitting = true;
                        errors = {};

                        // Client-side validation
                        if (!currentPassword) {
                            errors.current_password = ['Current password is required'];
                            isSubmitting = false;
                            return;
                        }

                        if (!newPassword) {
                            errors.password = ['New password is required'];
                            isSubmitting = false;
                            return;
                        }

                        if (newPassword.length < 8) {
                            errors.password = ['Password must be at least 8 characters'];
                            isSubmitting = false;
                            return;
                        }

                        if (newPassword !== confirmPassword) {
                            errors.password_confirmation = ['Passwords do not match'];
                            isSubmitting = false;
                            return;
                        }

                        // Submit form using Jetstream's endpoint
                        fetch($el.action, {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                current_password: currentPassword,
                                password: newPassword,
                                password_confirmation: confirmPassword
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.errors) {
                                errors = data.errors;
                                isSubmitting = false;
                            } else {
                                // Success - update the user's temporary password status
                                fetch('/user/mark-password-changed', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                        'Accept': 'application/json'
                                    }
                                }).then(() => {
                                    window.location.reload();
                                });
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            isSubmitting = false;
                            alert('An error occurred. Please try again.');
                        });
                    ">
                    @csrf
                    @method('PUT')

                    <!-- Current Password -->
                    <div>
                        <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">
                            <span class="flex items-center">
                                <span class="material-symbols-sharp text-sm mr-2 text-gray-500">key</span>
                                Current Password
                            </span>
                        </label>
                        <div class="relative">
                            <input
                                :type="showCurrentPassword ? 'text' : 'password'"
                                x-model="currentPassword"
                                id="current_password"
                                class="w-full px-3 py-2 pr-10 border border-gray-300 rounded-lg shadow-sm focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors"
                                placeholder="Enter your current password"
                                :class="errors.current_password ? 'border-red-500' : 'border-gray-300'">
                            <button type="button"
                                    @click="showCurrentPassword = !showCurrentPassword"
                                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600">
                                <span class="material-symbols-sharp text-sm" x-text="showCurrentPassword ? 'visibility_off' : 'visibility'"></span>
                            </button>
                        </div>
                        <div x-show="errors.current_password" class="mt-1 text-sm text-red-600" x-text="errors.current_password ? errors.current_password[0] : ''"></div>
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
                                :type="showNewPassword ? 'text' : 'password'"
                                x-model="newPassword"
                                id="password"
                                class="w-full px-3 py-2 pr-10 border border-gray-300 rounded-lg shadow-sm focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors"
                                placeholder="Enter your new password"
                                :class="errors.password ? 'border-red-500' : 'border-gray-300'">
                            <button type="button"
                                    @click="showNewPassword = !showNewPassword"
                                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600">
                                <span class="material-symbols-sharp text-sm" x-text="showNewPassword ? 'visibility_off' : 'visibility'"></span>
                            </button>
                        </div>
                        <div x-show="errors.password" class="mt-1 text-sm text-red-600" x-text="errors.password ? errors.password[0] : ''"></div>
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
                                :type="showConfirmPassword ? 'text' : 'password'"
                                x-model="confirmPassword"
                                id="password_confirmation"
                                class="w-full px-3 py-2 pr-10 border border-gray-300 rounded-lg shadow-sm focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors"
                                placeholder="Confirm your new password"
                                :class="errors.password_confirmation ? 'border-red-500' : 'border-gray-300'">
                            <button type="button"
                                    @click="showConfirmPassword = !showConfirmPassword"
                                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600">
                                <span class="material-symbols-sharp text-sm" x-text="showConfirmPassword ? 'visibility_off' : 'visibility'"></span>
                            </button>
                        </div>
                        <div x-show="errors.password_confirmation" class="mt-1 text-sm text-red-600" x-text="errors.password_confirmation ? errors.password_confirmation[0] : ''"></div>
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
                                :disabled="isSubmitting"
                                class="w-full px-4 py-2 text-sm font-medium text-white bg-gradient-to-r from-red-600 to-red-700 border border-transparent rounded-lg shadow-sm hover:from-red-700 hover:to-red-800 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
                            <span class="flex items-center justify-center">
                                <span class="material-symbols-sharp text-sm mr-2">save</span>
                                <span x-show="!isSubmitting">Change Password</span>
                                <span x-show="isSubmitting">Changing Password...</span>
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
    @endif
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
