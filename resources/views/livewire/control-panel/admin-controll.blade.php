<div class="container mx-auto">
    <div class="mb-4">
        <h1 class="text-2xl font-semibold tracking-tight">Control Panel</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400">{{ session('breadcrumb') }}</p>
    </div>
    <div class="bg-white dark:bg-gray-800 shadow-sm ring-1 ring-gray-200 dark:ring-gray-700 rounded-lg p-6">
            <!-- CSAT Enforcement Controls -->
            <div class="mb-6 flex items-center justify-between rounded-lg border border-gray-200 dark:border-gray-700 p-4 bg-gray-50 dark:bg-gray-900">
                <div>
                    <div class="text-sm font-medium text-gray-800 dark:text-gray-100">End-User CSAT Enforcement</div>
                    @if($csatEnforceSince)
                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">Active since {{ $csatEnforceSince }} · Pending users: <span class="font-semibold">{{ $csatPending }}</span></div>
                    @else
                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">Currently disabled</div>
                    @endif
                </div>
                <div class="flex items-center gap-3">
                    @if(!$csatEnforceSince)
                        <button wire:click="enableCsatEnforcement" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-md text-white bg-blue-600 hover:bg-blue-700 text-sm">
                            <span class="material-symbols-sharp text-sm">play_arrow</span> Require CSAT now
                        </button>
                    @else
                        <button wire:click="disableCsatEnforcement" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-md text-white bg-gray-600 hover:bg-gray-700 text-sm">
                            <span class="material-symbols-sharp text-sm">stop_circle</span> Disable enforcement
                        </button>
                    @endif
                </div>
            </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            <!-- Users -->
            <x-nav-link wire:navigate href="{{ route('controlPanel.user') }}" class="group block rounded-lg border border-gray-200 dark:border-gray-700 p-5 bg-gray-50 dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700 hover:shadow-sm transition">
                <div class="flex items-center gap-4">
                    <span class="material-symbols-sharp text-blue-600 dark:text-blue-400">person_add</span>
                    <div>
                        <h3 class="font-medium">Users Controller</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Manage users and profiles</p>
                    </div>
                </div>
            </x-nav-link>
            <!-- Roles -->
            <x-nav-link wire:navigate href="{{ route('controlPanel.roles') }}" class="group block rounded-lg border border-gray-200 dark:border-gray-700 p-5 bg-gray-50 dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700 hover:shadow-sm transition">
                <div class="flex items-center gap-4">
                    <span class="material-symbols-sharp text-emerald-600 dark:text-emerald-400">admin_panel_settings</span>
                    <div>
                        <h3 class="font-medium">Roles</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Permissions and access</p>
                    </div>
                </div>
            </x-nav-link>

            <!-- Departments -->
            <x-nav-link wire:navigate href="{{ route('controlPanel.departments') }}" class="group block rounded-lg border border-gray-200 dark:border-gray-700 p-5 bg-gray-50 dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700 hover:shadow-sm transition">
                <div class="flex items-center gap-4">
                    <span class="material-symbols-sharp text-orange-600 dark:text-orange-400">apartment</span>
                    <div>
                        <h3 class="font-medium">Departments</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Organize by unit</p>
                    </div>
                </div>
            </x-nav-link>

            <!-- Menus -->
            <x-nav-link wire:navigate href="{{ route('controlPanel.menus') }}" class="group block rounded-lg border border-gray-200 dark:border-gray-700 p-5 bg-gray-50 dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700 hover:shadow-sm transition">
                <div class="flex items-center gap-4">
                    <span class="material-symbols-sharp text-indigo-600 dark:text-indigo-400">menu</span>
                    <div>
                        <h3 class="font-medium">Menu Controller</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Navigation and sections</p>
                    </div>
                </div>
            </x-nav-link>

            <!-- Master Files Dashboard -->
            <x-nav-link wire:navigate href="{{ route('master-file.dashboard') }}" class="group block rounded-lg border border-gray-200 dark:border-gray-700 p-5 bg-gray-50 dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700 hover:shadow-sm transition">
                <div class="flex items-center gap-4">
                    <span class="material-symbols-sharp text-cyan-600 dark:text-cyan-400">folder_managed</span>
                    <div>
                        <h3 class="font-medium">Master Files</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Browse and manage files</p>
                    </div>
                </div>
            </x-nav-link>

            <!-- Uploads -->
            <x-nav-link wire:navigate href="{{ route('master-file.upload') }}" class="group block rounded-lg border border-gray-200 dark:border-gray-700 p-5 bg-gray-50 dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700 hover:shadow-sm transition">
                <div class="flex items-center gap-4">
                    <span class="material-symbols-sharp text-fuchsia-600 dark:text-fuchsia-400">upload_file</span>
                    <div>
                        <h3 class="font-medium">Uploads</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Add new versions</p>
                    </div>
                </div>
            </x-nav-link>

            <!-- Analytics -->
            <x-nav-link wire:navigate href="{{ route('master-file.analytics') }}" class="group block rounded-lg border border-gray-200 dark:border-gray-700 p-5 bg-gray-50 dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700 hover:shadow-sm transition">
                <div class="flex items-center gap-4">
                    <span class="material-symbols-sharp text-rose-600 dark:text-rose-400">query_stats</span>
                    <div>
                        <h3 class="font-medium">Analytics</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Usage insights</p>
                    </div>
                </div>
            </x-nav-link>

            <!-- Reports: Surveys -->
            <x-nav-link wire:navigate href="{{ route('controlPanel.reports.surveys') }}" class="group block rounded-lg border border-gray-200 dark:border-gray-700 p-5 bg-gray-50 dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700 hover:shadow-sm transition">
                <div class="flex items-center gap-4">
                    <span class="material-symbols-sharp text-yellow-600 dark:text-yellow-400">star_rate</span>
                    <div>
                        <h3 class="font-medium">Reports · Surveys</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Customer satisfaction stats</p>
                    </div>
                </div>
            </x-nav-link>
        </div>
    </div>
</div>

