<?php

namespace App\Livewire\PAMO;

use App\Models\PAMO\PamoAssetMovement;
use App\Models\PAMO\PamoAssets;
use App\Models\PAMO\PamoCategory;
use App\Models\PAMO\MasterList;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Component;


#[Layout('layouts.pamo')]
class Dashboard extends Component
{
    // Date Range Filter
    public $timePeriod = 'last_30_days';
    public $startDate;
    public $endDate;


    // Dashboard Data
    // public $assetCounts;
    // public $categoryDistribution;
    // public $statusDistribution;
    // public $departmentDistribution;
    public $assetCounts = [
        'total' => 0,
        'assigned' => 0,
        'assigned_percent' => 0,
        'new' => 0,
        'new_growth' => 0,
        'maintenance' => 0,
        'maintenance_percent' => 0,
    ];
    public $categoryDistribution = [
        'labels' => [],
        'data' => [],
        'counts' => [],
        'categories' => []
    ];
    public $statusDistribution = [
        'labels' => [],
        'data' => [],
        'counts' => [],
        'statuses' => []
    ];
    public $departmentDistribution = [
        'labels' => [],
        'data' => [],
        'departments' => []
    ];
    public $recentMovements = [];
    public $acquisitionTrend = [
        'labels' => [],
        'data' => [],
        'months' => []
    ];
    public $transferStats = [
        'last30Days' => 0,
        'monthlyAverage' => 0,
        'percentOfAverage' => 0,
        'byCategory' => [],
        'weekly' => [
            'labels' => [],
            'data' => []
        ]
    ];
    public $maintenanceStats = [
        'total' => 0,
        'monthly' => [
            'labels' => [],
            'data' => []
        ]
    ];
    public $valueData = [
        'totalValue' => 0,
        'currentValue' => 0,
        'depreciation' => 0,
        'depreciationRate' => 0,
        'yearly' => [
            'labels' => [],
            'purchaseValues' => [],
            'bookValues' => [],
            'depreciations' => []
        ]
    ];


    public function mount()
    {

       // Check for developer role access
         $role = strtolower(auth()->user()->role);
        if (!in_array($role, ['pamo', 'administrator', 'developer'])) {
            abort(404);
        }
        $this->setDateRange('last_30_days');
        $this->loadDashboardData();

        // Dispatch after everything is loaded
        $this->dispatch('dashboardDataUpdated');
    }

    public function setDateRange($period)
    {
        $this->timePeriod = $period;

        switch ($period) {
            case 'last_30_days':
                $this->startDate = Carbon::now()->subDays(30)->format('Y-m-d');
                $this->endDate = Carbon::now()->format('Y-m-d');
                break;
            case 'last_quarter':
                $this->startDate = Carbon::now()->subMonths(3)->format('Y-m-d');
                $this->endDate = Carbon::now()->format('Y-m-d');
                break;
            case 'last_6_months':
                $this->startDate = Carbon::now()->subMonths(6)->format('Y-m-d');
                $this->endDate = Carbon::now()->format('Y-m-d');
                break;
            case 'year_to_date':
                $this->startDate = Carbon::now()->startOfYear()->format('Y-m-d');
                $this->endDate = Carbon::now()->format('Y-m-d');
                break;
            case 'last_year':
                $this->startDate = Carbon::now()->subYear()->format('Y-m-d');
                $this->endDate = Carbon::now()->format('Y-m-d');
                break;
        }

        $this->loadDashboardData();
    }

