<?php

namespace App\Livewire\MasterFiles;

use App\Models\MasterFiles\MasterFile;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Show extends Component
{
    public MasterFile $file;

    public function mount(MasterFile $file)
    {
        // Check if user has access to this file
        $userDepartment = Auth::user()->department ?? 'ITSS';

        if (!Auth::user()->hasRole(['administrator', 'developer'])) {
            if (!$file->isVisibleToDepartment($userDepartment)) {
                abort(403, 'You do not have permission to view this file.');
            }
        }

        $this->file = $file;

        // Log the view
        $file->logAccess('view');
    }

    public function download()
    {
        return redirect()->route('master-file.download', $this->file);
    }

    public function render()
    {
        $this->file->load([
            'category',
            'uploader',
            'approver',
            'versions' => function($query) {
                $query->with('uploader')->orderBy('version', 'desc');
            },
            'accessLogs' => function($query) {
                $query->with('user')->latest()->take(10);
            }
        ]);

        return view('livewire.master-files.show');
    }

}
