<div class="flex-1 flex flex-col overflow-hidden">
    <!-- Dashboard Content -->
    <main class="flex-1 overflow-y-auto bg-gray-50 p-4">
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Dashboard Overview</h2>
            <p class="text-gray-600">Asset statistics, trends, and recent movements</p>
        </div>
        <!-- Date Range Filter -->
        <div class="bg-white rounded-lg shadow p-4 mb-6">
            <div class="flex flex-wrap items-center justify-between">
                <div class="flex items-center space-x-2">
                    <span class="text-sm font-medium text-gray-700">Time Period:</span>
                    <select wire:model.live="timePeriod" class="text-sm border-gray-300 rounded focus:ring-primary-500 focus:border-primary-500">
                        <option value="last_30_days">Last 30 Days</option>
                        <option value="last_quarter">Last Quarter</option>
                        <option value="last_6_months">Last 6 Months</option>
                        <option value="year_to_date">Year to Date</option>
                        <option value="last_year">Last Year</option>
                    </select>
                </div>
                <div class="flex space-x-2 mt-2 sm:mt-0">
                    <button wire:click="refreshData" class="bg-primary-500 hover:bg-primary-600 text-white text-sm font-medium py-2 px-4 rounded flex items-center">
                        <i class="fas fa-sync-alt mr-2"></i> Refresh Data
                    </button>
                    <button class="bg-gray-100 hover:bg-gray-200 text-gray-800 text-sm font-medium py-2 px-4 rounded flex items-center">
                        <i class="fas fa-download mr-2"></i> Export
                    </button>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-blue-600 mb-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Total Assets</p>
                        <p class="text-3xl font-bold text-blue-600">{{ $assetCounts['total'] ?? 0 }}</p>
                        <p class="text-gray-500 text-sm mt-1">₱{{ number_format($valueData['totalValue'] ?? 0, 2) }} total value</p>
                    </div>
                    <div class="w-12 h-12 bg-cyan-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-laptop text-blue-600 text-xl"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-green-600 mb-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Assigned Assets</p>
                        <p class="text-3xl font-bold text-green-600">{{ $assetCounts['assigned'] ?? 0 }}</p>
                        <p class="text-gray-500 text-sm mt-1">{{ $assetCounts['assigned_percent'] ?? 0 }}% of total</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-user-check text-green-600 text-xl"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-yellow-500 mb-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">New Assets</p>
                        <p class="text-3xl font-bold text-yellow-500">{{ $assetCounts['new'] ?? 0 }}</p>

                        <div class="flex items-center text-sm">
                            @if(isset($assetCounts['new_growth']))
                            <span class="{{ $assetCounts['new_growth'] >= 0 ? 'text-green-500' : 'text-red-500' }} flex items-center">
                                <i class="fas fa-arrow-{{ $assetCounts['new_growth'] >= 0 ? 'up' : 'down' }} mr-1"></i> {{ abs($assetCounts['new_growth']) }}%
                            </span>
                            <span class="text-gray-500 ml-2">from last month</span>
                            @endif
                        </div>
                    </div>
                    <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-plus-circle text-yellow-500 text-xl"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-red-500 mb-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">In Maintenance</p>
                        <p class="text-3xl font-bold text-red-500">{{ $assetCounts['maintenance'] ?? 0 }}</p>
                        <p class="text-gray-500 text-sm mt-1">{{ $assetCounts['maintenance_percent'] ?? 0 }}% of total assets</p>
                    </div>
                    <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-tools text-red-500 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Asset Movement Activity -->
        <div class="bg-white rounded-lg shadow mb-6">
            <div class="p-4 border-b border-gray-200 flex justify-between items-center">
                <h3 class="font-semibold text-gray-800">Asset Movement Activity</h3>
                <a href="{{ route('pamo.assetTracker') }}" class="text-xs bg-gray-100 hover:bg-gray-200 text-gray-800 font-medium py-1 px-2 rounded">
                    View All
                </a>
            </div>
            <div class="p-4">
                <div class="flex flex-col md:flex-row gap-6">
                    <!-- Movement Timeline -->
                    <div class="w-full md:w-2/3">
                        <h4 class="text-sm font-medium text-gray-700 mb-3">Recent Transfers</h4>
                        <div class="space-y-4">
                            @forelse($recentMovements as $movement)
                                <div class="flex">
                                    <div class="flex flex-col items-center mr-4">
                                        <div class="h-8 w-8 rounded-full
                                            {{ ($movement->movement_type ?? 'transfer') == 'transfer' ? 'bg-blue-100 text-blue-600' :
                                            (($movement->movement_type ?? 'transfer') == 'maintenance' ? 'bg-red-100 text-red-600' :
                                            'bg-green-100 text-green-600') }}
                                            flex items-center justify-center">
                                            <i class="fas {{ ($movement->movement_type ?? 'transfer') == 'transfer' ? 'fa-exchange-alt' :
                                                    (($movement->movement_type ?? 'transfer') == 'maintenance' ? 'fa-tools' : 'fa-check-circle') }}">
                                            </i>
                                        </div>
                                        @if(!$loop->last)
                                        <div class="h-full border-l border-dashed border-gray-300 my-1"></div>
                                        @endif
                                    </div>
                                    <div class="bg-gray-50 rounded-lg p-3 flex-1">
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">
                                                    {{ $movement->asset->brand ?? 'Unknown' }} {{ $movement->asset->model ?? '' }}
                                                    {{ ($movement->movement_type ?? 'transfer') == 'transfer' ? 'transferred' :
                                                    (($movement->movement_type ?? 'transfer') == 'maintenance' ? 'sent for repair' : 'assigned') }}
                                                </p>
                                                <p class="text-xs text-gray-500">
                                                    From: {{ $movement->fromLocation->name ?? 'Unknown' }} →
                                                    To: {{ $movement->toLocation->name ?? 'Unknown' }}
                                                </p>

                                                <!-- Show assigned employee if exists -->
                                                @if($movement->assignedEmployee)
                                                    <p class="text-xs text-blue-600 mt-1">
                                                        <i class="fas fa-user mr-1"></i>
                                                        Assigned to: {{ $movement->assignedEmployee->employee_number }} - {{ $movement->assignedEmployee->full_name }}
                                                        @if($movement->assignedEmployee->department)
                                                            ({{ $movement->assignedEmployee->department }})
                                                        @endif
                                                    </p>
                                                @endif

                                                <!-- Show who performed the action -->
                                                @if($movement->assignedBy)
                                                    <p class="text-xs text-gray-400 mt-1">
                                                        <i class="fas fa-user-cog mr-1"></i>
                                                        By: {{ $movement->assignedBy->name }}
                                                    </p>
                                                @endif

                                                <!-- Show notes if exists -->
                                                @if($movement->notes)
                                                    <p class="text-xs text-gray-500 mt-1 italic">
                                                        "{{ Str::limit($movement->notes, 50) }}"
                                                    </p>
                                                @endif
                                            </div>
                                            <div class="text-right">
                                                <span class="text-xs text-gray-500">
                                                    {{ $movement->created_at ? $movement->created_at->diffForHumans() : 'Unknown date' }}
                                                </span>
                                                <!-- Movement type badge -->
                                                <div class="mt-1">
                                                    @if(($movement->movement_type ?? 'transfer') == 'transfer')
                                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                            Transfer
                                                        </span>
                                                    @elseif(($movement->movement_type ?? 'transfer') == 'maintenance')
                                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                            Maintenance
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                            Assignment
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-8">
                                    <i class="fas fa-exchange-alt text-4xl text-gray-300 mb-3"></i>
                                    <p class="text-sm text-gray-500 mb-2">No recent asset movements found</p>
                                    <p class="text-xs text-gray-400">Asset transfers and assignments will appear here</p>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Movement Stats -->
                    <div class="w-full md:w-1/3 mt-6 md:mt-0">
                        <h4 class="text-sm font-medium text-gray-700 mb-3">Transfer Statistics</h4>
                        <div class="bg-gray-50 rounded-lg p-4 mb-4">
                            <div class="flex justify-between items-center mb-2">
                                <p class="text-sm text-gray-600">Total Transfers (30d)</p>
                                <p class="text-lg font-semibold text-gray-800">{{ $transferStats['last30Days'] ?? 0 }}</p>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-blue-500 h-2 rounded-full" style="width: {{ min(100, $transferStats['percentOfAverage'] ?? 0) }}%"></div>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">
                                {{ $transferStats['percentOfAverage'] ?? 0 }}% of monthly average
                            </p>
                        </div>

                        <!-- Recent Movement Types -->
                        <h4 class="text-sm font-medium text-gray-700 mb-2">Movement Types</h4>
                        <div class="space-y-2 mb-4">
                            @php
                                $movementTypes = $recentMovements->groupBy('movement_type')->map(function($movements, $type) {
                                    return [
                                        'type' => $type ?: 'transfer',
                                        'count' => $movements->count(),
                                        'percentage' => $movements->count()
                                    ];
                                });
                                $totalMovements = $recentMovements->count();
                                if ($totalMovements > 0) {
                                    $movementTypes = $movementTypes->map(function($item) use ($totalMovements) {
                                        $item['percentage'] = round(($item['count'] / $totalMovements) * 100, 1);
                                        return $item;
                                    });
                                }
                            @endphp

                            @forelse($movementTypes as $type)
                                <div class="flex justify-between items-center">
                                    <div class="flex items-center">
                                        <i class="fas {{ $type['type'] == 'transfer' ? 'fa-exchange-alt' :
                                            ($type['type'] == 'maintenance' ? 'fa-tools' : 'fa-user-plus') }}
                                            text-gray-400 w-5">
                                        </i>
                                        <span class="text-sm text-gray-600 ml-2">{{ ucfirst($type['type']) }}</span>
                                    </div>
                                    <span class="text-sm font-medium">{{ $type['percentage'] }}%</span>
                                </div>
                            @empty
                                <p class="text-xs text-gray-400">No movement data available</p>
                            @endforelse
                        </div>

                        <!-- Most Transferred Asset Types -->
                        <h4 class="text-sm font-medium text-gray-700 mb-2">Most Transferred Asset Types</h4>
                        <div class="space-y-2">
                            @forelse($transferStats['byCategory'] ?? [] as $category)
                                <div class="flex justify-between items-center">
                                    <div class="flex items-center">
                                        <i class="fas {{
                                            str_contains(strtolower($category['name'] ?? ''), 'laptop') ? 'fa-laptop' :
                                            (str_contains(strtolower($category['name'] ?? ''), 'desktop') ? 'fa-desktop' :
                                            (str_contains(strtolower($category['name'] ?? ''), 'phone') ? 'fa-mobile-alt' :
                                            (str_contains(strtolower($category['name'] ?? ''), 'tablet') ? 'fa-tablet-alt' :
                                            'fa-laptop'))) }}
                                            text-gray-400 w-5">
                                        </i>
                                        <span class="text-sm text-gray-600 ml-2">{{ $category['name'] ?? 'Unknown' }}</span>
                                    </div>
                                    <span class="text-sm font-medium">{{ $category['percentage'] ?? 0 }}%</span>
                                </div>
                            @empty
                                @php
                                    // Fallback: Get category distribution from recent movements
                                    $categoryData = $recentMovements->filter(function($movement) {
                                        return $movement->asset && $movement->asset->category;
                                    })->groupBy('asset.category.name')->map(function($movements, $categoryName) use ($recentMovements) {
                                        $count = $movements->count();
                                        $total = $recentMovements->count();
                                        return [
                                            'name' => $categoryName,
                                            'count' => $count,
                                            'percentage' => $total > 0 ? round(($count / $total) * 100, 1) : 0
                                        ];
                                    })->sortByDesc('count')->take(3);
                                @endphp

                                @forelse($categoryData as $category)
                                    <div class="flex justify-between items-center">
                                        <div class="flex items-center">
                                            <i class="fas {{
                                                str_contains(strtolower($category['name']), 'laptop') ? 'fa-laptop' :
                                                (str_contains(strtolower($category['name']), 'desktop') ? 'fa-desktop' :
                                                (str_contains(strtolower($category['name']), 'phone') ? 'fa-mobile-alt' :
                                                (str_contains(strtolower($category['name']), 'tablet') ? 'fa-tablet-alt' :
                                                'fa-laptop'))) }}
                                                text-gray-400 w-5">
                                            </i>
                                            <span class="text-sm text-gray-600 ml-2">{{ $category['name'] }}</span>
                                        </div>
                                        <span class="text-sm font-medium">{{ $category['percentage'] }}%</span>
                                    </div>
                                @empty
                                    <p class="text-xs text-gray-400">No category data available</p>
                                @endforelse
                            @endforelse
                        </div>

                        <div class="mt-4">
                            <canvas id="transferTrendChart" height="120"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row 1 -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <!-- Asset Acquisition Trend -->
            <div class="bg-white rounded-lg shadow">
                <div class="p-4 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="font-semibold text-gray-800">Asset Acquisition Trend</h3>
                    <div class="flex space-x-2">
                        <select class="text-xs border-gray-300 rounded focus:ring-primary-500 focus:border-primary-500">
                            <option>Monthly</option>
                            <option>Quarterly</option>
                            <option>Yearly</option>
                        </select>
                    </div>
                </div>
                <div class="p-4">
                    <div style="height: 250px; position: relative;">
                        <canvas id="acquisitionChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Asset Status Distribution -->
            <div class="bg-white rounded-lg shadow">
                <div class="p-4 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="font-semibold text-gray-800">Asset Status Distribution</h3>
                    <div class="flex space-x-2">
                        <button class="text-xs bg-gray-100 hover:bg-gray-200 text-gray-800 font-medium py-1 px-2 rounded">
                            <i class="fas fa-expand-arrows-alt mr-1"></i> Expand
                        </button>
                    </div>
                </div>
                <div class="p-4 flex flex-col md:flex-row items-center">
                    <div class="w-full md:w-1/2">
                        <canvas id="statusChart" height="220"></canvas>
                    </div>
                    <div class="w-full md:w-1/2 mt-4 md:mt-0">
                        <div class="space-y-3">
                            @foreach($statusDistribution['statuses'] ?? [] as $index => $status)
                                <div class="flex items-center">
                                    <span class="h-3 w-3 rounded-full mr-2
                                        {{ strtolower($status['name'] ?? '') == 'active' ? 'bg-green-500' :
                                        (strtolower($status['name'] ?? '') == 'in transfer' ? 'bg-yellow-500' :
                                        (strtolower($status['name'] ?? '') == 'maintenance' ? 'bg-red-500' :
                                        'bg-gray-300')) }}">
                                    </span>
                                    <span class="text-sm text-gray-600">
                                        {{ $status['name'] ?? 'Unknown' }} ({{ $status['percentage'] ?? 0 }}%)
                                    </span>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-6">
                            <p class="text-sm text-gray-600">
                                @if(count($statusDistribution['statuses'] ?? []) > 0)
                                    Most assets are currently {{ $statusDistribution['statuses'][0]['name'] ?? 'active' }}
                                    @if(isset($assetCounts['maintenance_percent']) && $assetCounts['maintenance_percent'] > 0)
                                        with {{ $assetCounts['maintenance_percent'] }}% in maintenance.
                                    @endif
                                @else
                                    No status data available.
                                @endif
                            </p>
                            <a href="{{ route('pamo.assetTracker') }}" class="text-sm text-primary-600 hover:text-primary-800 font-medium mt-2 inline-block">View Detailed Report</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row 2 -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <!-- Assets by Department -->
            <div class="bg-white rounded-lg shadow">
                <div class="p-4 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="font-semibold text-gray-800">Assets by Department</h3>
                    <div class="flex space-x-2">
                        <select class="text-xs border-gray-300 rounded focus:ring-primary-500 focus:border-primary-500">
                            <option>By Count</option>
                            <option>By Value</option>
                        </select>
                    </div>
                </div>
                <div class="p-4">
                    <canvas id="departmentChart" height="250"></canvas>
                </div>
            </div>

            <!-- Assets by Type -->
            <div class="bg-white rounded-lg shadow">
                <div class="p-4 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="font-semibold text-gray-800">Assets by Type</h3>
                    <div class="flex space-x-2">
                        <button class="text-xs bg-gray-100 hover:bg-gray-200 text-gray-800 font-medium py-1 px-2 rounded">
                            <i class="fas fa-expand-arrows-alt mr-1"></i> Expand
                        </button>
                    </div>
                </div>
                <div class="p-4 flex flex-col md:flex-row items-center">
                    <div class="w-full md:w-1/2">
                        <canvas id="typeChart" height="220"></canvas>
                    </div>
                    <div class="w-full md:w-1/2 mt-4 md:mt-0">
                        <div class="space-y-3">
                            @foreach($categoryDistribution['categories'] ?? [] as $index => $category)
                                @if($index < 5)
                                <div class="flex items-center">
                                    <span class="h-3 w-3 bg-{{ ['primary-500', 'primary-300', 'green-500', 'yellow-500', 'purple-500'][$index % 5] }} rounded-full mr-2"></span>
                                    <span class="text-sm text-gray-600">{{ $category['name'] ?? 'Unknown' }} ({{ $category['percentage'] ?? 0 }}%)</span>
                                </div>
                                @endif
                            @endforeach
                        </div>
                        <div class="mt-4">
                            <a href="#" class="text-sm text-primary-600 hover:text-primary-800 font-medium inline-block">View All Asset Types</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Maintenance Statistics -->
        <div class="bg-white rounded-lg shadow mb-6">
            <div class="p-4 border-b border-gray-200 flex justify-between items-center">
                <h3 class="font-semibold text-gray-800">Maintenance Statistics</h3>
                <div class="flex space-x-2">
                    <select wire:model.live="timePeriod" class="text-xs border-gray-300 rounded focus:ring-primary-500 focus:border-primary-500">
                        <option value="last_6_months">Last 6 Months</option>
                        <option value="last_year">Last Year</option>
                        <option value="year_to_date">Year to Date</option>
                    </select>
                </div>
            </div>
            <div class="p-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                    <div class="bg-gray-50 p-3 rounded-lg">
                        <p class="text-xs text-gray-500">Total Maintenance Events</p>
                        <p class="text-xl font-semibold text-gray-800">{{ $maintenanceStats['total'] ?? 0 }}</p>
                    </div>
                    <div class="bg-gray-50 p-3 rounded-lg">
                        <p class="text-xs text-gray-500">Assets Currently in Maintenance</p>
                        <p class="text-xl font-semibold text-gray-800">{{ $assetCounts['maintenance'] ?? 0 }}</p>
                    </div>
                    <div class="bg-gray-50 p-3 rounded-lg">
                        <p class="text-xs text-gray-500">Maintenance Rate</p>
                        <p class="text-xl font-semibold text-gray-800">{{ $assetCounts['maintenance_percent'] ?? 0 }}%</p>
                    </div>
                </div>

                <div class="mt-4">
                    <canvas id="maintenanceChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Create empty charts first
            initEmptyCharts();

            // Listen for data updates
            Livewire.on('dashboardDataUpdated', function() {
                console.log('Dashboard data updated event received');
                updateAllCharts();
            });
        });

        let acquisitionChart, statusChart, departmentChart, typeChart,
        maintenanceChart, transferTrendChart;

        function initEmptyCharts() {
            console.log('Creating empty chart containers');
            createAcquisitionChart();
            createStatusChart();
            createDepartmentChart();
            createTypeChart();
            createMaintenanceChart();
            createTransferTrendChart();
        }

        function updateAllCharts() {
            console.log('Updating all charts with new data');
            // Add a slight delay to ensure data is fully processed
            setTimeout(() => {
                updateAcquisitionChart();
                updateStatusChart();
                updateDepartmentChart();
                updateTypeChart();
                updateMaintenanceChart();
                updateTransferTrendChart();
            }, 300);
        }

        function initCharts() {
            console.log('Initializing charts');
            createAcquisitionChart();
            createStatusChart();
            createDepartmentChart();
            createTypeChart();
            createMaintenanceChart();
            createTransferTrendChart();

            // Immediately update with data
            setTimeout(() => {
                updateCharts();
            }, 100);
        }

        function updateCharts() {
            updateAcquisitionChart();
            updateStatusChart();
            updateDepartmentChart();
            updateTypeChart();
            updateMaintenanceChart();
            updateTransferTrendChart();
        }

        function createAcquisitionChart() {
            const ctx = document.getElementById('acquisitionChart').getContext('2d');
            acquisitionChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'New Assets',
                        data: [],
                        borderColor: '#0ea5e9',
                        backgroundColor: 'rgba(188, 242, 246, 0.5)',
                        tension: 0.3,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            }
                        }
                    }
                }
            });
        }

        function updateAcquisitionChart() {
            if (!acquisitionChart) return;

            const data = @js($acquisitionTrend);
            console.log('Updating acquisition chart with:', data);

            if (data && Array.isArray(data.labels) && Array.isArray(data.data)) {
                acquisitionChart.data.labels = data.labels;
                acquisitionChart.data.datasets[0].data = data.data;
                acquisitionChart.update('none'); // Use 'none' for better performance
            } else {
                console.warn('Invalid acquisition data structure:', data);
            }
        }

        function createStatusChart() {
            const ctx = document.getElementById('statusChart').getContext('2d');
            statusChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: [],
                    datasets: [{
                        data: [],
                        backgroundColor: [
                            '#006BFF', // Main blue
                            '#08C2FF', // Light blue
                            '#FFF100', // Bright yellow
                            '#BCF2F6'  // Pale blue
                        ],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    cutout: '70%'
                }
            });
        }

        function updateStatusChart() {
            if (statusChart) {
                const data = @this.statusDistribution;

                if (data && data.labels && data.data) {
                    // Add custom colors based on status
                    const colors = data.labels.map(status => {
                        const label = status.toLowerCase();
                        if (label.includes('active')) return '#22c55e';
                        if (label.includes('transfer')) return '#eab308';
                        if (label.includes('maintenance')) return '#ef4444';
                        return '#d1d5db';
                    });

                    statusChart.data.labels = data.labels;
                    statusChart.data.datasets[0].data = data.data;
                    statusChart.data.datasets[0].backgroundColor = colors;
                    statusChart.update();
                }
            }
        }

        function createDepartmentChart() {
            const ctx = document.getElementById('departmentChart').getContext('2d');
            departmentChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'Number of Assets',
                        data: [],
                        backgroundColor: '#006BFF',
                        borderRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            }
                        }
                    }
                }
            });
        }

        function updateDepartmentChart() {
            if (departmentChart) {
                const data = @this.departmentDistribution;

                if (data && data.labels && data.data) {
                    departmentChart.data.labels = data.labels;
                    departmentChart.data.datasets[0].data = data.data;
                    departmentChart.update();
                }
            }
        }

        function createTypeChart() {
            const ctx = document.getElementById('typeChart').getContext('2d');
            typeChart = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: [],
                    datasets: [{
                        data: [],
                        backgroundColor: [
                            '#0ea5e9', // primary
                            '#7dd3fc', // primary light
                            '#22c55e', // green
                            '#eab308', // yellow
                            '#a855f7'  // purple
                        ],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
        }

        function updateTypeChart() {
            if (typeChart) {
                const data = @this.categoryDistribution;

                if (data && data.labels && data.data) {
                    // Limit to first 5 categories for better visualization
                    const labels = data.labels.slice(0, 5);
                    const chartData = data.data.slice(0, 5);

                    // Add "Other" category if there are more than 5
                    if (data.labels.length > 5) {
                        labels.push('Other');
                        const otherSum = data.data.slice(5).reduce((sum, val) => sum + val, 0);
                        chartData.push(otherSum);
                    }

                    typeChart.data.labels = labels;
                    typeChart.data.datasets[0].data = chartData;
                    typeChart.update();
                }
            }
        }

        function createMaintenanceChart() {
            const ctx = document.getElementById('maintenanceChart').getContext('2d');
            maintenanceChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'Maintenance Events',
                        data: [],
                        backgroundColor: '#ef4444',
                        borderRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            }
                        }
                    }
                }
            });
        }

        function updateMaintenanceChart() {
            if (maintenanceChart) {
                const data = @this.maintenanceStats;

                if (data && data.monthly && data.monthly.labels && data.monthly.data) {
                    maintenanceChart.data.labels = data.monthly.labels;
                    maintenanceChart.data.datasets[0].data = data.monthly.data;
                    maintenanceChart.update();
                }
            }
        }

        function createTransferTrendChart() {
            const ctx = document.getElementById('transferTrendChart').getContext('2d');
            transferTrendChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'Transfers',
                        data: [],
                        borderColor: '#0ea5e9',
                        backgroundColor: 'rgba(14, 165, 233, 0.1)',
                        tension: 0.3,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            }
                        }
                    }
                }
            });
        }

        function updateTransferTrendChart() {
            if (transferTrendChart) {
                const data = @this.transferStats;

                if (data && data.weekly && data.weekly.labels && data.weekly.data) {
                    transferTrendChart.data.labels = data.weekly.labels;
                    transferTrendChart.data.datasets[0].data = data.weekly.data;
                    transferTrendChart.update();
                }
            }
        }
    </script>
</div>
