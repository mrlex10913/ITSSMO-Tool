<div class="p-6 max-w-7xl mx-auto">

    {{-- Header --}}
    <div class="flex flex-col gap-3 mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Helpdesk Reports</h1>
                <p class="text-sm text-gray-500">Analytics and performance metrics for helpdesk operations</p>
            </div>

            <div class="flex items-center gap-3 flex-wrap">
                {{-- Labels toggle --}}
                <div x-data="{ on: false }">
                    <button @click="on = !on; window.dispatchEvent(new CustomEvent('report-labels', { detail: on }))"
                        :class="on ? 'bg-blue-600 text-white' : 'bg-white text-gray-600 border border-gray-300 hover:bg-gray-50'"
                        class="px-3 py-1.5 text-sm font-medium rounded-md transition-colors flex items-center gap-1.5">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                        </svg>
                        <span x-text="on ? 'Labels: On' : 'Labels: Off'"></span>
                    </button>
                </div>

                {{-- Period switcher --}}
                <div class="flex items-center gap-1 bg-gray-100 p-1 rounded-lg">
                    @foreach(['daily' => 'Daily', 'weekly' => 'Weekly', 'monthly' => 'Monthly', 'yearly' => 'Yearly'] as $p => $label)
                        <button wire:click="setPeriod('{{ $p }}')"
                            class="px-4 py-1.5 text-sm font-medium rounded-md transition-colors
                                {{ (!$useCustomRange && $period === $p) ? 'bg-white text-gray-900 shadow' : 'text-gray-500 hover:text-gray-700' }}">
                            {{ $label }}
                        </button>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Date range row --}}
        <div class="flex items-center gap-2 flex-wrap">
            <span class="text-sm text-gray-500 font-medium">Date Range:</span>

            <input type="date" wire:model="dateFrom"
                class="border border-gray-300 rounded-md text-sm px-3 py-1.5 focus:ring-blue-500 focus:border-blue-500">

            <span class="text-gray-400 text-sm">to</span>

            <input type="date" wire:model="dateTo"
                class="border border-gray-300 rounded-md text-sm px-3 py-1.5 focus:ring-blue-500 focus:border-blue-500">

            <button wire:click="applyDateRange"
                class="px-4 py-1.5 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 transition-colors">
                Apply
            </button>

            @if($useCustomRange)
                <span class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded-full font-medium">
                    Custom: {{ \Carbon\Carbon::parse($dateFrom)->format('M j, Y') }} — {{ \Carbon\Carbon::parse($dateTo)->format('M j, Y') }}
                </span>
                <button wire:click="clearDateRange"
                    class="text-xs text-gray-500 hover:text-red-600 underline transition-colors">
                    Clear
                </button>
            @endif
        </div>
    </div>

    {{-- Summary Cards (6) --}}
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">

        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center justify-between">
                <span class="text-xs text-gray-500">Created</span>
                @if(($summary['created_change'] ?? 0) != 0)
                    <span class="text-xs px-1.5 py-0.5 rounded
                        {{ $summary['created_change'] > 0 ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }}">
                        {{ $summary['created_change'] > 0 ? '+' : '' }}{{ $summary['created_change'] }}%
                    </span>
                @endif
            </div>
            <div class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($summary['created'] ?? 0) }}</div>
        </div>

        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center justify-between">
                <span class="text-xs text-gray-500">Resolved</span>
                @if(($summary['resolved_change'] ?? 0) != 0)
                    <span class="text-xs px-1.5 py-0.5 rounded
                        {{ $summary['resolved_change'] > 0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                        {{ $summary['resolved_change'] > 0 ? '+' : '' }}{{ $summary['resolved_change'] }}%
                    </span>
                @endif
            </div>
            <div class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($summary['resolved'] ?? 0) }}</div>
        </div>

        <div class="bg-white rounded-lg shadow p-4">
            <span class="text-xs text-gray-500">Open Now</span>
            <div class="text-2xl font-bold text-blue-600 mt-1">{{ number_format($summary['open'] ?? 0) }}</div>
        </div>

        <div class="bg-white rounded-lg shadow p-4">
            <span class="text-xs text-gray-500">CSAT Score</span>
            <div class="text-2xl font-bold mt-1
                {{ ($summary['csat_score'] ?? 0) >= 80 ? 'text-green-600' : (($summary['csat_score'] ?? 0) >= 60 ? 'text-yellow-600' : 'text-red-600') }}">
                {{ $summary['csat_score'] !== null ? $summary['csat_score'] . '%' : '—' }}
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-4">
            <span class="text-xs text-gray-500">Avg Resolve</span>
            <div class="text-2xl font-bold text-gray-900 mt-1">
                {{ $summary['avg_resolution_hours'] !== null ? $summary['avg_resolution_hours'] . 'h' : '—' }}
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-4">
            <span class="text-xs text-gray-500">SLA Rate</span>
            <div class="text-2xl font-bold mt-1
                {{ ($summary['sla_rate'] ?? 100) >= 90 ? 'text-green-600' : (($summary['sla_rate'] ?? 100) >= 70 ? 'text-yellow-600' : 'text-red-600') }}">
                {{ $summary['sla_rate'] !== null ? $summary['sla_rate'] . '%' : '—' }}
            </div>
        </div>

    </div>

    {{-- Tabs --}}
    <div class="border-b border-gray-200 mb-6">
        <nav class="flex gap-4" aria-label="Tabs">
            @foreach(['overview' => 'Overview', 'agents' => 'Agent Performance', 'sla' => 'SLA Compliance'] as $tab => $label)
                <button wire:click="setTab('{{ $tab }}')"
                    class="py-2 px-1 border-b-2 text-sm font-medium
                        {{ $activeTab === $tab ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    {{ $label }}
                </button>
            @endforeach
        </nav>
    </div>

    {{-- ═══ OVERVIEW TAB ═══ --}}
    @if($activeTab === 'overview')

        {{-- Row 1: Volume line + Status doughnut --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">

            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-sm font-semibold text-gray-700 mb-4">Technical Assistance Volume</h3>
                <div class="h-64"
                     wire:key="volume-{{ $period }}"
                     x-data="volumeChart(@js($volumeTrends))"
                     x-init="init()">
                    <canvas x-ref="chart"></canvas>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-sm font-semibold text-gray-700 mb-4">Status Distribution</h3>
                <div class="h-64 flex items-center justify-center"
                     wire:key="status-{{ $period }}"
                     x-data="statusChart(@js($summary['by_status'] ?? []))"
                     x-init="init()">
                    @if(empty($summary['by_status']))
                        <p class="text-sm text-gray-400">No data for this period.</p>
                    @else
                        <canvas x-ref="chart"></canvas>
                    @endif
                </div>
            </div>

        </div>

        {{-- Row 2: Priority bar + Categories horizontal bar --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">

            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-sm font-semibold text-gray-700 mb-4">By Priority</h3>
                <div class="h-64"
                     wire:key="priority-{{ $period }}"
                     x-data="priorityChart(@js($summary['by_priority'] ?? []))"
                     x-init="init()">
                    <canvas x-ref="chart"></canvas>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-sm font-semibold text-gray-700 mb-4">Top Categories</h3>
                <div class="h-64"
                     wire:key="category-{{ $period }}"
                     x-data="categoryChart(@js($topCategories))"
                     x-init="init()">
                    @if($topCategories->isEmpty())
                        <p class="text-sm text-gray-400 pt-8 text-center">No categorized tickets in this period.</p>
                    @else
                        <canvas x-ref="chart"></canvas>
                    @endif
                </div>
            </div>

        </div>

        {{-- Row 3: Incident vs Request doughnut + Quick stats --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-sm font-semibold text-gray-700 mb-4">Incident vs Service Request</h3>
                <div class="h-64 flex items-center justify-center"
                     wire:key="type-{{ $period }}"
                     x-data="typeChart(@js($summary['by_type'] ?? []))"
                     x-init="init()">
                    @if(empty($summary['by_type']))
                        <p class="text-sm text-gray-400">No data for this period.</p>
                    @else
                        <canvas x-ref="chart"></canvas>
                    @endif
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-sm font-semibold text-gray-700 mb-4">Quick Stats</h3>
                <div class="space-y-3 mt-2">
                    @php
                        $resolutionRate = ($summary['created'] ?? 0) > 0
                            ? round(($summary['resolved'] / $summary['created']) * 100, 1)
                            : 0;
                    @endphp
                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                        <span class="text-sm text-gray-600">Avg First Response</span>
                        <span class="text-sm font-semibold text-gray-900">
                            {{ $summary['avg_frt_mins'] !== null ? $summary['avg_frt_mins'] . ' min' : '—' }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                        <span class="text-sm text-gray-600">Incidents</span>
                        <span class="text-sm font-semibold text-gray-900">{{ $summary['by_type']['incident'] ?? 0 }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                        <span class="text-sm text-gray-600">Service Requests</span>
                        <span class="text-sm font-semibold text-gray-900">{{ $summary['by_type']['request'] ?? 0 }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                        <span class="text-sm text-gray-600">Resolution Rate</span>
                        <span class="text-sm font-semibold
                            {{ $resolutionRate >= 80 ? 'text-green-600' : ($resolutionRate >= 50 ? 'text-yellow-600' : 'text-red-600') }}">
                            {{ $resolutionRate }}%
                        </span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                        <span class="text-sm text-gray-600">Critical Tickets</span>
                        <span class="text-sm font-semibold text-red-600">{{ $summary['by_priority']['critical'] ?? 0 }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2">
                        <span class="text-sm text-gray-600">Scheduled</span>
                        <span class="text-sm font-semibold text-purple-600">{{ $summary['by_status']['scheduled'] ?? 0 }}</span>
                    </div>
                </div>
            </div>

        </div>

    @endif

    {{-- ═══ AGENT PERFORMANCE TAB ═══ --}}
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
                                <td class="px-4 py-3 whitespace-nowrap font-medium text-gray-900">{{ $agent['name'] }}</td>
                                <td class="px-4 py-3 text-center text-sm text-gray-600">{{ $agent['assigned'] }}</td>
                                <td class="px-4 py-3 text-center text-sm text-gray-600">{{ $agent['resolved'] }}</td>
                                <td class="px-4 py-3 text-center">
                                    <span class="text-sm font-medium
                                        {{ $agent['resolution_rate'] >= 80 ? 'text-green-600' : ($agent['resolution_rate'] >= 50 ? 'text-yellow-600' : 'text-red-600') }}">
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
                                        <span class="px-2 py-1 rounded text-xs font-medium
                                            {{ $agent['sla_compliance'] >= 90 ? 'bg-green-100 text-green-800' : ($agent['sla_compliance'] >= 70 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                            {{ $agent['sla_compliance'] }}%
                                        </span>
                                    @else
                                        <span class="text-sm text-gray-400">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if($agent['csat_score'] !== null)
                                        <span class="text-sm font-medium
                                            {{ $agent['csat_score'] >= 80 ? 'text-green-600' : ($agent['csat_score'] >= 60 ? 'text-yellow-600' : 'text-red-600') }}">
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

    {{-- ═══ SLA COMPLIANCE TAB ═══ --}}
    @if($activeTab === 'sla')

        {{-- Summary cards --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow p-6 text-center">
                <div class="text-4xl font-bold
                    {{ ($slaCompliance['compliance_rate'] ?? 0) >= 90 ? 'text-green-600' : (($slaCompliance['compliance_rate'] ?? 0) >= 70 ? 'text-yellow-600' : 'text-red-600') }}">
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

        {{-- Charts --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-sm font-semibold text-gray-700 mb-4">SLA Compliance Trend</h3>
                <div class="h-64"
                     wire:key="sla-trend-{{ $period }}"
                     x-data="slaTrendChart(@js($slaCompliance['trend'] ?? []))"
                     x-init="init()">
                    <canvas x-ref="chart"></canvas>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-sm font-semibold text-gray-700 mb-4">SLA Compliance by Priority</h3>
                <div class="h-64"
                     wire:key="sla-priority-{{ $period }}"
                     x-data="slaPriorityChart(@js($slaCompliance['by_priority'] ?? []))"
                     x-init="init()">
                    <canvas x-ref="chart"></canvas>
                </div>
            </div>

        </div>

    @endif

</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
<script>
// Register datalabels plugin globally
Chart.register(ChartDataLabels);

// ─── Shared chart defaults ────────────────────────────────────────────────────
const chartDefaults = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 11 } } },
        datalabels: { display: false },
    },
};

// ─── Helper: listen for the global toggle event and update chart labels ───────
function watchLabels(component, overrides = {}) {
    const handler = (e) => {
        if (!component.chart) return;
        component.chart.options.plugins.datalabels.display = e.detail;
        Object.assign(component.chart.options.plugins.datalabels, overrides);
        component.chart.update('none');
    };
    window.addEventListener('report-labels', handler);
    // Clean up when Alpine destroys the component (e.g. period/tab change)
    component.$cleanup(() => window.removeEventListener('report-labels', handler));
}

// ─── Technical Assistance bar chart ──────────────────────────────────────────
function volumeChart(data) {
    return {
        chart: null,
        init() {
            this.chart = new Chart(this.$refs.chart.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: data.map(d => d.label),
                    datasets: [
                        {
                            label: 'Technical Assistance',
                            data: data.map(d => d.created),
                            backgroundColor: 'rgba(59,130,246,0.75)',
                            borderColor: '#3b82f6',
                            borderWidth: 1,
                            borderRadius: 5,
                        },
                    ],
                },
                options: {
                    ...chartDefaults,
                    layout: { padding: { top: 24 } },
                    plugins: {
                        legend: { display: false },
                        datalabels: {
                            display: true,
                            anchor: 'end',
                            align: 'end',
                            offset: 4,
                            font: { size: 11, weight: 'bold' },
                            color: '#1d4ed8',
                            formatter: v => v > 0 ? v : null,
                        },
                    },
                    scales: {
                        y: { beginAtZero: true, ticks: { stepSize: 1 } },
                    },
                },
            });
        },
    };
}

// ─── Status doughnut ─────────────────────────────────────────────────────────
function statusChart(data) {
    const colorMap = { open: '#3b82f6', in_progress: '#f59e0b', resolved: '#22c55e', closed: '#6b7280', scheduled: '#8b5cf6' };
    return {
        chart: null,
        init() {
            const keys   = Object.keys(data);
            const values = Object.values(data);
            if (!values.length) return;
            this.chart = new Chart(this.$refs.chart.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: keys.map(s => s.replace('_', ' ').replace(/\b\w/g, c => c.toUpperCase())),
                    datasets: [{ data: values, backgroundColor: keys.map(s => colorMap[s] ?? '#94a3b8'), borderWidth: 2, borderColor: '#fff' }],
                },
                options: {
                    ...chartDefaults,
                    cutout: '65%',
                    plugins: {
                        legend: { position: 'right', labels: { boxWidth: 12, font: { size: 11 } } },
                        datalabels: {
                            display: false,
                            color: '#fff',
                            font: { size: 11, weight: 'bold' },
                            formatter: (v, ctx) => {
                                const total = ctx.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                                return total > 0 && v > 0 ? Math.round(v / total * 100) + '%' : null;
                            },
                        },
                    },
                },
            });
            watchLabels(this, { color: '#fff', font: { size: 11, weight: 'bold' }, formatter: (v, ctx) => { const t = ctx.chart.data.datasets[0].data.reduce((a,b)=>a+b,0); return t>0&&v>0?Math.round(v/t*100)+'%':null; } });
        },
    };
}

// ─── Priority bar chart ───────────────────────────────────────────────────────
function priorityChart(data) {
    const order  = ['critical', 'high', 'medium', 'low'];
    const colors = { critical: '#ef4444', high: '#f97316', medium: '#3b82f6', low: '#6b7280' };
    return {
        chart: null,
        init() {
            this.chart = new Chart(this.$refs.chart.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: order.map(p => p.charAt(0).toUpperCase() + p.slice(1)),
                    datasets: [{ label: 'Tickets', data: order.map(p => data[p] ?? 0), backgroundColor: order.map(p => colors[p]), borderRadius: 4 }],
                },
                options: {
                    ...chartDefaults,
                    plugins: {
                        legend: { display: false },
                        datalabels: { display: false, anchor: 'end', align: 'top', font: { size: 11, weight: 'bold' }, color: '#374151', formatter: v => v > 0 ? v : null },
                    },
                    scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } },
                },
            });
            watchLabels(this, { anchor: 'end', align: 'top', font: { size: 11, weight: 'bold' }, color: '#374151', formatter: v => v > 0 ? v : null });
        },
    };
}

// ─── Top categories horizontal bar ───────────────────────────────────────────
function categoryChart(data) {
    const arr = Array.isArray(data) ? data : Object.values(data);
    return {
        chart: null,
        init() {
            if (!arr.length) return;
            this.chart = new Chart(this.$refs.chart.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: arr.map(d => d.name),
                    datasets: [{ label: 'Tickets', data: arr.map(d => d.count), backgroundColor: 'rgba(99,102,241,0.75)', borderColor: '#6366f1', borderWidth: 1, borderRadius: 3 }],
                },
                options: {
                    indexAxis: 'y',
                    ...chartDefaults,
                    plugins: {
                        legend: { display: false },
                        datalabels: { display: false, anchor: 'end', align: 'right', font: { size: 11, weight: 'bold' }, color: '#374151', formatter: v => v > 0 ? v : null },
                    },
                    scales: { x: { beginAtZero: true, ticks: { stepSize: 1 } } },
                },
            });
            watchLabels(this, { anchor: 'end', align: 'right', font: { size: 11, weight: 'bold' }, color: '#374151', formatter: v => v > 0 ? v : null });
        },
    };
}

// ─── Incident vs Request doughnut ────────────────────────────────────────────
function typeChart(data) {
    return {
        chart: null,
        init() {
            const incident = data['incident'] ?? 0;
            const request  = data['request']  ?? 0;
            if (!incident && !request) return;
            this.chart = new Chart(this.$refs.chart.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: ['Incident', 'Service Request'],
                    datasets: [{ data: [incident, request], backgroundColor: ['#ef4444', '#3b82f6'], borderWidth: 2, borderColor: '#fff' }],
                },
                options: {
                    ...chartDefaults,
                    cutout: '65%',
                    plugins: {
                        legend: { position: 'right', labels: { boxWidth: 12, font: { size: 11 } } },
                        datalabels: {
                            display: false,
                            color: '#fff',
                            font: { size: 11, weight: 'bold' },
                            formatter: (v, ctx) => {
                                const total = ctx.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                                return total > 0 && v > 0 ? Math.round(v / total * 100) + '%' : null;
                            },
                        },
                    },
                },
            });
            watchLabels(this, { color: '#fff', font: { size: 11, weight: 'bold' }, formatter: (v, ctx) => { const t = ctx.chart.data.datasets[0].data.reduce((a,b)=>a+b,0); return t>0&&v>0?Math.round(v/t*100)+'%':null; } });
        },
    };
}

// ─── SLA trend stacked bar ────────────────────────────────────────────────────
function slaTrendChart(data) {
    const arr = Array.isArray(data) ? data : Object.values(data);
    return {
        chart: null,
        init() {
            this.chart = new Chart(this.$refs.chart.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: arr.map(d => d.label),
                    datasets: [
                        { label: 'Compliant', data: arr.map(d => d.compliant), backgroundColor: 'rgba(34,197,94,0.75)', borderColor: '#22c55e', borderWidth: 1, borderRadius: 3, stack: 'sla' },
                        { label: 'Breached',  data: arr.map(d => d.breached),  backgroundColor: 'rgba(239,68,68,0.75)',  borderColor: '#ef4444', borderWidth: 1, borderRadius: 3, stack: 'sla' },
                    ],
                },
                options: {
                    ...chartDefaults,
                    plugins: {
                        ...chartDefaults.plugins,
                        datalabels: { display: false, color: '#fff', font: { size: 10, weight: 'bold' }, formatter: v => v > 0 ? v : null },
                    },
                    scales: { x: { stacked: true }, y: { stacked: true, beginAtZero: true, ticks: { stepSize: 1 } } },
                },
            });
            watchLabels(this, { color: '#fff', font: { size: 10, weight: 'bold' }, formatter: v => v > 0 ? v : null });
        },
    };
}

// ─── SLA by priority grouped bar ─────────────────────────────────────────────
function slaPriorityChart(data) {
    const arr = Array.isArray(data) ? data : Object.values(data);
    return {
        chart: null,
        init() {
            this.chart = new Chart(this.$refs.chart.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: arr.map(d => d.priority.charAt(0).toUpperCase() + d.priority.slice(1)),
                    datasets: [
                        { label: 'Compliant', data: arr.map(d => d.compliant), backgroundColor: 'rgba(34,197,94,0.75)', borderRadius: 3 },
                        { label: 'Breached',  data: arr.map(d => d.breached),  backgroundColor: 'rgba(239,68,68,0.75)', borderRadius: 3 },
                    ],
                },
                options: {
                    ...chartDefaults,
                    plugins: {
                        ...chartDefaults.plugins,
                        datalabels: { display: false, anchor: 'end', align: 'top', font: { size: 11, weight: 'bold' }, color: '#374151', formatter: v => v > 0 ? v : null },
                    },
                    scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } },
                },
            });
            watchLabels(this, { anchor: 'end', align: 'top', font: { size: 11, weight: 'bold' }, color: '#374151', formatter: v => v > 0 ? v : null });
        },
    };
}
</script>
@endpush
