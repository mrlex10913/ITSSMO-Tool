<div class="container mx-auto max-w-6xl">
    <div class="mb-6">
        <h1 class="text-2xl font-semibold tracking-tight">Reports · Customer Satisfaction</h1>
        <div class="mt-1 flex flex-wrap items-center gap-3 text-sm text-gray-600 dark:text-gray-300">
            <div class="flex items-center gap-2">
                <label>Start</label>
                <input type="date" wire:model.live="startDate" class="text-sm border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 rounded px-2 py-1" />
            </div>
            <div class="flex items-center gap-2">
                <label>End</label>
                <input type="date" wire:model.live="endDate" class="text-sm border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 rounded px-2 py-1" />
            </div>
            <div class="flex items-center gap-2">
                <label>Department</label>
                <select wire:model.live="department" class="text-sm border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 rounded px-2 py-1">
                    <option value="">All</option>
                    @foreach($departments as $d)
                        <option value="{{ $d['slug'] }}">{{ $d['label'] }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-center gap-2">
                <label>Source</label>
                <select wire:model.live="source" class="text-sm border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 rounded px-2 py-1">
                    <option value="all">All</option>
                    <option value="guest">Guest (Ticket CSAT)</option>
                    <option value="end_user">End User (System CSAT)</option>
                </select>
            </div>
            <a href="{{ $exportCsvUrl }}" class="inline-flex items-center gap-1 text-blue-600 dark:text-blue-400 hover:underline">Export CSV</a>
            <span class="ml-auto text-xs text-gray-500 dark:text-gray-400">Range: {{ $stats['range'][0] }} → {{ $stats['range'][1] }}</span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="bg-white dark:bg-gray-800 shadow-sm ring-1 ring-gray-200 dark:ring-gray-700 rounded-lg p-5">
            <div class="text-sm text-gray-500 dark:text-gray-400">Average Rating</div>
            <div class="text-3xl font-semibold mt-1">{{ $stats['avg'] ?? '—' }} <span class="text-sm font-normal text-gray-500">/ 5</span></div>
        </div>
        <div class="bg-white dark:bg-gray-800 shadow-sm ring-1 ring-gray-200 dark:ring-gray-700 rounded-lg p-5">
            <div class="text-sm text-gray-500 dark:text-gray-400">Responses</div>
            <div class="text-3xl font-semibold mt-1">{{ $stats['count'] }}</div>
        </div>
        <div class="bg-white dark:bg-gray-800 shadow-sm ring-1 ring-gray-200 dark:ring-gray-700 rounded-lg p-5">
            <div class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Distribution</div>
            <div class="mt-2" id="csatDistribution" style="height: 100px"></div>
            <div class="mt-2 text-xs text-gray-500 dark:text-gray-400">Good: {{ $stats['distribution']['good'] }} · Neutral: {{ $stats['distribution']['neutral'] }} · Poor: {{ $stats['distribution']['poor'] }}</div>
        </div>
    </div>

    <div class="mt-6 grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white dark:bg-gray-800 shadow-sm ring-1 ring-gray-200 dark:ring-gray-700 rounded-lg p-5">
            <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-4">Ratings Over Time</h3>
            <canvas id="csatOverTime" height="120"></canvas>
        </div>
    <div class="bg-white dark:bg-gray-800 shadow-sm ring-1 ring-gray-200 dark:ring-gray-700 rounded-lg p-5">
            <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-4">Top Categories</h3>
            <ul class="text-sm space-y-2">
                @forelse($stats['perCategory'] as $row)
                    <li class="flex items-center justify-between">
                        <span>{{ $row->category ?? 'Uncategorized' }}</span>
                        <span class="text-gray-500 dark:text-gray-400">{{ number_format((float) ($row->avg_rating ?? 0), 2) }}/5 · {{ $row->responses ?? 0 }}</span>
                    </li>
                @empty
                    <li class="text-gray-500 dark:text-gray-400">No data</li>
                @endforelse
            </ul>
        </div>
    </div>

    <div class="mt-6 grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white dark:bg-gray-800 shadow-sm ring-1 ring-gray-200 dark:ring-gray-700 rounded-lg p-5">
            <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-4">Average by Department</h3>
            <canvas id="csatByDept" height="120"></canvas>
        </div>
        <div class="bg-white dark:bg-gray-800 shadow-sm ring-1 ring-gray-200 dark:ring-gray-700 rounded-lg p-5">
            <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-4">Legend</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400">Shows top departments by number of responses in the selected range. Bars are colored green by average rating.</p>
        </div>
    </div>

    @if($source !== 'end_user')
        <div class="mt-6 bg-white dark:bg-gray-800 shadow-sm ring-1 ring-gray-200 dark:ring-gray-700 rounded-lg p-5">
            <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-4">Per-Agent Breakdown</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="text-left text-gray-600 dark:text-gray-300">
                        <tr>
                            <th class="py-2 pr-4">Agent</th>
                            <th class="py-2 pr-4">Responses</th>
                            <th class="py-2">Avg Rating</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-800 dark:text-gray-100">
                        @forelse($stats['perAgent'] as $row)
                            <tr class="border-t border-gray-100 dark:border-gray-700">
                                <td class="py-2 pr-4">{{ $row->agent ?? 'Unassigned' }}</td>
                                <td class="py-2 pr-4">{{ $row->responses ?? 0 }}</td>
                                <td class="py-2">{{ number_format((float) ($row->avg_rating ?? 0), 2) }}/5</td>
                            </tr>
                        @empty
                            <tr>
                                <td class="py-3 text-gray-500 dark:text-gray-400" colspan="3">No data</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('livewire:init', () => {
            let chart;
            let dept;
            const ctx = document.getElementById('csatOverTime').getContext('2d');
            const dtx = document.getElementById('csatByDept')?.getContext('2d');

            const isDark = () => document.documentElement.classList.contains('dark');
            const theme = () => ({
                text: isDark() ? '#cbd5e1' : '#374151', // slate-300 vs gray-700
                grid: isDark() ? '#334155' : '#e5e7eb', // slate-700 vs gray-200
                border: isDark() ? '#475569' : '#d1d5db', // slate-600 vs gray-300
                bar: '#10b981',
                barBorder: '#059669',
                tooltipBg: isDark() ? '#111827' : '#ffffff', // gray-900 vs white
                tooltipFg: isDark() ? '#e5e7eb' : '#111827',
            });

            const render = (points) => {
                if (chart) { chart.destroy(); }
                const t = theme();
                chart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: points.map(p => p.date),
                        datasets: [
                            { label: 'Avg Rating', data: points.map(p => p.avg), backgroundColor: t.bar, borderColor: t.barBorder, borderWidth: 1 }
                        ]
                    },
                    options: {
                        plugins: {
                            legend: { labels: { color: t.text } },
                            tooltip: {
                                backgroundColor: t.tooltipBg,
                                titleColor: t.tooltipFg,
                                bodyColor: t.tooltipFg,
                                borderColor: t.border,
                                borderWidth: 1,
                            },
                        },
                        scales: {
                            y: {
                                suggestedMin: 1,
                                suggestedMax: 5,
                                beginAtZero: false,
                                ticks: { color: t.text },
                                grid: { color: t.grid, borderColor: t.border },
                            },
                            x: {
                                ticks: { maxRotation: 0, autoSkip: true, color: t.text },
                                grid: { color: t.grid, borderColor: t.border },
                            }
                        }
                    }
                });
            };

            const renderBar = (dist) => {
                const el = document.getElementById('csatDistribution');
                if (!el) { return; }
                el.innerHTML = '';
                const total = (dist.good||0) + (dist.neutral||0) + (dist.poor||0);
                const segments = [
                    { key: 'good', color: '#10b981' },
                    { key: 'neutral', color: '#f59e0b' },
                    { key: 'poor', color: '#ef4444' },
                ];
                const wrap = document.createElement('div');
                wrap.className = 'w-full h-6 flex rounded overflow-hidden ring-1 ring-gray-200 dark:ring-gray-700';
                segments.forEach(s => {
                    const val = dist[s.key] || 0;
                    const pct = total ? (val/total*100) : 0;
                    const seg = document.createElement('div');
                    seg.style.width = pct + '%';
                    seg.style.backgroundColor = s.color;
                    wrap.appendChild(seg);
                });
                el.appendChild(wrap);
            };

            render(@json($series));
            renderBar(@json($stats['distribution']));

            const renderDept = (rows) => {
                if (!dtx) { return; }
                if (dept) { dept.destroy(); }
                const t = theme();
                const labels = rows.map(r => r.label ?? r.label);
                const data = rows.map(r => Number.parseFloat(r.avg_rating ?? r.avg_rating));
                dept = new Chart(dtx, {
                    type: 'bar',
                    data: {
                        labels,
                        datasets: [{ label: 'Avg Rating', data, backgroundColor: t.bar, borderColor: t.barBorder, borderWidth: 1 }]
                    },
                    options: {
                        plugins: {
                            legend: { labels: { color: t.text } },
                            tooltip: {
                                backgroundColor: t.tooltipBg,
                                titleColor: t.tooltipFg,
                                bodyColor: t.tooltipFg,
                                borderColor: t.border,
                                borderWidth: 1,
                            },
                        },
                        scales: {
                            y: { suggestedMin: 1, suggestedMax: 5, ticks: { color: t.text }, grid: { color: t.grid, borderColor: t.border } },
                            x: { ticks: { color: t.text }, grid: { color: t.grid, borderColor: t.border } },
                        }
                    }
                });
            };
            renderDept(@json($stats['byDepartment']));

            Livewire.hook('message.processed', () => {
                render(@json($series));
                renderBar(@json($stats['distribution']));
                renderDept(@json($stats['byDepartment']));
            });

            // Re-render when theme toggles (html.dark class changes)
            const mo = new MutationObserver(() => {
                render(@json($series));
                renderBar(@json($stats['distribution']));
                renderDept(@json($stats['byDepartment']));
            });
            mo.observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });
        });
    </script>
</div>
