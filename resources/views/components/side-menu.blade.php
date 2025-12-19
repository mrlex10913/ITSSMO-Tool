<nav id="" class="w-full md:w-64 p-0 hidden md:flex flex-col overflow-y-auto bg-white dark:bg-gray-800 shadow-lg border-r border-gray-200 dark:border-gray-700">
    <ul class="p-4 space-y-2 flex-1">
        <li class="mb-2">
            <x-nav-link wire:navigate href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')" class="flex items-center gap-3 px-3 py-2 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg transition-colors">
                <div class="flex gap-3 items-center">
                    <x-heroicon name="chart-bar" class="w-5 h-5" />
                    <h3>Dashboard</h3>
                </div>
            </x-nav-link>
        </li>

        <h3 class="px-3 text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-2">Department</h3>
        <li class="mb-2">
            @php $user = auth()->user(); @endphp
            @if($user && $user->hasRole(['administrator','developer','pamo']))
                <x-nav-link wire:navigate href="{{ route('pamo.dashboard') }}" :active="request()->routeIs('pamo.*')"
                        class="flex items-center justify-between px-3 py-2 text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg transition-colors">
                        <div class="flex gap-3 items-center">
                            <x-heroicon name="archive-box" class="w-5 h-5" />
                            <h3 class="text-sm">PAMO</h3>
                            @if($user->isDeveloper())
                                <span class="px-2 py-1 text-xs bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 rounded-full">Dev</span>
                            @endif
                        </div>
                </x-nav-link>
            @endif

            @if($user && $user->hasRole(['administrator','developer','bfo']))
                <x-nav-link wire:navigate href="{{ route('bfo.dashboard') }}" :active="request()->routeIs('bfo.*')"
                        class="flex items-center justify-between px-3 py-2 text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg transition-colors">
                        <div class="flex gap-3 items-center">
                            <x-heroicon name="archive-box" class="w-5 h-5" />
                            <h3 class="text-sm">BFO</h3>
                            @if($user->isDeveloper())
                                <span class="px-2 py-1 text-xs bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 rounded-full">Dev</span>
                            @endif
                        </div>
                </x-nav-link>
            @endif
        </li>
        <h3 class="px-3 text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-2">Transaction's</h3>
        <li class="mb-2">
            <x-nav-link wire:navigate href="{{ route('borrower.form') }}" :active="request()->routeIs('borrower.form')" class="flex items-center gap-3 px-3 py-2 text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg transition-colors">
                <div class="flex gap-3 items-center">
                    <x-heroicon name="handshake" class="w-5 h-5" />
                    <h3 class="text-sm">Borrower's</h3>
                </div>
            </x-nav-link>
        </li>
        <li class="mb-2">
            <x-nav-link wire:navigate href="{{ route('assets.view') }}" :active="request()->routeIs('assets.view') || request()->routeIs('assets.category') || request()->routeIs('assets.consumable')" class="flex items-center gap-3 px-3 py-2 text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg transition-colors">
                <div class="flex gap-3 items-center">
                    <x-heroicon name="squares-2x2" class="w-5 h-5" />
                    <h3 class="text-sm">Assets</h3>
                </div>
            </x-nav-link>
        </li>
        {{-- <li class="mb-2">
            <x-nav-link wire:navigate href="{{ route('reservation.form') }}" :active="request()->routeIs('reservation.form')" class="block p-2 rounded hover:bg-gray-300 dark:hover:bg-gray-700">
                <div class="flex gap-3 items-center">
                    <span class="material-symbols-sharp"> book_online </span>
                    <h3>BRF-Reserve</h3>
                </div>
            </x-nav-link>
        </li> --}}
        {{-- <li class="mb-2">
            <x-nav-link wire:navigate href="{{ route('consumable.tracker') }}" :active="request()->routeIs('consumable.tracker')" class="block p-2 rounded hover:bg-gray-300 dark:hover:bg-gray-700">
                <div class="flex gap-3 items-center">
                    <span class="material-symbols-sharp"> inventory_2 </span>
                    <h3> Consumable Tracker</h3>
                </div>
            </x-nav-link>
        </li> --}}
        {{-- <li class="mb-2">
            <x-nav-link wire:navigate href="{{ route('asset.form') }}" :active="request()->routeIs('asset.form')" class="block p-2 rounded hover:bg-gray-300 dark:hover:bg-gray-700">
                <div class="flex gap-3 items-center">
                    <span class="material-symbols-sharp"> partner_exchange </span>
                    <h3> Asset Transfer</h3>
                </div>
            </x-nav-link>
        </li> --}}
    <h3 class="px-3 text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-2">Record's</h3>

        <li class="mb-2">
            <x-nav-link wire:navigate href="{{ route('borrowers.logs') }}" :active="request()->routeIs('borrowers.logs') || request()->routeIs('borrowers.return')" class="flex items-center gap-3 px-3 py-2 text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg transition-colors">
                <div class="flex gap-3 items-center">
                    <x-heroicon name="arrow-path" class="w-5 h-5" />
                    <h3 class="text-sm">Borrower's Logs</h3>
                </div>
            </x-nav-link>
        </li>
        @if(\Illuminate\Support\Facades\Route::has('falco.records'))
        <li class="mb-2">
            <x-nav-link wire:navigate href="{{ route('falco.records') }}" :active="request()->routeIs('falco.records')" class="flex items-center gap-3 px-3 py-2 text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg transition-colors">
                <div class="flex gap-3 items-center">
                    <x-heroicon name="identification" class="w-5 h-5" />
                    <h3 class="text-sm">Falco Records</h3>
                </div>
            </x-nav-link>
        </li>
        @endif
        <!-- Master File Archive Dropdown -->
        <li class="mb-2">
            <div x-data="{ open: {{ request()->routeIs('master-file.*') ? 'true' : 'false' }} }" class="relative p-2 rounded">
                <a href="" @click.prevent="open = !open" :class="{'rounded bg-gray-300 dark:bg-gray-700': open}">
                    <div class="flex gap-3 items-center">
                        <x-heroicon name="folder" class="w-5 h-5" />
                        <h3>Master File Archive</h3>
                        <span x-bind:class="{'rotate-180': open}" class="transform transition-transform duration-300">
                            <x-heroicon name="chevron-down" class="w-5 h-5" />
                        </span>
                    </div>
                </a>
                <div x-show="open" x-collapse class="flex flex-col space-y-2 pl-10 mt-2">
                    <x-nav-link wire:navigate href="{{ route('master-file.dashboard') }}" :active="request()->routeIs('master-file.dashboard')" class="block p-2 rounded hover:bg-gray-300 dark:hover:bg-gray-700">
                        <div class="flex gap-2 items-center">
                            <x-heroicon name="chart-bar" class="w-4 h-4" />
                            <span class="text-sm">Dashboard</span>
                        </div>
                    </x-nav-link>
                    <x-nav-link wire:navigate href="{{ route('master-file.categories') }}" :active="request()->routeIs('master-file.categories')" class="block p-2 rounded hover:bg-gray-300 dark:hover:bg-gray-700">
                        <div class="flex gap-2 items-center">
                            <x-heroicon name="squares-2x2" class="w-4 h-4" />
                            <span class="text-sm">Categories</span>
                        </div>
                    </x-nav-link>
                    <x-nav-link wire:navigate href="{{ route('master-file.upload') }}" :active="request()->routeIs('master-file.upload')" class="block p-2 rounded hover:bg-gray-300 dark:hover:bg-gray-700">
                        <div class="flex gap-2 items-center">
                            <x-heroicon name="arrow-up-tray" class="w-4 h-4" />
                            <span class="text-sm">Upload Document</span>
                        </div>
                    </x-nav-link>
                    <x-nav-link wire:navigate href="{{ route('master-file.search') }}" :active="request()->routeIs('master-file.search')" class="block p-2 rounded hover:bg-gray-300 dark:hover:bg-gray-700">
                        <div class="flex gap-2 items-center">
                            <x-heroicon name="magnifying-glass" class="w-4 h-4" />
                            <span class="text-sm">Search Archive</span>
                        </div>
                    </x-nav-link>
                    <x-nav-link wire:navigate href="{{ route('master-file.versions') }}" :active="request()->routeIs('master-file.versions')" class="block p-2 rounded hover:bg-gray-300 dark:hover:bg-gray-700">
                        <div class="flex gap-2 items-center">
                            <x-heroicon name="arrow-path" class="w-4 h-4" />
                            <span class="text-sm">Version Control</span>
                        </div>
                    </x-nav-link>
                    <x-nav-link wire:navigate href="{{ route('master-file.analytics') }}" :active="request()->routeIs('master-file.analytics')" class="block p-2 rounded hover:bg-gray-300 dark:hover:bg-gray-700">
                        <div class="flex gap-2 items-center">
                            <x-heroicon name="chart-bar" class="w-4 h-4" />
                            <span class="text-sm">Analytics</span>
                        </div>
                    </x-nav-link>
                </div>
            </div>
        </li>
    <h3 class="px-3 text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-2">Admin Access</h3>
        <li class="mb-2">
            <x-nav-link wire:navigate href="{{ route('itss.sla.policies') }}" :active="request()->routeIs('itss.sla.policies')" class="block p-2 rounded hover:bg-gray-300 dark:hover:bg-gray-700">
                <div class="flex gap-3 items-center">
                    <x-heroicon name="clock" class="w-5 h-5" />
                    <h3> SLA Policies </h3>
                    @if(auth()->user()->isDeveloper())
                        <span class="text-xs bg-blue-500 text-white px-1 rounded">Dev</span>
                    @endif
                </div>
            </x-nav-link>
        </li>
        <li class="mb-2">
            <x-nav-link wire:navigate href="{{ route('controlPanel.admin') }}" :active="request()->routeIs('controlPanel.admin')" class="block p-2 rounded hover:bg-gray-300 dark:hover:bg-gray-700">
                <div class="flex gap-3 items-center">
                    <x-heroicon name="shield-check" class="w-5 h-5" />
                    <h3> Control Panel </h3>
                </div>
            </x-nav-link>
        </li>
    </ul>
</nav>
