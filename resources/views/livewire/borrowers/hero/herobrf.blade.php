<div class="flex space-x-4 mb-4">
    <button wire:navigate href="{{route('borrowers.logs')}}" class="bg-blue-500 text-white rounded px-4 py-2 hover:bg-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus-opacity-50">
        Borrowed
    </button>
    <button wire:navigate href="{{route('borrowers.return')}}" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded hover:bg-gray-300 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:ring-opacity-50">
        Return
    </button>
</div>
{{-- <div class="p-2 lg:p-4 bg-gray-100 dark:bg-gray-800">
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
</div> --}}
