<?php

namespace App\Models\DocumentLibrary;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MasterFile extends Model
{
    protected $table = 'documents';

    protected $fillable = [
        'category_id', 'folder_id', 'title', 'description', 'document_code',
        'file_path', 'original_filename', 'file_size', 'mime_type',
        'version', 'parent_file_id', 'status', 'effective_date',
        'expiry_date', 'review_date', 'tags', 'revision_notes',
        'department', 'visible_to_departments', 'visible_to_users', 'uploaded_by',
        'approved_by', 'approved_at', 'download_count', 'view_count',
        'last_accessed_at', 'is_confidential',
    ];

    protected $casts = [
        'tags' => 'array',
        'visible_to_departments' => 'array',
        'visible_to_users' => 'array',
        'effective_date' => 'date',
        'expiry_date' => 'date',
        'review_date' => 'date',
        'approved_at' => 'datetime',
        'last_accessed_at' => 'datetime',
        'is_confidential' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(MasterFileCategory::class, 'category_id');
    }

    public function folder()
    {
        return $this->belongsTo(DocumentFolder::class, 'folder_id');
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function parent()
    {
        return $this->belongsTo(MasterFile::class, 'parent_file_id');
    }

    public function versions()
    {
        return $this->hasMany(MasterFile::class, 'parent_file_id');
    }

    public function attachments()
    {
        return $this->hasMany(DocumentAttachment::class, 'document_id');
    }

    public function parentFile()
    {
        return $this->belongsTo(MasterFile::class, 'parent_file_id');
    }

    public function isLatestVersion(): bool
    {
        // if (!$this->parent_file_id) {
        //     return true; // This is the original file
        // }

        // return $this->version === $this->parentFile->versions()->max('version');
        return $this->getLatestVersion()->id === $this->id;
    }

    public function getAllVersions()
    {
        $parentId = $this->parent_file_id ?: $this->id;

        return static::where(function ($query) use ($parentId) {
            $query->where('parent_file_id', $parentId)
                ->orWhere('id', $parentId);
        })
            ->orderByRaw('CAST(SUBSTRING(version, 1, CHARINDEX(\'.\', version + \'.\') - 1) AS INT) DESC')
            ->orderByRaw('CAST(SUBSTRING(version, CHARINDEX(\'.\', version + \'.\') + 1, LEN(version)) AS INT) DESC')
            ->get();
    }

    public function getLatestVersion()
    {
        $parentId = $this->parent_file_id ?: $this->id;

        return static::where(function ($query) use ($parentId) {
            $query->where('parent_file_id', $parentId)
                ->orWhere('id', $parentId);
        })
            ->where('status', 'active')
            ->first();
    }

    public function accessLogs()
    {
        return $this->hasMany(MasterFileAccessLog::class, 'file_id');
    }

    public function getFileUrlAttribute(): string
    {
        return Storage::url($this->file_path);
    }

    public function getFormattedFileSizeAttribute(): string
    {
        $bytes = (int) $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        while ($bytes >= 1024 && $i < 3) {
            $bytes /= 1024;
            $i++;
        }

        return round($bytes, 2).' '.$units[$i];
    }

    public function isVisibleToDepartment($department): bool
    {
        if ($this->department === $department) {
            return true;
        }

        return in_array($department, $this->visible_to_departments ?? []);
    }

    public function isVisibleToUser($email): bool
    {
        return in_array($email, $this->visible_to_users ?? []);
    }

    public function canBeViewedBy($user): bool
    {
        // Check this file's visibility first
        if ($this->isVisibleToDepartment($user->department ?? 'ITSS')) {
            return true;
        }

        if ($this->isVisibleToUser($user->email)) {
            return true;
        }

        // For versioned documents, also check access from the latest version or parent
        // This ensures older versions are accessible if the user can view any version in the chain
        if ($this->parent_file_id) {
            // Check parent file's visibility
            $parent = $this->parentFile;
            if ($parent) {
                if ($parent->isVisibleToDepartment($user->department ?? 'ITSS')) {
                    return true;
                }
                if ($parent->isVisibleToUser($user->email)) {
                    return true;
                }
            }
        }

        // Check latest active version's visibility
        $latestVersion = $this->getLatestVersion();
        if ($latestVersion && $latestVersion->id !== $this->id) {
            if ($latestVersion->isVisibleToDepartment($user->department ?? 'ITSS')) {
                return true;
            }
            if ($latestVersion->isVisibleToUser($user->email)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if this document has a newer version available
     */
    public function hasNewerVersion(): bool
    {
        if ($this->status === 'active') {
            return false;
        }

        $latestVersion = $this->getLatestVersion();

        return $latestVersion && $latestVersion->id !== $this->id;
    }

    /**
     * Get the newest active version of this document
     */
    public function getNewestVersion()
    {
        return $this->getLatestVersion();
    }

    public function logAccess($action = 'view', $ipAddress = null, $userAgent = null)
    {
        // Database constraint only allows: view, download, print, share
        $allowedActions = ['view', 'download', 'print', 'share'];

        // Map common actions to allowed values
        if ($action === 'upload' || $action === 'preview') {
            $action = 'view';
        }

        // Ensure action is in allowed list
        if (! in_array($action, $allowedActions)) {
            $action = 'view'; // Default fallback
        }

        MasterFileAccessLog::create([
            'file_id' => $this->id,
            'user_id' => Auth::id(),
            'action' => $action,
            'ip_address' => $ipAddress ?: request()->ip(),
            'user_agent' => $userAgent ?: request()->userAgent(),
            'department' => Auth::user()->department ?? 'UNKNOWN',
        ]);
    }
}
