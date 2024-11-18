<div class="flex space-x-4 mb-4">
    {{-- <button wire:navigate href="{{route('assets.view')}}" class="bg-blue-500 text-white rounded px-4 py-2 hover:bg-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus-opacity-50">
        Asset's List
    </button> --}}
    <button wire:navigate href="{{ route('assets.view') }}"
        class="{{ Request::routeIs('assets.view') ? 'bg-blue-500 text-white rounded' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded' }} px-4 py-2 hover:bg-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus-opacity-50">
        Asset's List
    </button>
    <button wire:navigate href="{{route('assets.consumable')}}"
        class="{{ Request::routeIs('assets.consumable') ? 'bg-blue-500 text-white rounded' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded' }} px-4 py-2 hover:bg-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus-opacity-50">
        Asset's Consumable
    </button>
    <button wire:navigate href="{{route('assets.category')}}"
        class="{{ Request::routeIs('assets.category') ? 'bg-blue-500 text-white rounded' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded' }} px-4 py-2 hover:bg-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus-opacity-50">
        Asset's Category
    </button>
</div>
{{-- <div class="p-2 lg:p-4 bg-gray-100 dark:bg-gray-800">
    <div class="flex justify-between mb-4">
        <div class="flex gap-2 items-center">
            <h1 class=" font-bold text-xl">Asset's Record</h1>
        </div>
        <div class="flex items-center gap-4">
        </div>
    </div>
    <div class="cursor-pointer border-b border-gray-500 p-2 space-x-3">
        <x-nav-link wire:navigate href="{{route('assets.view')}}" :active="request()->routeIs('assets.view')">
            Asset's List
        </x-nav-link>
        <x-nav-link wire:navigate href="{{route('assets.consumable')}}" :active="request()->routeIs('assets.consumable')">
            Asset's Consumable
        </x-nav-link>
        <x-nav-link wire:navigate href="{{route('assets.category')}}" :active="request()->routeIs('assets.category')">
            Asset's Category
        </x-nav-link>
    </div>
</div> --}}
