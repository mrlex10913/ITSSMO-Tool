<div>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-blue-600 text-white rounded-lg shadow">
            <div class="p-4">
                <div class="flex justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-white">Total Assets</h3>
                        <p class="text-2xl font-bold">{{ $totalAssets ?? '0' }}</p>
                    </div>
                    <div class="opacity-50">
                        <span class="material-symbols-sharp text-4xl">inventory_2</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-green-600 text-white rounded-lg shadow">
            <div class="p-4">
                <div class="flex justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-white">Consumables</h3>
                        <p class="text-2xl font-bold">{{ $totalConsumables ?? '0' }}</p>
                    </div>
                    <div class="opacity-50">
                        <span class="material-symbols-sharp text-4xl">shopping_cart</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-yellow-500 text-white rounded-lg shadow">
            <div class="p-4">
                <div class="flex justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-white">Low Stock</h3>
                        <p class="text-2xl font-bold">{{ $lowStock ?? '0' }}</p>
                    </div>
                    <div class="opacity-50">
                        <span class="material-symbols-sharp text-4xl">warning</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-red-600 text-white rounded-lg shadow">
            <div class="p-4">
                <div class="flex justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-white">Maintenance Due</h3>
                        <p class="text-2xl font-bold">{{ $maintenanceDue ?? '0' }}</p>
                    </div>
                    <div class="opacity-50">
                        <span class="material-symbols-sharp text-4xl">build</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                Welcome to PAMO Dashboard
            </h2>

            <p class="text-gray-600 dark:text-gray-400">
                This is the starting point for managing your inventory, supplies, and transactions.
                Use the navigation menu on the left to access different PAMO modules.
            </p>

            <div class="mt-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg">
                    <h3 class="font-medium text-gray-900 dark:text-gray-100">Inventory Management</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Track and manage all your assets and supplies in one place.
                    </p>
                    <x-button class="mt-3" wire:navigate href="{{ route('pamo.inventory') }}">
                        Go to Inventory
                    </x-button>
                </div>

                <div class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg">
                    <h3 class="font-medium text-gray-900 dark:text-gray-100">Barcode Generation</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Create and print barcodes for your inventory items.
                    </p>
                    <x-button class="mt-3" wire:navigate href="{{ route('pamo.barcode') }}">
                        Generate Barcodes
                    </x-button>
                </div>

                <div class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg">
                    <h3 class="font-medium text-gray-900 dark:text-gray-100">Transaction History</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        View and manage all inventory-related transactions.
                    </p>
                    <x-button class="mt-3" wire:navigate href="{{ route('pamo.transactions') }}">
                        View Transactions
                    </x-button>
                </div>
            </div>
        </div>
    </div>
</div>
