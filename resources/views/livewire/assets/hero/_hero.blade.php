<div class="p-2 lg:p-4 bg-gray-100 dark:bg-gray-800">
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
</div>
