<?php

namespace App\Livewire\MasterFiles;

use App\Models\MasterFiles\MasterFile;
use App\Models\MasterFiles\MasterFileCategory;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

class Search extends Component
{
    use WithPagination;

    public $search = '';
    public $category_filter = '';
    public $department_filter = '';
    public $date_from = '';
    public $date_to = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'category_filter' => ['except' => ''],
        'department_filter' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingCategoryFilter()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->category_filter = '';
        $this->department_filter = '';
        $this->date_from = '';
        $this->date_to = '';
        $this->resetPage();
    }

    public function render()
    {
        $userDepartment = Auth::user()->department ?? 'ITSS';

        $files = MasterFile::with(['category', 'uploader'])
            ->when(!Auth::user()->hasRole(['administrator', 'developer']), function($query) use ($userDepartment) {
                $query->where(function($q) use ($userDepartment) {
                    $q->where('department', $userDepartment)
                      ->orWhereJsonContains('visible_to_departments', $userDepartment);
                });
            })
            ->when($this->search, function($query) {
                $query->where(function($q) {
                    $q->where('title', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%')
                      ->orWhere('document_code', 'like', '%' . $this->search . '%')
                      ->orWhereJsonContains('tags', $this->search);
                });
            })
            ->when($this->category_filter, function($query) {
                $query->where('category_id', $this->category_filter);
            })
            ->when($this->department_filter, function($query) {
                $query->where('department', $this->department_filter);
            })
            ->when($this->date_from, function($query) {
                $query->whereDate('created_at', '>=', $this->date_from);
            })
            ->when($this->date_to, function($query) {
                $query->whereDate('created_at', '<=', $this->date_to);
            })
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        $categories = MasterFileCategory::withCount(['files' => function($query) use ($userDepartment) {
            $query->when(!Auth::user()->hasRole(['administrator', 'developer']), function($q) use ($userDepartment) {
                $q->where(function($subQ) use ($userDepartment) {
                    $subQ->where('department', $userDepartment)
                         ->orWhereJsonContains('visible_to_departments', $userDepartment);
                });
            })->where('status', 'active');
        }])
        ->where('is_active', true)
        ->orderBy('name')
        ->get();

        $departments = MasterFile::select('department')
            ->distinct()
            ->whereNotNull('department')
            ->when(!Auth::user()->hasRole(['administrator', 'developer']), function($query) use ($userDepartment) {
                $query->where(function($q) use ($userDepartment) {
                    $q->where('department', $userDepartment)
                      ->orWhereJsonContains('visible_to_departments', $userDepartment);
                });
            })
            ->orderBy('department')
            ->pluck('department');


        return view('livewire.master-files.search', compact('files', 'categories', 'departments'));
    }
}
