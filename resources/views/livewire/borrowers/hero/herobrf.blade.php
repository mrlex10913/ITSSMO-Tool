<div class="p-2 lg:p-4 bg-gray-100 dark:bg-gray-800">
    <div class="flex justify-between mb-4">
        <div class="flex gap-2 items-center">
            <h1 class=" font-bold text-xl">Borrower's Logs</h1>
        </div>
        <div class="flex items-center gap-4">
        </div>
    </div>
    <div class="cursor-pointer border-b border-gray-500 p-2 space-x-3">
        <x-nav-link wire:navigate href="{{route('borrowers.logs')}}" :active="request()->routeIs('borrowers.logs')">
            Borrowed
        </x-nav-link>
        <x-nav-link wire:navigate href="{{route('borrowers.return')}}" :active="request()->routeIs('borrowers.return')">
           Return
        </x-nav-link>
    </div>
</div>
