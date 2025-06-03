<?php

namespace App\Http\Controllers;

use App\Models\MasterFiles\MasterFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MasterFileController extends Controller
{
    public function download(MasterFile $file)
    {
        // Check if user has access to this file
        $userDepartment = auth()->user()->department ?? 'ITSS';

        if (!auth()->user()->hasRole(['administrator', 'developer'])) {
            if (!$file->isVisibleToDepartment($userDepartment)) {
                abort(403, 'You do not have permission to download this file.');
            }
        }

        // Log the download
        $file->logAccess('download');

        // Return file download
        return Storage::disk('public')->download($file->file_path, $file->original_filename);
    }
}
