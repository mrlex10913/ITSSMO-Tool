<?php

namespace App\Livewire\MasterFiles;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\MasterFiles\MasterFile;
use App\Models\MasterFiles\MasterFileCategory;
use App\Models\MasterFiles\MasterFileAccessLog;

class Dashboard extends Component
{

    public $userDepartment;
    public $selectedPeriod = '30';

    public function mount()
    {
        $this->userDepartment = Auth::user()->department ?? 'ITSS';
    }
    public function render()
    {
        $stats = $this->getStats();
        $recentFiles = $this->getRecentFiles();
        $popularFiles = $this->getPopularFiles();
        $expiringFiles = $this->getExpiringFiles();
        $activityData = $this->getActivityData();


        return view('livewire.master-files.dashboard',  compact(
            'stats', 'recentFiles', 'popularFiles', 'expiringFiles', 'activityData'
        ));
    }

    private function getStats()
    {
        $baseQuery = MasterFile::when(!Auth::user()->isDeveloper(), function($query) {
            $query->where(function($q) {
                $q->where('department', $this->userDepartment)
                  ->orWhereJsonContains('visible_to_departments', $this->userDepartment);
            });
        });

        return [
            'total_files' => (clone $baseQuery)->count(),
            'active_files' => (clone $baseQuery)->where('status', 'active')->count(),
            'categories' => MasterFileCategory::where('is_active', true)->count(),
            'total_downloads' => (clone $baseQuery)->sum('download_count'),
            'pending_approval' => (clone $baseQuery)->where('status', 'pending_approval')->count(),
            'expiring_soon' => (clone $baseQuery)->where('expiry_date', '<=', now()->addDays(30))
                                                 ->where('expiry_date', '>', now())
                                                 ->count(),
        ];
    }

    private function getRecentFiles()
    {
        return MasterFile::with(['category', 'uploader'])
            ->when(!Auth::user()->isDeveloper(), function($query) {
                $query->where(function($q) {
                    $q->where('department', $this->userDepartment)
                    ->orWhereJsonContains('visible_to_departments', $this->userDepartment);
                });
            })
            ->where('status', 'active') // Only show active versions
            ->latest()
            ->take(5)
            ->get();
    }

    private function getPopularFiles()
    {
        return MasterFile::with(['category'])
            ->when(!Auth::user()->isDeveloper(), function($query) {
                $query->where(function($q) {
                    $q->where('department', $this->userDepartment)
                      ->orWhereJsonContains('visible_to_departments', $this->userDepartment);
                });
            })
            ->orderBy('download_count', 'desc')
            ->take(5)
            ->get();
    }

    private function getExpiringFiles()
    {
        return MasterFile::with('category')
            ->when(!Auth::user()->isDeveloper(), function($query) {
                $query->where(function($q) {
                    $q->where('department', $this->userDepartment)
                      ->orWhereJsonContains('visible_to_departments', $this->userDepartment);
                });
            })
            ->where('expiry_date', '<=', now()->addDays(30))
            ->where('expiry_date', '>', now())
            ->orderBy('expiry_date')
            ->get();
    }

    private function getActivityData()
    {
        $days = (int) $this->selectedPeriod;
        $startDate = now()->subDays($days);

        // SQL Server compatible date extraction
        return MasterFileAccessLog::selectRaw('CAST(created_at AS DATE) as date, COUNT(*) as count')
            ->where('created_at', '>=', $startDate)
            ->when(!Auth::user()->isDeveloper(), function($query) {
                $query->where('department', $this->userDepartment);
            })
            ->groupBy(DB::raw('CAST(created_at AS DATE)'))
            ->orderBy('date')
            ->get()
            ->mapWithKeys(function($item) {
                return [$item->date => $item->count];
            });
    }
}
