<div class="container mx-auto">
    <h1 class="text-xl font-bold mb-2 text-left">Administrative Access</h1>
    <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 space-y-6">
        <x-nav-link>
            <p class="text-xs">{{session('breadcrumb')}}</p>
        </x-nav-link>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="flex justify-center">
                <x-nav-link wire:navigate href="{{ route('controlPanel.user') }}" class="block px-6 py-2 rounded hover:bg-gray-300 dark:hover:bg-gray-700" >
                    <div class="flex gap-3 items-center">
                        <span class="material-symbols-sharp">
                            person_add
                        </span>
                        <h3>User's Controller</h3>
                    </div>
                </x-nav-link>
            </div>
            <div class="flex justify-center">
                <x-nav-link wire:navigate href="#" class="block px-6 py-2 rounded hover:bg-gray-300 dark:hover:bg-gray-700">
                    <div class="flex gap-3 items-center">
                        <span class="material-symbols-sharp">
                            settings_accessibility
                        </span>
                        <h3>Access Controller</h3>
                    </div>
                </x-nav-link>
            </div>
            <div class="flex justify-center">
                <x-nav-link wire:navigate href="#" class="block px-6 py-2 rounded hover:bg-gray-300 dark:hover:bg-gray-700">
                    <div class="flex gap-3 items-center">
                        <span class="material-symbols-sharp">
                            menu
                        </span>
                        <h3>Menu Controller</h3>
                    </div>
                </x-nav-link>
            </div>
            <div class="flex justify-center">
                <x-nav-link wire:navigate href="#" class="block px-6 py-2 rounded hover:bg-gray-300 dark:hover:bg-gray-700">
                    <div class="flex gap-3 items-center">
                        <span class="material-symbols-sharp">
                            upload_file
                        </span>
                        <h3>Upload Controller</h3>
                    </div>
                </x-nav-link>
            </div>
        </div>
    </div>
</div>

