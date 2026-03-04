<?php

namespace App\Models\DocumentLibrary;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;

/**
 * StorageLocation - Manages multiple storage directories for Document Library
 *
 * Allows administrators to configure multiple storage locations (local drives,
 * network shares, S3, etc.) and migrate files between them when storage fills up.
 */
class StorageLocation extends Model
{
    protected $fillable = [
        'name',
        'disk',
        'path_prefix',
        'driver',
        'root_path',
        'max_size_bytes',
        'used_size_bytes',
        'is_default',
        'is_active',
        'is_readonly',
        'description',
        'created_by',
    ];

    protected $casts = [
        'max_size_bytes' => 'integer',
        'used_size_bytes' => 'integer',
        'is_default' => 'boolean',
        'is_active' => 'boolean',
        'is_readonly' => 'boolean',
    ];

    // =========================================================================
    // RELATIONSHIPS
    // =========================================================================

    public function documents()
    {
        return $this->hasMany(MasterFile::class, 'storage_location_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // =========================================================================
    // SCOPES
    // =========================================================================

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeWritable($query)
    {
        return $query->where('is_active', true)->where('is_readonly', false);
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    // =========================================================================
    // STORAGE DISK HELPERS
    // =========================================================================

    /**
     * Register this storage location as a Laravel disk at runtime
     */
    public function registerDisk(): void
    {
        if ($this->driver === 'local' && $this->root_path) {
            Config::set("filesystems.disks.{$this->disk}", [
                'driver' => 'local',
                'root' => $this->root_path,
                'throw' => false,
            ]);
        }
        // For other drivers (s3, ftp), the disk should already be configured in filesystems.php
    }

    /**
     * Get the Storage disk instance for this location
     */
    public function disk()
    {
        $this->registerDisk();

        return Storage::disk($this->disk);
    }

    /**
     * Get the full path prefix for storing files
     */
    public function getStoragePath(string $subpath = ''): string
    {
        $path = $this->path_prefix ? trim($this->path_prefix, '/') : '';

        if ($subpath) {
            $path = $path ? "{$path}/{$subpath}" : $subpath;
        }

        return $path;
    }

    /**
     * Store a file in this storage location
     */
    public function storeFile($file, string $directory = ''): ?string
    {
        if ($this->is_readonly) {
            return null;
        }

        $this->registerDisk();
        $storagePath = $this->getStoragePath($directory);

        return $file->store($storagePath, $this->disk);
    }

    /**
     * Check if this location has enough space for a file
     */
    public function hasSpaceFor(int $bytes): bool
    {
        if ($this->max_size_bytes === null) {
            return true; // Unlimited
        }

        return ($this->used_size_bytes + $bytes) <= $this->max_size_bytes;
    }

    /**
     * Update the used space counter
     */
    public function addUsedSpace(int $bytes): void
    {
        $this->increment('used_size_bytes', $bytes);
    }

    /**
     * Decrease the used space counter
     */
    public function removeUsedSpace(int $bytes): void
    {
        $this->decrement('used_size_bytes', min($bytes, $this->used_size_bytes));
    }

    /**
     * Recalculate used space from actual documents
     */
    public function recalculateUsedSpace(): int
    {
        $totalBytes = $this->documents()->sum('file_size');
        $this->update(['used_size_bytes' => $totalBytes]);

        return $totalBytes;
    }

    // =========================================================================
    // COMPUTED ATTRIBUTES
    // =========================================================================

    public function getUsagePercentageAttribute(): ?float
    {
        if ($this->max_size_bytes === null || $this->max_size_bytes === 0) {
            return null;
        }

        return round(($this->used_size_bytes / $this->max_size_bytes) * 100, 2);
    }

    public function getFormattedUsedSizeAttribute(): string
    {
        return $this->formatBytes($this->used_size_bytes);
    }

    public function getFormattedMaxSizeAttribute(): string
    {
        if ($this->max_size_bytes === null) {
            return 'Unlimited';
        }

        return $this->formatBytes($this->max_size_bytes);
    }

    public function getAvailableSpaceAttribute(): ?int
    {
        if ($this->max_size_bytes === null) {
            return null;
        }

        return max(0, $this->max_size_bytes - $this->used_size_bytes);
    }

    public function getFormattedAvailableSpaceAttribute(): string
    {
        $space = $this->available_space;
        if ($space === null) {
            return 'Unlimited';
        }

        return $this->formatBytes($space);
    }

    // =========================================================================
    // STATIC HELPERS
    // =========================================================================

    /**
     * Get the default storage location for new uploads
     */
    public static function getDefault(): ?self
    {
        return static::where('is_default', true)
            ->where('is_active', true)
            ->where('is_readonly', false)
            ->first();
    }

    /**
     * Get a writable storage location with enough space
     */
    public static function getAvailableForSize(int $bytes): ?self
    {
        // First try the default
        $default = static::getDefault();
        if ($default && $default->hasSpaceFor($bytes)) {
            return $default;
        }

        // Then try any other active, writable location
        return static::where('is_active', true)
            ->where('is_readonly', false)
            ->where(function ($q) use ($bytes) {
                $q->whereNull('max_size_bytes')
                    ->orWhereRaw('(max_size_bytes - used_size_bytes) >= ?', [$bytes]);
            })
            ->orderByDesc('is_default')
            ->first();
    }

    /**
     * Set this location as the default (unsets other defaults)
     */
    public function setAsDefault(): void
    {
        static::where('is_default', true)->update(['is_default' => false]);
        $this->update(['is_default' => true]);
    }

    // =========================================================================
    // PRIVATE HELPERS
    // =========================================================================

    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $i = 0;
        while ($bytes >= 1024 && $i < 4) {
            $bytes /= 1024;
            $i++;
        }

        return round($bytes, 2).' '.$units[$i];
    }
}
