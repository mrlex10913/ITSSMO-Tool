<?php

namespace App\Livewire\MasterFiles;

use App\Models\MasterFiles\MasterFile;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;

use Livewire\Component;

class Versions extends Component
{
    use WithPagination;

    public $search = '';
    public $selectedFile = null;

    public function viewVersions($fileId)
    {
        $this->selectedFile = $fileId;
    }
    public function closeVersionPanel()
    {
        $this->selectedFile = null;
    }

    public function render()
    {
        $userDepartment = Auth::user()->department ?? 'ITSS';

        $files = MasterFile::with(['category', 'uploader', 'versions'])
            ->when(!Auth::user()->hasRole(['administrator', 'developer']), function($query) use ($userDepartment) {
                $query->where(function($q) use ($userDepartment) {
                    $q->where('department', $userDepartment)
                      ->orWhereJsonContains('visible_to_departments', $userDepartment);
                });
            })
            ->when($this->search, function($query) {
                $query->where('title', 'like', '%' . $this->search . '%')
                      ->orWhere('document_code', 'like', '%' . $this->search . '%');
            })
            ->whereNull('parent_file_id') // Only show parent files
            ->where('status', 'active') // Only show active documents
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $selectedFileVersions = null;
        if ($this->selectedFile) {
            $selectedFileVersions = MasterFile::with(['uploader'])
                ->where(function($query) {
                    $query->where('parent_file_id', $this->selectedFile)
                          ->orWhere('id', $this->selectedFile);
                })
                ->orderBy('version', 'desc')
                ->get();
        }

        return view('livewire.master-files.versions', compact('files', 'selectedFileVersions'));
    }
}
