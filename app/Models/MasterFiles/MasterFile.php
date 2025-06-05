<?php

namespace App\Models\MasterFiles;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class MasterFile extends Model
{
    protected $table = 'master_files';

    protected $fillable = [
        'category_id', 'title', 'description', 'document_code',
        'file_path', 'original_filename', 'file_size', 'mime_type',
        'version', 'parent_file_id', 'status', 'effective_date',
        'expiry_date', 'review_date', 'tags', 'revision_notes',
        'department', 'visible_to_departments', 'uploaded_by',
        'approved_by', 'approved_at', 'download_count', 'view_count',
        'last_accessed_at', 'is_confidential'
    ];

    protected $casts = [
        'tags' => 'array',
        'visible_to_departments' => 'array',
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

        return static::where(function($query) use ($parentId) {
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

        return static::where(function($query) use ($parentId) {
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
        return round($bytes, 2) . ' ' . $units[$i];
    }

    public function isVisibleToDepartment($department): bool
    {
        if ($this->department === $department) return true;

        return in_array($department, $this->visible_to_departments ?? []);
    }

    public function logAccess($action = 'view', $ipAddress = null, $userAgent = null)
{
    // Map 'upload' to 'view' since upload isn't in the constraint
    $allowedActions = ['view', 'download', 'preview'];

    // If action is 'upload', change it to 'view' as it represents initial access
    if ($action === 'upload') {
        $action = 'view';
    }

    // Ensure action is in allowed list
    if (!in_array($action, $allowedActions)) {
        $action = 'view'; // Default fallback
    }

    MasterFileAccessLog::create([
        'file_id' => $this->id,
        'user_id' => Auth::id(),
        'action' => $action,
        'ip_address' => $ipAddress ?: request()->ip(),
        'user_agent' => $userAgent ?: request()->userAgent(),
        'department' => Auth::user()->department ?? 'UNKNOWN'
    ]);
}
}
