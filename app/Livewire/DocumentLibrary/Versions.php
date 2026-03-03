<?php

namespace App\Livewire\DocumentLibrary;

use App\Models\DocumentLibrary\MasterFile;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.enduser')]
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
        $userEmail = Auth::user()->email;

        // Get parent documents (or documents without parents)
        $files = MasterFile::with(['category', 'uploader'])
            ->when(! Auth::user()->hasRole(['administrator', 'developer']), function ($query) use ($userDepartment, $userEmail) {
                $query->where(function ($q) use ($userDepartment, $userEmail) {
                    $q->where('department', $userDepartment)
                        ->orWhereJsonContains('visible_to_departments', $userDepartment)
                        ->orWhereJsonContains('visible_to_users', $userEmail);
                });
            })
            ->when($this->search, function ($query) {
                $query->where('title', 'like', '%'.$this->search.'%')
                    ->orWhere('document_code', 'like', '%'.$this->search.'%');
            })
            // Show latest active version of each document group
            ->where(function ($query) {
                $query->where('status', 'active')
                    ->whereNull('parent_file_id'); // Parent documents
            })
            ->orWhere(function ($query) {
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
                ->where(function ($query) use ($parentId) {
                    $query->where('parent_file_id', $parentId)
                        ->orWhere('id', $parentId);
                })
                ->orderByRaw('CAST(SUBSTRING(version, 1, CHARINDEX(\'.\', version + \'.\') - 1) AS INT) DESC')
                ->orderByRaw('CAST(SUBSTRING(version, CHARINDEX(\'.\', version + \'.\') + 1, LEN(version)) AS INT) DESC')
                ->get();
        }

        return view('livewire.document-library.versions', compact('files', 'selectedFileVersions'));
    }
}
