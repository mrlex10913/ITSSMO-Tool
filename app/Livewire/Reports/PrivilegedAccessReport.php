<?php

namespace App\Livewire\Reports;

use App\Exports\PAM\PrivilegedAccessReportExport;
use App\Models\Assets\AssetList;
use App\Models\Borrowers\BorrowerDetails;
use App\Models\Roles;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

#[Layout('layouts.app')]
class PrivilegedAccessReport extends Component
{
    use WithPagination;

    // Filters
    public string $search = '';

    public string $roleFilter = '';

    public string $statusFilter = '';

    public string $dateFrom = '';

    public string $dateTo = '';

    public string $reportType = 'summary'; // summary, detailed, audit

    // Data
    public array $roles = [];

    public array $stats = [];

    public ?int $selectedUser = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'roleFilter' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'dateFrom' => ['except' => ''],
        'dateTo' => ['except' => ''],
        'reportType' => ['except' => 'summary'],
    ];

    public function mount(): void
    {
        $this->roles = Roles::pluck('name', 'id')->toArray();
        $this->dateFrom = now()->subDays(30)->format('Y-m-d');
        $this->dateTo = now()->format('Y-m-d');
    }

    public function render()
    {
        $this->computeStats();

        $users = $this->getUsersWithAssets();

        return view('livewire.reports.privileged-access-report', [
            'users' => $users,
        ]);
    }

    protected function computeStats(): void
    {
        $users = User::whereNotNull('role_id')
            ->when($this->roleFilter, fn ($q) => $q->where('role_id', $this->roleFilter))
            ->get();

        $this->stats = [
            'total_users' => $users->count(),
            'with_assets' => $users->filter(fn ($u) => $this->userHasAssets($u))->count(),
            'total_assets_assigned' => $this->getTotalAssetsAssigned(),
            'active_borrows' => BorrowerDetails::where('status', 'Borrowed')
                ->when($this->dateFrom, fn ($q) => $q->where('date_to_borrow', '>=', $this->dateFrom))
                ->when($this->dateTo, fn ($q) => $q->where('date_to_borrow', '<=', $this->dateTo))
                ->count(),
        ];
    }

    protected function userHasAssets(User $user): bool
    {
        // Check ITSS assets
        $itssAssets = AssetList::where('assigned_to', $user->name)->count();
        if ($itssAssets > 0) {
            return true;
        }

        // Check PAMO assets
        $pamoAssets = \App\Models\PAMO\PamoAssets::where('assigned_to', $user->id)->count();
        if ($pamoAssets > 0) {
            return true;
        }

        // Check borrowed items
        $borrowed = BorrowerDetails::where('name', $user->name)
            ->where('status', 'Borrowed')
            ->count();

        return $borrowed > 0;
    }

    protected function getTotalAssetsAssigned(): int
    {
        $itss = AssetList::where('assigned_to', '!=', 'ITSS')
            ->where('assigned_to', '!=', 'ITSS Office')
            ->count();

        $pamo = \App\Models\PAMO\PamoAssets::whereNotNull('assigned_to')->count();

        return $itss + $pamo;
    }

    protected function getUsersWithAssets()
    {
        return User::whereNotNull('role_id')
            ->when($this->roleFilter, fn ($q) => $q->where('role_id', $this->roleFilter))
            ->when($this->search, fn ($q) => $q->where(function ($query) {
                $query->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('email', 'like', '%'.$this->search.'%');
            }))
            ->with(['role:id,name'])
            ->get()
            ->map(function ($user) {
                $user->itss_assets = AssetList::where('assigned_to', $user->name)->get();
                $user->pamo_assets = \App\Models\PAMO\PamoAssets::where('assigned_to', $user->id)->get();
                $user->borrowed_items = BorrowerDetails::where('name', $user->name)
                    ->when($this->dateFrom, fn ($q) => $q->where('date_to_borrow', '>=', $this->dateFrom))
                    ->when($this->dateTo, fn ($q) => $q->where('date_to_borrow', '<=', $this->dateTo))
                    ->with('itemBorrow.assetCategory')
                    ->get();
                $user->total_assets = $user->itss_assets->count() + $user->pamo_assets->count() + $user->borrowed_items->sum(fn ($b) => $b->itemBorrow->count());

                return $user;
            })
            ->filter(fn ($u) => $u->total_assets > 0 || $this->reportType === 'summary');
    }

    public function export(): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $users = $this->getUsersWithAssets();

        return Excel::download(
            new PrivilegedAccessReportExport($users, $this->stats),
            'privileged-access-report-'.now()->format('Y-m-d').'.xlsx'
        );
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'roleFilter', 'statusFilter']);
        $this->dateFrom = now()->subDays(30)->format('Y-m-d');
        $this->dateTo = now()->format('Y-m-d');
        $this->reportType = 'summary';
    }
}
