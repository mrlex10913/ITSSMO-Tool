<?php

namespace App\Http\Controllers;

use App\Models\DocumentLibrary\DocumentAttachment;
use App\Models\DocumentLibrary\MasterFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use ZipArchive;

class MasterFileController extends Controller
{
    public function download(MasterFile $file)
    {
        // Check if user has access to this file
        $userDepartment = auth()->user()->department ?? 'ITSS';

        if (! auth()->user()->hasRole(['administrator', 'developer'])) {
            if (! $file->canBeViewedBy(auth()->user())) {
                abort(403, 'You do not have permission to download this file.');
            }
        }

        // Log the download
        $file->logAccess('download');

        // Increment download count
        $file->increment('download_count');

        // Return file download
        return Storage::disk('public')->download($file->file_path, $file->original_filename);
    }

    public function preview(MasterFile $file)
    {
        // Check if user has access to this file
        if (! auth()->user()->hasRole(['administrator', 'developer'])) {
            if (! $file->canBeViewedBy(auth()->user())) {
                abort(403, 'You do not have permission to preview this file.');
            }
        }

        // Log the preview as a view action
        $file->logAccess('view');

        // Get the file path
        $path = Storage::disk('public')->path($file->file_path);

        if (! file_exists($path)) {
            abort(404, 'File not found.');
        }

        // Return file for inline viewing (not as download)
        return response()->file($path, [
            'Content-Type' => $file->mime_type,
            'Content-Disposition' => 'inline; filename="'.$file->original_filename.'"',
        ]);
    }

    public function downloadAttachment(DocumentAttachment $attachment)
    {
        $file = $attachment->document;

        // Check if user has access to the parent document
        if (! auth()->user()->hasRole(['administrator', 'developer'])) {
            if (! $file->canBeViewedBy(auth()->user())) {
                abort(403, 'You do not have permission to download this attachment.');
            }
        }

        // Get the file path
        $path = Storage::disk('public')->path($attachment->file_path);

        if (! file_exists($path)) {
            abort(404, 'Attachment file not found.');
        }

        // Return file download
        return Storage::disk('public')->download($attachment->file_path, $attachment->original_filename);
    }

    public function downloadAll(MasterFile $file)
    {
        // Check if user has access to this file
        if (! auth()->user()->hasRole(['administrator', 'developer'])) {
            if (! $file->canBeViewedBy(auth()->user())) {
                abort(403, 'You do not have permission to download this file.');
            }
        }

        // For superseded files, just download the single file
        if ($file->status === 'superseded') {
            return $this->download($file);
        }

        // Load attachments
        $attachments = $file->attachments;

        // If no attachments, just download the main file
        if ($attachments->isEmpty()) {
            return $this->download($file);
        }

        // Create a temporary ZIP file
        $zipFileName = Str::slug($file->title).'_v'.$file->version.'_'.now()->format('Ymd_His').'.zip';
        $zipPath = storage_path('app/temp/'.$zipFileName);

        // Ensure temp directory exists
        if (! file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }

        $zip = new ZipArchive;
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            abort(500, 'Could not create ZIP file.');
        }

        // Add the main document file
        $mainFilePath = Storage::disk('public')->path($file->file_path);
        if (file_exists($mainFilePath)) {
            $zip->addFile($mainFilePath, $file->original_filename);
        }

        // Create Attachments folder and add all attachments
        if ($attachments->isNotEmpty()) {
            $zip->addEmptyDir('Attachments');
            foreach ($attachments as $attachment) {
                $attachmentPath = Storage::disk('public')->path($attachment->file_path);
                if (file_exists($attachmentPath)) {
                    $zip->addFile($attachmentPath, 'Attachments/'.$attachment->original_filename);
                }
            }
        }

        $zip->close();

        // Log the download
        $file->logAccess('download');

        // Increment download count
        $file->increment('download_count');

        // Return the ZIP file and delete it after sending
        return response()->download($zipPath, $zipFileName)->deleteFileAfterSend(true);
    }
}
