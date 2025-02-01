<div class="flex space-x-4 mb-4">
    {{-- <button wire:navigate href="{{route('borrowers.logs')}}" class="bg-blue-500 text-white rounded px-4 py-2 hover:bg-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus-opacity-50">
        Borrowed
    </button>
    <button wire:navigate href="{{route('borrowers.return')}}" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded hover:bg-gray-300 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:ring-opacity-50">
        Return
    </button> --}}
    <button wire:navigate href="{{ route('borrowers.logs') }}"
        class="{{ Request::routeIs('borrowers.logs') ? 'bg-blue-500 text-white rounded' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded' }} px-4 py-2 hover:bg-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus-opacity-50">
        Borrowed
    </button>
    <button wire:navigate href="{{ route('borrowers.return') }}"
        class="{{ Request::routeIs('borrowers.return') ? 'bg-blue-500 text-white rounded' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded' }} px-4 py-2 hover:bg-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus-opacity-50">
        Return
    </button>
</div>

