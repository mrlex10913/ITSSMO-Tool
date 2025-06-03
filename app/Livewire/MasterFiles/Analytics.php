<?php

namespace App\Livewire\MasterFiles;

use App\Models\MasterFiles\MasterFile;
use App\Models\MasterFiles\MasterFileAccessLog;
use App\Models\MasterFiles\MasterFileCategory;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Component;

class Analytics extends Component
{
    public $selectedPeriod = '30';

    public function render()
    {
        $userDepartment = Auth::user()->department ?? 'ITSS';
        $days = (int) $this->selectedPeriod;

        // Basic statistics
        $totalFiles = MasterFile::when(!Auth::user()->hasRole(['administrator', 'developer']), function($query) use ($userDepartment) {
            $query->where(function($q) use ($userDepartment) {
                $q->where('department', $userDepartment)
                  ->orWhereJsonContains('visible_to_departments', $userDepartment);
            });
        })->where('status', 'active')->count();

        $totalDownloads = MasterFile::when(!Auth::user()->hasRole(['administrator', 'developer']), function($query) use ($userDepartment) {
            $query->where(function($q) use ($userDepartment) {
                $q->where('department', $userDepartment)
                  ->orWhereJsonContains('visible_to_departments', $userDepartment);
            });
        })->where('status', 'active')->sum('download_count');

        $totalViews = MasterFile::when(!Auth::user()->hasRole(['administrator', 'developer']), function($query) use ($userDepartment) {
            $query->where(function($q) use ($userDepartment) {
                $q->where('department', $userDepartment)
                  ->orWhereJsonContains('visible_to_departments', $userDepartment);
            });
        })->where('status', 'active')->sum('view_count');

        // Active users in the selected period
        $activeUsers = MasterFileAccessLog::when(!Auth::user()->hasRole(['administrator', 'developer']), function($query) use ($userDepartment) {
            $query->where('department', $userDepartment);
        })
        ->where('created_at', '>=', now()->subDays($days))
        ->distinct('user_id')
        ->count('user_id');

        // Most popular files
        $popularFiles = MasterFile::with('category')
            ->when(!Auth::user()->hasRole(['administrator', 'developer']), function($query) use ($userDepartment) {
                $query->where(function($q) use ($userDepartment) {
                    $q->where('department', $userDepartment)
                      ->orWhereJsonContains('visible_to_departments', $userDepartment);
                });
            })
            ->where('status', 'active')
            ->where('download_count', '>', 0)
            ->orderBy('download_count', 'desc')
            ->take(10)
            ->get();

        // Files by category - Fixed for SQL Server
        $categoryStats = MasterFileCategory::select('master_file_categories.*')
            ->selectSub(function ($query) use ($userDepartment) {
                $subquery = $query->from('master_files')
                    ->whereColumn('master_file_categories.id', 'master_files.category_id')
                    ->where('status', 'active');

                if (!Auth::user()->hasRole(['administrator', 'developer'])) {
                    $subquery->where(function($q) use ($userDepartment) {
                        $q->where('department', $userDepartment)
                          ->orWhereJsonContains('visible_to_departments', $userDepartment);
                    });
                }

                return $subquery->count();
            }, 'files_count')
            ->get()
            ->filter(function($category) {
                return $category->files_count > 0;
            })
            ->sortByDesc('files_count');

        // Recent activity
        $recentActivity = MasterFileAccessLog::with(['file.category', 'user'])
            ->when(!Auth::user()->hasRole(['administrator', 'developer']), function($query) use ($userDepartment) {
                $query->where('department', $userDepartment);
            })
            ->where('created_at', '>=', now()->subDays($days))
            ->orderBy('created_at', 'desc')
            ->take(20)
            ->get();

        // Daily activity chart data - Fixed for SQL Server
        $dailyActivity = MasterFileAccessLog::selectRaw('
                CAST(created_at AS DATE) as date,
                COUNT(*) as total,
                SUM(CASE WHEN action = ? THEN 1 ELSE 0 END) as downloads,
                SUM(CASE WHEN action = ? THEN 1 ELSE 0 END) as views
            ', ['download', 'view'])
            ->when(!Auth::user()->hasRole(['administrator', 'developer']), function($query) use ($userDepartment) {
                $query->where('department', $userDepartment);
            })
            ->where('created_at', '>=', now()->subDays($days))
            ->groupBy(DB::raw('CAST(created_at AS DATE)'))
            ->orderBy('date')
            ->get();

        // Department statistics
        $departmentStats = [];
        if (Auth::user()->hasRole(['administrator', 'developer'])) {
            $departments = ['ITSS', 'PAMO', 'BFO', 'HR', 'ACCOUNTING', 'ADMIN'];

            foreach ($departments as $dept) {
                $departmentStats[$dept] = [
                    'files' => MasterFile::where('department', $dept)->where('status', 'active')->count(),
                    'downloads' => MasterFile::where('department', $dept)->where('status', 'active')->sum('download_count'),
                    'views' => MasterFile::where('department', $dept)->where('status', 'active')->sum('view_count'),
                    'active_users' => MasterFileAccessLog::where('department', $dept)
                        ->where('created_at', '>=', now()->subDays($days))
                        ->distinct('user_id')
                        ->count('user_id')
                ];
            }
        } else {
            $departmentStats[$userDepartment] = [
                'files' => MasterFile::where('department', $userDepartment)->where('status', 'active')->count(),
                'downloads' => MasterFile::where('department', $userDepartment)->where('status', 'active')->sum('download_count'),
                'views' => MasterFile::where('department', $userDepartment)->where('status', 'active')->sum('view_count'),
                'active_users' => MasterFileAccessLog::where('department', $userDepartment)
                    ->where('created_at', '>=', now()->subDays($days))
                    ->distinct('user_id')
                    ->count('user_id')
            ];
        }

        // File type statistics
        $fileTypeStats = MasterFile::when(!Auth::user()->hasRole(['administrator', 'developer']), function($query) use ($userDepartment) {
            $query->where(function($q) use ($userDepartment) {
                $q->where('department', $userDepartment)
                  ->orWhereJsonContains('visible_to_departments', $userDepartment);
            });
        })
        ->where('status', 'active')
        ->get()
        ->groupBy(function($file) {
            return strtolower(pathinfo($file->original_filename, PATHINFO_EXTENSION));
        })
        ->map(function($group) {
            return $group->count();
        })
        ->sortDesc()
        ->take(10);

        return view('livewire.master-files.analytics', compact(
            'totalFiles', 'totalDownloads', 'totalViews', 'activeUsers',
            'popularFiles', 'categoryStats', 'recentActivity', 'dailyActivity',
            'departmentStats', 'fileTypeStats'
        ));
    }

}
