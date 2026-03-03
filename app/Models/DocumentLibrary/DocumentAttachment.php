<?php

namespace App\Models\DocumentLibrary;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class DocumentAttachment extends Model
{
    protected $table = 'document_attachments';

    protected $fillable = [
        'document_id',
        'title',
        'description',
        'file_path',
        'original_filename',
        'file_size',
        'mime_type',
        'uploaded_by',
    ];

    public function document()
    {
        return $this->belongsTo(MasterFile::class, 'document_id');
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function getFormattedFileSizeAttribute(): string
    {
        $bytes = $this->file_size;

        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2).' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2).' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2).' KB';
        }

        return $bytes.' bytes';
    }

    public function getFileExtensionAttribute(): string
    {
        return strtoupper(pathinfo($this->original_filename, PATHINFO_EXTENSION));
    }

    public function getFileUrlAttribute(): string
    {
        return Storage::url($this->file_path);
    }
}
