<div>
    <div class="sm:px-6 lg:px-8">
        <h1 class="text-2xl font-semibold text-gray-900 mb-6 mt-6">ITSS Dashboard</h1>

        <div class="bg-white rounded-lg shadow-md p-6">
            <!-- Stats Overview -->
            <div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-6">
                <!-- ID Production Stats -->
                <div class="bg-blue-50 rounded-lg p-6 border border-blue-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-medium text-blue-900">ID Production</h3>
                            <p class="text-3xl font-bold text-blue-700 mt-2">0</p>
                            <p class="text-sm text-blue-600 mt-1">Pending requests</p>
                        </div>
                        <div class="bg-blue-100 p-3 rounded-full">
                            <x-heroicon name="identification" class="w-6 h-6 text-blue-600" />
                        </div>
                    </div>
                </div>

                <!-- Helpdesk Stats -->
                <div class="bg-green-50 rounded-lg p-6 border border-green-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-medium text-green-900">Helpdesk</h3>
                            <p class="text-3xl font-bold text-green-700 mt-2">0</p>
                            <p class="text-sm text-green-600 mt-1">Open tickets</p>
                        </div>
                        <div class="bg-green-100 p-3 rounded-full">
                            <x-heroicon name="lifebuoy" class="w-6 h-6 text-green-600" />
                        </div>
                    </div>
                </div>

                <!-- SLA Due Today -->
                <div class="bg-amber-50 rounded-lg p-6 border border-amber-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-medium text-amber-900">SLA Due Today</h3>
                            <p class="text-3xl font-bold text-amber-700 mt-2">{{ $slaDueToday ?? 0 }}</p>
                            <p class="text-sm text-amber-600 mt-1">Tickets expiring today</p>
                        </div>
                        <div class="bg-amber-100 p-3 rounded-full">
                            <x-heroicon name="clock" class="w-6 h-6 text-amber-600" />
                        </div>
                    </div>
                </div>

                <!-- SLA Breached -->
                <div class="bg-red-50 rounded-lg p-6 border border-red-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-medium text-red-900">SLA Breached</h3>
                            <p class="text-3xl font-bold text-red-700 mt-2">{{ $slaBreached ?? 0 }}</p>
                            <p class="text-sm text-red-600 mt-1">Open or in progress</p>
                        </div>
                        <div class="bg-red-100 p-3 rounded-full">
                            <x-heroicon name="exclamation-triangle" class="w-6 h-6 text-red-600" />
                        </div>
                    </div>
                </div>

                <!-- System Stats -->
                <div class="bg-purple-50 rounded-lg p-6 border border-purple-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-medium text-purple-900">System Health</h3>
                            <p class="text-3xl font-bold text-purple-700 mt-2">100%</p>
                            <p class="text-sm text-purple-600 mt-1">All systems operational</p>
                        </div>
                        <div class="bg-purple-100 p-3 rounded-full">
                            <x-heroicon name="heart" class="w-6 h-6 text-purple-600" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="mt-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Recent Activity</h3>
                <div class="bg-gray-50 rounded-lg border border-gray-200 p-6">
                    <div class="flex items-center justify-center h-40">
                        <div class="text-center">
                            <x-heroicon name="bell" class="w-10 h-10 text-gray-300 block mb-2" />
                            <p class="text-gray-500">No recent activity</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
