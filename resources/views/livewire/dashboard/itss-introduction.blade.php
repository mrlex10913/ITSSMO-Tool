<div class="space-y-6">
    <!-- Top stats -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="rounded-xl border border-slate-700 bg-slate-800 text-slate-100 p-4">
            <div class="flex items-start justify-between">
                <div>
                    <div class="text-sm text-slate-300">Total Users</div>
                    <div class="mt-1 text-3xl font-semibold">{{ number_format($totalUsers) }}</div>
                    <div class="mt-1 text-xs text-emerald-400">+12% from last month</div>
                </div>
                <div class="h-10 w-10 rounded-lg bg-slate-700 flex items-center justify-center">
                    <span class="material-symbols-sharp text-indigo-300">group</span>
                </div>
            </div>
        </div>
        <div class="rounded-xl border border-slate-700 bg-slate-800 text-slate-100 p-4">
            <div class="flex items-start justify-between">
                <div>
                    <div class="text-sm text-slate-300">Active Sessions</div>
                    <div class="mt-1 text-3xl font-semibold">{{ number_format($activeSessions) }}</div>
                    <div class="mt-1 text-xs text-emerald-400">+8% from yesterday</div>
                </div>
                <div class="h-10 w-10 rounded-lg bg-emerald-700/30 flex items-center justify-center">
                    <span class="material-symbols-sharp text-emerald-400">bolt</span>
                </div>
            </div>
        </div>
        <div class="rounded-xl border border-slate-700 bg-slate-800 text-slate-100 p-4">
            <div class="flex items-start justify-between">
                <div>
                    <div class="text-sm text-slate-300">System Health</div>
                    <div class="mt-1 text-3xl font-semibold">{{ number_format($systemHealthPercent, 1) }}%</div>
                    <div class="mt-1 text-xs"><span class="text-emerald-400">{{ $systemHealthLabel }}</span> <span class="text-slate-400"> All systems operational</span></div>
                </div>
                <div class="h-10 w-10 rounded-lg bg-amber-700/30 flex items-center justify-center">
                    <span class="material-symbols-sharp text-amber-400">verified</span>
                </div>
            </div>
        </div>
        <div class="rounded-xl border border-slate-700 bg-slate-800 text-slate-100 p-4">
            <div class="flex items-start justify-between">
                <div>
                    <div class="text-sm text-slate-300">Pending Requests</div>
                    <div class="mt-1 text-3xl font-semibold">{{ number_format($pendingRequests) }}</div>
                    <div class="mt-1 text-xs text-rose-400">Needs attention &nbsp; <span class="text-slate-400">Review required</span></div>
                </div>
                <div class="h-10 w-10 rounded-lg bg-rose-700/30 flex items-center justify-center">
                    <span class="material-symbols-sharp text-rose-400">error</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Content -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <!-- Recent activities -->
        <div class="lg:col-span-2 rounded-xl border border-slate-700 bg-slate-800 text-slate-100">
            <div class="flex items-center justify-between p-4 border-b border-slate-700">
                <div class="text-lg font-semibold">Recent Activities</div>
                <a href="{{ route('itss.helpdesk') }}" class="text-amber-400 text-sm">View All</a>
            </div>
            <div class="p-4 space-y-3">
                @forelse($recentActivities as $a)
                    <div class="rounded-lg bg-slate-700/50 px-4 py-3">
                        <div class="font-medium">{{ $a['title'] }}</div>
                        <div class="text-xs text-slate-300">{{ $a['by'] ? $a['by'].' · ' : '' }}{{ $a['at'] }}</div>
                    </div>
                @empty
                    <div class="text-slate-300 text-sm">No recent activities.</div>
                @endforelse
            </div>
        </div>

        <!-- Quick actions -->
        <div class="space-y-4">
            <div class="rounded-xl border border-slate-700 bg-slate-800 text-slate-100">
                <div class="p-4 border-b border-slate-700 text-lg font-semibold">Quick Actions</div>
                <div class="p-4 space-y-3">
                    <a wire:navigate href="{{ route('controlPanel.user') }}" class="flex items-center gap-3 rounded-lg bg-amber-700/30 hover:bg-amber-700/40 px-4 py-3">
                        <span class="material-symbols-sharp">add</span>
                        <span>Add New User</span>
                    </a>
                    <a wire:navigate href="{{ route('controlPanel.reports.surveys') }}" class="flex items-center gap-3 rounded-lg bg-slate-700/70 hover:bg-slate-700 px-4 py-3">
                        <span class="material-symbols-sharp">insights</span>
                        <span>View Reports</span>
                    </a>
                    <a wire:navigate href="{{ route('controlPanel.menus') }}" class="flex items-center gap-3 rounded-lg bg-slate-700/70 hover:bg-slate-700 px-4 py-3">
                        <span class="material-symbols-sharp">settings</span>
                        <span>System Settings</span>
                    </a>
                </div>
            </div>

            <!-- System status -->
            <div class="rounded-xl border border-slate-700 bg-slate-800 text-slate-100">
                <div class="p-4 border-b border-slate-700 text-lg font-semibold">System Status</div>
                <div class="p-4 space-y-3 text-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-300">Database</span>
                        <span class="{{ $status['database'] === 'Online' ? 'text-emerald-400' : 'text-rose-400' }}">• {{ $status['database'] }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-300">API Services</span>
                        <span class="text-emerald-400">• {{ $status['api'] }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-300">File Storage</span>
                        <span class="{{ $status['storage'] === 'Online' ? 'text-emerald-400' : 'text-amber-400' }}">• {{ $status['storage'] }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-300">Email Service</span>
                        <span class="{{ $status['email'] === 'Online' ? 'text-emerald-400' : 'text-amber-400' }}">• {{ $status['email'] }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


{{-- <div class="py-6">
    <div class="max-w-full mx-auto sm:px-6 lg:px-4 max-h-[80vh] overflow-auto">
        <div class="bg-gray-100 dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg text-gray-100">
            <div class="grid grid-cols-2 grid-rows-2 gap-4 pb-10 px-6">
                <div class="col-span-2 row-span-3 grid place-items-center mb-10">
                    <div class="flex flex-col items-center">
                        <img src="{{asset('images/ITSSMO EDIT DRAFT.png')}}" alt="" class="w-96">
                        <h1 class="font-bold text-2xl text-wrap px-10 text-center">INFORMATION TECHNOLOGY SYSTEMS AND SERVICES MANAGEMENT OFFICE</h1>
                    </div>
                </div>
                <div class="row-start-4">
                    <div class="h-full w-full bg-gray-600 rounded-md bg-clip-padding backdrop-filter backdrop-blur-lg bg-opacity-70 border border-gray-100 p-4">
                        <h1 class="text-center text-2xl font-bold">Vission</h1>
                        <hr class="mx-24">
                        <p class="mx-24 text-xl">
                            To provide reliable and ongoing technology resources to faculty, staff and students that support the University’s Mission to advance the skills necessary to succeed in an increasingly competitive industry.
                        </p>
                    </div>
                </div>
                <div class="row-start-4">
                    <div class="h-full w-full bg-gray-600 rounded-md bg-clip-padding backdrop-filter backdrop-blur-lg bg-opacity-70 border border-gray-100 p-4">
                        <h1 class="text-center text-2xl">Mission</h1>
                        <hr class="mx-24">
                        <p class="mx-24 text-xl">
                            The ITSSMO aims to provide the University with technology solutions and support that establish and maintains an effective operational environment and deliver quality, prompt, cost-effective and reliable technology services that will be of service to the university’s vision and mission.
                        </p>
                    </div>
            </div>
            </div>
        </div>
    </div>
</div> --}}

