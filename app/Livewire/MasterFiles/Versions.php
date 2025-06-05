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

        // Get parent documents (or documents without parents)
        $files = MasterFile::with(['category', 'uploader'])
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
            // Show latest active version of each document group
            ->where(function($query) {
                $query->where('status', 'active')
                    ->whereNull('parent_file_id'); // Parent documents
            })
            ->orWhere(function($query) {
                // Or active versions that don't have a newer active version
                $query->where('status', 'active')
                    ->whereNotNull('parent_file_id');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $selectedFileVersions = null;
        if ($this->selectedFile) {
            // Get all versions of the selected document
            $selectedDoc = MasterFile::find($this->selectedFile);
            $parentId = $selectedDoc->parent_file_id ?: $selectedDoc->id;

            $selectedFileVersions = MasterFile::with(['uploader'])
                ->where(function($query) use ($parentId) {
                    $query->where('parent_file_id', $parentId)
                        ->orWhere('id', $parentId);
                })
                ->orderByRaw('CAST(SUBSTRING(version, 1, CHARINDEX(\'.\', version + \'.\') - 1) AS INT) DESC')
                ->orderByRaw('CAST(SUBSTRING(version, CHARINDEX(\'.\', version + \'.\') + 1, LEN(version)) AS INT) DESC')
                ->get();
        }

        return view('livewire.master-files.versions', compact('files', 'selectedFileVersions'));
    }
}
