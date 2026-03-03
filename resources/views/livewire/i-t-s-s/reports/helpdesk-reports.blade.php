<div class="p-6 max-w-7xl mx-auto">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Helpdesk Reports</h1>
            <p class="text-sm text-gray-500">Analytics and performance metrics for helpdesk operations</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="text-sm text-gray-500">Period:</span>
            <select wire:model.live="periodDays" class="border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500">
                <option value="7">Last 7 days</option>
                <option value="14">Last 14 days</option>
                <option value="30">Last 30 days</option>
                <option value="60">Last 60 days</option>
                <option value="90">Last 90 days</option>
            </select>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="border-b border-gray-200 mb-6">
        <nav class="flex gap-4" aria-label="Tabs">
            @foreach(['overview' => 'Overview', 'agents' => 'Agent Performance', 'sla' => 'SLA Compliance'] as $tab => $label)
                <button wire:click="setTab('{{ $tab }}')"
                    class="py-2 px-1 border-b-2 text-sm font-medium {{ $activeTab === $tab ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    {{ $label }}
                </button>
            @endforeach
        </nav>
    </div>

    {{-- Overview Tab --}}
    @if($activeTab === 'overview')
        {{-- Summary Cards --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow p-4">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-500">Tickets Created</span>
                    @if(($summary['created_change'] ?? 0) != 0)
                        <span class="text-xs px-1.5 py-0.5 rounded {{ $summary['created_change'] > 0 ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }}">
                            {{ $summary['created_change'] > 0 ? '+' : '' }}{{ $summary['created_change'] }}%
                        </span>
                    @endif
                </div>
                <div class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($summary['created'] ?? 0) }}</div>
            </div>
            <div class="bg-white rounded-lg shadow p-4">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-500">Tickets Resolved</span>
                    @if(($summary['resolved_change'] ?? 0) != 0)
                        <span class="text-xs px-1.5 py-0.5 rounded {{ $summary['resolved_change'] > 0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            {{ $summary['resolved_change'] > 0 ? '+' : '' }}{{ $summary['resolved_change'] }}%
                        </span>
                    @endif
                </div>
                <div class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($summary['resolved'] ?? 0) }}</div>
            </div>
            <div class="bg-white rounded-lg shadow p-4">
                <span class="text-sm text-gray-500">Open Tickets</span>
                <div class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($summary['open'] ?? 0) }}</div>
            </div>
            <div class="bg-white rounded-lg shadow p-4">
                <span class="text-sm text-gray-500">CSAT Score</span>
                <div class="text-2xl font-bold {{ ($summary['csat_score'] ?? 0) >= 80 ? 'text-green-600' : (($summary['csat_score'] ?? 0) >= 60 ? 'text-yellow-600' : 'text-red-600') }} mt-1">
                    {{ $summary['csat_score'] !== null ? $summary['csat_score'] . '%' : '—' }}
                </div>
            </div>
        </div>

        {{-- Secondary Stats --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-gray-50 rounded-lg p-4">
                <span class="text-xs text-gray-500">Avg Resolution Time</span>
                <div class="text-lg font-semibold text-gray-800">
                    {{ $summary['avg_resolution_hours'] !== null ? $summary['avg_resolution_hours'] . 'h' : '—' }}
                </div>
            </div>
            <div class="bg-gray-50 rounded-lg p-4">
                <span class="text-xs text-gray-500">Avg First Response</span>
                <div class="text-lg font-semibold text-gray-800">
                    {{ $summary['avg_frt_mins'] !== null ? $summary['avg_frt_mins'] . 'm' : '—' }}
                </div>
            </div>
            <div class="bg-gray-50 rounded-lg p-4">
                <span class="text-xs text-gray-500">Incidents</span>
                <div class="text-lg font-semibold text-gray-800">{{ $summary['by_type']['incident'] ?? 0 }}</div>
            </div>
            <div class="bg-gray-50 rounded-lg p-4">
                <span class="text-xs text-gray-500">Service Requests</span>
                <div class="text-lg font-semibold text-gray-800">{{ $summary['by_type']['request'] ?? 0 }}</div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            {{-- Ticket Volume Chart --}}
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-md font-medium text-gray-900 mb-4">Ticket Volume Trends</h3>
                <div class="h-64" x-data="volumeChart(@js($volumeTrends))" x-init="initChart()">
                    <canvas x-ref="chart"></canvas>
                </div>
            </div>

            {{-- Status Breakdown --}}
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-md font-medium text-gray-900 mb-4">Status Distribution</h3>
                <div class="space-y-3">
                    @php
                        $statusColors = [
                            'open' => 'bg-blue-500',
                            'in_progress' => 'bg-yellow-500',
                            'resolved' => 'bg-green-500',
                            'closed' => 'bg-gray-500',
                        ];
                        $total = array_sum($summary['by_status'] ?? []);
                    @endphp
                    @foreach($summary['by_status'] ?? [] as $status => $count)
                        @php $pct = $total > 0 ? round(($count / $total) * 100, 1) : 0; @endphp
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="text-gray-600">{{ ucwords(str_replace('_', ' ', $status)) }}</span>
                                <span class="font-medium">{{ $count }} ({{ $pct }}%)</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="{{ $statusColors[$status] ?? 'bg-gray-400' }} h-2 rounded-full" style="width: {{ $pct }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Priority Breakdown --}}
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-md font-medium text-gray-900 mb-4">By Priority</h3>
                <div class="space-y-3">
                    @php
                        $priorityColors = [
                            'low' => 'bg-gray-400',
                            'medium' => 'bg-blue-400',
                            'high' => 'bg-orange-500',
                            'critical' => 'bg-red-500',
                        ];
                        $priorityTotal = array_sum($summary['by_priority'] ?? []);
                    @endphp
                    @foreach(['critical', 'high', 'medium', 'low'] as $priority)
                        @php 
                            $count = $summary['by_priority'][$priority] ?? 0;
                            $pct = $priorityTotal > 0 ? round(($count / $priorityTotal) * 100, 1) : 0;
                        @endphp
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="text-gray-600">{{ ucfirst($priority) }}</span>
                                <span class="font-medium">{{ $count }} ({{ $pct }}%)</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="{{ $priorityColors[$priority] }} h-2 rounded-full" style="width: {{ $pct }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Top Categories --}}
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-md font-medium text-gray-900 mb-4">Top Categories</h3>
                @if($topCategories->isEmpty())
                    <p class="text-sm text-gray-500">No categorized tickets in this period.</p>
                @else
                    <div class="space-y-2">
                        @foreach($topCategories as $cat)
                            <div class="flex justify-between items-center py-1 border-b border-gray-100 last:border-0">
                                <span class="text-sm text-gray-700">{{ $cat['name'] }}</span>
                                <span class="text-sm font-medium text-gray-900">{{ $cat['count'] }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    @endif

    {{-- Agent Performance Tab --}}
    @if($activeTab === 'agents')
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b">
                <h3 class="text-md font-medium text-gray-900">Agent Performance Metrics</h3>
                <p class="text-sm text-gray-500">Performance data for the selected period</p>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Agent</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Assigned</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Resolved</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Resolution Rate</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Avg Resolution</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Avg FRT</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">SLA %</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">CSAT</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Time Logged</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($agentPerformance as $agent)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="font-medium text-gray-900">{{ $agent['name'] }}</div>
                                </td>
                                <td class="px-4 py-3 text-center text-sm text-gray-600">{{ $agent['assigned'] }}</td>
                                <td class="px-4 py-3 text-center text-sm text-gray-600">{{ $agent['resolved'] }}</td>
                                <td class="px-4 py-3 text-center">
                                    <span class="text-sm font-medium {{ $agent['resolution_rate'] >= 80 ? 'text-green-600' : ($agent['resolution_rate'] >= 50 ? 'text-yellow-600' : 'text-red-600') }}">
                                        {{ $agent['resolution_rate'] }}%
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center text-sm text-gray-600">
                                    {{ $agent['avg_resolution_hours'] !== null ? $agent['avg_resolution_hours'] . 'h' : '—' }}
                                </td>
                                <td class="px-4 py-3 text-center text-sm text-gray-600">
                                    {{ $agent['avg_frt_mins'] !== null ? $agent['avg_frt_mins'] . 'm' : '—' }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if($agent['sla_compliance'] !== null)
                                        <span class="px-2 py-1 rounded text-xs font-medium {{ $agent['sla_compliance'] >= 90 ? 'bg-green-100 text-green-800' : ($agent['sla_compliance'] >= 70 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                            {{ $agent['sla_compliance'] }}%
                                        </span>
                                    @else
                                        <span class="text-sm text-gray-400">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if($agent['csat_score'] !== null)
                                        <span class="text-sm font-medium {{ $agent['csat_score'] >= 80 ? 'text-green-600' : ($agent['csat_score'] >= 60 ? 'text-yellow-600' : 'text-red-600') }}">
                                            {{ $agent['csat_score'] }}%
                                        </span>
                                        <span class="text-xs text-gray-400">({{ $agent['csat_total'] }})</span>
                                    @else
                                        <span class="text-sm text-gray-400">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center text-sm text-gray-600">{{ $agent['time_logged_hours'] }}h</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-4 py-8 text-center text-gray-500">No agent data available for this period.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    {{-- SLA Compliance Tab --}}
    @if($activeTab === 'sla')
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow p-6 text-center">
                <div class="text-4xl font-bold {{ ($slaCompliance['compliance_rate'] ?? 0) >= 90 ? 'text-green-600' : (($slaCompliance['compliance_rate'] ?? 0) >= 70 ? 'text-yellow-600' : 'text-red-600') }}">
                    {{ $slaCompliance['compliance_rate'] ?? 0 }}%
                </div>
                <div class="text-sm text-gray-500 mt-1">Overall SLA Compliance</div>
            </div>
            <div class="bg-white rounded-lg shadow p-6 text-center">
                <div class="text-4xl font-bold text-green-600">{{ $slaCompliance['compliant'] ?? 0 }}</div>
                <div class="text-sm text-gray-500 mt-1">Tickets Met SLA</div>
            </div>
            <div class="bg-white rounded-lg shadow p-6 text-center">
                <div class="text-4xl font-bold text-red-600">{{ $slaCompliance['breached'] ?? 0 }}</div>
                <div class="text-sm text-gray-500 mt-1">Tickets Breached SLA</div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- By Priority --}}
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-md font-medium text-gray-900 mb-4">SLA Compliance by Priority</h3>
                <div class="space-y-4">
                    @foreach($slaCompliance['by_priority'] ?? [] as $item)
                        @if($item['total'] > 0)
                            <div>
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="font-medium text-gray-700">{{ ucfirst($item['priority']) }}</span>
                                    <span class="{{ $item['compliance_rate'] >= 90 ? 'text-green-600' : ($item['compliance_rate'] >= 70 ? 'text-yellow-600' : 'text-red-600') }} font-medium">
                                        {{ $item['compliance_rate'] }}%
                                    </span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-3 relative">
                                    <div class="{{ $item['compliance_rate'] >= 90 ? 'bg-green-500' : ($item['compliance_rate'] >= 70 ? 'bg-yellow-500' : 'bg-red-500') }} h-3 rounded-full" style="width: {{ $item['compliance_rate'] }}%"></div>
                                </div>
                                <div class="text-xs text-gray-500 mt-1">
                                    {{ $item['compliant'] }} compliant / {{ $item['breached'] }} breached ({{ $item['total'] }} total)
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>

            {{-- Weekly Trend --}}
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-md font-medium text-gray-900 mb-4">Weekly SLA Trend</h3>
                <div class="space-y-4">
                    @foreach($slaCompliance['weekly_trend'] ?? [] as $week)
                        <div class="flex items-center justify-between py-2 border-b border-gray-100 last:border-0">
                            <div>
                                <div class="font-medium text-gray-800">Week of {{ $week['week'] }}</div>
                                <div class="text-xs text-gray-500">{{ $week['total'] }} tickets</div>
                            </div>
                            <div class="text-right">
                                <div class="text-lg font-bold {{ $week['compliance_rate'] >= 90 ? 'text-green-600' : ($week['compliance_rate'] >= 70 ? 'text-yellow-600' : 'text-red-600') }}">
                                    {{ $week['compliance_rate'] }}%
                                </div>
                                <div class="text-xs text-gray-500">
                                    <span class="text-green-600">{{ $week['compliant'] }} met</span> /
                                    <span class="text-red-600">{{ $week['breached'] }} breach</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    function volumeChart(data) {
        return {
            chart: null,
            initChart() {
                const ctx = this.$refs.chart.getContext('2d');
                this.chart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: data.map(d => d.label),
                        datasets: [
                            {
                                label: 'Created',
                                data: data.map(d => d.created),
                                borderColor: '#3b82f6',
                                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                                tension: 0.3,
                                fill: true,
                            },
                            {
                                label: 'Resolved',
                                data: data.map(d => d.resolved),
                                borderColor: '#22c55e',
                                backgroundColor: 'rgba(34, 197, 94, 0.1)',
                                tension: 0.3,
                                fill: true,
                            },
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
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
        }
    }
</script>
@endpush
