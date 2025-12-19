<div class="space-y-4">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b border-gray-200">
        <div class="px-4 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-indigo-800 rounded-lg flex items-center justify-center">
                        <span class="text-white font-bold text-sm">SLA</span>
                    </div>
                    <h1 class="text-2xl font-bold text-gray-900">Service Level Agreement Insights</h1>
                </div>
                <div class="text-sm text-gray-600">
                    Last updated: <span class="font-medium">{{ now()->format('M d, Y H:i') }}</span>
                </div>
            </div>
        </div>
    </header>

    <!-- Filters / Actions -->
    <div class="px-4">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3">
                <div class="flex flex-wrap items-center gap-2">
                    <label class="text-sm text-gray-600">Window</label>
                    <input type="number" min="7" max="180" wire:model.live="window" class="w-24 rounded border-gray-300" />
                    <select wire:model.live="type" class="rounded border-gray-300 text-sm">
                        <option value="">All Types</option>
                        <option value="incident">Incident</option>
                        <option value="request">Request</option>
                    </select>
                    <select wire:model.live="priority" class="rounded border-gray-300 text-sm">
                        <option value="">All Priorities</option>
                        <option value="low">Low</option>
                        <option value="medium">Medium</option>
                        <option value="high">High</option>
                        <option value="critical">Critical</option>
                    </select>
                    <select wire:model.live="assigneeId" class="rounded border-gray-300 text-sm">
                        <option value="">All Assignees</option>
                        @foreach(($agents ?? collect()) as $a)
                            <option value="{{ $a->id }}">{{ $a->name }}</option>
                        @endforeach
                    </select>
                    <select wire:model.live="requesterId" class="rounded border-gray-300 text-sm">
                        <option value="">All Requesters</option>
                        @foreach(($requesters ?? collect()) as $r)
                            <option value="{{ $r->id }}">{{ $r->name }}</option>
                        @endforeach
                    </select>
                    <select wire:model.live="categoryId" class="rounded border-gray-300 text-sm">
                        <option value="">All Categories</option>
                        @foreach(($categories ?? collect()) as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endforeach
                    </select>
                    <select wire:model.live="aggregate" class="rounded border-gray-300 text-sm">
                        <option value="daily">Daily</option>
                        <option value="weekly">Weekly</option>
                    </select>
                </div>
                <div class="flex items-center gap-2">
                    <button wire:click="refreshData" class="px-3 py-1.5 rounded bg-indigo-600 text-white text-sm">Apply</button>
                    <button wire:click="exportCsv" class="px-3 py-1.5 rounded bg-emerald-600 text-white text-sm">Export CSV</button>
                    <button wire:click="exportDetailedCsv" class="px-3 py-1.5 rounded bg-blue-600 text-white text-sm">Export Detailed CSV</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Key Metrics Overview -->
    <div class="px-4">
        <h2 class="text-xl font-semibold text-gray-900 mb-3">Performance Overview</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-sm font-medium text-gray-600">Avg First Response</h3>
                    <div class="w-3 h-3 bg-indigo-700 rounded-full"></div>
                </div>
                <div class="text-3xl font-bold text-indigo-900">{{ $data['response_avg_mins'] !== null ? $data['response_avg_mins'].' min' : '—' }}</div>
                <p class="text-sm text-gray-500">in last {{ $data['window'] }}</p>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-sm font-medium text-gray-600">Avg Resolve Time</h3>
                    <div class="w-3 h-3 bg-emerald-500 rounded-full"></div>
                </div>
                <div class="text-3xl font-bold text-indigo-900">{{ $data['resolve_avg_mins'] !== null ? $data['resolve_avg_mins'].' min' : '—' }}</div>
                <p class="text-sm text-gray-500">in last {{ $data['window'] }}</p>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-sm font-medium text-gray-600">Breach Rate</h3>
                    <div class="w-3 h-3 bg-red-500 rounded-full"></div>
                </div>
                <div class="text-3xl font-bold text-indigo-900">{{ $data['breach_rate'] }}%</div>
                <p class="text-sm text-gray-500">resolved tickets</p>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <div class="text-sm font-medium text-gray-600">Responded</div>
                <div class="text-3xl font-bold text-indigo-900">{{ $data['responded_count'] }}</div>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <div class="text-sm font-medium text-gray-600">Resolved</div>
                <div class="text-3xl font-bold text-indigo-900">{{ $data['resolved_count'] }}</div>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <div class="text-sm font-medium text-gray-600">Due Today / Breached Open</div>
                <div class="text-3xl font-bold text-indigo-900">{{ $data['due_today'] }} / {{ $data['breached_open'] }}</div>
            </div>
        </div>
    </div>

    @php
        $days = $data['series']['days'] ?? [];
        $breach = $data['series']['breach_rate'] ?? [];
        $resp = $data['series']['avg_response_mins'] ?? [];
        $res = $data['series']['avg_resolve_mins'] ?? [];
        $maxResp = max(array_filter($resp, fn($v)=>$v!==null) ?: [0]);
        $maxRes = max(array_filter($res, fn($v)=>$v!==null) ?: [0]);
        $maxY = max(10, (int) ceil(max($maxResp, $maxRes) / 10) * 10);
        $w = 700; $h = 160; $pad = 28;
        $n = max(1, count($days));
        $sx = ($w - 2*$pad) / max(1, ($n-1));
        $sy = ($h - 2*$pad) / max(1, $maxY);
    $labelStep = ($aggregate ?? 'daily') === 'weekly' ? 1 : max(1, (int) ceil($n / 7)); // weekly: show all weeks
        $line = function($vals, $color) use ($n,$sx,$sy,$pad,$h){
            $d = '';
            for ($i=0; $i<$n; $i++) { $v = $vals[$i] ?? null; if ($v===null){ continue; } $x=$pad+$i*$sx; $y=$h-$pad-($v*$sy); $d .= ($d===''? 'M':' L').$x.','.$y; }
            return ['d'=>$d,'color'=>$color];
        };
        $respLine = $line($resp, '#2563eb');
        $resLine = $line($res, '#16a34a');
        $breachLine = $line($breach, '#dc2626');
    @endphp

    <div class="px-4">
        <div class="rounded-lg shadow-sm border border-gray-200 bg-white p-4">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-lg font-medium text-gray-900">Response Time & Breach Analysis ({{ ($aggregate ?? 'daily') === 'weekly' ? 'Weekly' : 'Daily' }})</h3>
                <div class="flex items-center space-x-4 text-sm text-gray-600">
                    <div class="flex items-center space-x-2">
                        <div class="w-3 h-3 bg-blue-700 rounded-full"></div>
                        <span>Avg Response (mins)</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <div class="w-3 h-3 bg-emerald-500 rounded-full"></div>
                        <span>Avg Resolve (mins)</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <div class="w-3 h-3 bg-amber-400 rounded-full"></div>
                        <span>Daily Breach Rate (%)</span>
                    </div>
                </div>
            </div>
            <div wire:ignore>
                <canvas id="slaChart" height="320"></canvas>
            </div>
        </div>
    </div>

    @once
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            window._slaCharts = window._slaCharts || {};
            function smooth(arr, decimals = 1) {
                if (!Array.isArray(arr)) return arr;
                const out = [];
                for (let i = 0; i < arr.length; i++) {
                    const cur = arr[i];
                    if (cur === null || cur === undefined) { out.push(null); continue; }
                    let sum = 0, c = 0;
                    for (let j = -1; j <= 1; j++) {
                        const k = i + j;
                        if (k < 0 || k >= arr.length) continue;
                        const v = arr[k];
                        if (v === null || v === undefined) continue;
                        sum += v; c++;
                    }
                    out.push(c ? + (sum / c).toFixed(decimals) : null);
                }
                return out;
            }
            function renderSlaChart(elId, payload) {
                const el = document.getElementById(elId);
                if (!el || !window.Chart) return;
                const agg = payload.aggregate || 'daily';
                const labels = payload.labels || [];
                const resp = (agg === 'weekly') ? smooth(payload.responseMins, 1) : payload.responseMins;
                const reso = (agg === 'weekly') ? smooth(payload.resolveMins, 1) : payload.resolveMins;
                const breach = (agg === 'weekly') ? smooth(payload.breachRate, 0) : payload.breachRate;

                const existing = window._slaCharts[elId];
                if (existing) {
                    existing.data.labels = labels;
                    existing.data.datasets[0].data = resp;
                    existing.data.datasets[1].data = reso;
                    existing.data.datasets[2].data = breach;
                    existing.update();
                    return existing;
                }

                const ctx = el.getContext('2d');
                const chart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels,
                        datasets: [
                            {
                                label: 'Average Response (mins)',
                                data: resp,
                                borderColor: '#1e40af',
                                backgroundColor: 'rgba(30, 64, 175, 0.10)',
                                borderWidth: 3,
                                fill: true,
                                tension: 0.35,
                                pointBackgroundColor: '#1e40af',
                                pointBorderColor: '#ffffff',
                                pointBorderWidth: 2,
                                pointRadius: 3,
                                pointHoverRadius: 5,
                                yAxisID: 'y'
                            },
                            {
                                label: 'Average Resolve (mins)',
                                data: reso,
                                borderColor: '#16a34a',
                                backgroundColor: 'rgba(22, 163, 74, 0.08)',
                                borderWidth: 3,
                                fill: true,
                                tension: 0.35,
                                pointBackgroundColor: '#16a34a',
                                pointBorderColor: '#ffffff',
                                pointBorderWidth: 2,
                                pointRadius: 3,
                                pointHoverRadius: 5,
                                yAxisID: 'y'
                            },
                            {
                                label: 'Daily Breach Rate (%)',
                                data: breach,
                                borderColor: '#fbbf24',
                                backgroundColor: 'rgba(251, 191, 36, 0.1)',
                                borderWidth: 3,
                                fill: false,
                                tension: 0.35,
                                pointBackgroundColor: '#fbbf24',
                                pointBorderColor: '#ffffff',
                                pointBorderWidth: 2,
                                pointRadius: 3,
                                pointHoverRadius: 5,
                                yAxisID: 'y1'
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: { intersect: false, mode: 'index' },
                        plugins: {
                            legend: { display: true },
                            tooltip: {
                                backgroundColor: 'rgba(255, 255, 255, 0.95)',
                                titleColor: '#374151',
                                bodyColor: '#374151',
                                borderColor: '#e5e7eb',
                                borderWidth: 1,
                                cornerRadius: 8,
                                displayColors: true,
                                callbacks: {
                                    label: function(context) {
                                        const y = context.parsed.y;
                                        if (context.dataset.yAxisID === 'y') {
                                            return `${context.dataset.label}: ${y ?? '—'} minutes`;
                                        }
                                        return `${context.dataset.label}: ${y ?? '—'}%`;
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                grid: { display: false },
                                ticks: { color: '#6b7280', font: { size: 12 } }
                            },
                            y: {
                                type: 'linear',
                                display: true,
                                position: 'left',
                                beginAtZero: true,
                                grid: { color: '#f3f4f6', drawBorder: false },
                                ticks: {
                                    color: '#1e40af',
                                    font: { size: 12, weight: 'bold' },
                                    callback: (v) => v + ' min'
                                },
                                title: {
                                    display: true,
                                    text: 'Response/Resolve Time (minutes)',
                                    color: '#1e40af',
                                    font: { size: 13, weight: 'bold' }
                                }
                            },
                            y1: {
                                type: 'linear',
                                display: true,
                                position: 'right',
                                beginAtZero: true,
                                grid: { drawOnChartArea: false },
                                ticks: {
                                    color: '#f59e0b',
                                    font: { size: 12, weight: 'bold' },
                                    callback: (v) => v + '%'
                                },
                                title: {
                                    display: true,
                                    text: 'Daily Breach Rate (%)',
                                    color: '#f59e0b',
                                    font: { size: 13, weight: 'bold' }
                                },
                                suggestedMax: 100
                            }
                        }
                    }
                });
                window._slaCharts[elId] = chart;
                return chart;
            }
        </script>
    @endonce

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            renderSlaChart('slaChart', {
                aggregate: @js($aggregate ?? 'daily'),
                labels: @js($days),
                responseMins: @js($resp),
                resolveMins: @js($res),
                breachRate: @js($breach)
            });
        });
        document.addEventListener('livewire:navigated', function () {
            renderSlaChart('slaChart', {
                aggregate: @js($aggregate ?? 'daily'),
                labels: @js($days),
                responseMins: @js($resp),
                resolveMins: @js($res),
                breachRate: @js($breach)
            });
        });
    </script>

    <div class="px-4">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-4 py-3 border-b border-gray-200 bg-gray-50">
                <h3 class="text-sm font-medium text-gray-900">Service Category Performance</h3>
            </div>
            <div class="overflow-auto p-2">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="text-left text-gray-600">
                            <th class="py-2 pr-4">Category</th>
                            <th class="py-2 pr-4">On-Time</th>
                            <th class="py-2 pr-4">Breached</th>
                            <th class="py-2 pr-4">On-Time %</th>
                            <th class="py-2 pr-4">Avg Response</th>
                            <th class="py-2 pr-4">Avg Resolve</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data['categories'] as $row)
                            <tr class="border-t hover:bg-gray-50">
                                <td class="py-2 pr-4 font-medium text-gray-900">{{ $row['name'] }}</td>
                                <td class="py-2 pr-4">{{ $row['on_time'] }}</td>
                                <td class="py-2 pr-4">{{ $row['breached'] }}</td>
                                <td class="py-2 pr-4">{{ $row['on_time_rate'] }}%</td>
                                <td class="py-2 pr-4">{{ $row['response_avg_mins'] !== null ? $row['response_avg_mins'].' min' : '—' }}</td>
                                <td class="py-2 pr-4">{{ $row['resolve_avg_mins'] !== null ? $row['resolve_avg_mins'].' min' : '—' }}</td>
                            </tr>
                        @empty
                            <tr><td class="py-4 text-center text-gray-500" colspan="6">No data in window.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
