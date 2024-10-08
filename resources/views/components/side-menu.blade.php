<aside class="py-4 ml-4 h-screen">
    <div class="bg-slate-100 dark:bg-gray-800 flex flex-col items-start mt-2 max-h-[86vh] rounded-lg overflow-y-auto p-6 gap-6 shadow-md">
        <x-nav-link wire:navigate href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')" class="flex gap-3 items-center text-slate-900 dark:text-gray-100 font-medium text-sm transition-all hover:ml-[1rem] hover:text-color-primary">
            <span class="material-symbols-sharp"> dashboard </span>
            <h3>Introduction</h3>
        </x-nav-link>
        <h1 class="text-xs text-gray-500">Manual's</h1>
        <x-nav-link wire:navigate href="{{ route('itss.manual') }}" :active="request()->routeIs('itss.manual')" class="flex gap-3 items-center text-slate-900 dark:text-gray-100 font-medium text-sm transition-all hover:ml-[1rem] hover:text-color-primary">
            <span class="material-symbols-sharp"> developer_guide </span>
            <h3>ITSSMO Manual</h3>
        </x-nav-link>
        {{-- <div x-data="{ open: {{ request()->routeIs('examination.subject') || request()->routeIs('examination.questions') ? 'true' : 'false' }} }" class="relative">
            <x-nav-link @click.prevent="open = !open" :class="{ 'text-color-primary': open }" class="flex gap-3 items-center text-slate-900 dark:text-gray-100 font-medium text-sm transition-all cursor-pointer">
                <span class="material-symbols-sharp"> group </span>
                <h3>Examination</h3>
                <span class="material-symbols-sharp transform transition-transform duration-300" :class="{'rotate-180': open}"> expand_more </span>
            </x-nav-link>
            <div x-show="open" x-collapse class="mt-2 pl-10 space-y-2 flex flex-col">
                <x-nav-link wire:navigate href="{{ route('examination.subject') }}" :active="request()->routeIs('examination.subject')" class="block text-sm text-gray-700 dark:text-gray-200 hover:text-color-primary">
                    Subject's
                </x-nav-link>
                <x-nav-link wire:navigate href="{{ route('examination.questions') }}" :active="request()->routeIs('examination.questions')" class="block text-sm text-gray-700 dark:text-gray-200 hover:text-color-primary">
                    Question's
                </x-nav-link>
            </div>
        </div> --}}
        <h1 class="text-xs text-gray-500">Brainstorm</h1>
        <div x-data="{ open: {{ request()->routeIs('examination.subject') || request()->routeIs('examination.coordinator') ? 'true' : 'false' }} }" class="relative">
            <x-nav-link @click.prevent="open = !open" :class="{ 'text-color-primary': open }" class="flex gap-3 items-center text-slate-900 dark:text-gray-100 font-medium text-sm transition-all cursor-pointer">
                <span class="material-symbols-sharp"> summarize </span>
                <h3>Examination</h3>
                <span class="material-symbols-sharp transform transition-transform duration-300" :class="{'rotate-180': open}"> expand_more </span>
            </x-nav-link>
            <div x-show="open" x-collapse class="mt-2 pl-10 space-y-2 flex flex-col">
                <x-nav-link wire:navigate href="{{ route('examination.subject') }}" :active="request()->routeIs('examination.subject')" class="block text-sm text-gray-700 dark:text-gray-200 hover:text-color-primary">
                    Admin
                </x-nav-link>
                <x-nav-link wire:navigate href="{{ route('examination.coordinator') }}" :active="request()->routeIs('examination.coordinator')" class="block text-sm text-gray-700 dark:text-gray-200 hover:text-color-primary">
                    Coordinator
                </x-nav-link>
            </div>
        </div>
        {{-- Transactions --}}
        <h1 class="text-xs text-gray-500">Transaction's</h1>
        {{-- <hr class=" text-gray-600"> --}}
        <x-nav-link wire:navigate href="{{ route('borrower.form') }}" :active="request()->routeIs('borrower.form')" class="flex gap-3 items-center text-slate-900 dark:text-gray-100 font-medium text-sm transition-all hover:ml-[1rem] hover:text-color-primary">
            <span class="material-symbols-sharp">
                handshake
            </span>
            <h3>Borrower's</h3>
        </x-nav-link>
        <x-nav-link wire:navigate href="{{ route('asset.form') }}" :active="request()->routeIs('asset.form')" class="flex gap-3 items-center text-slate-900 dark:text-gray-100 font-medium text-sm transition-all hover:ml-[1rem] hover:text-color-primary">
            <span class="material-symbols-sharp"> partner_exchange </span>
            <h3>Asset's Transfer</h3>
        </x-nav-link>
        <x-nav-link wire:navigate href="{{ route('consumable.tracker') }}" :active="request()->routeIs('consumable.tracker')" class="flex gap-3 items-center text-slate-900 dark:text-gray-100 font-medium text-sm transition-all hover:ml-[1rem] hover:text-color-primary">
            <span class="material-symbols-sharp">
                inventory_2
                </span>
            <h3>Consumable Tracker</h3>
        </x-nav-link>

        <h1 class="text-xs text-gray-500">Record's</h1>
        <x-nav-link wire:navigate href="{{ route('assets.view') }}" :active="request()->routeIs('assets.view') || request()->routeIs('assets.category') || request()->routeIs('assets.consumable')" class="flex gap-3 items-center text-slate-900 dark:text-gray-100 font-medium text-sm transition-all hover:ml-[1rem] hover:text-color-primary">
            <span class="material-symbols-sharp"> category </span>
            <h3>Assets</h3>
        </x-nav-link>
        <x-nav-link wire:navigate href="{{ route('borrowers.logs') }}" :active="request()->routeIs('borrowers.logs') || request()->routeIs('borrowers.return')" class="flex gap-3 items-center text-slate-900 dark:text-gray-100 font-medium text-sm transition-all hover:ml-[1rem] hover:text-color-primary">
            <span class="material-symbols-sharp"> sync_alt </span>
            <h3>Borrower's Logs</h3>
        </x-nav-link>
        <x-nav-link wire:navigate href="{{ route('falco.records') }}" :active="request()->routeIs('staff.records')" class="flex gap-3 items-center text-slate-900 dark:text-gray-100 font-medium text-sm transition-all hover:ml-[1rem] hover:text-color-primary">
            <span class="material-symbols-sharp"> badge </span>
            <h3>Falco Records</h3>
        </x-nav-link>
        <x-nav-link wire:navigate href="{{ route('student.records') }}" :active="request()->routeIs('student.records')" class="flex gap-3 items-center text-slate-900 dark:text-gray-100 font-medium text-sm transition-all hover:ml-[1rem] hover:text-color-primary">
            <span class="material-symbols-sharp"> group </span>
            <h3>Student Records</h3>
        </x-nav-link>
        <x-nav-link wire:navigate href="{{ route('falco') }}" :active="request()->routeIs('falco')" class="flex gap-3 items-center text-slate-900 dark:text-gray-100 font-medium text-sm transition-all hover:ml-[1rem] hover:text-color-primary">
            <span class="material-symbols-sharp"> group </span>
            <h3>test</h3>
        </x-nav-link>
        {{-- <div x-data="{ open: {{ request()->routeIs('borrower.form') || request()->routeIs('borrower.tracker') ? 'true' : 'false' }} }" class="relative">
            <x-nav-link @click.prevent="open = !open" :class="{ 'text-color-primary': open }" class="flex gap-3 items-center text-slate-900 dark:text-gray-100 font-medium text-sm transition-all cursor-pointer">
                <span class="material-symbols-sharp"> approval_delegation </span>
                <h3>Borrower's</h3>
                <span class="material-symbols-sharp transform transition-transform duration-300" :class="{'rotate-180': open}"> expand_more </span>
            </x-nav-link>
            <div x-show="open" x-collapse class="mt-2 pl-10 space-y-2 flex flex-col">
                <x-nav-link wire:navigate href="{{ route('borrower.form') }}" :active="request()->routeIs('borrower.form')" class="block text-sm text-gray-700 dark:text-gray-200 hover:text-color-primary">
                    Form
                </x-nav-link>
                <x-nav-link wire:navigate href="{{ route('borrower.tracker') }}" :active="request()->routeIs('borrower.tracker')" class="block text-sm text-gray-700 dark:text-gray-200 hover:text-color-primary">
                    Tracker
                </x-nav-link>
            </div>
        </div> --}}
        {{-- dropdown menu --}}
        {{-- <div x-data="{ open: {{ request()->routeIs('borrower.form') || request()->routeIs('borrower.tracker') ? 'true' : 'false' }} }" class="relative">
            <x-nav-link @click.prevent="open = !open" :class="{ 'text-color-primary': open }" class="flex gap-3 items-center text-slate-900 dark:text-gray-100 font-medium text-sm transition-all cursor-pointer">
                <span class="material-symbols-sharp"> approval_delegation </span>
                <h3>Borrower's</h3>
                <span class="material-symbols-sharp transform transition-transform duration-300" :class="{'rotate-180': open}"> expand_more </span>
            </x-nav-link>
            <div x-show="open" x-collapse class="mt-2 pl-10 space-y-2 flex flex-col">
                <x-nav-link wire:navigate href="{{ route('borrower.form') }}" :active="request()->routeIs('borrower.form')" class="block text-sm text-gray-700 dark:text-gray-200 hover:text-color-primary">
                    Form
                </x-nav-link>
                <x-nav-link wire:navigate href="{{ route('borrower.tracker') }}" :active="request()->routeIs('borrower.tracker')" class="block text-sm text-gray-700 dark:text-gray-200 hover:text-color-primary">
                    Tracker
                </x-nav-link>
            </div>
        </div> --}}

        {{-- <x-nav-link wire:navigate href="{{ route('userpanel') }}" :active="request()->routeIs('userpanel')" class="flex gap-3 items-center text-slate-900 dark:text-gray-100 font-medium text-sm transition-all hover:ml-[1rem] hover:text-color-primary">
            <span class="material-symbols-sharp"> group </span>
            <h3>User's</h3>
        </x-nav-link> --}}

        {{-- <x-nav-link wire:navigate href="{{ route('peripherals') }}" :active="request()->routeIs('peripherals')" class="flex gap-3 items-center text-slate-900 dark:text-gray-100 font-medium text-sm transition-all hover:ml-[1rem] hover:text-color-primary">
            <span class="material-symbols-sharp"> devices </span>
            <h3>Computer Peripherals</h3>
        </x-nav-link> --}}

        {{-- <div x-data="{ open: {{ request()->routeIs('idprod.idform') || request()->routeIs('idprod.idlist') ? 'true' : 'false' }} }" class="relative">
            <x-nav-link @click.prevent="open = !open" :class="{ 'text-color-primary': open }" class="flex gap-3 items-center text-slate-900 dark:text-gray-100 font-medium text-sm transition-all cursor-pointer">
                <span class="material-symbols-sharp"> approval_delegation </span>
                <h3>ID Production</h3>
                <span class="material-symbols-sharp transform transition-transform duration-300" :class="{'rotate-180': open}"> expand_more </span>
            </x-nav-link>
            <div x-show="open" x-collapse class="mt-2 pl-10 space-y-2 flex flex-col">
                <x-nav-link wire:navigate href="{{ route('idprod.idform') }}" :active="request()->routeIs('idprod.idform')" class="block text-sm text-gray-700 dark:text-gray-200 hover:text-color-primary">
                    Form
                </x-nav-link>
                <x-nav-link wire:navigate href="{{ route('idprod.idlist') }}" :active="request()->routeIs('idprod.idlist')" class="block text-sm text-gray-700 dark:text-gray-200 hover:text-color-primary">
                    List
                </x-nav-link>
            </div>
        </div> --}}
        {{-- <a href="#" class="flex gap-3 items-center text-slate-900 dark:text-gray-100 font-medium text-sm transition-all hover:ml-[1rem] hover:text-color-primary">
            <span class="material-symbols-sharp"> insights </span>
            <h3>Analytics</h3>
        </a>
        <a href="#" class="flex gap-3 items-center text-slate-900 dark:text-gray-100 font-medium text-sm transition-all hover:ml-[1rem] hover:text-color-primary">
            <span class="material-symbols-sharp"> mail_outline </span>
            <h3>Messages</h3>
        </a>
        <a href="#" class="flex gap-3 items-center text-slate-900 dark:text-gray-100 font-medium text-sm transition-all hover:ml-[1rem] hover:text-color-primary">
            <span class="material-symbols-sharp"> inventory </span>
            <h3>Products</h3>
        </a>
        <a href="#" class="flex gap-3 items-center text-slate-900 dark:text-gray-100 font-medium text-sm transition-all hover:ml-[1rem] hover:text-color-primary">
            <span class="material-symbols-sharp"> report_gmailerrorred </span>
            <h3>Reports</h3>
        </a>
            <a href="#" class="flex gap-3 items-center text-slate-900 dark:text-gray-100 font-medium text-sm transition-all hover:ml-[1rem] hover:text-color-primary">
            <span class="material-symbols-sharp"> settings </span>
            <h3>Settings</h3>
        </a>
            <a href="#" class="flex gap-3 items-center text-slate-900 dark:text-gray-100 font-medium text-sm transition-all hover:ml-[1rem] hover:text-color-primary">
            <span class="material-symbols-sharp"> add </span>
            <h3>Add Product</h3>
        </a> --}}
    </div>
</aside>


