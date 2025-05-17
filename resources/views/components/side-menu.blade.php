<nav id="" class="bg-gray-200 dark:bg-gray-800 w-full md:w-64 p-4 hidden md:block overflow-y-auto">
    <ul>
        <li class="mb-2">
            <x-nav-link wire:navigate href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')" class="block p-2 rounded hover:bg-gray-300 dark:hover:bg-gray-700">
                <div class="flex gap-3 items-center">
                    <span class="material-symbols-sharp"> dashboard </span>
                    <h3>Introduction</h3>
                </div>
            </x-nav-link>
        </li>
        <li class="mb-2">
            <x-nav-link wire:navigate href="{{ route('itss.manual') }}" :active="request()->routeIs('itss.manual')" class="block p-2 rounded hover:bg-gray-300 dark:hover:bg-gray-700">
                <div class="flex gap-3 items-center">
                    <span class="material-symbols-sharp"> developer_guide </span>
                    <h3>ITSSMO Manual</h3>
                </div>
            </x-nav-link>
        </li>
        <h1 class="text-xs text-gray-500">Brainstorm</h1>
        <li class="mb-2">
            <div x-data="{ open: {{ request()->routeIs('examination.subject') || request()->routeIs('examination.coordinator') ? 'true' : 'false' }} }" class="relative p-2 rounded">
                <a href="" @click.prevent="open = !open" :class="{'rounded bg-gray-300 dark:bg-gray-700': open}">
                    <div class="flex gap-3">
                        <span class="material-symbols-sharp"> summarize </span>
                        <h3>Examination</h3>
                        <span class="material-symbols-sharp transform transition-transform duration-300" :class="{'rotate-180': open}"> expand_more </span>
                    </div>
                </a>
                <div x-show="open" x-collapse class="flex flex-col space-y-2 pl-10 mt-2">
                    <x-nav-link wire:navigate href="{{ route('examination.subject') }}" :active="request()->routeIs('examination.subject')" class="block p-2 rounded hover:bg-gray-300 dark:hover:bg-gray-700">
                        Admin
                    </x-nav-link>
                    <x-nav-link wire:navigate href="{{ route('examination.coordinator') }}" :active="request()->routeIs('examination.coordinator')" class="block p-2 rounded hover:bg-gray-300 dark:hover:bg-gray-700">
                        Coordinator
                    </x-nav-link>
                </div>
            </div>
        </li>
        <h1 class="text-xs text-gray-500">Department</h1>
        <li class="mb-2">
           <x-nav-link wire:navigate href="{{ route('pamo.dashboard') }}" :active="request()->routeIs('pamo.*')"
                    class="block p-2 rounded hover:bg-gray-300 dark:hover:bg-gray-700">
                    <div class="flex gap-3 items-center">
                        <span class="material-symbols-sharp"> inventory </span>
                        <h3>PAMO</h3>
                       @if(auth()->user()->isDeveloper())
                            <span class="text-xs bg-blue-500 text-white px-1 rounded">Dev</span>
                       @endif
                    </div>
                </x-nav-link>
        </li>
        <h1 class="text-xs text-gray-500">Transaction's</h1>
        <li class="mb-2">
            <x-nav-link wire:navigate href="{{ route('borrower.form') }}" :active="request()->routeIs('borrower.form')" class="block p-2 rounded hover:bg-gray-300 dark:hover:bg-gray-700">
                <div class="flex gap-3 items-center">
                    <span class="material-symbols-sharp"> handshake </span>
                    <h3>Borrower's</h3>
                </div>
            </x-nav-link>
        </li>
        <li class="mb-2">
            <x-nav-link wire:navigate href="{{ route('assets.view') }}" :active="request()->routeIs('assets.view') || request()->routeIs('assets.category') || request()->routeIs('assets.consumable')" class="block p-2 rounded hover:bg-gray-300 dark:hover:bg-gray-700">
                <div class="flex gap-3 items-center">
                    <span class="material-symbols-sharp"> category </span>
                    <h3> Assets</h3>
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
        <h1 class="text-xs text-gray-500">Record's</h1>

        <li class="mb-2">
            <x-nav-link wire:navigate href="{{ route('borrowers.logs') }}" :active="request()->routeIs('borrowers.logs') || request()->routeIs('borrowers.return')" class="block p-2 rounded hover:bg-gray-300 dark:hover:bg-gray-700">
                <div class="flex gap-3 items-center">
                    <span class="material-symbols-sharp"> sync_alt </span>
                    <h3> Borrower's Logs </h3>
                </div>
            </x-nav-link>
        </li>
        <li class="mb-2">
            <x-nav-link wire:navigate href="{{ route('falco.records') }}" :active="request()->routeIs('staff.records')" class="block p-2 rounded hover:bg-gray-300 dark:hover:bg-gray-700">
                <div class="flex gap-3 items-center">
                    <span class="material-symbols-sharp"> badge </span>
                    <h3> Falco Records </h3>
                </div>
            </x-nav-link>
        </li>
        <h1 class="text-xs text-gray-500">Admin Access</h1>
        <li class="mb-2">
            <x-nav-link wire:navigate href="{{ route('controlPanel.admin') }}" :active="request()->routeIs('controlPanel.admin')" class="block p-2 rounded hover:bg-gray-300 dark:hover:bg-gray-700">
                <div class="flex gap-3 items-center">
                    <span class="material-symbols-sharp"> admin_panel_settings
                    </span>
                    <h3> Control Panel </h3>
                </div>
            </x-nav-link>
        </li>
    </ul>
</nav>