    public function refreshData()
    {
        $this->loadDashboardData();
        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Dashboard data refreshed'
        ]);

        // Explicitly dispatch chart update event
        $this->dispatch('dashboardDataUpdated');
    }

    public function loadDashboardData()
    {
        try {
            $this->loadAssetCounts();
            $this->loadStatusDistribution();
            $this->loadCategoryDistribution();
            $this->loadDepartmentDistribution();
            $this->loadRecentMovements();
            $this->loadAcquisitionTrend();
            $this->loadTransferStats();
            $this->loadMaintenanceStats();
            $this->loadValueData();

            // Ensure data is dispatched properly
            $this->dispatch('dashboardDataUpdated');

            return true;
        } catch (\Exception $e) {
            Log::error('Error loading dashboard data: ' . $e->getMessage());
            return false;
        }
    }

    private function loadAssetCounts()
    {
        $totalAssets = PamoAssets::count();
        $assignedAssets = PamoAssets::whereNotNull('assigned_to')->count();
        $newAssets = PamoAssets::whereBetween('purchase_date', [$this->startDate, $this->endDate])->count();
        $maintenanceAssets = PamoAssets::where('status', 'maintenance')->count();

        // Calculate growth rates (month-over-month)
        $lastMonth = Carbon::now()->subMonth();
        $newLastMonth = PamoAssets::whereMonth('purchase_date', $lastMonth->month)
            ->whereYear('purchase_date', $lastMonth->year)
            ->count();

        $newGrowth = $newLastMonth > 0
            ? round((($newAssets - $newLastMonth) / $newLastMonth) * 100, 1)
            : 0;

        $this->assetCounts = [
            'total' => $totalAssets,
            'assigned' => $assignedAssets,
            'assigned_percent' => $totalAssets > 0 ? round(($assignedAssets / $totalAssets) * 100, 1) : 0,
            'new' => $newAssets,
            'new_growth' => $newGrowth,
            'maintenance' => $maintenanceAssets,
            'maintenance_percent' => $totalAssets > 0 ? round(($maintenanceAssets / $totalAssets) * 100, 1) : 0,
        ];
    }
    private function loadStatusDistribution()
    {
        $statuses = PamoAssets::select('status')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('status')
            ->get();

        $totalAssets = PamoAssets::count() ?: 1; // Avoid division by zero

        $formattedStatuses = $statuses->map(function($item) use ($totalAssets) {
            return [
                'name' => ucfirst($item->status),
                'count' => $item->count,
                'percentage' => round(($item->count / $totalAssets) * 100, 1)
            ];
        });

        $this->statusDistribution = [
            'labels' => $formattedStatuses->pluck('name')->toArray(),
            'data' => $formattedStatuses->pluck('percentage')->toArray(),
            'counts' => $formattedStatuses->pluck('count')->toArray(),
            'statuses' => $formattedStatuses->toArray()
        ];
    }

    private function loadCategoryDistribution()
    {
        $categories = PamoAssets::join('pamo_categories', 'pamo_assets.category_id', '=', 'pamo_categories.id')
            ->select('pamo_categories.name')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('pamo_categories.name')
            ->orderByDesc('count')
            ->get();

        $totalAssets = PamoAssets::count() ?: 1; // Avoid division by zero

        $formattedCategories = $categories->map(function($item) use ($totalAssets) {
            return [
                'name' => $item->name,
                'count' => $item->count,
                'percentage' => round(($item->count / $totalAssets) * 100, 1)
            ];
        });

        $this->categoryDistribution = [
            'labels' => $formattedCategories->pluck('name')->toArray(),
            'data' => $formattedCategories->pluck('percentage')->toArray(),
            'counts' => $formattedCategories->pluck('count')->toArray(),
            'categories' => $formattedCategories->toArray()
        ];
    }

    private function loadDepartmentDistribution()
    {
        $departments = PamoAssets::join('master_lists', 'pamo_assets.assigned_to', '=', 'master_lists.id')
            ->whereNotNull('master_lists.department')
            ->select('master_lists.department')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('master_lists.department')
            ->orderByDesc('count')
            ->get();

        $this->departmentDistribution = [
            'labels' => $departments->pluck('department')->toArray(),
            'data' => $departments->pluck('count')->toArray(),
            'departments' => $departments->toArray()
        ];
    }

    private function loadRecentMovements()
    {
        $this->recentMovements = PamoAssetMovement::with(['asset', 'fromLocation', 'toLocation', 'assignedBy', 'assignedEmployee'])
            ->orderByDesc('movement_date')
            ->take(4)
            ->get();
    }

    private function loadAcquisitionTrend()
    {
        $months = collect(range(0, 11))->map(function($month) {
            $date = Carbon::now()->subMonths($month);
            return [
                'month' => $date->format('M'),
                'month_year' => $date->format('M Y'),
                'year' => $date->year,
                'month_num' => $date->month,
            ];
        })->reverse()->values();

        $acquisitionData = [];

        foreach ($months as $month) {
            $count = PamoAssets::whereMonth('purchase_date', $month['month_num'])
                ->whereYear('purchase_date', $month['year'])
                ->count();

            $acquisitionData[] = [
                'month' => $month['month'],
                'count' => $count,
                'month_year' => $month['month_year']
            ];
        }

        $this->acquisitionTrend = [
            'labels' => collect($acquisitionData)->pluck('month')->toArray(),
            'data' => collect($acquisitionData)->pluck('count')->toArray(),
            'months' => $acquisitionData
        ];
    }

    private function loadTransferStats()
    {
        // Get transfer count for last 30 days
        $last30DaysTransfers = PamoAssetMovement::where('movement_type', 'transfer')
            ->whereBetween('movement_date', [Carbon::now()->subDays(30), Carbon::now()])
            ->count();

        // Get monthly average for the last year
        $yearlyTransfers = PamoAssetMovement::where('movement_type', 'transfer')
            ->whereBetween('movement_date', [Carbon::now()->subYear(), Carbon::now()])
            ->count();
        $monthlyAverage = $yearlyTransfers / 12;

        // Get transfers by category
        $transfersByCategory = PamoAssetMovement::join('pamo_assets', 'asset_movements.asset_id', '=', 'pamo_assets.id')
            ->join('pamo_categories', 'pamo_assets.category_id', '=', 'pamo_categories.id')
            ->where('asset_movements.movement_type', 'transfer')
            ->whereBetween('asset_movements.movement_date', [Carbon::now()->subDays(90), Carbon::now()])
            ->select('pamo_categories.name')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('pamo_categories.name')
            ->orderByDesc('count')
            ->take(5)
            ->get();

        $totalTransfers = $transfersByCategory->sum('count') ?: 1;
        $transfersByCategory = $transfersByCategory->map(function($item) use ($totalTransfers) {
            return [
                'name' => $item->name,
                'count' => $item->count,
                'percentage' => round(($item->count / $totalTransfers) * 100, 1)
            ];
        });

        // Get weekly transfers for last 4 weeks
        $weekly = collect(range(0, 3))->map(function($week) {
            $startDate = Carbon::now()->subWeeks($week)->startOfWeek();
            $endDate = Carbon::now()->subWeeks($week)->endOfWeek();

            $count = PamoAssetMovement::where('movement_type', 'transfer')
                ->whereBetween('movement_date', [$startDate, $endDate])
                ->count();

            return [
                'week' => 'Week ' . (4 - $week),
                'count' => $count
            ];
        })->reverse()->values();

        $this->transferStats = [
            'last30Days' => $last30DaysTransfers,
            'monthlyAverage' => $monthlyAverage,
            'percentOfAverage' => $monthlyAverage > 0 ? round(($last30DaysTransfers / $monthlyAverage) * 100) : 0,
            'byCategory' => $transfersByCategory->toArray(),
            'weekly' => [
                'labels' => $weekly->pluck('week')->toArray(),
                'data' => $weekly->pluck('count')->toArray()
            ]
        ];
    }

    private function loadMaintenanceStats()
    {
        // Get total maintenance events in the selected period
        $totalMaintenance = PamoAssetMovement::where('movement_type', 'maintenance')
            ->whereBetween('movement_date', [$this->startDate, $this->endDate])
            ->count();

        // Get monthly maintenance data
        $months = collect(range(0, 5))->map(function($month) {
            $date = Carbon::now()->subMonths($month);

            $count = PamoAssetMovement::where('movement_type', 'maintenance')
                ->whereMonth('movement_date', $date->month)
                ->whereYear('movement_date', $date->year)
                ->count();

            return [
                'month' => $date->format('M'),
                'count' => $count
            ];
        })->reverse()->values();

        $this->maintenanceStats = [
            'total' => $totalMaintenance,
            'monthly' => [
                'labels' => $months->pluck('month')->toArray(),
                'data' => $months->pluck('count')->toArray()
            ]
        ];
    }

    private function loadValueData()
    {
        // Calculate total purchase value of all assets
        $totalValue = PamoAssets::sum('purchase_value');

        // Calculate current book value using getCurrentValue() method
        $assets = PamoAssets::all();
        $currentValue = $assets->sum(function($asset) {
            return $asset->getCurrentValue();
        });

        // Calculate total depreciation
        $totalDepreciation = $totalValue - $currentValue;

        // Get yearly value data
        $years = collect(range(-2, 3))->map(function($yearOffset) {
            $year = Carbon::now()->addYears($yearOffset)->year;

            // Assets that existed in that year (purchased before or during that year)
            $yearAssets = PamoAssets::whereYear('purchase_date', '<=', $year)->get();

            $purchaseValue = $yearAssets->sum('purchase_value');
            $bookValue = $yearAssets->sum(function($asset) use ($year) {
                $assetYear = Carbon::parse($asset->purchase_date)->year;
                $age = max(0, $year - $assetYear);
                $depRate = 0.2; // 20% per year
                $maxDep = 0.8; // Max 80% depreciation
                $dep = min($age * $depRate, $maxDep);
                return $asset->purchase_value * (1 - $dep);
            });

            return [
                'year' => $year,
                'purchaseValue' => $purchaseValue,
                'bookValue' => $bookValue,
                'depreciation' => $purchaseValue - $bookValue
            ];
        });

        $this->valueData = [
            'totalValue' => $totalValue,
            'currentValue' => $currentValue,
            'depreciation' => $totalDepreciation,
            'depreciationRate' => $totalValue > 0 ? round(($totalDepreciation / $totalValue) * 100, 1) : 0,
            'yearly' => [
                'labels' => $years->pluck('year')->toArray(),
                'purchaseValues' => $years->pluck('purchaseValue')->toArray(),
                'bookValues' => $years->pluck('bookValue')->toArray(),
                'depreciations' => $years->pluck('depreciation')->toArray()
            ]
        ];
    }


    public function render()
    {
        return view('livewire.p-a-m-o.dashboard');
    }
}
