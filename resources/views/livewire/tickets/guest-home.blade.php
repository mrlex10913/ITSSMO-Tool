<div class="min-h-screen bg-gradient-to-b from-blue-50 to-white dark:from-gray-900 dark:to-gray-900">
    <div class="max-w-4xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-10">
            <x-heroicon name="lifebuoy" class="w-12 h-12 text-blue-600" />
            <h1 class="mt-2 text-3xl font-semibold text-gray-900 dark:text-gray-100">ITSS Helpdesk</h1>
            <p class="mt-2 text-gray-600 dark:text-gray-300">How can we help you today?</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <a href="{{ route('helpdesk.new') }}" class="block rounded-xl border border-blue-200 dark:border-gray-700 bg-white dark:bg-gray-800 hover:bg-blue-50 dark:hover:bg-gray-700 p-6 shadow-sm ring-1 ring-black/5 dark:ring-white/10 transition">
                <div class="flex items-center gap-3">
                    <x-heroicon name="document-plus" class="w-8 h-8 text-blue-600" />
                    <div>
                        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">File a new ticket</h2>
                        <p class="text-sm text-gray-600 dark:text-gray-300">Submit a new request to the ITSS team.</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('helpdesk.track') }}" class="block rounded-xl border border-blue-200 dark:border-gray-700 bg-white dark:bg-gray-800 hover:bg-blue-50 dark:hover:bg-gray-700 p-6 shadow-sm ring-1 ring-black/5 dark:ring-white/10 transition">
                <div class="flex items-center gap-3">
                    <x-heroicon name="magnifying-glass" class="w-8 h-8 text-blue-600" />
                    <div>
                        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Track my existing ticket</h2>
                        <p class="text-sm text-gray-600 dark:text-gray-300">Check the status of a ticket using its reference number.</p>
                    </div>
                </div>
            </a>
        </div>

        <div class="mt-10 text-center text-sm text-gray-500 dark:text-gray-400">
            <p>For emergencies, please contact the ITSS office directly.</p>
        </div>
    </div>
</div>
