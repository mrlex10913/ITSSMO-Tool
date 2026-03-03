<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Document Library</h1>
            <p class="mt-2 text-sm text-gray-600">Centralized document management and version control system</p>
        </div>
        <div class="mt-4 sm:mt-0 flex space-x-3">
            <button wire:click="$refresh" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                <span class="material-symbols-sharp text-sm mr-2">refresh</span>
                Refresh
            </button>
            <a href="{{ route('document-library.upload') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white hover:bg-blue-700 transition-colors">
                <span class="material-symbols-sharp text-sm mr-2">upload_file</span>
                Upload Document
            </a>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-6">
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
            <div class="flex items-center">
                <div class="p-3 rounded-lg bg-blue-100">
                    <span class="material-symbols-sharp text-blue-600">folder</span>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Files</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_files']) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
            <div class="flex items-center">
                <div class="p-3 rounded-lg bg-green-100">
                    <span class="material-symbols-sharp text-green-600">check_circle</span>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Active Files</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['active_files']) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
            <div class="flex items-center">
                <div class="p-3 rounded-lg bg-purple-100">
                    <span class="material-symbols-sharp text-purple-600">category</span>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Categories</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['categories']) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
            <div class="flex items-center">
                <div class="p-3 rounded-lg bg-cyan-100">
                    <span class="material-symbols-sharp text-cyan-600">folder_open</span>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Folders</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['folders']) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
            <div class="flex items-center">
                <div class="p-3 rounded-lg bg-indigo-100">
                    <span class="material-symbols-sharp text-indigo-600">download</span>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Downloads</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_downloads']) }}</p>
                </div>
            </div>
        </div>

        @if($stats['pending_approval'] > 0)
        <div class="bg-white rounded-xl shadow-sm p-6 border border-yellow-200">
            <div class="flex items-center">
                <div class="p-3 rounded-lg bg-yellow-100">
                    <span class="material-symbols-sharp text-yellow-600">pending</span>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Pending</p>
                    <p class="text-2xl font-bold text-yellow-900">{{ number_format($stats['pending_approval']) }}</p>
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- My Folders Section -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="p-6 border-b border-gray-200 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <span class="material-symbols-sharp text-indigo-600 mr-2">folder_open</span>
                My Folders
            </h3>
            <a href="{{ route('document-library.folders') }}" class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                View all →
            </a>
        </div>
        <div class="p-6">
            @if($folders->count() > 0)
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 xl:grid-cols-8 gap-4">
                @foreach($folders as $folder)
                <a href="{{ route('document-library.folders', $folder->id) }}"
                   class="group p-4 rounded-xl border-2 border-transparent hover:border-indigo-200 hover:bg-indigo-50 transition-all text-center">
                    <div class="flex flex-col items-center">
                        <div class="w-14 h-14 rounded-xl flex items-center justify-center mb-3" style="background-color: {{ $folder->color }}20;">
                            <span class="material-symbols-sharp text-3xl" style="color: {{ $folder->color }};">folder</span>
                        </div>
                        <p class="text-sm font-medium text-gray-900 truncate w-full" title="{{ $folder->name }}">{{ $folder->name }}</p>
                        <p class="text-xs text-gray-500 mt-1">
                            {{ $folder->children->count() }} folders · {{ $folder->files->count() }} files
                        </p>
                    </div>
                </a>
                @endforeach
            </div>
            @else
            <div class="text-center py-8">
                <span class="material-symbols-sharp text-4xl text-gray-300">create_new_folder</span>
                <p class="mt-2 text-sm text-gray-500">No folders yet</p>
                <a href="{{ route('document-library.folders') }}" class="mt-3 inline-flex items-center px-4 py-2 bg-indigo-100 text-indigo-700 rounded-lg hover:bg-indigo-200 transition-colors text-sm">
                    <span class="material-symbols-sharp text-sm mr-2">add</span>
                    Create your first folder
                </a>
            </div>
            @endif
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Recent Files -->
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <span class="material-symbols-sharp text-blue-600 mr-2">history</span>
                    Recent Documents
                </h3>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @forelse($recentFiles as $file)
                    <div class="flex items-center space-x-4 p-4 rounded-lg hover:bg-gray-50 transition-colors">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background-color: {{ $file->category->color }}20;">
                                <span class="material-symbols-sharp text-sm" style="color: {{ $file->category->color }};">{{ $file->category->icon }}</span>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">{{ $file->title }}</p>
                            <div class="flex items-center space-x-2">
                                <p class="text-xs text-gray-500">{{ $file->category->name }} • v{{ $file->version }} • {{ $file->uploader->name }}</p>
                                @if($file->status === 'superseded')
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">
                                    Outdated
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="text-xs text-gray-500">{{ $file->created_at->diffForHumans() }}</span>
                            <a href="{{ route('document-library.show', $file) }}" class="text-blue-600 hover:text-blue-800">
                                <span class="material-symbols-sharp text-sm">visibility</span>
                            </a>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-8">
                        <span class="material-symbols-sharp text-4xl text-gray-300">folder_open</span>
                        <p class="mt-2 text-sm text-gray-500">No recent documents</p>
                    </div>
                    @endforelse
                </div>
                @if($recentFiles->count() > 0)
                <div class="mt-6 pt-4 border-t border-gray-200">
                    <a href="{{ route('document-library.search') }}" class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                        View all documents →
                    </a>
                </div>
                @endif
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Quick Actions -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
                <div class="space-y-3">
                    <a href="{{ route('document-library.folders') }}" class="w-full inline-flex items-center justify-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                        <span class="material-symbols-sharp text-sm mr-2">folder_open</span>
                        My Drive
                    </a>
                    <a href="{{ route('document-library.upload') }}" class="w-full inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <span class="material-symbols-sharp text-sm mr-2">upload_file</span>
                        Upload Document
                    </a>
                    @if(auth()->user()->hasRole(['administrator', 'developer']))
                    <a href="{{ route('document-library.categories') }}" class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                        <span class="material-symbols-sharp text-sm mr-2">category</span>
                        Manage Categories
                    </a>
                    @endif
                    <a href="{{ route('document-library.search') }}" class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                        <span class="material-symbols-sharp text-sm mr-2">search</span>
                        Search Documents
                    </a>
                </div>
            </div>

            <!-- Popular Files -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <span class="material-symbols-sharp text-green-600 mr-2">trending_up</span>
                    Popular Documents
                </h3>
                <div class="space-y-3">
                    @forelse($popularFiles as $file)
                    <div class="flex items-center space-x-3">
                        <div class="flex-shrink-0 w-8 h-8 rounded-lg flex items-center justify-center" style="background-color: {{ $file->category->color }}20;">
                            <span class="material-symbols-sharp text-xs" style="color: {{ $file->category->color }};">{{ $file->category->icon }}</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">{{ $file->title }}</p>
                            <p class="text-xs text-gray-500">{{ $file->download_count }} downloads</p>
                        </div>
                    </div>
                    @empty
                    <p class="text-sm text-gray-500">No popular documents yet</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Activity Chart -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <span class="material-symbols-sharp text-purple-600 mr-2">analytics</span>
                Activity Overview
            </h3>
            <select wire:model="selectedPeriod" class="text-sm border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                <option value="7">Last 7 days</option>
                <option value="30">Last 30 days</option>
                <option value="90">Last 90 days</option>
            </select>
        </div>
        <div class="h-64">
            <canvas id="activityChart" wire:ignore></canvas>
        </div>
    </div>
</div>

@push('scripts')
<script>
let activityChart;

document.addEventListener('DOMContentLoaded', function() {
    initActivityChart();
});

Livewire.on('refresh', () => {
    if (activityChart) {
        activityChart.destroy();
    }
    setTimeout(() => initActivityChart(), 100);
});

function initActivityChart() {
    const ctx = document.getElementById('activityChart');
    if (!ctx) return;

    const activityData = @json($activityData);
    const labels = Object.keys(activityData);
    const data = Object.values(activityData);

    activityChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Document Access',
                data: data,
                borderColor: 'rgb(59, 130, 246)',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                fill: true,
                tension: 0.4
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
                        stepSize: 1
                    }
                }
            }
        }
    });
}
</script>
@endpush
