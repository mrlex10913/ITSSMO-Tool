<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-50 flex items-start justify-center py-8">
    <div class="w-full max-w-6xl">
        <!-- Main Under Development Card (styled like BFO) -->
        <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
            <!-- Header Section -->
            <div class="bg-gradient-to-r from-indigo-600 to-blue-600 px-8 py-10 text-center">
                <div class="inline-flex items-center justify-center w-24 h-24 bg-white/20 rounded-full mb-6">
                    <i class="fas fa-bullhorn text-4xl text-white"></i>
                </div>
                <h1 class="text-4xl font-bold text-white mb-2">Announcements & News</h1>
                <p class="text-indigo-100">Your central hub for updates across teams and departments</p>
            </div>

            <!-- Content Section -->
            <div class="px-8 py-10">
                <div class="text-center mb-10">
                    <div class="inline-flex items-center justify-center w-28 h-28 bg-gradient-to-br from-yellow-400 to-orange-500 rounded-full mb-6 shadow-lg">
                        <i class="fas fa-hard-hat text-4xl text-white"></i>
                    </div>
                    <h2 class="text-3xl font-bold text-gray-800 mb-2">Under Development</h2>
                    <p class="text-lg text-gray-600 max-w-2xl mx-auto leading-relaxed">
                        This page is still under development. News and announcements for your role will appear here soon.
                        In the meantime, use the quick links below.
                    </p>
                </div>

                <!-- Features Coming Soon -->
                <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
                    <div class="text-center p-6 bg-blue-50 rounded-xl border-2 border-blue-100 hover:border-blue-300 transition-colors feature-card">
                        <div class="w-16 h-16 bg-blue-500 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-bell text-2xl text-white"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-800 mb-1">Real-time Alerts</h3>
                        <p class="text-gray-600 text-sm">Instant notifications for urgent updates</p>
                    </div>
                    <div class="text-center p-6 bg-emerald-50 rounded-xl border-2 border-emerald-100 hover:border-emerald-300 transition-colors feature-card">
                        <div class="w-16 h-16 bg-emerald-500 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-users text-2xl text-white"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-800 mb-1">Department Posts</h3>
                        <p class="text-gray-600 text-sm">Updates targeted to your department</p>
                    </div>
                    <div class="text-center p-6 bg-purple-50 rounded-xl border-2 border-purple-100 hover:border-purple-300 transition-colors feature-card">
                        <div class="w-16 h-16 bg-purple-500 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-paperclip text-2xl text-white"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-800 mb-1">Attachments</h3>
                        <p class="text-gray-600 text-sm">Policies, docs, and reference files</p>
                    </div>
                    <div class="text-center p-6 bg-amber-50 rounded-xl border-2 border-amber-100 hover:border-amber-300 transition-colors feature-card">
                        <div class="w-16 h-16 bg-amber-500 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-newspaper text-2xl text-white"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-800 mb-1">News Feed</h3>
                        <p class="text-gray-600 text-sm">Chronological timeline of updates</p>
                    </div>
                </div>

                <!-- Progress Bar (visual flair like BFO) -->
                <div class="bg-gray-100 rounded-xl p-8 mb-8">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-xl font-semibold text-gray-800">Development Progress</h3>
                        <span class="text-2xl font-bold text-indigo-600">35%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3 mb-3">
                        <div class="bg-gradient-to-r from-indigo-500 to-blue-500 h-3 rounded-full transition-all duration-1000 ease-out progress-bar" style="width: 35%"></div>
                    </div>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                        <div class="flex items-center"><i class="fas fa-check-circle text-indigo-500 mr-2"></i><span class="text-gray-600">Layout</span></div>
                        <div class="flex items-center"><i class="fas fa-check-circle text-indigo-500 mr-2"></i><span class="text-gray-600">Routing</span></div>
                        <div class="flex items-center"><i class="fas fa-clock text-amber-500 mr-2"></i><span class="text-gray-600">Announcements</span></div>
                        <div class="flex items-center"><i class="fas fa-clock text-amber-500 mr-2"></i><span class="text-gray-600">News Feed</span></div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="text-center space-y-4">
                    <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                        <button onclick="window.location.reload()"
                                class="px-8 py-3 bg-gradient-to-r from-indigo-600 to-blue-600 text-white font-semibold rounded-xl hover:from-indigo-700 hover:to-blue-700 transition-all duration-300 transform hover:scale-105 shadow-lg">
                            <i class="fas fa-sync-alt mr-2"></i>
                            Check for Updates
                        </button>

                        <a href="{{ route('generic.helpdesk') }}"
                           class="px-8 py-3 bg-gray-100 text-gray-700 font-semibold rounded-xl hover:bg-gray-200 transition-all duration-300 border-2 border-gray-300 hover:border-gray-400">
                            <i class="fas fa-life-ring mr-2"></i>
                            Open Helpdesk
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Links from Menu -->
        <div class="mt-8">
            <div class="mb-2 flex items-center gap-2">
                <span class="material-symbols-sharp text-slate-500">apps</span>
                <h3 class="text-sm font-semibold text-slate-600 tracking-wide">Quick Links</h3>
            </div>
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($menu as $item)
                    @php
                        $href = isset($item['route']) && $item['route'] ? route($item['route']) : ($item['url'] ?? '#');
                    @endphp
                    <a href="{{ $href }}" class="block p-6 bg-white rounded-xl shadow-sm hover:shadow-md transition border border-slate-100">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center">
                                <span class="material-symbols-sharp">{{ $item['icon'] ?? 'apps' }}</span>
                            </div>
                            <div class="font-semibold text-gray-800">{{ $item['label'] }}</div>
                        </div>
                    </a>
                @endforeach
                @if(empty($menu))
                    <div class="col-span-full">
                        <div class="rounded-xl border border-dashed border-slate-200 p-6 text-center text-slate-500">
                            <p>No quick links available for your role yet.</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Minor animations like BFO -->
    <style>
        @keyframes bounce { 0%,20%,53%,80%,100%{ transform: translate3d(0,0,0);} 40%,43%{ transform: translate3d(0,-8px,0);} 70%{ transform: translate3d(0,-4px,0);} 90%{ transform: translate3d(0,-2px,0);} }
        .fas.fa-hard-hat { animation: bounce 2s infinite; }
        .feature-card:hover { transform: translateY(-5px); box-shadow: 0 10px 25px rgba(0,0,0,0.1); }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const progress = document.querySelector('.progress-bar');
            if (progress) {
                progress.style.width = '0%';
                setTimeout(() => { progress.style.width = '35%'; }, 400);
            }
        });
    </script>
</div>
