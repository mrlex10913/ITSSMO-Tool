<div>
    <div class="sm:px-6 lg:px-8">
        <h1 class="text-2xl font-semibold text-gray-900 mb-6">Helpdesk Support System</h1>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            <!-- Sidebar with Stats -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <h2 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                        <span class="material-symbols-sharp text-blue-600 mr-2">analytics</span>
                        Ticket Summary
                    </h2>

                    <div class="space-y-4">
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <div class="text-sm text-blue-700">Open</div>
                            <div class="text-2xl font-bold text-blue-800">0</div>
                        </div>

                        <div class="bg-yellow-50 p-4 rounded-lg">
                            <div class="text-sm text-yellow-700">In Progress</div>
                            <div class="text-2xl font-bold text-yellow-800">0</div>
                        </div>

                        <div class="bg-green-50 p-4 rounded-lg">
                            <div class="text-sm text-green-700">Resolved</div>
                            <div class="text-2xl font-bold text-green-800">0</div>
                        </div>

                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="text-sm text-gray-700">Total</div>
                            <div class="text-2xl font-bold text-gray-800">0</div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                        <span class="material-symbols-sharp text-blue-600 mr-2">category</span>
                        Categories
                    </h2>

                    <div class="space-y-2">
                        <div class="flex items-center justify-between p-2 rounded-lg hover:bg-gray-50">
                            <span class="text-sm">Hardware Issues</span>
                            <span class="bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded-full">0</span>
                        </div>

                        <div class="flex items-center justify-between p-2 rounded-lg hover:bg-gray-50">
                            <span class="text-sm">Software Issues</span>
                            <span class="bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded-full">0</span>
                        </div>

                        <div class="flex items-center justify-between p-2 rounded-lg hover:bg-gray-50">
                            <span class="text-sm">Network Issues</span>
                            <span class="bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded-full">0</span>
                        </div>

                        <div class="flex items-center justify-between p-2 rounded-lg hover:bg-gray-50">
                            <span class="text-sm">Account Access</span>
                            <span class="bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded-full">0</span>
                        </div>

                        <div class="flex items-center justify-between p-2 rounded-lg hover:bg-gray-50">
                            <span class="text-sm">Other</span>
                            <span class="bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded-full">0</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="lg:col-span-3">
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="p-6 border-b border-gray-200">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                            <div class="flex items-center mb-4 md:mb-0">
                                <span class="material-symbols-sharp text-blue-600 mr-2 text-2xl">support_agent</span>
                                <h2 class="text-lg font-medium text-gray-900">Support Tickets</h2>
                            </div>
                            <div class="flex items-center space-x-3">
                                <button class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                    <span class="flex items-center">
                                        <span class="material-symbols-sharp text-sm mr-1">add</span>
                                        New Ticket
                                    </span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Filters & Search -->
                    <div class="p-4 bg-gray-50 border-b border-gray-200">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                            <div class="md:flex-1">
                                <input type="text" placeholder="Search tickets..."
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div class="flex flex-wrap gap-2">
                                <select class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">All Status</option>
                                    <option value="open">Open</option>
                                    <option value="in-progress">In Progress</option>
                                    <option value="resolved">Resolved</option>
                                    <option value="closed">Closed</option>
                                </select>
                                <select class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">All Priorities</option>
                                    <option value="low">Low</option>
                                    <option value="medium">Medium</option>
                                    <option value="high">High</option>
                                    <option value="critical">Critical</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Tickets List -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Ticket ID
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Subject
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Requestor
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Priority
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Created
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <!-- Empty state -->
                                <tr>
                                    <td colspan="7" class="px-6 py-16 text-center">
                                        <span class="material-symbols-sharp text-gray-300 text-5xl mb-4 block mx-auto">support_agent</span>
                                        <p class="text-gray-500 text-lg font-medium">No support tickets found</p>
                                        <p class="text-gray-400 text-sm mt-1">Create a new ticket to get started</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
