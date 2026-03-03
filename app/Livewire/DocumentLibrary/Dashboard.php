<?php

namespace App\Livewire\DocumentLibrary;

use App\Models\DocumentLibrary\DocumentFolder;
use App\Models\DocumentLibrary\MasterFile;
use App\Models\DocumentLibrary\MasterFileAccessLog;
use App\Models\DocumentLibrary\MasterFileCategory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.enduser')]
class Dashboard extends Component
{
    public $userDepartment;

    public $userEmail;

    public $selectedPeriod = '30';

    public function mount()
    {
        $this->userDepartment = Auth::user()->department ?? 'ITSS';
        $this->userEmail = Auth::user()->email;
    }

    public function render()
    {
        $stats = $this->getStats();
        $recentFiles = $this->getRecentFiles();
        $popularFiles = $this->getPopularFiles();
        $activityData = $this->getActivityData();
        $folders = $this->getFolders();

        return view('livewire.document-library.dashboard', compact(
            'stats', 'recentFiles', 'popularFiles', 'activityData', 'folders'
        ));
    }

    private function getStats()
    {
        $baseQuery = MasterFile::when(! Auth::user()->isDeveloper(), function ($query) {
            $query->where(function ($q) {
                $q->where('department', $this->userDepartment)
                    ->orWhereJsonContains('visible_to_departments', $this->userDepartment)
                    ->orWhereJsonContains('visible_to_users', $this->userEmail);
            });
        });

        return [
            'total_files' => (clone $baseQuery)->count(),
            'active_files' => (clone $baseQuery)->where('status', 'active')->count(),
            'categories' => MasterFileCategory::where('is_active', true)->count(),
            'folders' => DocumentFolder::where('is_active', true)
                ->when(! Auth::user()->isDeveloper(), function ($query) {
                    $query->where(function ($q) {
                        $q->where('department', $this->userDepartment)
                            ->orWhereNull('department')
                            ->orWhereJsonContains('visible_to_departments', $this->userDepartment);
                    });
                })->count(),
            'total_downloads' => (clone $baseQuery)->sum('download_count'),
            'pending_approval' => (clone $baseQuery)->where('status', 'pending_approval')->count(),
        ];
    }

    private function getRecentFiles()
    {
        return MasterFile::with(['category', 'uploader'])
            ->when(! Auth::user()->isDeveloper(), function ($query) {
                $query->where(function ($q) {
                    $q->where('department', $this->userDepartment)
                        ->orWhereJsonContains('visible_to_departments', $this->userDepartment)
                        ->orWhereJsonContains('visible_to_users', $this->userEmail);
                });
            })
            ->where('status', 'active') // Only show latest versions
            ->latest()
            ->take(5)
            ->get();
    }

    private function getPopularFiles()
    {
        return MasterFile::with(['category'])
            ->when(! Auth::user()->isDeveloper(), function ($query) {
                $query->where(function ($q) {
                    $q->where('department', $this->userDepartment)
                        ->orWhereJsonContains('visible_to_departments', $this->userDepartment)
                        ->orWhereJsonContains('visible_to_users', $this->userEmail);
                });
            })
            ->orderBy('download_count', 'desc')
            ->take(5)
            ->get();
    }

    private function getActivityData()
    {
        $days = (int) $this->selectedPeriod;
        $startDate = now()->subDays($days);

        // SQL Server compatible date extraction
        return MasterFileAccessLog::selectRaw('CAST(created_at AS DATE) as date, COUNT(*) as count')
            ->where('created_at', '>=', $startDate)
            ->when(! Auth::user()->isDeveloper(), function ($query) {
                $query->where('department', $this->userDepartment);
            })
            ->groupBy(DB::raw('CAST(created_at AS DATE)'))
            ->orderBy('date')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->date => $item->count];
            });
    }

    private function getFolders()
    {
        return DocumentFolder::with(['children', 'files'])
            ->whereNull('parent_id')
            ->where('is_active', true)
            ->when(! Auth::user()->isDeveloper(), function ($query) {
                $query->where(function ($q) {
                    $q->where('department', $this->userDepartment)
                        ->orWhereNull('department')
                        ->orWhereJsonContains('visible_to_departments', $this->userDepartment);
                });
            })
            ->orderBy('name')
            ->take(8)
            ->get();
    }
}
