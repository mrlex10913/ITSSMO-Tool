<!-- filepath: c:\inetpub\wwwroot\ITSSMO-Tool\resources\views\livewire\master-files\analytics.blade.php -->
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Analytics Dashboard</h1>
            <p class="mt-2 text-sm text-gray-600">Insights and statistics for the Master File Archive</p>
        </div>
        <div class="mt-4 sm:mt-0 flex space-x-3">
            <a href="{{ route('master-file.dashboard') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                <span class="material-symbols-sharp text-sm mr-2">arrow_back</span>
                Back to Dashboard
            </a>
            <select wire:model.live="selectedPeriod" class="px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white focus:ring-blue-500 focus:border-blue-500">
                <option value="7">Last 7 days</option>
                <option value="30">Last 30 days</option>
                <option value="90">Last 90 days</option>
                <option value="365">Last year</option>
            </select>
        </div>
    </div>

    <!-- Key Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Files -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <span class="material-symbols-sharp text-blue-600 text-xl">folder</span>
                    </div>
                </div>
                <div class="ml-4 flex-1">
                    <h3 class="text-sm font-medium text-gray-500">Total Files</h3>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($totalFiles) }}</p>
                    <p class="text-sm text-gray-500">Active documents</p>
                </div>
            </div>
        </div>

        <!-- Total Downloads -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <span class="material-symbols-sharp text-green-600 text-xl">download</span>
                    </div>
                </div>
                <div class="ml-4 flex-1">
                    <h3 class="text-sm font-medium text-gray-500">Total Downloads</h3>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($totalDownloads) }}</p>
                    <p class="text-sm text-gray-500">All time</p>
                </div>
            </div>
        </div>

        <!-- Total Views -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                        <span class="material-symbols-sharp text-purple-600 text-xl">visibility</span>
                    </div>
                </div>
                <div class="ml-4 flex-1">
                    <h3 class="text-sm font-medium text-gray-500">Total Views</h3>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($totalViews) }}</p>
                    <p class="text-sm text-gray-500">All time</p>
                </div>
            </div>
        </div>

        <!-- Active Users -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                        <span class="material-symbols-sharp text-orange-600 text-xl">people</span>
                    </div>
                </div>
                <div class="ml-4 flex-1">
                    <h3 class="text-sm font-medium text-gray-500">Active Users</h3>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($activeUsers) }}</p>
                    <p class="text-sm text-gray-500">Last {{ $selectedPeriod }} days</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Activity Chart -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <span class="material-symbols-sharp text-blue-600 mr-2">trending_up</span>
                    Daily Activity
                </h3>
                <p class="text-sm text-gray-600 mt-1">Downloads and views over time</p>
            </div>
            <div class="p-6">
                <div class="h-64">
                    <canvas id="activityChart" wire:ignore></canvas>
                </div>
            </div>
        </div>

        <!-- Category Distribution -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <span class="material-symbols-sharp text-purple-600 mr-2">donut_small</span>
                    Files by Category
                </h3>
                <p class="text-sm text-gray-600 mt-1">Distribution of documents</p>
            </div>
            <div class="p-6">
                <div class="h-64">
                    <canvas id="categoryChart" wire:ignore></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Popular Files and Recent Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Most Popular Files -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <span class="material-symbols-sharp text-green-600 mr-2">star</span>
                    Most Popular Files
                </h3>
                <p class="text-sm text-gray-600 mt-1">Top downloaded documents</p>
            </div>
            <div class="divide-y divide-gray-200">
                @forelse($popularFiles as $index => $file)
                <div class="p-6 hover:bg-gray-50 transition-colors">
                    <div class="flex items-center space-x-4">
                        <!-- Rank -->
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center
                                @if($index === 0) bg-yellow-100 text-yellow-800
                                @elseif($index === 1) bg-gray-100 text-gray-800
                                @elseif($index === 2) bg-orange-100 text-orange-800
                                @else bg-blue-100 text-blue-800
                                @endif font-bold text-sm">
                                {{ $index + 1 }}
                            </div>
                        </div>

                        <!-- File Info -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center space-x-2">
                                <div class="w-6 h-6 rounded flex items-center justify-center" style="background-color: {{ $file->category->color }}20;">
                                    <span class="material-symbols-sharp text-xs" style="color: {{ $file->category->color }};">{{ $file->category->icon }}</span>
                                </div>
                                <h4 class="text-sm font-medium text-gray-900 truncate">{{ $file->title }}</h4>
                            </div>
                            <div class="flex items-center space-x-4 mt-1 text-xs text-gray-500">
                                <span>{{ $file->category->name }}</span>
                                @if($file->document_code)
                                <span>{{ $file->document_code }}</span>
                                @endif
                            </div>
                        </div>

                        <!-- Stats -->
                        <div class="flex-shrink-0 text-right">
                            <div class="text-sm font-semibold text-gray-900">{{ number_format($file->download_count) }}</div>
                            <div class="text-xs text-gray-500">downloads</div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="p-6 text-center text-gray-500">
                    <span class="material-symbols-sharp text-4xl text-gray-300 mb-2">inbox</span>
                    <p>No download activity yet</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <span class="material-symbols-sharp text-red-600 mr-2">history</span>
                    Recent Activity
                </h3>
                <p class="text-sm text-gray-600 mt-1">Latest user interactions</p>
            </div>
            <div class="max-h-96 overflow-y-auto">
                <div class="divide-y divide-gray-200">
                    @forelse($recentActivity as $activity)
                    <div class="p-4">
                        <div class="flex items-start space-x-3">
                            <!-- Action Icon -->
                            <div class="flex-shrink-0">
                                @if($activity->action === 'download')
                                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                        <span class="material-symbols-sharp text-green-600 text-sm">download</span>
                                    </div>
                                @elseif($activity->action === 'view')
                                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                        <span class="material-symbols-sharp text-blue-600 text-sm">visibility</span>
                                    </div>
                                @else
                                    <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center">
                                        <span class="material-symbols-sharp text-gray-600 text-sm">history</span>
                                    </div>
                                @endif
                            </div>

                            <!-- Activity Details -->
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900">
                                    {{ $activity->user->name ?? 'Unknown User' }}
                                </p>
                                <p class="text-sm text-gray-600">
                                    {{ ucfirst($activity->action) }}d
                                    <a href="{{ route('master-file.show', $activity->file) }}" class="text-blue-600 hover:text-blue-800 font-medium">
                                        {{ Str::limit($activity->file->title, 30) }}
                                    </a>
                                </p>
                                <div class="flex items-center space-x-2 mt-1 text-xs text-gray-500">
                                    <span>{{ $activity->department }}</span>
                                    <span>•</span>
                                    <span>{{ $activity->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="p-6 text-center text-gray-500">
                        <span class="material-symbols-sharp text-4xl text-gray-300 mb-2">history</span>
                        <p>No recent activity</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Department Statistics -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <span class="material-symbols-sharp text-orange-600 mr-2">corporate_fare</span>
                Department Statistics
            </h3>
            <p class="text-sm text-gray-600 mt-1">File usage by department</p>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($departmentStats as $dept => $stats)
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="flex items-center justify-between mb-3">
                        <h4 class="font-semibold text-gray-900">{{ $dept }}</h4>
                        <span class="text-sm text-gray-500">{{ $stats['files'] }} files</span>
                    </div>
                    <div class="space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Downloads:</span>
                            <span class="font-medium text-gray-900">{{ number_format($stats['downloads']) }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Views:</span>
                            <span class="font-medium text-gray-900">{{ number_format($stats['views']) }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Active Users:</span>
                            <span class="font-medium text-gray-900">{{ $stats['active_users'] }}</span>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- File Type Distribution -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <span class="material-symbols-sharp text-indigo-600 mr-2">insert_drive_file</span>
                File Type Distribution
            </h3>
            <p class="text-sm text-gray-600 mt-1">Breakdown by file format</p>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                @foreach($fileTypeStats as $type => $count)
                <div class="text-center p-4 bg-gray-50 rounded-lg">
                    <div class="w-12 h-12 mx-auto mb-2 flex items-center justify-center">
                        @if($type === 'pdf')
                            <span class="material-symbols-sharp text-red-500 text-2xl">picture_as_pdf</span>
                        @elseif(in_array($type, ['doc', 'docx']))
                            <span class="material-symbols-sharp text-blue-500 text-2xl">description</span>
                        @elseif(in_array($type, ['xls', 'xlsx']))
                            <span class="material-symbols-sharp text-green-500 text-2xl">table_chart</span>
                        @elseif(in_array($type, ['ppt', 'pptx']))
                            <span class="material-symbols-sharp text-orange-500 text-2xl">slideshow</span>
                        @else
                            <span class="material-symbols-sharp text-gray-400 text-2xl">insert_drive_file</span>
                        @endif
                    </div>
                    <div class="text-lg font-bold text-gray-900">{{ $count }}</div>
                    <div class="text-xs text-gray-500 uppercase">{{ $type }}</div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('livewire:initialized', () => {
    initCharts();
});

function initCharts() {
    // Activity Chart
    const activityCtx = document.getElementById('activityChart');
    if (activityCtx) {
        const activityData = @json($dailyActivity);

        new Chart(activityCtx, {
            type: 'line',
            data: {
                labels: activityData.map(item => item.date),
                datasets: [
                    {
                        label: 'Downloads',
                        data: activityData.map(item => item.downloads),
                        borderColor: 'rgb(34, 197, 94)',
                        backgroundColor: 'rgba(34, 197, 94, 0.1)',
                        fill: true,
                        tension: 0.4
                    },
                    {
                        label: 'Views',
                        data: activityData.map(item => item.views),
                        borderColor: 'rgb(59, 130, 246)',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        fill: true,
                        tension: 0.4
                    }
                ]
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
                            stepSize: 1
                        }
                    }
                }
            }
        });
    }

    // Category Chart
    const categoryCtx = document.getElementById('categoryChart');
    if (categoryCtx) {
        const categoryData = @json($categoryStats);

        new Chart(categoryCtx, {
            type: 'doughnut',
            data: {
                labels: categoryData.map(item => item.name),
                datasets: [{
                    data: categoryData.map(item => item.files_count),
                    backgroundColor: categoryData.map(item => item.color + '80'),
                    borderColor: categoryData.map(item => item.color),
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                    }
                }
            }
        });
    }
}
</script>
@endpush
